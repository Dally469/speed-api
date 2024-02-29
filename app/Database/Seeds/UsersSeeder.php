<?php

namespace App\Database\Seeds;

use App\Models\Roles;
use App\Models\User;
use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class UsersSeeder extends Seeder
{
    /**
     * @throws \Exception
     */
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $role = $this->db->table((new Roles())->getTable())
            ->select('id', 'name')
            ->where('name', 'ADMINISTROR')
            ->get()
            ->getFirstRow();
        if ($role) {
            $data = [
                'id'       => Uuid::uuid4()->toString(),
                'role_id'  => $role->id,
                'names'    => 'Administrator',
                'country'  => 'RW',
                'email'    => 'admin@admin.com',
                'password' => password_hash('Pw@2024!!!', PASSWORD_ARGON2I),
                'status'   => User::ACTIVE,
            ];
            $user = $this->db->table((new User())->getTable());
            $user->truncate();
            $user->insert($data);
        }
        $this->db->enableForeignKeyChecks();
    }
}
