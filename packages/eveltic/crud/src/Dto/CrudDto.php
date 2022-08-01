<?php

namespace Eveltic\Crud\Dto;

use Doctrine\ORM\Tools\Pagination\Paginator;

final class CrudDto
{
    private array $dqlParts;
    private array $pages = [
        'max' => 1,
        'limit' => 25,
        'page' => 1,
        'count' => 1,
        'total_count' => 1,
    ];
    private Paginator $paginator;
    private array $tableColumns = [];

    public function __construct(array $dqlParts, array $pages, Paginator $paginator, array $tableColumns)
    {
        $this->dqlParts = $dqlParts;
        $this->pages = $pages;
        $this->paginator = $paginator;
        $this->tableColumns = $tableColumns;
    }

    /**
     * Get the value of tableColumns
     *
     * @return  mixed
     */
    public function getTableColumns(): array
    {
        return $this->tableColumns;
    }

    /**
     * Set the value of tableColumns
     *
     * @param   mixed  $tableColumns  
     *
     * @return  self
     */
    public function setTableColumns($tableColumns): self
    {
        $this->tableColumns = $tableColumns;

        return $this;
    }

    /**
     * Get the value of paginator
     *
     * @return  mixed
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * Set the value of paginator
     *
     * @param   mixed  $paginator  
     *
     * @return  self
     */
    public function setPaginator($paginator): self
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * Get the value of pages
     *
     * @return  mixed
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Set the value of pages
     *
     * @param   mixed  $pages  
     *
     * @return  self
     */
    public function setPages($pages): self
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Get the value of dqlParts
     *
     * @return  mixed
     */
    public function getDqlParts()
    {
        return $this->dqlParts;
    }

    /**
     * Set the value of dqlParts
     *
     * @param   mixed  $dqlParts  
     *
     * @return  self
     */
    public function setDqlParts($dqlParts): self
    {
        $this->dqlParts = $dqlParts;

        return $this;
    }
}
