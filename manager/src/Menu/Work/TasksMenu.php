<?php

namespace App\Menu\Work;

use App\Model\Work\Entity\Projects\Project\Project;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TasksMenu
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
        $queryParams = $options['queryParams'];
        $menu = $this->factory->createItem('root')->setChildrenAttribute('class', 'nav nav-tabs mb-4');
        $itemsContainLink = [];

        /** @var ?Project $project */
        $project = $options['project'];

        $route = $project ? 'work.projects.project.tasks' : 'work.projects.tasks';
        $params = $project ? ['id' => $project->getId()->getValue()] : [];

        $itemsContainLink[] = $menu->addChild(
            'All',
            ['route' => $route, 'routeParameters' => array_merge_recursive($queryParams, $params)]
        )->setExtra('routes', [
            ['route' => $route]
        ]);

        $route = $project ? 'work.projects.project.tasks.own' : 'work.projects.tasks.own';
        $itemsContainLink[] = $menu->addChild(
            'My',
            ['route' => $route, 'routeParameters' => array_merge_recursive($queryParams, $params)]
        )->setExtra('routes', [
            ['route' => $route]
        ]);

        $route = $project ? 'work.projects.project.tasks.me' : 'work.projects.tasks.me';
        $itemsContainLink[] = $menu->addChild(
            'For me',
            ['route' => $route, 'routeParameters' => array_merge_recursive($queryParams, $params)]
        )->setExtra('routes', [
            ['route' => $route]
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
