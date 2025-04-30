<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MataKuliah>
 */
class MataKuliahFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sks' => 3,
            'sks_tatap_muka' => 3,
            'sks_praktikum' => 0,
            'jenis_matakuliah' => 'Wajib',
        ];
    }
}
