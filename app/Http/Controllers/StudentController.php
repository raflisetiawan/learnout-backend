<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\Application;
use App\Models\JobListing;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::latest()->paginate(5);

        //return collection of students as a resource
        return new StudentResource(true, 'List Data Student', $students);
    }

    public function getStudentIdByUserId(string $id)
    {
        $student = Student::where('user_id', $id)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }
        return response()->json(['student_id' => $student->id], 200);
    }

    public function getStudentByUserId(string $id)
    {
        $student = Student::where('user_id', $id)->get();
        if (count($student) === 0) {
            return response()->json(['isRegistered' => false, 'message' => 'Anda belum terdaftar menjadi mahasiswa'], 200);
        }
        return response()->json(['isRegistered' => true, 'message' => 'Anda sudah terdaftar menjadi mahasiswa', 'student' => $student], 200);
    }

    public function show(string $id)
    {
        $student = Student::find($id);
        return new StudentResource(true, 'Detail Data Student', $student);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'university_id' => 'required|exists:universities,id',
            'categories' => 'nullable|array',
            'regency' => 'required',
            'province' => 'required',
            'district' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = Student::where('user_id', $request->user_id)->first();
        if ($user) {
            return response()->json(['message' => 'anda sudah terdaftar menjadi mahasiswa'], 422);
        }

        $student = Student::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'university_id' => $request->university_id,
            'regency' => $request->regency,
            'district' => $request->district,
            'province' => $request->province
        ]);

        $categoryIds = $request->input('categories');
        $student->categories()->sync($categoryIds);

        return new StudentResource(true, 'Data Student Berhasil ditambahkan', $student);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'university_id' => 'required|exists:universities,id',
            'regency' => 'required',
            'province' => 'required',
            'district' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $student = Student::find($id);
        $student->update([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'university_id' => $request->university_id,
            'regency' => $request->regency,
            'district' => $request->district,
            'province' => $request->province,
            'resume' => $request->resume
        ]);

        $categoryIds = $request->input('categories');
        $student->categories()->sync($categoryIds);

        return new StudentResource(true, 'Data Student Berhasil di update', $student);
    }

    public function destroy(string $id)
    {
        $student = Student::find($id);
        if ($student) {
            $student->delete();
            return new StudentResource(true, 'Data Student Berhasil di hapus', $student);
        }
        return new StudentResource(false, 'Data Student tidak ditemukan', $student);
    }

    public function jobAround(string $id)
    {
        $student = Student::where('user_id', $id)->first();
        if (!$student) {
            return response()->json(['isRegistered' => false, 'message' => 'Anda belum terdaftar menjadi mahasiswa'], 200);
        }

        $regency = $student->regency;
        $jobs = JobListing::with('company', 'categories')->where('regency', $regency)->get();

        if ($jobs->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Pekerjaan tidak ditemukan'], 200);
        }

        return response()->json(['jobs' => $jobs], 200);
    }

    public function getApplicationHistoryByUserId(string $id)
    {
        $student = Student::where('user_id', $id)->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $applications = Application::where('student_id', $student->id)->get();

        return response()->json($applications);
    }


    public function getStudentsByJobId(string $studentId)
    {
        $students = Student::whereHas('joblistings', function ($query) use ($studentId) {
            $query->where('joblistings.id', $studentId);
        })->get();

        return response()->json(['data' => $students], 200);
    }

    public function updateResumeStudent(string $studentId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'resume' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $curriculumVitae = $request->file('curriculum_vitae');
        $curriculumVitae->storeAs('public/students/curriculum_vitae', $curriculumVitae->hashName());

        $student = Student::findOrFail($studentId);
        $student->update(['resume' => $request->resume, 'curriculum_vitae' => $curriculumVitae->hashName()]);
        return response()->json(['data' => $student], 200);
    }

    public function getOneStudentByUserId(string $id)
    {
        $student = Student::where('user_id', $id)->first();
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }
        return response()->json(['student' => $student], 200);
    }
    public function getOneStudentByStudentId(string $id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }
        return response()->json(['student' => $student], 200);
    }

    public function getStudentWithResume(string $id)
    {
        $student  = Student::findOrFail($id);
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }
        return response()->json(['success' => true, 'student' => $student], 200);
    }
}
