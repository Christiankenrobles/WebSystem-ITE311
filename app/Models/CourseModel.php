<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'title',
        'description',
        'instructor_id',
        'created_at',
        'updated_at',
        'schedule_days',
        'schedule_time_start',
        'schedule_time_end',
        'schedule_location',
        'schedule_type',
    ];

    protected $useTimestamps = false;

    public function getUserEnrollments($userId)
    {
        return $this->db->table('enrollments')
            ->join('courses', 'enrollments.course_id = courses.id')
            ->where('enrollments.user_id', $userId)
            ->groupStart()
                ->where('enrollments.status', 'approved')
                ->orWhere('enrollments.status IS NULL', null, false)
            ->groupEnd()
            ->select('courses.*, enrollments.enrollment_date')
            ->get()
            ->getResultArray();
    }

    public function getAvailableCourses($userId)
    {
        // Return courses not enrolled OR pending, and include enrollment status for the current user
        return $this->db->table($this->table)
            ->select('courses.*, enrollments.status as enrollment_status')
            ->join(
                'enrollments',
                "enrollments.course_id = courses.id AND enrollments.user_id = " . (int) $userId,
                'left'
            )
            ->groupStart()
                ->where('enrollments.status IS NULL', null, false)
                ->orWhere('enrollments.status', 'pending')
            ->groupEnd()
            ->get()
            ->getResultArray();
    }

    /**
     * Search courses by title or description
     * 
     * @param string $searchTerm The search query
     * @return array Array of matching courses
     */
    public function searchCourses($searchTerm)
    {
        if (empty($searchTerm)) {
            return $this->findAll();
        }

        return $this->where('title LIKE', "%$searchTerm%")
                    ->orWhere('description LIKE', "%$searchTerm%")
                    ->findAll();
    }

    /**
     * Search courses by title only
     * 
     * @param string $searchTerm The search query
     * @return array Array of matching courses
     */
    public function searchByTitle($searchTerm)
    {
        if (empty($searchTerm)) {
            return $this->findAll();
        }

        return $this->where('title LIKE', "%$searchTerm%")->findAll();
    }

    /**
     * Advanced search with multiple filters
     * 
     * @param string $searchTerm The search query
     * @param int $instructorId Optional instructor filter
     * @return array Array of matching courses
     */
    public function advancedSearch($searchTerm, $instructorId = null)
    {
        $builder = $this->builder();

        if (!empty($searchTerm)) {
            $builder->where('title LIKE', "%$searchTerm%")
                    ->orWhere('description LIKE', "%$searchTerm%");
        }

        if ($instructorId !== null) {
            $builder->where('instructor_id', $instructorId);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get courses by instructor ID
     * 
     * @param int $instructorId The instructor/user ID
     * @return array Array of courses created by the instructor
     */
    public function getCoursesByInstructor($instructorId)
    {
        return $this->builder()
            ->where('instructor_id', $instructorId)
            ->get()
            ->getResultArray();
    }

    /**
     * Get enrollment schedule for a user
     * 
     * @param int $userId The user ID
     * @return array Array of enrolled courses with schedule information
     */
    public function getEnrollmentSchedule($userId)
    {
        return $this->db->table('enrollments')
            ->join('courses', 'enrollments.course_id = courses.id')
            ->join('users', 'courses.instructor_id = users.id')
            ->where('enrollments.user_id', $userId)
            ->select('courses.*, enrollments.enrollment_date, users.name as instructor_name, users.email as instructor_email')
            ->orderBy('courses.schedule_days', 'ASC')
            ->orderBy('courses.schedule_time_start', 'ASC')
            ->get()
            ->getResultArray();
    }
}