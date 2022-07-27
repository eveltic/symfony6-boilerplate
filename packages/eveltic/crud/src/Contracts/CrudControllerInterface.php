<?php

namespace Eveltic\Crud\Contracts;

interface CrudControllerInterface
{
    public function index();
    public function create();
    public function read();
    public function update();
    public function delete();
    public function configureCrud();
    public function getSubscribedServices();
    public function getEntityManager();
    public function getTranslator();
}
