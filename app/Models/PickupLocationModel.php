<?php

namespace App\Models;

use CodeIgniter\Model;

class PickupLocationModel extends Model {
    protected $table = 'app_client_pickup_locations';

    protected $primaryKey = 'id';
    protected $allowedFields = ['id','client_id','title','address','phone','latitude','longitude','status'];
    protected $useTimestamps = false;
}
