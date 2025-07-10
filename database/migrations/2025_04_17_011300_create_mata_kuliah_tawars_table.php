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
        Schema::create('mata_kuliah_tawar', function (Blueprint $table) {
            $table->id();
            $table->string('id_matkul');
            $table->integer('tahun_ajaran_pertama');
            $table->integer('tahun_ajaran_kedua');
            $table->string('kelas');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->string('dosen_ketua');
            $table->integer('kuota');
            $table->timestamps();
            $table->foreign('id_matkul')->references('kode')->on('mata_kuliah')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('dosen_ketua')->references('nip')->on('dosen')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah_tawar');
    }
};
