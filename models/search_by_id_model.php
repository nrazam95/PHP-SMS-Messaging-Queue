<?php

interface SMSSearchByIdModelInterface
{
    public function id(): int;
    public function room_id(): int;
    public function user_id(): int;
    public function status(): string;
    public function message(): string;
    public function created_at(): string;
    public function room(): object;
    public function sender(): object;
}

class SMSSearchByIdModel implements SMSSearchByIdModelInterface
{
    private $id;
    private $room_id;
    private $user_id;
    private $status;
    private $message;
    private $created_at;
    private $room;
    private $sender;

    public function __construct($sms)
    {
        $this->id = $sms['id'] ? $sms['id'] : null;
        $this->room_id = $sms['room_id'] ? $sms['room_id'] : null;
        $this->user_id = $sms['user_id'] ? $sms['user_id'] : null;
        $this->status = $sms['status'] ? $sms['status'] : null;
        $this->message = $sms['message'] ? $sms['message'] : null;
        $this->created_at = $sms['created_at'];
        $this->room = $this->getRoom($sms['room_id']);
        $this->sender = $this->getSender($sms['user_id']);
    }

    private function getRoom($room_id)
    {
        $room_services = new RoomService();
        $room = $room_services->getRoomById($room_id);
        return $room;
    }

    private function getSender($user_id)
    {
        $user_services = new UserService();
        $user = $user_services->getUserById(array(
            'id' => $user_id
        ));
        // remove password
        unset($user->password);
        return $user;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function room_id(): int
    {
        return $this->room_id;
    }

    public function user_id(): int
    {
        return $this->user_id;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function created_at(): string
    {
        return $this->created_at;
    }

    public function room(): object
    {
        return $this->room;
    }

    public function sender(): object
    {
        return $this->sender;
    }

    public function toObject()
    {
        return (object) [
            'id' => $this->id(),
            'room_id' => $this->room_id(),
            'user_id' => $this->user_id(),
            'status' => $this->status(),
            'message' => $this->message(),
            'created_at' => $this->created_at(),
            'room' => $this->room(),
            'sender' => $this->sender(),
        ];
    }
}