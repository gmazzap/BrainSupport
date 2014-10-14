<?php namespace Brain;

class Error extends \WP_Error implements \ArrayAccess {

    function __call( $name, $arguments ) {
        $code = 'brain-call-on-error-' . strtolower( $name );
        $message = "The function {$name} was called on an error object";
        $this->add( $code, $message, $arguments );
        return $this;
    }

    public function offsetExists( $offset ) {
        $code = "brain-call-on-error-{$offset}";
        $message = "Tried to check for {$offset} property on an error object";
        $this->add( $code, $message );
        return FALSE;
    }

    public function offsetGet( $offset ) {
        $code = "brain-call-on-error-{$offset}";
        $message = "Tried to get {$offset} from an error object";
        $this->add( $code, $message );
        return $this;
    }

    public function offsetSet( $offset, $value ) {
        $code = "brain-call-on-error-{$offset}";
        $message = "Tried to set {$offset} on an error object";
        $this->add( $code, $message, $value );
    }

    public function offsetUnset( $offset ) {
        $code = "brian-call-on-error-{$offset}";
        $message = "Tried to unset {$offset} from an error object";
        $this->add( $code, $message );
    }

}