<?php

namespace App\Menu\Work;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MembersMenu
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
            'Members',
            ['route' => 'work.members']
        )->setExtra('routes', [
            ['route' => 'work.members']
        ]);

        $itemsContainLink[] = $menu->addChild(
            'Groups',
            ['route' => 'work.members.groups']
        )->setExtra('routes', [
            ['route' => 'work.members.groups'],
            ['pattern' => '/^work.members.groups\..*/']
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
