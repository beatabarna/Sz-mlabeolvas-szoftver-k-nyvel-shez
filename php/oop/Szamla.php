<?php

namespace Gerke\Imagetotext;

include_once('Konyveles.php');

class Szamla extends Konyveles
{
   private $szamlaszam;
   private $oldSzamlaszam;
   private $partner;
   private $osszeg = 0;
   private $kiallitas;
   private $fizhat;
   private $teljesites;
   private $vevo = 0;
   private $penztar = 0;
   private $fizetve = 0;
   private $megjegyzes;
   private $szamlaTipus;
   private $pdf;
   private $konyveloID;
   private $cegID;

   public function __construct() {
      $this->connection = new Connection();
  }

   /**
    * Get the value of szamlaszam
    */
   public function getSzamlaszam()
   {
      return $this->szamlaszam;
   }

   /**
    * Set the value of szamlaszam
    *
    * @return  self
    */
   public function setSzamlaszam($szamlaszam)
   {
      $this->szamlaszam = $szamlaszam;

      return $this;
   }

   /**
    * Get the value of kiallitas
    */
   public function getKiallitas()
   {
      return $this->kiallitas;
   }

   /**
    * Set the value of kiallitas
    *
    * @return  self
    */
   public function setKiallitas($kiallitas)
   {
      $this->kiallitas = $kiallitas;

      return $this;
   }

   /**
    * Get the value of fizhat
    */
   public function getFizhat()
   {
      return $this->fizhat;
   }

   /**
    * Set the value of fizhat
    *
    * @return  self
    */
   public function setFizhat($fizhat)
   {
      $this->fizhat = $fizhat;

      return $this;
   }

   /**
    * Get the value of teljesites
    */
   public function getTeljesites()
   {
      return $this->teljesites;
   }

   /**
    * Set the value of teljesites
    *
    * @return  self
    */
   public function setTeljesites($teljesites)
   {
      $this->teljesites = $teljesites;

      return $this;
   }

   /**
    * Get the value of vevo
    */
   public function getVevo()
   {
      return $this->vevo;
   }

   /**
    * Set the value of vevo
    *
    * @return  self
    */
   public function setVevo($vevo)
   {
      $this->vevo = $vevo;

      return $this;
   }

   /**
    * Get the value of penztar
    */
   public function getPenztar()
   {
      return $this->penztar;
   }

   /**
    * Set the value of penztar
    *
    * @return  self
    */
   public function setPenztar($penztar)
   {
      $this->penztar = $penztar;

      return $this;
   }

   /**
    * Get the value of fizetve
    */
   public function getFizetve()
   {
      return $this->fizetve;
   }

   /**
    * Set the value of fizetve
    *
    * @return  self
    */
   public function setFizetve($fizetve)
   {
      $this->fizetve = $fizetve;

      return $this;
   }

   /**
    * Get the value of pdf
    */
   public function getPdf()
   {
      return $this->pdf;
   }

   /**
    * Set the value of pdf
    *
    * @return  self
    */
   public function setPdf($pdf)
   {
      $this->pdf = $pdf;

      return $this;
   }


   /**
    * Get the value of osszeg
    */
   public function getOsszeg()
   {
      return $this->osszeg;
   }

