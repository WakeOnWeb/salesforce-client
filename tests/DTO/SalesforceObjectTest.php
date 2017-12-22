<?php

namespace Tests\WakeOnWeb\SalesforceClient\DTO;

use PHPUnit\Framework\TestCase;
use WakeOnWeb\SalesforceClient\DTO\SalesforceObject as SUT;

/**
 * SalesforceObjectTest
 *
 * @uses TestCase
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class SalesforceObjectTest extends TestCase
{
    public function test_create_from_array_empty_data()
    {
        $sut = SUT::createFromArray([]);

        $this->assertNull($sut->getType());
        $this->assertNull($sut->getUrl());
        $this->assertEquals($sut->getAttributes(), []);

        $this->assertNull($sut->getAttribute('url'));
        $this->assertFalse($sut->hasAttribute('url'));
        $this->assertEquals($sut->getAttribute('url', 'foobar'), 'foobar');

        $this->assertNull($sut->getField('Id'));
        $this->assertFalse($sut->hasField('Id'));
        $this->assertEquals($sut->getField('Id', 'foobar'), 'foobar');
    }

    public function test_create_from_array_real_data()
    {
        $sut = SUT::createFromArray([
            'attributes' => [
                'url' => 'https://domain.tld',
                'type' => 'Foo',
            ],
            'Id' => 1337
        ]);

        $this->assertEquals($sut->getType(), 'Foo');
        $this->assertEquals($sut->getUrl(), 'https://domain.tld');
        $this->assertEquals($sut->getAttributes(), [
            'url' => 'https://domain.tld',
            'type' => 'Foo',
        ]);

        $this->assertEquals($sut->getAttribute('url'), 'https://domain.tld');
        $this->assertEquals($sut->getAttribute('url', 'foobar'), 'https://domain.tld');
        $this->assertTrue($sut->hasAttribute('url'));

        $this->assertEquals($sut->getField('Id'), 1337);
        $this->assertTrue($sut->hasField('Id'));
        $this->assertEquals($sut->getField('Id', 'foobar'), 1337);
    }

}
