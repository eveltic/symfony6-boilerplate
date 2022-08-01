<?php

namespace Eveltic\Crud\Factory;

use Closure;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Eveltic\Crud\Configuration\CrudConfiguration;
use Eveltic\Crud\Configuration\Group\AccessGroup;
use Eveltic\Crud\Configuration\Group\FieldGroup;
use Eveltic\Crud\Dto\CrudDto;
use Eveltic\Crud\Exception\FieldException;
use Eveltic\Crud\Field\AbstractField;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

class CrudFactory
{
    const PAGINATE_RECORDS_NO_LIMIT = 0;

    const PAGINATE_RECORDS_DEFAULT = 25;

    private QueryBuilder $queryBuilder;

    private Security $security;

    private TranslatorInterface $translator;

    public function __construct(Security $security, TranslatorInterface $translator)
    {
        $this->security = $security;
        $this->translator = $translator;
    }

    public function getCrud(CrudConfiguration $configuration, Request $request): CrudDto
    {
        /* Get query builder from configuration */
        $this->buildQuery($configuration);

        /* Get table columns */
        $tableColumns = $this->getTableColumns($configuration);

        /* Build select fields and get table columns */
        $this->buildSelect();

        /* Build order */
        $this->buildSearchAndOrder($tableColumns, $configuration, $request);

        /* Get objects */
        $DQLParts = $this->queryBuilder->getDQLParts();
        $oQuery = $this->queryBuilder->getQuery();
        $page = $this->getPage($request, $configuration);
        $limit = $this->getLimit($request, $configuration);
        $paginator = $this->getPaginator($oQuery, $page, $limit);
        $count = iterator_count($paginator->getIterator());
        $total_count = intval($paginator->count());
        $maxPages = $limit !== 0 ? intval(ceil($paginator->count() / $limit)) : 1;
        $_page = $page > $maxPages ? $maxPages : $page;

        return new CrudDto($DQLParts, ['max' => $maxPages, 'limit' => $limit, 'page' => $_page, 'count' => $count, 'total_count' => $total_count],$paginator, $tableColumns);
    }

    public function getMainEntityPrimaryKey(): string
    {
        $sMainEntity = $this->queryBuilder->getRootEntities()[0];
        $oMetadata = $this->queryBuilder->getEntityManager()->getClassMetadata($sMainEntity);
        return $oMetadata->getSingleIdentifierFieldName();
    }

    private function buildQuery(CrudConfiguration $configuration): void
    {
        /* Get the query and check for it */
        $this->queryBuilder = $configuration->getConfiguration('querybuilder');
        if(!$this->queryBuilder instanceof QueryBuilder) throw new ORMException('You must supply a Doctrine ORM Query Builder instance to the crud');
        if ($this->queryBuilder->getType() !== QueryBuilder::SELECT) throw new ORMException('You must supply a Doctrine ORM Query Builder SELECT statement');
    }

    public function getMainEntityAlias(): string
    {
        return $this->queryBuilder->getRootAliases()[0];
    }

    public function getWhereInValuesFromArray(array $aIds): array
    {
        $aReturn = [];
        /* Only check the first key, the others must follow the same schema */
        $bIsUuid = (isset($aIds[0]) AND self::isUuid($aIds[0]));

        /* If the result is a uuid, transform to binary */
        if($bIsUuid === true){
            foreach($aIds as $uuid){
                $uuid = Uuid::fromString($uuid);
                $aReturn[] = $uuid->toBinary();
            }
        } else { /* Else keep the values */
            $aReturn = $aIds;
        }
        return $aReturn;
    }

    public function getValuesFromQueryBuilder(array $aResults): array
    {
        /* The return is the same as the result */
        $aReturn = $aResults;

        /* Only check the first key, the others must follow the same schema */
        $bIsUuid = (isset($aResults[0]) AND isset($aResults[0]['primary_key']) AND self::isUuid($aResults[0]['primary_key']));
        /* If the result is a uuid, transform to binary */
        if($bIsUuid === true){
            foreach($aResults as $key => $field) {
                $uuid = Uuid::fromString($field['primary_key']);
                $aReturn[$key]['primary_key'] = $uuid->toBinary();
            }
        }
        return $aReturn;
    }

