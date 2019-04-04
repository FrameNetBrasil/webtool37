<?php
/**
 * Created by PhpStorm.
 * User: ematos
 * Date: 19/07/2018
 * Time: 10:22
 */

namespace ORM\Model;

class Frame
{
    protected $idFrame;
    protected $entry;
    protected $idEntity;


    /**
     * @return mixed
     */
    public function getIdFrame()
    {
        return $this->idFrame;
    }

    /**
     * @param mixed $idFrame
     */
    public function setIdFrame($idFrame)
    {
        $this->idFrame = $idFrame;
    }

    /**
     * @return mixed
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param mixed $entry
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }

    /**
     * @return mixed
     */
    public function getIdEntity()
    {
        return $this->idEntity;
    }

    /**
     * @param mixed $idEntity
     */
    public function setIdEntity($idEntity)
    {
        $this->idEntity = $idEntity;
    }

}
