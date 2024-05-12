<?php
use PHPUnit\Framework\TestCase;
use Gerke\Imagetotext\Bank;
use Gerke\Imagetotext\Konyvelesitetel;
use Gerke\Imagetotext\Connection;

class BankTest extends TestCase
{
    private $bank;
    private $connectionMock;

    protected function setUp(): void
    {
        $this->connectionMock = $this->createMock(Connection::class);
        
        $this->bank = new Bank();
        
        $reflection = new \ReflectionClass($this->bank);
        $connectionProperty = $reflection->getProperty('connection');
        $connectionProperty->setAccessible(true);
        $connectionProperty->setValue($this->bank, $this->connectionMock);
    }

    public function testSetAndGetBankszamlaSzam()
    {
        $szamlaSzam = '12345';
        $this->bank->setBankszamlaSzam($szamlaSzam);
        $this->assertEquals($szamlaSzam, $this->bank->getBankszamlaSzam());
    }

    public function testSetAndGetKonyveloID()
    {
        $konyveloID = 1;
        $this->bank->setKonyveloID($konyveloID);
        $this->assertEquals($konyveloID, $this->bank->getKonyveloID());
    }

    public function testSetAndGetCegID()
    {
        $cegID = 2;
        $this->bank->setCegID($cegID);
        $this->assertEquals($cegID, $this->bank->getCegID());
    }

    public function testSetAndGetSzamla_szamlaszam()
    {
        $szamlaSzamlaszam = '67890';
        $this->bank->setSzamlaSzamlaszam($szamlaSzamlaszam);
        $this->assertEquals($szamlaSzamlaszam, $this->bank->getSzamlaSzamlaszam());
    }

    public function testAddKonyvelesiTetel()
    {
        $tetel = $this->createMock(Gerke\Imagetotext\KonyvelesiTetel::class);
        $this->bank->addKonyvelesiTetel($tetel);
        $this->assertCount(1, $this->bank->getKonyvelesiTetelek());
    }

    public function testKonyveles()
    {
        $conn = $this->createMock(Gerke\Imagetotext\Connection::class);
        $tetel = $this->createMock(Gerke\Imagetotext\KonyvelesiTetel::class);
        $tetel->method('getTeljesitesDatuma')->willReturn('2024-01-01');
        $tetel->method('getTartozik')->willReturn(1000);
        $tetel->method('getKovetel')->willReturn(500);
        $tetel->method('getOsszeg')->willReturn(1500);
        $tetel->method('getBankSzamlaszam')->willReturn('12345');
        $tetel->method('getSzamlaSzamlaszam')->willReturn('67890');

        $this->bank->addKonyvelesiTetel($tetel);

        $conn->expects($this->once())
             ->method('setData')
             ->with(
                 $this->stringContains('INSERT INTO `konyvelesi_tetel`'),
                 $this->arrayHasKey(':datum')
             );

        $this->assertEquals('siker', $this->bank->konyveles('bank', $conn));
    }

    public function testBankModositasKonyveles()
    {
        $konyvelesitetel = new Konyvelesitetel("333","555", 500);
        $konyvelesitetel->setId(5);
        $konyvelesitetel->setBankSzamlaszam("1111111111111111");
        $konyvelesitetel->setTeljesitesDatuma("2024-01-01");
        $konyvelesitetel->setSzamlaSzamlaszam("222222222");

        $this->bank->addKonyvelesiTetel($konyvelesitetel);
        $this->bank->setKonyveloID(1);
       
        $expectedSql = "UPDATE `konyvelesi_tetel` SET `datum` = :datum, `tartozik` = :tartozik, `kovetel` = :kovetel, `osszeg` = :osszeg, `bank_szamlaszam` = :bank_szamlaszam, `penztar_id` = :penztar_id, `szamla_szamlaszam` = :szamla_szamlaszam, `egyeb_id` = :egyeb_id, `felhasznalo_id` = :felhasznalo_id WHERE `id` = :id";
        $expectedParams = [
            ":datum" => '2024-01-01',
            ":tartozik" => '333',
            ":kovetel" => '555',
            ":osszeg" => 500,
            ":bank_szamlaszam" => '1111111111111111',
            ":penztar_id" => null,
            ":szamla_szamlaszam" => '222222222',
            ":egyeb_id" => null,
            ":felhasznalo_id" => 1,  
            ":id" => 5
        ];

        $this->connectionMock->expects($this->once())
            ->method('setData')
            ->with($this->equalTo($expectedSql), $this->equalTo($expectedParams));

        // Execute the method
        $this->bank->bankModositasKonyveles('bank');
    }
}
