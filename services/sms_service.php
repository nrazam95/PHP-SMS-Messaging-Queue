<?php

require 'base/sms.php';

class SmsService
{
    private $sms;

    public function __construct()
    {
        $this->sms = new MessageQueue('sms.json');
    }

    public function addSms($sms)
    {
        return $this->sms->addSms($sms);
    }

    public function getSms()
    {
        return $this->sms->getSms();
    }

    public function getSmses()
    {
        return $this->sms->getSmses();
    }

    public function editSms($sms)
    {
        $this->sms->editSms($sms);
    }

    public function getSmsById($data)
    {
        $smses = $this->getSmses();
        if (isset($data['room_id']) && isset($data['sms_id'])) {
            foreach ($smses as $sms) {
                if ($data['sms_id'] == $sms->id && $data['room_id'] == $sms->room_id) {
                    return $sms;
                }
            }
        }
        return null;
    }

    public function getSMSByRoomId($data)
    {
        $smses = $this->getSmses();
        $room_smses = array();
        if (isset($data['room_id'])) {
            foreach ($smses as $sms) {
                if ($data['room_id'] == $sms->room_id) {
                    array_push($room_smses, $sms);
                }
            }
        }
        return $room_smses;
    }

    public function getSmsByPhone($data)
    {
        $smses = $this->getSmses();
        if (isset($data['phone'])) {
            foreach ($smses as $sms) {
                if ($data['phone'] == $sms->phone) {
                    return $sms;
                }
            }
        }
        return null;
    }

    public function clear()
    {
        $this->sms->clear();
    }

    public function updateStatusWhenGetSms($id)
    {
        $smses = $this->getSmses();
        $new_smses = array_filter($smses, function ($sms) use ($id) {
            if ($sms->user_id == $id) {
                $sms->status = 'read';
            }
            return $sms;
        });

        $this->clear();

        foreach ($new_smses as $sms) {
            $this->addSms($sms);
        }
    }

    public function updateSMS($data)
    {
        $smses = $this->getSmses();
        foreach ($smses as $sms) {
            if ($sms->id == $data['id']) {
                $sms->id = $data['id'];
                $sms->room_id = $data['room_id'];
                $sms->user_id = $data['user_id'];
                $sms->message = $data['message'];
                $sms->status = $data['status'];
                $sms->created_at = $data['created_at'];
            }
        }

        $this->clear();

        foreach ($smses as $sms) {
            $this->addSms($sms);
        }
    }

    public function deleteSMSById($data)
    {
        $smses = $this->getSmses();
        $new_smses = array_filter($smses, function ($sms) use ($data) {
            if ($sms->id != $data['sms_id']) {
                return $sms;
            }
        });

        $this->clear();

        foreach ($new_smses as $sms) {
            $this->addSms($sms);
        }
    }

    public function allMyUnreadSMS($data)
    {
        $room_service = new RoomService();
        $rooms = $room_service->getRooms();
        $smses = $this->getSmses();
        $my_smses = array();

        foreach ($rooms as $room) {
            // Find room by checking numbers existance
            if (in_array($data['phone'], $room->numbers)) {
                foreach ($smses as $sms) {
                    if ($sms->room_id == $room->id && $sms->status == 'unread' && $sms->user_id != $data['user_id']) {
                        array_push($my_smses, $sms);
                    }
                }
            }
        }

        return $my_smses;
    }
}