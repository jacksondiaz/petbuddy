<?php

namespace AppBundle\Entity\Hotel;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class OccupiedRooms
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="occupiedRooms")
 *
 */

class OccupiedRooms{
    /**
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id;
    
    /**
     *
     * @ORM\Column(type="string")
     *
     */
    private $petName;

    /**
     *
     * @ORM\Column(type="string")
     *
     */
    private $petType;

    /**
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id){
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPetName(){
        return $this->petName;
    }

    /**
     * @param mixed $petName
     */
    public function setPetName($petName){
        $this->petName = $petName;
    }

    /**
     * @return mixed
     */
    public function getPetType(){
        return $this->petType;
    }

    /**
     * @param mixed $petType
     */
    public function setPetType($petType){
        $this->petType = $petType;
    }
    
}
