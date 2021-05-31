<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;


    protected $fillable = ['occurs_at', 'type'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
