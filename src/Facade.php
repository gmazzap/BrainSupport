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

    private static $facade_api;

    /**
     * Must be overridded by concrete facades returning the Brain container id for facade API object
     *
     * @throws \RuntimeException
     */
    public static function getBindId() {
        throw new \RuntimeException( __METHOD__ . ' must be overridden in concrete facades' );
    }

    /**
     * @param string $id Brain container id for the facade
     * @return mixed The facade obect or Error
     */
    public static function api( $id = NULL ) {
        try {
            if ( is_null( $id ) ) {
                $id = static::getBindId();
            }
            self::$facade_api = Container::instance()->get( $id );
            return self::$facade_api;
        } catch ( \Exception $exception ) {
            return \Brain\exception2WPError( $exception, get_called_class() );
        }
    }

    public static function __callStatic( $name, $arguments ) {
        if ( is_null( self::$facade_api ) ) {
            $check = self::check();
            if ( is_wp_error( $check ) ) {
                return $check;
            }
        }
        if ( is_wp_error( self::$facade_api ) ) {
            return self::$facade_api;
        }
        return self::execute( $name, $arguments );
    }

    public function __call( $name, $arguments ) {
        return static::__callStatic( $name, $arguments );
    }

    private static function check() {
        $facade = get_called_class();
        if ( ! class_exists( 'brain\Container' ) ) {
            $msg = "Please install %s package using composer and be sure to load Composer autoload";
            return new \WP_Error( "brain-not-installed", sprintf( $msg, $facade ) );
        }
        if ( ! Container::instance() instanceof Container ) {
            $msg = "Brain container is not ready. Whait for brain_loaded before using %s API";
            return new \WP_Error( "brain-not-ready", sprintf( $msg, $facade ) );
        }
        $api = static::api();
        if ( is_wp_error( $api ) ) {
            return $api;
        }
        if ( ! is_object( $api ) ) {
            $msg = "%s API object is not ready.";
            return new \WP_Error( "brain-api-not-ready", sprintf( $msg, $facade ) );
        }
    }

    private static function execute( $name, $arguments ) {
        $facade = get_called_class();
        if ( method_exists( self::$facade_api, $name ) ) {
            try {
                return call_user_func_array( [ self::$facade_api, $name ], $arguments );
            } catch ( \Exception $exception ) {
                return \Brain\exception2WPError( $exception, $facade );
            }
        } else {
            $msg = "Invalid API call: method %s does not exists on %s API";
            return new Error( "brain-api-invalid-call", sprintf( $msg, $name, $facade ) );
        }
    }

}