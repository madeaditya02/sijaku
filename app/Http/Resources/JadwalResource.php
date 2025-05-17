<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JadwalResource extends JsonResource
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
            'id_jadwal' => $this->id_jadwal,
            'hari' => $this->hari,
            'jam_mulai' => $this->jam_mulai,
            'jam_selesai' => $this->jam_selesai,
            'ruangan' => [
                'id_ruangan' => $this->ruangan->id_ruangan,
                'nama_ruangan' => $this->ruangan->nama_ruangan,
                'kapasitas' => $this->ruangan->kapasitas,
            ],
        ];
    }
}
