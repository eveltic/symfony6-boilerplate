<?php
namespace App\Menu\Element\BackendTopMenu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use App\Menu\MenuElementInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class Builder
 * @package App\Menu\Element\BackendTopMenu
 */
class Builder implements MenuElementInterface
{
    /**
     * @var FactoryInterface
     */
    private FactoryInterface $factory;
    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * Builder constructor.
     * @param FactoryInterface $factory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array $options
     * @return ItemInterface
     */
    public function dispatch(array $options): ItemInterface
    {
        $menu = $this->factory->createItem(__FUNCTION__);
        $menu->addChild('profile', ['route' => ''])->setLabel('Profile');

        $this->eventDispatcher->dispatch(new Event($this->factory, $menu, []), Event::NAME);

        return $menu;
    }
}