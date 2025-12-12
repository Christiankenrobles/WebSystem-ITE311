<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToEnrollments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('enrollments', [
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'approved',
                'null' => false,
                'after' => 'enrollment_date',
            ],
        ]);

        // Backfill existing rows
        $this->db->query("UPDATE enrollments SET status = 'approved' WHERE status IS NULL OR status = ''");

        // Prevent duplicate enrollments
        $this->db->query('ALTER TABLE enrollments ADD UNIQUE KEY user_course_unique (user_id, course_id)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE enrollments DROP INDEX user_course_unique');
        $this->forge->dropColumn('enrollments', 'status');
    }
}
