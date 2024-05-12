<?php
namespace Gerke\Imagetotext;
session_start();
require('oop/Connection.php');
require('oop/Szamla.php');
require('oop/Konyvelesitetel.php');

$connection = new Connection();
$sablonok = $connection->getData("SELECT * FROM sablon");
$feliratok;
/**
 * partnerek lekérése attól függően, hogy milyen típusú számlát módosítunk
 * szállító
 * vevő
 * pénztár
 * minden más esetben nincs partner az űrlapon
 */
if ($_SESSION["tipus"] == "szallito") {
    $szallitocegek = $connection->getData("SELECT * FROM partner WHERE vevo = 0 ORDER BY nev");
    $feliratok["partner"] = "Szállító";
} else if ($_SESSION["tipus"] == "vevo") {
    $szallitocegek = $connection->getData("SELECT * FROM partner WHERE vevo = 1 ORDER BY nev");
    $feliratok["partner"] = "Vevő";
} else {
    $szallitocegek = $connection->getData("SELECT * FROM partner ORDER BY nev");
    $feliratok["partner"] = "Szállító/Vevő";
}

if (isset($_SESSION["szamlamuvelet"]) && $_SESSION["szamlamuvelet"] == "modositas") {

    switch ($_SESSION["tipus"]) {
        case 'penztar':
            $modositandopenztarbejegyzes = $connection->getData("SELECT p.*, k.* FROM penztar p INNER JOIN konyvelesi_tetel k ON p.id = k.penztar_id WHERE p.id = '" . $_SESSION['szamla'] . "'");
            $szamla = new Szamla();
            $szamla->setTeljesites($modositandopenztarbejegyzes[0]["datum"]);
            $szamla->setMegjegyzes($modositandopenztarbejegyzes[0]["megjegyzes"]);
            $szamla->setSzamlaszam($modositandopenztarbejegyzes[0]["penztar_id"]);
            $szamla->setKonyveloID($modositandopenztarbejegyzes[0]["felhasznalo_id"]);
            $szamla->setCegID($modositandopenztarbejegyzes[0]["ceg_adoszam"]);
            $szamla->setSzamlaTipus(2);
            $osszegSzum = 0;
            foreach ($modositandopenztarbejegyzes as $index => $tetel) {
                $konyvelesi_tetel = new Konyvelesitetel($tetel["tartozik"], $tetel["kovetel"], $tetel["osszeg"]);
                $osszegSzum += $tetel["osszeg"];
                $konyvelesi_tetel->setId($tetel["id"]);
                $konyvelesi_tetel->setTeljesitesDatuma($szamla->getTeljesites());
                $konyvelesi_tetel->setPenztarId($tetel["penztar_id"]);
                $konyvelesi_tetel->setFelhasznaloId($szamla->getKonyveloID());
                $szamla->addKonyvelesiTetel($konyvelesi_tetel);
                unset($konyvelesi_tetel);
            }
            $szamla->setOsszeg($osszegSzum);
            break;
        default:
            $modositandoszamla = $connection->getData(
                "SELECT sz.*, k.*, p.nev AS partner_name 
            FROM szamla sz INNER JOIN konyvelesi_tetel k 
            ON sz.szamlaszam = k.szamla_szamlaszam 
            INNER JOIN partner p 
            ON sz.partner_adoszam = p.adoszam 
            WHERE sz.ceg_adoszam = '" . $_SESSION['cegadoszam'] . "' AND sz.szamlaszam = '" . $_SESSION['szamla'] . "'"
            );
            $szamla = new Szamla();
            foreach ($modositandoszamla as $szamlaindex => $szamlaadat) {
                if ($szamlaindex == 0) {
                    $szamla->setSzamlaszam($szamlaadat["szamlaszam"]);
                    $szamla->setTeljesites($szamlaadat["teljesites"]);
                    $szamla->setFizhat($szamlaadat["fizhat"]);
                    $szamla->setKiallitas($szamlaadat["kiallitas"]);
                    $szamla->setPartner($szamlaadat["partner_adoszam"]);
                    $szamla->setPenztar($szamlaadat["penztar"]);
                    $szamla->setMegjegyzes($szamlaadat["megjegyzes"]);
                    $szamla->setPdf($szamlaadat["pdf"]);
                    $szamla->setFizetve($szamlaadat["fizetve"]);
                    $szamla->setKonyveloID($szamlaadat["felhasznalo_id"]);
                    $szamla->setCegID($szamlaadat["ceg_adoszam"]);
                    $szamla->setSzamlaTipus(0);
                }
                $konyvelesi_tetel = new Konyvelesitetel($szamlaadat["tartozik"], $szamlaadat["kovetel"], $szamlaadat["osszeg"]);
                $konyvelesi_tetel->setId($szamlaadat["id"]);
                $konyvelesi_tetel->setBankSzamlaszam($szamlaadat["bank_szamlaszam"]);
                $konyvelesi_tetel->setPenztarId($szamlaadat["penztar_id"]);
                $konyvelesi_tetel->setEgyebId($szamlaadat["egyeb_id"]);
                $szamla->addKonyvelesiTetel($konyvelesi_tetel);
                unset($konyvelesi_tetel);
            }
            $szamla->calcSzamlaOsszeg();
            break;
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
    <title><?php if ($_SESSION["tipus"]  == "szallito") : ?>Szállító számla módosítás<?php else : ?>Vevő számla módosítása <?php endif; ?></title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="../js/fuggvenyek.js"></script>
</head>

<body>
    <?php include('navbar.php') ?>
    <div id="contentContainer" class="suppinvoice container mx-auto m-5 shadow" style="background-color: rgba(225, 230, 224);">
        <div class="row">
            <div class="col-1 pt-3">
                <a href="konyvelt_listak/szallitolist.php" class="btn btn-success btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col-10 mx-auto text-center pt-3 mt-3">
                <h3>Számla módosítás</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="p-4">
            <div class="row">
                <div id="columnChange" class="mx-auto">
                    <form action="" method="post" class="mt-4" name="szamla" id="form_1" enctype="multipart/form-data">
                        <div class="form-group invoiceregistration">
                            <?php if ($_SESSION["tipus"] == "penztar") : ?>
                                <div class="row">
                                    <div class="col form-check form-switch pb-3">
                                        <input class="form-check-input" type="checkbox" id="csakPenztar" name="csakPenztar" checked>
                                        <label class="form-check-label" for="flexSwitchCheckDefault">pénztár tétel rögzítés</label>
                                    </div>
                                    <div class="col"></div>
                                </div>
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-5" style="padding-right: 0;">
                                    <label for="szallito"><?= $feliratok["partner"]; ?></label><br>
                                    <select class="form-control banks " name="szallito" id="szallito">
                                        <?php foreach ($szallitocegek as $index => $szallitocegekadatok) : ?>
                                            <?php if ($szamla->getPartner() == $szallitocegekadatok["adoszam"]) : ?>
                                                <option value="<?php echo $szallitocegekadatok["adoszam"] ?>" checked><?php echo $szallitocegekadatok["nev"] ?></option>
                                            <?php else : ?>
                                                <option value="<?php echo $szallitocegekadatok["adoszam"] ?>"><?php echo $szallitocegekadatok["nev"] ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-1">
                                    <button type="button" class="btn btn-outline-dark mt-4" style="padding:6px;" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                        <i class="fa-solid fa-user-plus"></i>
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
                                                <label for="partner_nev">Partner neve:</label><br>
                                                <input type="text" class="form-control" id="partner_nev" name="partner_nev"><br>
                                                <div class="alert  text-center mt-3 hidden" role="alert" id="alertbox">
                                                    <strong></strong>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="bezaras">Mégse</button>
                                                <button type="button" class="btn btn-primary" onclick="savePartner()">Mentés</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="sorszam">Számla sorszáma</label><br>
                                    <input type="text" class="form-control basicData" id="sorszam" name="sorszam" value="<?= $szamla->getSzamlaszam() ?>"><br>
                                    <input type="text" class="form-control basicData" id="sorszam" name="old_sorszam" value="<?= $szamla->getSzamlaszam() ?>" hidden><br>
                                    <input type="text" class="form-control basicData" id="pdf" name="pdf" value="<?= $szamla->getPdf() ?>" hidden><br>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <label for="osszeg">Számla összege</label><br>
                                    <input type="text" class="form-control basicData osszegek" id="osszeg" name="osszeg" value="<?= $szamla->getOsszeg() ?>"><br>
                                </div>
                                <div class="col-6">
                                    <label for="telj">Teljesítés</label><br>
                                    <input type="text" class="form-control basicData" id="telj" name="telj" value="<?= $szamla->getTeljesites() ?>"><br>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <label for="kiall">Kiállítás</label><br>
                                    <input type="text" class="form-control basicData" id="kiall" name="kiall" value="<?= $szamla->getKiallitas() ?>"><br>
                                </div>
                                <div class="col-6">
                                    <label for="fizhat">Fizetési határidő:</label><br>
                                    <input type="text" class="form-control basicData" id="fizhat" name="fizhat" value="<?= $szamla->getFizhat() ?>"><br>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <label for="megj">Megjegyzés</label><br>
                                    <input type="text" class="form-control" id="megj" name="megj" value="<?= $szamla->getMegjegyzes() ?>"><br>
                                </div>
                                <div class="col"></div>
                            </div>
                        </div>
                    </form>
                    <hr style="margin-bottom: 25px;">
                    <form action="" method="post" name="konyveles" id="form_2" autocomplete="off">
                        <div class="row">
                            <div class="col form-group">
                                <label for="exampleFormControlSelect1">Sablon könyvelési tételek</label>
                                <select class="form-control banks" id="sablon" onchange="loadSablon();">
                                    <option name="default" value="-1" selected="selected">Válasszon</option>
                                    <?php foreach ($sablonok as $index => $sablonadatok) : ?>
                                        <option name="<?php echo $sablonadatok["megnevezes"] ?>" value="<?php echo $sablonadatok["megnevezes"] ?>"><?php echo $sablonadatok["megnevezes"] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <button type="button" id="addLineButton" onclick="addNewLine()" class="btn btn-outline-success mb-2 mt-5"><i class="fa-solid fa-plus"></i></button>
                        <div class="form-group" id="dynamicInputSection">
                            <?php foreach ($szamla->getKonyvelesiTetelek() as $index => $konyvelesitetel) : ?>
                                <div class="row mb-2 mt-2" id="inputRow_<?= $index ?>">
                                    <input type="text" value="<?= $konyvelesitetel->getId() ?>" name="kt_<?= $index ?>" hidden>
                                    <div class="col-4">
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon1">T</span>
                                            <select class="form-select" name="tartozik_<?= $index ?>" id="tartozik_<?= $index ?>">
                                                <option><?= $konyvelesitetel->getTartozik() ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon1">K</span>
                                            <select class="form-select" name="kovetel_<?= $index ?>" id="kovetel_<?= $index ?>">
                                                <option><?= $konyvelesitetel->getKovetel() ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon1">összeg</span>
                                            <input type="text" class="form-control" id="osszeg_<?= $index ?>" name="osszeg_<?= $index ?>" value="<?= $konyvelesitetel->getOsszeg() ?>">
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <div class="input-group">
                                            <button class="btn btn-outline-success deleteButton" style="font-size: smaller;margin-top:2px" id="trash_<?= $index ?>" onclick="deleteTetel(this.id)"><i class="fa-solid fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                        <div class="row mt-5">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1"> Teljes összeg</span>
                                    <input type="text" class="form-control" id="totalInput" placeholder="0" name="totalInput" oninput="checkInputs()" value="<?= $szamla->getOsszeg() ?>" disabled>
                                </div>
                            </div>
                            <div class="col"></div>
                            <div class="col"></div>
                        </div>
                    </form>
                    <div class="selectprevnext" style="display: visible;">
                        <div class="row mt-3">
                            <div class="col mt-3 text-end">
                                <div class="row pb-3">
                                    <div class="col">
                                        <button id="selectArea" class="btn btn-outline-success" onclick="selectArea()"><i class="fa-regular fa-object-ungroup mx-1"></i>Adat kijelölés</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col"></div>
                                    <div class="col">
                                        <button id="prevButton" class="btn btn-outline-success" onclick="switchPDF(-1)" disabled><i class="fa-solid fa-chevron-left mx-1"></i></button>
                                        <button id="nextButton" class="btn btn-outline-success" onclick="switchPDF(1)" disabled><i class="fa-solid fa-chevron-right mx-1"></i></button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-10 mt-4">
                                        <div class="alert alert-success text-center mt-3 hidden" role="alert" id="alertbox_szallito">
                                            <strong>Sikeres rögzítés!</strong>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <button id="konyvmentes" class="btn btn-success mt-5" onclick="collectFormsForMod();"><i class="fa-regular fa-floppy-disk mx-1"></i> Mentés</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($szamla->getPdf()) : ?>
                    <div class="col-7 text-center">
                        <canvas id="imageCanvas"></canvas>
                    </div>
                <?php endif; ?>
            </div>
            <div id="recognizedText"></div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            for (let index = 0; index < <?= count($szamla->getKonyvelesiTetelek()) ?>; index++) {
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
        $(document).ready(function() {
            if ($("#csakPenztar").is(":checked")) {
                $("#szallito").prop("disabled", true);
                $("#sorszam").prop("disabled", true);
                $("#kiall").prop("disabled", true);
                $("#fizhat").prop("disabled", true);
            }
            $("#csakPenztar").change(function() {
                if ($(this).is(":checked")) {
                    $("#szallito").prop("disabled", true);
                    $("#sorszam").prop("disabled", true);
                    $("#kiall").prop("disabled", true);
                    $("#fizhat").prop("disabled", true);
                } else {
                    $("#szallito").prop("disabled", false);
                    $("#sorszam").prop("disabled", false);
                    $("#kiall").prop("disabled", false);
                    $("#fizhat").prop("disabled", false);
                }
            });
        });
        <?php if ($szamla->getPdf()) : ?>
            let img = new Image();
            let canvas = document.getElementById('imageCanvas');
            let ctx = canvas.getContext('2d');
            let startX, startY, endX, endY;
            let isSelecting = false;
            let recognizedTextArray = [];
            let savedImageData;
            let pdfFiles = [];
            let currentPdfIndex = 0;

            $(document).ready(function() {
                createFile();
            });
        <?php endif; ?>

        $('#dynamicInputSection').on('keyup', '[id^="osszeg_"]', calculateTotal);
        <?php if ($szamla->getPdf()) : ?>

            async function createFile() {
                let response;
                let data;
                let metadata;
                let file;
                response = await fetch('<?php echo $szamla->getPdf() ?>');
                data = await response.blob();
                metadata = {
                    type: 'application/pdf'
                };
                file = new File([data], "test.pdf", metadata);
                loadPDF(file);
                changeContentAfterUpload();
            }

            function loadPDF(file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const typedarray = new Uint8Array(e.target.result);
                    // PDF betöltés
                    pdfjsLib.getDocument({
                        data: typedarray
                    }).promise.then(function(pdf) {
                        pdf.getPage(1).then(function(page) {
                            // Canvas méretének beállítása
                            const canvas = document.getElementById('imageCanvas');
                            const context = canvas.getContext('2d');
                            const viewport = page.getViewport({
                                scale: 2.45
                            });

                            canvas.width = viewport.width;
                            canvas.height = viewport.height;
                            // PDF to img
                            page.render({
                                canvasContext: context,
                                viewport: viewport
                            }).promise.then(function() {
                                console.log('PDF rendered as image.');
                                ctx = canvas.getContext('2d');
                                increaseContrast(canvas, 2);
                                savedImageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                                img.src = canvas.toDataURL('image/png');
                            });
                        });
                    });
                };

                reader.readAsArrayBuffer(file);
            }

            function switchPDF(direction) {
                currentPdfIndex += direction;

                if (konyvelt_szamlak.includes(currentPdfIndex)) {
                    currentPdfIndex += direction;
                    loadPDF(pdfFiles[currentPdfIndex]);
                } else {
                    if (currentPdfIndex < 0) {
                        currentPdfIndex = pdfFiles.length - 1;
                    } else if (currentPdfIndex >= pdfFiles.length) {
                        currentPdfIndex = 0;
                    }
                    loadPDF(pdfFiles[currentPdfIndex]);
                }
                $('input').val('');
                $('select').prop('selectedIndex', 0);
            }

            function changeContentAfterUpload() {
                const contentContainer = document.getElementById('contentContainer');
                const columnChange = document.getElementById('columnChange');
                document.getElementById('selectArea').style.display = 'inline-block';

                contentContainer.className = "container-fluid mx-auto m-5 shadow";
                columnChange.className = "mx-auto col-5";
            }

            function selectArea() {
                canvas.addEventListener('mousedown', function(e) {
                    startX = e.clientX - canvas.getBoundingClientRect().left;
                    startY = e.clientY - canvas.getBoundingClientRect().top;
                    isSelecting = true;
                });

                let currentInputIndex = 0;

                const inputFields = document.querySelectorAll('.basicData');
                if (inputFields.length > 0) {
                    inputFields[0].style.border = '2px solid green';
                }

                canvas.addEventListener('mouseup', function(e) {
                    endX = e.clientX - canvas.getBoundingClientRect().left;
                    endY = e.clientY - canvas.getBoundingClientRect().top;
                    isSelecting = false;

                    // a kiválasztott adat elmentése változóba
                    if (startX !== undefined && startY !== undefined && endX !== undefined && endY !== undefined) {
                        const selectedData = ctx.getImageData(startX, startY, endX - startX, endY - startY);

                        // Tesseract.js 
                        Tesseract.recognize(selectedData, 'eng', {
                                logger: e => console.log(e),
                                tessedit_char_whitelist: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.,!?'
                            })
                            .then(out => {
                                let recognizedText = out.text;
                                recognizedTextArray.push(recognizedText);

                                // A felismert adat megjelenítése az inputban
                                const recognizedTextDiv = document.getElementById('recognizedText');

                                // dinamikus input update
                                const inputFields = document.querySelectorAll('.basicData');

                                // zöld border + dátum form
                                let number = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

                                if (inputFields[currentInputIndex]) {
                                    if (currentInputIndex < 1) {
                                        inputFields[currentInputIndex].value = recognizedText;
                                    } else if (currentInputIndex == 1) {
                                        if (recognizedText.includes(".00") || recognizedText.includes(",00")) {
                                            let i = recognizedText.indexOf(".00") || recognizedText.indexOf(",00");
                                            
                                            recognizedText = recognizedText.slice(0, i);
                                        }
                                        recognizedText = recognizedText.replace(/\D/gi, '');
                                        inputFields[currentInputIndex].value = parseInt(recognizedText);
                                    } else {
                                        recognizedText = recognizedText.replace(/\D/gi, '');
                                        console.log(recognizedText);
                                        let tmpArr = recognizedText.split('');
                                        tmpArr.splice(4, 0, '-');
                                        tmpArr.splice(7, 0, '-');

                                        inputFields[currentInputIndex].value = tmpArr.join('');
                                    }

                                    inputFields[currentInputIndex].style.border = '2px solid black';
                                }
                                // következő input focus
                                currentInputIndex++;
                                // Input reset
                                for (let i = 0; i < inputFields.length; i++) {
                                    if (i !== currentInputIndex) {
                                        inputFields[i].style.border = '1px solid black';
                                    }
                                }
                                // Következő input border update
                                if (currentInputIndex < inputFields.length) {
                                    inputFields[currentInputIndex].style.border = '2px solid green';
                                }
                            });
                    }
                });

                canvas.addEventListener('mousemove', function(e) {
                    if (isSelecting) {
                        endX = e.clientX - canvas.getBoundingClientRect().left;
                        endY = e.clientY - canvas.getBoundingClientRect().top;
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                        ctx.strokeStyle = 'green';
                        ctx.strokeRect(startX, startY, endX - startX, endY - startY);
                    }
                });

                canvas.addEventListener('click', function(e) {
                    clearSelection();
                });
            }

            function clearSelection() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                if (startX !== undefined && startY !== undefined && endX !== undefined && endY !== undefined) {
                    ctx.fillStyle = 'rgba(255, 255, 255, 0)';
                    ctx.fillRect(startX, startY, endX - startX, endY - startY);
                }
            }

            function increaseContrast(canvas, contrastFactor) {
                const context = canvas.getContext('2d');
                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                const data = imageData.data;

                for (let i = 0; i < data.length; i += 4) {
                    data[i] = (data[i] - 128) * contrastFactor + 64;
                    data[i + 1] = (data[i + 1] - 128) * contrastFactor + 64;
                    data[i + 2] = (data[i + 2] - 128) * contrastFactor + 64;
                }

                context.putImageData(imageData, 0, 0);
            }

        <?php endif; ?>
        let konyvelt_szamlak = [];

        function collectFormsForMod() {
            let form1 = $("#form_1");
            let form2 = $("#form_2");
            let forms = [];
            let tipus = "";
            forms[1] = getFormData(form1);
            forms[2] = getFormData(form2);
            let t = "<?= $_SESSION["tipus"] ?>";
            switch (t) {
                case "szallito":
                    tipus = "szallito";
                    break;
                case "vevo":
                    tipus = "vevo";
                    break;
                case "penztar":
                    tipus = "penztar";
                    break;
                case "penztarszamla":
                    tipus = "penztarszamla";
                    break;
                default:
                    break;
            }

            $.ajax({
                method: 'post',
                url: "inputkezeles.php",
                data: {
                    "dataForMod": forms,
                    "tipus": tipus,
                    "pdf": "<?php echo $szamla->getPdf(); ?>"
                },
                success: function(response) {
                    console.log(response);
                    if (response == "partnerMegadasaKotelezo") {
                        hiba("Partner megadása kötelező!", "alertbox_szallito");
                    }
                    if (response == "szamlaSorszamMegadasaKotelezo") {
                        hiba("Számla sorszáma megadása kötelező!", "alertbox_szallito");
                    }
                    if (response == "szamlaOsszegMegadasaKotelezo") {
                        hiba("Számla összeg megadása kötelező!", "alertbox_szallito");
                    }
                    if (response == "szamlaTeljMegadasaKotelezo") {
                        hiba("Teljesítés dátumának megadása kötelező!", "alertbox_szallito");
                    }
                    if (response == "szamlaKiallMegadasaKotelezo") {
                        hiba("Kiállítás dátumának megadása kötelező!", "alertbox_szallito");
                    }
                    if (response == "szamlaFizhatMegadasaKotelezo") {
                        hiba("Fizetési határidő dátumának megadása kötelező!", "alertbox_szallito");
                    }
                    if (response.includes("KonyvelesiTetelHIba_")) {
                        let tmp = response.split("_");
                        $('#inputRow_' + tmp[1]).css("background-color", "rgb(245, 208, 205)");
                        hiba("Nem található adat! (dátum/tartozik/követel/összeg) ", "alertbox_szallito");
                    }
                    if (response == "SzamlaFeltolteseSikertelen") {
                        hiba("Sikertelen számla mentés", "alertbox_szallito");
                    }
                    if (response == "SzamlaNemTalalhato") {
                        hiba("Nem található a kiválasztott számla", "alertbox_szallito");
                    }
                    if (response == "MindenMezoUres") {
                        hiba("Feltöltéshez legalább 1 könyvelési tétel megadása szükséges!", "alertbox_szallito");
                    }
                    if (response == "siker") {
                        if ($('#alertbox_szallito').hasClass("hidden")) {
                            $('#alertbox_szallito').removeClass("hidden");
                        } else {
                            $('#alertbox_szallito').removeClass("alert-danger");
                            $('#alertbox_szallito').addClass("alert-success");
                            $('#alertbox_szallito').text("Sikeres rögzítés");
                        }

                        <?php if ($_SESSION["szamla"] == "all") : ?>
                            konyvelt_szamlak.push(currentPdfIndex);
                            if (konyvelt_szamlak.length == pdfFiles.length) {
                                setTimeout(() => {
                                    $('#alertbox_szallito').addClass("hidden");
                                }, 1000);
                                setTimeout(() => {
                                    window.location.replace("konyvelt_listak/szallitolist.php");
                                }, 1000);

                            } else {

                                setTimeout(() => {
                                    $('#alertbox_szallito').addClass("hidden");
                                }, 1000);
                                setTimeout(() => {
                                    switchPDF(1);
                                }, 1000);

                            }
                        <?php else : ?>
                            setTimeout(() => {
                                $('#alertbox_szallito').addClass("hidden");
                            }, 1000);
                            setTimeout(() => {
                                if (sorszam == "-1") {
                                    window.location.replace("szamlarogzites.php");
                                } else {
                                    window.location.replace("konyvelt_listak/szallitolist.php");
                                }

                            }, 1000);

                        <?php endif; ?>
                    }
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