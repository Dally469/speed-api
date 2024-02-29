<?php

namespace App\Controllers\Api\Web;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index(): ResponseInterface
    {
        // Your logic to fetch data
        $data = [
            'message' => 'Welcome to the App Dashboard API endpoint',
        ];
        return $this->getResponse($data);
    }
}
