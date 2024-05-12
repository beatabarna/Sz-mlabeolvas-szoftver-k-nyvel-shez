<?php

namespace Gerke\Imagetotext;

session_start();
$szamlatukor;
if (file_exists('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_szamlatukor.json')) {
    $szamlatukor = file_get_contents('../data/' . $_SESSION["cegadoszam"] . '/' . $_SESSION["cegadoszam"] . '_szamlatukor.json');
    $szamlatukor = json_decode($szamlatukor, true);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../style.css">
    <title>Számlatükör</title>
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
                <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
            <div class="col-10 mx-auto text-center pt-3 mt-3">
                <h3>Számlatükör</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="p-4">
            <div class="row  pb-4">
                <div class="col-7"></div>
                <div class="col-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" placeholder="főkönyvi szám" class="form-control" id="fokonyvi_kereses" onkeyup='kereses("fokonyvi_kereses","fokonyviszamok")'>
                    </div>
                </div>
            </div>
            <div class="row">
                <div id="columnChange" class="">
                    <table class="rounded text-dark mx-auto" style="background-color: secondary; color:white; font-size: 14px" id="fokonyviszamok">
                        <thead>
                            <th class="pb-4 pt-3 px-2" style="text-align:right;">Főkönyvi szám</th>
                            <th class="pb-4 pt-3 px-2">Megnevezés</th>
                        </thead>
                        <tbody class="">
                            <?php foreach ($szamlatukor as $key => $konyvelesiszamadatok) : ?>
                                <tr style="border-top: 1px solid rgb(200, 200, 200)">
                                    <td class="px-2" style="border-right:1px solid white;text-align:right;"><b><?= $konyvelesiszamadatok["number"]; ?></b></td>
                                    <td class="text-left px-2"><?= $konyvelesiszamadatok["name"]; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>

</html>