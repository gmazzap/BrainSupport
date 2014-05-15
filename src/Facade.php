<?php namespace Brain;

/**
 * Facade Class.
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
 * Moreover using this class gives consistency on returned objects: on error all methods return
 * a WP_Error instance: every exception thrown by package classes is converted to a WP_Error.
 *
 */
abstract class Facade {

    abstract static function getBindId();

    abstract static function getName();

    public static function api() {
        return Container::instance()->get( static::getBindId() );
    }

    public static function __callStatic( $name, $arguments ) {
        if ( ! Container::instance() instanceof Container ) {
            return new \WP_Error( "brain-not-ready", "Brain container is not ready." );
        }
        $id = static::getBindId();
        $api_name = static::getName();
        if ( ! is_string( $api_name ) || empty( $api_name ) ) $api_name = 'unknown';
        if ( ! is_object( static::api() ) ) {
            if ( ! is_string( $id ) || empty( $id ) ) $id = 'unknown';
            return new \WP_Error( "{$id}-api-not-ready", "{$api_name} API object is not ready." );
        }
        if ( method_exists( static::api(), $name ) ) {
            try {
                return call_user_func_array( [ static::api(), $name ], $arguments );
            } catch ( \Exception $exception ) {
                return \Brain\exception2WPError( $exception, $id );
            }
        } else {
            return new \WP_Error( "{$id}-api-invalid-call", "Invalid {$api_name} API call." );
        }
    }

    public function __call( $name, $arguments ) {
        return static::__callStatic( $name, $arguments );
    }

}