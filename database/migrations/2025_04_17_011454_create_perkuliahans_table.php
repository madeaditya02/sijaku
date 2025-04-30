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
        Schema::create('perkuliahan', function (Blueprint $table) {
            $table->id('id_kuliah');
            $table->unsignedBigInteger('id_jadwal');
            $table->datetime('waktu_mulai');
            $table->datetime('waktu_selesai');
            $table->enum('status', ['Pending', 'Hadir', 'Rescheduled', 'Batal'])->default('Pending');
            $table->boolean('kelas_offline')->nullable();
            $table->datetime('rescheduled_time_start')->nullable();
            $table->datetime('rescheduled_time_end')->nullable();
            $table->foreign('id_jadwal')->references('id_jadwal')->on('jadwal')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perkuliahan');
    }
};
