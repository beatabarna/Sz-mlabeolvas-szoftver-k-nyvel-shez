<?php

namespace Gerke\Imagetotext;

session_start();
require('oop/Connection.php');
$connection = new Connection();
$total = 0;
if (isset($_POST["lekerdezes"])) {
    $start = $_POST["datetol"];
    $end = $_POST["dateig"];
    $parameters = [
        ":cegadoszam" => $_SESSION["cegadoszam"],
        ":start" => $start,
        ":end" => $end
    ];
    $afa466 = $connection->getData("SELECT k.*, s.*, p.* FROM konyvelesi_tetel k INNER JOIN szamla s ON k.szamla_szamlaszam = s.szamlaszam INNER JOIN partner p ON p.adoszam = s.partner_adoszam WHERE (k.tartozik = 466 OR k.kovetel = 466) AND s.ceg_adoszam = :cegadoszam AND k.datum BETWEEN :start AND :end", $parameters);
    $afaT466 = $connection->getData("SELECT SUM(k.osszeg) AS tartozik466 FROM konyvelesi_tetel k INNER JOIN szamla s ON k.szamla_szamlaszam = s.szamlaszam INNER JOIN partner p ON p.adoszam = s.partner_adoszam WHERE k.tartozik = 466 AND s.ceg_adoszam = :cegadoszam AND k.datum BETWEEN :start AND :end", $parameters);
    $afaK466 = $connection->getData("SELECT SUM(k.osszeg) AS kovetel466 FROM konyvelesi_tetel k INNER JOIN szamla s ON k.szamla_szamlaszam = s.szamlaszam INNER JOIN partner p ON p.adoszam = s.partner_adoszam WHERE k.kovetel = 466 AND s.ceg_adoszam = :cegadoszam AND k.datum BETWEEN :start AND :end", $parameters);
    $combined466 = array_merge($afaT466, $afaK466);
    $balance466 = $combined466[0]["tartozik466"] - $combined466[1]["kovetel466"];

    $afa467 = $connection->getData("SELECT k.*, s.*, p.* FROM konyvelesi_tetel k INNER JOIN szamla s ON k.szamla_szamlaszam = s.szamlaszam INNER JOIN partner p ON p.adoszam = s.partner_adoszam WHERE (k.tartozik = 467 OR k.kovetel = 467) AND s.ceg_adoszam = :cegadoszam AND k.datum BETWEEN :start AND :end", $parameters);
    $afaT467 = $connection->getData("SELECT SUM(k.osszeg) AS tartozik467 FROM konyvelesi_tetel k INNER JOIN szamla s ON k.szamla_szamlaszam = s.szamlaszam INNER JOIN partner p ON p.adoszam = s.partner_adoszam WHERE k.tartozik = 467 AND s.ceg_adoszam = :cegadoszam AND k.datum BETWEEN :start AND :end", $parameters);
    $afaK467 = $connection->getData("SELECT SUM(k.osszeg) AS kovetel467 FROM konyvelesi_tetel k INNER JOIN szamla s ON k.szamla_szamlaszam = s.szamlaszam INNER JOIN partner p ON p.adoszam = s.partner_adoszam WHERE k.kovetel = 467 AND s.ceg_adoszam = :cegadoszam AND k.datum BETWEEN :start AND :end", $parameters);
    $combined467 = array_merge($afaT467, $afaK467);
    $balance467 = $combined467[1]["kovetel467"] - $combined467[0]["tartozik467"];

    $total = $balance467 - $balance466;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../style.css">
    <title>Egyéb tételek rögzítése</title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <script src="../js/fuggvenyek.js"></script>
</head>

<body>
    <?php include('navbar.php') ?>
    <div id="contentContainer" class="suppinvoice container mx-auto m-5 shadow" style="background-color: rgba(225, 230, 224);">
        <div class="col-1 pt-3">
            <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
        </div>
        <div class="col">
            <h2 class="text-center mb-5">Áfa lekérdezés</h2>
        </div>
        <form method="POST" action="">
            <div class="row">
                <div class="col"></div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text" id="datetol"> -tól</span>
                        <input type="date" class="form-control" id="datetol" name="datetol">
                    </div>
                </div>
                <div class="col">
                    <div class="input-group">
                        <span class="input-group-text" id="dateig"> -ig</span>
                        <input type="date" class="form-control" id="dateig" name="dateig">
                    </div>
                </div>
                <div class="col"></div>
            </div>
            <div class="row mt-3 mb-5">
                <div class="col"></div>
                <div class="col text-center">
                    <button type="submit" class="btn btn-outline-dark mt-3 justify-content-md-end" name="lekerdezes"><i class="fa-regular fa-circle-down"></i> Lekérdezés</button>
                </div>
                <div class="col"></div>
            </div>
        </form>
        <div class="pt-5">
            <?php if ($total > 0) : ?>
                <h4 class="text-center" style="font-variant:normal">Fizetendő általános forgalmi adó összesen: <?= number_format($total,0,""," ") ?> Ft</h4>
            <?php elseif ($total == 0) : ?>
                <h4 class="text-center">Fizetendő/Visszaigényelhető: <?= number_format($total,0,""," ") ?> Ft</h4>
            <?php else : ?>
                <h4 class="text-center">Visszaigényelhető általános forgalmi adó összesen: <?= number_format(($total * -1),0,""," ") ?> Ft</h4>
            <?php endif ?>
        </div>
        <hr class="mt-5 mx-auto">
        <div id="printSection">
            <h2 class="text-center mt-5">Visszaigényelhető áfa</h2>
            <?php if (isset($afa466) && count($afa466) > 0) : ?>
                <table class="rounded text-dark w-100 mb-5" style="background-color: secondary; color:white; font-size: 14px">
                    <thead class="text-center">
                        <th class="pb-4 pt-3">Teljesítés</th>
                        <th class="pb-4 pt-3">Kiállítás</th>
                        <th class="pb-4 pt-3">Fizetési határidő</th>
                        <th class="pb-4 pt-3">Számlaszám</th>
                        <th class="pb-4 pt-3">Partner</th>
                        <th class="pb-4 pt-3">Fizetés módja</th>
                        <th class="pb-4 pt-3">Megjegyzés</th>
                        <th class="pb-4 pt-3">Áfa</th>
                    </thead>
                    <tbody class="text-center">
                        <?php foreach ($afa466 as $afa) : ?>
                            <tr style="border-top: 1px solid rgb(200, 200, 200)">
                                <td><?= $afa["teljesites"] ?></td>
                                <td><?= $afa["fizhat"] ?></td>
                                <td><?= $afa["kiallitas"] ?></td>
                                <td><?= $afa["szamla_szamlaszam"] ?></td>
                                <td><?= $afa["nev"] ?></td>
                                <?php if ($afa["penztar"] == 0) : ?>
                                    <td>pénztár</td>
                                <?php else : ?>
                                    <td>átutalás</td>
                                <?php endif ?>
                                <td><?= $afa["megjegyzes"] ?></td>
                                <td><?= $afa["osszeg"] ?></td>
                            </tr>
                        <?php endforeach ?>
                        <tr style="visibility: hidden;">
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6"></td>
                            <?php if ($combined466[0]["tartozik466"] == NULL) : ?>
                                <td class="text-center" style="border: 1px solid black;"><b>T</b> - 0</td>
                            <?php else : ?>
                                <td class="text-center" style="border: 1px solid black;"><b>T</b> - <?= number_format($combined466[0]["tartozik466"],0,""," ") ?></td>
                            <?php endif ?>
                            <?php if ($combined466[1]["kovetel466"] == NULL) : ?>
                                <td class="text-center" style="border: 1px solid black;"><b>K</b> - 0</td>
                            <?php else : ?>
                                <td class="text-center" style="border: 1px solid black;"><b>K</b> - <?= number_format($combined466[1]["kovetel466"],0,"", " ") ?></td>
                            <?php endif ?>
                        </tr>
                        <tr>
                            <td colspan="6"></td>
                            <td colspan="2" class="text-center" style="font-size: large; border: 1px solid black;"><b>Összesen visszaigényelhető: <?= number_format($balance466, 0, "", " ") ?> Ft</b></td>
                        </tr>
                    </tfoot>
                </table>
            <?php endif; ?>
            <hr class="mt-5 mx-auto">
            <h2 class="text-center">Fizetendő áfa</h2>
            <div class="pb-5">
                <?php if (isset($afa467) && count($afa467) > 0) : ?>
                    <table class="rounded text-dark w-100" style="background-color: secondary; color:white; font-size: 14px;">
                        <thead class="text-center">
                            <th class="pb-4 pt-3">Teljesítés</th>
                            <th class="pb-4 pt-3">Kiállítás</th>
                            <th class="pb-4 pt-3">Fizetési határidő</th>
                            <th class="pb-4 pt-3">Számlaszám</th>
                            <th class="pb-4 pt-3">Partner</th>
                            <th class="pb-4 pt-3">Fizetés módja</th>
                            <th class="pb-4 pt-3">Megjegyzés</th>
                            <th class="pb-4 pt-3">Áfa</th>
                        </thead>
                        <tbody class="text-center">
                            <?php foreach ($afa467 as $afa) : ?>
                                <tr style="border-top: 1px solid rgb(200, 200, 200)">
                                    <td><?= $afa["teljesites"] ?></td>
                                    <td><?= $afa["fizhat"] ?></td>
                                    <td><?= $afa["kiallitas"] ?></td>
                                    <td><?= $afa["szamla_szamlaszam"] ?></td>
                                    <td><?= $afa["nev"] ?></td>
                                    <?php if ($afa["penztar"] == 0) : ?>
                                        <td>pénztár</td>
                                    <?php else : ?>
                                        <td>átutalás</td>
                                    <?php endif ?>
                                    <td><?= $afa["megjegyzes"] ?></td>
                                    <td><?= $afa["osszeg"] ?></td>
                                </tr>
                            <?php endforeach ?>
                            <tr style="visibility: hidden;">
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                                <td>0</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="margin-top: 150px;" colspan="6"></td>
                                <?php if ($combined467[0]["tartozik467"] == NULL) : ?>
                                    <td class="text-center" style="border: 1px solid black;"><b>T</b> - 0</td>
                                <?php else : ?>
                                    <td class="text-center" style="border: 1px solid black;"><b>T</b> - <?= number_format($combined467[0]["tartozik467"],0,""," ") ?></td>
                                <?php endif ?>
                                <?php if ($combined467[1]["kovetel467"] == NULL) : ?>
                                    <td class="text-center" style="border: 1px solid black;"><b>K</b> - 0</td>
                                <?php else : ?>
                                    <td class="text-center" style="border: 1px solid black;"><b>K</b> - <?= number_format($combined467[1]["kovetel467"],0,""," ") ?></td>
                                <?php endif ?>
                            </tr>
                            <tr>
                                <td colspan="6"></td>
                                <td colspan="2" class="text-center" style="font-size: large; border: 1px solid black;"><b>Összesen fizetendő: <?= number_format($balance467,0,""," ") ?> Ft</b></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <?php if (isset($_POST["lekerdezes"])) : ?>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end pb-3">
                <button onclick="printAllTables()" class="btn btn-outline-success" id="printButton"><i class="fa-solid fa-print"></i> Nyomtatás</button>
            </div>
        <?php endif; ?>
    </div>
    <script>
        function printAllTables() {
            let printElement = document.getElementById('printSection');
            let cegName = <?php echo json_encode($_SESSION['cegnev']); ?>;
            if (printElement) {
                let tableHTML = printElement.outerHTML;
                let newWin = window.open('', '_blank', 'width=800,height=600');
                newWin.document.open();
                newWin.document.write(`<!DOCTYPE html>
                                        <html>
                                        <head>
                                        <title>Áfa</title>
                                        <style>
                                            table, th, td {
                                                border: 1px solid black;
                                                border-collapse: collapse;
                                                margin: 0;
                                                padding: 5px;
                                                width: 100%;
                                            }
                                            th, td {
                                                text-align: left;
                                            }
                                            h2 {
                                                text-align: center;
                                            }
                                            @media print {
                                                html, body {
                                                    width: 100%;
                                                    margin: 0;
                                                    padding: 0;
                                                    display: block;
                                                }
                                            }
                                        </style>
                                        </head>
                                        <body onload="window.print(); window.close();">
                                            <h2>${cegName}</h2>
                                            ${tableHTML}
                                        </body>
                                        </html>`);
                newWin.document.close();
            } else {
                console.error('Element with ID "printSection" not found.');
                alert('Error: Print section not found. Please check the page and try again.');
            }
        }
    </script>
</body>

</html>