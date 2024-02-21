<?php

namespace App\Models;

use CodeIgniter\Model;

class RequestCancellationModel extends Model {
    protected $table = 'app_request_cancellation';

    protected $primaryKey = 'id';
    protected $allowedFields = ['id','request_id','feedback','status'];
    protected $useTimestamps = false;
}
