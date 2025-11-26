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
    ];

    protected $useTimestamps = false;

    public function getUserEnrollments($userId)
    {
        return $this->db->table('enrollments')
            ->join('courses', 'enrollments.course_id = courses.id')
            ->where('enrollments.user_id', $userId)
            ->select('courses.id, courses.title, courses.description, enrollments.enrollment_date')
            ->get()
            ->getResultArray();
    }

    public function getAvailableCourses($userId)
    {
        $enrolledCourseIds = $this->db->table('enrollments')
            ->where('user_id', $userId)
            ->select('course_id')
            ->get()
            ->getResultArray();

        $enrolledIds = array_column($enrolledCourseIds, 'course_id');

        if (empty($enrolledIds)) {
            return $this->findAll();
        }

        return $this->whereNotIn('id', $enrolledIds)->findAll();
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
}