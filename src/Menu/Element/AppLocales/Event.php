<?php
namespace App\Menu\Element\AppLocales;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

/**
 * Class Event
 * @package App\Menu\Element\AppLocales
 */
class Event extends \Symfony\Contracts\EventDispatcher\Event
{
    /**
     *
     */
    const NAME = __CLASS__;

    /**
     * @var FactoryInterface
     */
    private FactoryInterface $factory;
    /**
     * @var ItemInterface
     */
    private ItemInterface $menu;
    /**
     * @var array
     */
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