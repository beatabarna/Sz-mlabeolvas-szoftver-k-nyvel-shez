<?php
namespace Gerke\Imagetotext;
use Gerke\Imagetotext\Szamla;
use PHPUnit\Framework\TestCase;

class SzamlaTest extends TestCase
{
    private $szamla;

    protected function setUp(): void
    {
        $this->szamla = new Szamla();
    }

    public function testGetSetSzamlaszam()
    {
        $this->szamla->setSzamlaszam("SZ12345678");
        $this->assertEquals("SZ12345678", $this->szamla->getSzamlaszam());
    }

    public function testGetSetKiallitas()
    {
        $date = new \DateTime();
        $this->szamla->setKiallitas($date);
        $this->assertSame($date, $this->szamla->getKiallitas());
    }

    public function testGetSetFizhat()
    {
        $date = new \DateTime();
        $this->szamla->setFizhat($date);
        $this->assertSame($date, $this->szamla->getFizhat());
    }

    public function testGetSetTeljesites()
    {
        $date = new \DateTime();
        $this->szamla->setTeljesites($date);
        $this->assertSame($date, $this->szamla->getTeljesites());
    }

    public function testAddKonyvelesiTetel()
    {
        $tetel = $this->createMock(KonyvelesiTetel::class);

        $this->szamla->setSzamlaTipus('0'); // Szállítói számla
        $this->szamla->addKonyvelesiTetel($tetel);

        $tetelek = $this->szamla->getKonyvelesi_tetelek();
        $this->assertCount(1, $tetelek);
        $this->assertSame($tetel, $tetelek[0]);
    }

    public function testCalcSzamlaOsszeg()
    {
        $this->szamla->setSzamlaTipus(2);
        $tetel = new Konyvelesitetel("381", "513", 150);
        $this->szamla->addKonyvelesiTetel($tetel);
        $this->szamla->addKonyvelesiTetel($tetel);
        $this->szamla->calcSzamlaOsszeg();

        $this->assertEquals(300, $this->szamla->getOsszeg());
    }

    public function testInvDuplicationCheck()
    {
        $conn = $this->createMock(Connection::class);
        $conn->method('getData')->willReturn([['szamlaszam' => 'SZ12345678']]);

        $this->szamla->setSzamlaszam("SZ12345678");
        $this->assertTrue($this->szamla->invDuplicationCheck($conn));
    }
}
