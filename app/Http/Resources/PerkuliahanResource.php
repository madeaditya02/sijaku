<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerkuliahanResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $waktu_mulai = $this->rescheduled_time_start ?? $this->waktu_mulai;
        $waktu_selesai = $this->rescheduled_time_end ?? $this->waktu_selesai;
        return [
            'id_kuliah' => $this->id_kuliah,
            'waktu_mulai' => $waktu_mulai,
            'waktu_selesai' => $waktu_selesai,
            'waktu_mulai_string' => $waktu_mulai->isoFormat('D MMMM YYYY'),
            'waktu_selesai_string' => $waktu_selesai->isoFormat('D MMMM YYYY'),
            'hari_tanggal' => $waktu_mulai->isoFormat('dddd, D MMMM YYYY'),
            'jam' => $waktu_mulai->format('h:i') . " - " . $waktu_selesai->format('h:i'),
            'status' => $this->status == 'Hadir' || $this->status == 'Rescheduled' ? ($this->kelas_offline ? "Offline" : "Online") : $this->status,
            'mata_kuliah' => new MataKuliahTawarResource($this->jadwal->mataKuliahTawar),
            'ruangan' => $this->jadwal->ruangan->nama_ruangan,
        ];
    }
}
