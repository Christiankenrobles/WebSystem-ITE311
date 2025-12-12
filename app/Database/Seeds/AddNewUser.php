<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class AddNewUser extends Seeder
{
    public function run()
    {
        $data = [
            'name'       => 'New User',
            'username'   => 'newuser',
            'email'      => 'newuser@example.com',
            'password'   => password_hash('NewUser@123', PASSWORD_DEFAULT),
            'role'       => 'student',
            'created_at' => Time::now(),
            'updated_at' => Time::now()
        ];

        // Check if user already exists
        $db = \Config\Database::connect();
        $user = $db->table('users')
                  ->where('username', 'newuser')
                  ->orWhere('email', 'newuser@example.com')
                  ->get()
                  ->getRow();

        if (!$user) {
            $db->table('users')->insert($data);
            echo "New user created successfully!\n";
            echo "Username: newuser\n";
            echo "Password: NewUser@123\n";
            echo "Role: student\n";
            echo "Please change this password after first login.\n";
        } else {
            echo "This user already exists.\n";
        }
    }
}
