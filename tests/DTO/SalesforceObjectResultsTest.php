<?php

namespace Tests\WakeOnWeb\SalesforceClient\DTO;

use PHPUnit\Framework\TestCase;
use WakeOnWeb\SalesforceClient\DTO\SalesforceObject;
use WakeOnWeb\SalesforceClient\DTO\SalesforceObjectResults as SUT;

/**
 * SalesforceObjectResultsTest
 *
 * @uses TestCase
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class SalesforceObjectResultsTest extends TestCase
{
    public function test_create_from_array_empty_data()
    {
        $sut = SUT::createFromArray([
            'totalSize' => 0,
            'done' => true,
            'records' => []
        ]);

        $this->assertEquals($sut->getTotalSize(), 0);
        $this->assertTrue($sut->isDone());
        $this->assertEquals($sut->getRecords(), []);
    }

    public function test_create_from_array_with_records()
    {
        $object1Data = [
            'attributes' => [
            ],
            'Id' => 1337,
        ];

        $object2Data = [
            'attributes' => [
            ],
            'Id' => 42,
        ];

        $sut = SUT::createFromArray([
            'totalSize' => 2,
            'done' => false,
            'records' => [$object1Data, $object2Data]
        ]);

        $this->assertEquals($sut->getTotalSize(), 2);
        $this->assertFalse($sut->isDone());
        $this->assertEquals($sut->getRecords()[0], SalesforceObject::createFromArray($object1Data));
        $this->assertEquals($sut->getRecords()[1], SalesforceObject::createFromArray($object2Data));
    }
}
