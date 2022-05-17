<?php

namespace App\Menu\Work;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class ProjectSettingsMenu
{
    private $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function build(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root')->setChildrenAttribute('class', 'nav nav-tabs mb-4');
        $itemsContainLink = [];

        $itemsContainLink[] = $menu->addChild(
            'Common',
            ['route' => 'work.projects.project.settings', 'routeParameters' => ['id' => $options['project_id']]]
        )->setExtra('routes', [
            ['route' => 'work.projects.project.settings'],
        ]);

        $itemsContainLink[] = $menu->addChild(
            'Departments',
            [
                'route' => 'work.projects.project.settings.departments',
                'routeParameters' => ['id' => $options['project_id']]
            ]
        )->setExtra('routes', [
            ['route' => 'work.projects.project.settings.departments'],
            ['pattern' => '/^work.projects.project.settings.departments\..*/']
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
