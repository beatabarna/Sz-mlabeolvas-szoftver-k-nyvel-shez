<?php
namespace Gerke\Imagetotext;
session_start();
require('oop/Connection.php');
$connection = new Connection();
$ugyfellista = $connection->getData("SELECT * FROM ceg");
$merleg = array();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../style.css">
    <title>Mérleg</title>
    <script src="https://unpkg.com/pdfjs-dist@2.10.377/build/pdf.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/ef4c73c0c7.js" crossorigin="anonymous"></script>
    <script src="../js/fuggvenyek.js"></script>
    <style>
        table,
        td,
        th {
            border: 1px solid;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <?php include('navbar.php') ?>
    <div id="" class="container mx-auto m-5 shadow" style="background-color: rgba(225, 230, 224);">
        <div class="row pb-4">
            <div class="col-1 pt-3">
                <a href="homepage.php" class="btn btn-outline-dark btn-sm mb-2"><i class="fa-solid fa-arrow-left"></i></a>
            </div>
            <div class="col-10 mx-auto text-center pt-3 mt-3">
                <h3>Mérleg</h3>
            </div>
            <div class="col-1"></div>
        </div>
        <div class="row pb-4 text-center">
            <form action="" id="merleg" class="text">
                <input class="p-1 ps-2" type="text" placeholder="évszám" name="ev" id="ev">
                <div class="btn btn-dark m-2" onclick="getMerleg(); showPrintButton();"><i class="fa-regular fa-circle-down"></i> Lekérés</div>
            </form>
        </div>
        <div id="merlegtable"></div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end pb-3">
            <button onclick="printTable('merlegtable')" class="btn btn-outline-success" id="printButton" style="display: none;"><i class="fa-solid fa-print"></i> Nyomtatás</button>
        </div>
    </div>
    <script>
        function printTable(elementId) {
            let printElement = document.getElementById(elementId);
            let cegName = <?php echo json_encode($_SESSION['cegnev']); ?>;
            // Check if the element exists
            if (printElement) {
                let tableHTML = printElement.outerHTML;
                let newWin = window.open('', '_blank', 'width=800,height=600');
                newWin.document.open();
                newWin.document.write(`<!DOCTYPE html>
            <html>
            <head>
            <title>Mérleg</title>
            <h2>${cegName}</h2>
            <style>
                /* Add your table styles here */
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

                table {
                    padding-bottom: 50px;
                }

                h2 {
                    text-align: center;
                }
                @media print {
                    /* Ensure the printing content is 100% width */
                    html, body {
                        width: 100%;
                        margin: 0;
                        padding: 0;
                        display: block;
                    }
                }
            </style>
        </head>
        <body onload="window.print(); window.close();">${tableHTML}</body>
        </html>`);
                newWin.document.close();
            } else {
                console.error('Element with ID ' + elementId + ' not found.');
            }
        }
    </script>
</body>

</html>