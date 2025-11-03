<?php
declare(strict_types=1);

namespace App\Repository;

use DB;

class CompanyRepository extends BaseRepository
{
    protected $table = 'business';
    protected $primaryKey = 'id';




    public function findCompanyById(int $businessId)
    {
      return  $this->findByPkId($businessId);
    }


     public function updateCompany(array $companyData,int $businessId):array 
    {
        $update = $this->update($businessId,(array) $companyData );

        return $this->findCompanyById($businessId);
    }

    // Layout Design Methods
    public function findLayoutDesignByBusinessId(int $businessId)
    {
        $result = DB::queryFirstRow("SELECT * FROM layout_design WHERE businessId = %i", $businessId);
        return $result ?: null;
    }

    public function updateLayoutDesign(array $layoutData, int $businessId): array
    {
        // Check if layout_design exists for this businessId
        $existing = $this->findLayoutDesignByBusinessId($businessId);
        
        if ($existing) {
            // Update existing record
            DB::update('layout_design', $layoutData, "businessId=%i", $businessId);
        } else {
            // Insert new record
            $layoutData['businessId'] = $businessId;
            DB::insert('layout_design', $layoutData);
        }
        
        return $this->findLayoutDesignByBusinessId($businessId);
    }

    


}