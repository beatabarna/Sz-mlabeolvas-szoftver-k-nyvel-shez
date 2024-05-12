<?php

namespace Gerke\Imagetotext;

session_start();

?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&family=Roboto&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="../js/fuggvenyek.js"></script>
    <title>Home page</title>
</head>

<body>
    <?php include('navbar.php') ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col" id="wholekonyvcard">
                <div class="card mx-auto shadow" style="width: 18rem;">
                    <div id="cardkonyveles">
                        <img class="card-img-top" src="../images/konyveles.png" alt="Card image cap">
                        <div class="card-body">
                            <h5 class="card-title">Könyvelés</h5>
                            <p class="card-text">Számlák, pénztári és banki tranzakciók, valamint egyéb gazdasági események könyvelése.</p>
                        </div>
                    </div>
                    <div>
                        <ul class="list-group list-group-flush" id="listkonyveles" style="display: none;">
                            <a href="szamlak.php?type=szallito">
                                <li class="list-group-item">szállítói számla könyvelése</li>
                            </a>
                            <a href="szamlak.php?type=vevo">
                                <li class="list-group-item">vevő számla könyvelése</li>
                            </a>
                            <a href="szamlak.php?type=penztar">
                                <li class="list-group-item">pénztár könyvelése</li>
                            </a>
                            <a href="bank.php">
                                <li class="list-group-item">bank könyvelése</li>
                            </a>
                            <a href="egyeb.php">
                                <li class="list-group-item">egyéb</li>
                            </a>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col" id="wholetargyicard">
                <div class="card mx-auto shadow" id="home1" style="width: 18rem;">
                    <div id="cardtargyi">
                        <img class="card-img-top" src="../images/targyieszk.png" alt="Card image cap">
                        <div class="card-body">
                            <h5 class="card-title">Tárgyi eszközök</h5>
                            <p class="card-text">A tárgyi eszközök és azok amortizációjának nyilvántartása, valamint ezek listázása.</p>
                        </div>
                    </div>
                    <div>
                        <ul class="list-group list-group-flush" id="listtargyi" style="display: none;">
                            <a href="targyieszkozok.php">
                                <li class="list-group-item">tárgyi eszköz rögzítése</li>
                            </a>
                            <a href="targyieszkozlistazas.php">
                                <li class="list-group-item">tárgyi eszközök listázása</li>
                            </a>
                            <a href="amortizacio.php">
                                <li class="list-group-item">amortizáció</li>
                            </a>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col" id="wholekivonatcard">
                <div class="card mx-auto shadow" style="width: 18rem;">
                    <div id="cardkivonat">
                        <img class="card-img-top" src="../images/kivonat.png" alt="Card image cap">
                        <div class="card-body">
                            <h5 class="card-title">Kivonatok</h5>
                            <p class="card-text">Rögzített tételek listázása, áfa, mérleg, eredménykimutatás és főkönyvi kivonat lekérdezése.</p>
                        </div>
                    </div>
                    <div>
                        <ul class="list-group list-group-flush" id="listkivonat" style="display: none;">
                            <a href="konyvelt_listak/szallitolist.php">
                                <li class="list-group-item">listázás</li>
                            </a>
                            <a href="afa.php">
                                <li class="list-group-item">áfa kivonat</li>
                            </a>
                            <a href="merleg.php">
                                <li class="list-group-item">mérleg</li>
                            </a>
                            <a href="eredmenykimut.php">
                                <li class="list-group-item">eredménykimutatás</li>
                            </a>
                            <a href="fokonyvi_kivonat.php">
                                <li class="list-group-item">főkönyvi kivonat</li>
                            </a>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col" id="wholeegyebcard">
                <div class="card mx-auto shadow" style="width: 18rem;">
                    <div id="egyebcard">
                        <img class="card-img-top" src="../images/egyeb.png" alt="Card image cap">
                        <div class="card-body">
                            <h5 class="card-title">Egyéb</h5>
                            <p class="card-text">Főkönyviszám, sablon, ügyfelek és partnerek rögzítése. Számlatükör és felhasználók kezelése.</p>
                        </div>
                    </div>
                    <div>
                        <ul class="list-group list-group-flush" id="listegyeb" style="display: none;">
                            <a href="szamlatukor.php">
                                <li class="list-group-item">számlatükör</li>
                            </a>
                            <a href="fokonyviszam_rogzites.php">
                                <li class="list-group-item">főkönyviszám rögzítése</li>
                            </a>
                            <a href="ksablon.php">
                                <li class="list-group-item">könyvelési sablon</li>
                            </a>
                            <a href="bankszamlarogz.php">
                                <li class="list-group-item">bankszámla rögzítése</li>
                            </a>
                            <a href="ugyfelek.php">
                                <li class="list-group-item">ügyfelek</li>
                            </a>
                            <?php if ($_SESSION["user_level"] == 1) : ?>
                                <a href="felhasznalok.php">
                                    <li class="list-group-item">felhasználók</li>
                                </a>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    $(document).ready(function() {
        $('#cardkonyveles').mouseenter(function() {
            $('#listkonyveles').show(300);
        });
        $('#wholekonyvcard').mouseleave(function() {
            $('#listkonyveles').hide(300);
        })
    });

    $(document).ready(function() {
        $('#cardtargyi').mouseenter(function() {
            $('#listtargyi').show(300);
        });
        $('#wholetargyicard').mouseleave(function() {
            $('#listtargyi').hide(300);
        })
    });

    $(document).ready(function() {
        $('#cardkivonat').mouseenter(function() {
            $('#listkivonat').show(300);
        });
        $('#wholekivonatcard').mouseleave(function() {
            $('#listkivonat').hide(300);
        })
    });

    $(document).ready(function() {
        $('#egyebcard').mouseenter(function() {
            $('#listegyeb').show(300);
        });
        $('#wholeegyebcard').mouseleave(function() {
            $('#listegyeb').hide(300);
        })
    });
</script>

</html>