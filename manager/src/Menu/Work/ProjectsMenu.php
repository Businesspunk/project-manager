<?php

namespace App\Menu\Work;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProjectsMenu
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
        $menu = $this->factory->createItem('root')->setChildrenAttribute('class', 'nav nav-tabs mb-4');
        $itemsContainLink = [];

        $itemsContainLink[] = $menu->addChild(
            'Projects',
            ['route' => 'work.projects']
        )->setExtra('routes', [
            ['route' => 'work.projects']
        ]);

        $itemsContainLink[] = $menu->addChild(
            'Tasks',
            ['route' => 'work.projects.tasks']
        )->setExtra('routes', [
            ['route' => 'work.projects.tasks'],
            ['pattern' => '/^work.projects.tasks\..*/']
        ]);

        if ($this->auth->isGranted('ROLE_WORK_ROLES_MANAGE')) {
            $itemsContainLink[] = $menu->addChild(
                'Roles',
                ['route' => 'work.projects.roles']
            )->setExtra('routes', [
                ['route' => 'work.projects.roles'],
                ['pattern' => '/^work.projects.roles\..*/']
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
