<?php

namespace Gerke\Imagetotext;

session_start();
require('oop/Connection.php');
$connection = new Connection();
$sablonlista = $connection->getData("SELECT megnevezes, tartozik, kovetel FROM sablon");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../style.css">
    <title>Könyvelési sablon</title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <script src="../js/fuggvenyek.js"></script>
</head>

<body>
    <?php include('navbar.php') ?>
    <div id="" class="container mx-auto m-5 shadow" style="background-color: rgba(225, 230, 224);">
        <div class="p-4">
            <div class="row listainput">
                <div class="col">
                    <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
                </div>
                <div class="col-10">
                    <button type="button" class="btn btn-outline-dark mb-3 float-end" style="padding:6px;" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                        <i class="fa-solid fa-file-invoice me-1"></i> Új könyvelési sablon
                    </button>
                </div>
                <div class="row">
                    <div class="col mx-auto text-center pt-3 mt-3 mb-4">
                        <h3>Rögzített sablonok</h3>
                    </div>
                </div>
                <div id="columnChange" class="mx-auto w-75">
                    <div class="row">
                        <div class="alert  text-center mt-3 hidden" role="alert" id="alertbox_konyvelesitetel">
                            <strong></strong>
                        </div>
                        <table class="rounded" style="background-color: rgb(108, 117, 125); color:white">
                            <thead class="text-center">
                                <th class="pb-2 pt-2">Megnevezés</th>
                                <th class="pb-2 pt-2">Tartozik</th>
                                <th class="pb-2 pt-2">Követel</th>
                                <th class="pb-2 pt-2">Műveletek</th>
                            </thead>
                            <tbody class="text-center">
                                <?php foreach ($sablonlista as $sablonok) : ?>
                                    <tr>
                                        <td class="text-center">
                                            <input type="text" class="border-0 text-center" name="megnevezes" id="megnevezes-<?= $sablonok["megnevezes"] ?>" value="<?= $sablonok["megnevezes"] ?>" disabled>
                                        </td>
                                        <input type="text" class="border-0" name="old_nev" id="old_megnevezes-<?= $sablonok["megnevezes"] ?>" value="<?= $sablonok["megnevezes"] ?>" hidden>
                                        <td class="text-center">
                                            <input type="text" class="border-0 text-center" name="tartozik" id="tartozik-<?= $sablonok["megnevezes"] ?>" value="<?= $sablonok["tartozik"] ?>" disabled>
                                        </td>
                                        <td class="text-center">
                                            <input type="text" class="border-0 text-center" name="kovetel" id="kovetel-<?= $sablonok["megnevezes"] ?>" value="<?= $sablonok["kovetel"] ?>" disabled>
                                        </td>
                                        <td><button class="btn btn-outline-light border m-2" style="font-size: smaller;" id="szerkeszt-<?= $sablonok["megnevezes"] ?>" onclick="editKonyvelesitetelSablon(this.id)"><i class="fa-solid fa-wrench"></i></button></td>
                                        <td><button class="btn btn-outline-light border m-2" style="font-size: smaller;" id="torol-<?= $sablonok["megnevezes"] ?>" onclick="editKonyvelesitetelSablon(this.id)"><i class="fa-solid fa-trash"></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-lg fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Új könyvelési sablon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" class="mt-4">
                        <div class="form-group ">
                            <div class="row mb-2">
                                <div class="input-group">
                                    <span class="input-group-text col-2" id="basic-addon1">Megnevezés</span>
                                    <input type="text" class="form-control" id="megnevezes" name="megnevezes">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <div class="input-group">
                                        <span class="input-group-text col-2" id="basic-addon1">Tartozik</span>
                                        <select class="form-select" name="tartozik_0" id="tartozik_0">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <div class="input-group">
                                        <span class="input-group-text col-2" id="basic-addon1">Követel</span>
                                        <select class="form-select" name="kovetel_0" id="kovetel_0">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="alert  text-center mt-3 hidden" role="alert" id="alertbox">
                        <strong></strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="bezaras">Mégse</button>
                    <button type="button" class="btn btn-success" onclick="saveUjKonyvelesiSablon()">Mentés</button>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    $(function() {
        $('#megnevezes').on('keypress', function(e) {
            if (e.which == 32) {
                console.log('Space Detected');
                return false;
            }
        });
    });

    $(document).ready(function() {
        for (let index = 0; index < 2; index++) {
            generateOptions()
                .then(dataOptions => {
                    let selectTartozik = $('#tartozik_' + index);
                    let selectKovetel = $('#kovetel_' + index);
                    dataOptions.forEach(o => {
                        selectTartozik.append(`<option value="${o.number}">${o.number} - ${o.name}</option>`);
                        selectKovetel.append(`<option value="${o.number}">${o.number} - ${o.name}</option>`);
                    })

                })
                .catch(error => {
                    console.error(error);
                })
        }
    });
</script>

</html>