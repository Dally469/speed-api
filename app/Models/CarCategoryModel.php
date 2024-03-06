<?php

namespace App\Models;

use CodeIgniter\Model;

class CarCategoryModel extends Model
{
    protected $table = 'app_cars_category';

    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'title', 'photo', 'price_per_meter', 'price_per_airport', 'price_per_day'];
    protected $useTimestamps = false;
}
