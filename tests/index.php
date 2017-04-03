<?php

error_reporting(0);
ini_set('display_errors', 0);
if (!defined('DS'))      define('DS'     , DIRECTORY_SEPARATOR);
if (!defined('APPROOT')) define('APPROOT', rtrim(dirname(__DIR__), '/'));
require APPROOT . DS . 'vendor' . DS . 'autoload.php';

$firstTest = true;
$tests     = 0;
$fails     = array();

class Test
{
    /**
     * @var int
     */
    protected $depth = 0;

    protected $template = array(
        true  => ".",
        false => "F",
    );

    /**
     * @param string $group
     *
     * @return Test
     */
    public static function init( $group )
    {
        global $firstTest;

        if ($firstTest) {
            $firstTest = false;
        } else {
            echo PHP_EOL, PHP_EOL;
        }

        echo $group, ':';
        flush();
        return new Test();
    }

    /**
     * @param mixed  $a
     * @param mixed  $b
     * @param string $errorMessage
     *
     * @return Test
     */
    public function assert( $a, $b, $errorMessage = null )
    {
        global $tests, $fails;

        if ( $this->depth === 0 ) {
            echo PHP_EOL, '  ';
            flush();
        }

        $tests++;
        $result = ( $a === $b );
        printf("%s", $this->template[$result]);
        flush();
        if( !$result ) {
            array_push($fails, array('assert', $a, $b, $errorMessage));
        }

        $this->depth = ($this->depth+1)%20;

        return $this;
    }

    /**
     * @param mixed  $a
     * @param mixed  $b
     * @param string $errorMessage
     *
     * @return Test
     */
    public function assertNot( $a, $b, $errorMessage = null )
    {
        global $tests, $fails;

        if ( $this->depth === 0 ) {
            echo PHP_EOL, '  ';
            flush();
        }

        $tests++;
        $result = ( $a !== $b );
        printf("%s", $this->template[$result]);
        flush();
        if( !$result ) {
            array_push($fails, array('assertNot', $a, $b, $errorMessage));
        }

        $this->depth = ($this->depth+1)%20;

        return $this;
    }

    /**
     * @param mixed  $a
     * @param mixed  $b
     * @param string $errorMessage
     *
     * @return Test
     */
    public function assertContains( $a, $b, $errorMessage = null )
    {
        global $tests, $fails;

        if ( $this->depth === 0 ) {
            echo PHP_EOL, ' ';
            flush();
        }

        $tests++;
        $result = strpos($b, $a) !== false;
        echo $this->template[$result];
        flush();
        if( !$result ) {
            array_push($fails, array('assertContains', $a, $b, $errorMessage));
        }

        $this->depth = ($this->depth+1)%20;

        return $this;
    }
}

// See: http://stackoverflow.com/a/24784144/2928176
function scandir_recursive( $dir, &$results = array() ) {
    $files = scandir($dir);
    foreach($files as $key => $value){
        $path = realpath($dir.DS.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            scandir_recursive($path, $results);
        }
    }
    return $results;
}

/**
 * @param array  $input
 * @param string $parentKey
 *
 * @return array
 */
function array_flatten( $input = array(), $parentKey = '' ) {
    $output = array();
    foreach ($input as $key => $value) {
        $compositeKey = strlen($parentKey) ? $parentKey.'.'.$key : $key;
        switch(gettype($value)) {
            case 'array':
                $output = array_merge($output, array_flatten($value, $compositeKey));
                break;
            case 'object':
                // Skip
                break;
            default:
                $output[$compositeKey] = $value;
                break;
        }
    }
    return $output;
}

// Startup message
$composerData = json_decode(file_get_contents(APPROOT.DS.'composer.json'));
echo PHP_EOL;
echo 'Testing environment for ', $composerData->name, PHP_EOL;
echo '------------------------', str_repeat('-', strlen($composerData->name)), PHP_EOL, PHP_EOL;

// Shutdown message
register_shutdown_function(function() {
    global $tests, $fails;
    printf(PHP_EOL . PHP_EOL . 'Ran %d tests of which %d failed' . PHP_EOL . PHP_EOL, $tests, count($fails));

    foreach ($fails as $index => $fail) {
        printf('Error #%d: %s' . PHP_EOL, $index+1, array_pop($fail));
        switch(array_shift($fail)) {
            case 'assert':
                $a = array_pop($fail);
                $b = array_pop($fail);
                if(is_array($a)) $a = json_encode($a);
                if(is_array($b)) $b = json_encode($b);
                printf('  "%s" doesn\'t match "%s"' . PHP_EOL, $a, $b );
                break;
            case 'assertNot':
                printf('  "%s" matches "%s"' . PHP_EOL, array_pop($fail), array_pop($fail));
                break;
            case 'assertContains':
                printf('  "%s" doesn\'t contain "%s"'.PHP_EOL, array_pop($fail), array_pop($fail));
                break;
            default:
                break;
        }
        print("\n");
    }

    exit(count($fails));
});

// Require all php files in the current folder
$_test_files = glob(__DIR__.DS.'*.php');
sort($_test_files);
foreach ($_test_files as $filename) {
    if ($filename == __FILE__) continue;
    require($filename);
}
