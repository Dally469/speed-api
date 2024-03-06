<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TblRequestCancellationModel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'request_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'feedback' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'status' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addPrimaryKey(('id'));
        $this->forge->createTable("app_request_cancellation");
    }

    public function down()
    {
        //
    }
}
