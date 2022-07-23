<?php
namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Interface MenuElementInterface
 * @package App\Menu\Element
 */
interface MenuElementInterface
{
    /**
     * MenuElementInterface constructor.
     * @param FactoryInterface $factory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher);

    /**
     * @param array $options
     * @return ItemInterface
     */
    public function dispatch(array $options): ItemInterface;
}