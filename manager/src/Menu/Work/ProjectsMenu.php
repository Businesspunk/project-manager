<?php

namespace App\Menu\Work;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProjectsMenu
{
    private $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
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
            'Roles',
            ['route' => 'work.projects.roles']
        )->setExtra('routes', [
            ['route' => 'work.projects.roles'],
            ['pattern' => '/^work.projects.roles\..*/']
        ]);

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
