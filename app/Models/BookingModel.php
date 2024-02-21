<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model {
    protected $table = 'app_booking';

    protected $primaryKey = 'id';
    protected $allowedFields = ['id','trip_refference_no','vehi_type','trip_type','trip_date','trip_time','client_id','trip_price','source_location','destination_location','source_lat','source_lng','destination_lat','destination_lng','status','approved_by'];
    protected $useTimestamps = true;
}
