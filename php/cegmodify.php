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
    <title>File Upload and Selection</title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
</head>

<body>
    <div id="contentContainer" class="suppinvoice container mx-auto m-5 shadow" style="background-color: rgba(225, 230, 224);">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" aria-current="page" href="ugyfelek.php">Partnerek</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cegek.php">Cégek</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="cegrogzites.php">Cég rögzítése</a>
            </li>
        </ul>
        <div class="row pt-3">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-success btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col mx-auto text-center pt-3 mt-3">
                <h3>Új cég rögzítése könyvelésre</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="p-4">
            <form action="" method="post" class="mt-4">
                <div class="form-group invoiceregistration">
                    <div class="row">
                        <div class="col">
                            <label for="szallito">Név</label><br>
                            <input type="text" class="form-control basicData" id="" name="nev"><br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="szallito">Adószám</label><br>
                            <input type="text" class="form-control basicData" id="adoszam" name="adoszam"><br>
                        </div>
                        <div class="col">
                            <label for="szallito">Elérhetőség:</label><br>
                            <input type="text" class="form-control basicData" id="" name="elerhetoseg"><br>
                        </div>
                    </div>
                    <div>
                        <h5>Cím</h5>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <label for="szallito">Irányítószám</label><br>
                            <input type="text" class="form-control basicData" id="" name="nev"><br>
                        </div>
                        <div class="col">
                            <label for="szallito">Város</label><br>
                            <input type="text" class="form-control basicData" id="adoszam" name="adoszam"><br>
                        </div>
                        <div class="col">
                            <label for="szallito">Közterület neve</label><br>
                            <input type="text" class="form-control basicData" id="" name="elerhetoseg"><br>
                        </div>
                        <div class="col-2">
                            <label for="szallito">Közterület jellege</label><br>
                            <input type="text" class="form-control basicData" id="" name="elerhetoseg"><br>
                        </div>

                        <div class="col-2">
                            <label for="szallito">Házszám/Épület</label><br>
                            <input type="text" class="form-control basicData" id="" name="elerhetoseg"><br>
                        </div>
                    </div>
                    <div class="pt-5">
                        <h5>Áfa bevallás rendszeressége</h5>
                    </div>
                    <div class="row pt-3">
                        <div class="col">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="havi">
                                <label class="form-check-label" for="flexSwitchCheckDefault">havi</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="negyedeves">
                                <label class="form-check-label" for="flexSwitchCheckDefault">negyedéves</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="eves">
                                <label class="form-check-label" for="flexSwitchCheckDefault">éves</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="mentes">
                                <label class="form-check-label" for="flexSwitchCheckDefault">mentes</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <button class="btn btn-outline-success">Mentés</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function(){
        $('.form-check-input').click(function(){
            $('.form-check-input').not(this).prop('disabled', this.checked);
        });
    });
    </script>
</body>

</html>