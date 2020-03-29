<?php

namespace App\Exceptions\Custom;

use Exception;

class ForbiddenException extends Exception
{
    /**
     * constructor
     * @param string $message
     * @param integer $code
     */
    public function __construct(
        $message = 'Forbidden',
        $code = 403
    ) {
        parent::__construct(
            $message,
            $code
        );
    }
}
