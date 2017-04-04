<?php

// Same as 01, but more advanced data
// Not testing table data; we tested that already

$verifyData = array(
    'site'           => array(
        'title'            => 'Example Config',
        'domain'           => 'example.com',
        'languages'        => array(
            'default'   => 'nl',
            'available' => '',
        ),
        'logo'             => '/assets/images/logo.png',
        'window_target'    => '_blank',
        'dynamic_title'    => false,
        'dynamic_meta'     => false,
        'last_login'       => array(
            'min_rating' => 7,
        ),
        'meta'             => array(
            'description' => '',
            'keywords'    => '',
            'viewport'    => 'width=device-width, initial-scale=1',
        ),
        'analytics'        => array(
            'use'  => true,
            'code' => 'UA-0000000-0',
        ),
        'default'          => array(
            'filters' => array(
                0 => array(
                    0 => 'foo',
                    1 => 'bar',
                ),
                1 => array(
                    0 => 'hello',
                    1 => 'world',
                ),
            ),
            'orderby' => array(
                0 => 'date:desc',
                1 => 'name:desc',
                2 => 'rating:desc',
            ),
        ),
        'template-options' => array(
            'test-thumb'    => 'test/thumb-simple',
            'test-filter'   => 'test/filter',
            'test-overview' => 'test/overview_filters',
        ),
    ),
    'development'    => array(
        'disable_cache' => 0,
        'compress_less' => 0,
        'less_ttl'      => 0,
    ),
    'snapshots'      => array(
        'server'      => 'http://images.example.com',
        'thumb'       => '250x150',
        'default'     => 'snapshot',
        'resolutions' => array(
            'snapshot' => '/snapshot/:pageid/snapshot.jpg',
            '99x84'    => '/snapshot/:pageid/99x84.jpg',
            '101x85'   => '/snapshot/:pageid/101x85.jpg',
            '200x150'  => '/snapshot/:pageid/200x150.jpg',
            '125x94'   => '/snapshot/:pageid/125x94.jpg',
        ),
    ),
    'filters'        => array(
        'date'     => array(
            0 => '>2014-01-01',
            1 => '<2017-01-01',
        ),
        'rating'   => array(
            0 => '1-10',
        ),
        'language' => array(
            0 => 'nl',
            1 => 'en',
            2 => 'de',
            3 => 'fr',
            4 => 'es',
            5 => 'it',
        ),
    ),
    'filter_presets' => array(
        'dutch' => array(
            'filter' => array(
                0 => 'language:nl',
            ),
        ),
        '00_99' => array(
            'orderby' => array(
                0 => 'age:asc',
            ),
        ),
        '99_00' => array(
            'orderby' => array(
                0 => 'age:desc',
            ),
        ),
    ),
    'router'         => array(
        'home'     => array(
            'default'  => true,
            'template' => 'index.html',
        ),
        'account'  => array(
            'template'               => 'my-account.html',
            'requiresAuthentication' => true,
        ),
        'messages' => array(
            'template'               => 'inbox.html',
            'requiresAuthentication' => true,
        ),
        'error'    => array(
            'parameters' => array(
                'errorCode' => 404,
            ),
            'template'   => 'error.html',
        ),
    ),
    'error'          => array(
        400 => array(
            'errorCode' => 400,
            'message'   => 'Route Parsing Error',
        ),
        404 => array(
            'errorCode' => 404,
            'message'   => 'Page Not Found',
        ),
        500 => array(
            'errorCode' => 500,
            'message'   => 'Internal Server Error',
        ),
    ),
    'urls'           => array(
        'nl' => array(
            'account'  => 'mijn-account',
            'messages' => 'mijn-berichten',
            'error'    => 'error/:errorCode',
        ),
        'en' => array(
            'account'  => 'my-account',
            'messages' => 'my-messages',
            'error'    => 'error/:errorCode',
        ),
    ),
    'api'            => array(
        'baseUri' => 'http://www.example.com/api/',
        'ttl'     => 30,
    ),
    'media'          => array(
        'baseUri' => 'http://media.example.com/',
    ),
    'messages'       => array(
        'threads'           => 30,
        'threadsViewed'     => 5,
        'paginationButtons' => 5,
        'minLength'         => 1,
        'maxLength'         => 1000,
    ),
    'pagination'     => array(
        'tab_size' => 6,
    ),
    'styles'         => array(
        0 => 'extra',
        1 => 'flexgrid',
        2 => 'global',
        3 => 'textsizes',
        4 => 'notify',
    ),
);

// Data formats
foreach (\Finwo\DataFile\DataFile::$supported as $format) {
    if ($format == 'csv') continue;
    foreach (glob(implode(DS, array( __DIR__, 'data', '02', 'data*.' . $format ))) as $filename) {
        $data = \Finwo\DataFile\DataFile::read($filename);
        $test->assert(array_flatten($verifyData), array_flatten($data), 'Decoded data of "' . $filename . '" does not match the predefined values');
    }
}
