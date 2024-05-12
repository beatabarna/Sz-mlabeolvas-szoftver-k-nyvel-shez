<?php

namespace Gerke\Imagetotext;

session_start();
require('../oop/Connection.php');
$connection = new Connection();
$params = [
    ":cegadoszam" =>  $_SESSION["cegadoszam"]
];
$egyeb = $connection->getData("SELECT e.*, k.datum, SUM(k.osszeg) as total_osszeg
                                  FROM egyeb e
                                  LEFT JOIN konyvelesi_tetel k 
                                  ON e.id = k.egyeb_id
                                  WHERE e.ceg_adoszam = :cegadoszam
                                  GROUP BY e.id
                                  ORDER BY k.datum DESC", $params);

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
                    <a class="nav-link text-black" href="vevolist.php">Vevő</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-black" href="penztarlist.php">Pénztár</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-black" href="banklist.php">Bank</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active text-secondary border-bottom-0 border-secondary" aria-current="page" href="egyeblist.php">Egyéb</a>
                </li>
            </ul>
        </div>
        <div class="row pt-3">
            <div class="col-1 pt-3">
                <a href="../homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col mx-auto text-center pt-3 mt-3 mb-4">
                <h3>Rögzített egyéb tételek</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="p-4">
            <div class="col-8 mb-5">
                <div class="row w-100">
                    <div class="col">
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" class="form-control" id="egyeb_nev" onkeyup='kereses("egyeb_nev","egyeb")' placeholder="Megnevezés alapján...">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" class="form-control" id="egyeb_megnevezes" onkeyup='kereses("egyeb_megnevezes","egyeb")' placeholder="Megjegyzés alapján...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row listainput">
                <div id="columnChange" class="mx-auto">
                    <div class="row">
                        <table class="rounded text-dark" style="background-color: secondary; color:white; font-size: 14px" id="egyeb">
                            <thead class="text-center">
                                <th class="pb-4 pt-3">Dátum</th>
                                <th class="pb-4 pt-3">Megnevezés</th>
                                <th class="pb-4 pt-3">Megjegyzés</th>
                                <th class="pb-4 pt-3">Összeg</th>
                            </thead>
                            <tbody class="text-center">
                                <?php foreach ($egyeb as $tetelek) : ?>
                                    <tr style="border-top: 1px solid rgb(200, 200, 200)">
                                        <td>
                                            <?= $tetelek["datum"] ?>
                                        </td>
                                        <td>
                                            <?= $tetelek["megnevezes"] ?>
                                        </td>
                                        <td>
                                            <?= $tetelek["megjegyzes"] ?>
                                        </td>
                                        <td>
                                            <?= number_format($tetelek["total_osszeg"],0, "", " ") ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-outline-dark" style="border:none;" id="egyeb_mod_<?= $tetelek["id"] ?>" onclick='szamlamodositas(this.id)'><i class="fa-regular fa-pen-to-square"></i></button>
                                            <button class="btn btn-outline-dark" style="border:none;" id="egyeb_del_<?= $tetelek["id"] ?>" onclick='szamlamodositas(this.id)'><i class="fa-regular fa-trash-can"></i></button>
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