<?php namespace Brain;

/**
 * Check if the in the admin
 *
 * @return boolean
 */
function isAdmin() {
    return is_admin() && ! isAjax();
}

/**
 * Check if current is an ajax request
 *
 * @return boolean
 */
function isAjax() {
    defined( 'DOING_AJAX' ) && DOING_AJAX;
}

/**
 * Check if the theme has been loaded
 *
 * @return boolean
 */
function themeLoaded() {
    return ( did_action( 'after_setup_theme' ) && current_filter() !== 'after_setup_theme' );
}

/**
 * Check if backend has been inited
 *
 * @return boolean
 */
function adminInited() {
    return ( did_action( 'admin_init' ) && current_filter() !== 'admin_init' );
}

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
