<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CarCategory extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'trip_refference_no' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'vehi_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'trip_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'trip_date' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'trip_time' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'client_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'trip_price' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'source_location' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'destination_location' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'source_lat' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'source_lng' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'destination_lat' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'destination_lng' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'status' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'approved_by' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addPrimaryKey(('id'));
        $this->forge->createTable("app_booking");
    }

    public function down()
    {
        //
    }
}
