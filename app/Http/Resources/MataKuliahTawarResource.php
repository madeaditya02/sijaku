<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\DosenResource;
use App\Http\Resources\JadwalResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MataKuliahTawarResource extends JsonResource
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
            'id_matkul' => $this->id,
            'kode_matkul' => $this->mata_kuliah->kode,
            'nama_matkul' => $this->mata_kuliah->nama_matakuliah,
            'semester' => $this->mata_kuliah->semester,
            'semester_ajaran' => [
                'semester' => $this->semester,
                'tahun_ajaran_pertama' => $this->tahun_ajaran_pertama,
                'tahun_ajaran_kedua' => $this->tahun_ajaran_kedua,
            ],
            'sks' => [
                'jumlah_sks' => $this->mata_kuliah->sks,
                'sks_tatap_muka' => $this->mata_kuliah->sks_tatap_muka,
                'sks_praktikum' => $this->mata_kuliah->sks_praktikum,
            ],
            'jenis_matakuliah' => $this->mata_kuliah->jenis_matakuliah,
            'kelas' => $this->kelas,
            'dosen' => new DosenResource($this->whenLoaded('dosen')),
            'jadwal' => new JadwalResource($this->whenLoaded('jadwal')),
            'kuota' => $this->kuota,
            'jumlah_mahasiswa' => $this->whenCounted('krs'),
            'mahasiswa' => MahasiswaResource::collection($this->whenLoaded('krs'))
        ];
    }
}
