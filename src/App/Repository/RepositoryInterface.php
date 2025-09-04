<?php
declare(strict_types=1);
namespace App\Repository;

interface RepositoryInterface
{
    public function find($id);
    public function findAll(): array;
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array;
    public function findOneBy(array $criteria);
    public function count(array $criteria = []): int;
    public function save(array $data);
    public function update($id, array $data);
    public function delete($id);
}