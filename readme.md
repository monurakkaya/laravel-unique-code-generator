# Laravel unique code generator
Provides unique code for your Eloquent models


## INSTALLATION
```
composer require monurakkaya/laravel-unique-code-generator
```

## USAGE
### The schema

#### To create the column

```
// This will generate an unique string column named `code` 255 length which equivalent to $table->string('code', 255)->unique();
Schema::create('table', function (Blueprint $table) {
    ...
    $table->uniqueCode();
});
```

#### To drop the column
```
Schema::table('table', function (Blueprint $table) {
    $table->dropUniqueCode();
});
```

#### To use your own column just pass the parameters to override the default values
```
// on create
$table->uniqueCode('my_code', 'bigInteger', 32); // columnName, columnType, columnLength

// on drop
$table->dropUniqueCode('my_code');
```

Using schema helper is optional. You can go on with your own definitions.

### The model

Your model should use Monurakkaya\Lucg\HasUniqueCode trait to enable unique code functions:
```
use Monurakkaya\Lucg\HasUniqueCode;

class Foo extends Model {
    use HasUniqueCode;
}
```


and that's all.

```
$foo = Foo::create(['title' => 'Foo']);
#attributes: array:10 [▼
    "id" => 1
    "code" => "OF0EIL8B"
    "title" => "Foo"
    "created_at" => "2022-01-24 13:11:03"
    "updated_at" => "2022-01-24 13:11:03"
  ]
```

#### SETTINGS

To change the column name just override the `uniqueCodeColumnName` method on your model
```

class Foo extends Model {
    use HasUniqueCode;
    
    protected static function uniqueCodeColumnName()
    {
        return 'my_code';
    }
}

```

** Make sure your code column name to be equal to your column name. Default is `code`


To change the code type just override the `uniqueCodeType` method on your model
```

class Foo extends Model {
    use HasUniqueCode;
    
    protected static function uniqueCodeType()
    {
        return 'numeric';
    }
}

```

Available types are `'random_uppercase', 'random_lowercase', 'uuid', 'numeric'`. Default is `random_uppercase`

** If you are going to use uuid, I recommend you to set column definition as uuid


To change the code length just override the `uniqueCodeLength` method on your model
```

class Foo extends Model {
    use HasUniqueCode;
    
    protected static function uniqueCodeLength()
    {
        return '32';
    }
}

```

** Make sure your code length lte than column length


