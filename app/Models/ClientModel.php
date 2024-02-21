<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model {
    protected $table = 'app_clients';

    protected $primaryKey = 'id';
    protected $allowedFields = ['id','names','phone','email','gender','password','photo','category','address_location','status'];
    protected $useTimestamps = false;
    
    public function checkClient($value, $key = "id"){
        $builder = $this->select('app_clients.*,');
        if ($key == null) {
            $builder->where($value);
        } else {
            $builder->where($key, $value);
        }
        return $builder->get()->getRow();
    }
}
