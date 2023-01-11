<?php

class MessageQueue
{
    private $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function push($message)
    {
        $json = json_encode($message);
        file_put_contents($this->fileName, $json . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public function pop()
    {
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
        $file = fopen($this->fileName, 'r');
        if (flock($file, LOCK_SH)) {
            $messages = [];
            while (($message = fgets($file)) !== false) {
                $messages[] = json_decode($message);
            }
            flock($file, LOCK_UN);
            fclose($file);
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
}