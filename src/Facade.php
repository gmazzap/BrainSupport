<?php namespace Brain;

use \Brain\Container as Brain;

/**
 * Facade Class
 *
 * This class is a sort of *proxy* to ease the modules API calls.
 * Concrete implementation are used to call methods defined in a Brain module
 * using static methods, like:
 *
 *     Brain\FacadeName::fooMethod( $foo, $bar, $baz );
 *
 * Same methods can be also called using dynamic methods:
 *
 *     $api = new Brain\FacadeName();
 *     $api->fooMethod( $foo, $bar, $baz );
 *
 * This is useful when the package is used inside OOP plugins, making use of dependency injection.
 *
 */
abstract class Facade {

    abstract static function getBindId();

    public static function api() {
        return self::getContainer()->get( static::getBindId() );
    }

    public static function __callStatic( $name, $arguments ) {
        if ( ! did_action( 'brain_loaded' ) || ! Brain::instance() instanceof Brain ) {
            return new \WP_Error( "brain-not-ready", "Brain container is not ready." );
        }
        $name = __CLASS__;
        $id = static::getBindId();
        if ( ! is_object( static::api() ) ) {
            return new \WP_Error( "{$id}-api-not-ready", "{$name} API object is not ready." );
        }
        if ( method_exists( static::api(), $name ) ) {
            try {
                return call_user_func_array( [ static::api(), $name ], $arguments );
            } catch ( Exception $exc ) {
                return \Brain\exception2WPError( $exc, $id );
            }
        } else {
            return new \WP_Error( "{$id}-api-invalid-call", "Invalid {$name} API call." );
        }
    }

    public function __call( $name, $arguments ) {
        return static::__callStatic( $name, $arguments );
    }

}