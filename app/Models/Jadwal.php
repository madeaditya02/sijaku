<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal', $primaryKey = 'id_jadwal', $guarded = ['id_jadwal'];

    /**
     * Get all of the perkuliahan for the Jadwal
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function perkuliahan()
    {
        return $this->hasMany(Perkuliahan::class, 'id_jadwal', 'id_jadwal');
    }

    /**
     * Get the ruangan that owns the Jadwal
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ruangan()
    {
        return $this->belongsTo(ruangan::class, 'id_ruangan', 'id_ruangan');
    }

    /**
     * Get the mataKuliahTawar that owns the Jadwal
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mataKuliahTawar()
    {
        return $this->belongsTo(MataKuliahTawar::class, 'id_matkul_tawar', 'id');
    }
}
