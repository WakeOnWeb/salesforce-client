<?php

namespace Tests\WakeOnWeb\SalesforceClient\DTO;

use PHPUnit\Framework\TestCase;
use WakeOnWeb\SalesforceClient\DTO\SalesforceObjectCreation as SUT;

/**
 * SalesforceObjectCreationTest
 *
 * @uses TestCase
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class SalesforceObjectCreationTest extends TestCase
{
    public function test_create_from_array_real_data()
    {
        $sut = SUT::createFromArray([
            'id' => 1337,
            'success' => true,
            'errors' => ['foo'],
            'warnings' => ['bar'],
        ]);

        $this->assertEquals($sut->getId(), 1337);
        $this->assertTrue($sut->isSuccess());
        $this->assertEquals($sut->getErrors(), ['foo']);
        $this->assertEquals($sut->getWarnings(), ['bar']);
    }

}
