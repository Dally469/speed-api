<?php

namespace App\Models;

use CodeIgniter\Model;

class DriverModel extends Model {
    protected $table = 'app_driver';

    protected $primaryKey = 'id';
    protected $allowedFields = ['id','name','telephone','photo','category','email','password','status'];
    protected $useTimestamps = false;
}
