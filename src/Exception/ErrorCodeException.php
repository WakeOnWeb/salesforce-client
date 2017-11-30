<?php

namespace WakeOnWeb\SalesforceClient\Exception;

class ErrorCodeException extends SalesforceClientException
{
    private $errorCode;

    public static function createFromCode($code, $message)
    {
        $exception = new self($message);
        $exception->errorCode = $code;

        return $exception;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
