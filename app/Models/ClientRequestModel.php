<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientRequestModel extends Model {
    protected $table = 'app_client_request';

    protected $primaryKey = 'id';
    protected $allowedFields = ['id' ,'client_id','trip_type','trip_price','source','destination', 's_latitude', 's_longitude', 'd_latitude', 'd_longitude', 'status','accepted_by'];
    protected $useTimestamps = true;

    public function getNearbyClientRequestByRange($lat, $lng, $range){
        $result =  $this->query("SELECT * FROM (
            SELECT app_client_request.id, ac.id as client_id, ac.names as client_name, ac.phone as client_phone,ac.photo as client_photo,trip_price, source,destination
            ,s_latitude,s_longitude,d_latitude,d_longitude, 
                (
                    (
                        (
                            acos(
                                sin(( $lat * pi() / 180))
                                *
                                sin(( `s_latitude` * pi() / 180)) + cos(( $lat * pi() /180 ))
                                *
                                cos(( `s_latitude` * pi() / 180)) * cos((( $lng - `s_longitude`) * pi()/180)))
                        ) * 180/pi()
                    ) * 60 * 1.1515 * 1.609344
                )
            as distance FROM app_client_request  LEFT JOIN app_clients ac ON ac.id = app_client_request.client_id WHERE app_client_request.status = 0
        ) app_client_request
        WHERE distance <= $range  ORDER BY distance
        LIMIT 1");

        if ($result->getNumRows() > 0) {
            return $result->getRow();
        }else{
            return false;
        }
    }

    
}
