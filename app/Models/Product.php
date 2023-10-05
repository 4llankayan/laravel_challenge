<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\ShoppingList;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'quantity',
        'description',
    ];

    public function shopping_lists(): BelongsToMany
    {
        return $this->belongsToMany(ShoppingList::class, 'product_shopping_list', 'product_id', 'shopping_list_id');
    }
}
