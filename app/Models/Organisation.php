<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    protected $table = 'organisation';
    
    protected $fillable = [
        'name',
    ];
}
