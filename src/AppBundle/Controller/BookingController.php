<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Entity\Shared\Hotels;
use AppBundle\Entity\Shared\ReservedRooms;
use AppBundle\Entity\Hotel\OccupiedRooms;
use \Doctrine\ORM\Query;

class BookingController extends Controller
{
    /**
     * @Route("/set", name="set")
     * @Method("POST")
     */
    public function setAction(Request $request){
        //Json request
        $data = json_decode($request->getContent(),true);

        if(!$data){
            return new Response("false", 400);
        }

        // Hotel capacity
        if($this->checkCapacity($data)) {
            $this->cleanDb();
            $reservedRooms = $data["reservedRooms"];
            for ($i = 1; $i < 4; $i++) {
                $hotel = $data['hotels'][$i];

                //Set data to shared Db
                $this->setSharedDb($i, $hotel);

                //Available rooms
                $capacity = $hotel["maxRooms"] - count($hotel["rooms"]);

                //Reserved rooms
                if($reservedRooms && $capacity){
                    $rooms = 0;
                    while($reservedRooms && $capacity){
                        $rooms++;
                        $capacity--;
                        $reservedRooms--;
                    }
                    //Save data in database
                    $this->setReservedRooms($i,$rooms);
                }

                //check hotel rooms
                if ($this->checkRatio($hotel["rooms"]) && $capacity > 0) {
                    foreach ($hotel["rooms"] as $room) {
                        //Save data in database
                        $result = $this->setHotelDb($i, $room);
                        if(!$result){
                            return new Response("false",400);
                        }
                    }
                }else{
                    return new Response("false",400);
                }
            }
            return new Response("true", 200);
        }else{
            return new Response("false", 400);
        }
    }

    /**
     * @Route("/get", name="get")
     * @Method("POST")
     */
    public function getAction(Request $request) {
        //receive a list of pets as a JSON
        $pets = json_decode($request->getContent(),true);

        if(!$pets){
            return new Response("Error", 400);
        }

        $data = $this->getData();
        $animals = array();

        foreach(array('CATS','DOGS') as $type) {
            if (isset($pets[$type])) {
                foreach ($pets[$type] as $petName) {
                    $pet = array(
                        'petId' => $this->getNewPetId($data),
                        'petType' => substr($type, 0, 3),
                        'petName' => $petName
                    );

                    $animals[] = $pet;
                }
            }
        }

        $data = $this->checkIn($data, $animals);

        if(!$data){
            return new Response("Error", 400);
        }

        return new Response(json_encode($data), 200);
    }

    /**
     * @Route("/disable/{hotelId}", name="disable")
     * @Method("GET")
     */
    public function disableAction($hotelId) {
        if(is_numeric($hotelId) && $hotelId > 0 && $hotelId < 4){
            $hotelId = (int)$hotelId;
            $data = $this->getData();

            $data['hotels'][$hotelId]['active'] = false;
            $rooms = $data['hotels'][$hotelId]['rooms'];
            $data['hotels'][$hotelId]['rooms'] = array();

            $data = $this->checkIn($data, $rooms);

            if(!$data){
                return new Response("Error", 400);
            }

            return new Response(json_encode($data), 200);
        }else{
            return new Response("Error", 400);
        }
    }

    // set data to the Shared DB
    private function setSharedDb($id,$data){
        $sharedDb = $this->getDoctrine()->getManager('shared_db');
        $hotels = new Hotels();
        $hotels->setId($id);
        $hotels->setNumRooms($data["maxRooms"]);
        $hotels->setName("Hotel {$id}");
        $hotels->setActive($data["active"]);
        $hotels->setDbName("HotelDB{$id}");

        $sharedDb->persist($hotels);
        $sharedDb->flush();
    }

    // set number of
    private function setReservedRooms($id,$number){
       $reservedRooms = new ReservedRooms();
        $reservedRooms->setId($id);
        $reservedRooms->setNumber($number);
        $reservedRooms->setDescription("Reserved, hotel {$id}");

        $sharedDb = $this->getDoctrine()->getManager('shared_db');
        $sharedDb->persist($reservedRooms);
        $sharedDb->flush();

    }

    //Set data to every single hotel
    private function setHotelDb($id,$data){
        if(!isset($data["petId"]) || !isset($data["petName"]) || !isset($data["petType"])){
            return false;
        }

        //Only dogs and cats are allowed
        if(strtoupper($data["petType"]) !== "DOG" && strtoupper($data["petType"]) !== "CAT"){
            return false;
        }

        $rooms = new OccupiedRooms();
        $rooms->setId($data["petId"]);
        $rooms->setPetName($data["petName"]);
        $rooms->setPetType($data["petType"]);

        $hotelDb = $this->getDoctrine()->getManager("hotel_db_{$id}");
        $hotelDb->persist($rooms);
        $hotelDb->flush();

        return true;
    }

    //Delete table rows
    private function cleanDb(){
        $sharedDb = $this->getDoctrine()->getManager('shared_db');

        //clean shared Db
        $query = $sharedDb->createQuery('DELETE AppBundle\Entity\Shared\Hotels');
        $query->execute();

        //clean reserved rooms
        $query = $sharedDb->createQuery('DELETE AppBundle\Entity\Shared\ReservedRooms');
        $query->execute();

        //clean all hotels
        for($i = 1; $i < 4; $i++) {
            $hotelDb = $this->getDoctrine()->getManager("hotel_db_{$i}");
            $query = $hotelDb->createQuery('DELETE AppBundle\Entity\Hotel\OccupiedRooms');
            $query->execute();
        }
    }

