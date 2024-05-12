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
    <title>Új bankszámla rögzítése</title>
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
                <h3>Új bankszámla rögzítése</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="p-4">
            <form action="" method="post" class="mt-4" id="bankszamlarogzites_form" autocomplete="off">
                <div class="form-group invoiceregistration">
                    <div class="pb-3">
                        <h5>Bankszámlaszámok</h5>
                    </div>
                    <div class="pb-1">
                        <p><i class="fa-solid fa-circle-exclamation mx-1 text-warning" style="font-size:18px;"></i>Új bankszámlaszámot csak az aktuálisan kiválasztott céghez lehet rögzíteni!</p>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="bszszam">1. Számlaszám</label><br>
                            <input type="text" class="form-control basicData" id="bszszam_1" name="bszszam_1" value="" placeholder="00000000-00000000-00000000"><br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="bszszam">2. Számlaszám (opcionális)</label><br>
                            <input type="text" class="form-control basicData" id="bszszam_2" name="bszszam_2" value="" placeholder="00000000-00000000-00000000"><br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="bszszam">3. Számlaszám (opcionális)</label><br>
                            <input type="text" class="form-control basicData" id="bszszam_3" name="bszszam_3" value="" placeholder="00000000-00000000-00000000"><br>
                        </div>
                    </div>
                    <div class="mt-5">
                        <div class="row">
                            <div class="col-11"></div>
                            <div class="col-1">
                                <div class="btn btn-outline-dark" onclick="addSzamlaszamok()">Mentés</div>
                            </div>
                            <div class="col-8">
                                <div class="alert alert-success text-center hidden" role="alert" id="alertbox_bsz">
                                    <strong>Sikeres rögzítés!</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>

    </script>
</body>

</html>