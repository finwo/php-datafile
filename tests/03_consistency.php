<?php

// New section
$test = Test::init('Data consistency');

// No need for hardcoded verification data anymore
// We're not checking for decoding capabilities here

$verifyData  = \Finwo\DataFile\DataFile::read(implode(DS,array(__DIR__,'data','03','original.yml')));
$verifyTable = \Finwo\DataFile\DataFile::read(implode(DS,array(__DIR__,'data','03','original.csv')));

foreach (\Finwo\DataFile\DataFile::$supported as $format) {
    if ($format == 'csv') continue;
    $filename = implode(DS,array(__DIR__,'data','03','tmp.'.$format));

    // Write the data
    $test->assert(true, !!\Finwo\DataFile\DataFile::write($filename, $verifyData), sprintf("Error during writing of %s", $filename));

    // Read the data again ( decoders are tested separately )
    $readData = \Finwo\DataFile\DataFile::read($filename);

    // Test the output
    $test->assert($verifyData, $readData, sprintf("Data for %s not consistent", $filename));

    // Remove the file again, let's not pollute our harddrive
    unlink($filename);
}
