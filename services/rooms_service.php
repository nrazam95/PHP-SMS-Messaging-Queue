<?php

require 'base/rooms.php';

class RoomService
{
    private $room;

    public function __construct()
    {
        $this->room = new Room('rooms.json');
    }

    public function addRoom($room)
    {
        return $this->room->addRoom($room);
    }

    public function getMyRooms($phone)
    {
        $rooms = $this->room->getRooms();
        $my_rooms = [];
        foreach ($rooms as $room) {
            if (in_array($phone, $room->numbers)) {
                $my_rooms[] = $room;
            }
        }
        return $my_rooms;
    }

    public function getRoom()
    {
        return $this->room->getRoom();
    }

    public function getRooms()
    {
        return $this->room->getRooms();
    }

    public function clear()
    {
        $this->room->clear();
    }

    public function getRoomById($data)
    {
        $assigned_room = $this->room->getRoomById($data);
        return $assigned_room;
    }

    public function addSms($sms)
    {
        $this->sms->addSms($sms);
    }

    public function getSms()
    {
        return $this->sms->getSms();
    }

    public function getSmses()
    {
        return $this->sms->getSmses();
    }

    public function clearSms()
    {
        $this->sms->clear();
    }

    public function getSmsByRoomId($data)
    {
        $all_rooms = $this->room->getRooms();
        $selected_room = [];
        if (isset($data['room_id'])) {
            foreach ($all_rooms as $room) {
                if ($data['room_id'] == $room->id) {
                    $selected_room[] = $room;
                }
            }
        }
        return $selected_room;
    }

    public function deleteRoom($data)
    {
        $this->room->deleteRoom($data);
    }
}