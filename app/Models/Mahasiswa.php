<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa', $primaryKey = 'nim', $keyType = 'string', $guarded = [];
    public $incrementing = false;
    /**
     * Get the user associated with the Mahasiswa
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The krs that belong to the Mahasiswa
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function krs()
    {
        return $this->belongsToMany(MataKuliahTawar::class, 'krs', 'nim', 'id_matkul_tawar');
    }
}
