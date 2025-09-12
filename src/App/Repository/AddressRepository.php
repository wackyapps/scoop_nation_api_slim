<?php
declare(strict_types=1);

namespace App\Repository;

use DB;

class AddressRepository extends BaseRepository
{
    protected $table = 'addresses';
    protected $primaryKey = 'id';

    /**
     * List all addresses for a specific user
     */
    public function listAllAddressesByUserId(int $userId): array
    {
        return $this->findBy(['user_id' => $userId], ['created_at' => 'DESC']);
    }

    /**
     * Add a new address for a user
     */
    public function addAddress(int $userId, array $addressData): int
    {
        $addressData['user_id'] = $userId;
        $addressData = array_merge([
            'address_type' => 'home',
            'country' => 'Pakistan',
            'is_default' => 0
        ], $addressData);

        // Validate required fields
        $required = ['street_address', 'city', 'state', 'postal_code'];
        foreach ($required as $field) {
            if (!isset($addressData[$field]) || empty($addressData[$field])) {
                throw new \InvalidArgumentException("Field '{$field}' is required");
            }
        }

        return $this->save($addressData);
    }

    /**
     * Remove an address by ID for a specific user
     */
    public function removeAddress(int $userId, int $addressId): bool
    {
        $address = $this->findOneBy(['id' => $addressId, 'user_id' => $userId]);
        if (!$address) {
            return false;
        }
        
        return (bool)$this->delete($addressId);
    }
}