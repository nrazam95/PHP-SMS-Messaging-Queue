<?php

class Authentication
{
    private $user;
    private $token;

    public function __construct()
    {
        $this->user = new UserService();
        $this->token = new Token();
    }

    public function login($data)
    {
        $user = $this->user->getUserByEmail(array(
            'email' => $data['email']
        ));
        if ($user != null) {
            if ($user->password == $data['password']) {
                $token = $this->token->generateToken($user);
                $dataToSend['token'] = $token;
                return $dataToSend;
            }
        }
        
        throw new Exception('Invalid credentials');
    }

    public function register($data)
    {
        $this->user->addUser($data);
    }

    public function logout($data)
    {
        $this->token->deleteToken($data['token']);
        return 'Logged out successfully';
    }
}