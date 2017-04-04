<?php

// Check all php files for syntax errors

$test = Test::init('PHP file linting');

foreach(scandir_recursive(APPROOT) as $filename) {
    if(strpos($filename,'/.')!==false) continue;
    if(strpos($filename,APPROOT.DS.'vendor'.DS)!==false) continue;
    $extension = explode('.', $filename);
    $extension = array_pop($extension);
    if(!in_array($extension, array('inc','php'))) continue;
    $test->assertContains('No syntax errors', exec(sprintf('php -l "%s" 2>/dev/null', $filename), $out), sprintf("%s contains syntax errors", $filename));
}

// Check all json files for syntax errors

$test = Test::init('JSON file linting');

foreach(scandir_recursive(APPROOT) as $filename) {
    if(strpos($filename,'/.')!==false) continue;
    if(strpos($filename,APPROOT.DS.'vendor'.DS)!==false) continue;
    $extension = explode('.', $filename);
    $extension = array_pop($extension);
    if(!in_array($extension, array('json'))) continue;
    try {
        $object = json_decode(file_get_contents($filename));
        $test->assertNot(null, $object, sprintf("%s does not contain valid JSON", $filename));
    } catch(Exception $e) {
        $test->assert(true, false, sprintf("Parsing error during %s", $filename));
    }
}
