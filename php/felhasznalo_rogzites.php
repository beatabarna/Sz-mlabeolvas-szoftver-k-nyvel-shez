<?php

namespace Gerke\Imagetotext;

session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../style.css">
    <title>Új felhasználó rögzítése</title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <script src="../js/fuggvenyek.js"></script>
</head>

<body>
    <?php include('navbar.php') ?>
    <div id="contentContainer" class="suppinvoice container mx-auto m-5 shadow" style="background-color: rgba(225, 230, 224);">
        <div class="row pt-3">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col mx-auto text-center pt-3 mt-3">
                <h3>Új felhasználó rögzítése</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="p-4">
            <form action="" method="post" class="mt-4" id="felhasznalorogzites_form" autocomplete="off">
                <div class="form-group invoiceregistration">
                    <div>
                        <h5>Személyes adatok</h5>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="nev">Név</label><br>
                            <input type="text" class="form-control basicData" id="nev" name="nev"><br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="email">E-mail cím</label><br>
                            <input type="text" class="form-control basicData" id="email" name="email"><br>
                        </div>
                        <div class="col">
                            <label for="jelszo">Jelszó</label><br>
                            <input type="password" class="form-control basicData" id="jelszo" name="jelszo"><br>
                        </div>
                    </div>
                    <div class="pt-4">
                        <h5>Munkakör</h5>
                    </div>
                    <div class="row pt-3">
                        <div class="col">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="admin" name="admin">
                                <label class="form-check-label" for="admin">Főkönyvelő/ADMIN</label>
                            </div>
                        </div>
                        <div class="col"></div>
                        <div class="col"></div>
                        <div class="col"></div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-10">
                            <div class="alert alert-success text-center hidden" role="alert" id="alertbox_felhasznalo">
                                <strong>Sikeres rögzítés!</strong>
                            </div>
                        </div>
                        <div class="col-2 text-center">
                            <div class="btn btn-outline-dark float-end" onclick="collectUjFelhasznaloAdatok()">
                                <i class="fa-regular fa-floppy-disk"></i> Mentés
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $('#nev').on('change', function() {
            let str = $('#nev').val();
            $('#email').val(str.normalize('NFD').replace(/[\u0300-\u036f]/g, "").replace(/\s/g, '').toLowerCase() + '@novabooks.com');
        })
    </script>
</body>

</html>