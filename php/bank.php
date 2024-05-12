<?php

namespace Gerke\Imagetotext;

session_start();
require('oop/Connection.php');
$connection = new Connection();
$params = [
    ":cegadoszam" =>  $_SESSION["cegadoszam"]
];
$sablonok = $connection->getData("SELECT * FROM sablon");
$bankszamlak = $connection->getData("SELECT * FROM bank WHERE ceg_adoszam = :cegadoszam", $params);
$szamlak = $connection->getData("SELECT szamlaszam FROM szamla WHERE ceg_adoszam = :cegadoszam AND fizetve = 0", $params);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../style.css">
    <title>Bank könyvelés</title>
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
        <div class="row">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col-10 mx-auto text-center pt-3 mt-3">
                <h3>Bank rögzítése</h3>
            </div>
            <div class="col-1"></div>
        </div>

        <div class="p-4">
            <div class="row">
                <div id="columnChange" class="mx-auto">
                    <form action="" method="post" id="form_1">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-7">
                                    <label for="exampleFormControlSelect1">Bank kiválasztása*</label>
                                    <select class="form-control banks" id="exampleFormControlSelect1" name="bankszamlaszam" required>
                                        <option value="-1">Válasszon</option>
                                        <?php foreach ($bankszamlak as $bankszamlaindex => $bankszamladatok) : ?>
                                            <option value="<?= $bankszamladatok["bankszamlaszam"] ?>"><?= $bankszamladatok["bankszamlaszam"] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr style="margin-bottom: 25px; margin-top: 25px;">
                    <form action="" method="post" name="konyveles" id="form_2">
                        <div class="row mt-3">
                            <div class="col-11 form-group">
                                <label for="exampleFormControlSelect1">Sablon könyvelési tételek</label>
                                <select class="form-control banks" id="sablon" onchange="loadSablon();">
                                    <option name="default" value="-1" selected="selected">Válasszon</option>
                                    <?php foreach ($sablonok as $index => $sablonadatok) : ?>
                                        <option name="<?php echo $sablonadatok["megnevezes"] ?>" value="<?php echo $sablonadatok["megnevezes"] ?>"><?php echo $sablonadatok["megnevezes"] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row mt-2">
                                <div class="col mt-2">
                                    <button type="button" id="addLineButton" onclick="addNewLineBank()" class="btn btn-outline-success mb-2 mt-5"><i class="fa-solid fa-plus"></i></button>
                                </div>
                                <div class="col"></div>
                            </div>
                            <div class="form-group mt-3" id="dynamicInputSection">
                                <div class="row mb-2 mt-2" id="inputRow_0">
                                    <div class="col-2">
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-calendar-days"></i>*</span>
                                            <input type="text" class="form-control" id="bank_datum_0" name="bank_datum_0">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <select class="form-control banks" id="szamlak_0" name="szamlak_0">
                                                <option name="default" value="-1" selected="selected">Válasszon</option>
                                                <?php foreach ($szamlak as $index => $szamlaadat) : ?>
                                                    <option name="<?php echo $szamlaadat["szamlaszam"] ?>" value="<?php echo $szamlaadat["szamlaszam"] ?>"><?php echo $szamlaadat["szamlaszam"] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon1">T*</span>
                                            <select class="form-select" name="tartozik_0" id="tartozik_0">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon1">K*</span>
                                            <select class="form-select" name="kovetel_0" id="kovetel_0">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
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
                                    <div class="col-2">
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-calendar-days"></i></span>
                                            <input type="text" class="form-control" id="bank_datum_1" name="bank_datum_1">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <select class="form-control banks" id="szamlak_1" name="szamlak_1">
                                                <option name="default" value="-1" selected="selected">Válasszon</option>
                                                <?php foreach ($szamlak as $index => $szamlaadat) : ?>
                                                    <option name="<?php echo $szamlaadat["szamlaszam"] ?>" value="<?php echo $szamlaadat["szamlaszam"] ?>"><?php echo $szamlaadat["szamlaszam"] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon1">T</span>
                                            <select class="form-select" name="tartozik_1" id="tartozik_1">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="input-group">
                                            <span class="input-group-text" id="basic-addon1">K</span>
                                            <select class="form-select" name="kovetel_1" id="kovetel_1">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-2">
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
                        </div>
                    </form>
                    <div class="row mt-5">
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text" id=""> teljes összeg</span>
                                <input type="text" class="form-control" id="totalInput" placeholder="0" name="totalInput" disabled>
                            </div>
                        </div>
                        <div class="col"></div>
                    </div>
                    <div class="selectprevnext" style="display: visible;">
                        <div class="row mt-3">
                            <div class="col-10 mt-4">
                                <div class="alert alert-success text-center mt-3 hidden" role="alert" id="alertbox">
                                    <strong>Sikeres rögzítés!</strong>
                                </div>
                            </div>
                            <div class="col-2 text-center">
                                <button id="konyvmentes" class="btn btn-outline-dark mt-5" onclick="collectForms();"><i class="fa-regular fa-floppy-disk mx-1"></i> Mentés</button>
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
                    "tipus": "bank",
                },
                success: function(response) {
                    console.log(response);
                    if (response == "bankszamlaNincsKivalasztva") {
                        hiba("Bankszámla kiválasztása kötelező!", "alertbox");
                    }
                    if (response.includes("bankKonyvelesiTetelHIba")) {
                        let tmp = response.split("_");
                        $('#inputRow_' + tmp[1]).css("background-color", "rgb(245, 208, 205)");
                        hiba("Nem található adat! (dátum/tartozik/követel/összeg) ", "alertbox");
                    }
                    if (response == "bankMindenMezoUres") {
                        hiba("Feltöltéshez legalább 1 könyvelési tétel megadása szükséges!", "alertbox");
                    }
                    if (response == "siker") {
                        if ($('#alertbox').hasClass("hidden")) {
                            $('#alertbox').removeClass("hidden");
                        } else {
                            $('#alertbox').removeClass("alert-danger");
                            $('#alertbox').addClass("alert-success");
                            $('#alertbox').text("Sikeres rögzítés");
                        }
                        $('#alertbox').removeClass("hidden");
                        setTimeout(() => {
                            $('#alertbox').addClass("hidden");
                        }, 1000);
                        setTimeout(() => {
                            window.location.replace("bank.php");
                        }, 1000);
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