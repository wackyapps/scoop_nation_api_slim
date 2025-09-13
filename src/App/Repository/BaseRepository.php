<?php
declare(strict_types=1);

namespace App\Repository;

use DB;

abstract class BaseRepository implements RepositoryInterface
{
    protected $table;
    protected $primaryKey = 'id';

    // Add this method for custom queries
    protected function executeQuery(string $query, array $params = []): array
    {
        return DB::query($query, ...$params);
    }

    protected function executeQueryFirstRow(string $query, array $params = [])
    {
        return DB::queryFirstRow($query, ...$params);
    }

    public function find($id)
    {
        return DB::queryFirstRow("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = %i", $id);
    }

    public function findAll(): array
    {
        return DB::query("SELECT * FROM {$this->table}");
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $query = "SELECT * FROM {$this->table}";
        $params = [];

        if (!empty($criteria)) {
            $query .= " WHERE ";
            $conditions = [];
            foreach ($criteria as $field => $value) {
                $conditions[] = "{$field} = %s";
                $params[] = $value;
            }
            $query .= implode(' AND ', $conditions);
        }

        if ($orderBy) {
            $query .= " ORDER BY ";
            $orders = [];
            foreach ($orderBy as $field => $direction) {
                $orders[] = "{$field} {$direction}";
            }
            $query .= implode(', ', $orders);
        }

        if ($limit) {
            $query .= " LIMIT %i";
            $params[] = $limit;
        }

        if ($offset) {
            $query .= " OFFSET %i";
            $params[] = $offset;
        }

        return DB::query($query, ...$params);
    }

    public function findOneBy(array $criteria)
    {
        $results = $this->findBy($criteria, null, 1);
        return $results[0] ?? null;
    }

    public function count(array $criteria = []): int
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];

        if (!empty($criteria)) {
            $query .= " WHERE ";
            $conditions = [];
            foreach ($criteria as $field => $value) {
                $conditions[] = "{$field} = %s";
                $params[] = $value;
            }
            $query .= implode(' AND ', $conditions);
        }

        $result = DB::queryFirstRow($query, ...$params);
        return (int) $result['count'];
    }

    public function save(array $data)
    {
        DB::insert($this->table, $data);
        return DB::insertId();
    }

    public function update($id, array $data)
    {
        DB::update($this->table, $data, "{$this->primaryKey} = %i", $id);
        return DB::affectedRows();
    }

    public function delete($id)
    {
        DB::delete($this->table, "{$this->primaryKey} = %i", $id);
        return DB::affectedRows();
    }
}