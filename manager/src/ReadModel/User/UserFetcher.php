<?php

namespace App\ReadModel\User;

use App\ReadModel\User\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use function Doctrine\DBAL\Query\QueryBuilder;

class UserFetcher
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function existsByResetToken(string $token)
    {
        return $this->connection->createQueryBuilder('t')
                ->select('COUNT(*)')
                ->from('user_users')
                ->where('reset_token_token = :token')
                ->setParameter(':token', $token)
                ->execute()->fetchColumn(0) > 0;
    }

    public function findForAuthByEmail(string $email): ?AuthView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id', 'email','password_hash', 'role', 'status',
                'TRIM(CONCAT(name_first, \' \', name_last)) as name'
            )
            ->from('user_users')
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findForAuthByNetwork(string $network, string $identity): ?AuthView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'u.id', 'u.email', 'u.password_hash', 'u.role', 'u.status',
                'TRIM(CONCAT(name_first, \' \', name_last)) as name'
            )
            ->from('user_users', 'u')
            ->innerJoin('u', 'user_user_networks', 'n', 'u.id = n.user_id')
            ->where('n.network = :network AND n.identity = :identity')
            ->setParameters([
                'network' => $network,
                'identity' => $identity
            ])->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findByEmail(string $email): ?ShortView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'email', 'password_hash', 'role', 'status')
            ->from('user_users')
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, ShortView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findBySignUpConfirmationToken(string $token): ?ShortView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'email', 'password_hash', 'role', 'status')
            ->from('user_users')
            ->where('confirm_token = :confirm_token')
            ->setParameter(':confirm_token', $token)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, ShortView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getDetail(string $id): ?DetailView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id', 'email', 'date',  'role', 'status',
                'TRIM(CONCAT(name_first, \' \', name_last)) as name'
            )
            ->from('user_users')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        $stmt = $this->connection->createQueryBuilder()
            ->select('n.network', 'n.identity')
            ->from('user_users', 'u')
            ->innerJoin('u', 'user_user_networks', 'n', 'u.id = n.user_id')
            ->where('u.id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, NetworkView::class);

        $result->networks = $stmt->fetchAll();

        return $result ?: null;
    }

    public function all(Filter $filter): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id', 'email', 'date',  'role', 'status',
                'TRIM(CONCAT(name_first, \' \', name_last)) as name'
            )
            ->from('user_users')
            ->orderBy('date', 'desc');

        if ($name = $filter->name) {
            $qb->andWhere($qb->expr()->like('LOWER(TRIM(CONCAT(name_first, \' \', name_last)))',  ':name'));
            $qb->setParameter(':name', '%' . mb_strtolower($name) . '%');
        }

        if ($email = $filter->email) {
            $qb->andWhere($qb->expr()->like('LOWER(email)',  ':email'));
            $qb->setParameter(':email', '%' . mb_strtolower($email) . '%');
        }

        if ($status = $filter->status) {
            $qb->andWhere('status = :status');
            $qb->setParameter(':status', $status);
        }

        if ($role = $filter->role) {
            $qb->andWhere('role = :role');
            $qb->setParameter(':role', $role);
        }

        $stmt = $qb->execute();
        $stmt->setFetchMode(FetchMode::ASSOCIATIVE);
        return $stmt->fetchAll();
    }
}