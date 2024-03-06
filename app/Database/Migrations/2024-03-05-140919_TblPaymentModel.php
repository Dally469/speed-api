<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TblPaymentModel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'booking_id' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'paid_amount' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'payment_mode' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'payment_status' => [
                'type' => 'INT',
                'constraint' => 10,
            ],
            'reference_no' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'account_no' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
 ,
            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
        ]);
        $this->forge->addPrimaryKey(('id'));
        $this->forge->createTable("app_payments");
    }

    public function down()
    {
        //
    }
}
