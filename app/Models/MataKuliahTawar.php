<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliahTawar extends Model
{
    protected $table = 'mata_kuliah_tawar', $guarded = [];

    /**
     * Get the jadwal associated with the MataKuliahTawar
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function jadwal()
    {
        return $this->hasOne(Jadwal::class, 'id_matkul_tawar', 'id');
    }

    /**
     * The krs that belong to the MataKuliahTawar
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function krs()
    {
        return $this->belongsToMany(Mahasiswa::class, 'krs', 'id_matkul_tawar', 'nim');
    }

    /**
     * Get the mata_kuliah that owns the MataKuliahTawar
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mata_kuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'id_matkul', 'kode');
    }

    /**
     * Get the dosen that owns the MataKuliahTawar
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_ketua', 'nip');
    }
}
