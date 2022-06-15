<?php

namespace App\ReadModel\Work\Task;

use App\ReadModel\Work\Task\Filter\Filter;
use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class TaskFetcher
{
    private $connection;
    private $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function all(Filter $filter, int $page, int $perPage, string $sortBy, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                't.id',
                't.date',
                'concat(m.name_first, \' \', m.name_last) as author',
                'p.name as project',
                'p.id as project_id',
                't.parent_id as parent',
                't.title',
                't.type',
                't.priority',
                't.plan_date',
                't.status',
                't.progress'
            )->leftJoin('t', 'work_members_members', 'm', 't.author_id = m.id')
            ->leftJoin('t', 'work_projects_projects', 'p', 'p.id = t.project_id')
            ->from('work_projects_tasks', 't');

        if ($project = $filter->project) {
            $qb->andWhere('t.project_id = :project');
            $qb->setParameter(':project', $project);
        }

        if ($search = $filter->search) {
            $qb->andWhere($qb->expr()->like('LOWER(t.title)', ':search'));
            $qb->setParameter(':search', '%' . mb_strtolower($search) . '%');
        }

        if ($member = $filter->member) {
            $qb->andWhere('EXISTS(
                SELECT ms.member_id FROM work_projects_memberships ms 
                WHERE ms.project_id = t.project_id AND ms.member_id = :member
            )');
            $qb->setParameter(':member', $member);
        }

        if ($type = $filter->type) {
            $qb->andWhere('t.type = :type');
            $qb->setParameter(':type', $type);
        }

        if ($status = $filter->status) {
            $qb->andWhere('t.status = :status');
            $qb->setParameter(':status', $status);
        }

        if ($priority = $filter->priority) {
            $qb->andWhere('t.priority = :priority');
            $qb->setParameter(':priority', $priority);
        }

        if ($executor = $filter->executor) {
            $qb->innerJoin('t', 'work_projects_tasks_members', 'tm', 't.id = tm.task_id');
            $qb->andWhere('tm.member_id = :executor');
            $qb->setParameter(':executor', $executor);
        }

        $sortable = ['id', 'date', 'author', 'project', 'title', 'type', 'priority', 'plan_date', 'status', 'progress'];

        if (!in_array($sortBy, $sortable, true)) {
            throw new \DomainException('Unable to sort');
        }

        $qb->orderBy($sortBy, $direction === 'desc' ? 'desc' : 'asc');

        $pagination = $this->paginator->paginate($qb, $page, $perPage);
        $tasks = (array) $pagination->getItems();
        $executors = $this->batchLoadExecutors(array_column($tasks, 'id'));

        $pagination->setItems(array_map(static function (array $task) use ($executors) {
            return array_merge($task, [
                'executors' => array_values(
                    array_column(
                        array_filter(
                            $executors,
                            static function (array $executor) use ($task) {
                                return $executor['task_id'] === $task['id'];
                            }
                        ),
                        'name'
                    )
                )
            ]);
        }, $tasks));

        return $pagination;
    }

    public function childrenOf(int $id): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                't.id',
                't.date',
                'p.name as project',
                'p.id as project_id',
                't.title',
                't.type',
                't.priority',
                't.status',
                't.progress'
            )->leftJoin('t', 'work_members_members', 'm', 't.author_id = m.id')
            ->leftJoin('t', 'work_projects_projects', 'p', 'p.id = t.project_id')
            ->from('work_projects_tasks', 't')
            ->where('t.parent_id = :parent_id')
            ->setParameter(':parent_id', $id)
            ->execute();

        $tasks = $qb->fetchAllAssociative();
        $executors = $this->batchLoadExecutors(array_column($tasks, 'id'));

        return array_map(static function (array $task) use ($executors) {
            return array_merge($task, [
                'executors' => array_values(
                    array_column(
                        array_filter(
                            $executors,
                            static function (array $executor) use ($task) {
                                return $executor['task_id'] === $task['id'];
                            }
                        ),
                        'name'
                    )
                )
            ]);
        }, $tasks);
    }

    private function batchLoadExecutors(array $ids): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'e.task_id',
                'TRIM(CONCAT(m.name_first, \' \', m.name_last)) AS name'
            )
            ->from('work_projects_tasks_members', 'e')
            ->innerJoin('e', 'work_members_members', 'm', 'm.id = e.member_id')
            ->where('e.task_id IN (:task)')
            ->setParameter(':task', $ids, Connection::PARAM_INT_ARRAY)
            ->orderBy('name')
            ->execute();

        return $stmt->fetchAllAssociative();
    }
}
