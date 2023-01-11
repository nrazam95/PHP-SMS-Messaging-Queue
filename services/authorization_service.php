<?php

class Authorization
{
    public function authorize($user)
    {
        if ($user != null) {
            return true;
        }

        throw new Exception('Unauthorized');
    }
}