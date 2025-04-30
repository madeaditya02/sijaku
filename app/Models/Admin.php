<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin', $primaryKey = 'nip', $keyType = 'string', $guarded = [];
    public $incrementing = false;
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
