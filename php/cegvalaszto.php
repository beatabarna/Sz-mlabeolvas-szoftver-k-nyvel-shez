<?php

namespace Gerke\Imagetotext;

require_once "oop/Connection.php";
session_start();
$connection = new Connection();
$ceglista = $connection->getData("SELECT * FROM ceg;");

if (isset($_POST["cegek"])) {
    $adatok = explode("_", $_POST["cegek"]);
    $_SESSION["cegadoszam"] = $adatok[1];
    $_SESSION["cegnev"] = $adatok[0];
    header("location: homepage.php");
}

?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <title>Cégválasztás</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-5 rounded shadow-lg mt-5 mx-auto cegvalaszto" style="background-image: url('../images/cegbg.jpg'); height:800px;">
                <h2 class="text-center p-5 text-dark">Cég kiválasztása</h2>
                <form action="" method="post" class="text-center mt-5" id="cegform">
                    <p style="font-variant: small-caps; font-size:19px;">Cégek</p>
                    <select class="form-select shadow mx-auto" multiple name="cegek" id="ceg" style="width: 70%;">
                        <?php foreach ($ceglista as $index => $cegadatok) : ?>
                            <option value="<?php echo $cegadatok["nev"] . "_" . $cegadatok["adoszam"] ?>" id="<?php echo $cegadatok["adoszam"] ?>"><?php echo $cegadatok["nev"] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="text-center loginbutton">
                        <div class="row">
                            <div class="col">
                                <button type="submit" class="btn btn-dark mb-5">Kiválaszt</button>
                            </div>
                        </div>
                        <?php if ($_SESSION["user_level"] == 1) : ?>
                            <a href="cegrogzites.php" class="btn btn-success mb-5">Új cég rögzítése</a>
                            <a href="felhasznalo_rogzites.php" class="btn btn-success mb-5">Új felhasználó rögzítése</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>