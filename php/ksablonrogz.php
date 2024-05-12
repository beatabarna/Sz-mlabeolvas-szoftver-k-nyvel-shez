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
    <title>File Upload and Selection</title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
</head>

<body>
<?php include('navbar.php')?>
    <div id="contentContainer" class="suppinvoice container mx-auto m-5 shadow" style="background-color: rgba(225, 230, 224);">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" aria-current="page" href="ksablon.php">Sablonok</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="ksablonrogz.php">Sablon rögzítés</a>
            </li>
        </ul>
        <div class="row">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-success btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col-10 mx-auto text-center pt-3 mt-3">
                <h3>Új sablon rögzítése</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="p-4">
            <form action="" method="post" class="mt-4">
                <div class="form-group invoiceregistration">
                    <div class="row">
                        <div class="col">
                            <label for="szallito">Megnevezés</label><br>
                            <input type="text" class="form-control basicData" id="megnevezes" name="megnevezes"><br>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">T</span>
                                <input type="text" class="form-control" id="tartozik_0" name="tartozik_0" onclick="setvariables(this)">
                                <div id="match-list_tartozik_0" class="shadow"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">K</span>
                                <input type="text" class="form-control" id="kovetel_0" name="kovetel_0" onclick="setvariables(this)">
                                <div id="match-list_kovetel_0" class="shadow" style="z-index: 1;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <button id="sablonmentes" class="btn btn-success mt-5" onclick="save();"><i class="fa-regular fa-floppy-disk mx-1"></i> Mentés</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <script>
        $(function() {
            $('#megnevezes').on('keypress', function(e) {
                if (e.which == 32) {
                    console.log('Space Detected');
                    return false;
                }
            });
        });

        function setvariables(e) {
            let id = e.id;
            let fullID = id.split("_");
            switch (fullID[0]) {
                case "tartozik":
                    tartozik = document.getElementById(id);
                    matchList = document.getElementById('match-list_tartozik_' + fullID[1]);
                    tartozik.addEventListener('input', () => searchSzamlaszam(tartozik.value));
                    break;
                case "kovetel":
                    kovetel = document.getElementById(id);
                    matchList = document.getElementById('match-list_kovetel_' + fullID[1]);
                    kovetel.addEventListener('input', () => searchSzamlaszam(kovetel.value));
                    break;
                default:
                    break;
            }

        }

        const searchSzamlaszam = async searchText => {
            const res = await fetch('../szamlatukor.json');
            const szamlak = await res.json();

            let matches = szamlak.filter(szamla => {
                const regex = new RegExp(`^${searchText}`, 'gi');
                return szamla.number.match(regex) || szamla.name.match(regex);
            });

            if (searchText.length === 0) {
                matches = [];
                matchList.innerHTML = '';
            }

            outputHtml(matches);
        }

        const outputHtml = matches => {
            if (matches.length > 0) {
                const html = matches.map(match => `
                <div class="listitem" value="${match.number} - ${match.name}" id=""${match.number}" onclick="putThisinInput(this)">${match.number} - ${match.name}</div>
                `).join('');

                matchList.innerHTML = html;
            }
        }

        function putThisinInput(element) {
            let fullID = element.parentElement.id.split("_");
            switch (fullID[1]) {
                case "tartozik":
                    tartozik.value = element.getAttribute("value");
                    break;
                case "kovetel":
                    kovetel.value = element.getAttribute("value");
                    break;
                default:
                    break;
            }
            matchList.innerHTML = "";

        }

        function save() {

            let uj_ertekek = {
                ":nev": $("#megnevezes").val(),
                ":tartozik": $("#tartozik_0").val(),
                ":kovetel": $("#kovetel_0").val(),
                ":felhasznalo_id": 0
            }
            $.ajax({
                method: 'post',
                url: "inputkezeles.php",
                data: {
                    "data": uj_ertekek,
                    "tipus": "ksablonmentes",
                },
                success: function(response) {
                    console.log(response);
                    window.location.replace("ksablon.php");
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    </script>
</body>

</html>
