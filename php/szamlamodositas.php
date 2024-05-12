<?php
namespace Gerke\Imagetotext;
session_start();
require('oop/Connection.php');

if (isset($_POST["modositas"])) {
    if ($_POST["vegzendomuvelet"] == "mod") {
        $_SESSION["szamlamuvelet"] = "modositas";
        $_SESSION["szamla"] = $_POST["modositas"];
        $_SESSION["tipus"] = $_POST["szamlatipus"];
        switch ($_POST["szamlatipus"]) {
            case 'bank':
                echo "sikeresbank";
                break;
            case "egyeb":
                echo "sikeresegyeb";
                break;
            default:
                echo "sikeresszamlalekeres";
                break;
        }
    } else {
        switch ($_POST["szamlatipus"]) {
            case 'szallito':
            case 'vevo':
                $connection = new Connection();
                $params = [
                    ":szamlaszam" => $_POST["modositas"]
                ];
                $connection->getData("DELETE FROM `szamla` WHERE `szamlaszam` = :szamlaszam", $params);
                echo "sikeresszamlatorles";
                break;
            case "bank":
                $connection = new Connection();
                $params = [
                    ":konvelesitetelID" => $_POST["modositas"]
                ];
                $connection->getData("DELETE FROM `konyvelesi_tetel` WHERE `id` = :konvelesitetelID", $params);
                echo "sikeresszamlatorles";
                break;
            case "penztar":
                $connection = new Connection();
                $params = [
                    ":penztarID" => $_POST["modositas"]
                ];
                $connection->getData("DELETE FROM `penztar` WHERE `id` = :penztarID", $params);
                echo "sikeresszamlatorles";
                break;
            case 'penztarszamla':
                $connection = new Connection();
                $params = [
                    ":szamlaszam" => $_POST["modositas"]
                ];
                $connection->getData("DELETE FROM `szamla` WHERE `szamlaszam` = :szamlaszam", $params);
                echo "sikeresszamlatorles";
                break;
            case "egyeb":
                $connection = new Connection();
                $params = [
                    ":egyebID" => $_POST["modositas"]
                ];
                $connection->getData("DELETE FROM `egyeb` WHERE `id` = :egyebID", $params);
                echo "sikeresszamlatorles";
                break;
            default:
                break;
        }
    }
}
if(isset($_POST["ceg_adoszam"]) && $_POST["muvelet"] == "modositas"){
    $_SESSION["ceg_adoszam_modositasra"] = $_POST["ceg_adoszam"];
   echo "siker";
}
