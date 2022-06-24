<?php

namespace Monurakkaya\Lucg\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function setUpDatabase(): void
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('foos', function (Blueprint $table) {
            $table->id();
            $table->uniqueCode();
            $table->softDeletes();
        });

        for ($i = 0; $i < 10; $i++) {
            Foo::create();
        }
    }

    protected function getPackageProviders($app): array
    {
        return [
            'Monurakkaya\Lucg\Providers\LucgServiceProvider',
        ];
    }
}
