<?php

class User
{
    private $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function addUser($user)
    {
        $json = json_encode($user);
        file_put_contents($this->fileName, $json . PHP_EOL, FILE_APPEND | LOCK_EX);
        return $user;
    }

    public function getUser()
    {
        if (!file_exists($this->fileName)) {
            return null;
        }

        $file = fopen($this->fileName, 'r+');
        if (flock($file, LOCK_EX)) {
            $user = fgets($file);
            if ($user !== false) {
                ftruncate($file, 0);
                fwrite($file, substr($user, strlen($user)));
                fflush($file);
                flock($file, LOCK_UN);
                fclose($file);
                return json_decode($user);
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

    public function editUser($user)
    {
        $old_users = $this->getUsers();
        $users = array_map(function ($u) use ($user) {
            if ($u->id == $user['id']) {
                $u->name = $user['name'];
                $u->email = $user['email'];
                $u->phone = $user['phone'];
                $u->password = $user['password'];
                $u->created_at = $user['created_at'];
            }
            return $u;
        }, $old_users);
        $this->clear();
        foreach ($users as $u) {
            $this->addUser($u);
        }
    }

    public function deleteUser($id)
    {
        $old_users = $this->getUsers();
        $users = array_filter($old_users, function ($u) use ($id) {
            return $u->id != $id;
        });
        $this->clear();
        foreach ($users as $u) {
            $this->addUser($u);
        }
    }

    public function getUsers()
    {
        if (!file_exists($this->fileName)) {
            return [];
        }
        $file = fopen($this->fileName, 'r');
        if (flock($file, LOCK_SH)) {
            $users = [];
            while (($user = fgets($file)) !== false) {
                $users[] = json_decode($user);
            }
            flock($file, LOCK_UN);
            fclose($file);

            $users = array_map(function ($user) {
                $user->created_at = strtotime($user->created_at);
                return $user;
            }, $users);
            // change back to date format
            $users = array_map(function ($user) {
                $user->created_at = date('Y-m-d H:i:s', $user->created_at);
                return $user;
            }, $users);
            return $users;
        } else {
            fclose($file);
            return null;
        }
    }

    public function clear()
    {
        file_put_contents($this->fileName, '');
    }
}