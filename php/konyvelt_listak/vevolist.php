<?php

namespace Gerke\Imagetotext;

session_start();
require('../oop/Connection.php');
$connection = new Connection();
$params = [
    ":cegadoszam" =>  $_SESSION["cegadoszam"]
];

$konyveltszamlak = $connection->getData("SELECT sz.*, p.nev, 
SUM(
    CASE
        WHEN k.kovetel LIKE '31%' THEN -k.osszeg
        WHEN k.tartozik LIKE '31%' THEN k.osszeg
        ELSE k.osszeg
    END
) AS adjusted_osszeg,
SUM(
    CASE
        WHEN bank_szamlaszam IS NULL THEN
            CASE
                WHEN k.kovetel LIKE '31%' THEN -k.osszeg
                WHEN k.tartozik LIKE '31%' THEN k.osszeg
                ELSE k.osszeg
            END
        ELSE 0
    END
) AS adjusted_osszeg_no_bank,
SUM(
    CASE
        WHEN bank_szamlaszam IS NOT NULL THEN
            CASE
                WHEN k.kovetel LIKE '31%' THEN -k.osszeg
                WHEN k.tartozik LIKE '31%' THEN k.osszeg
                ELSE k.osszeg
            END
        ELSE 0
    END
) AS adjusted_osszeg_with_bank
FROM szamla sz
INNER JOIN konyvelesi_tetel k ON sz.szamlaszam = k.szamla_szamlaszam
INNER JOIN partner p ON sz.partner_adoszam = p.adoszam
WHERE sz.ceg_adoszam = :cegadoszam  
AND p.vevo = 1 
AND sz.szamlaszam IS NOT NULL
GROUP BY sz.szamlaszam
ORDER BY p.nev", $params);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../../style.css">
    <title>Számlák</title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <script src="../../js/fuggvenyek.js"></script>
</head>

<body>
    <?php include('../navbar.php') ?>
    <div id="contentContainer" class="suppinvoice container mx-auto m-5 shadow" style="background-color: rgba(225, 230, 224);">
        <div class="pt-2">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link text-black" href="szallitolist.php">Szállító</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active text-secondary border-bottom-0 border-secondary" aria-current="page" href="vevolist.php">Vevő</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-black" href="penztarlist.php">Pénztár</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-black" href="banklist.php">Bank</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-black" href="egyeblist.php">Egyéb</a>
                </li>
            </ul>
        </div>

        <div class="row pt-3">
            <div class="col-1 pt-3">
                <a href="../homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col mx-auto text-center pt-3 mt-3 mb-4">
                <h3>Rögzített vevő számlák</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="p-4">
            <div class="row">
                <div class="col-8 mb-5">
                    <div class="row w-100">
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" class="form-control" id="vevo_nev" onkeyup='kereses("vevo_nev","vevoszamlak")' placeholder="Név alapján...">
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" class="form-control" id="vevo_szamla" onkeyup='kereses("vevo_szamla","vevoszamlak")' placeholder="Számlaszám alapján...">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row listainput">
                    <div id="columnChange" class="mx-auto">
                        <div class="row">
                            <table class="rounded text-dark" style="background-color: secondary; color:white; font-size: 14px" id="vevoszamlak">
                                <thead class="text-center">
                                    <th class="pb-4 pt-3">Név</th>
                                    <th class="pb-4 pt-3">Számlaszám</th>
                                    <th class="pb-4 pt-3">Teljesítés</th>
                                    <th class="pb-4 pt-3">Fizetési határidő</th>
                                    <th class="pb-4 pt-3">Kiállítás</th>
                                    <th class="pb-4 pt-3">Összeg</th>
                                    <th class="pb-4 pt-3">Egyenleg</th>
                                    <th class="pb-4 pt-3">Fizetve</th>
                                    <th class="pb-4 pt-3">Megjegyzés</th>
                                    <th></th>
                                </thead>
                                <tbody class="text-center">
                                    <?php foreach ($konyveltszamlak as $szamla) : ?>
                                        <tr style="border-top: 1px solid rgb(200, 200, 200)">
                                            <td>
                                                <?= $szamla["nev"] ?>
                                            </td>
                                            <td style="padding: 10px;">
                                                <?php $pdf = str_replace("../", "../../", $szamla["pdf"]); ?>
                                                <?php if (is_file($pdf)) : ?>
                                                    <a href="<?= $pdf ?>" target="_blank"> <?= $szamla["szamlaszam"] ?></a>
                                                <?php else : ?>
                                                    <?= $szamla["szamlaszam"] ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $szamla["teljesites"] ?>
                                            </td>
                                            <td>
                                                <?= $szamla["fizhat"] ?>
                                            </td>
                                            <td>
                                                <?= $szamla["kiallitas"] ?>
                                            </td>
                                            <td>
                                                <?= number_format($szamla["adjusted_osszeg_no_bank"],0,""," ") ?>
                                            </td>
                                            <td>
                                                <?= number_format($szamla["adjusted_osszeg_no_bank"] + $szamla["adjusted_osszeg_with_bank"],0,""," ") ?>
                                            </td>
                                            <td>
                                                <?php if (($szamla["adjusted_osszeg_no_bank"] + $szamla["adjusted_osszeg_with_bank"]) == 0) : ?>
                                                    <?= '<i class="fa-solid fa-check text-success border rounded ps-3 pe-3" style="background-color: rgb(210, 235, 211);"></i>' ?>
                                                <?php else : ?>
                                                    <?= '<i class="fa-solid fa-xmark text-danger border rounded ps-3 pe-3" style="background-color: rgb(245, 208, 205);""></i>' ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $szamla["megjegyzes"] ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-outline-dark" style="border:none;" id="vevo_mod_<?= $szamla["szamlaszam"] ?>" onclick='szamlamodositas(this.id)'><i class="fa-regular fa-pen-to-square"></i></button>
                                                <button class="btn btn-outline-dark" style="border:none;" id="vevo_del_<?= $szamla["szamlaszam"] ?>" onclick='szamlamodositas(this.id)'><i class="fa-regular fa-trash-can"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>

        </script>
</body>

</html>