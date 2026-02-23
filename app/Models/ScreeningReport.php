<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScreeningReport extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id', 'user_data', 'status','ip_address', 'tokens_used', 'chat_history', 'report_data'];
    protected $casts = [
        'user_data' => 'array',
        'chat_history' => 'array',
        'report_data' => 'array',
    ];
}