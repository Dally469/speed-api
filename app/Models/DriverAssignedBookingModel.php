<?php

namespace App\Models;

use CodeIgniter\Model;

class DriverAssignedBookingModel extends Model {
    protected $table = 'app_driver_trip_dispatch';

    protected $primaryKey = 'id';
    protected $allowedFields = ['id','driver_id','booking_id'];
    protected $useTimestamps = false;
}
