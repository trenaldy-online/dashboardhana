<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    // Mengizinkan kolom 'key' dan 'value' untuk diisi ke database
    protected $fillable = [
        'key',
        'value'
    ];
}