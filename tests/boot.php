<?php
if ( ! defined( 'BRAINSUPPORTPATH' ) ) define( 'BRAINSUPPORTPATH', dirname( dirname( __FILE__ ) ) );

require_once BRAINSUPPORTPATH . '/vendor/autoload.php';

if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', TRUE );
}
require_once BRAINSUPPORTPATH . '/vendor/phpunit/phpunit/PHPUnit/Framework/Assert/Functions.php';
