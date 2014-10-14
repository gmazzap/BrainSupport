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

    public static function getBindId() {
        throw new \RuntimeException( __METHOD__ . ' must be overridden in concrete facades.' );
    }

    public static function api( $id = NULL ) {
        if ( is_null( $id ) ) {
            $id = static::getBindId();
        }
        try {
            return Container::instance()->get( $id );
        } catch ( \ Exception $e ) {
            $code = preg_replace( '/[^\w]/', '-', strtolower( get_called_class() ) );
            return exception2WPError( $e, $code );
        }
    }

    public static function __callStatic( $name, $arguments ) {
        $id = self::getApiId();
        $api = is_wp_error( $id ) ? $id : self::getApiObject( $id );
        if ( is_wp_error( $api ) ) {
            return $api;
        }
        return static::execute( $api, $name, $arguments, $id );
    }

    public function __call( $name, $arguments ) {
        return static::__callStatic( $name, $arguments );
    }

    private static function getApiId() {
        if ( ! Container::instance() instanceof Container ) {
            return exception2WPError( new \RuntimeException( "Brain not ready." ) );
        }
        $id = static::getBindId();
        if ( ! is_string( $id ) || empty( $id ) ) {
            $code = preg_replace( '/[^\w]/', '-', strtolower( get_called_class() ) );
            return exception2WPError( new \RuntimeException( "Bad or empty facade ID." ), $code );
        }
        return $id;
    }

    private static function getApiObject( $id ) {
        $api = static::api( $id );
        if ( is_wp_error( $api ) ) {
            return $api;
        }
        if ( ! is_object( $api ) ) {
            return exception2WPError( new \RuntimeException( "API object is not ready." ), $id );
        }
        return $api;
    }

    private static function execute( $api, $name, $arguments, $id ) {
        if ( method_exists( $api, $name ) ) {
            try {
                return call_user_func_array( [ $api, $name ], $arguments );
            } catch ( \Exception $exception ) {
                return exception2WPError( $exception, $id );
            }
        }
        return exception2WPError( new \RuntimeException( "Invalid API call." ), $id );
    }

}