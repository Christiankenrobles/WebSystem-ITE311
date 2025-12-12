<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddScheduleToCourses extends Migration
{
    public function up()
    {
        $fields = [
            'schedule_days' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Days of week (e.g., "Monday,Wednesday,Friday" or "MWF")'
            ],
            'schedule_time_start' => [
                'type' => 'TIME',
                'null' => true,
                'comment' => 'Class start time'
            ],
            'schedule_time_end' => [
                'type' => 'TIME',
                'null' => true,
                'comment' => 'Class end time'
            ],
            'schedule_location' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Class location/room'
            ],
            'schedule_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'default' => 'regular',
                'comment' => 'Type: regular, online, hybrid'
            ],
        ];

        $this->forge->addColumn('courses', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', [
            'schedule_days',
            'schedule_time_start',
            'schedule_time_end',
            'schedule_location',
            'schedule_type'
        ]);
    }
}

