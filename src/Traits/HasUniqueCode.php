<?php

namespace Monurakkaya\Lucg\Traits;

use Illuminate\Support\Str;
use Monurakkaya\Lucg\Exceptions\UniqueCodeNotSupportedException;

trait HasUniqueCode
{
    protected static function supportedUniqueCodeTypes()
    {
        return [
            'string_uppercase',
            'string_lowercase',
            'uuid',
            'numeric',
            'random'
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
                $model->{self::uniqueCodeColumnName()} = self::generateCode();
            }
        });
    }

    /**
     * @throws UniqueCodeNotSupportedException
     */
    public static function generateCode()
    {
        self::checkUniqueCodeIsSupported();
        $code = self::{'generate'. Str::studly(self::uniqueCodeType()).'UniqueCode'}();
        if (self::query()->where(self::uniqueCodeColumnName(), $code)->exists()) {
            return self::generateCode();
        }
        return $code;
    }

    protected static function generateStringUppercaseUniqueCode()
    {
        return Str::upper(
            Str::random(
                self::uniqueCodeLength()
            )
        );
    }

    protected static function generateStringLowercaseUniqueCode()
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
        return Str::uuid();
    }

    protected static function uniqueCodeLength()
    {
        return 8;
    }

    protected static function uniqueCodeType()
    {
        return 'string_uppercase';
    }

    protected static function uniqueCodeColumnName()
    {
        return 'code';
    }
}