<?php namespace App\Controllers\Api\Web;

use CodeIgniter\RESTful\ResourceController;

class Dashboard extends ResourceController
{
    public function index()
    {
        // Your logic to fetch data
        $data = [
            'message' => 'Welcome to the App Dashboard API endpoint',
        ];

        return $this->respond($data);
    }
}
