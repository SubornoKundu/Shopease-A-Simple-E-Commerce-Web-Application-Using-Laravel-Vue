<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    /**
     * `line_total` needs the `product` relation loaded -- always eager
     * load it (`CartItem::with('product')`) before serializing a cart
     * item, otherwise this falls back to a lazy-loaded N+1 query per row.
     *
     * @var list<string>
     */
    protected $appends = ['line_total'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Line total for this cart row (unit final price x quantity).
     */
    public function getLineTotalAttribute(): float
    {
        return round($this->product->final_price * $this->quantity, 2);
    }
}
