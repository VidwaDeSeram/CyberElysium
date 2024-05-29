<?php
namespace App\Http\Controllers;

use App\Services\StudentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * @OA\Get(
     *     path="/api/students",
     *     summary="Get all students",
     *     tags={"Student CRUD"},
     *     @OA\Response(
     *         response=200,
     *         description="List of students"
     *     )
     * )
     */
    public function index()
    {
        $students = $this->studentService->getAllStudents();
        return Inertia::render('Students/Index', compact('students'));
    }

    /**
     * @OA\Get(
     *     path="/api/students/create",
     *     summary="Show create student form",
     *     tags={"Student CRUD"},
     *     @OA\Response(
     *         response=200,
     *         description="Create student form"
     *     )
     * )
     */
    public function create()
    {
        return Inertia::render('Students/Create');
    }

    /**
     * @OA\Post(
     *     path="/api/students",
     *     summary="Store a new student",
     *     tags={"Student CRUD"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="age", type="integer"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Student created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $this->studentService->createStudent($request->all());
        return redirect()->route('students.index');
    }

    /**
     * @OA\Get(
     *     path="/api/students/{id}/edit",
     *     summary="Show edit student form",
     *     tags={"Student CRUD"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Edit student form"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found"
     *     )
     * )
     */
    public function edit($id)
    {
        $student = $this->studentService->getStudentById($id);
        return Inertia::render('Students/Edit', compact('student'));
    }

    /**
     * @OA\Put(
     *     path="/api/students/{id}",
     *     summary="Update an existing student",
     *     tags={"Student CRUD"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="image", type="string"),
     *             @OA\Property(property="age", type="integer"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $this->studentService->updateStudent($id, $request->all());
        return redirect()->route('students.index');
    }

    /**
     * @OA\Delete(
     *     path="/api/students/{id}",
     *     summary="Delete a student",
     *     tags={"Student CRUD"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $this->studentService->deleteStudent($id);
        return redirect()->route('students.index');
    }
}
