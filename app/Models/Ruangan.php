<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table = 'ruangan', $primaryKey = 'id_ruangan', $guarded = ['id_ruangan'];

    /**
     * Get all of the jadwal for the Ruangan
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'id_ruangan', 'id_ruangan');
    }
}
