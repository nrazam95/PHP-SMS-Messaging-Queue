<?php

class MessageQueue
{
    private $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function addSms($message)
    {
        $json = json_encode($message);
        file_put_contents($this->fileName, $json . PHP_EOL, FILE_APPEND | LOCK_EX);
        return $message;
    }

    public function getSms()
    {
        if (!file_exists($this->fileName)) {
            return null;
        }
        $file = fopen($this->fileName, 'r+');
        if (flock($file, LOCK_EX)) {
            $message = fgets($file);
            if ($message !== false) {
                ftruncate($file, 0);
                fwrite($file, substr($message, strlen($message)));
                fflush($file);
                flock($file, LOCK_UN);
                fclose($file);
                return json_decode($message);
            } else {
                flock($file, LOCK_UN);
                fclose($file);
                return null;
            }
        } else {
            fclose($file);
            return null;
        }
    }

    public function getMessages()
    {
        if (!file_exists($this->fileName)) {
            return [];
        }
        $file = fopen($this->fileName, 'r');
        if (flock($file, LOCK_SH)) {
            $messages = [];
            while (($message = fgets($file)) !== false) {
                $messages[] = json_decode($message);
            }
            flock($file, LOCK_UN);
            fclose($file);

            $messages = array_map(function ($message) {
                $message->created_at = strtotime($message->created_at);
                return $message;
            }, $messages);
            return $messages;
        } else {
            fclose($file);
            return null;
        }
    }

    public function clear()
    {
        file_put_contents($this->fileName, '');
    }

    public function totalQueuedMessages()
    {
        if (!file_exists($this->fileName)) {
            return 0;
        }
        $file = fopen($this->fileName, 'r');
        if (flock($file, LOCK_SH)) {
            $count = 0;
            while (($message = fgets($file)) !== false) {
                $count++;
            }
            flock($file, LOCK_UN);
            fclose($file);
            return $count;
        } else {
            fclose($file);
            return null;
        }
    }

    public function getSmses()
    {
        if (!file_exists($this->fileName)) {
            return [];
        }

        $file = fopen($this->fileName, 'r');
        if (flock($file, LOCK_SH)) {
            $smses = [];
            while (($sms = fgets($file)) !== false) {
                $smses[] = json_decode($sms);
            }
            flock($file, LOCK_UN);
            fclose($file);

            // sort by created_at
            $smses = array_map(function ($sms) {
                $sms->created_at = strtotime($sms->created_at);
                return $sms;
            }, $smses);
            return $smses;
        } else {
            fclose($file);
            return null;
        }
    }
}