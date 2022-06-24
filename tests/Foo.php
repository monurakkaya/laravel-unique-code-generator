<?php

namespace Monurakkaya\Lucg\Tests;

use Illuminate\Database\Eloquent\Model;
use Monurakkaya\Lucg\Traits\HasUniqueCode;

class Foo extends Model
{
    use HasUniqueCode;

    protected $table = 'foos';

    protected $guarded = [];

    public $timestamps = false;
}
