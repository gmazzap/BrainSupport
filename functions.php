<?php namespace Brain;

if ( ! function_exists( 'Brain\isAdmin' ) ) {

    /**
     * Check if the in the admin
     *
     * @return boolean
     */
    function isAdmin() {
        return is_admin() && ! isAjax();
    }

}

if ( ! function_exists( 'Brain\isAjax' ) ) {

    /**
     * Check if current is an ajax request
     *
     * @return boolean
     */
    function isAjax() {
        defined( 'DOING_AJAX' ) && DOING_AJAX;
    }

}

if ( ! function_exists( 'Brain\themeLoaded' ) ) {

    /**
     * Check if the theme has been loaded
     *
     * @return boolean
     */
    function themeLoaded() {
        return ( did_action( 'after_setup_theme' ) && current_filter() !== 'after_setup_theme' );
    }

}

if ( ! function_exists( 'Brain\adminInited' ) ) {

    /**
     * Check if backend has been inited
     *
     * @return boolean
     */
    function adminInited() {
        return ( did_action( 'admin_init' ) && current_filter() !== 'admin_init' );
    }

}

if ( ! function_exists( 'Brain\exception2WPError' ) ) {

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
        $msg .= $exc->getMessage() ? '. ' . $exc->getMessage() . '. ' : '';
        $msg .= 'STACK TRACE: ' . $exc->getTraceAsString();
        return new Error( "{$prefix}-exception-" . $name, $msg );
    }

}

if ( ! function_exists( 'Brain\getQueryType' ) ) {

    /**
     * Get the type of a WP_Query object.
     *
     * @param \WP_Query $query  Query object to check. If null main query is used.
     * @return string|boolean   False when no main query available and no query object given.
     */
    function getQueryType( \WP_Query $query = NULL ) {
        if ( is_null( $query ) ) {
            $query = isset( $GLOBALS[ 'wp_query' ] ) ? $GLOBALS[ 'wp_query' ] : NULL;
            if ( ! $query instanceof \WP_Query ) {
                return FALSE;
            }
        }
        $types = [
            'is_404'               => '404',
            'is_search'            => 'search',
            'is_front_page'        => 'frontpage',
            'is_home'              => 'home',
            'is_post_type_archive' => 'archive',
            'is_tax'               => 'taxonomy',
            'is_attachment'        => 'attachment',
            'is_single'            => 'single',
            'is_page'              => 'page',
            'is_category'          => 'category',
            'is_tag'               => 'tag',
            'is_author'            => 'author',
            'is_date'              => 'date',
            'is_comments_popup'    => 'comments_popup',
            'is_paged'             => 'paged'
        ];
        foreach ( $types as $callback => $type ) {
            if ( call_user_func( [ $query, $callback ] ) ) {
                return $type;
            }
        }
        return 'index';
    }

}

if ( ! function_exists( 'Brain\getQueryTypes' ) ) {

    function getQueryTypes( \WP_Query $query = NULL ) {
        $type = getQueryType( $query );
        if ( $type === FALSE ) {
            return FALSE;
        }
        if ( $type === 'index' ) {
            return [ 'index' ];
        }
        $types = [ $type ];
        if ( in_array( $type, [ 'taxonomy', 'category', 'tag', 'author', 'date' ], TRUE ) ) {
            $types[] = 'archive';
        }
        if ( $type === 'frontpage' ) {
            $types[] = get_option( 'show_on_front' ) === 'page' ? 'page' : 'home';
        }
        $types[] = 'index';
        return $types;
    }

}

if ( ! function_exists( 'Brain\getMainQueryTypes' ) ) {

    function getMainQueryTypes() {
        return getQueryTypes();
    }

}

if ( ! function_exists( 'Brain\stringKeyed' ) ) {

    /**
     * Get an array and return only items with string key
     *
     * @param $array    Original array
     * @return $array   Filtered array
     */
    function stringKeyed( Array $array = [ ] ) {
        if ( ! empty( $array ) ) {
            $keys = array_filter( array_keys( $array ), 'is_string' );
            $array = empty( $keys ) ? [ ] : array_intersect_key( $array, array_flip( $keys ) );
        }
        return $array;
    }

}
