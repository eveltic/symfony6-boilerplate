<?php
namespace App\Menu\Element\BackendLeftSidebar;

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
        $menu = $this->factory->createItem(__FUNCTION__);
        $menu->addChild('backend_title', [])->setLabel('Administration')->setExtra('roles', ['ROLE_ROOT']);
//        $menu->addChild('backend_dashboard', ['route' => 'app_backend_index_index'])->setLabel('Dashboard')->setLabelAttribute('icon', 'fas fa-tachometer-alt');
//        $menu->addChild('backend_log_index', ['route' => ''])->setLabel('Logs')->setLabelAttribute('icon', 'fas fa-fingerprint');
//        $menu->addChild('backend_user_index', ['route' => ''])->setLabel('Users')->setLabelAttribute('icon', 'fas fa-users');
//        $menu->addChild('backend_settings', ['route' => ''])->setLabel('Settings')->setLabelAttribute('icon', 'fas fa-cogs')->setExtra('roles', ['ROLE_ROOT']);


        //->setLabelAttribute('badge', ['class' => 'badge-danger right', 'value' => 'New']);
        //        $menu->addChild('home', ['route' => 'app_backend_user_profile'])->setLabel('Homepage');
//        $menu->addChild('comments', ['uri' => '#comments'])->setLabel('Comments')
//            ->setLabelAttribute('icon', 'far fa-circle text-danger')
//            ->setLabelAttribute('badge', ['class' => 'badge-danger right', 'value' => 'New']);
//
//        $notifications = $menu['comments']->addChild('my_notifications', ['route' => 'app_frontend_user_login'])->setLabel('My Notifications');
//        $notifications->addChild('my_dashboard', ['route' => ''])->setLabel('Dashboard');
//
//        $comments = $menu['comments']->addChild('my_comments', ['route' => 'app_frontend_user_login'])->setLabel('My comments');
//        $comments->addChild('my_second_comments', ['route' => 'app_frontend_user_login'])->setLabel('Second comments');

        $this->eventDispatcher->dispatch(new Event($this->factory, $menu, ['menu' => $menu]), Event::NAME);

        return $menu;
    }
}