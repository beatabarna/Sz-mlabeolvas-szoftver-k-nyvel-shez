<?php
namespace Gerke\Imagetotext;
session_start();
$files;
$konyvelt_szamlak;
$feltoltesValtozok = [
    "oldalCim" => "",
    "eleresiUt" => ""
];
switch ($_GET["type"]) {
    case "szallito":
        $_SESSION["regType"] = "szallito";
        $files = glob("../invoices/szallito/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/*"); 
        $konyvelt_szamlak = glob("../invoices/szallito/" . $_SESSION["cegadoszam"] . "/*");
        $feltoltesValtozok = [
            "oldalCim" => "Szállítói számlák feltöltése",
            "eleresiUt" => "../invoices/szallito/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/"
        ];
        break;
    case "vevo":
        $_SESSION["regType"] = "vevo";
        $files = glob("../invoices/vevo/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/*");
        
        $konyvelt_szamlak = glob("../invoices/vevo/" . $_SESSION["cegadoszam"] . "/*");
        $feltoltesValtozok = [
            "oldalCim" => "Vevő számlák feltöltése",
            "eleresiUt" => "../invoices/vevo/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/"
        ];
        print_r($feltoltesValtozok);
        break;
    case "penztar":
        $_SESSION["regType"] = "penztar";
        $files = glob("../invoices/penztar/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/*");
        $konyvelt_szamlak = glob("../invoices/penztar/" . $_SESSION["cegadoszam"] . "/*");
        $feltoltesValtozok = [
            "oldalCim" => "Pénztár számlák feltöltése",
            "eleresiUt" => "../invoices/penztar/" . $_SESSION["cegadoszam"] . "/feldolgozasra_var/"
        ];
        break;
    default:
        break;
};

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="../style.css">
    <title>Számlák könyvelése</title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <script src="../js/fuggvenyek.js"></script>
</head>

<body>
    <?php include('navbar.php')?>
    <div id="contentContainer" class="suppinvoice container mx-auto m-5 shadow rounded" style="background-color: rgba(225, 230, 224);">
        <div class="row">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col-10 mx-auto text-center pt-3 mt-3">
                <h3><?= $feltoltesValtozok["oldalCim"] ?></h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="p-4">
            <div class="row">
                <div id="columnChange" class="mx-auto">
                    <form action="upload_process.php" method="post" class="mt-4" name="szamla" id="form_1" enctype="multipart/form-data">
                        <div class="row mt-3 mb-3">
                            <div class="input-group col-6">
                                <input type="file" class="form-control" id="fileInput" accept="application/pdf" name="files[]" multiple>
                            </div>
                            <div class="col-6"></div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <input type="submit" class="btn btn-outline-dark" name="mentes" value="Kiválasztott fájlok feltöltése">
                                <input type="text" name="szamlatipus" value="szallito" hidden>
                            </div>
                            <div class="col-10"></div>
                        </div>
                        <hr style="margin-bottom: 25px;">
                        <div class="row">
                            <div class="col">
                                <input type="button" class="btn btn-outline-dark" name="szamla_nelkul" value="Számla nélküli könyvelés" onclick="picknone()">
                                <input type="button" class="btn btn-outline-dark" name="all" value="Összes betöltése" onclick="pickall()">
                            </div>
                            <div class="col-7"></div>
                        </div>
                    </form>
                    <h4 class="mt-5 mb-4 text-center">feldolgozásra váró számlák</h4>
                    <div class="text-center">
                        <?php foreach ($files as $file) : ?>
                            <?php echo '<a class="btn btn-light col-3 border border-dark p-2 m-2" value="' . $file . '" onclick="pickinvoice(this);" style="overflow:hidden;">' . str_replace($feltoltesValtozok["eleresiUt"], "", $file) . '</a>'; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    </script>
</body>

</html>