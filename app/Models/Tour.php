<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Tour extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'country',
        'description',
        'starting_date',
        'days_count',
        'peoples_count',
        'price',
        'img',
    ];
}
