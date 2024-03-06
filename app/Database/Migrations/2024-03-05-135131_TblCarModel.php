<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TblCarModel extends Migration
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
            'car_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'car_seats' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'plate_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'photo' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'document_front_image' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'document_back_image' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'category_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'extra' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addPrimaryKey(('id'));
        $this->forge->createTable("app_cars");
    }

    public function down()
    {
        //
    }
}
