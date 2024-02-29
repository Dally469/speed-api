<?php

namespace App\Models;

use App\Traits\UuidTrait;
use CodeIgniter\Model;

class User extends Model
{
    use UuidTrait;

    protected $table            = 'users';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields = false;

    // Dates
    protected $useTimestamps = true;

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];

    public const STATUS = [
        'ACTIVE', 'DISABLED',
    ];
    public const ACTIVE = self::STATUS[0];
    public const DISABLED = self::STATUS[1];

    public function getTable()
    {
        return $this->table;
    }

    public function insert($data = null, bool $returnID = true)
    {
        if (! isset($data['id'])) {
            $data['id'] = $this->generateUuid();
        }

        return parent::insert($data, $returnID);
    }
}
