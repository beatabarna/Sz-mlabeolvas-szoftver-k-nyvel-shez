<?php

namespace Gerke\Imagetotext;

session_start();
require('oop/Connection.php');
$connection = new Connection();
$params = [
    ":cegadoszam" =>  $_SESSION["cegadoszam"]
];
$targyieszkozlista = $connection->getData("SELECT t.megnevezes, t.id FROM targyi_eszkoz t INNER JOIN szamla s ON t.szamla_szamlaszam = s.szamlaszam WHERE s.ceg_adoszam =:cegadoszam AND t.megjegyzes = 'eszköz' ORDER BY t.megnevezes", $params);
$sablonok = $connection->getData("SELECT * FROM sablon");
$szamlalista = $connection->getData("SELECT * FROM szamla WHERE ceg_adoszam = :cegadoszam", $params);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../style.css">
    <title>Értékcsökkenés</title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <script src="../js/fuggvenyek.js"></script>
</head>

<body>
    <?php include('navbar.php') ?>
    <div id="clientContainer" class="clientinvoice container mx-auto m-5 shadow" style="background-color: rgba(225, 230, 224);">
        <div class="row">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col-10 mx-auto text-center pt-3 mt-3">
                <h3>Értékcsökkenés rögzítése</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="p-4">
            <div class="row">
                <div id="columnChange" class="mx-auto">
                    <form id="form_1" action="" method="post" class="mt-4">
                        <div class="form-group invoiceregistration">
                            <div class="row">
                                <div class="col">
                                    <label for="datum">Dátum*</label><br>
                                    <input type="text" class="form-control basicData" id="datum" name="datum"><br>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="megnevezes">Tárgyi eszköz kiválasztása*</label>
                                        <select class="form-control banks" id="megnevezes" name="megnevezes">
                                            <option value="-1" selected>Válasszon</option>
                                            <?php foreach ($targyieszkozlista as $index => $targyieszkoz) : ?>

                                                <option value="<?php echo $targyieszkoz["megnevezes"] ?>"><?php echo $targyieszkoz["megnevezes"] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <hr style="margin-bottom: 25px;">

                    <form action="" method="post" name="konyvmentes" id="form_2">
                        <div class="row">
                            <div class="col form-group mb-3">
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
                                        <button class="btn btn-outline-danger deleteButton" style="font-size: smaller;margin-top:2px" id="trash_0" onclick="deleteTetel(this.id)"><i class="fa-solid fa-trash"></i></button>
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
                                        <button class="btn btn-outline-danger deleteButton" style="font-size: smaller;margin-top:2px" id="trash_1" onclick="deleteTetel(this.id)"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <div class="col-4">
                                <div class="input-group">
                                    <span class="input-group-text" id="basic-addon1"> teljes összeg</span>
                                    <input type="text" class="form-control" id="totalInput" placeholder="0" name="totalInput" disabled>
                                </div>
                            </div>
                            <div class="col"></div>
                            <div class="col"></div>
                        </div>
                    </form>
                    <div class="selectprevnext" style="display: visible;">
                        <div class="row mt-3">
                            <div class="col mt-3 text-end">
                                <div class="btn btn-outline-dark m-2" onclick="collectForms();"><i class="fa-regular fa-floppy-disk mx-1"></i>Könyvel</div>
                            </div>
                            <div class="alert alert-success text-center hidden" role="alert" id="alertbox_amortizacio">
                                <strong>Sikeres számlarögzítés!</strong>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <footer>
            <p style="font-size: small;" class="pb-2">A *-gal megjelölt mezők kitöltése kötelező</p>
        </footer>
    </div>

    </div>
    <script>
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

        $('#dynamicInputSection').on('keyup', '[id^="osszeg_"]', calculateTotalEgyeb);

        $('#dynamicInputSection').on('click', '.deleteButton', function() {
            $(this).closest('.row').remove();
            calculateTotal();
            updateDeleteButtonStatus();
        });

        function collectForms() {
            let form1 = $("#form_1");
            let form2 = $("#form_2");
            let forms = [];
            forms[1] = getFormData(form1);
            forms[2] = getFormData(form2);

            $.ajax({
                method: 'post',
                url: "inputkezeles.php",
                data: {
                    "data": forms,
                    "tipus": "amortizacio",
                },
                success: function(response) {
                    console.log(response);
                    if (response == "amortizacioMegnevezesNemLehetUres") {
                        hiba("Tárgyi eszköz választása kötelező!", "alertbox_amortizacio");
                    }
                    if (response == "amortizacioDatumNemLehetUres") {
                        hiba("Dátum megadása kötelező!", "alertbox_amortizacio");
                    }

                    if (response.includes("amortizacioKonyvelesiTetelHIba_")) {
                        let tmp = response.split("_");
                        $('#inputRow_' + tmp[1]).css("background-color", "rgb(245, 208, 205)");
                        hiba("Nem található adat! (dátum/tartozik/követel/összeg) ", "alertbox_amortizacio");
                    }
                    if (response == "amortizacioMindenMezoUres") {
                        hiba("Feltöltéshez legalább 1 könyvelési tétel megadása szükséges!", "alertbox_amortizacio");
                    }
                    if (response == "siker") {
                        if ($('#alertbox_amortizacio').hasClass("hidden")) {
                            $('#alertbox_amortizacio').removeClass("hidden");
                        } else {
                            $('#alertbox_amortizacio').removeClass("alert-danger");
                            $('#alertbox_amortizacio').addClass("alert-success");
                            $('#alertbox_amortizacio').text("Sikeres rögzítés");
                        }
                        setTimeout(() => {
                            $('#alertbox_amortizacio').addClass("hidden");
                        }, 2000);
                        setTimeout(() => {
                            window.location.replace("amortizacio.php");
                        }, 1000);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Hiba");
                    console.log(textStatus, errorThrown);
                }
            });
        }

        function getFormData(form) {
            var unindexed_array = form.serializeArray();
            var indexed_array = {};

            $.map(unindexed_array, function(n, i) {
                indexed_array[n['name']] = n['value'];
            });

            return indexed_array;
        }

        function loadSablon() {
            let url = 'inputkezeles.php';
            $.ajax({
                method: 'post',
                url: url,
                data: {
                    "sablon": document.getElementById("sablon").value
                },
                success: function(response) {
                    r = JSON.parse(response);
                    if (r != "no") {
                        let talaltunk_ureset = false;
                        while (!talaltunk_ureset) {
                            if (document.getElementById("tartozik_" + actualLine)) {
                                if (document.getElementById("tartozik_" + actualLine).value.length > 0 || document.getElementById("kovetel_" + actualLine).value.length > 0) {
                                    actualLine++;
                                } else {
                                    document.getElementById("tartozik_" + actualLine).value = r["tartozik"];
                                    document.getElementById("kovetel_" + actualLine).value = r["kovetel"];
                                    actualLine++;
                                    $('#sablon').prop('selectedIndex', 0);
                                    talaltunk_ureset = true;
                                }
                            } else {
                                addNewLine();
                                document.getElementById("tartozik_" + actualLine).value = r["tartozik"];
                                document.getElementById("kovetel_" + actualLine).value = r["kovetel"];
                                $('#sablon').prop('selectedIndex', 0);
                                talaltunk_ureset = true;
                            }

                        }
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