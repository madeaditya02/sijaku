<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perkuliahan extends Model
{
    protected $table = 'perkuliahan', $primaryKey = 'id_kuliah', $guarded = ['id_kuliah'];

    /**
     * Get the jadwal that owns the Perkuliahan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'id_jadwal', 'id_jadwal');
    }

    public static function mingguIni($now = null) {
        $now = $now ?? now();
        return $this->where(function (Builder $query) use ($now) {
            $query->whereDate('waktu_mulai', '>=', $now->startOfWeek())->whereDate('waktu_mulai', '<=', $now->endOfWeek());
        })
        ->orWhere(function (Builder $query) use ($now) {
            $query->whereDate('rescheduled_time', '>=', $now->startOfWeek())->whereDate('rescheduled_time', '<=', $now->endOfWeek());
        });
    }

    protected function casts()
    {
        return [
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }
}
