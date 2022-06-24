<?php

namespace Monurakkaya\Lucg\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Monurakkaya\Lucg\Exceptions\UniqueCodeNotSupportedException;

trait HasUniqueCode
{
    protected static function supportedUniqueCodeTypes()
    {
        return [
            'random_uppercase',
            'random_lowercase',
            'uuid',
            'numeric'
        ];
    }

    /**
     * @throws UniqueCodeNotSupportedException
     */
    private static function checkUniqueCodeIsSupported()
    {
        if (!in_array(self::uniqueCodeType(), self::supportedUniqueCodeTypes())) {
            throw new UniqueCodeNotSupportedException(
                self::uniqueCodeType(). ' is not supported. Code types must be one of followings '. implode(', ', self::supportedUniqueCodeTypes())
            );
        }
    }

    protected static function bootHasUniqueCode()
    {
        static::creating(function ($model) {
            if (!$model->isDirty(self::uniqueCodeColumnName())) {
                $model->{self::uniqueCodeColumnName()} = self::generateCode($model);
            }
        });
    }

    /**
     * @throws UniqueCodeNotSupportedException
     */
    public static function generateCode(Model $model)
    {
        self::checkUniqueCodeIsSupported();
        $code = self::{'generate'. Str::studly(self::uniqueCodeType()).'UniqueCode'}();

        $query = self::query();

        if (in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $query->withTrashed();
        }

        if ($query->where(self::uniqueCodeColumnName(), $code)->exists()) {
            return self::generateCode($model);
        }
        return $code;
    }

    protected static function generateRandomUppercaseUniqueCode()
    {
        return Str::upper(
            Str::random(
                self::uniqueCodeLength()
            )
        );
    }

    protected static function generateRandomLowercaseUniqueCode()
    {
        return Str::lower(
            Str::random(
                self::uniqueCodeLength()
            )
        );
    }

    protected static function generateNumericUniqueCode()
    {
        return random_int(
            pow(10, self::uniqueCodeLength() - 1),
            (pow(10, self::uniqueCodeLength()) - 1)
        );
    }

    protected static function generateUuidUniqueCode()
    {
        return Str::uuid()->toString();
    }

    protected static function uniqueCodeLength()
    {
        return 8;
    }

    protected static function uniqueCodeType()
    {
        return 'random_uppercase';
    }

    protected static function uniqueCodeColumnName()
    {
        return 'code';
    }
}
