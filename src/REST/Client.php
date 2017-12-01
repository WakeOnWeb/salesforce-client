<?php

namespace WakeOnWeb\SalesforceClient\REST;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use WakeOnWeb\SalesforceClient\ClientInterface;
use WakeOnWeb\SalesforceClient\DTO;
use WakeOnWeb\SalesforceClient\Exception;
use WakeOnWeb\SalesforceClient\Exception\SalesforceClientException;
use WakeOnWeb\SalesforceClient\REST\GrantType\StrategyInterface as GrantTypeStrategyInterface;

class Client implements ClientInterface
{
    private $gateway;
    private $grantTypeStrategy;
    private $accessToken;

    const OBJECT_PATH = 'sobjects';

    public function __construct(Gateway $gateway, GrantTypeStrategyInterface $grantTypeStrategy)
    {
        $this->gateway = $gateway;
        $this->grantTypeStrategy = $grantTypeStrategy;
    }

    public function getAvailableResources(): array
    {
        return $this->doAuthenticatedRequest(
            new Request(
                'GET',
                $this->gateway->getServiceDataUrl()
            )
        );
    }

    public function getAllObjects(): array
    {
        return $this->doAuthenticatedRequest(
            new Request(
                'GET',
                $this->gateway->getServiceDataUrl(static::OBJECT_PATH)
            )
        );
    }

    public function getObjectMetadata(string $object, \DateTimeInterface $since = null): array
    {
        $headers = [];
        if ($since) {
            $headers['IF-Modified-Since'] = $since->format('D, j M Y H:i:s e');
        }

        return $this->doAuthenticatedRequest(
            new Request(
                'GET',
                $this->gateway->getServiceDataUrl(static::OBJECT_PATH.'/'.$object),
                $headers
            )
        );
    }

    public function describeObjectMetadata(string $object, \DateTimeInterface $since = null): array
    {
        $headers = [];
        if ($since) {
            $headers['IF-Modified-Since'] = $since->format('D, j M Y H:i:s e');
        }

        return $this->doAuthenticatedRequest(
            new Request(
                'GET',
                $this->gateway->getServiceDataUrl(static::OBJECT_PATH.'/'.$object.'/describe'),
                $headers
            )
        );
    }


    public function createObject(string $object, array $data): DTO\SalesforceObjectCreation
    {
        $data = $this->doAuthenticatedRequest(
            new Request(
                'POST',
                $this->gateway->getServiceDataUrl(static::OBJECT_PATH.'/'.$object),
                ['content-type' => 'application/json'],
                json_encode($data)
            )
        );

        return DTO\SalesforceObjectCreation::createFromArray($data);
    }

    public function patchObject(string $object, string $id, array $data): void
    {
        $this->doAuthenticatedRequest(
            new Request(
                'PATCH',
                $this->gateway->getServiceDataUrl(static::OBJECT_PATH.'/'.$object.'/'.$id),
                ['content-type' => 'application/json'],
                json_encode($data)
            )
        );
    }

    public function deleteObject(string $object, string $id): void
    {
        $this->doAuthenticatedRequest(
            new Request(
                'DELETE',
                $this->gateway->getServiceDataUrl(static::OBJECT_PATH.'/'.$object.'/'.$id)
            )
        );
    }

    public function getObject(string $object, string $id, array $fields = []): DTO\SalesforceObject
    {
        $url = $this->gateway->getServiceDataUrl(static::OBJECT_PATH.'/'.$object.'/'.$id);

        if (false === empty($fields)) {
            $url .= '?fields='.implode(',', $fields);
        }

        return DTO\SalesforceObject::createFromArray(
            $this->doAuthenticatedRequest(new Request('GET', $url))
        );
    }

    public function searchSOQL(string $query, bool $all = self::NOT_ALL): DTO\SalesforceObjectResults
    {
        $url = $this->gateway->getServiceDataUrl($all ? 'queryAll' : 'query').'?q='.$query;

        return DTO\SalesforceObjectResults::createFromArray(
            $this->doAuthenticatedRequest(
                new Request('GET', $url)

            )
        );
    }

    public function explainSOQL(string $query): array
    {
        $url = $this->gateway->getServiceDataUrl($all ? 'queryAll' : 'query').'?explain='.$query;

        return $this->doAuthenticatedRequest(
            new Request('GET', $url)
        );
    }

    private function doAuthenticatedRequest(Request $request)
    {
        $this->connectIfAccessTokenIsEmpty();

        $request = $request->withAddedHeader('Authorization', 'Bearer '.$this->accessToken);

        try {
            $client = new HttpClient();
            $response = $client->send($request);
        } catch (RequestException $e) {
            throw Exception\ExceptionFactory::generateFromRequestException($e);
        } catch (\Exception $e) {
            throw new SalesforceClientException($e->getMessage(), 0, $e);
        }

        return json_decode((string) $response->getBody(), true);
    }

    private function connectIfAccessTokenIsEmpty(): void
    {
        if (null !== $this->accessToken) {
            return;
        }

        $this->accessToken = $this->grantTypeStrategy->buildAccessToken($this->gateway);
    }
}
