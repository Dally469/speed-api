<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TblClientRequestModel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'client_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'trip_type' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
            ],
            'trip_price' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'source' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'destination' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            's_latitude' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            's_longitude' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'd_latitude' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'd_longitude' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'status' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'accepted_by' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addPrimaryKey(('id'));
        $this->forge->createTable("app_client_request");
    }

    public function down()
    {
        //
    }
}
