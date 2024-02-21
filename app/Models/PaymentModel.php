<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model {
    protected $table = 'app_payments';

    protected $primaryKey = 'id';
    protected $allowedFields = ['id','booking_id','paid_amount','payment_mode','payment_status','reference_no','account_no'];
    protected $useTimestamps = true;
}
