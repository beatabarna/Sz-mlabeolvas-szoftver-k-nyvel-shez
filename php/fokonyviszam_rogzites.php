<?php

namespace Gerke\Imagetotext;

session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../style.css">
    <title>Új főkönyviszám rögzítése</title>
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
        <div class="row pt-3">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col mx-auto text-center pt-3 mt-3">
                <h3>Új főkönyvi szám rögzítése</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="p-4">
            <form action="" method="post" class="mt-4" id="fokonyviszam_rogzites_form" autocomplete="off">
                <div class="form-group ">
                    <div>
                        <h5>Típus kiválasztás</h5>
                    </div>
                    <input class="btn-check " type="radio" id="eredmeny" name="tipus" value="eredmeny">
                    <label class="btn btn-secondary  col-2 border-0" id="" for="eredmeny">Eredménykimutatás</label>
                    <input class="btn-check " type="radio" id="merleg" name="tipus" value="merleg">
                    <label class="btn btn-secondary col-2 border-0" id="" for="merleg">Mérleg</label>
                    <hr>
                    <div>
                        <h5>Kategória kiválasztás</h5>
                    </div>
                    <div class="row">
                        <div class="col" id="lvl1_container">
                            <select class="form-select form-select-sm" name="level_1" id="level_1">
                                <option value="-1">Válasszon...</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col" id="lvl2_container">
                            <select class="form-select form-select-sm" name="level_2" id="level_2">
                                <option value="-1">Válasszon...</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col" id="lvl3_container">
                            <select class="form-select form-select-sm" name="level_3" id="level_3">
                                <option value="-1">Válasszon...</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2 mt-2 hidden" id="ujadatok">
                        <div class="col-3">
                            <div class="input-group">
                                <span class="input-group-text">Főkönyvi szám</span>
                                <input type="text" class="form-control" name="ujfokonyviszam" id="ujfokonyviszam">
                            </div>
                        </div>
                        <div class="col w-100">
                            <div class="input-group">
                                <span class="input-group-text">Főkönyvi szám megnevezés</span>
                                <input type="text" class="form-control" name="ujfokonyviszam_magyarazat" id="ujfokonyviszam_magyarazat">
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <div class="row">
                            <div class="col-10">
                                <div class="alert alert-success text-center hidden" role="alert" id="alertbox_fokonyviszam">
                                    <strong>Sikeres rögzítés!</strong>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="btn btn-outline-dark float-end" onclick="collectUjFokonyviszamAdatok()"><i class="fa-regular fa-floppy-disk mx-1"></i> Mentés</div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            let selectedRadio;
            let selectedOption1;
            let selectedOption2;

            $('input[type="radio"][name="tipus"]').change(function() {
                selectedRadio = $(this).attr('id');
                $.ajax({
                    method: 'POST',
                    url: "inputkezeles.php",
                    data: {
                        "fokonyvi_szamok": selectedRadio
                    },
                    dataType: 'json',
                    success: function(response) {
                        const selectElement = document.getElementById('level_1');
                        /* kiürítjük az 1. szintet */
                        $("#level_1").empty();
                        /* betesszük a válasszon opciót az 1. szintre */
                        const option1 = document.createElement('option');
                        option1.value = "-2";
                        option1.textContent = 'Válasszon...';
                        document.getElementById('level_1').appendChild(option1);

                        /* kiürítjük az 1. szintet */
                        $("#level_2").empty();
                        /* betesszük a válasszon opciót az 1. szintre */
                        const option2 = document.createElement('option');
                        option2.value = "-2";
                        option2.textContent = 'Válasszon...';
                        document.getElementById('level_2').appendChild(option2);

                        /* kiürítjük az 1. szintet */
                        $("#level_3").empty();
                        /* betesszük a válasszon opciót az 1. szintre */
                        const option3 = document.createElement('option');
                        option3.value = "-2";
                        option3.textContent = 'Válasszon...';
                        document.getElementById('level_3').appendChild(option3);
                        /* betöltjük a json adatokat az 1. szintre */
                        response.forEach(optionData => {
                            const option = document.createElement('option');
                            option.value = optionData;
                            option.textContent = optionData;
                            selectElement.appendChild(option);
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert("Error loading data: " + textStatus + " - " + errorThrown);
                    }
                });
            });

            $('select[name="level_1"]').change(function() {
                selectedOption1 = $(this).val();
                $.ajax({
                    method: 'POST',
                    url: "inputkezeles.php",
                    data: {
                        "fokonyvi_szamok": selectedRadio,
                        "lvl1": selectedOption1
                    },
                    //dataType: 'json',
                    success: function(response) {
                        response = JSON.parse(response);
                        let selectlvl2 = document.getElementById('level_2');
                        /* kiürítjük az 2. szintet */
                        $("#level_2").empty();
                        /* betesszük a válasszon opciót az 2. szintre */
                        const option2 = document.createElement('option');
                        option2.value = "-2";
                        option2.textContent = 'Válasszon...';
                        document.getElementById('level_2').appendChild(option2);

                        /* kiürítjük az 3. szintet */
                        $("#level_3").empty();
                        /* betesszük a válasszon opciót az 3. szintre */
                        const option3 = document.createElement('option');
                        option3.value = "-2";
                        option3.textContent = 'Válasszon...';
                        document.getElementById('level_3').appendChild(option3);
                        response.forEach(optionData => {
                            const option = document.createElement('option');
                            option.value = optionData;
                            option.textContent = optionData;
                            selectlvl2.appendChild(option);
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus);
                    }
                });
            });

            $('select[name="level_2"]').change(function() {
                selectedOption2 = $(this).val();
                $.ajax({
                    method: 'POST',
                    url: "inputkezeles.php",
                    data: {
                        "fokonyvi_szamok": selectedRadio,
                        "lvl1": selectedOption1,
                        "lvl2": selectedOption2
                    },
                    //dataType: 'json',
                    success: function(response) {
                        response = JSON.parse(response);
                        let selectlvl3 = document.getElementById('level_3');
                        /* kiürítjük az 3. szintet */
                        $("#level_3").empty();
                        /* betesszük a válasszon opciót az 1. szintre */
                        const option3 = document.createElement('option');
                        option3.value = "-2";
                        option3.textContent = 'Válasszon...';
                        document.getElementById('level_3').appendChild(option3);
                        response.forEach(optionData => {
                            const option = document.createElement('option');
                            option.value = optionData;
                            option.textContent = optionData;
                            selectlvl3.appendChild(option);
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus);
                    }
                });
            });

            $('select[name="level_3"]').change(function() {

                $('#ujadatok').removeClass('hidden');


            });
        });
    </script>
</body>

</html>