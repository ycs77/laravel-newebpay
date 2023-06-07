<?php

namespace Ycs77\NewebPay\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class NewebpayDecodeFailException extends Exception
{
    public function __construct(Throwable $previous, mixed $errorData)
    {
        Log::error('The NewebPay decode error content: ', [$errorData]);

        parent::__construct('The NewebPay decode data error.', 400, $previous);
    }
}
