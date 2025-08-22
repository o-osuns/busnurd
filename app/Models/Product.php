<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name','slug','price','description','image_path',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public $incrementing = false;

    protected $keyType = 'string';
}
