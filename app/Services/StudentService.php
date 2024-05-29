<?php
namespace App\Services;

use App\Models\Student;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class StudentService
{
    public function getAllStudents()
    {
        try {
            $students = Student::all();
            return ['success' => true, 'data' => $students];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function createStudent($data)
    {
        try {
            $student = Student::create($data);
            return ['success' => true, 'data' => $student];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getStudentById($id)
    {
        try {
            $student = Student::findOrFail($id);
            return ['success' => true, 'data' => $student];
        } catch (ModelNotFoundException $e) {
            return ['success' => false, 'error' => 'Student not found', 'code' => 404];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function updateStudent($id, $data)
    {
        try {
            $student = Student::findOrFail($id);
            $student->update($data);
            return ['success' => true, 'data' => $student];
        } catch (ModelNotFoundException $e) {
            return ['success' => false, 'error' => 'Student not found', 'code' => 404];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function deleteStudent($id)
    {
        try {
            $deleted = Student::destroy($id);
            if ($deleted) {
                return ['success' => true, 'message' => 'Student deleted successfully'];
            } else {
                return ['success' => false, 'error' => 'Student not found', 'code' => 404];
            }
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

