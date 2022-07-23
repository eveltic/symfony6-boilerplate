<?php
namespace App\Menu\Element\BackendTopMenu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class Event extends \Symfony\Contracts\EventDispatcher\Event
{
    const NAME = __CLASS__;

    private FactoryInterface $factory;
    private ItemInterface $menu;
    private array $extra = [];

    /**
     * @param FactoryInterface $factory
     * @param ItemInterface $menu
     * @param array $extra
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu, array $extra = [])
    {
        $this->factory = $factory;
        $this->menu = $menu;
        $this->extra = $extra;
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    /**
     * @return ItemInterface
     */
    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }
}