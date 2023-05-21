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
            'email' => 'required|email|unique:students',
            'phone' => 'required|string',
            'university_id' => 'required|exists:universities,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $student = Student::create([
            'name' => $request->name,
            'address' => $request->address,
            'email' => $request->email,
            'phone' => $request->phone,
            'university_id' => $request->university_id,
        ]);

        return new StudentResource(true, 'Data Student Berhasil ditambahkan', $student);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|email|unique:students',
            'phone' => 'required|string',
            'university_id' => 'required|exists:universities,id',
        ]);

        $student = Student::find($id);
        $student->update([
            'name' => $request->name,
            'address' => $request->address,
            'email' => $request->email,
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
