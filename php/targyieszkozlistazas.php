<?php
namespace Gerke\Imagetotext;
session_start();
require('oop/Connection.php');
$connection = new Connection();
$params = [
    ":cegadoszam" =>  $_SESSION["cegadoszam"]
];
$targyieszkozlista = $connection->getData("SELECT t.megnevezes, t.bekerulesi_ertek, t.hasznalati_ido, t.ertekcsokkenes, t.megjegyzes, t.szamla_szamlaszam, s.teljesites, s.pdf FROM targyi_eszkoz t INNER JOIN szamla s ON t.szamla_szamlaszam = s.szamlaszam WHERE s.ceg_adoszam = :cegadoszam ORDER BY t.megnevezes ", $params);

$telista = [];
foreach ($targyieszkozlista as $index => $targyieszkoz) {
    $telista[$targyieszkoz["megnevezes"]][] = $targyieszkozlista[$index];
}
$targyieszkoz_osszegzo;
foreach ($telista as $megnevezes => $targyieszkozok) {
    foreach ($targyieszkozok as $index => $adat) {
        unset($telista[$megnevezes][$index]["megnevezes"]);
        if (isset($targyieszkoz_osszegzo[$megnevezes]["osszeg"])) {
            $targyieszkoz_osszegzo[$megnevezes]["osszeg"] += $telista[$megnevezes][$index]["bekerulesi_ertek"];
        } else {
            $targyieszkoz_osszegzo[$megnevezes]["osszeg"] = $telista[$megnevezes][$index]["bekerulesi_ertek"];
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../style.css">
    <title>File Upload and Selection</title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <script src="../js/fuggvenyek.js"></script>
</head>

<body>
    <?php include('navbar.php')?>
    <div id="contentContainer" class="suppinvoice container mx-auto m-5 shadow"  style="background-color: rgba(225, 230, 224);">
        <div class="row">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col-10 mx-auto text-center pt-3 mt-3">
                <h3>Tárgyi eszközök listázása</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="p-4">
            <div class="row">
                <div class="col input-group mb-4"> 
                <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control" id="t_eszk_kereses" onkeyup='kereses("t_eszk_kereses","tlista")'><br>
                </div>
            </div>
            <div class="row">
                <div id="columnChange" class="mx-auto">
                    <table class="rounded text-dark w-100" style="background-color: secondary; color:white; font-size: 14px" id="tlista">
                        <thead class="text-center">
                            <th class="pb-4 pt-3">Megnevezés</th>
                            <th class="pb-4 pt-3">Bekerülési érték</th>
                            <th class="pb-4 pt-3">Használati idő</th>
                            <th class="pb-4 pt-3">Értékcsökkenés</th>
                            <th class="pb-4 pt-3">Megjegyzés</th>
                            <th class="pb-4 pt-3">Számla</th>
                            <th class="pb-4 pt-3">Dátum</th>
                        </thead>
                        <tbody class="text-center">
                            <?php
                            $seen = array();
                            foreach ($telista as $megnevezes => $adatok) : ?>
                                <?php foreach ($adatok as $azonosito => $adat) : ?>
                                    <tr style="border-top: 1px solid rgb(200, 200, 200)">
                                        <?php if (!in_array($megnevezes, $seen)) : ?>
                                            <td><?= $megnevezes ?></td>
                                            <?php foreach ($adat as $key => $val) : ?>
                                                <?php if ($key != "pdf") : ?>
                                                    <?php if ($key == "szamla_szamlaszam") : ?>
                                                        <?php $pdf = $adat["pdf"] ?>

                                                        <?php if (is_file($pdf)) : ?>
                                                            <td><a href="<?= $pdf ?>" target="_blank"><?= $val ?></a></td>
                                                        <?php else : ?>
                                                            <td><?= $val ?></td>
                                                        <?php endif; ?>
                                                    <?php else : ?>
                                                        <td><?= $val ?></td>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php $seen[] = $megnevezes; ?>

                                        <?php else : ?>

                                            <td></td> 
                                            <?php foreach ($adat as $key => $val) : ?>
                                                <?php if ($key != "pdf") : ?>
                                                    <?php if ($key == "szamla_szamlaszam") : ?>
                                                        <?php $pdf = $adat["pdf"] ?>
                                                        <?php if (is_file($pdf)) : ?>
                                                            <td><a href="<?= $pdf ?>" target="_blank"><?= $val ?></a></td>
                                                        <?php else : ?>
                                                            <td><?= $val ?></td>
                                                        <?php endif; ?>

                                                    <?php else : ?>
                                                        <td><?= $val ?></td>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                                <tr style="border-top: 1px solid rgb(200, 200, 200)">
                                    <td></td>
                                    <td><b><?= number_format($targyieszkoz_osszegzo[$megnevezes]["osszeg"],0,""," ") ?></b></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>