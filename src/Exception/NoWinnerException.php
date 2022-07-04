<?php

namespace App\Exception;

class NoWinnerException extends \Exception
{
    public function __construct()
    {
        parent::__construct('No winner');
    }
}
