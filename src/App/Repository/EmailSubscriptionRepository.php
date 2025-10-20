<?php
declare(strict_types=1);

namespace App\Repository;

class EmailSubscriptionRepository extends BaseRepository
{
    protected $table = TABLE_EMAIL_SUBSCRIPTIONS;
    protected $primaryKey = 'id';

    /**
     * Check if an email already exists in subscriptions
     * 
     * @param string $email Email address to check
     * @return bool True if email exists, false otherwise
     */
    public function emailExists(string $email): bool
    {
        $result = $this->findOneBy(['email' => $email]);
        return $result !== null;
    }

    /**
     * Subscribe an email address
     * 
     * @param string $email Email address to subscribe
     * @return int The ID of the newly created subscription
     */
    public function subscribe(string $email): int
    {
        return (int) $this->save(['email' => $email]);
    }

    /**
     * Get all email subscriptions
     * 
     * @param array|null $orderBy Order criteria
     * @param int|null $limit Limit number of results
     * @param int|null $offset Offset for pagination
     * @return array List of email subscriptions
     */
    public function getAllSubscriptions(?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return $this->findBy([], $orderBy ?? ['id' => 'DESC'], $limit, $offset);
    }

    /**
     * Delete a subscription by email
     * 
     * @param string $email Email address to unsubscribe
     * @return bool True if deleted, false otherwise
     */
    public function unsubscribeByEmail(string $email): bool
    {
        $subscription = $this->findOneBy(['email' => $email]);
        if ($subscription) {
            $this->delete($subscription['id']);
            return true;
        }
        return false;
    }
}
