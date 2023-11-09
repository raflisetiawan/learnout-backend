<?php

namespace App\Http\Controllers;

use App\Http\Resources\User as ResourcesUser;
use App\Models\Company;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('role')->get();
        return new ResourcesUser(true, 'List Data User with specific role', $users);
    }
    public function profile(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $userWithRole = $user->load('role');
            return new ResourcesUser(true, 'User Profile', $userWithRole);
        } else {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }
    }

    public function addRole(string $id, Request $request)
    {
        $user = User::find($id);
        $companyRoleId = $this->getCompanyRoleId();
        $user->update([
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
            'role_id' => $companyRoleId
        ]);
        return new ResourcesUser(true, 'List Data User with specific role', $user);
    }

    public function getCompanyRoleId()
    {
        $roles = Role::where('name', 'company')->get('id')->first();
        return $roles->id;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('role')->findOrFail($id);
        return new ResourcesUser(true, 'User', $user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $validator = Validator::make($request->all(), [
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::find($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role
        ]);
    }

    public function updateImageAndName(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $userImage = $request->file('image');
        $user = User::find($id);
        if (!$userImage) {
            $user->update([
                'name' => $request->name,
            ]);
            return response()->json(['message' => 'Berhasil Edit data User', 'image' => $user->image], 200);
        }

        $userImage->storeAs('public/users/images', $userImage->hashName());


        $user = User::find($id);
        $user->update([
            'name' => $request->name,
            'image' => $userImage->hashName()
        ]);
        return response()->json(['message' => 'Berhasil Edit data User', 'image' => $user->image], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getUserWithStudentWithUniversityByUserId(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $student = Student::where('user_id', $user->id)->with('university', 'categories', 'student_roles')->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $data = [
            'user' => $user,
            'student' => $student,
        ];

        return response()->json($data);
    }


    public function getUserAndStudentByUserId(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $student = Student::with('categories', 'student_roles')->where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $data = [
            'user' => $user,
            'student' => $student,
        ];

        return response()->json($data);
    }

    public function getUserAndCompanyByUserId(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $company = Company::where('user_id', $user->id)->first();

        if (!$company) {
            return response()->json(['message' => 'Company not found.'], 404);
        }

        $data = [
            'user' => $user,
            'company' => $company,
        ];

        return response()->json($data);
    }
}
