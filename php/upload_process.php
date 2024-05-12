<?php
namespace Gerke\Imagetotext;
session_start();
if (isset($_POST["mentes"])) {
    switch ($_SESSION["regType"]) {
        case 'szallito':
            for ($i = 0; $i < count($_FILES["files"]["name"]); $i++) {
                move_uploaded_file($_FILES["files"]['tmp_name'][$i], "../invoices/szallito/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/" . $_FILES["files"]["name"][$i]);
            }

            break;
        case 'vevo':
            for ($i = 0; $i < count($_FILES["files"]["name"]); $i++) {
                move_uploaded_file($_FILES["files"]['tmp_name'][$i], "../invoices/vevo/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/" . $_FILES["files"]["name"][$i]);
            }

            break;
        case 'penztar':
            for ($i = 0; $i < count($_FILES["files"]["name"]); $i++) {
                move_uploaded_file($_FILES["files"]['tmp_name'][$i], "../invoices/penztar/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/" . $_FILES["files"]["name"][$i]);
            }

            break;
        default:
            break;
    }
    header("Location: szamlak.php?type=" . $_SESSION["regType"]);
}
if (isset($_POST["invoice"])) {
    switch ($_POST["invoice"]) {
        case 'all':
            $_SESSION["szamla"] = "all";
            break;
        case 'none':
            $_SESSION["szamla"] = "none";
            header("Location: szamlarogzites.php");
            break;
        case "only-penztar":
            $_SESSION["szamla"] = "only-penztar";
            header("Location: szamlarogzites.php");
            break;
        default:
            $_SESSION["szamla"] = $_POST["invoice"];
            break;
    }
}

