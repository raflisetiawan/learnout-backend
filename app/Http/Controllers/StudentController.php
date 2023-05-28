<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
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
            'categories' => 'nullable|array'
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
        ]);

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
}
