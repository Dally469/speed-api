<?php

namespace App\Models;

use CodeIgniter\Model;

class CityModel extends Model {
    protected $table = 'app_provinces';

    protected $primaryKey = 'id';
    protected $allowedFields = ['id','name','phone','email'];
    protected $useTimestamps = false;
}
