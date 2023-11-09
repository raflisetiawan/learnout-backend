<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_application_requisites', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_cover_letter');
            $table->boolean('is_transcript');
            $table->boolean('is_recommendation_letter');
            $table->boolean('is_proposal');
            $table->boolean('is_resume');
            $table->unsignedBigInteger('joblisting_id')->nullable(); // Kolom role_id
            $table->foreign('joblisting_id')->references('id')->on('joblistings');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_application_requisites');
    }
};
