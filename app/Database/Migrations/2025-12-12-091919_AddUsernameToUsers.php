<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsernameToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'name'
            ],
        ]);
        
        // Add unique index for username
        $this->forge->addKey('username', false, true);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'username');
    }
}
