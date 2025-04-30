<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah', $primaryKey = 'kode', $keyType = 'string', $guarded = [];
    public $incrementing = false;
    /**
     * Get all of the mataKuliahTawar for the MataKuliah
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mataKuliahTawar()
    {
        return $this->hasMany(MataKuliahTawar::class, 'id_matkul', 'kode');
    }
}
