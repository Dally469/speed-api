<?php

namespace App\Database\Migrations;

use App\Models\User;
use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class Users extends Migration
{
    public function up()
    {
        //
        $this->forge->addField([
            'id'               => [
                'type'       => 'VARCHAR',
                'constraint' => '36',
                'unique'     => true,
            ],
            'role_id'          => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
            ],
            'names'            => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'avatar'           => [
                'type'       => 'VARCHAR',
                'constraint' => '250',
                'null'       => true,
            ],
            'telephone_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'unique'     => true,
            ],
            'country'          => [
                'type'       => 'VARCHAR',
                'constraint' => '4',
                'default'    => 'RW'
            ],
            'email'            => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'password'         => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'status'           => [
                'type'       => "ENUM",
                'constraint' => User::STATUS,
                'default'    => User::ACTIVE,
                'null'       => false,
            ],
            'created_at'       => [
                'type'    => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at'       => [
                'type'      => 'TIMESTAMP',
                'default'   => new RawSql('CURRENT_TIMESTAMP'),
                'on update' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'deleted_at'       => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
