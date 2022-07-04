<?php

namespace App\Exception;

class MoreThanOneWinnerException extends \Exception
{
    public function __construct()
    {
        parent::__construct('More than one winner');
    }
}
