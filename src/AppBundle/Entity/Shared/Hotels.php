<?php

namespace AppBundle\Entity\Shared;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Hotels
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="hotels")
 *
 */

class Hotels{
    /**
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     *
     * @ORM\Column(type="integer")
     */
    private $numRooms;
    
    /**
     *
     * @ORM\Column(type="string")
     *
     */
    private $name;

    /**
     *
     * @ORM\Column(type="boolean")
     *
     */
    private $active;

    /**
     *
     * @ORM\Column(type="string")
     *
     */
    private $dbName;
    
    /**
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName(){
        return $this->name;
    }


    /**
     * @param mixed $name
     */
    public function setName($name){
        $this->name = $name;
    }


    /**
     * @return mixed
     */
    public function getNumRooms(){
        return $this->numRooms;
    }

    /**
     * @param mixed $numRooms
     */
    public function setNumRooms($numRooms){
        $this->numRooms = $numRooms;
    }

    /**
     * @return mixed
     */
    public function getActive(){
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active){
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getDbName(){
        return $this->dbName;
    }

    /**
     * @param mixed $dbName
     */
    public function setDbName($dbName){
        $this->dbName = $dbName;
    }

}