    //check reserved rooms and the number of pets
    private function checkCapacity($data){
        $availableRooms = 0;
        for($i = 1; $i < 4; $i++){
            $availableRooms += $data['hotels'][$i]["maxRooms"] - count($data['hotels'][$i]["rooms"]);
        }
        return $availableRooms >= $data['reservedRooms'];
    }

    private function checkRatio($rooms){
        $dogs = $cats = 0;
            foreach ($rooms as $room){
                switch (strtoupper($room["petType"])) {
                    case "DOG":
                        $dogs++;
                        break;
                    case "CAT":
                        $cats++;
                        break;
            }
        }

        return (($cats/2 < $dogs && $dogs/2 < $cats) || ($cats == 0 || $dogs == 0));
    }

    //Add pets
    private function checkIn($data,$pets){
        $dogs = $cats = array();
        $reservedRooms = $this->getReservedRooms();

        for($i = 1; $i < 4; $i++) {
            $rooms = $data['hotels'][$i]['rooms'];
            if($rooms){
                foreach($rooms as $room){
                    if($room["petType"] === "DOG"){
                        $dogs[] = $room;
                    }else{
                        $cats[] = $room;
                    }
                }
                $data['hotels'][$i]['rooms'] = array();
            }
        }

        foreach($pets as $pet){
            if($pet["petType"] === "DOG"){
                $dogs[] = $pet;
            }else{
                $cats[] = $pet;
            }
        }

        for($k = 1; $k < 4; $k++){
            $hotel = $data['hotels'][$k];
            $availableRooms = $hotel['maxRooms'] - $reservedRooms[$k];

            $animals = count($cats) + count($dogs);
            if($hotel['active'] && $availableRooms > 0 && $animals > 0) {
                $capacity = $availableRooms <= $animals ? $availableRooms : $animals;
                $inserted = false;
                do {
                    $permutations = $this->getGuestPermutations($capacity);
                    foreach ($permutations as $permutation) {
                        $newRoom = array();
                        if ($permutation['cats'] <= count($cats) && $permutation['dogs'] <= count($dogs)) {

                            for ($i = 1; $i <= $permutation['cats']; $i++) {
                                $newRoom[] = array_shift($cats);
                            }

                            for ($i = 1; $i <= $permutation['dogs']; $i++) {
                                $newRoom[] = array_shift($dogs);
                            }

                            $data['hotels'][$k]['rooms'] = $newRoom;
                            $inserted = true;
                            break;
                        }
                    }
                    $capacity--;
                }while(!$inserted && $capacity > 0);
            }
        }

        if(count($cats) > 0 || count($dogs) > 0){
            return false;
        }

        return $data;
    }

    // Return a new ID
    private function getNewPetId($data){
        $ids = array();
        for ($i = 1; $i < 4; $i++) {
            $hotel = $data['hotels'][$i];
            foreach ($hotel['rooms'] as $room) {
                $ids[] = $room['petId'];
            }
        }
        return $ids ? max($ids) + 1 : 1;
    }

    //Retrieve data from DB
    private function getData(){
        $sharedDb = $this->getDoctrine()->getManager('shared_db');

        $hotels = array();
        for($i = 1; $i < 4; $i++) {
            // Select DB
            $hotelDb = $this->getDoctrine()->getManager("hotel_db_{$i}");
            // Shared info
            $hotel = $sharedDb->getRepository('AppBundle\Entity\Shared\Hotels')->find($i);

            //DB query
            $rooms = $hotelDb->getRepository('AppBundle\Entity\Hotel\OccupiedRooms')
                ->createQueryBuilder('q')
                ->select(array('q.id as petId', 'q.petType', 'q.petName'))
                ->orderBy('q.id', 'ASC')
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY);

            //Hotel details
            if ($hotel) {
                $hotels[$i] = array(
                    'active' => $hotel->getActive(),
                    'maxRooms' => $hotel->getNumRooms(),
                    'rooms' => $rooms
                );
            }else{
                $hotels[$i] = array();
            }
        }

        $reservedRooms = 0;
        foreach($this->getReservedRooms() as $reservedRoom){
            $reservedRooms += $reservedRoom;
        }

        return array(
            'reservedRooms' => $reservedRooms,
            'hotels' => $hotels
        );

    }

    //Retrieve reserved rooms from Db
    private function getReservedRooms(){
        $sharedDb = $this->getDoctrine()->getManager('shared_db');
        for($i = 1; $i < 4; $i++) {
            $room = $sharedDb->getRepository('AppBundle\Entity\Shared\ReservedRooms')->find($i);
            $reservedRooms[$i] = $room ? $room->getNumber() : 0;
        }
        return $reservedRooms;
    }

    //Return permutations
    private function getPermutations($chars, $size, $combinations) {
        if (empty($combinations)) {
            $combinations = $chars;
        }

        if ($size == 1) {
            return $combinations;
        }

        $newCombinations = array();
        foreach ($combinations as $combination) {
            foreach ($chars as $char) {
                $value = $combination .','.$char;
                $array = explode(',',$value);
                sort($array);
                $final = implode('',$array);
                $newCombinations[$final] = $final;
            }
        }

        return $this->getPermutations($chars, $size - 1, $newCombinations);
    }


    //Return the number of cats and dogs
    private function getGuestPermutations($numRooms){
        $permutations = $this->getPermutations(array('0', '1'), $numRooms, array( ));
        $result = array();

        foreach($permutations as $option){
            $values = array_count_values(str_split($option));
            $cats = isset($values[0]) ? $values[0] : 0;
            $dogs = isset($values[1]) ? $values[1] : 0;

            if(($cats/2 < $dogs && $dogs/2 < $cats) || ($cats == 0 || $dogs == 0)){
               $result[] = array('cats' => $cats, 'dogs' => $dogs);
            }
        }

        return $result;
    }

}