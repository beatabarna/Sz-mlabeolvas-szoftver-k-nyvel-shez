<?php

namespace Gerke\Imagetotext;

require_once "oop/Connection.php";
session_start();
$connection = new Connection();


if (isset($_POST["modositas"])) {
    if ($_POST["ujjelszo"] == $_POST["ujjelszoujra"]) {
        $param = [
            ":psw" => password_hash($_POST["ujjelszo"], PASSWORD_DEFAULT),
            ":utolso_belepes" => date("Y-m-d"),
            ":id" => $_SESSION["felhasznalo_id"]
        ];
        $connection->setData("UPDATE felhasznalo SET jelszo = :psw, utolso_belepes = :utolso_belepes WHERE id = :id", $param);
        header("location: cegvalaszto.php");
    } else {
        echo "nem egyezik a két jelszó";
    }
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
    <title>Jelszó módosítás</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-5 rounded shadow-lg mt-5 mx-auto" style="background-color: rgb(225, 225, 225);">
                <h2 class="text-center p-5 text-success">Jelszó megváltoztatása</h2>
                <form action="" method="post" class="text-center mt-5" id="cegform">
                    <label for="ujjelszo" class="form-label">Új jelszó</label><br>
                    <input type="password" id="ujjelszo" name="ujjelszo" class="form-control">
                    <label for="ujjelszoujra" class="form-label">Új jelszó ismét</label><br>
                    <input type="password" id="ujjelszoujra" name="ujjelszoujra" class="form-control">
                    <div class="text-center loginbutton">
                        <button type="submit" class="btn btn-outline-success mb-5" name="modositas">Módosítás</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>