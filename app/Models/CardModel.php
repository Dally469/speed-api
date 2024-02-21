<?php

namespace App\Models;

use CodeIgniter\Model;

class CardModel extends Model {
    protected $table = 'app_clients_cards';

    protected $primaryKey = 'id';
    protected $allowedFields = ['id','client_id','card_number','card_uuid','card_pin','balance','status'];
    protected $useTimestamps = false;
}
