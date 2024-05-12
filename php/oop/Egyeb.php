<?php

namespace Gerke\Imagetotext;

class Egyeb extends Konyveles
{

    private $id;
    private $megnevezes;
    private $datum;
    private $megjegyzes;
    private $konyveloID;
    private $cegID;

    public function __construct() {
        $this->connection = new Connection();
    }

    /**
     * Get the value of megnevezes
     */
    public function getMegnevezes()
    {
        return $this->megnevezes;
    }

    /**
     * Set the value of megnevezes
     *
     * @return  self
     */
    public function setMegnevezes($megnevezes)
    {
        $this->megnevezes = $megnevezes;

        return $this;
    }

    /**
     * Get the value of datum
     */
    public function getDatum()
    {
        return $this->datum;
    }

    /**
     * Set the value of datum
     *
     * @return  self
     */
    public function setDatum($datum)
    {
        $this->datum = $datum;

        return $this;
    }
    public function addKonyvelesiTetel($konyvelesitetel)
    {

        $this->konyvelesitetelek[] = $konyvelesitetel;
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
     * Get the value of megjegyzes
     */
    public function getMegjegyzes()
    {
        return $this->megjegyzes;
    }

    /**
     * Set the value of megjegyzes
     *
     * @return  self
     */
    public function setMegjegyzes($megjegyzes)
    {
        $this->megjegyzes = $megjegyzes;

        return $this;
    }

    /**
     * Get the value of konyvelesi_tetelek
     */
    public function getKonyvelesiTetelek()
    {
        return $this->konyvelesitetelek;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function konyveles($tipus, $conn): string
    {
        $parameters = array();
        switch ($tipus) {
            case 'egyeb':
                $parameters = [
                    ":megnevezes" => $this->getMegnevezes(),
                    ":megjegyzes" => $this->getMegjegyzes(),
                    ":ceg_adoszam" => $_SESSION["cegadoszam"]
                ];
                if (!$this->invDuplicationCheck($conn)) {
                    $conn->setData("INSERT INTO `egyeb`(`megnevezes`, `megjegyzes`, `ceg_adoszam`) 
                            VALUES 
                            (:megnevezes, :megjegyzes, :ceg_adoszam)", $parameters);
                    unset($parameters[":megjegyzes"]);
                    $result = $conn->getData("SELECT id FROM `egyeb` WHERE `megnevezes`=:megnevezes AND `ceg_adoszam`=:ceg_adoszam", $parameters);
                    $id = $result[0]["id"];
                    unset($parameters);
                    foreach ($this->getKonyvelesiTetelek() as $konyvelesitetelIndex => $konyvelesitetel) {
                        $parameters = [
                            ":datum" => $this->getDatum(),
                            ":tartozik" => $konyvelesitetel->getTartozik(),
                            ":kovetel" => $konyvelesitetel->getKovetel(),
                            ":osszeg" => $konyvelesitetel->getOsszeg(),
                            ":bank_szamlaszam" => null,
                            ":penztar_id" => null,
                            ":szamla_szamlaszam" => null,
                            ":egyeb_id" => $id,
                            ":felhasznalo_id" => $this->getKonyveloID(),
                        ];
                        $conn->setData("INSERT INTO `konyvelesi_tetel`(`datum`, `tartozik`, `kovetel`, `osszeg`, `bank_szamlaszam`, `penztar_id`, `szamla_szamlaszam`, `egyeb_id`, `felhasznalo_id`) 
                                VALUES 
                                (:datum, :tartozik, :kovetel, :osszeg, :bank_szamlaszam, :penztar_id, :szamla_szamlaszam, :egyeb_id, :felhasznalo_id)", $parameters);
                    }
                } else {
                    return "duplication";
                }
                break;
            default:
                break;
        }
        return "siker";
    }

    public function invDuplicationCheck($conn)
    {
        $parameters = [":megnevezes" => $this->getMegnevezes()];
        $result = $conn->getData("SELECT megnevezes FROM egyeb WHERE megnevezes=:megnevezes", $parameters);
        if(isset($result)){
            if (count($result) > 0) {
                return true;
            }
        }
        return false;
    }

    public function egyebModositasKonyveles($tipus)
    {
        /* $this->kiiras($egyeb); */
        switch ($tipus) {
            case 'egyeb':
                $this->parameters = [
                    ":megnevezes" => $this->getMegnevezes(),
                    ":megjegyzes" => $this->getMegjegyzes(),
                    ":ceg_adoszam" => $_SESSION["cegadoszam"],
                    ":id" => $this->getId()
                ];
                $this->connection->setData("UPDATE `egyeb` SET 
                `megnevezes` = :megnevezes,
                `megjegyzes` = :megjegyzes,
                `ceg_adoszam` = :ceg_adoszam
                WHERE `id` =  :id", $this->parameters);

                unset($this->parameters);
                foreach ($this->getKonyvelesiTetelek() as $konyvelesitetelIndex => $konyvelesitetel) {
                    $this->parameters = [
                        ":datum" => $this->getDatum(),
                        ":tartozik" => $konyvelesitetel->getTartozik(),
                        ":kovetel" => $konyvelesitetel->getKovetel(),
                        ":osszeg" => $konyvelesitetel->getOsszeg(),
                        ":bank_szamlaszam" => $konyvelesitetel->getBankSzamlaszam(),
                        ":penztar_id" => $konyvelesitetel->getPenztarId(),
                        ":szamla_szamlaszam" => $konyvelesitetel->getSzamlaSzamlaszam(),
                        ":egyeb_id" => $this->getId(),
                        ":felhasznalo_id" => $this->getKonyveloID(),
                        ":id" => $konyvelesitetel->getId()
                    ];

                    $this->connection->setData("UPDATE `konyvelesi_tetel` SET 
                    `datum` =:datum,
                    `tartozik` = :tartozik,
                    `kovetel` = :kovetel,
                    `osszeg` = :osszeg,
                    `bank_szamlaszam` = :bank_szamlaszam,
                    `penztar_id` = :penztar_id,
                    `szamla_szamlaszam` = :szamla_szamlaszam,
                    `egyeb_id` = :egyeb_id,
                    `felhasznalo_id` = :felhasznalo_id
                    WHERE
                    `id` = :id", $this->parameters);
                }
                break;
            default:
                break;
        }
    }
}
