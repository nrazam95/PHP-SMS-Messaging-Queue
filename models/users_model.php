<?php
interface UserBodyInterface
{
    public function id(): int;
    public function name(): string | null;
    public function email(): string | null;
    public function password(): string | null;
    public function phone(): string | null;
    public function created_at(): string | null;
}

class UserBody implements UserBodyInterface
{
    private $id;
    private $name;
    private $email;
    private $password;
    private $phone;
    private $created_at;

    public function __construct($body)
    {
        $this->id = $this->createNextID();
        $this->name = $body['name'] ? $body['name'] : null;
        $this->email = $body['email'] ? $body['email'] : null;
        $this->password = $body['password'] ? $body['password'] : null;
        $this->phone = $body['phone'] ? $body['phone'] : null;
        $this->created_at = date('Y-m-d H:i:s');
    }

    private function createNextID(): int
    {
        $user_services = new UserService();
        $users = $user_services->getUsers();
        if (count($users) == 0) {
            return 1;
        } else {
            $last_user = end($users);
            return $last_user->id + 1;
        }
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string | null
    {
        return $this->name;
    }

    public function email(): string | null
    {
        return $this->email;
    }

    public function password(): string | null
    {
        return $this->password;
    }

    public function phone(): string | null
    {
        return $this->phone;
    }

    public function created_at(): string
    {
        return $this->created_at;
    }
}