<?php

namespace Tests\WakeOnWeb\SalesforceClient\REST;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use WakeOnWeb\SalesforceClient\REST\Client as SUT;
use WakeOnWeb\SalesforceClient\REST\Gateway;
use WakeOnWeb\SalesforceClient\REST\GrantType\StrategyInterface;
use WakeOnWeb\SalesforceClient\DTO\SalesforceObjectCreation;
use WakeOnWeb\SalesforceClient\DTO\SalesforceObject;
use WakeOnWeb\SalesforceClient\DTO\SalesforceObjectResults;

class ClientTest extends TestCase
{
    public function test_get_available_resources()
    {
        $sut = $this->createSUT(
            new Request('GET', 'https://domain.tld', ['Authorization' => 'Bearer access_token']),
            new Response(200, [], '{"foo": "bar"}')
        );
        $this->assertEquals($sut->getAvailableResources(), ['foo' => 'bar']);
    }

    public function test_get_all_objects()
    {
        $sut = $this->createSUT(
            new Request('GET', 'https://domain.tld', ['Authorization' => 'Bearer access_token']),
            new Response(200, [], '{"foo": "bar"}')
        );
        $this->assertEquals($sut->getAllObjects(), ['foo' => 'bar']);
    }

    public function test_get_object_metadata()
    {
        $sut = $this->createSUT(
            new Request('GET', 'https://domain.tld', ['Authorization' => 'Bearer access_token']),
            new Response(200, [], '{"foo": "bar"}')
        );
        $this->assertEquals($sut->getObjectMetadata('foo'), ['foo' => 'bar']);


        $since = new \DateTimeImmutable();
        $sut = $this->createSUT(
            new Request('GET', 'https://domain.tld', [
                'Authorization' => 'Bearer access_token',
                'IF-Modified-Since' => $since->format('D, j M Y H:i:s e')
            ]),
            new Response(200, [], '{"foo": "bar"}')
        );
        $this->assertEquals($sut->getObjectMetadata('foo', $since), ['foo' => 'bar']);
    }

    public function test_describe_object_metadata()
    {
        $sut = $this->createSUT(
            new Request('GET', 'https://domain.tld', ['Authorization' => 'Bearer access_token']),
            new Response(200, [], '{"foo": "bar"}')
        );
        $this->assertEquals($sut->describeObjectMetadata('foo'), ['foo' => 'bar']);


        $since = new \DateTimeImmutable();
        $sut = $this->createSUT(
            new Request('GET', 'https://domain.tld', [
                'Authorization' => 'Bearer access_token',
                'IF-Modified-Since' => $since->format('D, j M Y H:i:s e')
            ]),
            new Response(200, [], '{"foo": "bar"}')
        );
        $this->assertEquals($sut->describeObjectMetadata('foo', $since), ['foo' => 'bar']);
    }

    public function test_create_object()
    {
        $response = [
            'id' => 1337,
            'success' => true,
            'errors' => [],
            'warnings' => [],
        ];

        $sut = $this->createSUT(null, new Response(200, [], json_encode($response)));
        // we can't test the request since stream are different ...
        // let's find a way to fix that.
        $this->assertEquals($sut->createObject('foo', []), SalesforceObjectCreation::createFromArray($response));
    }

    public function test_patch_object()
    {
        $sut = $this->createSUT(null, new Response(200));
        // we can't test the request since stream are different ...
        // let's find a way to fix that.
        $this->assertNull($sut->patchObject('foo', 1234, []));
    }

    public function test_delete_object()
    {
        $sut = $this->createSUT(
            new Request('DELETE', 'https://domain.tld', ['Authorization' => 'Bearer access_token']),
            new Response(200, [], '{"foo": "bar"}')
        );
        $this->assertNull($sut->deleteObject('foo', 1234));
    }

    public function test_get_object()
    {
        $response = [
            'attributes' => [],
            'Id'=> 1337
        ];

        $sut = $this->createSUT(
            new Request('GET', 'https://domain.tld', ['Authorization' => 'Bearer access_token']),
            new Response(200, [], json_encode($response))
        );
        $this->assertEquals($sut->getObject('foo', 1234), SalesforceObject::createFromArray($response));

        $response = [
            'attributes' => [],
            'Id'=> 1337
        ];

        $sut = $this->createSUT(
            new Request('GET', 'https://domain.tld?fields=foo,bar', ['Authorization' => 'Bearer access_token']),
            new Response(200, [], json_encode($response))
        );
        $this->assertEquals($sut->getObject('foo', 1234, ['foo', 'bar']), SalesforceObject::createFromArray($response));
    }

    public function test_search_soql()
    {
        $response = [
            'totalSize' => 0,
            'done' => true,
            'records' => []
        ];

        $sut = $this->createSUT(
            new Request('GET', 'https://domain.tld?q=MY QUERY', ['Authorization' => 'Bearer access_token']),
            new Response(200, [], json_encode($response))
        );
        $this->assertEquals($sut->searchSOQL('MY QUERY'), SalesforceObjectResults::createFromArray($response));
    }

    public function test_explain_soql()
    {
        $sut = $this->createSUT(
            new Request('GET', 'https://domain.tld?explain=MY QUERY', ['Authorization' => 'Bearer access_token']),
            new Response(200, [], '[]')
        );
        $this->assertEquals($sut->explainSOQL('MY QUERY'), []);
    }

    private function createSUT(Request $requestExpected = null, Response $httpClientResponse)
    {
        $gateway = $this->createMock(Gateway::class);
        $gateway->expects($this->once())
            ->method('getServiceDataUrl')
            ->willReturn('https://domain.tld');

        $grantTypeStrategy = $this->createMock(StrategyInterface::class);
        $httpClient = $this->createMock(\GuzzleHttp\Client::class);

        if ($requestExpected) {
            $httpClient->expects($this->once())
                ->method('send')
                ->with($requestExpected)
                ->willReturn($httpClientResponse);
        } else {
            $httpClient->expects($this->once())
                ->method('send')
                ->willReturn($httpClientResponse);
        }

        $grantTypeStrategy
            ->expects($this->once())
            ->method('buildAccessToken')
            ->willReturn('access_token');

        return new SUT($gateway, $grantTypeStrategy, $httpClient);
    }
}
