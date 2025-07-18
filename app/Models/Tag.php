<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_tags');
    }
}
