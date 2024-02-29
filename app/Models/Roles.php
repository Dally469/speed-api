<?php

namespace App\Models;

use CodeIgniter\Model;

class Roles extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'roles';
    protected $allowedFields    = ['name'];
    protected $returnType = 'object';

    // Dates
    protected $useTimestamps = true;

    // Validation
    protected $validationRules      = [
        'name' => 'required|max_length[30]|alpha_numeric_space|min_length[3]|is_unique[roles.name]'
    ];
    protected $validationMessages   = [
        'name' => [
            'is_unique' => 'Sorry. That name has already been created!',
        ],
    ];

    public function getTable()
    {
        return $this->table;
    }

    public function users()
    {

    }

}
