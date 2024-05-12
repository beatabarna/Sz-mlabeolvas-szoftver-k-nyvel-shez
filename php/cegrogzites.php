<?php

namespace Gerke\Imagetotext;

session_start();
require('oop/Connection.php');
$connection = new Connection();
$ugyfellista = $connection->getData("SELECT * FROM ceg");
if (isset($_SESSION["ceg_adoszam_modositasra"])) {
    $params[":adoszam"] = $_SESSION["ceg_adoszam_modositasra"];
    $cegadatok = $connection->getData("SELECT * FROM ceg WHERE adoszam = :adoszam", $params);
    $cegadatok = $cegadatok[0];
    $cim = explode(",", $cegadatok["cim"]);
    $cimreszletek = explode(" ", $cim[2]);
    $cegadatok["irsz"] = $cim[0];
    $cegadatok["varos"] = $cim[1];
    $cegadatok["utca"] = $cimreszletek[0];
    $cegadatok["jelleg"] = $cimreszletek[1];
    $cegadatok["hazszam"] = $cimreszletek[2];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../style.css">
    <title>Új cég rögzítése</title>
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
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link text-black" aria-current="page" href="ugyfelek.php">Partnerek</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active text-secondary border-bottom-0 border-secondary" href="cegrogzites.php">Cég rögzítése</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-black" href="cegek.php">Cégek</a>
            </li>
        </ul>
        <div class="row pt-3">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col mx-auto text-center pt-3 mt-3">
                <h3>Új cég rögzítése könyvelésre</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="p-4">
            <form action="" method="post" class="mt-4" id="cegrogzites_form" autocomplete="off">
                <div class="form-group invoiceregistration">
                    <div>
                        <h5>Cégadatok</h5>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="cegnev">Név</label><br>
                            <?php if (isset($cegadatok["nev"])) : ?>
                                <input type="text" class="form-control basicData" id="cegnev" name="cegnev" value="<?= $cegadatok["nev"] ?>"><br>
                            <?php else : ?>
                                <input type="text" class="form-control basicData" id="cegnev" name="cegnev"><br>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="adoszam">Adószám</label><br>
                            <?php if (isset($cegadatok["adoszam"])) : ?>
                                <input type="text" class="form-control basicData" id="adoszam" name="cegadoszam" value="<?= $cegadatok["adoszam"] ?>"><br>
                            <?php else : ?>
                                <input type="text" class="form-control basicData" id="adoszam" name="cegadoszam"><br>
                            <?php endif; ?>
                        </div>
                        <div class="col">
                            <label for="cegelerhetoseg">Elérhetőség:</label><br>
                            <?php if (isset($cegadatok["elerhetoseg"])) : ?>
                                <input type="text" class="form-control basicData" id="cegelerhetoseg" name="cegelerhetoseg" value="<?= $cegadatok["elerhetoseg"] ?>"><br>

                            <?php else : ?>
                                <input type="text" class="form-control basicData" id="cegelerhetoseg" name="cegelerhetoseg"><br>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <h5>Cím</h5>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <label for="cegirsz">Irányítószám</label><br>
                            <?php if (isset($cegadatok["irsz"])) : ?>
                                <input type="text" class="form-control basicData" id="cegirsz" name="cegirsz" value="<?= $cegadatok["irsz"] ?>"><br>
                            <?php else : ?>
                                <input type="text" class="form-control basicData" id="cegirsz" name="cegirsz"><br>
                            <?php endif; ?>
                        </div>
                        <div class="col">
                            <label for="cegvaros">Város</label><br>
                            <?php if (isset($cegadatok["varos"])) : ?>
                                <input type="text" class="form-control basicData" id="cegvaros" name="cegvaros" value="<?= $cegadatok["varos"] ?>"><br>
                            <?php else : ?>
                                <input type="text" class="form-control basicData" id="cegvaros" name="cegvaros"><br>
                            <?php endif; ?>
                        </div>
                        <div class="col">
                            <label for="cegkozter">Közterület neve</label><br>
                            <?php if (isset($cegadatok["utca"])) : ?>
                                <input type="text" class="form-control basicData" id="cegkozter" name="cegkozter" value="<?= $cegadatok["utca"] ?>"><br>
                            <?php else : ?>
                                <input type="text" class="form-control basicData" id="cegkozter" name="cegkozter"><br>
                            <?php endif; ?>
                        </div>
                        <div class="col-2">
                            <label for="cegkozterjelleg">Közterület jellege</label><br>
                            <select class="form-control basicData" name="cegkozterjelleg">
                                <?php if (isset($cegadatok["jelleg"])) : ?>
                                    <option value="<?= $cegadatok["jelleg"] ?>"><?= $cegadatok["jelleg"] ?></option>
                                    <option value="ut">Út</option>
                                    <option value="utca">Utca</option>
                                    <option value="ter">Tér</option>
                                <?php else : ?>
                                    <option value="-1">Válassz</option>
                                    <option value="ut">Út</option>
                                    <option value="utca">Utca</option>
                                    <option value="ter">Tér</option>
                                <?php endif; ?>
                            </select>
                            <br>
                        </div>
                        <div class="col-2">
                            <label for="ceghazszamepulet">Házszám/Épület</label><br>
                            <?php if (isset($cegadatok["hazszam"])) : ?>
                                <input type="text" class="form-control basicData" id="ceghazszamepulet" name="ceghazszamepulet" value="<?= $cegadatok["hazszam"] ?>"><br>
                            <?php else : ?>
                                <input type="text" class="form-control basicData" id="ceghazszamepulet" name="ceghazszamepulet"><br>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="pt-5">
                        <h5>Áfa bevallás rendszeressége</h5>
                    </div>
                    <div class="row pt-3">
                        <div class="col">
                            <div class="form-check form-switch">
                                <?php if (isset($cegadatok["afabevallas"]) && $cegadatok["afabevallas"] == "havi") : ?>
                                    <input class="form-check-input" type="checkbox" id="havi" name="havi" checked>
                                <?php else : ?>
                                    <input class="form-check-input" type="checkbox" id="havi" name="havi">
                                <?php endif; ?>
                                <label class="form-check-label" for="havi">havi</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-switch">
                                <?php if (isset($cegadatok["afabevallas"]) && $cegadatok["afabevallas"] == "negyedeves") : ?>
                                    <input class="form-check-input" type="checkbox" id="negyedeves" name="negyedeves" checked>

                                <?php else : ?>
                                    <input class="form-check-input" type="checkbox" id="negyedeves" name="negyedeves">
                                <?php endif; ?>
                                <label class="form-check-label" for="negyedeves">negyedéves</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-switch">
                                <?php if (isset($cegadatok["afabevallas"]) && $cegadatok["afabevallas"] == "eves") : ?>
                                    <input class="form-check-input" type="checkbox" id="eves" name="eves" checked>
                                <?php else : ?>
                                    <input class="form-check-input" type="checkbox" id="eves" name="eves">
                                <?php endif; ?>
                                <label class="form-check-label" for="eves">éves</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-check form-switch">
                                <?php if (isset($cegadatok["afabevallas"]) && $cegadatok["afabevallas"] == "mentes") : ?>
                                    <input class="form-check-input" type="checkbox" id="mentes" name="mentes" checked>
                                <?php else : ?>
                                    <input class="form-check-input" type="checkbox" id="mentes" name="mentes">
                                <?php endif; ?>
                                <label class="form-check-label" for="mentes">mentes</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <div class="row">
                            <div class="col-11"></div>
                            <div class="col-1">
                                <?php if (isset($_SESSION["ceg_adoszam_modositasra"])) : ?>
                                    <div class="btn btn-outline-dark" onclick="collectModCegAdatok()">Mentés</div>
                                <?php else : ?>
                                    <div class="btn btn-outline-dark" onclick="collectUjCegAdatok()">Mentés</div>
                                <?php endif; ?>
                            </div>

                            <div class="col-8">
                                <div class="alert alert-success text-center hidden" role="alert" id="alertbox_ceg">
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
        $(document).ready(function() {
            $('.form-check-input').click(function() {
                $('.form-check-input').not(this).prop('disabled', this.checked);
            });
        });
    </script>
</body>

</html>