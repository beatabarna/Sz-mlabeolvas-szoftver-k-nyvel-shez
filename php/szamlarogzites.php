<?php

namespace Gerke\Imagetotext;

session_start();
require('oop/Connection.php');
require('oop/Szamla.php');
require('oop/Konyvelesitetel.php');

$connection = new Connection();
$sablonok = $connection->getData("SELECT * FROM sablon");
$szallitocegek = "";

$szamlaRogzitesValtozok = [
    "oldalCim" => "",
    "partner" => "",
    "utvonal" => ""
];
switch ($_SESSION["regType"]) {
    case "szallito":
        $szallitocegek = $connection->getData("SELECT * FROM partner WHERE vevo = 0 ORDER BY nev");
        $szamlaRogzitesValtozok = [
            "oldalCim" => "Szállítói számla rögzítés",
            "partner" => "Szállító*",
            "utvonal" => "../invoices/szallito/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/"
        ];
        break;
    case "vevo":
        $szallitocegek = $connection->getData("SELECT * FROM partner WHERE vevo = 1 ORDER BY nev");
        $szamlaRogzitesValtozok = [
            "oldalCim" => "Vevő számla rögzítés",
            "partner" => "Vevő*",
            "utvonal" => "../invoices/vevo/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/"
        ];
        break;
    case "penztar":
        $szallitocegek = $connection->getData("SELECT * FROM partner ORDER BY nev");
        $szamlaRogzitesValtozok = [
            "oldalCim" => "Pénztár rögzítés",
            "partner" => "Szállító/Vevő*",
            "utvonal" => "../invoices/penztar/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/"
        ];
        break;
    default:
        break;
};
$files = glob("../invoices/" . $_SESSION["regType"] . "/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/*");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../style.css">
    <title><?= $szamlaRogzitesValtozok["oldalCim"] ?></title>
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
                <a href="szamlak.php?type=<?= $_SESSION["regType"] ?>" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col-10 mx-auto text-center pt-3 mt-3">
                <h3><?= $szamlaRogzitesValtozok["oldalCim"] ?></h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="p-4">
            <div class="row">
                <div id="columnChange" class="mx-auto">
                    <form action="" method="post" class="mt-4" name="szamla" id="form_1" enctype="multipart/form-data">
                        <div class="form-group invoiceregistration">
                            <?php if ($_SESSION["regType"] == "penztar") : ?>
                                <div class="row">
                                    <div class="col form-check form-switch pb-3">
                                        <input class="form-check-input" type="checkbox" id="csakPenztar" name="csakPenztar">
                                        <label class="form-check-label" for="csakPenztar">pénztár tétel rögzítés</label>
                                    </div>
                                    <div class="col"></div>
                                </div>
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-5" style="padding-right: 0;">
                                    <label for="szallito"><?= $szamlaRogzitesValtozok["partner"] ?></label><br>
                                    <select class="form-control banks " name="szallito" id="szallito">
                                        <option value="-1">Válasszon</option>
                                        <?php foreach ($szallitocegek as $index => $szallitocegekadatok) : ?>
                                            <option value="<?php echo $szallitocegekadatok["adoszam"] ?>"><?php echo $szallitocegekadatok["nev"] ?></option>
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
                                                <label for="partner_adoszam">Partner Adószáma:</label><br>
                                                <input type="text" class="form-control" id="partner_adoszam" name="partner_adoszam"><br>
                                                <label for="partner_nev">Partner neve:</label><br>
                                                <input type="text" class="form-control" id="partner_nev" name="partner_nev"><br>
                                                <div class="col form-check form-switch pb-3">
                                                    <input class="form-check-input" type="checkbox" id="vevo" name="vevo">
                                                    <label class="form-check-label" for="vevo">Vevő</label>
                                                </div>
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
                                    <label for="sorszam">Számla sorszáma*</label><br>
                                    <input type="text" class="form-control basicData" id="sorszam" name="sorszam"><br>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <label for="osszeg">Számla összege*</label><br>
                                    <input type="text" class="form-control basicData osszegek" id="osszeg" name="osszeg"><br>
                                </div>
                                <div class="col-6">
                                    <label for="telj">Teljesítés*</label><br>
                                    <input type="text" class="form-control basicData" id="telj" name="telj"><br>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <label for="kiall">Kiállítás*</label><br>
                                    <input type="text" class="form-control basicData" id="kiall" name="kiall"><br>
                                </div>
                                <div class="col-6">
                                    <label for="fizhat">Fizetési határidő*</label><br>
                                    <input type="text" class="form-control basicData" id="fizhat" name="fizhat"><br>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <label for="megj">Megjegyzés</label><br>
                                    <input type="text" class="form-control" id="megj" name="megj"><br>
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
                            <div class="row mb-2 mt-2" id="inputRow_0">
                                <div class="col-4">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">T*</span>
                                        <select class="form-select" name="tartozik_0" id="tartozik_0">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">K*</span>
                                        <select class="form-select" name="kovetel_0" id="kovetel_0">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">összeg*</span>
                                        <input type="text" class="form-control" id="osszeg_0" name="osszeg_0">
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="input-group">
                                        <div class="btn btn-outline-danger deleteButton" style="font-size: smaller;margin-top:2px" id="trash_0" onclick="deleteTetel(this.id)"><i class="fa-solid fa-trash"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2 mt-2" id="inputRow_1">
                                <div class="col-4">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">T</span>
                                        <select class="form-select" name="tartozik_1" id="tartozik_1">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">K</span>
                                        <select class="form-select" name="kovetel_1" id="kovetel_1">
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon1">összeg</span>
                                        <input type="text" class="form-control" id="osszeg_1" name="osszeg_1">
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="input-group">
                                        <div class="btn btn-outline-danger deleteButton" style="font-size: smaller;margin-top:2px" id="trash_1" onclick="deleteTetel(this.id)"><i class="fa-solid fa-trash"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col">
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1"> Teljes összeg</span>
                                    <input type="text" class="form-control" id="totalInput" placeholder="0" name="totalInput" oninput="checkInputs()" disabled>
                                </div>
                            </div>
                            <div class="col"></div>
                            <div class="col"></div>
                        </div>
                    </form>
                    <div class="selectprevnext" style="display: visible;">
                        <div class="row mt-3">
                            <div class="col mt-3 text-end">
                                <?php if ($_SESSION["szamla"] == "all") : ?>
                                    <div class="row pb-3">
                                        <div class="col">
                                            <button id="selectArea" class="btn btn-outline-success" onclick="selectArea()"><i class="fa-regular fa-object-ungroup mx-1"></i>Adat kijelölés</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col"></div>
                                        <div class="col">
                                            <button id="prevButton" class="btn btn-outline-success" onclick="switchPDF(-1)"><i class="fa-solid fa-chevron-left mx-1"></i> </button>
                                            <button id="actualPDF" class="btn btn-outline-success" disabled></button>
                                            <button id="nextButton" class="btn btn-outline-success" onclick="switchPDF(1)"> <i class="fa-solid fa-chevron-right mx-1"></i></button>
                                        </div>
                                    </div>
                                <?php elseif ($_SESSION["szamla"] == "none") : ?>
                                    <div class="row pb-3">
                                        <div class="col">
                                            <button id="selectArea" class="btn btn-outline-success hidden" onclick="selectArea()" disabled><i class="fa-regular fa-object-ungroup mx-1"></i>Adat kijelölés</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col"></div>
                                        <div class="col">
                                            <button id="prevButton" class="btn btn-outline-success hidden" onclick="switchPDF(-1)" disabled><i class="fa-solid fa-chevron-left mx-1"></i></button>
                                            <button id="nextButton" class="btn btn-outline-success hidden" onclick="switchPDF(1)" disabled> <i class="fa-solid fa-chevron-right mx-1"></i></button>
                                        </div>
                                    </div>
                                <?php else : ?>
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
                                <?php endif; ?>
                                <div class="row">
                                    <div class="col-10 mt-4">
                                        <div class="alert alert-success text-center mt-3 hidden" role="alert" id="alertbox_szamla_rogzites">
                                            <strong>Sikeres rögzítés!</strong>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <button id="konyvmentes" class="btn btn-dark mt-5" onclick="collectForms();" disabled><i class="fa-regular fa-floppy-disk mx-1"></i> Mentés</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-7 text-center">
                    <canvas id="imageCanvas"></canvas>
                </div>
            </div>
            <div id="recognizedText"></div>
        </div>
        <footer>
            <p style="font-size: small;" class="pb-2">A *-gal megjelölt mezők kitöltése kötelező</p>
        </footer>
    </div>
    <script>
        $(document).ready(function() {
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
        let file_paths = [];
        <?php if ($_SESSION["szamla"]  != "none" && $_SESSION["szamla"]  != "only-penztar") : ?>
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

        <?php if ($_SESSION["szamla"]  != "none" && $_SESSION["szamla"]  != "only-penztar") : ?>
            async function createFile() {
                let response;
                let data;
                let metadata;
                let file;
                <?php if ($_SESSION["szamla"] == "all") : ?>
                    <?php $files_path = $szamlaRogzitesValtozok["utvonal"] . "*";  ?>
                    <?php $files = glob($files_path);  ?>
                    <?php foreach ($files as $file) : ?>
                        response = await fetch('<?php echo $file ?>');
                        data = await response.blob();
                        metadata = {
                            type: 'application/pdf'
                        };
                        fname=Math.random() * 100;
                        file = new File([data], fname+".pdf", metadata);
                        pdfFiles.push(file);
                        file_paths.push('<?php echo $file ?>');
                    <?php endforeach; ?>
                    currentPdfIndex = 0;
                    loadPDF(pdfFiles[currentPdfIndex]);
                    changeContentAfterUpload();
                    $('#actualPDF').text((currentPdfIndex+1)+"/"+pdfFiles.length);
                <?php else : ?>
                    response = await fetch('<?php echo $_SESSION["szamla"] ?>');
                    data = await response.blob();
                    metadata = {
                        type: 'application/pdf'
                    };
                    file = new File([data], "test.pdf", metadata);
                    loadPDF(file);
                    changeContentAfterUpload();
                <?php endif; ?>
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
                $('#actualPDF').text((currentPdfIndex+1)+"/"+pdfFiles.length);
                if (konyvelt_szamlak.includes(currentPdfIndex)) {
                    currentPdfIndex += direction;
                    $('#actualPDF').text((currentPdfIndex+1)+"/"+pdfFiles.length);
                    loadPDF(pdfFiles[currentPdfIndex]);
                } else {
                    if (currentPdfIndex < 0) {
                        currentPdfIndex = pdfFiles.length - 1;
                        $('#actualPDF').text((currentPdfIndex+1)+"/"+pdfFiles.length);
                    } else if (currentPdfIndex >= pdfFiles.length) {
                        currentPdfIndex = 0;
                        $('#actualPDF').text((currentPdfIndex+1)+"/"+pdfFiles.length);
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

                    if (startX !== undefined && startY !== undefined && endX !== undefined && endY !== undefined) {
                        const selectedData = ctx.getImageData(startX, startY, endX - startX, endY - startY);

                        Tesseract.recognize(selectedData, 'eng', {
                                logger: e => console.log(e),
                                tessedit_char_whitelist: 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.,!?'
                            })
                            .then(out => {
                                let recognizedText = out.text;
                                recognizedTextArray.push(recognizedText);
                                const recognizedTextDiv = document.getElementById('recognizedText');
                                const inputFields = document.querySelectorAll('.basicData');
                                let number = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

                                if (inputFields[currentInputIndex]) {
                                    if (currentInputIndex < 1) {
                                        inputFields[currentInputIndex].value = recognizedText;
                                    } else if (currentInputIndex == 1) {
                                        
                                        if (recognizedText.includes(".00") || recognizedText.includes(",00")) {
                                            let i = recognizedText.indexOf(".00") !== -1 ? recognizedText.indexOf(".00") : recognizedText.indexOf(",00");
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
                                currentInputIndex++;

                                for (let i = 0; i < inputFields.length; i++) {
                                    if (i !== currentInputIndex) {
                                        inputFields[i].style.border = '1px solid black';
                                    }
                                }

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

        function collectForms() {
            let form1 = $("#form_1");
            let form2 = $("#form_2");
            let forms = [];
            forms[1] = getFormData(form1);
            forms[2] = getFormData(form2);
            let sorszam = 0;
            <?php if ($_SESSION["szamla"] == "none") : ?>
                sorszam = "-1";
            <?php else : ?>
                sorszam = currentPdfIndex;
            <?php endif; ?>
            $.ajax({
                method: 'post',
                url: "inputkezeles.php",
                data: {
                    "data": forms,
                    "tipus": "<?= $_SESSION["regType"] ?>",
                    "szamlasorszama": sorszam,
                    "filename" : file_paths[sorszam]
                },
                success: function(response) {
                    console.log(response);
                    if (response == "partnerMegadasaKotelezo") {
                        hiba("Partner megadása kötelező!", "alertbox_szamla_rogzites");
                    }
                    if (response == "szamlaSorszamMegadasaKotelezo") {
                        hiba("Számla sorszáma megadása kötelező!", "alertbox_szamla_rogzites");
                    }
                    if (response == "szamlaOsszegMegadasaKotelezo") {
                        hiba("Számla összeg megadása kötelező!", "alertbox_szamla_rogzites");
                    }
                    if (response == "szamlaTeljMegadasaKotelezo") {
                        hiba("Teljesítés dátumának megadása kötelező!", "alertbox_szamla_rogzites");
                    }
                    if (response == "szamlaKiallMegadasaKotelezo") {
                        hiba("Kiállítás dátumának megadása kötelező!", "alertbox_szamla_rogzites");
                    }
                    if (response == "szamlaFizhatMegadasaKotelezo") {
                        hiba("Fizetési határidő dátumának megadása kötelező!", "alertbox_szamla_rogzites");
                    }
                    if (response.includes("KonyvelesiTetelHIba_")) {
                        let tmp = response.split("_");
                        $('#inputRow_' + tmp[1]).css("background-color", "rgb(245, 208, 205)");
                        hiba("Nem található adat! (dátum/tartozik/követel/összeg) ", "alertbox_szamla_rogzites");
                    }
                    if (response == "szamlaFeltolteseSikertelen") {
                        hiba("Sikertelen számla mentés", "alertbox_szamla_rogzites");
                    }
                    if (response == "szamlaNemTalalhato") {
                        hiba("Nem található a kiválasztott számla", "alertbox_szamla_rogzites");
                    }
                    if (response == "mindenMezoUres") {
                        hiba("Feltöltéshez legalább 1 könyvelési tétel megadása szükséges!", "alertbox_szamla_rogzites");
                    }
                    if (response == "szamlaDuplikacio") {
                        hiba("Ezzel a számlaszámmal már létezik számla az adatbázisban!", "alertbox_szamla_rogzites");
                    }
                    if (response == "siker") {
                        if ($('#alertbox_szamla_rogzites').hasClass("hidden")) {
                            $('#alertbox_szamla_rogzites').removeClass("hidden");
                        } else {
                            $('#alertbox_szamla_rogzites').removeClass("alert-danger");
                            $('#alertbox_szamla_rogzites').addClass("alert-success");
                            $('#alertbox_szamla_rogzites').text("Sikeres rögzítés");
                        }

                        <?php if ($_SESSION["szamla"] == "all") : ?>
                            konyvelt_szamlak.push(currentPdfIndex);
                            if (konyvelt_szamlak.length == pdfFiles.length) {
                                setTimeout(() => {
                                    $('#alertbox_szamla_rogzites').addClass("hidden");
                                }, 1000);
                                setTimeout(() => {
                                    window.location.replace("szamlak.php?type=<?= $_SESSION["regType"] ?>");
                                }, 1000);

                            } else {

                                setTimeout(() => {
                                    $('#alertbox_szamla_rogzites').addClass("hidden");
                                }, 1000);
                                setTimeout(() => {
                                    switchPDF(1);
                                }, 1000);

                            }
                        <?php else : ?>
                            setTimeout(() => {
                                $('#alertbox_szamla_rogzites').addClass("hidden");
                            }, 1000);
                            setTimeout(() => {
                                if (sorszam == "-1") {
                                    window.location.replace("szamlarogzites.php");
                                } else {
                                    window.location.replace("szamlak.php?type=<?= $_SESSION["regType"] ?>");
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