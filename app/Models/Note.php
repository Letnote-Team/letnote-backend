<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, strinb>
     */
    protected $fillabe = [
        'title',
        'body',
        'parent_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'body' => 'array',
    ];
}
