<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;
    protected $fillable = [
        'child_firstname',
        'child_lastname',
        'child_patronymic',
        'child_gender',
        'child_birthdate',
        'child_branch_id',
    ];
}
