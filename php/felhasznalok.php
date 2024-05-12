<?php

namespace Gerke\Imagetotext;

session_start();
require('oop/Connection.php');
$connection = new Connection();
$felhasznalolista = $connection->getData("SELECT * FROM felhasznalo ORDER BY aktiv DESC, nev ASC");

if (file_exists('../data/profile_information.json')) {
    $profilok = file_get_contents('../data/profile_information.json');
    $profilok = json_decode($profilok, true);
}

foreach ($felhasznalolista as &$felhasznalo) {
    if ($felhasznalo["admin"] == 1) {
        $felhasznalo["user_level_name"] = "Főkönyvelő/admin";
    } else {
        $felhasznalo["user_level_name"] = "Könyvelő";
    }
    $felhasznalo["profilkep"] = $profilok[$felhasznalo["email"]];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../style.css">
    <title>Felhasználók</title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <script src="../js/fuggvenyek.js"></script>
</head>

<body>
    <?php include('navbar.php') ?>
    <div id="" class="suppinvoice container mx-auto m-5 shadow" style="max-width:750px; background-color: rgba(225, 230, 224);">
        <div class="row pt-3">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col mx-auto text-center pt-3 mt-3">
                <h3>Felhasználók adatai</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="row mt-3">
            <div class="col-8"></div>
            <div class="col-4">
                <a href="felhasznalo_rogzites.php" class="btn btn-outline-dark mb-1">Új felhasználó rögzítése</a>
            </div>
        </div>
        <div class="row mx-auto mt-4">
            <?php foreach ($felhasznalolista as $ugyfel) : ?>
                <hr>
                <?php if ($ugyfel["aktiv"] == 1) : ?>
                    <div class="col-3 my-auto mb-3">
                        <img src="<?= "../images/profile_pictures/" . $ugyfel["profilkep"] ?>" style="width:150px; height:150px; border:2px solid grey">
                    </div>
                    <div class="col-6 mb-3">
                        <h5><?= $ugyfel["nev"] ?></h5>
                        <span><?= $ugyfel["email"] ?></span><br>
                        <span>Utolsó belépés dátuma: <?= $ugyfel["utolso_belepes"] ?></span><br>
                        <span><?= $ugyfel["user_level_name"] ?></span><br>
                        <div class="btn btn-outline-dark mt-1 mb-1" id="<?= $ugyfel["id"] ?>" onclick="inaktivalas(this.id)">Felhasználó inaktiválása</div>
                    </div>
                <?php else : ?>
                    <div class="col-3 my-auto mb-3"></div>
                    <div class="col-6 mb-3">
                        <h5 class="text-secondary"><?= $ugyfel["nev"] ?></h5>
                        <span class="text-secondary"><?= $ugyfel["email"] ?></span><br>
                        <span class="text-secondary">Utolsó belépés dátuma: <?= $ugyfel["utolso_belepes"] ?></span><br>
                        <span class="text-secondary"><?= $ugyfel["user_level_name"] ?></span><br>
                        <div class="btn btn-secondary mt-1 mb-1 disabled" id="<?= $ugyfel["id"] ?>" onclick="inaktivalas(this.id)">Inaktív felhasználó</div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <script>

        </script>
</body>

</html>