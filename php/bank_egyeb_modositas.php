<?php
namespace Gerke\Imagetotext;
session_start();
require('oop/Connection.php');
require('oop/Bank.php');
require('oop/Konyvelesitetel.php');
require_once('oop/Konyveles.php');
require('oop/Egyeb.php');

$connection = new Connection();
$sablonok = $connection->getData("SELECT * FROM sablon");
$feliratok = [
    "oldalcim" => ""
];
if (isset($_SESSION["szamlamuvelet"]) && $_SESSION["szamlamuvelet"] == "modositas") {
    if ($_SESSION["tipus"] == "bank") {
        $feliratok["oldalcim"] = "Bank";
        $parameters = [
            ":id" => $_SESSION["szamla"]
        ];
        $banktetel = $connection->getData("SELECT * FROM konyvelesi_tetel WHERE id = :id", $parameters);
        $banktetel = $banktetel[0];
        $bank = new Bank();
        $bank->setBankszamlaSzam($banktetel["bank_szamlaszam"]);
        $bank->setCegID($_SESSION["cegadoszam"]);
        $konyvelesi_tetel = new Konyvelesitetel($banktetel["tartozik"], $banktetel["kovetel"], $banktetel["osszeg"]);
        $konyvelesi_tetel->setId($banktetel["id"]);
        $konyvelesi_tetel->setBankSzamlaszam($bank->getBankszamlaSzam());
        $konyvelesi_tetel->setTeljesitesDatuma($banktetel["datum"]);
        $konyvelesi_tetel->setSzamlaSzamlaszam($banktetel["szamla_szamlaszam"]);
        $konyvelesi_tetel->setPenztarId($banktetel["penztar_id"]);
        $konyvelesi_tetel->setEgyebId($banktetel["egyeb_id"]);
        $konyvelesi_tetel->setFelhasznaloId($_SESSION["felhasznalo_id"]);
        $bank->addKonyvelesiTetel($konyvelesi_tetel);
        $bank->setKonyveloID($_SESSION["felhasznalo_id"]);
        $params = [
            ":cegadoszam" =>  $_SESSION["cegadoszam"]
        ];
        $szamlak = $connection->getData("SELECT szamlaszam FROM szamla WHERE ceg_adoszam = :cegadoszam AND fizetve = 0", $params);
    } else {
        $feliratok["oldalcim"] = "Egyéb";
        $parameters = [
            ":id" => $_SESSION["szamla"]
        ];
        $egyeb_all_data = $connection->getData("SELECT e.*, k.* FROM egyeb e INNER JOIN konyvelesi_tetel k ON k.egyeb_id = e.id WHERE e.id = :id", $parameters);
        $egyeb = new Egyeb();
        $egyeb->setId($_SESSION["szamla"]);
        $egyeb->setMegnevezes($egyeb_all_data[0]["megnevezes"]);
        $egyeb->setMegjegyzes($egyeb_all_data[0]["megjegyzes"]);
        $egyeb->setDatum($egyeb_all_data[0]["datum"]);
        $egyeb->setCegID($egyeb_all_data[0]["ceg_adoszam"]);
        $egyeb->setKonyveloID($egyeb_all_data[0]["felhasznalo_id"]);
        foreach ($egyeb_all_data as $index => $egyebdata) {
            $konyvelesitetel = new Konyvelesitetel($egyebdata["tartozik"], $egyebdata["kovetel"], $egyebdata["osszeg"]);
            $konyvelesitetel->setId($egyebdata["id"]);
            $konyvelesitetel->setTeljesitesDatuma($egyeb->getDatum());
            $egyeb->addKonyvelesiTetel($konyvelesitetel);
        }
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
    <title>Bank könyvelés</title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <script src="../js/fuggvenyek.js"></script>
</head>

<body>
    <div id="contentContainer" class="suppinvoice container mx-auto m-5 shadow" style="background-color: rgba(225, 230, 224);">
        <div class="row">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-success btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col-10 mx-auto text-center pt-3 mt-3">
                <h3><?= $feliratok["oldalcim"] ?> módosítása</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <?php if ($_SESSION["tipus"] == "bank") : ?>
            <div class="p-4">
                <div class="row">
                    <div id="columnChange" class="mx-auto">
                        <form action="" method="post" id="form_1">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-7">
                                        <label for="exampleFormControlSelect1">Bank kiválasztása</label>
                                        <input type="text" class="form-control w-100" id="bank_id" name="bank_id" value="<?php echo $bank->getKonyvelesiTetelek()[0]->getId() ?>" hidden>
                                        <select class="form-control banks" id="exampleFormControlSelect1" name="bankszamlaszam" required>
                                            <option value="<?= $bank->getBankszamlaSzam() ?>"><?= $bank->getBankszamlaSzam() ?></option>
                                            <?php foreach ($bankszamlak as $bankszamlaindex => $bankszamladatok) : ?>
                                                <option value="<?= $bankszamladatok["bankszamlaszam"] ?>"><?= $bankszamladatok["bankszamlaszam"] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label for="bankszamla_egyenleg">Bankszámla egyenlege:</label><br>
                                        <input type="text" class="form-control w-100" id="bankszamla_egyenleg" name="bankszamla_egyenleg"><br>
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
                                <div class="form-group mt-3" id="dynamicInputSection">
                                    <div class="row mb-2 mt-2" id="inputRow_0">
                                        <div class="col-2">
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-calendar-days"></i></span>
                                                <input type="text" class="form-control" id="bank_datum_0" name="bank_datum_0" value="<?php echo $bank->getKonyvelesiTetelek()[0]->getTeljesitesDatuma(); ?>">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <select class="form-control banks" id="szamlak_0" name="szamlak_0">

                                                    <?php foreach ($szamlak as $index => $szamlaadat) : ?>
                                                        <?php if ($szamlaadat["szamlaszam"] == $bank->getKonyvelesiTetelek()[0]->getSzamlaSzamlaszam()) : ?>
                                                            <option name="<?php echo $szamlaadat["szamlaszam"] ?>" value="<?php echo $szamlaadat["szamlaszam"] ?>" selected><?php echo $szamlaadat["szamlaszam"] ?></option>
                                                        <?php else : ?>
                                                            <option name="<?php echo $szamlaadat["szamlaszam"] ?>" value="<?php echo $szamlaadat["szamlaszam"] ?>"><?php echo $szamlaadat["szamlaszam"] ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">T</span>
                                                <select class="form-select" name="tartozik_0" id="tartozik_0">
                                                    <option value="<?= $bank->getKonyvelesiTetelek()[0]->getTartozik() ?>"><?= $bank->getKonyvelesiTetelek()[0]->getTartozik() ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">K</span>
                                                <select class="form-select" name="kovetel_0" id="kovetel_0">
                                                    <option value="<?= $bank->getKonyvelesiTetelek()[0]->getKovetel() ?>"><?= $bank->getKonyvelesiTetelek()[0]->getKovetel() ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">összeg</span>
                                                <input type="text" class="form-control" id="osszeg_0" name="osszeg_0" value="<?= $bank->getKonyvelesiTetelek()[0]->getOsszeg() ?>">
                                            </div>
                                        </div>
                                        <div class="col-1">
                                            <div class="input-group">
                                                <button class="btn btn-outline-success deleteButton" style="font-size: smaller;margin-top:2px" id="trash_0"><i class="fa-solid fa-trash"></i></button>
                                            </div>
                                        </div>
                                    </div>


                                </div>

                            </div>
                        </form>
                        <div class="selectprevnext" style="display: visible;">
                            <div class="row mt-3">
                                <div class="col-10 mt-4">
                                    <div class="alert alert-success text-center mt-3 hidden" role="alert" id="alertbox">
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
        <?php else : ?>
            <div class="p-4">
                <div class="row">
                    <div id="columnChange" class="mx-auto">
                        <form action="" method="post" id="form_1">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-7">
                                        <label for="megnevezes">Megnevezés</label>
                                        <input type="text" class="form-control w-100" id="megnevezes" name="megnevezes" value="<?= $egyeb->getMegnevezes() ?>"><br>
                                        <input type="text" class="form-control w-100" id="egyeb_id" name="egyeb_id" value="<?= $egyeb->getId() ?>" hidden><br>
                                    </div>
                                    <div class="col-4">
                                        <label for="bankszamla_egyenleg">Dátum</label><br>
                                        <input type="text" class="form-control w-100" id="datum" name="datum" value="<?= $egyeb->getDatum() ?>"><br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-11">
                                        <label for="megjegyzes">Megjegyzés</label>
                                        <input type="text" class="form-control w-100" id="megjegyzes" name="megjegyzes" value="<?= $egyeb->getMegjegyzes() ?>"><br>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <hr style="margin-bottom: 25px; margin-top: 25px;">
                        <form action="" method="post" name="konyveles" id="form_2">
                            <div class="row">
                                <div class="col-11 form-group">
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
                                <?php foreach ($egyeb->getKonyvelesiTetelek() as $index => $konyvelesitetel) : ?>
                                    <div class="row mb-2 mt-2" id="inputRow_<?= $index ?>">
                                        <input type="text" value="<?= $konyvelesitetel->getId() ?>" name="kt_<?= $index ?>" hidden>
                                        <div class="col-4">
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">T</span>
                                                <select class="form-select" name="tartozik_<?= $index ?>" id="tartozik_<?= $index ?>">
                                                    <option value="<?= $konyvelesitetel->getTartozik() ?>"><?= $konyvelesitetel->getTartozik() ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">K</span>
                                                <select class="form-select" name="kovetel_<?= $index ?>" id="kovetel_<?= $index ?>">
                                                    <option value="<?= $konyvelesitetel->getKovetel() ?>"><?= $konyvelesitetel->getKovetel() ?></option>
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
                                        <input type="text" class="form-control" id="totalInput" placeholder="0" name="totalInput" disabled>
                                    </div>
                                </div>
                                <div class="col"></div>
                                <div class="col"></div>
                            </div>

                        </form>
                        <div class="selectprevnext" style="display: visible;">
                            <div class="row mt-3">
                                <div class="col-10 mt-4">
                                    <div class="alert alert-success text-center mt-3 hidden" role="alert" id="alertbox_egyeb">
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
        <?php endif; ?>
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
            calculateTotalEgyeb();
            updateDeleteButtonStatus();
        });

        function collectFormsForMod() {
            let form1 = $("#form_1");
            let form2 = $("#form_2");
            let forms = [];
            let tipus = "<?= $_SESSION["tipus"] ?>";
            forms[1] = getFormData(form1);
            forms[2] = getFormData(form2);
            $.ajax({
                method: 'post',
                url: "inputkezeles.php",
                data: {
                    "dataForMod": forms,
                    "tipus": tipus,
                },
                success: function(response) {
                    console.log(response);
                    if (response == "bankszamlaNincsKivalasztva") {
                        hiba("Számlaszám megadása kötelező!", "alertbox");
                    }
                    if (response == "bankMindenMezoUres") {
                        hiba("Minden mező kitöltése kötelező!", "alertbox");
                    }
                    if (response.includes("bankKonyvelesiTetelHIba_")) {
                        let tmp = response.split("_");
                        $('#inputRow_' + tmp[1]).css("background-color", "rgb(245, 208, 205)");
                        hiba("Nem található adat! (dátum/tartozik/követel/összeg) ", "alertbox");
                    }
                    if (response == "siker") {
                        if ($('#alertbox').hasClass("hidden")) {
                            $('#alertbox').removeClass("hidden");
                        } else {
                            $('#alertbox').removeClass("alert-danger");
                            $('#alertbox').addClass("alert-success");
                            $('#alertbox').text("Sikeres rögzítés");
                        }

                        setTimeout(() => {
                            $('#alertbox').addClass("hidden");
                        }, 1000);
                        setTimeout(() => {
                            if (tipus == "bank") {
                                window.location.replace("konyvelt_listak/banklist.php");
                            } else {
                                window.location.replace("konyvelt_listak/egyeblist.php");
                            }

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