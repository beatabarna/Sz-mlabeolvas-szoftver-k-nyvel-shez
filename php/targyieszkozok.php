<?php
namespace Gerke\Imagetotext;
session_start();
require('oop/Connection.php');
$connection = new Connection();
$params = [
    ":cegadoszam" =>  $_SESSION["cegadoszam"]
];
$targyieszkozlista = $connection->getData("SELECT t.megnevezes, t.id FROM targyi_eszkoz t INNER JOIN szamla s ON t.szamla_szamlaszam = s.szamlaszam WHERE s.ceg_adoszam = :cegadoszam AND t.megjegyzes = 'eszköz' ORDER BY t.megnevezes", $params);
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
    <title>Tárgyi eszközök</title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <script src="../js/fuggvenyek.js"></script>
</head>

<body>
    <?php include('navbar.php')?>
    <div id="contentContainer" class="suppinvoice container mx-auto m-5 shadow" style="background-color: rgba(225, 230, 224);">
        <div class="row">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col-10 mx-auto text-center pt-3 mt-3">
                <h3>Tárgyi eszközök rögzítése</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="p-4">
            <div class="row">
                <div id="columnChange" class="mx-auto">
                    <div class="row">
                        <form action="" method="post" class="mt-4" id="form_0">
                            <div class="col form-check form-switch pb-3">
                                <input class="form-check-input" type="checkbox" id="erteknovekedes" name="erteknovekedes">
                                <label class="form-check-label" for="flexSwitchCheckDefault">Értéknövekedés rögzítés</label>
                            </div>
                        </form>
                        <form action="" method="post" class="mt-4" name="szamla" id="form_1">
                            <div class="form-group invoiceregistration">

                                <div class="row text-center" id="targyieszkozrogz" style="cursor: pointer;">
                                    <div class="col bg-secondary text-white targyieszkozok pt-3 rounded-top">
                                        <h5 class="">Új tárgyi eszköz rögzítés</h5>
                                    </div>
                                </div>
                                <div class="row bg-secondary text-white terogz pt-2">
                                    <div class="row">
                                        <div class="col">
                                            <button type="button" id="addLineButton" onclick="addNewLine()" class="btn btn-outline-light mb-3"><i class="fa-solid fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div id="dynamicInputSection">
                                        <div class="row" id="inputRow_0">
                                            <div class="row">
                                                <div class="col">
                                                    <label for="" class="text-white">Tárgyi eszköz megnevezése:*</label><br>
                                                    <input type="text" class="form-control" id="megnevezes_0" name="megnevezes_0"><br>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <label for="" class="text-white">Érték:*</label><br>
                                                <input type="text" class="form-control" id="ertek_0" name="ertek_0"><br>
                                            </div>
                                            <div class="col">
                                                <label for="h_ido_0" class="text-white">Várható használati idő:*</label><br>
                                                <input type="number" placeholder="években" class="form-control" id="h_ido_0" name="h_ido_0"><br>
                                            </div>
                                            <div class="col me-4">
                                                <div class="form-group">
                                                    <label for="szamla_0" class="text-white">Számla kiválasztása:*</label>
                                                    <select class="form-control banks" id="szamla_0" name="szamla_0">
                                                        <option>Válasszon</option>
                                                        <?php foreach ($szamlalista as $index => $szamla) : ?>
                                                            <option value="<?php echo $szamla["szamlaszam"] ?>"><?php echo $szamla["szamlaszam"] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="">
                                                <input type="text" value="eszköz" class="form-control" id="megjegyzes_0" name="megjegyzes_0" style="display: none;"><br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <form action="" method="post" class="mt-4 hidden" name="szamla" id="form_2">
                            <div class="form-group invoiceregistration">
                                <div class="row text-center" id="targyieszkozbeknov" style="cursor: pointer;">
                                    <div class="col bg-secondary text-white targyieszkozok pt-3 rounded-top">
                                        <h5 class="">Bekerülési érték növelés</h5>
                                    </div>
                                </div>
                                <div class="row bg-secondary text-white tebeknov pt-2">
                                    <div class="row">
                                        <div class="col">
                                            <button type="button" id="addLineButton" onclick="addNewLineBeknov()" class="btn btn-outline-light mb-3"><i class="fa-solid fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <div id="dynamicInputSectionBeknov">
                                        <div class="row" id="inputRowBeknov_0">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="targyieszkoz_0" class="text-white">Tárgyi eszköz kiválasztása:*</label>
                                                    <select class="form-control banks" id="targyieszkoz_0" name="targyieszkoz_0">
                                                        <option value="-1">Válasszon</option>
                                                        <?php foreach ($targyieszkozlista as $index => $targyieszkoz) : ?>

                                                            <option value="<?php echo $targyieszkoz["megnevezes"] ?>"><?php echo $targyieszkoz["megnevezes"] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <label for="targyieszk_erteknov_0" class="text-white">Érték:*</label><br>
                                                <input type="text" class="form-control" id="targyieszk_erteknov_0" name="targyieszk_erteknov_0"><br>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label for="targyieszk_erteknov_szamla_0" class="text-white">Számla kiválasztása:*</label>
                                                    <select class="form-control banks" id="targyieszk_erteknov_szamla_0" name="targyieszk_erteknov_szamla_0">
                                                        <option value=-1>Válasszon</option>
                                                        <?php foreach ($szamlalista as $index => $szamla) : ?>
                                                            <option value="<?php echo $szamla["szamlaszam"] ?>"><?php echo $szamla["szamlaszam"] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <label for="targyieszk_erteknov_megjegyzes_0" class="text-white">Megjegyzés:</label><br>
                                                <input type="text" class="form-control" id="targyieszk_erteknov_megjegyzes_0" name="targyieszk_erteknov_megjegyzes_0"><br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-10 mt-4">
                            <div class="alert alert-success text-center hidden" role="alert" id="alertbox_te">
                                <strong>Sikeres rögzítés!</strong>
                            </div>
                        </div>
                        <div class="col-2 text-center" style="padding-right: 0px;">
                            <div id="tementes" class="btn btn-secondary mt-5 float-end"  onclick="collectForms();"><i class="fa-regular fa-floppy-disk mx-1"></i> Mentés</div>
                        </div>
                    </div>
                </div>
            </div>
            <p style="font-size: smaller;">A *-al megjelölt mezők kitöltése kötelező</p>
        </div>
    </div>
    <script>
        let TElineCount = 0;

        function addNewLine() {
            TElineCount++;

            let newRow = $('#inputRow_0').clone();

            newRow.attr('id', 'inputRow_' + TElineCount);
            newRow.find('[id^="megnevezes_"]').attr('id', 'megnevezes_' + TElineCount).attr('name', 'megnevezes_' + TElineCount).val('');
            newRow.find('[id^="ertek_"]').attr('id', 'ertek_' + TElineCount).attr('name', 'ertek_' + TElineCount).val('');
            newRow.find('[id^="szamla_"]').attr('id', 'szamla_' + TElineCount).attr('name', 'szamla_' + TElineCount).val('');
            newRow.find('[id^="h_ido_"]').attr('id', 'h_ido_' + TElineCount).attr('name', 'h_ido_' + TElineCount).val('');
            newRow.find('[id^="megjegyzes_"]').attr('id', 'megjegyzes_' + TElineCount).attr('name', 'megjegyzes_' + TElineCount).val('');

            $('#dynamicInputSection').append(newRow);
        }

        let lineCountbeknov = 0;

        function addNewLineBeknov() {
            lineCountbeknov++;

            let newRow = $('#inputRowBeknov_0').clone();
            newRow.attr('id', 'inputRowBeknov_' + lineCountbeknov);
            newRow.find('[id^="targyieszkoz_"]').attr('id', 'targyieszkoz_' + lineCountbeknov).attr('name', 'targyieszkoz_' + lineCountbeknov).val('');
            newRow.find('[id^="targyieszk_erteknov_"]').attr('id', 'targyieszk_erteknov_' + lineCountbeknov).attr('name', 'targyieszk_erteknov_' + lineCountbeknov).val('');
            newRow.find('[id^="targyieszk_erteknovszamla_"]').attr('id', 'targyieszk_erteknovszamla_' + lineCountbeknov).attr('name', 'targyieszk_erteknovszamla_' + lineCountbeknov).val('');
            newRow.find('[id^="targyieszk_erteknovmegjegyzes_"]').attr('id', 'targyieszk_erteknovmegjegyzes_' + lineCountbeknov).attr('name', 'targyieszk_erteknovmegjegyzes_' + lineCountbeknov).val('');

            $('#dynamicInputSectionBeknov').append(newRow);
        }

        function collectForms() {
            let form0 = $("#form_0");
            let form1 = $("#form_1");
            let form2 = $("#form_2");
            let forms = [];
            forms[1] = getFormData(form1);
            forms[2] = getFormData(form2);
            forms[3] = getFormData(form0);
            $.ajax({
                method: 'post',
                url: "inputkezeles.php",
                data: {
                    "data": forms,
                    "tipus": "targyieszkoz",
                },
                success: function(response) {
                    console.log(response);
                    /* tárgyiezsköz rögzítés hibák */
                    if (response == "targyieszkozMegnevezesNemLehetUres") {
                        hiba("Megnevezés megadása kötelező!", "alertbox_te");
                    }
                    if (response == "targyieszkozErtekNemLehetUres") {
                        hiba("Érték megadása kötelező!", "alertbox_te");
                    }
                    if (response == "targyieszkozHIdoNemLehetUres") {
                        hiba("Használati idő megadása kötelező!", "alertbox_te");
                    }
                    if (response == "targyieszkozSzamladoNemLehetUres") {
                        hiba("Számla választása kötelező!", "alertbox_te");
                    }
                    /* értéknövelés hibák */
                    if (response == "targyieszkozValasztasNemLehetUres") {
                        hiba("Egy tárgyieszköz választása kötelező!", "alertbox_te");
                    }
                    if (response == "targyieszkozErteknovOsszegNemLehetUres") {
                        hiba("Érték megadása kötelező!", "alertbox_te");
                    }
                    if (response == "targyieszkozErteknovSzamlaNemLehetUres") {
                        hiba("Számla választása kötelező!", "alertbox_te");
                    }
                    /* minden mező üres */
                    if (response == "targyieszkozMindenMezoUres" || response == "targyieszkozErtekNovekedesMindenMezoUres") {
                        hiba("Feltöltéshez legalább 1 tárgyieszköz vagy Értéknövelés kitöltése kötelező!", "alertbox_te");
                    }
                    if (response == "siker") {
                        if ($('#alertbox_te').hasClass("hidden")) {
                            $('#alertbox_te').removeClass("hidden");
                        } else {
                            $('#alertbox_te').removeClass("alert-danger");
                            $('#alertbox_te').addClass("alert-success");
                            $('#alertbox_te').text("Sikeres rögzítés");
                        }
                        setTimeout(() => {
                            $('#alertbox_te').addClass("hidden");
                        }, 1000);
                        setTimeout(() => {
                            window.location.replace("targyieszkozok.php");
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
        $("#erteknovekedes").on("change", function() {
            if ($('#form_1').hasClass("hidden")) {
                $('#form_1').removeClass("hidden");
                $('#form_2').addClass("hidden");
            } else {
                $('#form_2').removeClass("hidden");
                $('#form_1').addClass("hidden");
            }
        });
    </script>
</body>

</html>