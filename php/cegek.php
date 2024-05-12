<?php

namespace Gerke\Imagetotext;

session_start();
require('oop/Connection.php');
$connection = new Connection();
$ugyfellista = $connection->getData("SELECT * FROM ceg");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../style.css">
    <title>Cégek</title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <script src="../js/fuggvenyek.js"></script>
</head>

<body>
    <?php include('navbar.php') ?>
    <div id="contentContainer" class="suppinvoice container mx-auto m-5 shadow" style="max-width:750px; background-color: rgba(225, 230, 224);">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link text-black" aria-current="page" href="ugyfelek.php">Partnerek</a>
            </li>
            <?php if ($_SESSION["user_level"] == 1) : ?>
                <li class="nav-item">
                    <a class="nav-link text-black" href="cegrogzites.php">Cég rögzítése</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link active text-secondary border-bottom-0 border-secondary" href="cegek.php">Cégek</a>
            </li>
        </ul>
        <div class="row pt-3">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col mx-auto text-center pt-3 mt-3">
                <h3>Ügyfelek adatai</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="row mx-auto mt-5">
        <div class="">
            <p><i class="fa-solid fa-circle-exclamation mx-1 text-warning" style="font-size:18px;"></i>Az adatok módosításához admin jog szükséges</p>
        </div>
            <?php foreach ($ugyfellista as $ugyfel) : ?>
                <hr>
                <div class="col-2"></div>
                <div class="col-6 mb-3">
                    <h5><?= $ugyfel["nev"] ?></h5>
                    <span><?= $ugyfel["adoszam"] ?></span><br>
                    <span><?= $ugyfel["cim"] ?></span><br>
                    <span><?= $ugyfel["elerhetoseg"] ?></span><br>
                    <span">Áfabevallás rendszeressége: <?= $ugyfel["afabevallas"] ?></span>
                </div>
                <?php if ($_SESSION["user_level"] == 1) : ?>
                    <div class="col pt-5">
                        <button class="btn btn-outline-dark" id="<?= $ugyfel["adoszam"] ?>" onclick="cegModositas(this.id)"><i class="fa-solid fa-pen"></i></button>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <script>

    </script>
</body>

</html>