    public static function isUuid($uuid): bool
    {
        return preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $uuid) === 1;
    }

    private function getSelectFields(): array
    {
        /* Add alias to all select parts */
        $sNewSelectPartsBeforeCheckAliases = '';
        /* Get all select parts and create a string with them */
        foreach ($this->queryBuilder->getDqlPart('select') as $selectKey => $oSelect) {
            foreach ($oSelect->getParts() as $partKey => $partValue) {
                /* Remove all select alias "entity.field as something" */
                $sNewSelectPartsBeforeCheckAliases .= ', ' . $partValue;//
            }
        }
        /* Get all selectable parts individually and trim them all */
        $aFields = array_map('trim', preg_split("/[,]+(?![^\(]*\))/", trim($sNewSelectPartsBeforeCheckAliases, ', ')));
        foreach ($aFields as $key => $field) {
            /* For each part, if no function found and there's an alias, remove it */
            if (strpos($field, '(') === false AND stripos($field, 'as') !== false) {
                $aFields[$key] = trim(substr($field, 0, stripos($field, 'as')));
            }
        }

        /* Add translations field to the selectable fields array if available */
        $aFromEntities = $this->queryBuilder->getDqlPart('from');
        $oEntityManager = $this->queryBuilder->getEntityManager();
        foreach($aFromEntities as $aFromEntity){
            $bHasTranslationsField = $oEntityManager->getClassMetadata($aFromEntity->getFrom())->hasField('_translations');
            if($bHasTranslationsField === true) $aFields[] = sprintf('%s._translations', $aFromEntity->getAlias());
        }

        return array_unique($aFields);
    }

    private function getTableColumns(CrudConfiguration $configuration): array
    {
        /* Get select parts from query */
        $aFields = $this->getSelectFields();

        /* Get configuration settings */
        $aConfigurationFields = ($configuration->getConfiguration('fieldgroup') instanceof FieldGroup) ? $configuration->getConfiguration('fieldgroup')->getChilds() : [];

        /* Loop through fields and set table columns */
        $tableColumns = [];
        foreach ($aFields as $key => $field) {
            /* Get key and value from field select part */
            $key = (stripos($field, 'as') !== false) ? preg_split("/ as /i", $field)[1] : str_replace('.', '_', $field);
            $value = (stripos($field, 'as') !== false) ? preg_split("/ as /i", $field)[0] : sprintf('%s %s', ucfirst(strtolower(explode('.', $field)[0])), ucfirst(strtolower(explode('.', $field)[1])));
            /* If the field is from a function/alias get the alias */
            if (stripos($field, 'as') !== false) {
                $field = preg_split("/ as /i", $field)[1];
            }
            /* If the key exists */
            if (isset($aConfigurationFields[$field])) {
                /* Get object */
                $oField = $aConfigurationFields[$field];
                /* Check roles*/
                if ($this->hasAccess($oField->getRoles()) === true OR empty($oField->getRoles())) {
                    $tableColumns[$key] = $value;
                }
            }
        }
        return $tableColumns;
    }

    private function buildSelect(): void
    {
        /* Get select parts from query */
        $aFields = $this->getSelectFields();

        /* Get the main entity alias */
        $sMainEntityAlias = $this->getMainEntityAlias();

        /* Reset query select */
        $this->queryBuilder->resetDQLPart('select');

        /* Add primary key to select */
        $this->queryBuilder->addSelect(sprintf('%s.%s as primary_key', $sMainEntityAlias, $this->getMainEntityPrimaryKey()));

        /* Add select fields and create the tableColumns */
        foreach ($aFields as $key => $field) {
            $this->queryBuilder->addSelect(stripos($field, 'as') !== false ? $field : sprintf('%s as %s', $field, str_replace('.', '_', $field)));
        }
    }

    private function buildSearchAndOrder($tableColumns, CrudConfiguration $configuration, Request $request): void
    {
        /* Get configuration settings */
        $aConfigurationFields = ($configuration->getConfiguration('fieldgroup') instanceof FieldGroup) ? $configuration->getConfiguration('fieldgroup')->getChilds() : null;
        $aConfigurationAccesses = ($configuration->getConfiguration('accessgroup') instanceof AccessGroup) ? $configuration->getConfiguration('accessgroup')->getChilds() : null;
        /* Request order and search */
        foreach ($tableColumns as $field => $fieldTitle) {
            if (!empty($request->query->all($field))) {
                $aFieldRequestParts = $request->query->all($field);
                /* If the first part of the field doesn't match with one of the entity aliases, there's a field alias, so use it */
                $fieldInDatabaseFormat = (in_array(explode('_', $field, 2)[0], $this->queryBuilder->getAllAliases()) OR count(explode('_', $field, 2)) < 2) ? implode('.', explode('_', $field, 2)) : $field;
                /* Get field key from objects array */
                $oField = isset($aConfigurationFields[$fieldInDatabaseFormat]) ? $aConfigurationFields[$fieldInDatabaseFormat] : null;
                /* If the key exists */
                if (!empty($oField)) {
                    /* Get settings objects for access checking */
                    $oAccessSearch = isset($aConfigurationAccesses['search']) ? $aConfigurationAccesses['search'] : null;
                    $oAccessOrder = isset($aConfigurationAccesses['order']) ? $aConfigurationAccesses['order'] : null;
                    /* Set where */
                    if ($oField->isSearchable() === true AND (!empty($oAccessSearch) AND $oAccessSearch->getAccess() === true) AND (empty($oAccessSearch->getRoles()) OR $this->hasAccess($oAccessSearch->getRoles()))) {
                        if (isset($aFieldRequestParts['operator']) AND in_array($aFieldRequestParts['operator'], ['like', 'eq', 'neq', 'lt', 'gt', 'lte', 'gte']) AND isset($aFieldRequestParts['value'])) {
                            /* Check if the field has a searchTransformer method (used to transform search value to database value */
                            if(!method_exists($oField->getType(), 'searchTransformer')) {
                                $sValue = $aFieldRequestParts['value'];
                            } else {
                                $oMethod = new ReflectionMethod($oField->getType(),'searchTransformer');
                                $sValue = $oMethod->invokeArgs((new ReflectionClass($oField->getType()))->newInstance(), [$aFieldRequestParts['value']]);
                            }
                            $this->queryBuilder->andWhere($this->queryBuilder->expr()->{$aFieldRequestParts['operator']}($fieldInDatabaseFormat, ':' . $field));
                            $this->queryBuilder->setParameter(':' . $field, ($aFieldRequestParts['operator'] === 'like' ? '%' . $sValue . '%' : $sValue));
                        }
                    }
                    /*
                     * TODO: Assigning a number to each column or moving column order we will be able to make query column order selectable
                     */
                    /* Set order */
                    if ($oField->isSortable() === true AND (!empty($oAccessOrder) AND $oAccessOrder->getAccess() === true) AND (empty($oAccessOrder->getRoles()) OR $this->hasAccess($oAccessOrder->getRoles()))) {
                        if (isset($aFieldRequestParts['order']) AND in_array($aFieldRequestParts['order'], ['ASC', 'DESC'])) {
                            $this->queryBuilder->addOrderBy(new OrderBy($fieldInDatabaseFormat, $aFieldRequestParts['order']));
                        }
                    }
                }
            }
        }
        /* Default order by */
        if (empty($this->queryBuilder->getDQLPart('orderBy'))) {
            $this->queryBuilder->addOrderBy(sprintf('%s.%s', $this->getMainEntityAlias(), $this->getMainEntityPrimaryKey()), 'DESC');
        }
        /* If one id is selected set where clause */
        if(!empty($request->get('_id'))){
            $this->queryBuilder->andWhere($this->getMainEntityAlias() . '.id = :id')->setParameter(':id', intval($request->get('_id')));
        }
    }

    private function getPaginator($dql, $page, $limit): Paginator
    {
        if($limit === 0){
            $oPaginator = new Paginator($dql);
            $oPaginator->setUseOutputWalkers(false);
            $oPaginator->getQuery()
                ->setFirstResult(0);
        } else {
            $oPaginator = new Paginator($dql);
            $oPaginator->setUseOutputWalkers(false);
            $oPaginator->getQuery()
                ->setFirstResult($limit * ($page - 1))// Offset
                ->setMaxResults($limit); // Limit
        }


        return $oPaginator;
    }

    private function getPage(Request $request, CrudConfiguration $configuration): int
    {
        /* Check access */
        $oAccess = ($configuration->getConfiguration('accessgroup') instanceof AccessGroup) ? $configuration->getConfiguration('accessgroup')->getChilds('paginate') : null;
        if(!empty($oAccess) AND ($oAccess->getAccess() !== true OR !$this->hasAccess($oAccess->getRoles())) AND !empty($oAccess->getRoles())){
            return 1;
        }
        /* Access ok, check page */
        return intval($request->query->get('_page')) > 0 ? intval($request->query->get('_page')) : 1;
    }

    private function getLimit(Request $request, CrudConfiguration $configuration): int
    {
      /* Check access */
        $oAccess = ($configuration->getConfiguration('accessgroup') instanceof AccessGroup) ? $configuration->getConfiguration('accessgroup')->getChilds('paginate') : null;
        if(!empty($oAccess) AND ($oAccess->getAccess() !== true OR !$this->hasAccess($oAccess->getRoles())) AND !empty($oAccess->getRoles())){
            return self::PAGINATE_RECORDS_NO_LIMIT;
        }
        /* Access ok, check page */
        return !is_null($request->query->get('_limit')) ? intval($request->query->get('_limit')) : self::PAGINATE_RECORDS_DEFAULT;
    }

    private function hasAccess($aRoles): bool
    {
        /* Show the column as soon as one positive match and stop the loop */
        $bResult = false;
        foreach ($aRoles as $roleKey => $role) {
            if ($this->security->isGranted($role)) {
                $bResult = true;
                break;
            }
        }
        return $bResult;
    }

    public function renderField($field, $value, array $row)
    {
        /* If the field type (filter) is a closure, execute it */
        if ($field instanceof Closure) {
            return call_user_func($field, $value, $row);
        } else { /* If is one of the defined Field classes, create and invoke */
            /* Create the field class */
            $oReflection = new ReflectionClass($field);
            $oField = $oReflection->newInstance();

            /* Add translator in case the field needs it */
            if($oReflection->hasMethod('setTranslator')){
                $oField->setTranslator($this->translator);
            }

            /* Check if the child class is extending the abstract field class */
            if (!is_subclass_of($oField, AbstractField::class)) {
                throw new FieldException(sprintf('The class "%s" must extend the class "%s"', $oReflection->getName(), AbstractField::class));
            }

            /* Set the value, the row and render the field */
            return $oField
                ->setValue($value)
                ->setRow($row)
                ->render();
        }
    }
}
