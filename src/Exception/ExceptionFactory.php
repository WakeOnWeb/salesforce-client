<?php

namespace WakeOnWeb\SalesforceClient\Exception;

use GuzzleHttp\Exception\RequestException;

abstract class ExceptionFactory
{
    public static function generateFromRequestException(RequestException $e)
    {
        $body = (string) $e->getResponse()->getBody();
        $data = json_decode($body, true);

        if (false === is_array($data)) {
            throw static::createDefaultException($e);
        }

        $error = current($data);

        if (false === is_array($error) || false === array_key_exists('errorCode', $error)) {
            throw static::createDefaultException($e);
        }

        $message = array_key_exists('message', $error) ? $error['message'] : $e->getMessage();

        switch ($error['errorCode']) {
            case 'DUPLICATES_DETECTED':
                return new DuplicatesDetectedException($message);
                break;
            case 'ENTITY_IS_DELETED':
                return new EntityIsDeletedException($message);
                break;
            case 'NOT_FOUND':
                return new NotFoundException($message);
                break;
            default:
                return ErrorCodeException::createFromCode($error['errorCode'], $message);
            break;
        }
    }

    private static function createDefaultException(RequestException $e)
    {
        return new SalesforceClientException($e->getMessage(), 0, $e);
    }
}