   /**
    * Set the value of osszeg
    *
    * @return  self
    */
   public function setOsszeg($osszeg)
   {
      $this->osszeg = $osszeg;

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
    * Get the value of szamlaTipus
    */
   public function getSzamlaTipus()
   {
      return $this->szamlaTipus;
   }

   /**
    * Set the value of szamlaTipus
    *
    * @return  self
    */
   public function setSzamlaTipus($szamlaTipus)
   {
      $this->szamlaTipus = $szamlaTipus;

      return $this;
   }

   public function addKonyvelesiTetel($konyvelesitetel)
   {
      switch ($this->szamlaTipus) {
         case '0':
            /* szállítói számla */
            $konyvelesitetel->setSzamlaSzamlaszam($this->getSzamlaszam());
            $konyvelesitetel->setTeljesitesDatuma($this->getTeljesites());
            $this->konyvelesitetelek[] = $konyvelesitetel;
            break;
         case '1':
            /* vevői számla */
            $konyvelesitetel->setSzamlaSzamlaszam($this->getSzamlaszam());
            $konyvelesitetel->setTeljesitesDatuma($this->getTeljesites());
            $this->konyvelesitetelek[] = $konyvelesitetel;
            break;
         case '2':
            /* penztar számla */
            $konyvelesitetel->setSzamlaSzamlaszam($this->getSzamlaszam());
            $konyvelesitetel->setTeljesitesDatuma($this->getTeljesites());
            $this->konyvelesitetelek[] = $konyvelesitetel;
            break;
         default:
            break;
      }
   }

   public function calcSzamlaOsszeg()
   {
      foreach ($this->konyvelesitetelek as $index => $konyvelesitetel) {
         switch (true) {
            case (substr($konyvelesitetel->getKovetel(), 0, 2) === "45"):
               $this->osszeg += $konyvelesitetel->getOsszeg();
               break;
            case (substr($konyvelesitetel->getTartozik(), 0, 2) === "45"):
               $this->osszeg -= $konyvelesitetel->getOsszeg();
               break;
            case (substr($konyvelesitetel->getKovetel(), 0, 1) === "9"):
               $this->osszeg += $konyvelesitetel->getOsszeg();
               break;
            case (substr($konyvelesitetel->getTartozik(), 0, 1) === "9"):
               $this->osszeg -= $konyvelesitetel->getOsszeg();
               break;
            case (substr($konyvelesitetel->getTartozik(), 0, 3) === "381"):
               $this->osszeg += $konyvelesitetel->getOsszeg();
               break;
            case (substr($konyvelesitetel->getKovetel(), 0, 3) === "381"):
               $this->osszeg -= $konyvelesitetel->getOsszeg();
               break;
            default:
               $this->osszeg += $konyvelesitetel->getOsszeg();
               break;
         }
      }
   }
   /**
    * Get the value of partner
    */
   public function getPartner()
   {
      return $this->partner;
   }

   /**
    * Set the value of partner
    *
    * @return  self
    */
   public function setPartner($partner)
   {
      $this->partner = $partner;

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
    * Get the value of oldSzamlaszam
    */
   public function getOldSzamlaszam()
   {
      return $this->oldSzamlaszam;
   }

   /**
    * Set the value of oldSzamlaszam
    *
    * @return  self
    */
   public function setOldSzamlaszam($oldSzamlaszam)
   {
      $this->oldSzamlaszam = $oldSzamlaszam;

      return $this;
   }

   public function konyveles($tipus, $conn): string
   {
      $parameters = array();
      switch ($tipus) {
         case 'szallito':
            if (isset($_SESSION["cegadoszam"])) {
               $adoszam = $_SESSION["cegadoszam"];
            } else {
               $adoszam = $this->getCegID();
            }

            $parameters = [
               ":szamlaszam" => $this->getSzamlaszam(),
               ":teljesites" => $this->getTeljesites(),
               ":fizhat" => $this->getFizhat(),
               ":kiallitas" => $this->getKiallitas(),
               ":partner_adoszam" => $this->getPartner(),
               ":penztar" => $this->getPenztar(),
               ":megjegyzes" => $this->getMegjegyzes(),
               ":pdf" => $this->getPdf(),
               ":fizetve" => $this->getFizetve(),
               ":ceg_adoszam" =>  $adoszam
            ];
            if (!$this->invDuplicationCheck($conn)) {
               /* számla feltöltése */
               $conn->setData("INSERT INTO `szamla`(`szamlaszam`, `teljesites`, `fizhat`, `kiallitas`, `partner_adoszam`, `penztar`, `megjegyzes`, `pdf`, `fizetve`, `ceg_adoszam`) 
                 VALUES 
                 (:szamlaszam, :teljesites, :fizhat, :kiallitas, :partner_adoszam, :penztar, :megjegyzes, :pdf, :fizetve, :ceg_adoszam)", $parameters);
               unset($parameters);
               foreach ($this->getKonyvelesiTetelek() as $konyvelesitetelIndex => $konyvelesitetel) {
                  $parameters = [
                     ":datum" => $konyvelesitetel->getTeljesitesDatuma(),
                     ":tartozik" => $konyvelesitetel->getTartozik(),
                     ":kovetel" => $konyvelesitetel->getKovetel(),
                     ":osszeg" => $konyvelesitetel->getOsszeg(),
                     ":bank_szamlaszam" => null,
                     ":penztar_id" => null,
                     ":szamla_szamlaszam" => $this->getSzamlaszam(),
                     ":egyeb_id" => null,
                     ":felhasznalo_id" => $this->getKonyveloID(),
                  ];

                  /* könyvelés feltöltés */
                  $conn->setData("INSERT INTO `konyvelesi_tetel`(`datum`, `tartozik`, `kovetel`, `osszeg`, `bank_szamlaszam`, `penztar_id`, `szamla_szamlaszam`, `egyeb_id`, `felhasznalo_id`) 
                     VALUES 
                     (:datum, :tartozik, :kovetel, :osszeg, :bank_szamlaszam, :penztar_id, :szamla_szamlaszam, :egyeb_id, :felhasznalo_id)", $parameters);
               }
            } else {
               return "duplication";
            }
            break;
         case 'vevo':
            $parameters = [
               ":szamlaszam" => $this->getSzamlaszam(),
               ":teljesites" => $this->getTeljesites(),
               ":fizhat" => $this->getFizhat(),
               ":kiallitas" => $this->getKiallitas(),
               ":partner_adoszam" => $this->getPartner(),
               ":penztar" => $this->getPenztar(),
               ":megjegyzes" => $this->getMegjegyzes(),
               ":pdf" => $this->getPdf(),
               ":fizetve" => $this->getFizetve(),
               ":ceg_adoszam" => $_SESSION["cegadoszam"]
            ];
            if (!$this->invDuplicationCheck($conn)) {
               /* számla feltötlés */
               $conn->setData("INSERT INTO `szamla`(`szamlaszam`, `teljesites`, `fizhat`, `kiallitas`, `partner_adoszam`, `penztar`, `megjegyzes`, `pdf`, `fizetve`, `ceg_adoszam`) 
                 VALUES 
                 (:szamlaszam, :teljesites, :fizhat, :kiallitas, :partner_adoszam, :penztar, :megjegyzes, :pdf, :fizetve, :ceg_adoszam)", $parameters);
               unset($parameters);
               foreach ($this->getKonyvelesiTetelek() as $konyvelesitetelIndex => $konyvelesitetel) {
                  $parameters = [
                     ":datum" => $konyvelesitetel->getTeljesitesDatuma(),
                     ":tartozik" => $konyvelesitetel->getTartozik(),
                     ":kovetel" => $konyvelesitetel->getKovetel(),
                     ":osszeg" => $konyvelesitetel->getOsszeg(),
                     ":bank_szamlaszam" => null,
                     ":penztar_id" => null,
                     ":szamla_szamlaszam" => $this->getSzamlaszam(),
                     ":egyeb_id" => null,
                     ":felhasznalo_id" => $this->getKonyveloID(),
                  ];
                  /* könyvelés feltöltés */
                  $conn->setData("INSERT INTO `konyvelesi_tetel`(`datum`, `tartozik`, `kovetel`, `osszeg`, `bank_szamlaszam`, `penztar_id`, `szamla_szamlaszam`, `egyeb_id`, `felhasznalo_id`) 
                     VALUES 
                     (:datum, :tartozik, :kovetel, :osszeg, :bank_szamlaszam, :penztar_id, :szamla_szamlaszam, :egyeb_id, :felhasznalo_id)", $parameters);
               }
            } else {
               return "duplication";
            }

            break;
         case 'penztar':
            $parameters = [
               ":szamlaszam" => $this->getSzamlaszam(),
               ":teljesites" => $this->getTeljesites(),
               ":fizhat" => $this->getFizhat(),
               ":kiallitas" => $this->getKiallitas(),
               ":partner_adoszam" => $this->getPartner(),
               ":penztar" => $this->getPenztar(),
               ":megjegyzes" => $this->getMegjegyzes(),
               ":pdf" => $this->getPdf(),
               ":fizetve" => $this->getFizetve(),
               ":ceg_adoszam" => $_SESSION["cegadoszam"]
            ];
            if (!$this->invDuplicationCheck($conn)) {
               /* számla feltöltése */
               $conn->setData("INSERT INTO `szamla`(`szamlaszam`, `teljesites`, `fizhat`, `kiallitas`, `partner_adoszam`, `penztar`, `megjegyzes`, `pdf`, `fizetve`, `ceg_adoszam`) 
                     VALUES 
                     (:szamlaszam, :teljesites, :fizhat, :kiallitas, :partner_adoszam, :penztar, :megjegyzes, :pdf, :fizetve, :ceg_adoszam)", $parameters);
               unset($parameters);
               foreach ($this->getKonyvelesiTetelek() as $konyvelesitetelIndex => $konyvelesitetel) {
                  $parameters = [
                     ":datum" => $konyvelesitetel->getTeljesitesDatuma(),
                     ":tartozik" => $konyvelesitetel->getTartozik(),
                     ":kovetel" => $konyvelesitetel->getKovetel(),
                     ":osszeg" => $konyvelesitetel->getOsszeg(),
                     ":bank_szamlaszam" => null,
                     ":penztar_id" => null,
                     ":szamla_szamlaszam" => $this->getSzamlaszam(),
                     ":egyeb_id" => null,
                     ":felhasznalo_id" => $this->getKonyveloID(),
                  ];
                  /* könyvelés feltöltése */
                  $conn->setData("INSERT INTO `konyvelesi_tetel`(`datum`, `tartozik`, `kovetel`, `osszeg`, `bank_szamlaszam`, `penztar_id`, `szamla_szamlaszam`, `egyeb_id`, `felhasznalo_id`) 
                         VALUES 
                         (:datum, :tartozik, :kovetel, :osszeg, :bank_szamlaszam, :penztar_id, :szamla_szamlaszam, :egyeb_id, :felhasznalo_id)", $parameters);
               }
            } else {
               return "duplication";
            }
            break;
         case "penztar-szamlanelkul":
            $parameters = [
               ":szamlaszam" => $this->getSzamlaszam(),
               ":teljesites" => $this->getTeljesites(),
               ":fizhat" => $this->getFizhat(),
               ":kiallitas" => $this->getKiallitas(),
               ":partner_adoszam" => $this->getPartner(),
               ":penztar" => $this->getPenztar(),
               ":megjegyzes" => $this->getMegjegyzes(),
               ":pdf" => null,
               ":fizetve" => $this->getFizetve(),
               ":ceg_adoszam" => $_SESSION["cegadoszam"]
            ];
            if (!$this->invDuplicationCheck($conn)) {
               /* számla feltöltése */
               $conn->setData("INSERT INTO `szamla`(`szamlaszam`, `teljesites`, `fizhat`, `kiallitas`, `partner_adoszam`, `penztar`, `megjegyzes`, `pdf`, `fizetve`, `ceg_adoszam`) 
                     VALUES 
                     (:szamlaszam, :teljesites, :fizhat, :kiallitas, :partner_adoszam, :penztar, :megjegyzes, :pdf, :fizetve, :ceg_adoszam)", $parameters);
               unset($parameters);
               foreach ($this->getKonyvelesiTetelek() as $konyvelesitetelIndex => $konyvelesitetel) {
                  $parameters = [
                     ":datum" => $konyvelesitetel->getTeljesitesDatuma(),
                     ":tartozik" => $konyvelesitetel->getTartozik(),
                     ":kovetel" => $konyvelesitetel->getKovetel(),
                     ":osszeg" => $konyvelesitetel->getOsszeg(),
                     ":bank_szamlaszam" => null,
                     ":penztar_id" => null,
                     ":szamla_szamlaszam" => $this->getSzamlaszam(),
                     ":egyeb_id" => null,
                     ":felhasznalo_id" => $this->getKonyveloID(),
                  ];
                  /* könyvelés feltöltése */
                  $conn->setData("INSERT INTO `konyvelesi_tetel`(`datum`, `tartozik`, `kovetel`, `osszeg`, `bank_szamlaszam`, `penztar_id`, `szamla_szamlaszam`, `egyeb_id`, `felhasznalo_id`) 
                         VALUES 
                         (:datum, :tartozik, :kovetel, :osszeg, :bank_szamlaszam, :penztar_id, :szamla_szamlaszam, :egyeb_id, :felhasznalo_id)", $parameters);
               }
            } else {
               return "duplication";
            }
            break;
         case "only-penztar":
            $parameters = [
               ":datum" => $this->getTeljesites(),
               ":megjegyzes" => $this->getMegjegyzes(),
               ":ceg_adoszam" => $_SESSION["cegadoszam"]
            ];
            $conn->setData("INSERT INTO `penztar`(`datum`, `megjegyzes`, `ceg_adoszam`) 
                     VALUES 
                     (:datum, :megjegyzes, :ceg_adoszam)", $parameters);
            unset($parameters[":megjegyzes"]);
            $result = $conn->getData("SELECT id FROM `penztar` WHERE `datum`=:datum AND `ceg_adoszam`=:ceg_adoszam", $parameters);
            $id = $result[0]["id"];
            unset($parameters);
            foreach ($this->getKonyvelesiTetelek() as $konyvelesitetelIndex => $konyvelesitetel) {
               $parameters = [
                  ":datum" => $konyvelesitetel->getTeljesitesDatuma(),
                  ":tartozik" => $konyvelesitetel->getTartozik(),
                  ":kovetel" => $konyvelesitetel->getKovetel(),
                  ":osszeg" => $konyvelesitetel->getOsszeg(),
                  ":bank_szamlaszam" => null,
                  ":penztar_id" => $id,
                  ":szamla_szamlaszam" => $this->getSzamlaszam(),
                  ":egyeb_id" => null,
                  ":felhasznalo_id" => $this->getKonyveloID(),
               ];
               $conn->setData("INSERT INTO `konyvelesi_tetel`(`datum`, `tartozik`, `kovetel`, `osszeg`, `bank_szamlaszam`, `penztar_id`, `szamla_szamlaszam`, `egyeb_id`, `felhasznalo_id`) 
                         VALUES 
                         (:datum, :tartozik, :kovetel, :osszeg, :bank_szamlaszam, :penztar_id, :szamla_szamlaszam, :egyeb_id, :felhasznalo_id)", $parameters);
            }
            break;
         default:
            break;
      }
      return "siker";
   }

   public function invDuplicationCheck($conn)
   {
      $parameters = [":szamlaszam" => $this->getSzamlaszam()];
      $result = $conn->getData("SELECT szamlaszam FROM szamla WHERE szamlaszam=:szamlaszam", $parameters);
      if (count($result) > 0) {
         return true;
      }
      return false;
   }

   public function szamlaModositas($tipus)
    {
        switch ($tipus) {
            case 'szallito':
            case 'vevo':
            case 'penztar':
            case "penztar-szamlanelkul":
                $this->parameters = [
                    ":szamlaszam" => $this->getSzamlaszam(),
                    ":teljesites" => $this->getTeljesites(),
                    ":fizhat" => $this->getFizhat(),
                    ":kiallitas" => $this->getKiallitas(),
                    ":partner" => $this->getPartner(),
                    ":penztar" => $this->getPenztar(),
                    ":megjegyzes" => $this->getMegjegyzes(),
                    ":pdf" => $this->getPdf(),
                    ":fizetve" => $this->getFizetve(),
                    ":ceg_adoszam" => $_SESSION["cegadoszam"],
                    ":old_szamlaszam" => $this->getOldSzamlaszam()
                ];

                $this->connection->setData("UPDATE `szamla` 
                SET 
                `szamlaszam`=:szamlaszam,
                `teljesites`=:teljesites,
                `fizhat`=:fizhat,
                `kiallitas`=:kiallitas,
                `partner_adoszam`=:partner,
                `penztar`=:penztar,
                `megjegyzes`=:megjegyzes,
                `pdf`=:pdf,
                `fizetve`=:fizetve,
                `ceg_adoszam`=:ceg_adoszam 
                WHERE `szamlaszam` = :old_szamlaszam", $this->parameters);
                unset($this->parameters);
                foreach ($this->getKonyvelesiTetelek() as $konyvelesitetel_index => $konyvelesitetel) {
                    $this->parameters = [
                        ":datum" => $konyvelesitetel->getTeljesitesDatuma(),
                        ":tartozik" => $konyvelesitetel->getTartozik(),
                        ":kovetel" => $konyvelesitetel->getKovetel(),
                        ":osszeg" => $konyvelesitetel->getOsszeg(),
                        ":bank_szamlaszam" => null,
                        ":penztar_id" => null,
                        ":szamla_szamlaszam" => $this->getSzamlaszam(),
                        ":egyeb_id" => null,
                        ":felhasznalo_id" => $this->getKonyveloID(),
                        ":id" => $konyvelesitetel->getId()
                    ];

                    $this->connection->setData("UPDATE `konyvelesi_tetel` 
                    SET
                    `datum` = :datum,
                    `tartozik`= :tartozik,
                    `kovetel`= :kovetel,
                    `osszeg`= :osszeg,
                    `bank_szamlaszam`= :bank_szamlaszam,
                    `penztar_id`= :penztar_id,
                    `szamla_szamlaszam`= :szamla_szamlaszam,
                    `egyeb_id`= :egyeb_id,
                    `felhasznalo_id`= :felhasznalo_id
                     WHERE `id` = :id
                    ", $this->parameters);
                    unset($this->parameters);
                }
                break;
            case "only-penztar":
                $this->parameters = [
                    ":datum" => $this->getTeljesites(),
                    ":megjegyzes" => $this->getMegjegyzes(),
                    ":ceg_adoszam" => $_SESSION["cegadoszam"],
                    ":id" => $this->getOldSzamlaszam()
                ];
                $this->connection->setData("UPDATE `penztar` SET 
                `datum` = :datum, 
                `megjegyzes`=:megjegyzes, 
                `ceg_adoszam`=:ceg_adoszam 
                WHERE `id` = :id", $this->parameters);
                foreach ($this->getKonyvelesiTetelek() as $konyvelesitetelIndex => $konyvelesitetel) {
                    $this->parameters = [
                        ":datum" => $konyvelesitetel->getTeljesitesDatuma(),
                        ":tartozik" => $konyvelesitetel->getTartozik(),
                        ":kovetel" => $konyvelesitetel->getKovetel(),
                        ":osszeg" => $konyvelesitetel->getOsszeg(),
                        ":bank_szamlaszam" => null,
                        ":penztar_id" => $konyvelesitetel->getPenztarId(),
                        ":szamla_szamlaszam" => null,
                        ":egyeb_id" => null,
                        ":felhasznalo_id" => $this->getKonyveloID(),
                        ":id" => $konyvelesitetel->getId()
                    ];

                    $this->connection->setData("UPDATE `konyvelesi_tetel` 
                    SET
                    `datum` = :datum,
                    `tartozik`= :tartozik,
                    `kovetel`= :kovetel,
                    `osszeg`= :osszeg,
                    `bank_szamlaszam`= :bank_szamlaszam,
                    `penztar_id`= :penztar_id,
                    `szamla_szamlaszam`= :szamla_szamlaszam,
                    `egyeb_id`= :egyeb_id,
                    `felhasznalo_id`= :felhasznalo_id
                     WHERE `id` = :id
                    ", $this->parameters);
                    unset($this->parameters);
                }
                break;
            default:
                break;
        }
    }
}
