<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TblDriverDispatchModel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'driver_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'booking_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
             
            'status' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
             
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addPrimaryKey(('id'));
        $this->forge->createTable("app_driver_trip_dispatch");
    }

    public function down()
    {
        //
    }
}
