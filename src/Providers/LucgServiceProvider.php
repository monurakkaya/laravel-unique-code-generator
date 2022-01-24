<?php

namespace Monurakkaya\Lucg\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;

class LucgServiceProvider extends ServiceProvider
{

    public function register()
    {
        Blueprint::macro('uniqueCode', function ($columnName = 'code', $columnType = 'string', $length = '255') {
            return $this->addColumn($columnType, $columnName, compact('length'))->unique();
        });

        Blueprint::macro('dropUniqueCode', function ($columnName = 'code') {
            return $this->dropColumn($columnName);
        });
    }
}