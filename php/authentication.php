<?php

namespace Gerke\Imagetotext;

require('oop/Connection.php');
session_start();
$conn = new Connection();

if (isset($_POST['data'])) {
    $auth_data["email"] = htmlspecialchars($_POST['data']["email"], ENT_QUOTES, 'UTF-8', false);
    $auth_data["jelszo"] = htmlspecialchars($_POST['data']["jelszo"], ENT_QUOTES, 'UTF-8', false);
    if (isset($auth_data["email"]) && isset($auth_data["jelszo"])) {
        $sql = 'SELECT * FROM felhasznalo WHERE email = :email';
        $params = [':email' => $auth_data["email"]];
        $result = $conn->getData($sql, $params);
        if (count($result) == 1) {
            if (password_verify($auth_data["jelszo"], $result[0]["jelszo"])) {
                if ($result[0]["aktiv"] == 1) {
                    $_SESSION["felhasznalo"] = $result[0]["nev"];
                    $_SESSION["felhasznalo_email"] = $result[0]["email"];
                    $_SESSION["felhasznalo_id"] = $result[0]["id"];
                    $_SESSION["user_level"] = $result[0]["admin"];
                    $_SESSION["utolso_belepes"] = $result[0]["utolso_belepes"];
                    $params = [":email" => $auth_data["email"], "datum" => date("Y-m-d")];
                    $conn->setData("UPDATE felhasznalo SET utolso_belepes = :datum WHERE email = :email", $params);
                    if (file_exists('../data/profile_information.json')) {
                        $profilok = file_get_contents('../data/profile_information.json');
                        $profilok = json_decode($profilok, true);
                        $_SESSION["profilkep"] = "../images/profile_pictures/" . $profilok[$auth_data["email"]];
                    }
                    if ($result[0]["utolso_belepes"] == "0000-00-00") {
                        echo "psw";
                    } else {
                        $_SESSION["cegnev"] = "";
                        $_SESSION["cegadoszam"] = "Nincs cég kiválasztva!";
                        echo "ok";
                    }
                }else{
                    echo "inaktivfelhasznalo";
                }
            } else {
                echo "rosszjelszo";
            }
        } else {
            echo "nincsfelhasznalo";
        }
    } elseif (!isset($auth_data["email"]) || isset($auth_data["jelszo"])) {
    }
}
