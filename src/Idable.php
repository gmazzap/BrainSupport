<?php namespace Brain;

trait Idable
{

    public function setId($id)
    {
        if ( ! is_string($id) || empty($id)) {
            throw new \InvalidArgumentException;
        }
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

}