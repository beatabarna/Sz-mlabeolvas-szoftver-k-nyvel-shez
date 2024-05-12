<?php
namespace Gerke\Imagetotext;
session_start();
require('oop/Connection.php');
$connection = new Connection();
$params = [
    ":felhasznalo_id" => $_SESSION["felhasznalo_id"]
];
$felhasznalo = $connection->getData("SELECT * FROM felhasznalo WHERE id = :felhasznalo_id", $params);
$ugyfellista = $connection->getData("SELECT * FROM ceg WHERE felhasznalo_id = :felhasznalo_id", $params);

$konyvelt_tetelek = $connection->getData("SELECT MONTH(k.datum) AS month, COUNT(*) AS monthly_count
                                        FROM konyvelesi_tetel k
                                        WHERE k.felhasznalo_id = :felhasznalo_id
                                        AND YEAR(k.datum) = YEAR(CURDATE())
                                        GROUP BY MONTH(k.datum)
                                        ORDER BY month", $params);
$data = array();
for ($i = 1; $i < 13; $i++) {
    $data[$i] = 0;
}
foreach ($konyvelt_tetelek as $index => $adatok) {
    $data[$adatok["month"]] = $adatok["monthly_count"];
}

$data2 = array();
$konyvelt_szamlak = $connection->getData("SELECT 
                                        MONTH(sz.teljesites) AS Month,
                                        COUNT(DISTINCT sz.szamlaszam) AS NumberOfInvoices
                                        FROM 
                                        szamla sz
                                        INNER JOIN 
                                        konyvelesi_tetel k ON sz.szamlaszam = k.szamla_szamlaszam
                                        WHERE 
                                        k.felhasznalo_id = :felhasznalo_id
                                        AND YEAR(k.datum) = YEAR(CURDATE())
                                        GROUP BY MONTH(sz.teljesites)
                                        ORDER BY Month", $params);
for ($i = 1; $i < 13; $i++) {
    $data2[$i] = 0;
}
foreach ($konyvelt_szamlak as $index2 => $adatok2) {
    $data2[$adatok2["Month"]] = $adatok2["NumberOfInvoices"];
}

$data3 = array();
$konyvelt_bank = $connection->getData("SELECT 
                                        MONTH(k.datum) AS Month,
                                        COUNT(k.bank_szamlaszam) AS bank
                                        FROM 
                                        konyvelesi_tetel k
                                        WHERE 
                                        k.felhasznalo_id = :felhasznalo_id
                                        AND YEAR(k.datum) = YEAR(CURDATE())
                                        GROUP BY MONTH(k.datum)
                                        ORDER BY Month", $params);
for ($i = 1; $i < 13; $i++) {
    $data3[$i] = 0;
}
foreach ($konyvelt_bank as $index3 => $adatok3) {
    $data3[$adatok3["Month"]] = $adatok3["bank"];
}
$files = glob("../images/profile_pictures/*");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../style.css">
    <title>Felhasználó</title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="../js/fuggvenyek.js"></script>
</head>

<body>
    <?php include('navbar.php')?>
    <div id="contentContainer" class="suppinvoice container mx-auto m-5 shadow" style="background-color: rgba(225, 230, 224);max-width:750px;">
        <div class="row">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
        </div>
        <div class="row mt-3 p-2">
            <div class="col-2"></div>
            <div class="col-3">
                <button class="btn btn-success ms-5" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    <img src="<?php echo $_SESSION["profilkep"] ?>" height="100" width="100" />
                </button>
            </div>
            <div class="col-6 border ms-3">
                <h5><?= $_SESSION["felhasznalo"] ?></h5>
                <?php if ($_SESSION["user_level"] == 1) : ?>
                    <span>Könyvelő/ADMIN</span><br>
                <?php else : ?>
                    <span>Könyvelő</span><br>
                <?php endif; ?>
                <span><i class="fa-regular fa-envelope"></i> <?= $felhasznalo[0]["email"] ?></span><br>
                <span>Utolsó belépés dátuma : <i class="fa-regular fa-clock"></i> <?= $felhasznalo[0]["utolso_belepes"] ?></span><br>
            </div>
        </div>
        <canvas class="mx-auto mt-5" id="myChart" style="width:100%;max-width:600px"></canvas>
        <div class="row mt-3 p-2">
            <div class="row mx-auto mt-5">
                <h5>Könyvelt cégek</h5>
                <?php foreach ($ugyfellista as $ugyfel) : ?>
                    <hr>
                    <div class="col-2"></div>
                    <div class="col-6 mb-3">
                        <h5><?= $ugyfel["nev"] ?></h5>
                        <span><?= $ugyfel["adoszam"] ?></span><br>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Választható profilképek</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php foreach ($files as $file) : ?>
                        <img class="btn m-1 p-1 border border-success shadow" style="width: 100px;height:100px;" src="<?= $file ?>" id="<?= $file ?>" onclick="changeProfilePicture(this.id)">
                    <?php endforeach; ?>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    <script>
        const xValues = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
        const konyvelt_tetelek = [];
        const konyvelt_szamlak = [];
        const konyvelt_bank = [];
        <?php foreach ($data as $honap => $db) : ?>
            konyvelt_tetelek.push("<?= $db ?>");
        <?php endforeach; ?>
        <?php foreach ($data2 as $honap2 => $db2) : ?>
            konyvelt_szamlak.push("<?= $db2 ?>");
        <?php endforeach; ?>
        <?php foreach ($data3 as $honap3 => $db3) : ?>
            konyvelt_bank.push("<?= $db3 ?>");
        <?php endforeach; ?>

        new Chart("myChart", {
            type: "line",
            data: {
                labels: xValues,
                datasets: [{
                        label: 'Könyvelt tételek',
                        data: konyvelt_tetelek,
                        borderColor: "green",
                        fill: false
                    },
                    {
                        label: 'Könyvelt számlák',
                        data: konyvelt_szamlak,
                        borderColor: '#fcba03',
                        fill: false
                    }, {
                        label: 'Könyvelt banki tételek',
                        data: konyvelt_bank,
                        borderColor: "#03fcfc",
                        fill: false
                    }
                ]
            },
            options: {
                legend: {
                    display: true
                }
            }
        });
    </script>
</body>

</html>