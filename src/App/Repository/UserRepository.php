<?php
declare(strict_types=1);
namespace App\Repository;

class UserRepository extends BaseRepository
{
    protected $table = 'user';
    protected $primaryKey = 'id';

    public function findByEmail(string $email)
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function findByRole(string $role, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['role' => $role];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }
}