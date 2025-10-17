<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title'        => 'Introduction to PHP',
                'description'  => 'Learn the basics of PHP programming language.',
                'instructor_id' => 4, // Alice Instructor
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'title'        => 'Advanced JavaScript',
                'description'  => 'Deep dive into JavaScript concepts and frameworks.',
                'instructor_id' => 4, // Alice Instructor
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'title'        => 'Database Design',
                'description'  => 'Understand relational databases and SQL.',
                'instructor_id' => 5, // Bob Instructor
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'title'        => 'Web Development Basics',
                'description'  => 'HTML, CSS, and basic web development principles.',
                'instructor_id' => 5, // Bob Instructor
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'title'        => 'Python for Beginners',
                'description'  => 'Start your journey with Python programming.',
                'instructor_id' => 4, // Alice Instructor
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert all courses at once
        $this->db->table('courses')->insertBatch($data);
    }
}
