<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MataKuliahResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'kode_matkul' => $this->kode,
            'nama_matkul' => $this->nama_matakuliah,
            'semester' => $this->semester,
            'sks' => [
                'jumlah_sks' => $this->sks,
                'sks_tatap_muka' => $this->sks_tatap_muka,
                'sks_praktikum' => $this->sks_praktikum,
            ],
            'jenis_matakuliah' => $this->jenis_matakuliah,
        ];
    }
}
