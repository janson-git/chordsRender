<?php
define('APP_DIR', __DIR__ . '/..');

require_once __DIR__ . '/ChordsTestSuite.php';

$testSuite = new ChordsTestSuite();
$methods = get_class_methods('ChordsTestSuite');

$tests = [];
foreach ($methods as $method) {
    if (strpos($method, 'test') === 0) {
        array_push($tests, $method);
    }
}

ini_set('display_errors', 0);

foreach ($tests as $test) {
    $testSuite->setUp();
    $testSuite->$test();

    $err = error_get_last();
    if ($err === null) {
        echo '.';
    }

    $testSuite->tearDown();
}
