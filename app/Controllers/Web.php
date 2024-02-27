<?php

namespace App\Controllers;

use App\Models\DriverLocationModel;
use App\Models\CarCategoryModel;
use App\Models\ClientRequestModel;
use App\Models\CityModel;
use App\Models\CarModel;
use App\Models\DriverModel;
use App\Models\ClientModel;
use App\Models\CardModel;
use App\Models\PaymentModel;
use App\Models\BookingModel;
use App\Models\PickupLocationModel;
use App\Models\RequestCancellationModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Model;
use Config\Services;
use Exception;
use Redis;
use RedisException;
use ReflectionException;
class Home extends BaseController
{

    public $accessData = null;
    public $redis;

    public function __construct()
    {
        helper("gas_api");
        $this->redis = new Redis();
        try {
            if ($this->redis->connect("127.0.0.1")) {
                $this->redis->auth(getenv('app.redisPass'));
                // $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_JSON);
            } else {
                echo "Redis connection error, Guarsy require redis to run";
                die();
            }
        } catch (RedisException $e) {
            echo "Redis connection error, " . $e->getMessage();
            die();
        }
        session_write_close();
    }

    public function testRedis()
    {
        echo $this->redis->get('token');
        echo "Redis connected <br />";
        echo $this->redis->ping("hello") . "<br />";
        if ($this->redis->set("token", " hello token 1")) {
            echo "Token saved";
        }
    }


}




