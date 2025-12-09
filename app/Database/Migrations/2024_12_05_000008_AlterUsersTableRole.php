<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersTableRole extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('users', [
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['admin', 'teacher', 'student'],
                'default' => 'student',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('users', [
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['admin', 'staff'],
                'default' => 'staff',
            ],
        ]);
    }
}
