<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    public static function value(string $key, $default = null)
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function put(string $key, $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
