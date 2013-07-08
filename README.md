Laravel-4-Facebook-Comment-Like
===============================

Laravel 4 Package to embed facebook comment and like plugin

----------

##INSTALL







add this line to your app.php provider array:

    'Totox777\Fbplugins\FbpluginsServiceProvider',
    
and add this line to app.php aliases array:

    'Fbplugins' => 'Totox777\Fbplugins\Facades\Profiler',


config: don't forget to input your facebook app_id and admins in src/config/config.php

##Usage
Always use this once in your script:
    Fbplugins::getScript();

To embed comment:
    Fbplugins::getPlugin('comments');

To embed like:
    Fbplugins::getPlugin('like');

To embed activity:
    Fbplugins::getPlugin('activity');

