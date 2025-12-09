<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSaleItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'sale_id' => [
                'type' => 'INT',
            ],
            'product_id' => [
                'type' => 'INT',
            ],
            'quantity' => [
                'type' => 'INT',
                'default' => 1,
            ],
            'unit_price' => [
                'type' => 'DECIMAL',
                'constraint' => [10, 2],
            ],
            'total_price' => [
                'type' => 'DECIMAL',
                'constraint' => [12, 2],
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
        $this->forge->addForeignKey('sale_id', 'sales', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('sale_items');
    }

    public function down()
    {
        $this->forge->dropTable('sale_items');
    }
}
