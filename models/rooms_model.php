<?php

interface RoomBodyInterface
{
    public function id(): int;
    public function to_number(): string | null;
    public function from_number(): string | null;
    public function numbers(): array;
    public function created_at(): string | null;
}

class RoomBody implements RoomBodyInterface
{
    private $id;
    private $to_number;
    private $from_number;
    private $numbers;
    private $created_at;

    public function __construct($body)
    {
        $this->id = $this->createNextID();
        $this->to_number = $body['to_number'] ? $body['to_number'] : null;
        $this->from_number = $body['from_number'] ? $body['from_number'] : null;
        $this->numbers = $this->collectNumbers($body['to_number'], $body['from_number']);
        $this->created_at = date('Y-m-d H:i:s');
    }

    private function createNextID(): int
    {
        $room_services = new RoomService();
        $rooms = $room_services->getRooms();
        if (count($rooms) == 0) {
            return 1;
        } else {
            $last_room = end($rooms);
            return $last_room->id + 1;
        }
    }

    private function collectNumbers($to_number, $from_number): array
    {
        $numbers = array();
        array_push($numbers, $to_number);
        array_push($numbers, $from_number);
        return $numbers;
    }

    public function numbers(): array
    {
        return $this->numbers;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function to_number(): string | null
    {
        return $this->to_number;
    }

    public function from_number(): string | null
    {
        return $this->from_number;
    }

    public function created_at(): string | null
    {
        return $this->created_at;
    }
}

