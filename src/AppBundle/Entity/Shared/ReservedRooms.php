<?php

namespace AppBundle\Entity\Shared;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ReservedRooms
 * @package AppBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="reservedRooms")
 *
 */

class ReservedRooms{
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
    private $number;


    /**
     *
     * @ORM\Column(type="string")
     *
     */
    private $description;

    /**
     * @return mixed
     */
    public function getId()
    {
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
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

}
