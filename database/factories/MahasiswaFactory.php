<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mahasiswa>
 */
class MahasiswaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'angkatan' => 2023,
            // 'fakultas' => 'MIPA',
            // 'program_studi' => 'Informatika',
            'nomor_telpon' => '08973891362',
            'tempat_lahir' => 'Denpasar',
            'tanggal_lahir' => '2004-10-02',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Hindu',
        ];
    }
}
