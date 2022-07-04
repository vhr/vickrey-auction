<?php

namespace App\Exception;

class NoSecondBuyerException extends \Exception
{
    public function __construct()
    {
        parent::__construct('No second buyer bid');
    }
}
