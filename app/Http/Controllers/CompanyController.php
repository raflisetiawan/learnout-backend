<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $posts = Company::latest()->paginate(5);

        //return collection of posts as a resource
        return new CompanyResource(true, 'List Data Posts', $posts);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'website' => 'nullable|regex:/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,}$/',
            'email' => 'required|email',
            'phone' => 'required|string',
            'user_id' => 'required',
            'regency' => 'required',
            'district' => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $user = Company::where('user_id', $request->user_id)->first();
        if ($user) {
            return response()->json(['message' => 'anda sudah terdaftar menjadi mahasiswa'], 422);
        }

        $company = Company::create([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'website' => $request->website,
            'email' => $request->email,
            'phone' => $request->phone,
            'user_id' => $request->user_id,
            'regency' => $request->regency,
            'district' => $request->district,
        ]);

        return new CompanyResource(true, 'Data Company Berhasil Ditambahkan!', $company);
    }

    public function getCompanyByUserId(string $id)
    {
        $company = Company::where('user_id', $id)->get();
        if (count($company) === 0) {
            return response()->json(['isRegistered' => false, 'message' => 'Anda belum terdaftar menjadi owner perusahaan'], 200);
        }
        return response()->json(['isRegistered' => true, 'message' => 'Anda sudah terdaftar menjadi owner perusahaan', 'student' => $company], 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $company = Company::find($id);

        //return single company as a resource
        return new CompanyResource(true, 'Detail Data company!', $company);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'website' => 'nullable|url',
            'email' => 'required|email',
            'phone' => 'required|string',
            'regency' => 'required',
            'district' => 'required'
        ]);


        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $company = Company::find($id);


        $user = Company::where('user_id', $request->user_id)->first();
        if ($user) {
            return response()->json(['message' => 'anda sudah terdaftar menjadi Company'], 422);
        }

        $company->update([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'website' => $request->website,
            'email' => $request->email,
            'phone' => $request->phone,
            'regency' => $request->regency,
            'district' => $request->district,
        ]);

        return new CompanyResource(true, 'Data company berhasil di update', $company);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $company = Company::find($id);
        $company->delete();
        return new CompanyResource(true, 'Data company berhasil di hapus', $company);
    }
}
