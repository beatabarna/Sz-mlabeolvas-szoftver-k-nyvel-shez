<?php

namespace Gerke\Imagetotext;

session_start();
require('oop/Connection.php');
$connection = new Connection();
$partnerlista = $connection->getData("SELECT * FROM partner ORDER BY vevo, nev");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../style.css">
    <title>Ügyfelek</title>
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
                <a class="nav-link active text-secondary border-bottom-0 border-secondary" aria-current="page" href="ugyfelek.php">Partnerek</a>
            </li>
            <?php if ($_SESSION["user_level"] == 1) : ?>
                <li class="nav-item">
                    <a class="nav-link text-black" href="cegrogzites.php">Cég rögzítése</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link text-black" href="cegek.php">Cégek</a>
            </li>
        </ul>
        <div class="row pt-3">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col mx-auto text-center pt-1 mt-3">
                <h3>Partner rögzítés</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="row pt-2 ms-2 me-2">
            <div class="col-3">
                <button type="button" class="btn btn-outline-dark mt-4" style="padding:6px;" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                    <i class="fa-solid fa-user-plus mx-2"></i>Új partner rögzítése
                </button>
            </div>
            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Partner rögzítése</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label for="partner_adoszam">Partner Adószáma:</label><br>
                            <input type="text" class="form-control" id="partner_adoszam" name="partner_adoszam"><br>
                            <label for="partner_nev">Partner neve:</label><br>
                            <input type="text" class="form-control" id="partner_nev" name="partner_nev"><br>
                            <div class="col form-check form-switch pb-3">
                                <input class="form-check-input" type="checkbox" id="vevo_azonosito" name="vevo" value="yes">
                                <label class="form-check-label" for="vevo">Vevő</label>
                            </div>
                            <div class="alert  text-center mt-3 hidden" role="alert" id="alertbox">
                                <strong></strong>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="bezaras">Mégse</button>
                            <button type="button" class="btn btn-success" onclick="savePartner()">Mentés</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row mb-5">
            <div class="col mx-auto text-center pt-3 mt-3">
                <h3>Partnerek listázása</h3>
            </div>
        </div>
        <div class="row mb-3 me-1">
            <div class="col-8"></div>
            <div class="col-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control" id="partner_nev_kereses" onkeyup='kereses("partner_nev_kereses","partnertable")' placeholder="Név alapján...">
                </div>
            </div>
        </div>
        <div class="p-4">
            <div class="row listainput">
                <div id="columnChange" class="mx-auto">
                    <div class="row">
                        <div class="alert  text-center mt-3 hidden" role="alert" id="alertbox_partner">
                            <strong></strong>
                        </div>
                        <table class="rounded" style="background-color: rgb(108, 117, 125); color:white" id="partnertable">
                            <thead class="text-center">
                                <th class="pb-2 pt-2">Név</th>
                                <th class="pb-2 pt-2 ">Adószám</th>
                                <th class="pb-2 pt-2">Partner típusa</th>
                                <th class="pb-2 pt-2" colspan="2">Műveletek</th>
                            </thead>
                            <tbody class="text-center">
                                <?php foreach ($partnerlista as $partnerek) : ?>
                                    <tr>
                                        <td class="text-center">
                                            <input type="text" style="width: 100%" class="border-0 text-center" name="nev" id="nev_<?= $partnerek["adoszam"] ?>" value="<?= $partnerek["nev"] ?>" disabled>
                                        </td>
                                        <input type="text" class="border-0" name="old_nev" id="old_adoszam_<?= $partnerek["adoszam"] ?>" value="<?= $partnerek["adoszam"] ?>" hidden>
                                        <td class="text-center"><input type="text" class="border-0 text-center" name="adoszam" id="adoszam_<?= $partnerek["adoszam"] ?>" value="<?= $partnerek["adoszam"] ?>" disabled></td>
                                        <td class="">
                                            <?php if ($partnerek["vevo"] == 0) : ?>
                                                <input class="btn-check" type="radio" id="tipus_1_<?= $partnerek["adoszam"] ?>" name="partnertipus_<?= $partnerek["adoszam"] ?>" value="szallito" checked disabled>
                                                <label class="btn btn-secondary border-0" id="label_1_<?= $partnerek["adoszam"] ?>" for="tipus_1_<?= $partnerek["adoszam"] ?>">Szállító</label>
                                                <input class="btn-check" type="radio" id="tipus_2_<?= $partnerek["adoszam"] ?>" name="partnertipus_<?= $partnerek["adoszam"] ?>" value="vevo" disabled>
                                                <label class="btn btn-secondary border-0" id="label_2_<?= $partnerek["adoszam"] ?>" for="tipus_2_<?= $partnerek["adoszam"] ?>">Vevő</label>
                                            <?php else : ?>
                                                <input class="btn-check" type="radio" id="tipus_1_<?= $partnerek["adoszam"] ?>" name="partnertipus_<?= $partnerek["adoszam"] ?>" value="szallito" disabled>
                                                <label class="btn btn-secondary border-0" id="label_1_<?= $partnerek["adoszam"] ?>" for="tipus_1_<?= $partnerek["adoszam"] ?>">Szállító</label>
                                                <input class="btn-check" type="radio" id="tipus_2_<?= $partnerek["adoszam"] ?>" name="partnertipus_<?= $partnerek["adoszam"] ?>" value="vevo" checked disabled>
                                                <label class="btn btn-secondary border-0" id="label_2_<?= $partnerek["adoszam"] ?>" for="tipus_2_<?= $partnerek["adoszam"] ?>">Vevő</label>
                                            <?php endif; ?>
                                        </td>
                                        <td><button class="btn btn-outline-light border m-2" style="font-size: smaller;" id="szerkeszt_<?= $partnerek["adoszam"] ?>" onclick="editPartner(this.id)"><i class="fa-solid fa-wrench"></i></button></td>
                                        <td><button class="btn btn-outline-light border m-2" style="font-size: smaller;" id="torol_<?= $partnerek["adoszam"] ?>" onclick="editPartner(this.id)"><i class="fa-solid fa-trash"></i></button></td>
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