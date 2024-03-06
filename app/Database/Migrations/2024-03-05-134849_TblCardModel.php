<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TblCardModel extends Migration
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
            'card_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'card_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'card_pin' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'balance' => [
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
        $this->forge->createTable("app_clients_cards");
    }

    public function down()
    {
        //
    }
}
