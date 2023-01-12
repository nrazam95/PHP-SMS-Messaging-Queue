<?php

interface UnreadMessageInterface
{
    public function id(): int;
    public function sms_id(): int;
    public function created_at(): string;
}

class UnreadMessage implements UnreadMessageInterface
{
    private $id;
    private $sms_id;
    private $created_at;

    public function __construct($body)
    {
        $this->id = $this->createNextID();
        $this->sms_id = $body['sms_id'];
        $this->created_at = date('Y-m-d H:i:s');
    }

    private function createNextID(): int
    {
        $unread_services = new UnreadService();
        $unread = $unread_services->getUnread();
        if (count($unread) == 0) {
            return 1;
        } else {
            $last_unread = end($unread);
            return $last_unread->id + 1;
        }
    }

    public function id(): int
    {
        return $this->id;
    }

    public function sms_id(): int
    {
        return $this->sms_id;
    }

    public function created_at(): string
    {
        return $this->created_at;
    }
}