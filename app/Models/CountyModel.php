<?php

namespace App\Models;

use CodeIgniter\Model;

class CountyModel extends Model
{
    protected $table      = 'county';      // table name
    protected $primaryKey = 'id';          // primary key

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'country_id',
        'title',
        'active',
        'date_created',
    ];

    protected $useTimestamps = false;      // using custom date_created



    public function getCountyNameById(int $id): ?string
{
    $row = $this->find($id);
    return $row ? $row['title'] : null;
}
}