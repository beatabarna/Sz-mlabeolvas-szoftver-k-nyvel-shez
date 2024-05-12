<?php
namespace Gerke\Imagetotext;

use PHPUnit\Framework\TestCase;

final class KonyvelesitetelTest extends TestCase
{
    public function testSetAndGetTartozik()
    {
        $konyvelesitetel = new Konyvelesitetel('513', '454', '500');
        $this->assertEquals('513', $konyvelesitetel->getTartozik());
    }

    public function testSetAndGetKovetel()
    {
        $konyvelesitetel = new Konyvelesitetel('513', '454', '500');
        $this->assertEquals('454', $konyvelesitetel->getKovetel());
    }

    public function testSetAndGetOsszeg()
    {
        $konyvelesitetel = new Konyvelesitetel('513', '454', '500');
        $this->assertEquals('500', $konyvelesitetel->getOsszeg());
    }

    public function testSetAndGetTeljesitesDatuma()
    {
        $konyvelesitetel = new Konyvelesitetel('513', '454', '500');
        $konyvelesitetel->setTeljesitesDatuma('2024-01-01');
        $this->assertEquals('2024-01-01', $konyvelesitetel->getTeljesitesDatuma());
    }

    public function testSetAndGetBankSzamlaszam()
    {
        $konyvelesitetel = new Konyvelesitetel('513', '454', '500');
        $konyvelesitetel->setBankSzamlaszam('1234567890');
        $this->assertEquals('1234567890', $konyvelesitetel->getBankSzamlaszam());
    }

    public function testSetAndGetPenztarId()
    {
        $konyvelesitetel = new Konyvelesitetel('513', '454', '500');
        $konyvelesitetel->setPenztarId(101);
        $this->assertEquals(101, $konyvelesitetel->getPenztarId());
    }

    public function testSetAndGetSzamlaSzamlaszam()
    {
        $konyvelesitetel = new Konyvelesitetel('513', '454', '500');
        $konyvelesitetel->setSzamlaSzamlaszam('9876543210');
        $this->assertEquals('9876543210', $konyvelesitetel->getSzamlaSzamlaszam());
    }

    public function testSetAndGetEgyebId()
    {
        $konyvelesitetel = new Konyvelesitetel('513', '454', '500');
        $konyvelesitetel->setEgyebId(202);
        $this->assertEquals(202, $konyvelesitetel->getEgyebId());
    }

    public function testSetAndGetFelhasznaloId()
    {
        $konyvelesitetel = new Konyvelesitetel('513', '454', '500');
        $konyvelesitetel->setFelhasznaloId(303);
        $this->assertEquals(303, $konyvelesitetel->getFelhasznaloId());
    }

    public function testSetAndGetId()
    {
        $konyvelesitetel = new Konyvelesitetel('513', '454', '500');
        $konyvelesitetel->setId(1);
        $this->assertEquals(1, $konyvelesitetel->getId());
    }
}
