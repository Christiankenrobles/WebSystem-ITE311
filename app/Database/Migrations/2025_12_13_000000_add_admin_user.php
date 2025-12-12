<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class AddAdminUser extends Migration
{
    public function up()
    {
        // Add a new admin user
        $data = [
            'name'       => 'Administrator',
            'username'   => 'admin',
            'email'      => 'admin@example.com',
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'role'       => 'admin',
            'created_at' => new RawSql('CURRENT_TIMESTAMP'),
            'updated_at' => new RawSql('CURRENT_TIMESTAMP')
        ];

        // Check if user already exists
        $db = \Config\Database::connect();
        $user = $db->table('users')
                  ->where('username', 'admin')
                  ->orWhere('email', 'admin@example.com')
                  ->get()
                  ->getRow();

        if (!$user) {
            $this->db->table('users')->insert($data);
            echo "Admin user created successfully.\n";
            echo "Username: admin\n";
            echo "Password: admin123\n";
            echo "Please change this password after first login.\n";
        } else {
            echo "Admin user already exists.\n";
        }
    }

    public function down()
    {
        // Remove the admin user (optional)
        $this->db->table('users')
                ->where('username', 'admin')
                ->orWhere('email', 'admin@example.com')
                ->delete();
    }
}
