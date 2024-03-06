<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TblDriverModel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'telephone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'photo' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'category' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],

            'status' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addPrimaryKey(('id'));
        $this->forge->createTable("app_driver");
    }

    public function down()
    {
        //
    }
}
