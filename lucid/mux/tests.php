<?php

require __DIR__.'/vendor/autoload.php';


$files = `find -H ./src ./tests -name '*.php'`;
//
//
$pf = null;
foreach (explode(PHP_EOL, $files) as $file) {

    if (!file_exists($file)) {
        var_dump($file);
    }

    $err = null;

    set_error_handler(function ($errno, $errstr) {
        throw new \ErrorException($errstr, $errno);
    });

    var_dump(class_exists('Lucid\Mux\Parser\ParserInterface'));
    var_dump($pf);
    $pf = $file;

    try {
        include_once($file);
        if (class_exists('Lucid\Mux\Parser\ParserInterface')) {
            var_dump($file);
        }
    } catch (\Throwable $e) {
        $err = $e;
        var_dump($file);
    }

    restore_error_handler();

    if (null !== $err) {
        throw $err;
    }

}

