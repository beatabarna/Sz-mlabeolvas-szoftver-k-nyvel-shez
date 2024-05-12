<?php

namespace Gerke\Imagetotext;

class Targyieszkoz
{

    private $megnevezes;
    private $ertek;
    private $szamla;
    private $megjegyzes;
    private $hasznalatiIdo;
    private $erteknovekedes;
    private $erteknovSzamla;
    private $erteknovMegjegyzes;
    private $ertekcsokkenes = 0;
    private $parameters;
    private $connection;

    public function __construct()
    {
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
     * Get the value of ertek
     */
    public function getErtek()
    {
        return $this->ertek;
    }

    /**
     * Set the value of ertek
     *
     * @return  self
     */
    public function setErtek($ertek)
    {
        $this->ertek = $ertek;

        return $this;
    }

    /**
     * Get the value of szamla
     */
    public function getSzamla()
    {
        return $this->szamla;
    }

    /**
     * Set the value of szamla
     *
     * @return  self
     */
    public function setSzamla($szamla)
    {
        $this->szamla = $szamla;

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
     * Get the value of hasznalatiIdo
     */
    public function getHasznalatiIdo()
    {
        return $this->hasznalatiIdo;
    }

    /**
     * Set the value of hasznalatiIdo
     *
     * @return  self
     */
    public function setHasznalatiIdo($hasznalatiIdo)
    {
        $this->hasznalatiIdo = $hasznalatiIdo;

        return $this;
    }

    /**
     * Get the value of erteknovekedes
     */
    public function getErteknovekedes()
    {
        return $this->erteknovekedes;
    }

    /**
     * Set the value of erteknovekedes
     *
     * @return  self
     */
    public function setErteknovekedes($erteknovekedes)
    {
        $this->erteknovekedes = $erteknovekedes;

        return $this;
    }

    /**
     * Get the value of erteknov_szamla
     */
    public function getErteknovSzamla()
    {
        return $this->erteknovSzamla;
    }

    /**
     * Set the value of erteknov_szamla
     *
     * @return  self
     */
    public function setErteknovSzamla($erteknovSzamla)
    {
        $this->erteknovSzamla = $erteknovSzamla;

        return $this;
    }

    /**
     * Get the value of erteknov_megjegyzes
     */
    public function getErteknovMegjegyzes()
    {
        return $this->erteknovMegjegyzes;
    }

    /**
     * Set the value of erteknov_megjegyzes
     *
     * @return  self
     */
    public function setErteknovMegjegyzes($erteknovMegjegyzes)
    {
        $this->erteknovMegjegyzes = $erteknovMegjegyzes;

        return $this;
    }

    /**
     * Get the value of ertekcsokkenes
     */
    public function getErtekcsokkenes()
    {
        return $this->ertekcsokkenes;
    }

    public function addErtekcsokkenes($ertek)
    {
        $this->ertekcsokkenes += $ertek;
    }

    public function targyieszkozrogzites($targyieszkozok)
    {
        foreach ($targyieszkozok as $index => $eszkoz) {
            $this->parameters = [
                ":megnevezes" => $eszkoz->getMegnevezes(),
                ":bekerulesi_ertek" => $eszkoz->getErtek(),
                ":ertekcsokkenes" => 0,
                ":megjegyzes" => $eszkoz->getMegjegyzes(),
                ":hasznalati_ido" => $eszkoz->getHasznalatiIdo(),
                ":szamla_szamlaszam" => $eszkoz->getSzamla()
            ];
            $this->connection->setData("INSERT INTO `targyi_eszkoz`(`megnevezes`, `bekerulesi_ertek`, `ertekcsokkenes`, `megjegyzes`, `hasznalati_ido`, `szamla_szamlaszam`) 
                        VALUES 
                        (:megnevezes, :bekerulesi_ertek, :ertekcsokkenes, :megjegyzes, :hasznalati_ido, :szamla_szamlaszam)", $this->parameters);
        }
    }

    public function targyieszkozBekerulesiErtekNoveles($targyieszkozok)
    {
        foreach ($targyieszkozok as $index => $eszkoz) {
            $this->parameters = [
                ":megnevezes" => $eszkoz->getMegnevezes(),
                ":bekerulesi_ertek" => $eszkoz->getErteknovekedes(),
                ":ertekcsokkenes" => 0,
                ":megjegyzes" => $eszkoz->getErteknovMegjegyzes(),
                ":hasznalati_ido" => $eszkoz->getHasznalatiIdo(),
                ":szamla_szamlaszam" => $eszkoz->getErteknovSzamla()
            ];
            $this->connection->setData("INSERT INTO `targyi_eszkoz`(`megnevezes`, `bekerulesi_ertek`, `ertekcsokkenes`, `megjegyzes`, `hasznalati_ido`, `szamla_szamlaszam`) 
                        VALUES 
                        (:megnevezes, :bekerulesi_ertek, :ertekcsokkenes, :megjegyzes, :hasznalati_ido, :szamla_szamlaszam)", $this->parameters);
        }
    }

    public function amortizacio($targyieszkoz)
    {
        $this->parameters = [
            ":megnevezes" => $targyieszkoz->getMegnevezes(),
            ":ertekcsokkenes" => $targyieszkoz->getErtekcsokkenes()
        ];
        $this->connection->setData(
            "UPDATE `targyi_eszkoz` SET `ertekcsokkenes` = :ertekcsokkenes WHERE `megnevezes` = :megnevezes AND `megjegyzes` = 'eszköz'",
            $this->parameters
        );
    }
}
