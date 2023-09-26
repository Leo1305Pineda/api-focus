<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubStateTypeUserApp extends Model
{
    protected $table = "sub_state_type_user_app";
    protected $fillable = [
        'sub_state_id',
        'type_user_app_id'
    ];
}
