<?php

class PostSMSBody
{
    public $room_id;
    public $user_id;
    public $message;
}

interface SMSBodyInterface
{
    public function id(): int;
    public function room_id(): int;
    public function user_id(): int;
    public function status(): string;
    public function message(): string;
    public function created_at(): string;
}

class SMSBody implements SMSBodyInterface
{
    private $id;
    private $room_id;
    private $user_id;
    private $status;
    private $message;
    private $created_at;

    public function __construct($body)
    {
        $this->id = $this->createNextID();
        $this->room_id = $body['room_id'] ? $body['room_id'] : null;
        $this->user_id = $body['user_id'] ? $body['user_id'] : null;
        $this->message = $body['message'] ? $body['message'] : null;
        $this->status = 'unread';
        $this->created_at = date('Y-m-d H:i:s');
    }

    private function createNextID(): int
    {
        $sms_services = new SmsService();
        $sms = $sms_services->getSmses();
        if (count($sms) == 0) {
            return 1;
        } else {
            $last_sms = end($sms);
            return $last_sms->id + 1;
        }
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

    public function message(): string
    {
        return $this->message;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function created_at(): string
    {
        return $this->created_at;
    }
}

interface GetSMSInterface
{
    public function id(): int;
    public function room_id(): int;
    public function user_id(): int;
    public function status(): string;
    public function message(): string;
    public function created_at(): string;
}

class GetSMS implements GetSMSInterface
{
    private $id;
    private $room_id;
    private $user_id;
    private $status;
    private $message;
    private $created_at;

    public function __construct($sms)
    {
        $this->id = $sms['id'];
        $this->room_id = $sms['room_id'];
        $this->user_id = $sms['user_id'];
        $this->status = $sms['status'];
        $this->message = $sms['message'];
        $this->created_at = $sms['created_at'];
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
}