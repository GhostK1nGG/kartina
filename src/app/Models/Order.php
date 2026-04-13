<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const STATUSES = [
        'new' => 'Новый',
        'processed' => 'В работе',
        'completed' => 'Завершен',
    ];

    protected $fillable = [
        'session_id',
        'customer_name',
        'contact',
        'phone',
        'address',
        'comment',
        'cart_snapshot',
        'total_rub',
        'total_usd',
        'status',
    ];

    protected $casts = [
        'cart_snapshot' => 'array',
        'total_rub' => 'decimal:2',
        'total_usd' => 'decimal:2',
    ];
}
