<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Psy\Util\Str;

class File extends Model
{
    use HasFactory;

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

        return new self();
    }

    private static function createUniqueId(): string
    {
        $id = \Illuminate\Support\Str::random(10);
        $validator = Validator::make(['id' => $id], ['id' => 'unique:files,id']);

        if ($validator->fails()){
            return self::createUniqueId();
        }

        return $id;
    }

    private function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
