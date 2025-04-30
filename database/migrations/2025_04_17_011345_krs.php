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
        Schema::create('krs', function (Blueprint $table) {
            $table->id();
            $table->string('nim');
            $table->unsignedBigInteger('id_matkul_tawar');
            // $table->integer('semester');
            $table->foreign('nim')->references('nim')->on('mahasiswa')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_matkul_tawar')->references('id')->on('mata_kuliah_tawar')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('krs');
    }
};
