<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin is created by migration (AddAdminUser). Seeder should be safe to run multiple times.
        $now = date('Y-m-d H:i:s');
        $users = [
            [
                'name'       => 'John Student',
                'email'      => 'john.student@example.com',
                'password'   => password_hash('student123', PASSWORD_DEFAULT),
                'role'       => 'student',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Jane Student',
                'email'      => 'jane.student@example.com',
                'password'   => password_hash('student123', PASSWORD_DEFAULT),
                'role'       => 'student',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Alice Instructor',
                'email'      => 'alice.instructor@example.com',
                'password'   => password_hash('instructor123', PASSWORD_DEFAULT),
                'role'       => 'teacher',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name'       => 'Bob Instructor',
                'email'      => 'bob.instructor@example.com',
                'password'   => password_hash('instructor123', PASSWORD_DEFAULT),
                'role'       => 'teacher',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $table = $this->db->table('users');

        foreach ($users as $user) {
            $existing = $table->select('id')->where('email', $user['email'])->get()->getRowArray();

            if ($existing) {
                // Don't overwrite created_at for existing rows
                unset($user['created_at']);
                $table->where('id', $existing['id'])->update($user);
            } else {
                $table->insert($user);
            }
        }
    }
}
