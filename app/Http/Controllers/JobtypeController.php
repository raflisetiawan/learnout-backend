<?php

namespace App\Http\Controllers;

use App\Models\Jobtype;
use Illuminate\Http\Request;

class JobtypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobtypes = Jobtype::all();
        return response()->json(['job_types' => $jobtypes]);
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
        $jobtype = Jobtype::findOrFail($id);
        return response()->json(['jobtype' => $jobtype]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jobtype $jobtype)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jobtype $jobtype)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jobtype $jobtype)
    {
        //
    }
}
