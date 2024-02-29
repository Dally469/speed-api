<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\Roles;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $data = [
           [
               "name" => "ADMINISTROR",
           ],
           [
               "name" => "MANAGER",
           ],
        ];
        $role = $this->db->table((new Roles())->getTable());
        $this->db->disableForeignKeyChecks();
        $role->truncate();
        $role->insertBatch($data);
        $this->db->enableForeignKeyChecks();
    }
}
