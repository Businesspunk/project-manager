<?php

namespace App\Menu\Work\Project;

use App\Security\Voter\Work\ProjectAccess;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MainMenu
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

        $itemsContainLink[] = $menu->addChild(
            'Dashboard',
            ['route' => 'work.projects.project', 'routeParameters' => ['id' => $options['project_id']]]
        )->setExtra('routes', [
            ['route' => 'work.projects.project']
        ]);

        $itemsContainLink[] = $menu->addChild(
            'Tasks',
            ['route' => 'work.projects.project.tasks', 'routeParameters' => ['id' => $options['project_id']]]
        )->setExtra('routes', [
            ['route' => 'work.projects.project.tasks'],
            ['pattern' => '/^work.projects.project.tasks\..*/']
        ]);

        $itemsContainLink[] = $menu->addChild(
            'Settings',
            ['route' => 'work.projects.project.settings', 'routeParameters' => ['id' => $options['project_id']]]
        )->setExtra('routes', [
            ['route' => 'work.projects.project.settings'],
            ['pattern' => '/^work.projects.project.settings\..*/']
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
