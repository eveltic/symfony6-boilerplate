<?php
namespace App\Menu\Element\AppLocales;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use App\Menu\MenuElementInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Builder implements MenuElementInterface
{
    private FactoryInterface $factory;
    private EventDispatcherInterface $eventDispatcher;

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
        $sLocale = $options['locale'];
        $aLocales = $options['locales'];
        $menu = $this->factory->createItem(__FUNCTION__);
        foreach($aLocales as $locale){
            $menu->addChild(sprintf('locale_%s', $locale), [])->setLabel($locale);
        }
        $this->eventDispatcher->dispatch(new Event($this->factory, $menu, []), Event::NAME);
        return $menu;
    }
}