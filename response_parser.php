<?php

class ResponseParser
{

    public function parse($data)
    {
        return json_encode($data);
    }
};