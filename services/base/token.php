<?php

class Token
{
    private $user;

    public function __construct()
    {
        $this->user = new UserService();
    }

    public function generateToken($user)
    {
        $token = $this->encryptToken($user->email);
        return $token;
    }
    
    public function tokenDecode($token)
    {
        $token = $this->decryptToken($token);
        $user = $this->user->getUserByEmail(array(
            'email' => $token
        ));
        return $user;
    }

    public function encryptToken($email)
    {
        $key = '1234567891011121';
        $iv = '1234567891011121';
        $encrypted = openssl_encrypt($email, 'AES-128-CBC', $key, 0, $iv);
        return $encrypted;
    }

    public function decryptToken($token)
    {
        $key = '1234567891011121';
        $iv = '1234567891011121';
        $decrypted = openssl_decrypt($token, 'AES-128-CBC', $key, 0, $iv);
        return $decrypted;
    }

    public function validateToken($token)
    {
        $decrypted_token = $this->decryptToken($token);
        $users = $this->user->getUserByEmail(array(
            'email' => $decrypted_token
        ));
        if ($users != null) {
            return true;
        }
        return false;
    }
}