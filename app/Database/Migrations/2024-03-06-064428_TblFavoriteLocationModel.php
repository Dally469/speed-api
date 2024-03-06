<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TblFavoriteLocationModel extends Migration
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
                'constraint' => 11,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'phone' => [
                'type' => 'VARCHAR',
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

            'status' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addPrimaryKey(('id'));
        $this->forge->createTable("app_client_pickup_locations");
    }

    public function down()
    {
        //
    }
}
