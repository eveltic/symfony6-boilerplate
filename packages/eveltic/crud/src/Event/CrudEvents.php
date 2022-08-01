<?php

namespace Eveltic\Crud\Event;


/**
 * Class CrudEvents
 * @package App\Manager\Crud\Event
 */
final class CrudEvents
{
    /**
     * @Method: CrudControllerTrait\Index
     * @Method: CrudControllerTrait\Create
     * @Method: CrudControllerTrait\Edit
     * @Method: CrudControllerTrait\Remove
     * @Location: In the beginning
     * @Purpose: Check method security
     */
    const SECURITY = 'onSecurity';
    /**
     * @Method: CrudControllerTrait\Index
     * @Location: After load the crud object
     */
    const INDEX_POST_LOAD = 'onIndexPostLoad';
    /**
     * @Method: CrudControllerTrait\Remove
     * @Location: Before deleting the object
     */
    const REMOVE_PRE_DELETE = 'onRemovePreRemove';
    /**
     * @Method: CrudControllerTrait\Remove
     * @Location: After deleting the object
     */
    const REMOVE_POST_DELETE = 'onRemovePostRemove';
    /**
     * @Method: CrudControllerTrait\Create
     * @Location:
     */
    const CREATE_PRE_FORM = 'onCreatePreForm';
    /**
     * @Method: CrudControllerTrait\Create
     * @Location:
     */
    const CREATE_POST_FORM = 'onCreatePostForm';
    /**
     * @Method: CrudControllerTrait\Create
     * @Location:
     */
    const CREATE_PRE_INSERT = 'onCreatePreInsert';
    /**
     * @Method: CrudControllerTrait\Create
     * @Location:
     */
    const CREATE_POST_INSERT = 'onCreatePostInsert';
    /**
     * @Method: CrudControllerTrait\Edit
     * @Location:
     */
    const EDIT_PRE_LOAD = 'onEditPreForm';
    /**
     * @Method: CrudControllerTrait\Edit
     * @Location:
     */
    const EDIT_PRE_FORM = 'onEditPreForm';
    /**
     * @Method: CrudControllerTrait\Edit
     * @Location:
     */
    const EDIT_POST_FORM = 'onEditPostForm';
    /**
     * @Method: CrudControllerTrait\Edit
     * @Location:
     */
    const EDIT_PRE_INSERT = 'onEditPreInsert';
    /**
     * @Method: CrudControllerTrait\Edit
     * @Location:
     */
    const EDIT_POST_INSERT = 'onEditPostInsert';
    /**
     * @Method: CrudControllerTrait\Clone
     * @Location:
     */
    const CLONE_PRE_LOAD = 'onClonePreForm';
    /**
     * @Method: CrudControllerTrait\Clone
     * @Location:
     */
    const CLONE_PRE_FORM = 'onClonePreForm';
    /**
     * @Method: CrudControllerTrait\Clone
     * @Location:
     */
    const CLONE_POST_FORM = 'onClonePostForm';
    /**
     * @Method: CrudControllerTrait\Clone
     * @Location:
     */
    const CLONE_PRE_INSERT = 'onClonePreInsert';
    /**
     * @Method: CrudControllerTrait\Clone
     * @Location:
     */
    const CLONE_POST_INSERT = 'onClonePostInsert';
}