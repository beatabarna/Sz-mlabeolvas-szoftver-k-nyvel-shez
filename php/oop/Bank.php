<?php

namespace Gerke\Imagetotext;

include_once('Konyveles.php');

class Bank extends Konyveles
{
    private $bankszamlaSzam;
    private $szamlaSzamlaszam = null;
    private $konyveloID;
    private $cegID;

    public function __construct()
    {
        $this->connection = new Connection();
    }

    public function addKonyvelesiTetel($konyvelesitetel)
    {
        $this->konyvelesitetelek[] = $konyvelesitetel;
    }

    /**
     * Get the value of bankszamlaSzam
     */
    public function getBankszamlaSzam()
    {
        return $this->bankszamlaSzam;
    }

    /**
     * Set the value of bankszamlaSzam
     *
     * @return  self
     */
    public function setBankszamlaSzam($bankszamlaSzam)
    {
        $this->bankszamlaSzam = $bankszamlaSzam;

        return $this;
    }

    /**
     * Get the value of konyveloID
     */
    public function getKonyveloID()
    {
        return $this->konyveloID;
    }

    /**
     * Set the value of konyveloID
     *
     * @return  self
     */
    public function setKonyveloID($konyveloID)
    {
        $this->konyveloID = $konyveloID;

        return $this;
    }

    /**
     * Get the value of cegID
     */
    public function getCegID()
    {
        return $this->cegID;
    }

    /**
     * Set the value of cegID
     *
     * @return  self
     */
    public function setCegID($cegID)
    {
        $this->cegID = $cegID;

        return $this;
    }

    /**
     * Get the value of konyvelesi_tetelek
     */
    public function getKonyvelesiTetelek()
    {
        return $this->konyvelesitetelek;
    }


    public function getSzamlaSzamlaszam()
    {
        return $this->szamlaSzamlaszam;
    }

    /**
     * Set the value of szamla_szamlaszam
     *
     * @return  self
     */
    public function setSzamlaSzamlaszam($szamlaSzamlaszam)
    {
        $this->szamlaSzamlaszam = $szamlaSzamlaszam;

        return $this;
    }

    public function konyveles($tipus, $conn): string
    {
        $parameters = array();
        switch ($tipus) {
            case 'bank':
                foreach ($this->getKonyvelesiTetelek() as $konyvelesitetelIndex => $konyvelesitetel) {
                    $parameters = [
                        ":datum" => $konyvelesitetel->getTeljesitesDatuma(),
                        ":tartozik" => $konyvelesitetel->getTartozik(),
                        ":kovetel" => $konyvelesitetel->getKovetel(),
                        ":osszeg" => $konyvelesitetel->getOsszeg(),
                        ":bank_szamlaszam" => $konyvelesitetel->getBankSzamlaszam(),
                        ":penztar_id" => null,
                        ":szamla_szamlaszam" => $konyvelesitetel->getSzamlaSzamlaszam(),
                        ":egyeb_id" => null,
                        ":felhasznalo_id" => $this->getKonyveloID(),
                    ];
                    $conn->setData("INSERT INTO `konyvelesi_tetel`(`datum`, `tartozik`, `kovetel`, `osszeg`, `bank_szamlaszam`, `penztar_id`, `szamla_szamlaszam`, `egyeb_id`, `felhasznalo_id`) 
                    VALUES 
                    (:datum, :tartozik, :kovetel, :osszeg, :bank_szamlaszam, :penztar_id, :szamla_szamlaszam, :egyeb_id, :felhasznalo_id)", $parameters);


                    if ($konyvelesitetel->getSzamlaSzamlaszam() !== null) {
                        $parameters = [
                            ":szamla_szamlaszam" => $konyvelesitetel->getSzamlaSzamlaszam(),
                        ];
                        $szamla = $conn->getData("SELECT SUM(k.osszeg) AS osszeg FROM konyvelesi_tetel k WHERE k.szamla_szamlaszam = :szamla_szamlaszam AND k.bank_szamlaszam IS NULL", $parameters);
                        $szamlaOsszeg = $szamla[0]["osszeg"] ?? 0;
                      

                        unset($parameters);
                        $parameters = [
                            ":szamla_szamlaszam" => $konyvelesitetel->getSzamlaSzamlaszam(),
                            ":bank_szamlaszam" => $konyvelesitetel->getBankSzamlaszam(),
                        ];
                        $tmp = $conn->getData("SELECT SUM(k.osszeg) AS bankOsszeg FROM konyvelesi_tetel k WHERE szamla_szamlaszam = :szamla_szamlaszam AND k.bank_szamlaszam = :bank_szamlaszam", $parameters);

                        $bankOsszeg = $tmp[0]["bankOsszeg"] ?? 0;
                     
                        if ($bankOsszeg === $szamlaOsszeg) {
                            $parameters = [
                                ":szamla_szamlaszam" => $konyvelesitetel->getSzamlaSzamlaszam(),
                            ];
                            $conn->setData("UPDATE szamla SET fizetve = 1 WHERE szamlaszam = :szamla_szamlaszam", $parameters);
                        }
                    }
                    unset($parameters);
                }
                break;
            default:
                break;
        }
        return "siker";
    }

    public function invDuplicationCheck($conn)
    {
    }

    public function bankModositasKonyveles($tipus)
    {
        switch ($tipus) {
            case 'bank':
                foreach ($this->getKonyvelesiTetelek() as $konyvelesitetelIndex => $konyvelesitetel) {
                    $this->parameters = [
                        ":datum" => $konyvelesitetel->getTeljesitesDatuma(),
                        ":tartozik" => $konyvelesitetel->getTartozik(),
                        ":kovetel" => $konyvelesitetel->getKovetel(),
                        ":osszeg" => $konyvelesitetel->getOsszeg(),
                        ":bank_szamlaszam" => $konyvelesitetel->getBankSzamlaszam(),
                        ":penztar_id" => null,
                        ":szamla_szamlaszam" => $konyvelesitetel->getSzamlaSzamlaszam(),
                        ":egyeb_id" => null,
                        ":felhasznalo_id" => $this->getKonyveloID(),
                        ":id" => $konyvelesitetel->getId()
                    ];
                    $this->connection->setData("UPDATE `konyvelesi_tetel` SET `datum` = :datum, `tartozik` = :tartozik, `kovetel` = :kovetel, `osszeg` = :osszeg, `bank_szamlaszam` = :bank_szamlaszam, `penztar_id` = :penztar_id, `szamla_szamlaszam` = :szamla_szamlaszam, `egyeb_id` = :egyeb_id, `felhasznalo_id` = :felhasznalo_id WHERE `id` = :id", $this->parameters);
                    unset($this->parameters);
                }
                break;

            default:
                break;
        }
    }
}
