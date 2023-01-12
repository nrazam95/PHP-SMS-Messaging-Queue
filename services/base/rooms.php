<?php

class Room
{
    private $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function addRoom($room)
    {
        if ($this->checkIfTwoNumbersAreInRoom([
            $room['numbers'][0],
            $room['numbers'][1]
        ]) == false) {
            $existed_room = $this->getRoomsByNumbers([
                $room['numbers'][0],
                $room['numbers'][1]
            ]);
            return $existed_room;
        }
        $json = json_encode($room);
        file_put_contents($this->fileName, $json . PHP_EOL, FILE_APPEND | LOCK_EX);
        return $room;
    }

    public function getRoom()
    {
        $rooms = $this->getRooms();
        if (count($rooms) > 0) {
            return $rooms[count($rooms) - 1];
        }
        return null;
    }

    public function checkIfTwoNumbersAreInRoom(array $numbers)
    {
        $rooms = $this->getRooms();
        foreach ($rooms as $room) {
            if (in_array($numbers[0], $room->numbers) && in_array($numbers[1], $room->numbers)) {
                return false;
            }
        }
        return true;
    }

    public function getRoomsByNumbers($numbers)
    {
        $rooms = $this->getRooms();
        foreach ($rooms as $room) {
            if (in_array($numbers[0], $room->numbers) && in_array($numbers[1], $room->numbers)) {
                return $room;
            }
        }
        return true;
    }

    public function getRooms()
    {
        if (!file_exists($this->fileName)) {
            return [];
        }

        $file = fopen($this->fileName, 'r');
        if (flock($file, LOCK_SH)) {
            $rooms = [];
            while (($room = fgets($file)) !== false) {
                $rooms[] = json_decode($room);
            }
            flock($file, LOCK_UN);
            fclose($file);

            // Sort rooms by created_at
            usort($rooms, function ($a, $b) {
                return $a->created_at < $b->created_at;
            });
            return $rooms;
        } else {
            fclose($file);
            return null;
        }
    }

    public function clear()
    {
        file_put_contents($this->fileName, '');
    }

    public function getRoomById($data)
    {
        $rooms = $this->getRooms();
        if (isset($data)) {
            foreach ($rooms as $room) {
                if ($data == $room->id) {
                    return $room;
                }
            }
        }
        return null;
    }

    public function deleteRoom($data)
    {
        $rooms = $this->getRooms();
        $new_rooms = array_filter($rooms, function ($room) use ($data) {
            return $room->id != $data;
        });

        $this->clear();

        foreach ($new_rooms as $room) {
            $this->addRoom($room);
        }
    }
}