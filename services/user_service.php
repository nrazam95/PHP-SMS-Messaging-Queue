<?php

require 'services/base/users.php';

class UserService
{
    private $user;

    public function __construct()
    {
        $this->user = new User('users.json');
    }

    public function addUser($user)
    {
        $this->user->addUser($user);
    }

    public function getUser()
    {
        return $this->user->getUser();
    }

    public function getUsers()
    {
        return $this->user->getUsers();
    }

    public function editUser($user)
    {
        $this->user->editUser($user);
    }

    public function getUserById($data)
    {
        $users = $this->getUsers();
        if (isset($data['id'])) {
            foreach ($users as $user) {
                if ($data['id'] == $user->id) {
                    return $user;
                }
            }
        }
        return null;
    }

    public function getUserByEmail($data)
    {
        $users = $this->getUsers();
        if (isset($data['email'])) {
            foreach ($users as $user) {
                if ($data['email'] == $user->email) {
                    return $user;
                }
            }
        }
        return null;
    }

    public function clear()
    {
        $this->user->clear();
    }

    public function deleteUser($data)
    {
        $this->user->deleteUser($data['id']);
    }

    public function validate($user)
    {
        $users = $this->getUsers();
        echo json_encode($users);
        foreach ($users as $u) {
            if ($u->email == $user['email'] || $u->phone == $user['phone']) {
                return true;
            }
        }
        return false;
    }
}