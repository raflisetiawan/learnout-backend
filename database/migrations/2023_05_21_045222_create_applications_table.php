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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('joblisting_id');
            $table->text('cover_letter');
            $table->text('transcripts')->nullable();
            $table->text('recommendation_letter')->nullable();
            $table->text('proposal')->nullable();
            $table->text('resume')->nullable();
            $table->string('status')->default('pending');
            $table->boolean('is_canceled')->default(false);

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('joblisting_id')->references('id')->on('joblistings')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
