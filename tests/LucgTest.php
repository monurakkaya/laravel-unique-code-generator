<?php

namespace Monurakkaya\Lucg\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Monurakkaya\Lucg\Exceptions\UniqueCodeNotSupportedException;
use Monurakkaya\Lucg\Traits\HasUniqueCode;

final class LucgTest extends TestCase
{
    /** @test */
    public function it_defines_code_on_create(): void
    {
        $foo = Foo::create();

        $this->assertNotNull($foo->code);
    }

    /** @test */
    public function it_checks_soft_deletes_trait(): void
    {
        DB::enableQueryLog();
        $model = new class () extends Foo {
            use SoftDeletes;
        };

        $model::creating(function ( Model $m ) use ($model) {
            $logs = DB::getQueryLog();
            $this->assertStringNotContainsString('deleted_at', $logs[0]['query']);
        });

        $model::create();

        DB::disableQueryLog();
    }

    /** @test */
    public function it_generates_code_with_given_length(): void
    {
        $class = new class extends Model {
            use HasUniqueCode;
            protected $table = 'foos';
            public $timestamps = false;

            protected static function uniqueCodeLength()
            {
                return 11;
            }

        };

        $foo = $class::create();

        $this->assertSame(11, strlen($foo->code));
    }

    /** @test */
    public function it_generates_code_with_given_type_numeric(): void
    {
        $class = new class extends Model {
            use HasUniqueCode;
            protected $table = 'foos';
            public $timestamps = false;

            protected static function uniqueCodeType()
            {
                return 'numeric';
            }

        };

        $foo = $class::create();

        $this->assertIsNumeric($foo->code);
    }

    /** @test */
    public function it_generates_code_with_given_type_uuid(): void
    {
        $class = new class extends Model {
            use HasUniqueCode;
            protected $table = 'foos';
            public $timestamps = false;

            protected static function uniqueCodeType()
            {
                return 'uuid';
            }

        };

        $foo = $class::create();

        $this->assertTrue((Str::isUuid($foo->code)));
    }

    /** @test */
    public function it_throws_error_with_undefined_type(): void
    {
        $class = new class extends Model {
            use HasUniqueCode;
            protected $table = 'foos';
            public $timestamps = false;

            protected static function uniqueCodeType()
            {
                return 'xxx';
            }

        };

        $this->assertThrows(fn () => $class::create(), UniqueCodeNotSupportedException::class);
    }
}
