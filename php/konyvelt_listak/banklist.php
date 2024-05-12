<?php

namespace Gerke\Imagetotext;

session_start();
require('../oop/Connection.php');
$connection = new Connection();
$params = [
    ":cegadoszam" =>  $_SESSION["cegadoszam"]
];
$banktetelek = $connection->getData("SELECT k.*, b.* 
                                    FROM konyvelesi_tetel k 
                                    INNER JOIN bank b 
                                    ON k.bank_szamlaszam = b.bankszamlaszam 
                                    WHERE b.ceg_adoszam = :cegadoszam 
                                    ORDER BY b.bankszamlaszam, k.datum", $params);

$evek = array();
foreach ($banktetelek as $index => $banktetel) {
    $tmp = explode("-", $banktetel["datum"]);
    if (!in_array($tmp[0], $evek)) {
        $evek[] = $tmp[0];
    }
}
$evek = array_reverse($evek);
$bankszamlak = array();
foreach ($banktetelek as $index => $banktetel) {
    if (!in_array($banktetel["bank_szamlaszam"], $bankszamlak)) {
        $bankszamlak[] = $banktetel["bank_szamlaszam"];
    }
}

$honapok = [
    "01" => "Január",
    "02" => "Február",
    "03" => "Március",
    "04" => "Április",
    "05" => "Május",
    "06" => "Június",
    "07" => "Július",
    "08" => "Augusztus",
    "09" => "Szeptember",
    "10" => "Október",
    "11" => "November",
    "12" => "December"
];

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
                    <a class="nav-link active text-secondary border-bottom-0 border-secondary" aria-current="page" href="banklist.php">Bank</a>
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
                <h3>Rögzített bank tételek</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="row w-100 pt-4 pb-4">
            <div class="col-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-building-columns"></i></span>
                    <select class="form-control" id="bankszamlaszam">
                        <?php foreach ($bankszamlak as $bsz) : ?>
                            <option value="<?= $bsz ?>"><?= $bsz ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-regular fa-calendar"></i></span>
                    <select class="form-control" id="evszam">
                        <?php foreach ($evek as $ev) : ?>
                            <option value="<?= $ev ?>"><?= $ev ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control" id="bank_megjegyzes" onkeyup='kereses("bank_megjegyzes","bank")' placeholder="Megjegyzés alapján...">
                </div>
            </div>
        </div>
        <div class="row pt-1 pb-1">
            <div class="col w-100">
                <div class="tab text-center">
                    <?php foreach ($honapok as $index => $nev) : ?>
                        <button class="btn btn-secondary col" onclick="getMonthData('<?= $index ?>')"><?= $nev ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <hr>
        <div class="col"></div>
        <div class="row ">
            <div id="columnChange" class="mx-auto">
                <div class="row" id="bankteteltablazat">
                </div>
            </div>
        </div>
    </div>
    <script>
        function getMonthData(honap) {
            let ev = $('#evszam').val();
            let bankszamlaszam = $('#bankszamlaszam').val();
            $.ajax({
                method: 'post',
                url: "../inputkezeles.php",
                data: {
                    "ev": ev,
                    "honap": honap,
                    "bankszamlaszam": bankszamlaszam,
                },
                success: function(response) {
                    console.log(response);
                    $('#bankteteltablazat').html(response);

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Hiba");
                    console.log(textStatus, errorThrown);
                }
            });
        }
    </script>
</body>

</html>