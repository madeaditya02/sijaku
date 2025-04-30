<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $table = 'dosen', $primaryKey = 'nip', $keyType = 'string', $guarded = [];
    public $incrementing = false;
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the mataKuliah for the Dosen
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mataKuliah()
    {
        return $this->hasMany(MataKuliahTawar::class, 'dosen_ketua', 'nip');
    }
}
