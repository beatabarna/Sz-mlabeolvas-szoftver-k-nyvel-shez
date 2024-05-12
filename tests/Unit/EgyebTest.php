<?php

namespace Gerke\Imagetotext;

use Gerke\Imagetotext\Egyeb;
use PHPUnit\Framework\TestCase;

class EgyebTest extends TestCase
{
    private $egyeb;

    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];  
        $_SESSION['cegadoszam'] = '123';  
        $this->egyeb = new Egyeb();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        session_unset();  
    }

    public function testGetAndSetDatum()
    {
        $date = new \DateTime();
        $this->egyeb->setDatum($date);
        $this->assertSame($date, $this->egyeb->getDatum());
    }

    public function testAddKonyvelesiTetel()
    {
        $konyvelesitetel = $this->createMock(KonyvelesiTetel::class);
        $this->egyeb->addKonyvelesiTetel($konyvelesitetel);
        $this->assertCount(1, $this->egyeb->getKonyvelesi_tetelek());
    }

    public function testKonyveles()
    {
        $conn = $this->createMock(Connection::class);
        $conn->method('setData')->willReturn(true);
        $conn->method('getData')->willReturn([['id' => 3]]);

        $this->egyeb = new Egyeb();
        $this->egyeb->setMegnevezes('Example Name2');
        $this->egyeb->setMegjegyzes('Example Name');
        $this->egyeb->setCegID('123');
        $this->egyeb->setID('123');
        $this->egyeb->addKonyvelesiTetel(new KonyvelesiTetel("513", "454", 500));  

        $result = $this->egyeb->konyveles('egyeb', $conn);
        $this->assertEquals('duplication', $result);
    }

    public function testInvDuplicationCheck()
    {
        $conn = $this->createMock(Connection::class);
        $conn->method('getData')->willReturn([['megnevezes' => 'Test Entry']]);

        $this->egyeb->setMegnevezes('Test Entry');
        $this->assertTrue($this->egyeb->invDuplicationCheck($conn));
    }
}
