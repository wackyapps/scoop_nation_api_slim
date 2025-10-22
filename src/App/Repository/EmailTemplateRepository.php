<?php
declare(strict_types=1);

namespace App\Repository;

use DB;

class EmailTemplateRepository extends BaseRepository
{
    protected $table = 'email_templates';
    protected $primaryKey = 'id';

    /**
     * Get all active email templates
     *
     * @return array Array of active email templates
     */
    public function getActiveEmailTemplates(): array
    {
        return $this->findBy(['is_active' => 1], ['name' => 'ASC']);
    }

    /**
     * Get all email templates with pagination and optional search
     *
     * @param string|null $search Search term for name or subject
     * @param int $limit Number of records per page
     * @param int $page Current page number
     * @return array Array containing data and total count
     */
    public function getAllEmailTemplates(?string $search = null, int $limit = 10, int $page = 1): array
    {
        $query = "SELECT id,name,slug,subject,variables,is_active,created_at,updated_at FROM {$this->table}";
        $params = [];
        $whereClauses = [];

        if ($search) {
            $whereClauses[] = "(name LIKE %s OR subject LIKE %s)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $query .= " ORDER BY created_at DESC LIMIT %i OFFSET %i";
        $params[] = $limit;
        $params[] = ($page - 1) * $limit;

        $templates = DB::query($query, ...$params);

        $countQuery = "SELECT COUNT(id) as total FROM {$this->table}";
        $countParams = [];

        if ($search) {
            $countQuery .= " WHERE (name LIKE %s OR subject LIKE %s)";
            $countParams[] = "%{$search}%";
            $countParams[] = "%{$search}%";
        }

        $totalResult = DB::queryFirstRow($countQuery, ...$countParams);

        return [
            'data' => $templates,
            'total' => (int) $totalResult['total']
        ];
    }
}