Xeno Session
============

A simple PHP session handler library with some extra features.

## Installation

```
composer require xenokore/session
```

## Usage

Load the `$_SESSION` variable in the constructor:

```php
$session = new Xenokore\Session\Session($_SESSION);
```

You can access it as an array as if you're working directly with *$_SESSION*:

```php
$session['test'] = 'hello';

var_dump($session['test']); // string(4) "test"

unset($session['test']);
```

You can also use the `get()` and `set()` methods:

```php
// "name" will not be found so the default fallback will be used
var_dump( $session->get('name', 'unknown') ); // string(7) "unknown"

$session->set('name', 'yani'),

echo $session->get('name', 'unknown'); // string(4) "yani"
```

Another feature of this library is to remember a variable **once** using `once()` and `getOnce()`. This behaves like *FlashMessages* and can be used to pass one-time data between requests:


```php
$session->once('error', 'failed to login');

// On a different request:
$error = $session->getOnce('error', null);

if($error){
    var_dump($error); // string(15) "failed to login"
}

$error = $session->getOnce('error', null);

var_dump($error); // null
```

`getOnce()` instantly removes the one-time variable from the session. You can also use `getAllOnce()` to get all one-time variables.
