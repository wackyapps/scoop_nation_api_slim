<?php
declare(strict_types=1);
namespace App\Repository;

class ContactSubmissionRepository extends BaseRepository
{
    protected $table = 'contact_submissions';
    protected $primaryKey = 'id';

    /**
     * Create a new contact submission
     */
    public function createSubmission(array $data): int
    {
        return $this->insert($data);
    }

    /**
     * Get submissions by business
     */
    public function getSubmissionsByBusiness(int $businessId, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['business_id' => $businessId];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Get submissions by branch
     */
    public function getSubmissionsByBranch(int $branchId, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['branch_id' => $branchId];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Get submissions by status
     */
    public function getSubmissionsByStatus(string $status, array $orderBy = null, $limit = null, $offset = null): array
    {
        $criteria = ['status' => $status];
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Update submission status
     */
    public function updateStatus(int $submissionId, string $status): bool
    {
        return $this->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')], ['id' => $submissionId]) ? true : false;
    }
}