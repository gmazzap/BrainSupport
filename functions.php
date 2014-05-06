<?php namespace Brain;

/**
 * Convert an exception to a WP_Error.
 *
 * @param \Exception $exc   Exception to convert
 * @param string $prefix    A prefix to use in WP_Error error code
 * @param string $code      Used in WP_Error error code. (Prepended with "{$prefix}-exception-").
 *                          If empty (default) the exception error code is used if any.
 * @param string $msg       Used in WP_Error error code. The exception message is appended id any.
 * @return \WP_Error
 */
function exception2WPError( \Exception $exc = NULL, $prefix = 'brain', $code = '', $msg = '' ) {
    if ( is_null( $exc ) ) return;
    if ( ! is_string( $prefix ) ) $prefix = 'brain';
    if ( ! is_string( $msg ) ) $msg = '';
    $name = get_class( $exc );
    if ( is_string( $code ) && ! empty( $code ) ) {
        $name .= "-{$code}";
    } else {
        $name .= $exc->getCode() ? '-' . $exc->getCode() : '';
    }
    $msg .= $exc->getMessage() ? '. ' . $exc->getMessage() : '';
    return new \WP_Error( "{$prefix}-exception-" . $name, $msg );
}
