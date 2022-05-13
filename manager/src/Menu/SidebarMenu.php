<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SidebarMenu
{
    private $factory;
    private $auth;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $auth)
    {
        $this->factory = $factory;
        $this->auth = $auth;
    }

    public function build(): ItemInterface
    {
        $menu = $this->factory->createItem('root')->setChildrenAttribute('class', 'nav');
        $itemsContainLink = [];

        $itemsContainLink[] = $menu->addChild('Dashboard', ['route' => 'home'])
            ->setExtra('icon', 'build/coreui/icons/svg/free.svg#cil-speedometer')
            ->setExtra('routes', [
                ['route' => 'home']
            ]);

        $menu->addChild('Work')->setAttribute('class', 'nav-title');

        if ($this->auth->isGranted('ROLE_WORK_MEMBERS_MANAGE')) {
            $itemsContainLink[] = $menu->addChild('Members', ['route' => 'work.members'])
                ->setExtra('icon', 'build/coreui/icons/svg/free.svg#cil-people')
                ->setExtra('routes', [
                    ['route' => 'work.members'],
                    ['pattern' => '/^work.members\..*/']
                ]);
        }

        $menu->addChild('Control')->setAttribute('class', 'nav-title');

        $itemsContainLink[] = $menu->addChild('Profile', ['route' => 'profile'])
            ->setExtra('icon', 'build/coreui/icons/svg/free.svg#cil-user')
            ->setExtra('routes', [
                ['route' => 'profile'],
                ['pattern' => '/^profile\..*/']
            ]);

        if ($this->auth->isGranted('ROLE_MANAGE_USERS')) {
            $itemsContainLink[] = $menu->addChild('Users', ['route' => 'users'])
                ->setExtra('icon', 'build/coreui/icons/svg/free.svg#cil-people')
                ->setExtra('routes', [
                    ['route' => 'users'],
                    ['pattern' => '/^users\..*/']
                ]);
        }

        $this->setSettingsForItemsContainLinks($itemsContainLink);
        return $menu;
    }

    private function setSettingsForItemsContainLinks($items)
    {
        foreach ($items as $item) {
            $item->setAttribute('class', 'nav-item')
                ->setLinkAttribute('class', 'nav-link');
        }
    }
}
