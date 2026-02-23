<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Screening extends Model
{
    // Mengizinkan kolom-kolom ini diisi data dari API
    protected $fillable = [
        'name',
        'whatsapp',
        'email',
        'info_source',
        'marketing_opt_in',
        'cancer_type',
        'risk_level',
        'summary'
    ];
}