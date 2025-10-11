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

    


}