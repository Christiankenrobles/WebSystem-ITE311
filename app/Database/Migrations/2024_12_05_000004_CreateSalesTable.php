<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSalesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'invoice_no' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'customer_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => [12, 2],
                'default' => 0,
            ],
            'paid_amount' => [
                'type' => 'DECIMAL',
                'constraint' => [12, 2],
                'default' => 0,
            ],
            'change_amount' => [
                'type' => 'DECIMAL',
                'constraint' => [12, 2],
                'default' => 0,
            ],
            'payment_method' => [
                'type' => 'ENUM',
                'constraint' => ['cash', 'card', 'check', 'online'],
                'default' => 'cash',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['completed', 'cancelled', 'pending'],
                'default' => 'completed',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('sales');
    }

    public function down()
    {
        $this->forge->dropTable('sales');
    }
}
