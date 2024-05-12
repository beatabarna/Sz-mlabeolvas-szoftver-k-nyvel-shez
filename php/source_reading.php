<?php

namespace Gerke\Imagetotext;

if (isset($_POST["data"])) {
    switch ($_POST["data"]) {
        case 'szamlatukor':
            $path = '../data/szamlatukor.json';
            $szamlatukor_nyers = file_get_contents($path);
            $eredmeny = json_decode($szamlatukor_nyers, true);
            echo json_encode($eredmeny);
            break;
        default:
            break;
    }
}
