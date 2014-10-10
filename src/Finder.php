<?php namespace Brain;

class Finder {

    const PLUGIN = 0;
    const THEME = 1;
    const CHILD = 2;

    private $base;
    private $type;
    private $sniffed;

    /**
     * @param string $path File or Folder path to take as base.
     */
    public function __construct( $path ) {
        $dir = $this->getDir( $path );
        $this->base = trailingslashit( $dir );
    }

    /**
     * Get absolute path for a given base relative path.
     *
     * @param string $rel
     * @return string
     */
    public function path( $rel = '' ) {
        $path = $this->base;
        if ( ! empty( $rel ) ) {
            $path .= filter_var( trim( wp_normalize_path( $rel ), '\\/ ' ), FILTER_SANITIZE_URL );
        }
        return trailingslashit( wp_normalize_path( $path ) );
    }

    /**
     * Return full url for a given base relative path
     *
     * @param string $rel
     * @return string
     */
    public function url( $rel = '' ) {
        $this->sniffed OR $this->sniffTheme();
        $url = $this->getBaseUrl();
        if ( ! empty( $rel ) ) {
            $url .= filter_var( trim( wp_normalize_path( $rel ), '\\/ ' ), FILTER_SANITIZE_URL );
        }
        return trailingslashit( $url );
    }

    private function getDir( $path ) {
        $dir = FALSE;
        if ( is_file( $path ) || is_dir( $path ) ) {
            $dir = is_file( $path ) ?
                wp_normalize_path( dirname( $path ) ) :
                wp_normalize_path( $path );
        }
        if ( empty( $dir ) || ! is_string( $dir ) ) {
            throw new \InvalidArgumentException;
        }
        return untrailingslashit( $dir );
    }

    private function sniffTheme() {
        $this->sniffed = TRUE;
        if ( strpos( $this->base, wp_normalize_path( get_theme_root() ) ) === 0 ) {
            $child = is_child_theme() ? wp_normalize_path( get_stylesheet_directory() ) : FALSE;
            $this->type = $child && strpos( $this->base, trailingslashit( $child ) ) === 0 ?
                self::CHILD :
                self::THEME;
            return;
        }
        $this->type = self::PLUGIN;
    }

    private function getBaseUrl() {
        if ( $this->type === self::PLUGIN ) {
            $file = trailingslashit( $this->base ) . 'file.php';
            return trailingslashit( plugins_url( '/', $file ) );
        }
        $uri = $this->type === self::CHILD ?
            get_stylesheet_directory_uri() :
            get_template_directory_uri();
        return trailingslashit( $uri );
    }

}