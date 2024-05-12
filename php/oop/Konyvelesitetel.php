<?php

namespace Gerke\Imagetotext;


class Konyvelesitetel
{
    private $id;
    private $tartozik;
    private $kovetel;
    private $osszeg;
    private $teljesitesDatuma;
    private $bankSzamlaszam;
    private $penztarId;
    private $szamlaSzamlaszam = null;
    private $egyebId;
    private $felhasznaloId;

    public function __construct($t, $k, $o)
    {
        $this->tartozik = preg_replace('/[^0-9]/', '', $t);
        $this->kovetel = preg_replace('/[^0-9]/', '', $k);
        $this->osszeg = preg_replace('/[^0-9]/', '', $o);
    }

    /**
     * Get the value of tartozik
     */ 
    public function getTartozik()
    {
        return $this->tartozik;
    }

    /**
     * Get the value of kovetel
     */ 
    public function getKovetel()
    {
        return $this->kovetel;
    }

    /**
     * Get the value of osszeg
     */ 
    public function getOsszeg()
    {
        return $this->osszeg;
    }

    /**
     * Get the value of teljesitesDatuma
     */ 
    public function getTeljesitesDatuma()
    {
        return $this->teljesitesDatuma;
    }

    /**
     * Set the value of teljesitesDatuma
     *
     * @return  self
     */ 
    public function setTeljesitesDatuma($teljesitesDatuma)
    {
        $this->teljesitesDatuma = $teljesitesDatuma;

        return $this;
    }

    /**
     * Get the value of bank_szamlaszam
     */ 
    public function getBankSzamlaszam()
    {
        return $this->bankSzamlaszam;
    }

    /**
     * Set the value of bank_szamlaszam
     *
     * @return  self
     */ 
    public function setBankSzamlaszam($bankSzamlaszam)
    {
        $this->bankSzamlaszam = $bankSzamlaszam;

        return $this;
    }

    /**
     * Get the value of penztar_id
     */ 
    public function getPenztarId()
    {
        return $this->penztarId;
    }

    /**
     * Set the value of penztar_id
     *
     * @return  self
     */ 
    public function setPenztarId($penztarId)
    {
        $this->penztarId = $penztarId;

        return $this;
    }

    /**
     * Get the value of szamla_szamlaszam
     */ 
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

    /**
     * Get the value of egyeb_id
     */ 
    public function getEgyebId()
    {
        return $this->egyebId;
    }

    /**
     * Set the value of egyeb_id
     *
     * @return  self
     */ 
    public function setEgyebId($egyebId)
    {
        $this->egyebId = $egyebId;

        return $this;
    }

    /**
     * Get the value of felhasznalo_id
     */ 
    public function getFelhasznaloId()
    {
        return $this->felhasznaloId;
    }

    /**
     * Set the value of felhasznalo_id
     *
     * @return  self
     */ 
    public function setFelhasznaloId($felhasznaloId)
    {
        $this->felhasznaloId = $felhasznaloId;

        return $this;
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
}
