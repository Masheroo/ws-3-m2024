<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class File extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'filename',
        'user_id',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    public static function new(): static
    {
        $instance = new self();
        $instance->id = self::createUniqueId();

        return $instance;
    }

    public static function createUniqueId(): string
    {
        $id = \Illuminate\Support\Str::random(10);
        $validator = Validator::make(['id' => $id], ['id' => 'unique:files,id']);

        if ($validator->fails()){
            return self::createUniqueId();
        }

        return $id;
    }
}
