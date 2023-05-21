<?php

namespace App\Http\Controllers;

use App\Http\Resources\UniversityResource;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UniversityController extends Controller
{
    public function index()
    {
        $universities = University::latest()->paginate(5);

        //return collection of universities as a resource
        return new UniversityResource(true, 'List Data Job', $universities);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'location' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $university = University::create([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        return new UniversityResource(true, 'Data Universitas berhasil ditambahkan', $university);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'location' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $university = University::find($id);

        $university->update([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        return new UniversityResource(true, 'Data Universitas berhasil di update', $university);
    }

    public function show(string $id)
    {
        $university = University::find($id);

        return new UniversityResource(true, 'Data Detail Universitas', $university);
    }

    public function destroy(string $id)
    {
        $university = University::find($id);

        $university->delete();
        return new UniversityResource(true, 'Data Universitas berhasil di hapus', $university);
    }
}
