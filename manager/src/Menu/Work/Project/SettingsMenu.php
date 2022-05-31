<?php

namespace App\Menu\Work\Project;

use App\Model\Work\Entity\Projects\Project\Project;
use App\Security\Voter\Work\ProjectAccess;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SettingsMenu
{
    private $factory;
    private $auth;

    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $auth)
    {
        $this->factory = $factory;
        $this->auth = $auth;
    }

    public function build(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root')->setChildrenAttribute('class', 'nav nav-tabs mb-4');
        $itemsContainLink = [];

        /** @var Project $project */
        $project = $options['project'];
        $defaultRouteParameters = ['id' => $project->getId()];

        $itemsContainLink[] = $menu->addChild(
            'Common',
            ['route' => 'work.projects.project.settings', 'routeParameters' => $defaultRouteParameters]
        )->setExtra('routes', [
            ['route' => 'work.projects.project.settings'],
        ]);

        $itemsContainLink[] = $menu->addChild(
            'Departments',
            [
                'route' => 'work.projects.project.settings.departments',
                'routeParameters' => $defaultRouteParameters
            ]
        )->setExtra('routes', [
            ['route' => 'work.projects.project.settings.departments'],
            ['pattern' => '/^work.projects.project.settings.departments\..*/']
        ]);

        $itemsContainLink[] = $menu->addChild(
            'Members',
            [
                'route' => 'work.projects.project.settings.members',
                'routeParameters' => $defaultRouteParameters
            ]
        )->setExtra('routes', [
            ['route' => 'work.projects.project.settings.members'],
            ['pattern' => '/^work.projects.project.settings.members\..*/']
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
