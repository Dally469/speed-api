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

class Home extends BaseController
{
    public function index()
    {
        return $this->response->setStatusCode(200)->setJSON(["status" => "success", "message" => "Welcome to AbyRide API is well configured"]);
    }

    public function getDriverLocation(): ResponseInterface
    {
        $mdl = new DriverLocationModel();
        $query = $mdl->select('app_driver_locations.id,driver_id,car,city,driver_category,driver_name,driver_phone,
            ds.title as is_online,latitude,longitude,model,price,created_at,updated_at')
            ->join("app_map_driver_status ds", "ds.id = app_driver_locations.is_online", "LEFT")
            ->whereIn('app_driver_locations.is_online', [1, 2])
            ->groupBy('app_driver_locations.driver_id')
            ->get()->getResultArray();

        if (!empty($query)) {
            $result['statusCode'] = 200;
            $result['success'] = true;
            $result['message'] = "Success data found";
            $result['data'] = $query;
            return $this->response->setJSON($result);
        } else {
            $result['statusCOde'] = 404;
            $result['success'] = false;
            $result['message'] = "No data found";
            $result['data'] = [];
            return $this->response->setStatusCode(404)->setJSON($result);
        }
    }

    public function getCarCategories(): ResponseInterface
    {
        $mdl = new CarCategoryModel();
        $query = $mdl->select('app_cars_category.*,COALESCE(onLocation.available, 0) as available')
            ->join("(SELECT count(app_driver_locations.id) as available, driver_category,is_online FROM app_driver_locations JOIN app_cars_category acc ON acc.id = app_driver_locations.driver_category WHERE is_online = 1 GROUP BY driver_category) onLocation", "onLocation.driver_category = app_cars_category.id ", "left")
            ->groupBy('id')
            ->get()->getResultArray();

        if (!empty($query)) {
            $result['statusCode'] = 200;
            $result['status'] = true;
            $result['message'] = "Successful";
            $result['car_category'] = $query;
            return $this->response->setJSON($result);
        } else {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "No category found";
            $result['car_category'] = $query;

            return $this->response->setStatusCode(400)->setJSON($result);
        }
    }

    public function getNearbyClientRequest(): ResponseInterface
    {
        $mdl = new ClientRequestModel();
        $result = $mdl->select('app_client_request.id,client_id,ac.names as client_name,ac.phone as client_phone,ac.category as client_package,
        trip_type,trip_price,source,destination,s_latitude,s_longitude,d_latitude,d_longitude,app_client_request.status,created_at,updated_at')
            ->join("app_clients ac", "ac.id = app_client_request.client_id", "LEFT")
            ->where("app_client_request.status", 0)
            ->groupBy('app_client_request.id')
            ->get()->getResultArray();

        if (!empty($result)) {
            return $this->response->setJSON($result);
        } else {
            $result['status'] = 404;
            $result['message'] = "No category found";
            $result['data'] = null;
            return $this->response->setStatusCode(404)->setJSON($result);
        }
    }

    public function getCities(): ResponseInterface
    {
        $mdl = new CityModel();
        $result = $mdl->select('app_provinces.*')
            ->groupBy('id')
            ->get()->getResultArray();

        if (!empty($result)) {
            return $this->response->setJSON($result);
        } else {
            $result['status'] = 404;
            $result['message'] = "No city found";
            $result['data'] = null;
            return $this->response->setStatusCode(404)->setJSON($result);
        }
    }

    public function registerDriverCarTest(): ResponseInterface
    {
        $mdl = new DriverModel();
        $carMdl = new CarModel();

        $result = $mdl->select('app_provinces.*')
            ->groupBy('id')
            ->get()->getResultArray();

        if (!empty($result)) {
            return $this->response->setJSON($result);
        } else {
            $result['status'] = 404;
            $result['message'] = "No city found";
            $result['data'] = null;
            return $this->response->setStatusCode(404)->setJSON($result);
        }
    }

    /**
     * @throws ReflectionException
     * @throws \Exception
     */
    public function registerDriver(): ResponseInterface
    {
        $mdl = new DriverModel();
        $input = json_decode(file_get_contents("php://input"));

        $drive = $mdl->select("id,status")->where("telephone", $input->driverPhone)->first();

        if (!empty($drive)) {

            if ($drive['status'] == 0) {
                $message = "Driver account locked";
            } else {
                $message = "Driver already registered";
            }
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = $message;
            return $this->response->setStatusCode(400)->setJSON($result);
        }
        $diverId = $mdl->insert([
            'telephone' => $input->driverPhone,
            "status" => 1
        ]);

        $result['statusCode'] = 200;
        $result['status'] = true;
        $result['otp'] = $this->referenceNumber(4);
        $result['message'] = "Driver Registered Successfully";
        $result['driver_data'] = $mdl->where('id', $diverId)->get()->getResultArray();

        return $this->response->setJSON($result);
    }


    public function registerDriverCar(): ResponseInterface
    {
        $carMdl = new CarModel();
        $input = json_decode(file_get_contents("php://input"));

        if (empty($_POST['driverId'])) {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Invalid missing Driver information ";

            return $this->response->setStatusCode(404)->setJSON($result);
        }
        $imageFileFront = $this->request->getFile('image_front');
        $newNameFront = $imageFileFront->getRandomName();
        $imageFileFront->move('../public/assets/profile/cars/documents', $newNameFront);

        $imageFileBack = $this->request->getFile('image_back');
        $newNameBack = $imageFileBack->getRandomName();
        $imageFileBack->move('../public/assets/profile/cars/documents', $newNameBack);

        $imageFile = $this->request->getFile('image');
        $newName = $imageFile->getRandomName();
        $imageFile->move('../public/assets/profile/cars', $newName);

        $carData = [
            "car_name" => $_POST['carName'] ?? '',
            "car_seats" => $_POST['carSeat'] ?? '',
            "plate_number" => $_POST['carPlate'] ?? '',
            "category_id" => $_POST['carCategory'] ?? '',
            "color" => $_POST['carColor'] ?? '',
            "driver_id" => $_POST['driverId'],
            "document_front_image" => $newNameFront,
            "document_back_image" => $newNameBack,
            "photo" => $newName
        ];
        $carDetail = $carMdl->save($carData);
        $result['statusCode'] = 200;
        $result['status'] = true;
        $result['message'] = "Driver car registered successfully";
        $result['car_data'] = $carData;

        return $this->response->setJSON($result);
    }

    public function updateDriverInformation(): ResponseInterface
    {
        $mdl = new DriverModel();
        $input = json_decode(file_get_contents("php://input"));

        // var_dump($_POST['driverId']); die();
        if (empty($_POST['driverId'])) {

            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Driver identification not found";

            return $this->response->setStatusCode(400)->setJSON($result);
        }
        $imageFile = $this->request->getFile('image');
        $newName = $imageFile->getRandomName();
        $imageFile->move('../public/assets/profile/drivers', $newName);

        $driverId =  $_POST['driverId'];
        $mdl->save([
            'id' => $driverId,
            'name' => $_POST['driverName'] ?? '',
            'email' => $_POST['driverEmail'] ?? '',
            'photo' => $newName,
            'password' => password_hash($_POST['password'] ?? '123456', PASSWORD_DEFAULT),
            'address_location' => "United States",
            'status' => 1
        ]);

        $result['statusCode'] = 200;
        $result['status'] = true;
        $result['message'] = "Profile Updated Successfully";
        $result['driver_data'] = $mdl->where('id', $driverId)->get()->getResultArray();
        return $this->response->setJSON($result);
    }

    public function updateDriverInformationV2(): ResponseInterface
    {
        $mdl = new DriverModel();
        $input = json_decode(file_get_contents("php://input"));

        // var_dump($_POST['driverId']); die();
        if (empty($_POST['driverId'])) {

            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Driver identification not found";

            return $this->response->setStatusCode(400)->setJSON($result);
        }

        $driverId =  $_POST['driverId'];
        $mdl->save([
            'id' => $driverId,
            'name' => $_POST['driverName'] ?? '',
            'address_location' => "United States",
            'status' => 1
        ]);

        $result['statusCode'] = 200;
        $result['status'] = true;
        $result['message'] = "Profile Updated Successfully";
        $result['driver_data'] = $mdl->where('id', $driverId)->get()->getResultArray();
        return $this->response->setJSON($result);
    }
    /**
     * @throws ReflectionException
     * @throws \Exception
     */
    public function loginDriver(): ResponseInterface
    {
        $mdl = new DriverModel();
        $carMdl = new CarModel();
        $input = json_decode(file_get_contents("php://input"));

        $drive = $mdl->select("id")->where("telephone", $input->driverPhone)->where('status', 1)->first();

        if ($drive) {
            $result['statusCode'] = 200;
            $result['status'] = true;
            $result['message'] = "Account Already exist";
            $result['driver_data'] = $mdl->where('id', $drive['id'])->get()->getResultArray();
            $result['car_data'] = $carMdl->where('driver_id', $drive['id'])->get()->getResultArray();
            return $this->response->setStatusCode(200)->setJSON($result);
        } else {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Sorry, your account is locked";
            return $this->response->setStatusCode(400)->setJSON($result);
        }
    }

    public function clientLogin(): Response
    {
        $model = new ClientModel();
        $input = json_decode(file_get_contents('php://input'));
        try {
            $email = $input->email;
            $password = $input->password;

            $result = $model->checkClient("email='$email' or phone='$email'", null);
            if ($result != null) {
                if (password_verify($password, $result->password)) {
                    if ($result->status == 1 || $result->status == 2) {
                        // 			$model->save(['id' => $result->id, 'last_login' => time()]);
                        $key = sha1('SECRET_KEY' . ':' . $this->request->getServer("HTTP_USER_AGENT"));
                        $payload = array(
                            "iss" => base_url(),
                            "iat" => time(),
                            "exp" => time() + 3600,
                            "pht" => $result->photo,
                            "psw" => $result->password,
                            "uid" => $result->id,
                            "loc" => $result->address_location,
                        );
                        // $token = JWT::encode($payload, $key);
                        $token = sha1('CA' . uniqid(time()));
                        $data = array(
                            'id' => $result->id,
                            'names' => $result->names,
                            'phone' => $result->phone,
                            'email' => $result->email,
                            'gender' => $result->gender,
                            'password' => $result->password,
                            'photo' => $result->photo,
                            'category' => $result->category,
                            'address_location' => $result->address_location,
                            'status' => $result->status,
                        );

                        $res['statusCode'] = 200;
                        $res['status'] = true;
                        $res['message'] = "Login Registered Successfully";
                        $res['user_login_data'] = $data;

                        return $this->response->setStatusCode(200)->setJSON($res);
                    } else {
                        return $this->response->setStatusCode(400)->setJSON(array("statusCode" => 400, "status" => false, "message" => "Your account is locked"));
                    }
                } else {
                    return $this->response->setStatusCode(403)->setJSON(array("statusCode" => 400, "status" => false, "message" => "Your credentials is invalid"));
                }
            } else {
                return $this->response->setStatusCode(403)->setJSON(array("statusCode" => 400, "status" => false, "message" => "Your credentials is invalids"));
            }
        } catch (Exception $e) {
            return $this->response->setStatusCode(403)->setJSON(array("statusCode" => 400, "status" => false, "message" => "Provide all required data" . $e->getMessage() . " line" . $e->getLine()));
        }
    }

    /**
     * @throws ReflectionException
     * @throws \Exception
     */
    public function registerCustomer(): ResponseInterface
    {
        $mdl = new ClientModel();
        $cardMdl = new CardModel();
        $input = json_decode(file_get_contents("php://input"));
        $client = $mdl->select("id")->where("phone", $input->clientPhone)->first();
        $otp = $this->referenceNumber(4);

        if (!empty($client)) {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['otp'] = $otp;
            $result['message'] = "Passenger Already Registered";
            $result['client_data'] =
                $mdl->where('id', $client['id'])->get()->getRow();;
            return $this->response->setStatusCode(400)->setJSON($result);
        }

        $clientId = $mdl->insert([
            'phone' => $input->clientPhone,
        ]);

        $result['statusCode'] = 200;
        $result['status'] = true;
        $result['otp'] = $otp;
        $result['message'] = "Passenger Registered Successfully";
        $result['client_data'] = $mdl->where('id', $clientId)->get()->getRow();
        return $this->response->setJSON($result);
    }

    public function updateClientInformationV2(): ResponseInterface
    {
        $mdl = new ClientModel();
        $input = json_decode(file_get_contents("php://input"));

        if (empty($_POST['verifyId'])) {

            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Client identification not found";

            return $this->response->setStatusCode(400)->setJSON($result);
        }
        $imageFile = $this->request->getFile('image');
        $newName = $imageFile->getRandomName();
        $imageFile->move('../public/assets/profile/clients', $newName);

        $clientId =  $_POST['verifyId'];
        $mdl->save([
            'id' => $clientId,
            'names' => $_POST['clientName'] ?? '',
            'email' => $_POST['email'] ?? '',
            'gender' => $_POST['gender'] ?? '',
            'photo' => $newName,
            'password' => password_hash($_POST['password'] ?? '123456', PASSWORD_DEFAULT),
            'address_location' => "United States",
            'status' => 1
        ]);

        $result['statusCode'] = 200;
        $result['success'] = true;
        $result['message'] = "Profile Updated Successfully";
        $result['update_data'] = $mdl->where('id', $clientId)->first();
        return $this->response->setJSON($result);
    }
    public function updateClientInformation(): ResponseInterface
    {
        $mdl = new ClientModel();
        $input = json_decode(file_get_contents("php://input"));


        // var_dump($_POST['driverId']); die();
        if (empty($_POST['verifyId'])) {

            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Client identification not found";

            return $this->response->setStatusCode(400)->setJSON($result);
        }


        $clientId =  $_POST['verifyId'];
        $mdl->save([
            'id' => $clientId,
            'names' => $_POST['clientName'] ?? '',
            'photo' => 'photos.png',
            'status' => 1
        ]);

        $result['statusCode'] = 200;
        $result['success'] = true;
        $result['message'] = "Profile Updated Successfully";
        $result['update_data'] = $mdl->where('id', $clientId)->first();
        return $this->response->setJSON($result);
    }
    public function isDriverAvailableOnline(): ResponseInterface
    {
        $mdl = new DriverLocationModel();
        $input = json_decode(file_get_contents("php://input"));

        if (empty($input->driverId)) {
            return $this->response->setStatusCode(400)->setJSON(["message" => "Driver identification missing"]);
        }
        $id = $input->driverId;
        $driver = $mdl->select("id")->where("driver_id", $id)->first();

        if (!empty($driver)) {
            $data = $mdl->save([
                "id" => $driver['id'],
                "latitude" => $input->latitude,
                "longitude" => $input->longitude
            ]);
        } else {
            $data = $mdl->save([
                "driver_id" => $input->driverId,
                "car" => $input->car,
                "city" => $input->city,
                "driver_category" => $input->category,
                "driver_name" => $input->name,
                "driver_phone" => $input->phone,
                "is_online" => 1,
                "payment_method" => $input->payment_method,
                "model" => $input->model,
                "price" => $input->price,
                "latitude" => $input->latitude,
                "longitude" => $input->longitude
            ]);
        }

        $result['statusCode'] = 200;
        $result['status'] = true;
        $result['message'] = "Record Saved Successfully";
        $result['driver_data'] = $mdl->where('id', $id)->get()->getResultArray();
        return $this->response->setJSON($result);
    }
    public function switchMapAvailability(): ResponseInterface
    {
        $mdl = new DriverLocationModel();
        $input = json_decode(file_get_contents("php://input"));

        if (empty($input->driverId)) {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Swithing failed, missing data";
            $result['data'] = null;
            return $this->response->setStatusCode(400)->setJSON($result);
        }
        $id = $input->driverId;
        $driver = $mdl->select("id")->where("driver_id", $id)->first();
        $message = '';

        if ($input->status == "1") {
            $message = "Driver turned online successful";
        } else {
            $message = "Driver turned offline successful";
        }



        $data = $mdl->save([
            "id" => $driver['id'],
            "is_online" => $input->status,
        ]);

        $result['statusCode'] = 200;
        $result['status'] = true;
        $result['message'] = $message;
        $result['data'] = $mdl->select("id,is_online")->where("driver_id", $id)->get()->getRow();
        return $this->response->setJSON($result);
    }
    public function isDriverOnline(): ResponseInterface
    {
        $mdl = new DriverLocationModel();
        $input = json_decode(file_get_contents("php://input"));
        $id = $input->driverId;
        if (empty($id)) {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['statusData'] = null;
            $result['message'] = "Sorry , Driver identification missing";
            return $this->response->setStatusCode(400)->setJSON($result);
        }
        $driver = $mdl->select("id")->where("driver_id", $id)->where("is_online", 1)->first();

        if (!empty($driver)) {
            $data = 1;
        } else {
            $data = 0;
        }
        $result['statusCode'] = 200;
        $result['status'] = true;
        $result['statusData'] = $data;
        $result['message'] = "Wow , Driver found ";
        return $this->response->setJSON($result);
    }
    public function clientRequestRide($type = 0): ResponseInterface
    {
        $mdl = new ClientRequestModel();
        $input = json_decode(file_get_contents("php://input"));

        if (empty($input->clientId)) {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Sorry , Client identification missing";
            return $this->response->setStatusCode(400)->setJSON($result);
        }
        $id = $input->clientId;
        $request = $mdl->select("id")->where("client_id", $id)->where("status", 0)->first();

        try {

            if (!empty($request)) {
                $result['statusCode'] = 400;
                $result['status'] = false;
                $result['message'] = "Sorry , you still have pending request, cancel first to request another";

                return $this->response->setStatusCode(400)->setJSON($result);
            }

            $data = $mdl->save([
                "client_id" => $id,
                "trip_type" => $input->tripType,
                "trip_price" => $input->price,
                "source" => $input->source,
                "destination" => $input->destination,
                "s_latitude" => $input->sLatitude,
                "s_longitude" => $input->sLongitude,
                "d_latitude" => $input->dLatitude,
                "d_longitude" => $input->dLongitude,
                "accepted_by" => $input->acceptedBy ?? '',
                "status" => 0
            ]);


            $result['statusCode'] = 200;
            $result['status'] = true;
            $result['message'] = "Ride request sent successfully";
            $result['request_data'] = $mdl->select('app_client_request.*')->where("client_id", $id)->where('status', 0)->get()->getRow();
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            return $this->response->setJSON($e->getMessage());
        }
    }
    public function acceptClientRequest($id = 0): ResponseInterface
    {
        $mdl = new ClientRequestModel();
        $locationMdl = new DriverLocationModel();
        $input = json_decode(file_get_contents("php://input"));

        $id = $input->id;
        $driverId = $input->acceptedBy;
        $status = $input->status;

        if (empty($id)) {
            return $this->response->setStatusCode(400)->setJSON(["message" => "Request information missing"]);
        }
        $request = $mdl->select("id")->where("id", $id)->where("status", 0)->first();
        $drviverLocStatus = $locationMdl->select("id")->where("driver_id", $driverId)->where("is_online", 1)->first();
        try {

            if (empty($drviverLocStatus)) {
                $result['statusCode'] = 400;
                $result['status'] = false;
                $result['message'] = "Opps!, No available driver to approve request";
                $result['approve_data'] = null;

                return $this->response->setStatusCode(400)->setJSON($result);
            }

            if (empty($request)) {
                $result['statusCode'] = 400;
                $result['status'] = false;
                $result['message'] = "Opps!, No available client request";
                $result['approve_data'] = null;

                return $this->response->setStatusCode(400)->setJSON($result);
            }
            // Driver map status availabity 1: available, 2: working

            $data = [
                "id" => $id,
                "status" => $status,
                "accepted_by" => $driverId
            ];


            $res =  $mdl->save($data);

            $res2 = $locationMdl->save(["id" => $drviverLocStatus['id'], "is_online" => 2]);

            $result['statusCode'] = 200;
            $result['status'] = true;
            $result['message'] = "Request Approved Successfully";
            $result['approve_data'] = $data;

            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            return $this->response->setJSON($e->getMessage());
        }
    }
    public function approveCancellationClientRequest($id = 0): ResponseInterface
    {
        $mdl = new ClientRequestModel();
        $locationMdl = new DriverLocationModel();
        $input = json_decode(file_get_contents("php://input"));

        $id = $input->id;
        $driverId = $input->acceptedBy;

        if (empty($id)) {
            return $this->response->setStatusCode(400)->setJSON(["message" => "Request information missing"]);
        }
        $request = $mdl->select("id")->where("id", $id)->where("status", 2)->first();
        $drviverLocStatus = $locationMdl->select("id")->where("driver_id", $driverId)->where("is_online", 2)->first();
        try {

            if (empty($drviverLocStatus)) {
                $result['statusCode'] = 400;
                $result['status'] = false;
                $result['message'] = "Opps!, No available driver to approve request";
                $result['approve_data'] = null;

                return $this->response->setStatusCode(400)->setJSON($result);
            }

            if (empty($request)) {
                $result['statusCode'] = 400;
                $result['status'] = false;
                $result['message'] = "Opps!, No available client request";
                $result['approve_data'] = null;

                return $this->response->setStatusCode(400)->setJSON($result);
            }
            // Driver map status availabity 1: available, 2: working
            $data = [
                "id" => $id,
                "accepted_by" => "0"
            ];

            $res =  $mdl->save($data);

            $res2 = $locationMdl->save(["id" => $drviverLocStatus['id'], "is_online" => 1]);

            $result['statusCode'] = 200;
            $result['status'] = true;
            $result['message'] = "Request Approved Successfully";
            $result['approve_data'] = $data;

            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            return $this->response->setJSON($e->getMessage());
        }
    }    public function watchCancelledClientRequest(): ResponseInterface
    {
        $mdl = new ClientRequestModel();
        $input = json_decode(file_get_contents("php://input"));

        $driverId = $input->acceptedBy;

        if (empty($driverId)) {
            return $this->response->setStatusCode(400)->setJSON(["message" => "Request information missing"]);
        }
        $query = $mdl->select("app_client_request.*")->where("accepted_by", $driverId)->where("status", 2)->first();
        try {
            if (empty($query)) {
                $result['statusCode'] = 400;
                $result['status'] = false;
                $result['message'] = "Opps!, No available client cancel request";
                $result['cancelled_data'] = null;

                return $this->response->setStatusCode(400)->setJSON($result);
            } else {
                $result['statusCode'] = 200;
                $result['status'] = true;
                $result['message'] = "Request Approved Successfully";
                $result['cancelled_data'] = $query;

                return $this->response->setJSON($result);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON($e->getMessage());
        }
    }

    public function getMyAcceptedRequest(): ResponseInterface
    {
        $mdl = new ClientRequestModel();
        $input = json_decode(file_get_contents("php://input"));

        $clientId = $input->clientId;

        if (empty($clientId)) {
            return $this->response->setStatusCode(400)->setJSON(["message" => "Client information missing"]);
        }
        $query = $mdl->select('app_client_request.id, ad.id as driver_id,ad.name as driver_name, ad.telephone as driver_phone,ad.photo as driver_photo,c.color as car_color,c.photo as car_photo, c.car_name,c.car_seats,c.plate_number as car_plate_number,app_client_request.status')
            ->join("app_clients ac", "ac.id = app_client_request.client_id", "LEFT")
            ->join("app_driver ad", "ad.id = app_client_request.accepted_by", "LEFT")
            ->join("app_cars c", "c.driver_id = ad.id", "LEFT")
            ->where("app_client_request.status", 1)
            ->where("app_client_request.client_id", $clientId)
            ->where("date_format(app_client_request.created_at, '%Y-%m-%d')", date('Y-m-d'))
            ->orderBy('app_client_request.id', 'DESC')
            ->get(1)->getRow();

        // var_dump(date('Y-m-d'));die();
        if (!empty($query)) {
            $result["statusCode"] = 200;
            $result["success"] = true;
            $result["message"] = "Data found";
            $result["driver_found_data"] = $query;
            return $this->response->setJSON($result);
        } else {
            $result["statusCode"] = 400;
            $result["success"] = false;
            $result["message"] = "Data not found";
            $result["driver_found_data"] = [];
            return $this->response->setStatusCode(404)->setJSON($result);
        }
    }

    public function getRequestFromClient($driverId = 0): ResponseInterface
    {
        $mdl = new ClientRequestModel();

        if (empty($driverId)) {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Driver information missing";

            return $this->response->setStatusCode(400)->setJSON($result);
        }
        $query = $mdl->select('app_client_request.id, ac.names as client_name, ac.phone as client_phone,ac.category,trip_price, source,destination
            ,s_latitude,s_longitude,d_latitude,d_longitude')
            ->join("app_clients ac", "ac.id = app_client_request.client_id", "LEFT")
            ->join("app_driver ad", "ad.id = app_client_request.accepted_by", "LEFT")
            ->join("app_cars c", "c.driver_id = ad.id", "LEFT")
            ->where("app_client_request.status", 0)
            ->where("app_client_request.accepted_by", $driverId)
            ->orderBy('app_client_request.id', 'DESC')
            ->get(1)->getRow();

        if (!empty($query)) {
            $result["status"] = 200;
            $result["message"] = "Seccuccful exist";
            $result["data"] = $query;
            return $this->response->setJSON($result);
        } else {
            return $this->response->setStatusCode(400)->setJSON($query);
        }
    }

    public function processingPayment(): ResponseInterface
    {
        $mdl = new PaymentModel();
        $input = json_decode(file_get_contents("php://input"));
        try {
            $data = $mdl->save([
                "booking_id" => $input->bookId,
                "paid_amount" => $input->amount,
                "payment_mode" => $input->mode,
                "payment_status" => $input->status,
                "reference_no" => " ",
                "account_no" => " "
            ]);

            $result['status'] = 200;
            $result['message'] = "Payment Saved Successfully";
            $result['data'] = $data;
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            return $this->response->setJSON($e->getMessage());
        }
    }

    public function getNearbyClientRequest_v3(): ResponseInterface
    {
        $mdl = new ClientRequestModel();
        $input = json_decode(file_get_contents("php://input"));
        $currentLat = $input->latitude;
        $currentLng = $input->longitude;
        $currentRenge = $input->radius;

        $query = $mdl->getNearbyClientRequestByRange($currentLat, $currentLng, $currentRenge);

        if (empty($query)) {

            $result['statusCode'] = 400;
            $result['success'] = false;
            $result['message'] = "Sorry, No Nearby client found";
            $result['nearby_client_data'] = null;

            return $this->response->setStatusCode(400)->setJSON($result);
        } else {
            $result['statusCode'] = 200;
            $result['success'] = true;
            $result['message'] = " Nearby client found";
            $result['nearby_client_data'] = $query;

            return $this->response->setStatusCode(200)->setJSON($result);
        }
    }

    public function getDailyReport($driverId = 0): ResponseInterface
    {
        $mdl = new PaymentModel();
        $query = $mdl->select('sum(paid_amount) as accountBalance, count(ac.id) as completedTrip')
            ->join("app_client_request ac", "ac.id = app_payments.booking_id", "LEFT")
            ->where("ac.accepted_by", $driverId)
            ->get(1)->getRow();

        if (!empty($query)) {

            return $this->response->setJSON($query);
        } else {
            return $this->response->setStatusCode(404)->setJSON($query);
        }
    }

    function referenceNumber($length = 10)
    {
        $characters = '0123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }



    public function clientBookingRide(): ResponseInterface
    {
        $mdl = new BookingModel();
        $input = json_decode(file_get_contents("php://input"));

        if (empty($clientId)) {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Client information missing";

            return $this->response->setStatusCode(400)->setJSON($result);
        }
        $id = $input->clientId;

        try {
            if ($input->dueDate < date('Y-m-d')) {
                $result['status'] = 404;
                $result['message'] = "Sorry; Invalid date you pick is in past";
                return $this->response->setStatusCode(404)->setJSON($result);
            } else {
                $data = $mdl->save([
                    "client_id" => $id,
                    "trip_refference_no" => $this->referenceNumber(),
                    "trip_type" => "BOOKING",
                    "vehi_type" => $input->carType,
                    "trip_price" => $input->price,
                    "trip_date" => $input->dueDate,
                    "trip_time" => $input->dueTime,
                    "source_location" => $input->source,
                    "destination_location" => $input->destination,
                    "source_lat" => $input->sLatitude,
                    "source_lng" => $input->sLongitude,
                    "destination_lat" => $input->dLatitude,
                    "destination_lng" => $input->dLongitude,
                    "approved_by" => 0,
                    "status" => 0
                ]);
                $result['status'] = 200;
                $result['message'] = "Order successfully received";
                $result['data'] = $data;
                return $this->response->setStatusCode(200)->setJSON($result);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON($e->getMessage());
        }
    }

    public function getMyBookingRequest($clientId = 0): ResponseInterface
    {
        $mdl = new BookingModel();
        $result = $mdl->select('app_booking.*,d.name as driver,d.telephone as driver_phone,d.category')
            ->join("app_driver_booking ad", "ad.id = app_booking.approved_by", "left")
            ->join("app_driver d", "d.id = ad.driver_id", "left")
            ->where("app_booking.client_id", $clientId)
            ->orderBy('app_booking.status', 'DESC')
            ->get()->getResultArray();

        // var_dump(date('Y-m-d'));die();
        if (!empty($result)) {
            return $this->response->setJSON($result);
        } else {
            return $this->response->setStatusCode(404)->setJSON($result);
        }
    }

    public function saveClientPickupLocation(): ResponseInterface
    {
        $mdl = new PickupLocationModel();
        $input = json_decode(file_get_contents("php://input"));

        if (empty($input->clientId)) {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Client information missing";

            return $this->response->setStatusCode(400)->setJSON($result);
        }
        $id = $input->clientId;

        try {
            $data = $mdl->insert([
                "client_id" => $id,
                "title" => $input->title ?? '',
                "phone" => $input->phone ?? '',
                "address" => $input->address ?? "",
                "latitude" => $input->latitude,
                "longitude" => $input->longitude,
                "status" => 1
            ]);

            $result['statusCade'] = 200;
            $result['status'] = true;
            $result['message'] = "Record Saved Successfully";
            $result['location_data'] = $mdl->where('id', $data)->first();
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            return $this->response->setJSON($e->getMessage());
        }
    }

    public function getClientPickUpLocation($id = 0): ResponseInterface
    {
        $mdl = new PickupLocationModel();
        $input = json_decode(file_get_contents("php://input"));

        if (empty($input->clientId)) {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Client information missing";
            $result['address_records'] = [];
            return $this->response->setStatusCode(400)->setJSON($result);
        }
        $clientId = $input->clientId;

        $query = $mdl->select('app_client_pickup_locations.*')
            ->where("app_client_pickup_locations.client_id", $clientId)
            ->orderBy('app_client_pickup_locations.id', 'DESC')
            ->get()->getResultArray();

        if (!empty($query)) {
            $result['statusCode'] = 200;
            $result['status'] = true;
            $result['message'] = "Get data success";
            $result['address_records'] = $query;
        } else {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Get data success";
            $result['address_records'] = $query;
        }
        return $this->response->setJSON($result);
    }


    public function getMyHistory($clientId = 0): ResponseInterface
    {
        $mdl = new ClientRequestModel();
        $query = $mdl->select('app_client_request.*')
            ->where("app_client_request.client_id", $clientId)
            ->where("app_client_request.status", 1)
            ->orderBy('app_client_request.id', 'DESC')
            ->get()->getResultArray();

        if (!empty($query)) {
            $result['statusCode'] = 200;
            $result['status'] = true;
            $result['message'] = "Get data missing";
            $result['data'] = $query;
            return $this->response->setStatusCode(200)->setJSON($result);
        } else {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "No data missing";
            $result['data'] = $query;
            return $this->response->setStatusCode(400)->setJSON($result);
        }
    }

    public function getClientPickUpLocationV2(): ResponseInterface
    {
        //$this->appendHeader();
        $mdl = new PickupLocationModel();
        $input = json_decode(file_get_contents("php://input"));


        if (empty($input->clientId)) {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Client information missing";
            $result['address_records'] = [];
            return $this->response->setStatusCode(400)->setJSON($result);
        }

        $clientId = $input->clientId;
        $query = $mdl->select('app_client_pickup_locations.*')
            ->where("app_client_pickup_locations.client_id", $clientId)
            ->orderBy('app_client_pickup_locations.id', 'DESC')
            ->get()->getResultArray();

        if (!empty($query)) {
            $result['statusCode'] = 200;
            $result['status'] = true;
            $result['message'] = "Get data success";
            $result['address_records'] = $query;
            return $this->response->setJSON($result);
        } else {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "No data missing";
            $result['address_records'] = $query;
            return $this->response->setStatusCode(400)->setJSON($result);
        }
    }
    public function getClientNearbyDriver(): ResponseInterface
    {
        $mdl = new DriverLocationModel();
        $input = json_decode(file_get_contents("php://input"));
        if (empty($input->latitude) && empty($input->longitude) && empty($input->radius)) {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Request nearby driver failed, some information missing";

            return $this->response->setStatusCode(400)->setJSON($result);
        }
        $currentLat = $input->latitude;
        $currentLng = $input->longitude;
        $currentRenge = $input->radius;
        $paymentMethod = $input->paymentMethod;

        $query = $mdl->getNearbyDriverByRange($currentLat, $currentLng, $currentRenge, $paymentMethod);

        if (empty($query)) {

            $result['statusCode'] = 400;
            $result['success'] = false;
            $result['message'] = "Sorry, No Nearby driver found";
            $result['nearby_driver_data'] = null;

            return $this->response->setStatusCode(400)->setJSON($result);
        } else {
            $result['statusCode'] = 200;
            $result['success'] = true;
            $result['message'] = " Nearby driver found";
            $result['nearby_driver_data'] = $query;

            return $this->response->setStatusCode(200)->setJSON($result);
        }
    }

    public function cancelRideRequest(): ResponseInterface
    {
        $mdl = new RequestCancellationModel();
        $requestMdl = new ClientRequestModel();
        $input = json_decode(file_get_contents("php://input"));

        if (empty($input->requestId)) {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Ride request information missing";

            return $this->response->setStatusCode(400)->setJSON($result);
        }
        $id = $input->requestId;
        $requestId = $requestMdl->select("id")->where("id", $id)->where("date_format(created_at, '%Y-%m-%d')", date('Y-m-d'))->whereIn("status", [1, 0])->first();
        try {
            if ($requestId) {
                $requestMdl->save(["id" => $requestId, "status" => 2]);

                $data = $mdl->save([
                    "request_id" => $id,
                    "feedback" => $input->feedback ?? '',
                    "status" => 1
                ]);

                $result['statusCade'] = 200;
                $result['status'] = $data;
                $result['message'] = "Ride Cancelled Successfully";
                return $this->response->setJSON($result);
            } else {

                $result['statusCode'] = 400;
                $result['status'] = false;
                $result['message'] = "Unable to Cancel your Ride";
                return $this->response->setJSON($result);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON($e->getMessage());
        }
    }

    public function getClientInfo(): Response
    {

        $mdl = new ClientModel();
        $requestMdl = new ClientRequestModel();
        $locModl = new PickupLocationModel();
        $input = json_decode(file_get_contents("php://input"));

        $id =  $input->clientId;

        if (empty($id) && !isset($id)) {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Profile not found";
            $result['profile_data'] = [];
            return $this->response->setStatusCode(400)->setJSON($result);
        }

        $client = $mdl->where('id', $id)->first();

        $requests = $requestMdl->select('app_client_request.*')
            ->where("app_client_request.client_id", $id)
            ->orderBy('app_client_request.id', 'DESC')
            ->get()->getResultArray();

        $addresses = $locModl->select('app_client_pickup_locations.*')
            ->where("app_client_pickup_locations.client_id", $id)
            ->orderBy('app_client_pickup_locations.id', 'DESC')
            ->get()->getResultArray();

        $result['statusCode'] = 200;
        $result['status'] = true;
        $result['message'] = "Profile Updated Successfully";
        $result['profile_data'] = $client;
        $result['addresses_data'] = $addresses;
        $result['requests_data'] = $requests;

        return $this->response->setJSON($result);
    }

    public function getClientHistorys(): Response
    {

        $requestMdl = new ClientRequestModel();
        $input = json_decode(file_get_contents("php://input"));

        $id =  $input->clientId;

        if (empty($id) && !isset($id)) {
            $result['statusCode'] = 400;
            $result['status'] = false;
            $result['message'] = "Profile not found";
            $result['profile_data'] = [];
            return $this->response->setStatusCode(400)->setJSON($result);
        }

        $requests = $requestMdl->select('app_client_request.*')
            ->where("app_client_request.client_id", $id)
            ->orderBy('app_client_request.id', 'DESC')
            ->get()->getResultArray();


        $result['statusCode'] = 200;
        $result['status'] = true;
        $result['message'] = "Profile Updated Successfully";
        $result['requests_data'] = $requests;

        return $this->response->setJSON($result);
    }
}
