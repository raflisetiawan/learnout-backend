<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobApplicationRequisite;
use Illuminate\Http\Request;

class JobApplicationRequisiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobApplicationRequisite = JobApplicationRequisite::all();
        return response()->json(['data' => $jobApplicationRequisite]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
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
        $jobApplicationRequisite = JobApplicationRequisite::findOrFail($id);
        return response()->json(['data' => $jobApplicationRequisite]);
    }

    public function getByJoblistingId(string $id)
    {
        // $job = Application::findOrFail($id);
        $jobApplicationRequisite = JobApplicationRequisite::where('joblisting_id', $id)->first();

        // Melakukan konversi nilai-nilai numerik ke boolean
        $jobApplicationRequisite->is_cover_letter = (bool) $jobApplicationRequisite->is_cover_letter;
        $jobApplicationRequisite->is_transcript = (bool) $jobApplicationRequisite->is_transcript;
        $jobApplicationRequisite->is_recommendation_letter = (bool) $jobApplicationRequisite->is_recommendation_letter;
        $jobApplicationRequisite->is_proposal = (bool) $jobApplicationRequisite->is_proposal;
        $jobApplicationRequisite->is_resume = (bool) $jobApplicationRequisite->is_resume;
        $jobApplicationRequisite->is_health_insurance = (bool) $jobApplicationRequisite->is_health_insurance;

        return response()->json(['jobApplicationRequisite' => $jobApplicationRequisite]);
    }
    public function getByJoblistingIdFromUpdateApplication(string $id)
    {
        $job = Application::findOrFail($id);
        $jobApplicationRequisite = JobApplicationRequisite::where('joblisting_id', $job->joblisting_id)->first();

        // Melakukan konversi nilai-nilai numerik ke boolean
        $jobApplicationRequisite->is_cover_letter = (bool) $jobApplicationRequisite->is_cover_letter;
        $jobApplicationRequisite->is_transcript = (bool) $jobApplicationRequisite->is_transcript;
        $jobApplicationRequisite->is_recommendation_letter = (bool) $jobApplicationRequisite->is_recommendation_letter;
        $jobApplicationRequisite->is_proposal = (bool) $jobApplicationRequisite->is_proposal;
        $jobApplicationRequisite->is_resume = (bool) $jobApplicationRequisite->is_resume;
        $jobApplicationRequisite->is_health_insurance = (bool) $jobApplicationRequisite->is_health_insurance;

        return response()->json(['jobApplicationRequisite' => $jobApplicationRequisite]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobApplicationRequisite $jobApplicationRequisite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobApplicationRequisite $jobApplicationRequisite)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobApplicationRequisite $jobApplicationRequisite)
    {
        //
    }
}
