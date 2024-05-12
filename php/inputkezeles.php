<?php

namespace Gerke\Imagetotext;

session_start();

require('oop/Szamla.php');
require('oop/Bank.php');
require('oop/Egyeb.php');
require('oop/Konyvelesitetel.php');
require('oop/Targyieszkoz.php');
require('oop/Connection.php');

$hibaLista = array();
$connection = new Connection();
/**

 */
if (isset($_POST["data"])) {
    $form_adatok = $_POST["data"];
    if (isset($form_adatok[1])) {
        $szamlaadatok = $form_adatok[1];
    }
    if (isset($form_adatok[2])) {
        $konyvelesadatok = $form_adatok[2];
    }
    if (isset($form_adatok[3])) {
        $targyieszkozvalaszto = $form_adatok[3];
    }

    switch (true) {
        case (($_POST["tipus"] == 'szallito') || ($_POST["tipus"] == 'vevo')):

            $szamla = new Szamla();
            if ($szamlaadatok["szallito"] == "-1") {
                echo "partnerMegadasaKotelezo";
                break;
            }
            $szamla->setPartner($szamlaadatok["szallito"]);
            if (!isset($szamlaadatok["sorszam"]) || strlen($szamlaadatok["sorszam"]) == 0) {
                echo "szamlaSorszamMegadasaKotelezo";
                break;
            }
            $szamla->setSzamlaszam($szamlaadatok["sorszam"]);
            if (!isset($szamlaadatok["osszeg"]) || strlen($szamlaadatok["osszeg"]) == 0) {
                echo "szamlaOsszegMegadasaKotelezo";
                break;
            }
            $szamla->setOsszeg($szamlaadatok["osszeg"]);
            if (!isset($szamlaadatok["telj"]) || strlen($szamlaadatok["telj"]) == 0) {
                echo "szamlaTeljMegadasaKotelezo";
                break;
            }
            $szamla->setTeljesites($szamlaadatok["telj"]);
            if (!isset($szamlaadatok["kiall"]) || strlen($szamlaadatok["kiall"]) == 0) {
                echo "szamlaKiallMegadasaKotelezo";
                break;
            }
            $szamla->setKiallitas($szamlaadatok["kiall"]);
            if (!isset($szamlaadatok["fizhat"]) || strlen($szamlaadatok["fizhat"]) == 0) {
                echo "szamlaFizhatMegadasaKotelezo";
                break;
            }
            $szamla->setFizhat($szamlaadatok["fizhat"]);
            $szamla->setSzamlaTipus(0);
            if (isset($_SESSION["felhasznalo_id"])) {
                $szamla->setKonyveloID($_SESSION["felhasznalo_id"]);
            }
            if (isset($_SESSION["cegadoszam"])) {
                $szamla->setCegID($_SESSION["cegadoszam"]);
            }
            if (isset($szamlaadatok["megj"])) {
                $szamla->setMegjegyzes($szamlaadatok["megj"]);
            }
            $totalCount = 0;
            for ($i = 0; $i < (count($konyvelesadatok) / 3); $i++) {
                $count = 0;

                if (strlen($konyvelesadatok["tartozik_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["kovetel_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["osszeg_" . $i]) > 0) {
                    $count++;
                }

                if ($count == 3) {
                    $konyvelesi_tetel = new Konyvelesitetel($konyvelesadatok["tartozik_" . $i], $konyvelesadatok["kovetel_" . $i], $konyvelesadatok["osszeg_" . $i]);
                    $konyvelesi_tetel->setTeljesitesDatuma($szamla->getTeljesites());
                    $szamla->addKonyvelesiTetel($konyvelesi_tetel);
                    $totalCount += 4;
                }
                if ($count > 0 && $count < 3) {
                    echo "KonyvelesiTetelHIba_" . $i . "_";
                    break;
                }
            }
            switch ($_SESSION["szamla"]) {
                case 'all':
                    if (is_file($_POST["filename"])) {
                        $reginev = $_POST["filename"];
                        $ujnev =  "../invoices/" . $_SESSION["regType"] . "/" . $_SESSION["cegadoszam"] . "/" . str_replace("../invoices/" . $_SESSION["regType"] . "/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/", "", $reginev);
                        copy($reginev, $ujnev);
                        unlink($reginev);
                        if (is_file($ujnev)) {
                            $szamla->setPdf($ujnev);
                        } else {
                            echo "szamlaFeltolteseSikertelen";
                            break;
                        }
                    } else {
                        echo "szamlaNemTalalhato";
                        break;
                    }
                    break;
                case "none":
                    $szamla->setPdf(null);
                    break;
                default:
                    if (is_file($_SESSION["szamla"])) {
                        $reginev = $_SESSION["szamla"];
                        $ujnev =  "../invoices/" . $_SESSION["regType"] . "/" . $_SESSION["cegadoszam"] . "/" . str_replace("../invoices/" . $_SESSION["regType"] . "/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/", "", $reginev);
                        copy($reginev, $ujnev);
                        unlink($reginev);
                        if (is_file($ujnev)) {
                            $szamla->setPdf($ujnev);
                        } else {
                            echo "szamlaFeltolteseSikertelen";
                            break;
                        }
                    } else {
                        echo "szamlaNemTalalhato";
                        break;
                    }
                    break;
            }
            if ($totalCount != 0) {
                $result = $szamla->konyveles($_SESSION["regType"], $connection);
                if ($result === "siker") {
                    echo "siker";
                } else {
                    echo "szamlaDuplikacio";
                }
            } else {
                echo "mindenMezoUres";
            }

            break;
        case ($_POST["tipus"] == 'penztar'):
            $penztar_szamla = new Szamla();
            $penztar_szamla->setPenztar(1);
            $penztar_szamla->setFizetve(1);
            $penztar_szamla->setSzamlaTipus(2);
            if (isset($_SESSION["felhasznalo_id"])) {
                $penztar_szamla->setKonyveloID($_SESSION["felhasznalo_id"]);
            }
            if (isset($szamlaadatok["megj"])) {
                $penztar_szamla->setMegjegyzes($szamlaadatok["megj"]);
            }


            if (isset($szamlaadatok["csakPenztar"])) {
                $_SESSION["szamla"] = "only-penztar";
            }
            $hibaMiattLeptunkKi = 0;
            switch ($_SESSION["szamla"]) {
                case 'all':
                    /* több számla kiválasztva */
                    if ($szamlaadatok["szallito"] == "-1") {
                        echo "partnerMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setPartner($szamlaadatok["szallito"]);

                    if (!isset($szamlaadatok["sorszam"]) || strlen($szamlaadatok["sorszam"]) == 0) {
                        echo "szamlaSorszamMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setSzamlaszam($szamlaadatok["sorszam"]);

                    if (!isset($szamlaadatok["osszeg"]) || strlen($szamlaadatok["osszeg"]) == 0) {
                        echo "szamlaOsszegMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setOsszeg($szamlaadatok["osszeg"]);

                    if (!isset($szamlaadatok["telj"]) || strlen($szamlaadatok["telj"]) == 0) {
                        echo "szamlaTeljMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setTeljesites($szamlaadatok["telj"]);

                    if (!isset($szamlaadatok["kiall"]) || strlen($szamlaadatok["kiall"]) == 0) {
                        echo "szamlaKiallMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setKiallitas($szamlaadatok["kiall"]);

                    if (!isset($szamlaadatok["fizhat"]) || strlen($szamlaadatok["fizhat"]) == 0) {
                        echo "szamlaFizhatMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setFizhat($szamlaadatok["fizhat"]);

                    if (isset($_SESSION["cegadoszam"])) {
                        $penztar_szamla->setCegID($_SESSION["cegadoszam"]);
                    }
                    if (is_file($_POST["filename"])) {
                        $reginev = $_POST["filename"];
                        $ujnev =  "../invoices/" . $_SESSION["regType"] . "/" . $_SESSION["cegadoszam"] . "/" . str_replace("../invoices/" . $_SESSION["regType"] . "/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/", "", $reginev);
                        copy($reginev, $ujnev);
                        unlink($reginev);
                        if (is_file($ujnev)) {
                            $penztar_szamla->setPdf($ujnev);
                        } else {
                            echo "szamlaFeltolteseSikertelen";
                            $hibaMiattLeptunkKi = 1;
                            break;
                        }
                    } else {
                        echo "szamlaNemTalalhato";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    break;
                case 'none':
                    /* nincs számla kiválasztva */
                    if ($szamlaadatok["szallito"] == "-1") {
                        echo "patnerMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setPartner($szamlaadatok["szallito"]);

                    if (!isset($szamlaadatok["sorszam"]) || strlen($szamlaadatok["sorszam"]) == 0) {
                        echo "szamlaSorszamMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setSzamlaszam($szamlaadatok["sorszam"]);

                    if (!isset($szamlaadatok["osszeg"]) || strlen($szamlaadatok["osszeg"]) == 0) {
                        echo "szamlaOsszegMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setOsszeg($szamlaadatok["osszeg"]);

                    if (!isset($szamlaadatok["telj"]) || strlen($szamlaadatok["telj"]) == 0) {
                        echo "szamlaTeljMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setTeljesites($szamlaadatok["telj"]);

                    if (!isset($szamlaadatok["kiall"]) || strlen($szamlaadatok["kiall"]) == 0) {
                        echo "szamlaKiallMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setKiallitas($szamlaadatok["kiall"]);

                    if (!isset($szamlaadatok["fizhat"]) || strlen($szamlaadatok["fizhat"]) == 0) {
                        echo "szamlaFizhatMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setFizhat($szamlaadatok["fizhat"]);

                    $penztar_szamla->setFizetve(1);
                    if (isset($_SESSION["cegadoszam"])) {
                        $penztar_szamla->setCegID($_SESSION["cegadoszam"]);
                    }
                    $penztar_szamla->setPdf(null);
                    break;
                case "only-penztar":
                    /* csak pénztár rögzítése */
                    if (!isset($szamlaadatok["osszeg"]) || strlen($szamlaadatok["osszeg"]) == 0) {
                        echo "szamlaOsszegMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setOsszeg($szamlaadatok["osszeg"]);

                    if (!isset($szamlaadatok["telj"]) || strlen($szamlaadatok["telj"]) == 0) {
                        echo "szamlaTeljMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setTeljesites($szamlaadatok["telj"]);

                    $penztar_szamla->setPdf(null);
                    break;
                default:
                    /* 1 számla kiválasztva */
                    if ($szamlaadatok["szallito"] == "-1") {
                        echo "partnerMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setPartner($szamlaadatok["szallito"]);

                    if (!isset($szamlaadatok["sorszam"]) || strlen($szamlaadatok["sorszam"]) == 0) {
                        echo "szamlaSorszamMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setSzamlaszam($szamlaadatok["sorszam"]);

                    if (!isset($szamlaadatok["osszeg"]) || strlen($szamlaadatok["osszeg"]) == 0) {
                        echo "szamlaOsszegMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setOsszeg($szamlaadatok["osszeg"]);

                    if (!isset($szamlaadatok["telj"]) || strlen($szamlaadatok["telj"]) == 0) {
                        echo "szamlaTeljMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setTeljesites($szamlaadatok["telj"]);

                    if (!isset($szamlaadatok["kiall"]) || strlen($szamlaadatok["kiall"]) == 0) {
                        echo "szamlaKiallMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setKiallitas($szamlaadatok["kiall"]);

                    if (!isset($szamlaadatok["fizhat"]) || strlen($szamlaadatok["fizhat"]) == 0) {
                        echo "szamlaFizhatMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setFizhat($szamlaadatok["fizhat"]);

                    if (isset($_SESSION["cegadoszam"])) {
                        $penztar_szamla->setCegID($_SESSION["cegadoszam"]);
                    }
                    if (is_file($_SESSION["szamla"])) {
                        $reginev = $_SESSION["szamla"];
                        $ujnev =  "../invoices/" . $_SESSION["regType"] . "/" . $_SESSION["cegadoszam"] . "/" . str_replace("../invoices/" . $_SESSION["regType"] . "/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/", "", $reginev);
                        copy($reginev, $ujnev);
                        unlink($reginev);
                        if (is_file($ujnev)) {
                            $penztar_szamla->setPdf($ujnev);
                        } else {
                            echo "szamlaFeltolteseSikertelen";
                            $hibaMiattLeptunkKi = 1;
                            break;
                        }
                    } else {
                        echo "szamlaNemTalalhato";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    break;
            }
            if ($hibaMiattLeptunkKi == 1) {
                break;
            }
            $totalCount = 0;
            for ($i = 0; $i < (count($konyvelesadatok) / 3); $i++) {
                $count = 0;

                if (strlen($konyvelesadatok["tartozik_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["kovetel_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["osszeg_" . $i]) > 0) {
                    $count++;
                }
                if ($count == 3) {
                    $konyvelesi_tetel = new Konyvelesitetel($konyvelesadatok["tartozik_" . $i], $konyvelesadatok["kovetel_" . $i], $konyvelesadatok["osszeg_" . $i]);
                    $konyvelesi_tetel->setTeljesitesDatuma($penztar_szamla->getTeljesites());
                    $penztar_szamla->addKonyvelesiTetel($konyvelesi_tetel);
                    $totalCount += 4;
                }
                if ($count > 0 && $count < 3) {
                    echo "KonyvelesiTetelHIba_" . $i . "_";
                    break;
                }
            }

            if ($totalCount != 0) {
                switch ($_SESSION["szamla"]) {
                    case 'all':
                        $result = $penztar_szamla->konyveles("penztar", $connection);
                        break;
                    case 'none':
                        $result = $penztar_szamla->konyveles("penztar-szamlanelkul", $connection);
                        break;
                    case "only-penztar":
                        $result = $penztar_szamla->konyveles("only-penztar", $connection);
                        break;
                    default:
                        $result = $penztar_szamla->konyveles("penztar", $connection);
                        break;
                }
                if ($result === "siker") {
                    echo "siker";
                }
            } else {
                echo "mindenMezoUres";
            }
            break;
        case ($_POST["tipus"] == 'bank'):
            $bank = new Bank();
            if ($szamlaadatok["bankszamlaszam"] == "-1") {
                echo "bankszamlaNincsKivalasztva";
                break;
            }
            $bank->setBankszamlaSzam($szamlaadatok["bankszamlaszam"]);
            if (isset($_SESSION["cegadoszam"])) {
                $bank->setCegID($_SESSION["cegadoszam"]);
            }
            if (isset($_SESSION["felhasznalo_id"])) {
                $bank->setKonyveloID($_SESSION["felhasznalo_id"]);
            }

            $totalCount = 0;
            for ($i = 0; $i < (count($konyvelesadatok) / 5); $i++) {

                $count = 0;

                if (strlen($konyvelesadatok["tartozik_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["kovetel_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["osszeg_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["bank_datum_" . $i]) > 0) {
                    $count++;
                }

                if ($count == 4) {
                    $konyvelesi_tetel = new Konyvelesitetel($konyvelesadatok["tartozik_" . $i], $konyvelesadatok["kovetel_" . $i], $konyvelesadatok["osszeg_" . $i]);
                    $konyvelesi_tetel->setBankSzamlaszam($szamlaadatok["bankszamlaszam"]);
                    if (isset($konyvelesadatok["szamlak_" . $i]) && $konyvelesadatok["szamlak_" . $i] != "-1") {
                        $konyvelesi_tetel->setSzamlaSzamlaszam($konyvelesadatok["szamlak_" . $i]);
                    } else {
                        $konyvelesi_tetel->setSzamlaSzamlaszam(NULL);
                    }
                    $konyvelesi_tetel->setTeljesitesDatuma($konyvelesadatok["bank_datum_" . $i]);
                    $bank->addKonyvelesiTetel($konyvelesi_tetel);
                    $totalCount += 4;
                }
                if ($count > 0 && $count < 4) {
                    echo "bankKonyvelesiTetelHIba_" . $i . "_";
                    break;
                }
            }

            if ($totalCount != 0) {
                $result = $bank->konyveles("bank", $connection);
                if ($result === "siker") {
                    echo "siker";
                }
            } else {
                echo "bankMindenMezoUres";
            }

            break;
        case ($_POST["tipus"] == 'egyeb'):
            $egyeb = new Egyeb();
            if (!isset($szamlaadatok["megnevezes"]) || strlen($szamlaadatok["megnevezes"]) == 0) {
                echo "egyebMegnevezesNemLehetUres";
                break;
            }
            $egyeb->setMegnevezes($szamlaadatok["megnevezes"]);

            $egyeb->setMegjegyzes($szamlaadatok["megjegyzes"]);

            if (!isset($szamlaadatok["datum"]) || strlen($szamlaadatok["datum"]) == 0) {
                echo "egyebDatumNemLehetUres";
                break;
            }
            $egyeb->setDatum($szamlaadatok["datum"]);
            if (isset($_SESSION["cegadoszam"])) {
                $egyeb->setCegID($_SESSION["cegadoszam"]);
            }
            if (isset($_SESSION["felhasznalo_id"])) {
                $egyeb->setKonyveloID($_SESSION["felhasznalo_id"]);
            }
            $totalCount = 0;
            for ($i = 0; $i < (count($konyvelesadatok) / 3); $i++) {
                $count = 0;

                if (strlen($konyvelesadatok["tartozik_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["kovetel_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["osszeg_" . $i]) > 0) {
                    $count++;
                }
                if ($count == 3) {
                    $konyvelesi_tetel = new Konyvelesitetel($konyvelesadatok["tartozik_" . $i], $konyvelesadatok["kovetel_" . $i], $konyvelesadatok["osszeg_" . $i]);
                    $konyvelesi_tetel->setTeljesitesDatuma($egyeb->getDatum());
                    $egyeb->addKonyvelesiTetel($konyvelesi_tetel);
                    $totalCount += 4;
                }
                if ($count > 0 && $count < 3) {
                    echo "egyebKonyvelesiTetelHIba_" . $i . "_";
                    break;
                }
            }
            if ($totalCount != 0) {
                $result = $egyeb->konyveles("egyeb", $connection);
                if ($result === "siker") {
                    echo "siker";
                }
            } else {
                echo "egyebMindenMezoUres";
            }
            break;
        case ($_POST["tipus"] == 'targyieszkoz'):
            if (!isset($targyieszkozvalaszto)) {
                /* Tárgyieszköz rögzítés */
                $uj_targyi_eszkozok = array();
                if ($szamlaadatok["megnevezes_0"] == "" && $szamlaadatok["ertek_0"] == "" && $szamlaadatok["h_ido_0"] == "" && $szamlaadatok["szamla_0"] == "Válasszon" && $szamlaadatok["megjegyzes_0"] == "eszköz") {
                    echo "targyieszkozMindenMezoUres";
                    break;
                }
                $hiba = "";
                for ($i = 0; $i < (count($szamlaadatok) / 5); $i++) {
                    if (!isset($szamlaadatok["megnevezes_" . $i]) || strlen($szamlaadatok["megnevezes_" . $i]) == 0) {
                        $hiba = "targyieszkozMegnevezesNemLehetUres";
                        break;
                    }
                    if (!isset($szamlaadatok["ertek_" . $i]) || strlen($szamlaadatok["ertek_" . $i]) == 0) {
                        $hiba = "targyieszkozErtekNemLehetUres";
                        break;
                    }
                    if (!isset($szamlaadatok["h_ido_" . $i]) || strlen($szamlaadatok["h_ido_" . $i]) == 0) {
                        $hiba = "targyieszkozHIdoNemLehetUres";
                        break;
                    }
                    if (!isset($szamlaadatok["szamla_" . $i]) || strlen($szamlaadatok["szamla_" . $i]) == 0 || $szamlaadatok["szamla_" . $i] == "Válasszon") {
                        $hiba = "targyieszkozSzamladoNemLehetUres";
                        break;
                    }
                }
                if (strlen($hiba) > 0) {
                    echo $hiba;
                    break;
                }
                /* $szamlaadatok */
                for ($i = 0; $i < (count($szamlaadatok) / 5); $i++) {
                    $targyieszkoz = new Targyieszkoz();
                    $targyieszkoz->setMegnevezes($szamlaadatok["megnevezes_" . $i]);
                    $targyieszkoz->setErtek($szamlaadatok["ertek_" . $i]);
                    $targyieszkoz->setHasznalatiIdo($szamlaadatok["h_ido_" . $i]);
                    $targyieszkoz->setSzamla($szamlaadatok["szamla_" . $i]);
                    if (strlen($szamlaadatok["megjegyzes_" . $i]) > 0) {
                        $targyieszkoz->setMegjegyzes($szamlaadatok["megjegyzes_" . $i]);
                    } else {
                        $targyieszkoz->setMegjegyzes("");
                    }
                    $uj_targyi_eszkozok[] = $targyieszkoz;
                }
            } else {
                /* Értéknövekedés rögzítés */
                $erteknovekedesek = array();
                if ($konyvelesadatok["targyieszkoz_0"] == "-1" && $konyvelesadatok["targyieszk_erteknov_0"] == "" &&  $konyvelesadatok["targyieszk_erteknov_szamla_0"] == "-1") {
                    echo "targyieszkozErtekNovekedesMindenMezoUres";
                    break;
                }
                $hiba = "";
                for ($i = 0; $i < (count($konyvelesadatok) / 4); $i++) {
                    if (!isset($konyvelesadatok["targyieszkoz_" . $i]) || strlen($konyvelesadatok["targyieszkoz_" . $i]) == 0 || $konyvelesadatok["targyieszkoz_" . $i] == "-1") {
                        $hiba = "targyieszkozValasztasNemLehetUres";
                        break;
                    }
                    if (!isset($konyvelesadatok["targyieszk_erteknov_" . $i]) || strlen($konyvelesadatok["targyieszk_erteknov_" . $i]) == 0) {
                        $hiba =  "targyieszkozErteknovOsszegNemLehetUres";
                        break;
                    }
                    if (!isset($konyvelesadatok["targyieszk_erteknov_szamla_" . $i]) || strlen($konyvelesadatok["targyieszk_erteknov_szamla_" . $i]) == 0 || $konyvelesadatok["targyieszk_erteknov_szamla_" . $i] == "-1") {
                        $hiba = "targyieszkozErteknovSzamlaNemLehetUres";
                        break;
                    }
                }
                if (strlen($hiba) > 0) {
                    echo $hiba;
                    break;
                }
                for ($i = 0; $i < (count($konyvelesadatok) / 4); $i++) {

                    $targyieszkozErteknovnek = new Targyieszkoz();
                    $targyieszkozErteknovnek->setMegnevezes($konyvelesadatok["targyieszkoz_" . $i]);
                    $targyieszkozErteknovnek->setErteknovekedes($konyvelesadatok["targyieszk_erteknov_" . $i]);
                    $targyieszkozErteknovnek->setErteknovSzamla($konyvelesadatok["targyieszk_erteknov_szamla_" . $i]);

                    if (strlen($konyvelesadatok["targyieszk_erteknov_megjegyzes_" . $i]) > 0) {
                        $targyieszkozErteknovnek->setErteknovMegjegyzes($konyvelesadatok["targyieszk_erteknov_megjegyzes_" . $i]);
                    }
                    $erteknovekedesek[] = $targyieszkozErteknovnek;
                }
            }

            if (isset($uj_targyi_eszkozok) && count($uj_targyi_eszkozok) > 0) {
                $targyieszkoz->targyieszkozrogzites($uj_targyi_eszkozok);
                echo "siker";
            }

            if (isset($erteknovekedesek) && count($erteknovekedesek) > 0) {
                $targyieszkoz = new Targyieszkoz();
                $targyieszkoz->targyieszkozBekerulesiErtekNoveles($erteknovekedesek);
                echo "siker";
            }
            break;
        case ($_POST["tipus"] == 'amortizacio'):
            $targyieszkoz = new Targyieszkoz();
            $egyeb = new Egyeb();

            if (!isset($szamlaadatok["datum"]) || strlen($szamlaadatok["datum"]) == 0) {
                echo "amortizacioDatumNemLehetUres";
                break;
            }
            $egyeb->setDatum($szamlaadatok["datum"]);


            if (!isset($szamlaadatok["megnevezes"]) || strlen($szamlaadatok["megnevezes"]) == 0 || $szamlaadatok["megnevezes"] == "-1") {
                echo "amortizacioMegnevezesNemLehetUres";
                break;
            }
            $targyieszkoz->setMegnevezes($szamlaadatok["megnevezes"]);
            $egyeb->setMegnevezes($szamlaadatok["megnevezes"]);




            $egyeb->setMegjegyzes($egyeb->getDatum() . "_" . $egyeb->getMegnevezes());
            if (isset($_SESSION["cegadoszam"])) {
                $egyeb->setCegID($_SESSION["cegadoszam"]);
            }
            if (isset($_SESSION["felhasznalo_id"])) {
                $egyeb->setKonyveloID($_SESSION["felhasznalo_id"]);
            }
            $totalCount = 0;
            for ($i = 0; $i < (count($konyvelesadatok) / 3); $i++) {
                $count = 0;

                if (strlen($konyvelesadatok["tartozik_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["kovetel_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["osszeg_" . $i]) > 0) {
                    $count++;
                }
                if ($count == 3) {
                    $konyvelesi_tetel = new Konyvelesitetel($konyvelesadatok["tartozik_" . $i], $konyvelesadatok["kovetel_" . $i], $konyvelesadatok["osszeg_" . $i]);
                    $konyvelesi_tetel->setTeljesitesDatuma($egyeb->getDatum());
                    $egyeb->addKonyvelesiTetel($konyvelesi_tetel);
                    $targyieszkoz->addErtekcsokkenes($konyvelesadatok["osszeg_" . $i]);
                    $totalCount += 4;
                }
                if ($count > 0 && $count < 3) {
                    echo "amortizacioKonyvelesiTetelHIba_" . $i . "_";
                    break;
                }
            }
            if ($totalCount != 0) {
                $result = $egyeb->konyveles("egyeb", $connection);
                if ($result === "siker") {
                    $targyieszkoz->amortizacio($targyieszkoz);
                    echo "siker";
                }
            } else {
                echo "amortizacioMindenMezoUres";
            }
            break;
        case ($_POST["tipus"] == 'ksablonmentes'):
            $form_adatok;
            $form_adatok[":felhasznalo_id"] = $_SESSION["felhasznalo_id"];
            $connection->setData("INSERT INTO `sablon`(`megnevezes`, `tartozik`, `kovetel`, `felhasznalo_id`) 
            VALUES 
            (:nev, :tartozik, :kovetel, :felhasznalo_id)", $form_adatok);
            echo "siker";
            break;
        default:
            break;
    }
}
/**
 * Számlák módosítása adatellenőrzés után
 */
if (isset($_POST["dataForMod"])) {
    $form_adatok = $_POST["dataForMod"];
    if (isset($form_adatok[1])) {
        $szamlaadatok = $form_adatok[1];
    }
    if (isset($form_adatok[2])) {
        $konyvelesadatok = $form_adatok[2];
    }
    switch ($_POST["tipus"]) {
        case 'szallito':
            $szamla = new Szamla();
            if ($szamlaadatok["szallito"] == "-1") {
                echo "szallitoMegadasaKotelezo";
                break;
            }
            $szamla->setPartner($szamlaadatok["szallito"]);
            $szamla->setOldSzamlaszam($szamlaadatok["old_sorszam"]);
            $szamla->setPdf($szamlaadatok["pdf"]);
            if (!isset($szamlaadatok["sorszam"]) || strlen($szamlaadatok["sorszam"]) == 0) {
                echo "szamlaSorszamMegadasaKotelezo";
                break;
            }
            $szamla->setSzamlaszam($szamlaadatok["sorszam"]);
            if (!isset($szamlaadatok["osszeg"]) || strlen($szamlaadatok["osszeg"]) == 0) {
                echo "szamlaOsszegMegadasaKotelezo";
                break;
            }
            $szamla->setOsszeg($szamlaadatok["osszeg"]);
            if (!isset($szamlaadatok["telj"]) || strlen($szamlaadatok["telj"]) == 0) {
                echo "szamlaTeljMegadasaKotelezo";
                break;
            }
            $szamla->setTeljesites($szamlaadatok["telj"]);
            if (!isset($szamlaadatok["kiall"]) || strlen($szamlaadatok["kiall"]) == 0) {
                echo "szamlaKiallMegadasaKotelezo";
                break;
            }
            $szamla->setKiallitas($szamlaadatok["kiall"]);
            if (!isset($szamlaadatok["fizhat"]) || strlen($szamlaadatok["fizhat"]) == 0) {
                echo "szamlaFizhatMegadasaKotelezo";
                break;
            }
            $szamla->setFizhat($szamlaadatok["fizhat"]);
            $szamla->setSzamlaTipus(0);
            if (isset($_SESSION["felhasznalo_id"])) {
                $szamla->setKonyveloID($_SESSION["felhasznalo_id"]);
            }
            if (isset($_SESSION["cegadoszam"])) {
                $szamla->setCegID($_SESSION["cegadoszam"]);
            }
            if (isset($szamlaadatok["megj"])) {
                $szamla->setMegjegyzes($szamlaadatok["megj"]);
            }

            $totalCount = 0;
            for ($i = 0; $i < (count($konyvelesadatok) / 4); $i++) {
                $count = 0;

                if (strlen($konyvelesadatok["tartozik_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["kovetel_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["osszeg_" . $i]) > 0) {
                    $count++;
                }

                if ($count == 3) {
                    $konyvelesi_tetel = new Konyvelesitetel($konyvelesadatok["tartozik_" . $i], $konyvelesadatok["kovetel_" . $i], $konyvelesadatok["osszeg_" . $i]);
                    $konyvelesi_tetel->setTeljesitesDatuma($szamla->getTeljesites());
                    $konyvelesi_tetel->setId($konyvelesadatok["kt_" . $i]);
                    $szamla->addKonyvelesiTetel($konyvelesi_tetel);
                    $totalCount += 4;
                }
                if ($count > 0 && $count < 3) {
                    echo "szallitoKonyvelesiTetelHIba_" . $i . "_";
                    break;
                }
            }
            if ($totalCount != 0) {
                $szamla->szamlaModositas("szallito");
                echo "siker";
            } else {
                echo "szallitoMindenMezoUres";
            }

            break;
        case "vevo":
            $szamla = new Szamla();
            if ($szamlaadatok["szallito"] == "-1") {
                echo "szallitoMegadasaKotelezo";
                break;
            }
            $szamla->setPartner($szamlaadatok["szallito"]);
            $szamla->setOldSzamlaszam($szamlaadatok["old_sorszam"]);
            $szamla->setPdf($szamlaadatok["pdf"]);
            if (!isset($szamlaadatok["sorszam"]) || strlen($szamlaadatok["sorszam"]) == 0) {
                echo "szamlaSorszamMegadasaKotelezo";
                break;
            }
            $szamla->setSzamlaszam($szamlaadatok["sorszam"]);
            if (!isset($szamlaadatok["osszeg"]) || strlen($szamlaadatok["osszeg"]) == 0) {
                echo "szamlaOsszegMegadasaKotelezo";
                break;
            }
            $szamla->setOsszeg($szamlaadatok["osszeg"]);
            if (!isset($szamlaadatok["telj"]) || strlen($szamlaadatok["telj"]) == 0) {
                echo "szamlaTeljMegadasaKotelezo";
                break;
            }
            $szamla->setTeljesites($szamlaadatok["telj"]);
            if (!isset($szamlaadatok["kiall"]) || strlen($szamlaadatok["kiall"]) == 0) {
                echo "szamlaKiallMegadasaKotelezo";
                break;
            }
            $szamla->setKiallitas($szamlaadatok["kiall"]);
            if (!isset($szamlaadatok["fizhat"]) || strlen($szamlaadatok["fizhat"]) == 0) {
                echo "szamlaFizhatMegadasaKotelezo";
                break;
            }
            $szamla->setFizhat($szamlaadatok["fizhat"]);
            $szamla->setSzamlaTipus(0);
            if (isset($_SESSION["felhasznalo_id"])) {
                $szamla->setKonyveloID($_SESSION["felhasznalo_id"]);
            }
            if (isset($_SESSION["cegadoszam"])) {
                $szamla->setCegID($_SESSION["cegadoszam"]);
            }
            if (isset($szamlaadatok["megj"])) {
                $szamla->setMegjegyzes($szamlaadatok["megj"]);
            }

            $totalCount = 0;
            for ($i = 0; $i < (count($konyvelesadatok) / 4); $i++) {
                $count = 0;

                if (strlen($konyvelesadatok["tartozik_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["kovetel_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["osszeg_" . $i]) > 0) {
                    $count++;
                }

                if ($count == 3) {
                    $konyvelesi_tetel = new Konyvelesitetel($konyvelesadatok["tartozik_" . $i], $konyvelesadatok["kovetel_" . $i], $konyvelesadatok["osszeg_" . $i]);
                    $konyvelesi_tetel->setTeljesitesDatuma($szamla->getTeljesites());
                    $konyvelesi_tetel->setId($konyvelesadatok["kt_" . $i]);
                    $szamla->addKonyvelesiTetel($konyvelesi_tetel);
                    $totalCount += 4;
                }
                if ($count > 0 && $count < 3) {
                    echo "szallitoKonyvelesiTetelHIba_" . $i . "_";
                    break;
                }
            }
            if ($totalCount != 0) {
                $szamla->szamlaModositas("vevo");
                echo "siker";
            } else {
                echo "szallitoMindenMezoUres";
            }

            break;
        case "penztarszamla":
        case "penztar":
            $penztar_szamla = new Szamla();
            $penztar_szamla->setPenztar(1);
            $penztar_szamla->setSzamlaTipus(2);
            if (isset($_SESSION["felhasznalo_id"])) {
                $penztar_szamla->setKonyveloID($_SESSION["felhasznalo_id"]);
            }
            if (isset($szamlaadatok["megj"])) {
                $penztar_szamla->setMegjegyzes($szamlaadatok["megj"]);
            }


            if (isset($szamlaadatok["csakPenztar"])) {
                $_SESSION["szamla"] = "only-penztar";
            }
            $hibaMiattLeptunkKi = 0;
            switch ($_SESSION["szamla"]) {
                case 'all':
                    /* több számla kiválasztva */
                    if ($szamlaadatok["szallito"] == "-1") {
                        echo "partnerMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setPartner($szamlaadatok["szallito"]);

                    if (!isset($szamlaadatok["sorszam"]) || strlen($szamlaadatok["sorszam"]) == 0) {
                        echo "szamlaSorszamMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setSzamlaszam($szamlaadatok["sorszam"]);
                    $penztar_szamla->setOldSzamlaszam($szamlaadatok["old_sorszam"]);
                    if (!isset($szamlaadatok["osszeg"]) || strlen($szamlaadatok["osszeg"]) == 0) {
                        echo "szamlaOsszegMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setOsszeg($szamlaadatok["osszeg"]);

                    if (!isset($szamlaadatok["telj"]) || strlen($szamlaadatok["telj"]) == 0) {
                        echo "szamlaTeljMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setTeljesites($szamlaadatok["telj"]);

                    if (!isset($szamlaadatok["kiall"]) || strlen($szamlaadatok["kiall"]) == 0) {
                        echo "szamlaKiallMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setKiallitas($szamlaadatok["kiall"]);

                    if (!isset($szamlaadatok["fizhat"]) || strlen($szamlaadatok["fizhat"]) == 0) {
                        echo "szamlaFizhatMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setFizhat($szamlaadatok["fizhat"]);

                    if (isset($_SESSION["cegadoszam"])) {
                        $penztar_szamla->setCegID($_SESSION["cegadoszam"]);
                    }
                    $penztar_szamla->setPdf($szamlaadatok["pdf"]);
                    break;
                case 'none':
                    /* nincs számla kiválasztva */
                    if ($szamlaadatok["szallito"] == "-1") {
                        echo "patnerMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setPartner($szamlaadatok["szallito"]);

                    if (!isset($szamlaadatok["sorszam"]) || strlen($szamlaadatok["sorszam"]) == 0) {
                        echo "szamlaSorszamMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setSzamlaszam($szamlaadatok["sorszam"]);
                    $penztar_szamla->setOldSzamlaszam($szamlaadatok["old_sorszam"]);
                    if (!isset($szamlaadatok["osszeg"]) || strlen($szamlaadatok["osszeg"]) == 0) {
                        echo "szamlaOsszegMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setOsszeg($szamlaadatok["osszeg"]);

                    if (!isset($szamlaadatok["telj"]) || strlen($szamlaadatok["telj"]) == 0) {
                        echo "szamlaTeljMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setTeljesites($szamlaadatok["telj"]);

                    if (!isset($szamlaadatok["kiall"]) || strlen($szamlaadatok["kiall"]) == 0) {
                        echo "szamlaKiallMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setKiallitas($szamlaadatok["kiall"]);

                    if (!isset($szamlaadatok["fizhat"]) || strlen($szamlaadatok["fizhat"]) == 0) {
                        echo "szamlaFizhatMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setFizhat($szamlaadatok["fizhat"]);

                    $penztar_szamla->setFizetve(1);
                    if (isset($_SESSION["cegadoszam"])) {
                        $penztar_szamla->setCegID($_SESSION["cegadoszam"]);
                    }
                    $penztar_szamla->setPdf(null);
                    break;
                case "only-penztar":
                    /* csak pénztár rögzítése */
                    $penztar_szamla->setOldSzamlaszam($szamlaadatok["old_sorszam"]);
                    if (!isset($szamlaadatok["osszeg"]) || strlen($szamlaadatok["osszeg"]) == 0) {
                        echo "szamlaOsszegMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setOsszeg($szamlaadatok["osszeg"]);

                    if (!isset($szamlaadatok["telj"]) || strlen($szamlaadatok["telj"]) == 0) {
                        echo "szamlaTeljMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setTeljesites($szamlaadatok["telj"]);

                    $penztar_szamla->setPdf(null);
                    break;
                default:
                    /* 1 számla kiválasztva */
                    if ($szamlaadatok["szallito"] == "-1") {
                        echo "partnerMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setPartner($szamlaadatok["szallito"]);

                    if (!isset($szamlaadatok["sorszam"]) || strlen($szamlaadatok["sorszam"]) == 0) {
                        echo "szamlaSorszamMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setSzamlaszam($szamlaadatok["sorszam"]);
                    $penztar_szamla->setOldSzamlaszam($szamlaadatok["old_sorszam"]);
                    $penztar_szamla->setPdf($szamlaadatok["pdf"]);
                    if (!isset($szamlaadatok["osszeg"]) || strlen($szamlaadatok["osszeg"]) == 0) {
                        echo "szamlaOsszegMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setOsszeg($szamlaadatok["osszeg"]);

                    if (!isset($szamlaadatok["telj"]) || strlen($szamlaadatok["telj"]) == 0) {
                        echo "szamlaTeljMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setTeljesites($szamlaadatok["telj"]);

                    if (!isset($szamlaadatok["kiall"]) || strlen($szamlaadatok["kiall"]) == 0) {
                        echo "szamlaKiallMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setKiallitas($szamlaadatok["kiall"]);

                    if (!isset($szamlaadatok["fizhat"]) || strlen($szamlaadatok["fizhat"]) == 0) {
                        echo "szamlaFizhatMegadasaKotelezo";
                        $hibaMiattLeptunkKi = 1;
                        break;
                    }
                    $penztar_szamla->setFizhat($szamlaadatok["fizhat"]);

                    if (isset($_SESSION["cegadoszam"])) {
                        $penztar_szamla->setCegID($_SESSION["cegadoszam"]);
                    }

                    //$penztar_szamla->setPdf("../invoices/penztar/" . $_SESSION["szamla"] . ".pdf");
                    break;
            }
            if ($hibaMiattLeptunkKi == 1) {
                break;
            }
            $totalCount = 0;

            for ($i = 0; $i < (count($konyvelesadatok) / 4); $i++) {
                $count = 0;

                if (strlen($konyvelesadatok["tartozik_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["kovetel_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["osszeg_" . $i]) > 0) {
                    $count++;
                }
                if ($count == 3) {
                    $konyvelesi_tetel = new Konyvelesitetel($konyvelesadatok["tartozik_" . $i], $konyvelesadatok["kovetel_" . $i], $konyvelesadatok["osszeg_" . $i]);
                    $konyvelesi_tetel->setId($konyvelesadatok["kt_" . $i]);
                    $konyvelesi_tetel->setTeljesitesDatuma($penztar_szamla->getTeljesites());
                    $konyvelesi_tetel->setPenztarId($penztar_szamla->getOldSzamlaszam());
                    $penztar_szamla->addKonyvelesiTetel($konyvelesi_tetel);
                    $totalCount += 4;
                }
                if ($count > 0 && $count < 3) {
                    echo "KonyvelesiTetelHIba_" . $i . "_";
                    break;
                }
            }

            if ($totalCount != 0) {
                switch ($_SESSION["szamla"]) {
                    case 'all':
                        $szamla->szamlaModositas("penztar");
                        break;
                    case 'none':
                        $szamla->szamlaModositas("penztar-szamlanelkul");
                        break;
                    case "only-penztar":
                        $szamla->szamlaModositas("only-penztar");
                        break;
                    default:
                        $szamla->szamlaModositas("penztar");
                        break;
                }
                echo "siker";
            } else {
                echo "mindenMezoUres";
            }
            break;
        case "bank":
            $bank = new Bank();
            if ($szamlaadatok["bankszamlaszam"] == "-1") {
                echo "bankszamlaNincsKivalasztva";
                break;
            }
            $bank->setBankszamlaSzam($szamlaadatok["bankszamlaszam"]);
            if (isset($_SESSION["cegadoszam"])) {
                $bank->setCegID($_SESSION["cegadoszam"]);
            }
            if (isset($_SESSION["felhasznalo_id"])) {
                $bank->setKonyveloID($_SESSION["felhasznalo_id"]);
            }

            $totalCount = 0;
            for ($i = 0; $i < (count($konyvelesadatok) / 5); $i++) {

                $count = 0;

                if (strlen($konyvelesadatok["tartozik_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["kovetel_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["osszeg_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["bank_datum_" . $i]) > 0) {
                    $count++;
                }

                if ($count == 4) {
                    $konyvelesi_tetel = new Konyvelesitetel($konyvelesadatok["tartozik_" . $i], $konyvelesadatok["kovetel_" . $i], $konyvelesadatok["osszeg_" . $i]);
                    $konyvelesi_tetel->setBankSzamlaszam($szamlaadatok["bankszamlaszam"]);
                    if (isset($konyvelesadatok["szamlak_" . $i]) && $konyvelesadatok["szamlak_" . $i] != "-1") {
                        $konyvelesi_tetel->setSzamlaSzamlaszam($konyvelesadatok["szamlak_" . $i]);
                    } else {
                        $konyvelesi_tetel->setSzamlaSzamlaszam(NULL);
                    }
                    $konyvelesi_tetel->setTeljesitesDatuma($konyvelesadatok["bank_datum_" . $i]);
                    $konyvelesi_tetel->setId($szamlaadatok["bank_id"]);
                    $bank->addKonyvelesiTetel($konyvelesi_tetel);
                    $totalCount += 4;
                }
                if ($count > 0 && $count < 4) {
                    echo "bankKonyvelesiTetelHIba_" . $i . "_";
                    break;
                }
            }

            if ($totalCount != 0) {
                $bank->bankModositasKonyveles("bank");
                echo "siker";
            } else {
                echo "bankMindenMezoUres";
            }
            break;
        case "egyeb":
            /* kiiras($szamlaadatok); */
            $egyeb = new Egyeb();
            $egyeb->setId($szamlaadatok["egyeb_id"]);
            if (!isset($szamlaadatok["megnevezes"]) || strlen($szamlaadatok["megnevezes"]) == 0) {
                echo "egyebMegnevezesNemLehetUres";
                break;
            }
            $egyeb->setMegnevezes($szamlaadatok["megnevezes"]);

            $egyeb->setMegjegyzes($szamlaadatok["megjegyzes"]);

            if (!isset($szamlaadatok["datum"]) || strlen($szamlaadatok["datum"]) == 0) {
                echo "egyebDatumNemLehetUres";
                break;
            }
            $egyeb->setDatum($szamlaadatok["datum"]);
            if (isset($_SESSION["cegadoszam"])) {
                $egyeb->setCegID($_SESSION["cegadoszam"]);
            }
            if (isset($_SESSION["felhasznalo_id"])) {
                $egyeb->setKonyveloID($_SESSION["felhasznalo_id"]);
            }
            $totalCount = 0;
            for ($i = 0; $i < (count($konyvelesadatok) / 4); $i++) {
                $count = 0;

                if (strlen($konyvelesadatok["tartozik_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["kovetel_" . $i]) > 0) {
                    $count++;
                }

                if (strlen($konyvelesadatok["osszeg_" . $i]) > 0) {
                    $count++;
                }
                if ($count == 3) {
                    $konyvelesi_tetel = new Konyvelesitetel($konyvelesadatok["tartozik_" . $i], $konyvelesadatok["kovetel_" . $i], $konyvelesadatok["osszeg_" . $i]);
                    $konyvelesi_tetel->setId($konyvelesadatok["kt_" . $i]);
                    $konyvelesi_tetel->setTeljesitesDatuma($egyeb->getDatum());
                    $egyeb->addKonyvelesiTetel($konyvelesi_tetel);
                    $totalCount += 4;
                }
                if ($count > 0 && $count < 3) {
                    echo "egyebKonyvelesiTetelHIba_" . $i . "_";
                    break;
                }
            }
            if ($totalCount != 0) {
                $egyeb->egyebModositasKonyveles("egyeb");
                echo "siker";
            } else {
                echo "egyebMindenMezoUres";
            }
            break;
        default:
            break;
    }
}

/**
 * Könyvelési sablon lekérése adatbázisból
 * Ezek a felhasználó által mentett sablonok
 */
if (isset($_POST["sablon"])) {
    if ($_POST["sablon"] != -1) {
        $params = [":megnevezes" => $_POST["sablon"]];
        $result = $connection->getData("SELECT * FROM sablon WHERE megnevezes = :megnevezes;", $params);
        $tmp["tartozik"] = $result[0]["tartozik"];
        $tmp["kovetel"] = $result[0]["kovetel"];
        echo json_encode($tmp);
    } else {
        echo json_encode("no");
    }
}

/**
 * Új partner rögzítése az adatbázisba attól függően, hogy vevő, vagy szállító
 */
if (isset($_POST["partnernev"]) && isset($_POST["partneradoszam"])) {

    if (strlen($_POST["partneradoszam"]) != 0) {
        $tmp = explode("-", $_POST["partneradoszam"]);
        if ((!isset($tmp[0])) || (strlen($tmp[0]) != 8) || (!isset($tmp[1])) || (strlen($tmp[1]) != 1) || (!isset($tmp[2])) || (strlen($tmp[2]) != 2)) {
            echo "hibasadoszam";
            die();
        }
    } else {
        echo "adoszamhiany";
        die();
    }
    if ($_POST["vevo"] === "true") {
        $params = [
            ":nev" => $_POST["partnernev"],
            ":adoszam" => $_POST["partneradoszam"],
            ":vevo" => 1
        ];
    } else {
        $params = [
            ":nev" => $_POST["partnernev"],
            ":adoszam" => $_POST["partneradoszam"],
            ":vevo" => 0
        ];
    }
    $connection->setData("INSERT INTO `partner`(`adoszam`,`nev`,`vevo`) VALUES (:adoszam, :nev, :vevo)", $params);
}

/**
 * Új cég rögzítése adatellenőrzés után, akinek könyvelni fogunk
 */
if (isset($_POST["ujcegdata"])) {
    $ujcegadatok = $_POST["ujcegdata"][0];
    $ellenorzottCegadatok;
    if (strlen($ujcegadatok["cegnev"]) == 0) {
        echo "cegnevhiany";
        die();
    } else {
        $ellenorzottCegadatok["cegnev"] = $ujcegadatok["cegnev"];
    }
    if (strlen($ujcegadatok["cegadoszam"]) != 0) {
        $tmp = explode("-", $ujcegadatok["cegadoszam"]);
        if ((!isset($tmp[0])) || (strlen($tmp[0]) != 8) || (!isset($tmp[1])) || (strlen($tmp[1]) != 1) || (!isset($tmp[2])) || (strlen($tmp[2]) != 2)) {
            echo "ceghibasadoszam";
            die();
        } else {
            $ellenorzottCegadatok["cegadoszam"] = $ujcegadatok["cegadoszam"];
        }
    } else {
        echo "cegadoszamhiany";
        die();
    }
    if (strlen($ujcegadatok["cegelerhetoseg"]) == 0) {
        echo "cegelerhetoseghiba";
        die();
    } else {
        $ellenorzottCegadatok["cegelerhetoseg"] = $ujcegadatok["cegelerhetoseg"];
    }

    if (!isset($ujcegadatok["cegirsz"]) || strlen($ujcegadatok["cegirsz"]) != 4) {
        echo "cegirszhiba";
        die();
    } else {
        $ellenorzottCegadatok["cegirsz"] = $ujcegadatok["cegirsz"];
    }
    if (strlen($ujcegadatok["cegvaros"]) == 0) {
        echo "cegvaroshiba";
        die();
    } else {
        $ellenorzottCegadatok["cegvaros"] = $ujcegadatok["cegvaros"];
    }
    if (strlen($ujcegadatok["cegkozter"]) == 0) {
        echo "cegkozterhiba";
        die();
    } else {
        $ellenorzottCegadatok["cegkozter"] = $ujcegadatok["cegkozter"];
    }
    if ($ujcegadatok["cegkozterjelleg"] == -1) {
        echo "cegkozterjelleghiba";
        die();
    } else {
        $ellenorzottCegadatok["cegkozterjelleg"] = $ujcegadatok["cegkozterjelleg"];
    }
    if (strlen($ujcegadatok["ceghazszamepulet"]) == 0) {
        echo "ceghazszamepulethiba";
        die();
    } else {
        $ellenorzottCegadatok["ceghazszamepulet"] = $ujcegadatok["ceghazszamepulet"];
    }
    if (isset($ujcegadatok["havi"])) {
        $ellenorzottCegadatok["afa"] = "havi";
    }
    if (isset($ujcegadatok["negyedeves"])) {
        $ellenorzottCegadatok["afa"] = "negyedeves";
    }
    if (isset($ujcegadatok["eves"])) {
        $ellenorzottCegadatok["afa"] = "eves";
    }
    if (isset($ujcegadatok["mentes"])) {
        $ellenorzottCegadatok["afa"] = "mentes";
    }
    if (!isset($ujcegadatok["havi"]) && !isset($ujcegadatok["negyedeves"]) && !isset($ujcegadatok["eves"]) && !isset($ujcegadatok["mentes"])) {
        echo "cegafahiba";
        die();
    }
    if (!is_dir("../invoices/szallito/" . $ellenorzottCegadatok["cegadoszam"] . "/")) {
        mkdir("../invoices/szallito/" . $ellenorzottCegadatok["cegadoszam"] . "/");
        mkdir("../invoices/vevo/" . $ellenorzottCegadatok["cegadoszam"] . "/");
        mkdir("../invoices/penztar/" . $ellenorzottCegadatok["cegadoszam"] . "/");

        mkdir("../invoices/szallito/" . $ellenorzottCegadatok["cegadoszam"] . "/feldolgozasra_var/");
        mkdir("../invoices/vevo/" . $ellenorzottCegadatok["cegadoszam"] . "/feldolgozasra_var/");
        mkdir("../invoices/penztar/" . $ellenorzottCegadatok["cegadoszam"] . "/feldolgozasra_var/");

        mkdir("../data/" . $ellenorzottCegadatok["cegadoszam"] . "/");
        copy("../data/szamlatukor.json", "../data/" . $ellenorzottCegadatok["cegadoszam"] . "/" . $ellenorzottCegadatok["cegadoszam"] . "_szamlatukor.json");
        copy("../data/merleg_seged.json", "../data/" . $ellenorzottCegadatok["cegadoszam"] . "/" . $ellenorzottCegadatok["cegadoszam"] . "_merleg_seged.json");
        copy("../data/eredmenykimut_seged.json", "../data/" . $ellenorzottCegadatok["cegadoszam"] . "/" . $ellenorzottCegadatok["cegadoszam"] . "_eredmenykimut_seged.json");
    }
    if (isset($_POST["muvelet"]) && $_POST["muvelet"] == "mod") {
        $params = [
            ":nev" => $ellenorzottCegadatok["cegnev"],
            ":adoszam" => $ellenorzottCegadatok["cegadoszam"],
            ":elerhetoseg" => $ellenorzottCegadatok["cegelerhetoseg"],
            ":cim" => $ellenorzottCegadatok["cegirsz"] . "," . $ellenorzottCegadatok["cegvaros"] . "," . $ellenorzottCegadatok["cegkozter"] . " " . $ellenorzottCegadatok["cegkozterjelleg"] . " " . $ellenorzottCegadatok["ceghazszamepulet"],
            ":afabevallas" => $ellenorzottCegadatok["afa"],
            ":felhasznalo_id" => $_SESSION["felhasznalo_id"]
        ];
        $connection->setData("UPDATE `ceg` SET
        `nev` =:nev,
        `adoszam` =:adoszam,
        `elerhetoseg` =:elerhetoseg,
        `cim` =:cim,
        `afabevallas` =:afabevallas,
        `felhasznalo_id` =:felhasznalo_id
        WHERE 
        adoszam = :adoszam", $params);
        echo "siker";
    } else {
        $params = [
            ":nev" => $ellenorzottCegadatok["cegnev"],
            ":adoszam" => $ellenorzottCegadatok["cegadoszam"],
            ":elerhetoseg" => $ellenorzottCegadatok["cegelerhetoseg"],
            ":cim" => $ellenorzottCegadatok["cegirsz"] . "," . $ellenorzottCegadatok["cegvaros"] . "," . $ellenorzottCegadatok["cegkozter"] . " " . $ellenorzottCegadatok["cegkozterjelleg"] . " " . $ellenorzottCegadatok["ceghazszamepulet"],
            ":afabevallas" => $ellenorzottCegadatok["afa"],
            ":felhasznalo_id" => $_SESSION["felhasznalo_id"]
        ];
        $connection->setData("INSERT INTO `ceg`( `nev`, `adoszam`, `elerhetoseg`, `cim`, `afabevallas`, `felhasznalo_id`) VALUES (:nev, :adoszam, :elerhetoseg, :cim, :afabevallas, :felhasznalo_id)", $params);
        echo "siker";
    }
}

/**
 * Meglévő partner adatainak módosítása ellenőrzés után
 */
if (isset($_POST["partneradat"])) {
    //kiiras($_POST["partneradat"]);
    $adatok = $_POST["partneradat"];
    if (!isset($adatok[0]) || strlen($adatok[0]) == 0) {
        echo "nevMegadasKotelezo";
        die();
    }
    if (strlen($adatok[1]) != 0) {
        $tmp = explode("-", $adatok[1]);
        if ((!isset($tmp[0])) || (strlen($tmp[0]) != 8) || (!isset($tmp[1])) || (strlen($tmp[1]) != 1) || (!isset($tmp[2])) || (strlen($tmp[2]) != 2)) {
            echo "hibasadoszam";
            die();
        }
    } else {
        echo "adoszamhiany";
        die();
    }
    if ($adatok[2] == "vevo") {
        $adatok[2] = 1;
    } else {
        $adatok[2] = 0;
    }
    $param = [
        ":adoszam" => $adatok[1],
        ":nev" => $adatok[0],
        ":vevo" => $adatok[2],
        ":old_adoszam" => $adatok[3]
    ];
    $connection->setData("UPDATE `partner` SET `adoszam` = :adoszam, `nev` = :nev, `vevo` = :vevo WHERE `adoszam` = :old_adoszam", $param);
    echo "siker";
}

/**
 * Meglévő könyvelési sablonok adatainak módosítása ellenőrzés után
 */
if (isset($_POST["ksablon_modositas"])) {
    $adatok = $_POST["ksablon_modositas"];
    if (!isset($adatok[0]) || strlen($adatok[0]) == "") {
        echo "nevMegadasKotelezo";
        die();
    }
    if (strlen($adatok[1]) == 0) {
        echo "hibastartozik";
        die();
    }
    if (strlen($adatok[2]) == 0) {
        echo "hibaskovetel";
        die();
    }
    if ($adatok[1] == $adatok[2]) {
        echo "azonosszamok";
        die();
    }
    $param = [
        ":megnevezes" => $adatok[0],
        ":tartozik" => $adatok[1],
        ":kovetel" => $adatok[2],
        ":old_megnevezes" => $adatok[3],
        ":felhasznalo_id" => $_SESSION["felhasznalo_id"]
    ];
    $connection->setData("UPDATE sablon SET `megnevezes` = :megnevezes,`tartozik` = :tartozik, `kovetel` = :kovetel WHERE `megnevezes` = :old_megnevezes AND `felhasznalo_id` = :felhasznalo_id", $param);
    echo "siker";
}

/**
 * Partner törlése
 */
if (isset($_POST["partnertorles"])) {
    $params = [":adoszam" => $_POST["partnertorles"]];
    $connection->setData("DELETE FROM partner WHERE adoszam = :adoszam", $params);
    $id = $connection->getData("SELECT * FROM partner WHERE adoszam = :adoszam", $params);
    if (count($id) == 0) {
        echo "sikerestorles";
    } else {
        echo "sikertelentorles";
    }
}

/**
 * Könyvelési tétel sablon törlése
 */
if (isset($_POST["ksablon_torles"])) {
    $params = [":megnevezes" => $_POST["ksablon_torles"]];
    $connection->setData("DELETE FROM sablon WHERE megnevezes = :megnevezes", $params);
    $id = $connection->getData("SELECT * FROM sablon WHERE megnevezes = :megnevezes", $params);
    if (count($id) == 0) {
        echo "sikerestorles";
    } else {
        echo "sikertelentorles";
    }
}

/**
 * Új könyvelő hozzáadása a rendszerhez adatellenőrzéssel
 */
if (isset($_POST["ujfelhasznalodata"])) {

    $ujFelhasznaloadatok = $_POST["ujfelhasznalodata"][0];
    $ellenorzottFelhasznaloadatok;
    if (strlen($ujFelhasznaloadatok["nev"]) == 0) {
        echo "nevhiany";
        die();
    } else {
        $ellenorzottFelhasznaloadatok["nev"] = $ujFelhasznaloadatok["nev"];
        $ellenorzottFelhasznaloadatok["email"] = $ujFelhasznaloadatok["email"];
    }

    if (strlen($ujFelhasznaloadatok["jelszo"]) == 0) {
        echo "hianyzojelszo";
        die();
    } else {
        $ellenorzottFelhasznaloadatok["jelszo"] = $ujFelhasznaloadatok["jelszo"];
    }


    if (isset($ujFelhasznaloadatok["admin"])) {
        $ellenorzottFelhasznaloadatok["user_level"] = "1";
    } else {
        $ellenorzottFelhasznaloadatok["user_level"] = "0";
    }

    $params = [
        ":nev" => $ellenorzottFelhasznaloadatok["nev"],
        ":email" => $ellenorzottFelhasznaloadatok["email"],
        ":jelszo" => password_hash($ellenorzottFelhasznaloadatok["jelszo"], PASSWORD_DEFAULT),
        ":utolso_belepes" => "0000-00-00",
        ":user_level" => $ellenorzottFelhasznaloadatok["user_level"]
    ];
    $profilok = array();
    if (file_exists('../data/profile_information.json')) {
        $profilok = file_get_contents('../data/profile_information.json');
        $profilok = json_decode($profilok, true);
        $profilok[$ellenorzottFelhasznaloadatok["email"]] = "profile_1.jpg";
        $jsonString = json_encode($profilok, JSON_PRETTY_PRINT);
        $fp = fopen('../data/profile_information.json', 'w');
        fwrite($fp, $jsonString);
        $jsonString = "";
        fclose($fp);
    }
    $connection->setData("INSERT INTO `felhasznalo`( `nev`, `email`, `jelszo`, `utolso_belepes`, `admin`) VALUES (:nev, :email, :jelszo, :utolso_belepes, :user_level)", $params);

    echo "siker";
}

/**
 * Választott cég mérlegének elkészítése
 */
if (isset($_POST["merleg_formdata"])) {
    $ev = $_POST["merleg_formdata"]["ev"];
    $merleg = array();
    if (file_exists('../data/merleg_seged.json')) {
        $merleg = file_get_contents('../data/merleg_seged.json');
        $merleg = json_decode($merleg, true);
    }

    $start = $ev . "-01-01";
    $end = $ev . "-12-31";
    $params = [
        ":start_date" => $start,
        ":end_date" => $end,
        ":ceg_adoszam" => $_SESSION["cegadoszam"]
    ];
    $szamlak_merleg = $connection->getData("SELECT k.tartozik, k.kovetel, k.osszeg 
    FROM konyvelesi_tetel k
    INNER JOIN szamla sz ON k.szamla_szamlaszam = sz.szamlaszam 
    WHERE sz.ceg_adoszam = :ceg_adoszam AND k.datum BETWEEN :start_date AND :end_date", $params);

    $bank_merleg = $connection->getData("SELECT k.tartozik, k.kovetel, k.osszeg 
    FROM konyvelesi_tetel k
    INNER JOIN bank b ON k.bank_szamlaszam = b.bankszamlaszam 
    WHERE b.ceg_adoszam = :ceg_adoszam AND k.datum BETWEEN :start_date AND :end_date", $params);

    $penztar_merleg = $connection->getData("SELECT k.tartozik, k.kovetel, k.osszeg 
    FROM konyvelesi_tetel k
    INNER JOIN penztar p ON k.penztar_id = p.id 
    WHERE p.ceg_adoszam = :ceg_adoszam AND k.datum BETWEEN :start_date AND :end_date", $params);

    $egyeb_merleg = $connection->getData("SELECT k.tartozik, k.kovetel, k.osszeg 
    FROM konyvelesi_tetel k
    INNER JOIN egyeb e ON k.egyeb_id = e.id 
    WHERE e.ceg_adoszam = :ceg_adoszam AND k.datum BETWEEN :start_date AND :end_date", $params);


    $tmp = [$szamlak_merleg, $bank_merleg, $penztar_merleg, $egyeb_merleg];
    $konyvelesi_tetelek = [];

    foreach ($tmp as $valtozo) {
        foreach ($valtozo as $index => $adat) {
            $elso_karakter = substr($adat["tartozik"], 0, 1);
            switch (true) {
                case ($elso_karakter == "1" || $elso_karakter == "2" || $elso_karakter == "3" || $elso_karakter == "5" || $elso_karakter == "8"):
                    if (isset($konyvelesi_tetelek["tartozik"][$adat["tartozik"]])) {
                        $konyvelesi_tetelek["tartozik"][$adat["tartozik"]] += $adat["osszeg"];
                    } else {
                        $konyvelesi_tetelek["tartozik"][$adat["tartozik"]] = $adat["osszeg"];
                    }

                    break;
                default:
                    if (isset($konyvelesi_tetelek["tartozik"][$adat["tartozik"]])) {
                        $konyvelesi_tetelek["tartozik"][$adat["tartozik"]] += ($adat["osszeg"] * -1);
                    } else {
                        $konyvelesi_tetelek["tartozik"][$adat["tartozik"]] = ($adat["osszeg"] * -1);
                    }
                    break;
            }

            $elso_karakter = substr($adat["kovetel"], 0, 1);
            switch (true) {
                case ($elso_karakter == "1" || $elso_karakter == "2" || $elso_karakter == "3" || $elso_karakter == "5" || $elso_karakter == "8"):
                    if (isset($konyvelesi_tetelek["kovetel"][$adat["kovetel"]])) {
                        $konyvelesi_tetelek["kovetel"][$adat["kovetel"]] += ($adat["osszeg"] * -1);
                    } else {
                        $konyvelesi_tetelek["kovetel"][$adat["kovetel"]] = ($adat["osszeg"] * -1);
                    }

                    break;
                default:
                    if (isset($konyvelesi_tetelek["kovetel"][$adat["kovetel"]])) {
                        $konyvelesi_tetelek["kovetel"][$adat["kovetel"]] += $adat["osszeg"];
                    } else {
                        $konyvelesi_tetelek["kovetel"][$adat["kovetel"]] = $adat["osszeg"];
                    }
                    break;
            }
        }
    }

    foreach ($merleg as $focsoport => &$focsoport_adat) {
        foreach ($focsoport_adat as $csoport => &$csoport_adat) {
            foreach ($csoport_adat as $merleg_tetel => &$merleg_tetel_adat) {
                foreach ($merleg_tetel_adat as $fokonyviszam => &$fokonyviszam_adat) {
                    if (isset($konyvelesi_tetelek["tartozik"][$fokonyviszam]) && isset($konyvelesi_tetelek["kovetel"][$fokonyviszam])) {
                        $fokonyviszam_adat =   $konyvelesi_tetelek["tartozik"][$fokonyviszam] + $konyvelesi_tetelek["kovetel"][$fokonyviszam];
                    }

                    if (!isset($konyvelesi_tetelek["tartozik"][$fokonyviszam]) && isset($konyvelesi_tetelek["kovetel"][$fokonyviszam])) {
                        $fokonyviszam_adat =   0 + $konyvelesi_tetelek["kovetel"][$fokonyviszam];
                    }

                    if (isset($konyvelesi_tetelek["tartozik"][$fokonyviszam]) && !isset($konyvelesi_tetelek["kovetel"][$fokonyviszam])) {
                        $fokonyviszam_adat =   $konyvelesi_tetelek["tartozik"][$fokonyviszam] + 0;
                    }

                    if (!isset($konyvelesi_tetelek["tartozik"][$fokonyviszam]) && !isset($konyvelesi_tetelek["kovetel"][$fokonyviszam])) {
                        $fokonyviszam_adat = 0;
                    }
                }
            }
        }
    }

    showMerleg($merleg);
}

/**
 * Adott cég eredménykimutatásának elkészítése
 */
if (isset($_POST["eredmenykimutatas_formdata"])) {
    $ev = $_POST["eredmenykimutatas_formdata"]["ev"];
    $eredmenykimutatas = array();
    if (file_exists('../data/eredmenykimut_seged.json')) {
        $eredmenykimutatas = file_get_contents('../data/eredmenykimut_seged.json');
        $eredmenykimutatas = json_decode($eredmenykimutatas, true);
    }

    $start = $ev . "-01-01";
    $end = $ev . "-12-31";
    $params = [
        ":start_date" => $start,
        ":end_date" => $end,
        ":ceg_adoszam" => $_SESSION["cegadoszam"]
    ];
    $szamlak_eredmenykimutatas = $connection->getData("SELECT k.tartozik, k.kovetel, k.osszeg 
    FROM konyvelesi_tetel k
    INNER JOIN szamla sz ON k.szamla_szamlaszam = sz.szamlaszam 
    WHERE sz.ceg_adoszam = :ceg_adoszam AND k.datum BETWEEN :start_date AND :end_date", $params);

    $bank_eredmenykimutatas = $connection->getData("SELECT k.tartozik, k.kovetel, k.osszeg 
    FROM konyvelesi_tetel k
    INNER JOIN bank b ON k.bank_szamlaszam = b.bankszamlaszam 
    WHERE b.ceg_adoszam = :ceg_adoszam AND k.datum BETWEEN :start_date AND :end_date", $params);

    $penztar_eredmenykimutatas = $connection->getData("SELECT k.tartozik, k.kovetel, k.osszeg 
    FROM konyvelesi_tetel k
    INNER JOIN penztar p ON k.penztar_id = p.id 
    WHERE p.ceg_adoszam = :ceg_adoszam AND k.datum BETWEEN :start_date AND :end_date", $params);

    $egyeb_eredmenykimutatas = $connection->getData("SELECT k.tartozik, k.kovetel, k.osszeg 
    FROM konyvelesi_tetel k
    INNER JOIN egyeb e ON k.egyeb_id = e.id 
    WHERE e.ceg_adoszam = :ceg_adoszam AND k.datum BETWEEN :start_date AND :end_date", $params);


    $tmp = [$szamlak_eredmenykimutatas, $bank_eredmenykimutatas, $penztar_eredmenykimutatas, $egyeb_eredmenykimutatas];
    $konyvelesi_tetelek = [];

    foreach ($tmp as $valtozo) {
        foreach ($valtozo as $index => $adat) {
            $elso_karakter = substr($adat["tartozik"], 0, 1);
            //echo $elso_karakter . " ";
            switch (true) {
                case ($elso_karakter == "1" || $elso_karakter == "2" || $elso_karakter == "3" || $elso_karakter == "5" || $elso_karakter == "8"):
                    if (isset($konyvelesi_tetelek["tartozik"][$adat["tartozik"]])) {
                        $konyvelesi_tetelek["tartozik"][$adat["tartozik"]] += $adat["osszeg"];
                    } else {
                        $konyvelesi_tetelek["tartozik"][$adat["tartozik"]] = $adat["osszeg"];
                    }

                    break;
                default:
                    if (isset($konyvelesi_tetelek["tartozik"][$adat["tartozik"]])) {
                        $konyvelesi_tetelek["tartozik"][$adat["tartozik"]] += ($adat["osszeg"] * -1);
                    } else {
                        $konyvelesi_tetelek["tartozik"][$adat["tartozik"]] = ($adat["osszeg"] * -1);
                    }
                    break;
            }

            $elso_karakter = substr($adat["kovetel"], 0, 1);
            //echo $elso_karakter . " ";
            switch (true) {
                case ($elso_karakter == "1" || $elso_karakter == "2" || $elso_karakter == "3" || $elso_karakter == "5" || $elso_karakter == "8"):
                    if (isset($konyvelesi_tetelek["kovetel"][$adat["kovetel"]])) {
                        $konyvelesi_tetelek["kovetel"][$adat["kovetel"]] += ($adat["osszeg"] * -1);
                    } else {
                        $konyvelesi_tetelek["kovetel"][$adat["kovetel"]] = ($adat["osszeg"] * -1);
                    }

                    break;
                default:
                    if (isset($konyvelesi_tetelek["kovetel"][$adat["kovetel"]])) {
                        $konyvelesi_tetelek["kovetel"][$adat["kovetel"]] += $adat["osszeg"];
                    } else {
                        $konyvelesi_tetelek["kovetel"][$adat["kovetel"]] = $adat["osszeg"];
                    }
                    break;
            }
        }
    }

    foreach ($eredmenykimutatas as $focsoport => &$focsoport_adat) {
        foreach ($focsoport_adat as $csoport => &$csoport_adat) {
            foreach ($csoport_adat as $merleg_tetel => &$merleg_tetel_adat) {
                foreach ($merleg_tetel_adat as $fokonyviszam => &$fokonyviszam_adat) {
                    if (isset($konyvelesi_tetelek["tartozik"][$fokonyviszam]) && isset($konyvelesi_tetelek["kovetel"][$fokonyviszam])) {
                        $fokonyviszam_adat =   $konyvelesi_tetelek["tartozik"][$fokonyviszam] + $konyvelesi_tetelek["kovetel"][$fokonyviszam];
                    }

                    if (!isset($konyvelesi_tetelek["tartozik"][$fokonyviszam]) && isset($konyvelesi_tetelek["kovetel"][$fokonyviszam])) {
                        $fokonyviszam_adat =   0 + $konyvelesi_tetelek["kovetel"][$fokonyviszam];
                    }

                    if (isset($konyvelesi_tetelek["tartozik"][$fokonyviszam]) && !isset($konyvelesi_tetelek["kovetel"][$fokonyviszam])) {
                        $fokonyviszam_adat =   $konyvelesi_tetelek["tartozik"][$fokonyviszam] + 0;
                    }

                    if (!isset($konyvelesi_tetelek["tartozik"][$fokonyviszam]) && !isset($konyvelesi_tetelek["kovetel"][$fokonyviszam])) {
                        $fokonyviszam_adat = 0;
                    }
                }
            }
        }
    }
    showEredmenykimutatas($eredmenykimutatas);
}

/**
 * Adott cég főkönyvi kivonatának elkészítése
 */
if (isset($_POST["fokonyvi_kivonat"])) {
    $ev = $_POST["fokonyvi_kivonat"]["ev"];
    $szamlatukor = array();
    if (file_exists('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_szamlatukor.json')) {
        $szamlatukor = file_get_contents('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_szamlatukor.json');
        $szamlatukor = json_decode($szamlatukor, true);
    }

    $start = $ev . "-01-01";
    $end = $ev . "-12-31";
    $params = [
        ":start_date" => $start,
        ":end_date" => $end,
        ":ceg_adoszam" => $_SESSION["cegadoszam"]
    ];
    $szamlak_eredmenykimutatas = $connection->getData("SELECT k.tartozik, k.kovetel, k.osszeg 
    FROM konyvelesi_tetel k
    INNER JOIN szamla sz ON k.szamla_szamlaszam = sz.szamlaszam 
    WHERE sz.ceg_adoszam = :ceg_adoszam AND k.datum BETWEEN :start_date AND :end_date", $params);

    $bank_eredmenykimutatas = $connection->getData("SELECT k.tartozik, k.kovetel, k.osszeg 
    FROM konyvelesi_tetel k
    INNER JOIN bank b ON k.bank_szamlaszam = b.bankszamlaszam 
    WHERE b.ceg_adoszam = :ceg_adoszam AND k.datum BETWEEN :start_date AND :end_date", $params);

    $penztar_eredmenykimutatas = $connection->getData("SELECT k.tartozik, k.kovetel, k.osszeg 
    FROM konyvelesi_tetel k
    INNER JOIN penztar p ON k.penztar_id = p.id 
    WHERE p.ceg_adoszam = :ceg_adoszam AND k.datum BETWEEN :start_date AND :end_date", $params);

    $egyeb_eredmenykimutatas = $connection->getData("SELECT k.tartozik, k.kovetel, k.osszeg 
    FROM konyvelesi_tetel k
    INNER JOIN egyeb e ON k.egyeb_id = e.id 
    WHERE e.ceg_adoszam = :ceg_adoszam AND k.datum BETWEEN :start_date AND :end_date", $params);


    $tmp = [$szamlak_eredmenykimutatas, $bank_eredmenykimutatas, $penztar_eredmenykimutatas, $egyeb_eredmenykimutatas];
    $konyvelesi_tetelek = [];

    foreach ($tmp as $valtozo) {
        foreach ($valtozo as $index => $adat) {
            /* tartozik */
            if (isset($konyvelesi_tetelek["tartozik"][$adat["tartozik"]])) {
                $konyvelesi_tetelek["tartozik"][$adat["tartozik"]] += $adat["osszeg"];
                $konyvelesi_tetelek["kovetel"][$adat["tartozik"]] += 0;
            } else {
                $konyvelesi_tetelek["tartozik"][$adat["tartozik"]] = $adat["osszeg"];
                $konyvelesi_tetelek["kovetel"][$adat["tartozik"]] = 0;
            }
            /* követel */
            if (isset($konyvelesi_tetelek["kovetel"][$adat["kovetel"]])) {
                $konyvelesi_tetelek["kovetel"][$adat["kovetel"]] += ($adat["osszeg"]);
                if (!isset($konyvelesi_tetelek["tartozik"][$adat["kovetel"]])) {
                    $konyvelesi_tetelek["tartozik"][$adat["kovetel"]] = 0;
                }
            } else {
                $konyvelesi_tetelek["kovetel"][$adat["kovetel"]] = ($adat["osszeg"]);
                if (!isset($konyvelesi_tetelek["tartozik"][$adat["kovetel"]])) {
                    $konyvelesi_tetelek["tartozik"][$adat["kovetel"]] = 0;
                }
            }
        }
    }

    $tmp = array();
    foreach ($szamlatukor as $index => $adat) {
        $tmp[$adat["number"]]["name"] = $adat["name"];
        $tmp[$adat["number"]]["osszeg"] = 0;
    }

    unset($szamlatukor);
    $szamlatukor = $tmp;
    unset($tmp);
    $uj_szamlatukor = array();

    foreach ($szamlatukor as $fokonyvi_szam => $fokonyviszamadat) {
        $uj_szamlatukor[$fokonyvi_szam]["tartozik"]["osszeg"] = 0;
        $uj_szamlatukor[$fokonyvi_szam]["kovetel"]["osszeg"] = 0;
    }

    foreach ($szamlatukor as $fokonyvi_szam => $fokonyviszamadat) {
        if (isset($konyvelesi_tetelek["tartozik"][$fokonyvi_szam])) {
            $uj_szamlatukor[$fokonyvi_szam]["tartozik"]["osszeg"] += $konyvelesi_tetelek["tartozik"][$fokonyvi_szam];
        }
        if (isset($konyvelesi_tetelek["kovetel"][$fokonyvi_szam])) {
            $uj_szamlatukor[$fokonyvi_szam]["kovetel"]["osszeg"] += $konyvelesi_tetelek["kovetel"][$fokonyvi_szam];
        }
    }

    echo '<table class="rounded text-dark" style="background-color: secondary; color:white; font-size: 14px" id="fokonyviszamok">
                        <thead >
                            <th class="pb-4 pt-3 px-2" style="text-align:right;">Főkönyvi szám</th>
                            <th class="pb-4 pt-3 px-2">Megnevezés</th>
                            <th class="pb-4 pt-3 px-2">Tartozik</th>
                            <th class="pb-4 pt-3 px-2">Követel</th>
                            <th class="pb-4 pt-3 px-2">T&nbsp;Egyenleg</th>
                            <th class="pb-4 pt-3 px-2">K&nbsp;Egyenleg</th>
                        </thead>
                        <tbody class="">';
    foreach ($uj_szamlatukor as $fokonyviszam => $konyvelesiszamadatok) {
        echo '<tr style="border-top: 1px solid rgb(200, 200, 200)">
                                <td class="px-2" style="border-right:1px solid white;text-align:right;"><b>' . $fokonyviszam . '</b></td>
                                <td class="text-left px-2">' . $szamlatukor[$fokonyviszam]["name"] . '</td>
                                <td class="text-center px-2">' . $konyvelesiszamadatok["tartozik"]["osszeg"] . '</td>
                                <td class="text-center px-2">' . $konyvelesiszamadatok["kovetel"]["osszeg"] . '</td>';

        switch (true) {
            case ($konyvelesiszamadatok["tartozik"]["osszeg"] > $konyvelesiszamadatok["kovetel"]["osszeg"]):
                echo '<td class="text-center px-2">' . $konyvelesiszamadatok["tartozik"]["osszeg"] - $konyvelesiszamadatok["kovetel"]["osszeg"] . '</td>';
                echo '<td class="text-center px-2">0</td>';
                break;
            case ($konyvelesiszamadatok["tartozik"]["osszeg"] < $konyvelesiszamadatok["kovetel"]["osszeg"]):
                echo '<td class="text-center px-2">0</td>';
                echo '<td class="text-center px-2">' . $konyvelesiszamadatok["kovetel"]["osszeg"] - $konyvelesiszamadatok["tartozik"]["osszeg"] . '</td>';
                break;
            default:
                echo '<td class="text-center px-2">0</td>';
                echo '<td class="text-center px-2">0</td>';
                break;
        }
        echo '</tr>';
    }
    echo '</tbody></table>';
}

/**
 * Új főkönyvi szám beszúráshoz szűrt adatok megjelenítése
 */
if (isset($_POST["fokonyvi_szamok"])) {
    $szint = $_POST["fokonyvi_szamok"];
    switch ($szint) {
        case 'eredmeny':
            $merleg = array();
            if (file_exists('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_eredmenykimut_seged.json')) {
                $merleg = file_get_contents('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_eredmenykimut_seged.json');
                $merleg = json_decode($merleg, true);
            }
            if (isset($_POST["lvl1"])) {
                if (isset($_POST["lvl2"])) {
                    $result = array();
                    foreach ($merleg[$_POST["lvl1"]][$_POST["lvl2"]] as $key => $value) {
                        $result[] = $key;
                    }
                    echo json_encode($result);
                } else {
                    $result = array();
                    foreach ($merleg[$_POST["lvl1"]] as $key => $value) {
                        $result[] = $key;
                    }
                    echo json_encode($result);
                }
            } else {
                $result = array();
                foreach ($merleg as $key => $value) {
                    $result[] = $key;
                }
                echo json_encode($result);
            }
            break;
        case 'merleg':
            $merleg = array();
            if (file_exists('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_merleg_seged.json')) {
                $merleg = file_get_contents('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_merleg_seged.json');
                $merleg = json_decode($merleg, true);
            }
            if (isset($_POST["lvl1"])) {

                if (isset($_POST["lvl2"])) {

                    $result = array();
                    foreach ($merleg[$_POST["lvl1"]][$_POST["lvl2"]] as $key => $value) {
                        $result[] = $key;
                    }
                    echo json_encode($result);
                } else {
                    $result = array();
                    foreach ($merleg[$_POST["lvl1"]] as $key => $value) {
                        $result[] = $key;
                    }
                    echo json_encode($result);
                }
            } else {
                $result = array();
                foreach ($merleg as $key => $value) {
                    $result[] = $key;
                }
                echo json_encode($result);
            }
            break;
        default:
            break;
    }
}

/**
 * Új főkönyvi szám beszúrása a cég saját mérleg, eredménykimutatás, számlatükör JSON fájljába, adott helyre/sorrendben
 */
if (isset($_POST["ujFokonyviszamok"])) {

    $ujFokonyviszamok = $_POST["ujFokonyviszamok"][0];
    $ujsor = [
        "number" => $_POST["ujFokonyviszamok"][0]["ujfokonyviszam"],
        "name" => $_POST["ujFokonyviszamok"][0]["ujfokonyviszam_magyarazat"]
    ];

    $szamlatukor = array();
    if (file_exists('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_szamlatukor.json')) {
        $szamlatukor = file_get_contents('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_szamlatukor.json');
        $szamlatukor = json_decode($szamlatukor, true);
    }
    foreach ($szamlatukor as $key => $value) {
        if ($value["number"] == $ujsor["number"]) {
            echo "letezoszam";
            die();
        }
    }
    $szamlatukor[] = $ujsor;
    $tmp = array();
    foreach ($szamlatukor as $key => $value) {
        $tmp[] = $value['number'];
    }

    sort($tmp, SORT_STRING);

    $bovitett_szamlatukor = array();
    foreach ($tmp as $key => $value) {
        $tmp2["number"] = $value;
        $tmp2["name"] = '0';
        foreach ($szamlatukor as $key => $szamlatukoradat) {
            if ($tmp2["number"] == $szamlatukoradat["number"]) {
                $tmp2["name"] = $szamlatukoradat["name"];
                $bovitett_szamlatukor[] = $tmp2;
                break;
            }
        }
    }
    foreach ($bovitett_szamlatukor as $key => $value) {
        if ($value['name'] == "0") {
            $bovitett_szamlatukor[$key]['name'] = $ujsor['name'];
        }
    }

    $jsonString = json_encode($bovitett_szamlatukor, JSON_PRETTY_PRINT);
    $fp = fopen('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_szamlatukor.json', 'w');
    fwrite($fp, $jsonString);
    $jsonString = "";
    fclose($fp);

    if ($ujFokonyviszamok["tipus"] == "eredmeny") {
        $merleg = array();
        if (file_exists('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_eredmenykimut_seged.json')) {
            $merleg = file_get_contents('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_eredmenykimut_seged.json');
            $merleg = json_decode($merleg, true);
        }
        $merleg[$ujFokonyviszamok["level_1"]][$ujFokonyviszamok["level_2"]][$ujFokonyviszamok["level_3"]][$ujFokonyviszamok["ujfokonyviszam"]] = 1;
        ksort($merleg[$ujFokonyviszamok["level_1"]][$ujFokonyviszamok["level_2"]][$ujFokonyviszamok["level_3"]]);
        $jsonString = json_encode($merleg, JSON_PRETTY_PRINT);
        $fp = fopen('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_eredmenykimut_seged.json', 'w');
        fwrite($fp, $jsonString);
        $jsonString = "";
        fclose($fp);
    } else {
        $merleg = array();
        if (file_exists('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_merleg_seged.json')) {
            $merleg = file_get_contents('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_merleg_seged.json');
            $merleg = json_decode($merleg, true);
        }
        $merleg[$ujFokonyviszamok["level_1"]][$ujFokonyviszamok["level_2"]][$ujFokonyviszamok["level_3"]][$ujFokonyviszamok["ujfokonyviszam"]] = 1;
        ksort($merleg[$ujFokonyviszamok["level_1"]][$ujFokonyviszamok["level_2"]][$ujFokonyviszamok["level_3"]]);
        $jsonString = json_encode($merleg, JSON_PRETTY_PRINT);
        $fp = fopen('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_merleg_seged.json', 'w');
        fwrite($fp, $jsonString);
        $jsonString = "";
        fclose($fp);
    }
}

/**
 * Felhasználói profilképcsere
 */
if (isset($_POST["profilkep_csere"])) {
    if (file_exists('../data/profile_information.json')) {
        $profilok = file_get_contents('../data/profile_information.json');
        $profilok = json_decode($profilok, true);
        $profilok[$_SESSION["felhasznalo_email"]] = str_replace("../images/profile_pictures/", "", $_POST["profilkep_csere"]);
        $_SESSION["profilkep"] =  $_POST["profilkep_csere"];
        $jsonString = json_encode($profilok, JSON_PRETTY_PRINT);
        $fp = fopen('../data/profile_information.json', 'w');
        fwrite($fp, $jsonString);
        $jsonString = "";
        fclose($fp);
    }
    echo "siker";
}

/**
 * Felhasználó kizárása a rendszerből
 */
if (isset($_POST["inaktivalas"])) {
    $params = [
        ":id" => $_POST["inaktivalas"],
        ":allapot" => 0
    ];
    $connection->setData("UPDATE `felhasznalo` SET
    `aktiv` = :allapot
    WHERE 
    id = :id", $params);
    echo "siker";
}

if (isset($_POST["ev"]) && isset($_POST["honap"]) && isset($_POST["bankszamlaszam"])) {
    $ev = $_POST["ev"];
    $honap = $_POST["honap"];
    $bankszamlaszam = $_POST["bankszamlaszam"];
    $params = [
        ":cegadoszam" =>  $_SESSION["cegadoszam"],
        ":bankszamlaszam" =>  $bankszamlaszam,
        ":kezdodatum" => $ev . "-" . $honap . "-01",
        ":zarodatum" => date("Y-m-t", strtotime($ev . "-" . $honap . "-01"))
    ];
    $connection = new Connection();
    $banktetelek = $connection->getData("SELECT k.*, b.* 
                                        FROM konyvelesi_tetel k 
                                        INNER JOIN bank b 
                                        ON k.bank_szamlaszam = b.bankszamlaszam 
                                        WHERE b.ceg_adoszam = :cegadoszam
                                        AND k.datum BETWEEN :kezdodatum AND :zarodatum
                                        AND b.bankszamlaszam = :bankszamlaszam 
                                        ORDER BY b.bankszamlaszam, k.datum", $params);
    $tartozikEgyenleg = $connection->getData("SELECT 
                                        SUM(k.osszeg) AS tartozik_osszeg
                                        FROM konyvelesi_tetel k 
                                        INNER JOIN bank b 
                                        ON k.bank_szamlaszam = b.bankszamlaszam 
                                        WHERE b.ceg_adoszam = :cegadoszam
                                        AND k.tartozik LIKE '384%'
                                        AND k.datum BETWEEN :kezdodatum AND :zarodatum 
                                        AND b.bankszamlaszam = :bankszamlaszam 
                                        ORDER BY b.bankszamlaszam, k.datum", $params);
    $kovetelEgyenleg = $connection->getData("SELECT
                                        SUM(k.osszeg) AS kovetel_osszeg
                                        FROM konyvelesi_tetel k 
                                        INNER JOIN bank b 
                                        ON k.bank_szamlaszam = b.bankszamlaszam 
                                        WHERE b.ceg_adoszam = :cegadoszam
                                        AND k.kovetel LIKE '384%'
                                        AND k.datum BETWEEN :kezdodatum AND :zarodatum 
                                        AND b.bankszamlaszam = :bankszamlaszam 
                                        ORDER BY b.bankszamlaszam, k.datum", $params);
    showBanktetelek($banktetelek, $tartozikEgyenleg, $kovetelEgyenleg);
}

if (isset($_POST["ujbankszamlaszam"])) {
    $connection = new Connection();
    for ($i = 1; $i < 4; $i++) {
        if (isset($_POST["ujbankszamlaszam"][0]["bszszam_" . ($i)]) && strlen($_POST["ujbankszamlaszam"][0]["bszszam_" . ($i)]) > 1) {
            $bankszamlaszam = str_replace("-", "", $_POST["ujbankszamlaszam"][0]["bszszam_" . ($i)]);
            if ((strlen($bankszamlaszam) % 8) == 0 && strlen($bankszamlaszam) > 8) {
                if (strlen($bankszamlaszam) < 17) {
                    $bankszamlaszam = substr($bankszamlaszam, 0, 8) . '-' . substr($bankszamlaszam, 8) . '-' . "00000000";
                } else {
                    $bankszamlaszam = substr($bankszamlaszam, 0, 8) . '-' . substr($bankszamlaszam, 8, 8) . '-' . substr($bankszamlaszam, 16);
                }
                $params = [
                    ":bankszamlaszam" => $bankszamlaszam,
                    ":cegadoszam" => $_SESSION["cegadoszam"]
                ];
                $connection->setData("INSERT INTO `bank`(`ceg_adoszam`, `bankszamlaszam`) VALUES (:cegadoszam,:bankszamlaszam)", $params);
                unset($params);
            } else {
                echo "bankszamlahiba";
                die();
            }
        }
    }
    echo "siker";
}
/**
 * Mérleg megjelenítése
 *
 * @param [type] $merleg - array, mérleg adatait tartalmazza (szám + összeg)
 * @return void
 */
function showMerleg($merleg)
{
    echo '<div class="row">';
    echo '<div class="col">';

    echo "<table>";
    $eszkoztotal = 0;
    $ervenyes_sorok = ["A", "B", "C"];
    foreach ($merleg as $eszkoz_focsoport => $eszkoz_focsoport_adat) {
        if (in_array(substr($eszkoz_focsoport, 0, 1), $ervenyes_sorok)) {
            echo "<tr>";

            $eszkoz_csoport_total = 0;
            foreach ($eszkoz_focsoport_adat as $eszkoz_csoport => $eszkoz_csoport_adat) {
                foreach ($eszkoz_csoport_adat as $eszkoz_merleg_tetel => $eszkoz_merleg_tetel_adat) {
                    $eszkoz_csoport_total += array_sum($eszkoz_merleg_tetel_adat);
                    $eszkoztotal += array_sum($eszkoz_merleg_tetel_adat);
                }
            }
            echo "<td><strong>" . $eszkoz_focsoport . "</strong></td><td><strong>" . $eszkoz_csoport_total . "</strong></td></tr>";
            foreach ($eszkoz_focsoport_adat as $eszkoz_csoport => $eszkoz_csoport_adat) {
                $total = 0;
                foreach ($eszkoz_csoport_adat as $eszkoz_merleg_tetel => $eszkoz_merleg_tetel_adat) {
                    $total += array_sum($eszkoz_merleg_tetel_adat);
                }
                if ($eszkoz_csoport != "X") {
                    echo "<tr><td><strong><i>" . $eszkoz_csoport . "</i></strong></td><td><strong><i>" . $total . "</i></strong></td></tr>";
                }

                foreach ($eszkoz_csoport_adat as $eszkoz_merleg_tetel => $eszkoz_merleg_tetel_adat) {
                    if ($eszkoz_merleg_tetel != "X") {
                        echo "<tr><td>" . $eszkoz_merleg_tetel . "</td><td>" . array_sum($eszkoz_merleg_tetel_adat) . "</td></tr>";
                    }
                }
                echo "</tr>";
            }
        } else {
            break;
        }
    }
    echo "<tr><td><strong>ESZKÖZÖK ÖSSZESEN</strong></td><td><strong>" . $eszkoztotal . "</strong></td></tr>";
    echo "</table>";
    echo "</div>";
    echo '<div class="col">';
    echo "<table>";
    $forrastotal = 0;
    $ervenyes_sorok = ["D", "E", "F", "G"];
    foreach ($merleg as $forrasfocsoport => $forrasfocsoport_adat) {
        if (in_array(substr($forrasfocsoport, 0, 1), $ervenyes_sorok)) {
            $focsoport_total = 0;
            foreach ($forrasfocsoport_adat as $sum_csoport => $sum_csoport_adat) {
                foreach ($sum_csoport_adat as $sum_merleg_tetel => $sum_merleg_tetel_adat) {
                    $focsoport_total += array_sum($sum_merleg_tetel_adat);
                }
            }
            $forrastotal += $focsoport_total;
            echo "<tr><td><strong>" . $forrasfocsoport . "</strong></td><td><strong>" . $focsoport_total . "</strong></td></tr>";
            foreach ($forrasfocsoport_adat as $forrascsoport => $forrascsoport_adat) {

                if (substr($forrascsoport, 0, 1) != "X") {
                    $sub_csoport_total = 0;
                    foreach ($forrascsoport_adat as $sum_forrasmerleg_tetel => $sum_forrasmerleg_tetel_adat) {
                        if ($sum_forrasmerleg_tetel != "X") {
                            $sub_csoport_total += array_sum($sum_forrasmerleg_tetel_adat);
                        }
                    }
                    echo "<tr><td><strong><i>" . $forrascsoport . "</i></strong></td><td><strong><i>" . $sub_csoport_total . "</i></strong></td></tr>";
                    foreach ($forrascsoport_adat as $forrasmerleg_tetel => $forrasmerleg_tetel_adat) {
                        if ($forrasmerleg_tetel != "X") { // Exclude placeholder "X"
                            $item_total = array_sum($forrasmerleg_tetel_adat);
                            echo "<tr><td>" . $forrasmerleg_tetel . "</td><td>" . $item_total . "</td></tr>";
                        }
                    }
                }
            }
        }
    }
    echo "<tr><td><strong>FORRÁSOK ÖSSZESEN</strong></td><td><strong>" . $forrastotal . "</strong></td></tr>";
    echo "</table>";
    echo "</div>";
    echo "</div>";
}

/**
 * Eredménykimutatás megjelenítése
 *
 * @param [type] $eredmenykimutatas - array, eredménykimutatás adatait tartalmazza (szám + összeg)
 * @return void
 */
function showEredmenykimutatas($eredmenykimutatas)
{
    $eszkoztotal = 0;
    $nagybetuk = [
        "A" => 0,
        "B" => 0,
        "C" => 0,
        "D" => 0,
        "E" => 0,
        "F" => 0,
        "G" => 0,
    ];
    $romaiszamok = [
        "I" => 0,
        "II" => 0,
        "III" => 0,
        "IV" => 0,
        "V" => 0,
        "VI" => 0,
        "VII" => 0,
        "VIII" => 0,
        "IX" => 0,
        "X" => 0,
        "XI" => 0,
        "XII" => 0,
    ];
    $arabszamok = array();
    /* számolás */
    foreach ($eredmenykimutatas as $nagybetu => $nagybetu_adat) {
        foreach ($nagybetu_adat as $romaiszam => $romaiszam_adat) {
            $tmp_romaiszam = explode(".", $romaiszam);
            if ($tmp_romaiszam[0] != "W") {
                foreach ($romaiszam_adat as $arabszam => $arabszam_adat) {
                    if ($arabszam != "W") {
                        $tmp_arabszam = explode(".", $arabszam);
                        if (isset($arabszamok[intval($tmp_arabszam[0])])) {
                            $arabszamok[intval($tmp_arabszam[0])] += array_sum($arabszam_adat);
                            $romaiszamok[$tmp_romaiszam[0]] += array_sum($arabszam_adat);
                        } else {
                            $arabszamok[intval($tmp_arabszam[0])] = array_sum($arabszam_adat);
                            $romaiszamok[$tmp_romaiszam[0]] += array_sum($arabszam_adat);
                        }
                    } else {
                        $romaiszamok[$tmp_romaiszam[0]] += array_sum($arabszam_adat);
                    }
                }
            } else {
                foreach ($romaiszam_adat as $arabszam => $arabszam_adat) {
                    $tmp_arabszam = explode(".", $arabszam);
                    if (isset($arabszamok[intval($tmp_arabszam[0])])) {
                        $arabszamok[intval($tmp_arabszam[0])] += array_sum($arabszam_adat);
                    } else {
                        $arabszamok[intval($tmp_arabszam[0])] = array_sum($arabszam_adat);
                    }
                }
            }
        }
    }
    $nagybetuk["A"] = $romaiszamok["I"] + $romaiszamok["II"] + $romaiszamok["III"] - $romaiszamok["IV"] - $romaiszamok["V"] - $romaiszamok["VI"] - $romaiszamok["VII"];
    $nagybetuk["B"] = $romaiszamok["VIII"] - $romaiszamok["IX"];
    $nagybetuk["C"] = $nagybetuk["A"] + $nagybetuk["B"];
    $nagybetuk["D"] = $romaiszamok["X"] - $romaiszamok["XI"];
    $nagybetuk["E"] = $nagybetuk["C"] + $nagybetuk["D"];
    $nagybetuk["F"] = $nagybetuk["E"] - $romaiszamok["XII"];
    $nagybetuk["G"] = $nagybetuk["F"] + ($arabszamok["22"] - $arabszamok["23"]);

    ksort($arabszamok);
    ksort($eredmenykimutatas);
    echo '<div class="row">';
    echo '<div class="col">';

    echo "<table>";
    echo "<tr>";
    foreach ($eredmenykimutatas as $nagybetu => $nagybetu_adat) {
        $tmp_nagybetu = explode(".", $nagybetu);
        echo "<td><strong>" . $nagybetu . "</strong></td><td><strong>" . $nagybetuk[$tmp_nagybetu[0]] . "</strong></td></tr>";
        foreach ($nagybetu_adat as $romaiszam => $romaiszamadat) {
            $tmp_romaiszam = explode(".", $romaiszam);
            if ($tmp_romaiszam[0] != "W") {
                echo "<tr><td><i>" . $romaiszam . "</i></td><td><i>" . $romaiszamok[$tmp_romaiszam[0]] . "</i></td></tr>";
            }
            foreach ($romaiszamadat as $arabszam => $arabszam_adat) {
                $tmp_arabszam = explode(".", $arabszam);
                if ($tmp_arabszam[0] != "W") {
                    echo "<tr><td>" . $arabszam . "</td><td>" . $arabszamok[intval($tmp_arabszam[0])] . "</td></tr>";
                }
            }
            echo "</tr>";
        }
    }
    echo "</table>";
    echo "</div>";
    echo "</div>";
}

function showBanktetelek($banktetelek, $tartozikEgyenleg, $kovetelEgyenleg)
{
    echo '
    <div class="row pb-3 pt-3">
        <div class="col"></div>
        <div class="input-group col">
            <span class="input-group-text">T</span>
            <input type="text" class="form-control" value="' . $tartozikEgyenleg[0]["tartozik_osszeg"] . '" disabled>
        </div>
        <div class="input-group col">
            <span class="input-group-text">K</span>
            <input type="text" class="form-control" value="' . $kovetelEgyenleg[0]["kovetel_osszeg"] . '" disabled>
        </div>
    </div>';

    echo '<table class="rounded text-dark" style="background-color: secondary; color:white; font-size: 14px" id="bank">
                        <thead class="text-center">
                            <th class="pb-4 pt-3">Jóváírás/Terhelés</th>
                            <th class="pb-4 pt-3">Dátum</th>
                            <th class="pb-4 pt-3">Megjegyzés</th>
                            <th class="pb-4 pt-3">Összeg</th>
                            <th></th>
                        </thead>
                        <tbody class="text-center">';
    foreach ($banktetelek as $bank) {
        echo '<tr style="border-top: 1px solid rgb(200, 200, 200)">';
        echo '<td>';
        if ($bank["tartozik"] == 384) {
            echo '<i class="fa-regular fa-square-plus text-success border rounded" style="background-color: rgb(210, 275, 211);"></i>';
        } else {
            echo '<i class="fa-regular fa-square-minus text-danger border rounded" style="background-color: rgb(245, 208, 205);"></i>';
        }
        echo '</td>';
        echo '<td>' . $bank["datum"] . '</td>';
        echo '<td>' . $bank["szamla_szamlaszam"] . '</td>';
        echo '<td>' . number_format($bank["osszeg"], 0, "", " ") . '</td>';
        echo '<td>';
        $bankId = $bank["id"];
        echo '<button class="btn btn-outline-dark" style="border:none;" id="bank_mod_' . $bank["id"] . '" onclick="szamlamodositas(this.id)"><i class="fa-regular fa-pen-to-square"></i></button>';


        echo '<button class="btn btn-outline-dark" style="border:none;" id="bank_del_' . $bank["id"] . '" onclick="szamlamodositas(this.id)"><i class="fa-regular fa-trash-can"></i></button>';
        echo '</td></tr>';
    }
    echo '</tbody></table>';
}
/**
 * kiiras
 *
 * @param [type] $tomb - array, kiírja formázott formában a paraméter tömböt
 * @return void
 */
function kiiras($tomb)
{
    echo "<pre>";
    var_dump($tomb);
    echo "</pre>";
}
