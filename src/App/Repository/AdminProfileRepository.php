<?php
declare(strict_types=1);

namespace App\Repository;

require_once __DIR__ . '/SQL_Table_Names.php';

use DB;

class AdminProfileRepository extends BaseRepository
{
    protected $table = TABLE_ADMIN_PROFILE;
    protected $primaryKey = 'id';

    /**
     * Find admin profile by user ID
     * 
     * @param int $userId
     * @return array|null
     */
    public function findByUserId(int $userId): ?array
    {
        $profile = $this->findOneBy(['user_id' => $userId]);
        return $profile ?: null;
    }

    /**
     * Get admin profile with user information
     * 
     * @param int $userId
     * @return array|null
     */
    public function findProfileWithUser(int $userId): ?array
    {
        $sql = "
            SELECT 
                ap.*,
                u.email,
                u.phone,
                u.role,
                u.email_verified,
                u.phone_verified
            FROM " . TABLE_ADMIN_PROFILE . " ap
            INNER JOIN " . TABLE_USER . " u ON ap.user_id = u.id
            WHERE ap.user_id = %i
        ";

        $profile = DB::queryFirstRow($sql, $userId);
        return $profile ?: null;
    }

    /**
     * Create or update admin profile
     * 
     * @param int $userId
     * @param array $profileData
     * @return bool|int Profile ID
     */
    public function createOrUpdate(int $userId, array $profileData)
    {
        $existingProfile = $this->findByUserId($userId);

        if ($existingProfile) {
            // Update existing profile
            $this->update($existingProfile['id'], $profileData);
            return $existingProfile['id'];
        } else {
            // Create new profile
            $profileData['user_id'] = $userId;
            return $this->save($profileData);
        }
    }

    /**
     * Update admin profile avatar
     * 
     * @param int $userId
     * @param string $avatarPath
     * @return bool
     */
    public function updateAvatar(int $userId, string $avatarPath): bool
    {
        $profile = $this->findByUserId($userId);
        
        if (!$profile) {
            return false;
        }

        $this->update($profile['id'], ['avatar' => $avatarPath]);
        return true;
    }

    /**
     * Delete admin profile by user ID
     * 
     * @param int $userId
     * @return bool
     */
    public function deleteByUserId(int $userId): bool
    {
        $profile = $this->findByUserId($userId);
        
        if (!$profile) {
            return false;
        }

        return $this->delete($profile['id']);
    }

    /**
     * Get all admin profiles
     * 
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findAllAdminProfiles(?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $sql = "
            SELECT 
                ap.*,
                u.email,
                u.phone,
                u.role,
                u.email_verified,
                u.phone_verified
            FROM " . TABLE_ADMIN_PROFILE . " ap
            INNER JOIN " . TABLE_USER . " u ON ap.user_id = u.id
            WHERE u.role IN ('administrator', 'admin')
        ";

        if ($orderBy) {
            $sql .= " ORDER BY ";
            $orders = [];
            foreach ($orderBy as $field => $direction) {
                $orders[] = "{$field} {$direction}";
            }
            $sql .= implode(', ', $orders);
        } else {
            $sql .= " ORDER BY ap.created_at DESC";
        }

        if ($limit) {
            $sql .= " LIMIT %i";
            if ($offset) {
                $sql .= " OFFSET %i";
            }
        }

        return DB::query($sql, $limit ? ($offset ? [$limit, $offset] : [$limit]) : []);
    }
}
