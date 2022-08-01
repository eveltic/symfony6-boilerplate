<?php

namespace Eveltic\Crud\Configuration\Group;

interface GroupInterface
{
    const TYPE_CLASS = null;
    public function __construct(object ...$childs);
    public function getChilds(?string $key = null): mixed;
    public function setChilds(array $childs): self;
}
