ddv-file
===================

Installation - 安装
------------

```bash
composer require ddvphp/ddv-file
```

Usage - 使用
-----

### 1、我们以`laravel`为例子

```php

\DdvPhp\DdvFile\Handler::setHandler(function (array $r, $e) {
  var_dump($r);  
});


```

### 2、抛出异常

```php

throw new \DdvPhp\DdvFile\Error("测试一个异常", 'TEST_A_EXCEPTION');

```

### 3、抛出自定义继承异常

```php

class UserError extends \DdvPhp\DdvFile\Error
{
  // 魔术方法
  public function __construct( $message = 'Unknown Error' , $errorId = 'UNKNOWN_ERROR' , $code = '400', $errorData = array() )
  {
    parent::__construct( $message , $errorId , $code, $errorData );
  }
}

throw new UserError("测试一个异常", 'TEST_A_EXCEPTION');

```
