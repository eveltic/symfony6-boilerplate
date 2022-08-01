<?php

namespace Eveltic\Crud\Twig;

use Closure;
use Eveltic\Crud\Factory\CrudFactory;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Uid\Uuid;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;

class CrudExtension extends AbstractExtension
{
    private $urlGenerator;
    private $crudFactory;
    
    public function __construct(UrlGeneratorInterface $urlGenerator, CrudFactory $crudFactory)
    {
        $this->urlGenerator = $urlGenerator;
        $this->crudFactory = $crudFactory;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('toRfc4122', [$this, 'toRfc4122']),
        ];
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('isUuid', [$this, 'isUuid']),
            new TwigFunction('routeExists', [$this, 'routeExists']),
            new TwigFunction('crudField', [$this, 'crudField']),
            new TwigFunction('executeClosure', [$this, 'executeClosure']),
            new TwigFunction('isDefinedAnd', [$this, 'isDefinedAnd']),
        ];
    }

    public function isDefinedAnd(mixed $seed, mixed $equal): bool
    {
        return boolval(isset($seed) and $seed === $equal);
    }

    public function toRfc4122(Uuid $uuid): string
    {
        return $uuid->toRfc4122();
    }

    public function isUuid($uuid): bool
    {
        return CrudFactory::isUuid($uuid);
    }

    public function routeExists($route, ?array $params = []): bool
    {
        try {
            $this->urlGenerator->generate($route, $params);
            return true;
        } catch (RouteNotFoundException $e) {
            return false;
        }
    }

    public function executeClosure(?Closure $closure = null, ?array $arguments = []): bool
    {
        return empty($closure) ? true : $closure(...$arguments);
    }

    public function crudField($field, $value, $row)
    {
        return $this->crudFactory->renderField($field, $value, $row);
    }
}
