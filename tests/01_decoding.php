<?php

// New section
$test = Test::init('Data decoding');

// Hardcoded verification data
// We're testing if the decoders work properly

$verifyData = array(
    "hello" => array(
        "world",
        "pizza",
    ),
    "foo" => "bar",
);

$verifyTable = array(
    array('_id' => 'AsQlRmSlDw2etDy7', 'name' => 'Hello World'           ),
    array('_id' => 'rfbKkdKHvJjTtYzI', 'name' => 'FooBar'                ),
    array('_id' => 'uqMuS2hkk5fRsiYh', 'name' => 'Finwo'                 ),
    array('_id' => 'G9DgHZBPx0akqAcC', 'name' => 'Pizza Delivery Service'),
    array('_id' => 'EeKvOD0vfQx9fQHK', 'name' => 'Some other fancy name' ),
);

// Data formats
foreach (\Finwo\DataFile\DataFile::$supported as $format) {
    if($format=='csv') continue;
    foreach (glob(implode(DS,array(__DIR__,'data','01/data*.'.$format))) as $filename) {
        $data = \Finwo\DataFile\DataFile::read($filename);
        $test->assert(array_flatten($verifyData), array_flatten($data), 'Decoded data of "' . $filename . '" does not match the predefined values');
    }
}

// Table formats
if(in_array('csv', \Finwo\DataFile\DataFile::$supported)) {
    foreach (glob(implode(DS,array(__DIR__,'data','01/data*.csv'))) as $filename) {
        $data = \Finwo\DataFile\DataFile::read($filename);
        $test->assert(array_flatten($verifyTable), array_flatten($data), 'Decoded data of "' . $filename . '" does not match the predefined values');
    }
}
