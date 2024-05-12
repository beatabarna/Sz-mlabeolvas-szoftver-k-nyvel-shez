<?php

namespace Gerke\Imagetotext;
use PHPUnit\Framework\TestCase;

class TargyieszkozTest extends TestCase
{
    private $targyieszkoz;

    protected function setUp(): void
    {
        $this->targyieszkoz = new Targyieszkoz();
    }

    public function testSetAndGetMegnevezes()
    {
        $this->targyieszkoz->setMegnevezes('Laptop');
        $this->assertSame('Laptop', $this->targyieszkoz->getMegnevezes());
    }

    public function testSetAndGetErtek()
    {
        $this->targyieszkoz->setErtek(50000);
        $this->assertSame(50000, $this->targyieszkoz->getErtek());
    }

    public function testSetAndGetSzamla()
    {
        $this->targyieszkoz->setSzamla('1001');
        $this->assertSame('1001', $this->targyieszkoz->getSzamla());
    }

    public function testSetAndGetMegjegyzes()
    {
        $this->targyieszkoz->setMegjegyzes('New purchase');
        $this->assertSame('New purchase', $this->targyieszkoz->getMegjegyzes());
    }

    public function testSetAndGetHasznalatiIdo()
    {
        $this->targyieszkoz->setHasznalatiIdo(5);
        $this->assertSame(5, $this->targyieszkoz->getHasznalatiIdo());
    }

    public function testSetAndGetErteknovekedes()
    {
        $this->targyieszkoz->seterteknovekedes(2000);
        $this->assertSame(2000, $this->targyieszkoz->geterteknovekedes());
    }

    public function testSetAndGetErteknovSzamla()
    {
        $this->targyieszkoz->setErteknov_szamla('2002');
        $this->assertSame('2002', $this->targyieszkoz->getErteknov_szamla());
    }

    public function testSetAndGetErteknovMegjegyzes()
    {
        $this->targyieszkoz->setErteknov_megjegyzes('Value increased');
        $this->assertSame('Value increased', $this->targyieszkoz->getErteknov_megjegyzes());
    }

    public function testAddErtekcsokkenes()
    {
        $this->targyieszkoz->addErtekcsokkenes(1000);
        $this->targyieszkoz->addErtekcsokkenes(500);
        $this->assertSame(1500, $this->targyieszkoz->getErtekcsokkenes());
    }
}
