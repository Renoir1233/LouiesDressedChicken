<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'position',
        'address',
        'contact_no',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];
}