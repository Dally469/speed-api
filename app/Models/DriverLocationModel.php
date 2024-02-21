<?php

namespace App\Models;

use CodeIgniter\Model;

class DriverLocationModel extends Model {
    protected $table = 'app_driver_locations';

    protected $primaryKey = 'id';
    

    protected $allowedFields = ['id' ,'car','city','driver_category','driver_id','driver_name', 'driver_phone', 'is_online','latitude', 'longitude', 'model', 'price','payment_method'];
    protected $useTimestamps = true;


    public function getNearbyDriverByRange($lat, $lng, $range, $payMode){
        $result =  $this->query("SELECT * FROM (
            SELECT app_driver_locations.id,driver_name, driver_phone,ad.photo,driver_category,ac.car_name,ac.color,ac.photo as car_photo,ac.plate_number,price, model,is_online
            ,latitude,longitude,payment_method,
                (
                    (
                        (
                            acos(
                                sin(( $lat * pi() / 180))
                                *
                                sin(( `latitude` * pi() / 180)) + cos(( $lat * pi() /180 ))
                                *
                                cos(( `latitude` * pi() / 180)) * cos((( $lng - `longitude`) * pi()/180)))
                        ) * 180/pi()
                    ) * 60 * 1.1515 * 1.609344
                )
            as distance FROM app_driver_locations  LEFT JOIN app_cars ac ON ac.driver_id = app_driver_locations.driver_id  LEFT JOIN app_driver ad ON ad.id = app_driver_locations.driver_id  WHERE app_driver_locations.is_online = 1
        ) app_driver_locations
        WHERE distance <= $range AND payment_method  like  '%$payMode%'  ORDER BY distance
        LIMIT 5");

        if ($result->getNumRows() > 0) {
            return $result->getResultArray();
        }else{
            return false;
        }
    }

}
