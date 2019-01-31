# Runtime loading of Vue.js components from PHP

START FROM DEMO
-------------------

1) Launch composer update to adjust namespaces

```
$ composer update
```

2) Launch demo/demo.php from webserver (or from php cli to see the html output)

```
http://localhost/demo/demo.php
```

3) This should be shown on browser:

<img width="609" alt="schermata 2019-01-31 alle 15 58 52" src="https://user-images.githubusercontent.com/4108673/52063777-8be4b880-2573-11e9-9107-c136262a83ae.png">


HOW IT WORKS
------------

I wanted to load dinamically Vue.js components without passing through WebPack or similar.

So I thought to read Vue.js files (html, css and js) from filesystem and then print them to html returned to browser.

Basically, we need to create a class that extends `VueLoaderAbstract` class and implements the two mandatory methods: `componentName()`, `vueType()` and if components belongs to other component, it needed to fill `dependencies()`

The first thing is to create the App component and define its dependencies.


APP COMPONENT
-------------

1) Write the php class

```php
<?php

namespace fabriziocaldarelli\vuelive\demo\customApp;

use fabriziocaldarelli\vuelive\VueLoaderAbstract;

class VueLoader extends VueLoaderAbstract
{
    protected function componentName() : string
    {
        return "app";
    }

    protected function vueType() : string
    {
        return self::VUETYPE_APP;
    }

    protected function dependencies() : array
    {
        return [
            \fabriziocaldarelli\vuelive\demo\customComponent\VueLoader::class
        ];
    }
}

?>
```

2) Inside the same folder of class file, create 3 files: vue.html, vue.css and vue.js

**vue.html**

```php
<div id="___APPLICATION_ID___">
    This is a message from App : {{ message }}
</div>
```

**vue.js**

```js
/* var app = */
new Vue({
    el: '___APPLICATION_ID___',
    data: {
      message: 'Hello Vue!'
    }
  })
```

___APPLICATION_ID___ will be filled with componentName() returned value.

INDEX PHP FILE
-------------

```php
<?php 
require(__DIR__.'/../vendor/autoload.php');

// 1. Create app component Vue.js
$app = (new \fabriziocaldarelli\vuelive\demo\customApp\VueLoader());
?>

<html>

<head>

<!-- 2. Load Vue.js library -->
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

<!-- 3. Load all css from app component and its dependencies -->
<?= $app->getCssContentsAsString() ?>

</head>

<body>

    <!-- 4. Load all html from app component and its dependencies -->
    <?= $app->getHtmlContentsAsString() ?>

</body>

</html>

<!-- 5. Load all js from app component and its dependencies -->
<?= $app->getJsContentsAsString() ?>
```

The index file do 5 things:

1. Create `$app` object from VueLoaderAbstract subclass;
2. Load js Vue.js library;
3. Load all css from app component and its dependencies;
4. Load all html from app component and its dependencies;
5. Load all js from app component and its dependencies;
