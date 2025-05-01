<?php
declare(strict_types=1);

namespace CakeTezos\Test\TestCase\Identifier;

use ArrayObject;
use Cake\TestSuite\TestCase;
use CakeTezos\Identifier\TezosBaseIdentifier;

class TezosBaseIdentifierTest extends TestCase
{
    public function testIdentify()
    {
        $identifier = new TezosBaseIdentifier();

        $address = 'tz1UxbPFjP22Hmc4tz2cxEXUx3cz17W4L7ow';
        $credentials = [
            'pk' => 'edpkvYdirUXWtuwdcxPnXkbX4gXeL7LNtji4Qionp71d3Nw6Hmqezz',
            'pkh' => $address,
            // see https://github.com/ziwot/pezos/blob/main/tests/Unit/Keys/PubKeyTest.php#L34
            'message' => '05010000000548656c6c6f',
            'signature' => 'edsigtxHb4HCsgf3zLLcTx4Rj23Y3CSJf8jaRXwoVHZJgSsMhzFoxKtinx2TT5FgYKprLVQ9nq8o93MCpmxaTuRB7igT9b6nZyf',
        ];

        $result = $identifier->identify($credentials);
        $this->assertEquals(new ArrayObject(['address' => $address]), $result);
    }

    /**
     * testIdentifyMissingData
     *
     * @return void
     */
    public function testIdentifyMissingData()
    {
        $identifier = new TezosBaseIdentifier();

        $credentials = [
            'pk' => 'edpkvYdirUXWtuwdcxPnXkbX4gXeL7LNtji4Qionp71d3Nw6Hmqezz',
            'pkh' => 'tz1UxbPFjP22Hmc4tz2cxEXUx3cz17W4L7ow',
            'message' => '05010000000548656c6c6f',
        ];

        $result = $identifier->identify($credentials);
        $this->assertNull($result);
    }
}
