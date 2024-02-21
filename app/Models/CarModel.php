<?php

namespace App\Models;

use CodeIgniter\Model;

class CarModel extends Model {
    protected $table = 'app_cars';

    protected $primaryKey = 'id';
    protected $allowedFields = ['id','driver_id','car_name','car_seats','plate_number','color','photo','document_front_image','document_back_image','category_id','extra'];
    protected $useTimestamps = false;
}
