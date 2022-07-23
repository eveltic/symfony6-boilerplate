<?php
namespace App\Menu;


use Knp\Menu\MenuItem;
use Knp\Menu\ItemInterface;
use Knp\Menu\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MenuBuilder
{
    private FactoryInterface $factory;
    private EventDispatcherInterface $eventDispatcher;

    const MENU_BUILDER_CLASS = 'App\Menu\Element\%s\Builder';
    const MENU_ELEMENT_INTERFACE = MenuElementInterface::class;

    public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create(array $options): MenuItem
    {
        /* Check that the name option is set */
        if (!isset($options['name']) OR empty($options['name']) or !is_string($options['name'])) throw new \Exception('The menu must declare a "name" option of type string to identify the method that has to manage the menu itself');

        /* Copy the name, and unset his key in the options array */
        $name = $options['name'];
        unset($options['name']);

        /* Check that the class exists */
        if (!class_exists(sprintf(self::MENU_BUILDER_CLASS, ucfirst($name)))) throw new \Exception(sprintf('The class "%s" does not exist, please check that the file exists and the namespace is correct', $name));

        /* Check that the class implements the needed interface */
        if (!isset(class_implements(sprintf(self::MENU_BUILDER_CLASS, ucfirst($name)))[self::MENU_ELEMENT_INTERFACE])) throw new \Exception(sprintf('The class "%s" must implement the "%s" interface', $name, self::MENU_ELEMENT_INTERFACE));

        /* Instantiate the menu builder */
        $oReflection = new \ReflectionClass(sprintf(self::MENU_BUILDER_CLASS, ucfirst($name)));
        $oMenuBuilder = $oReflection->newInstanceArgs([$this->factory, $this->eventDispatcher]);

        /* Execute the method */
        $oMenuItem = $oMenuBuilder->dispatch($options);

        /* Check that the method returns a valid object */
        if (!$oMenuItem instanceof MenuItem) throw new \Exception(sprintf('The object returned by the method %s() must be a "Knp\Menu\MenuItem" object', sprintf('App\Menu\Element\%s\MenuBuilder\dispatch', ucfirst($name)), $name));

        /* Add extra parameters as extra root menu array items */
        $oMenuItem->setExtras($options['parameters']??[]);

        /* Return the menu */
        return $oMenuItem;
    }
}