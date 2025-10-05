<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'original_file_path',
        'preview_file_path',
    ];

    /**
     * Get the purchases for the product.
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
