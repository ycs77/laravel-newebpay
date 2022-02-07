<?php

namespace Ycs77\NewebPay\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class NewebpayDecodeFailException extends Exception
{
    /**
     * Create a new newebpay decode fail exception.
     *
     * @param  \Throwable  $previous
     * @param  mixed  $errorData
     * @return void
     */
    public function __construct(Throwable $previous, $errorData)
    {
        Log::error('The NewebPay decode error content: ', [$errorData]);

        parent::__construct('The NewebPay decode data error.', 400, $previous);
    }
}
