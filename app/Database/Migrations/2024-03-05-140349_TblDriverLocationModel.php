<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TblDriverLocationModel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'car' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'driver_category' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'driver_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'driver_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'driver_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],

            'is_online' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'latitude' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'longitude' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'model' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'price' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],

            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addPrimaryKey(('id'));
        $this->forge->createTable("app_driver_locations");
    }

    public function down()
    {
        //
    }
}
