<?php

namespace Gerke\Imagetotext;
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
    <script src="../js/fuggvenyek.js"></script>
    <title>Login</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col"></div>
            <div class="login col-5 rounded shadow-lg mt-5">
                <h2 class="text-center p-5 text-dark">Bejelentkezés</h2>
                <form action="" method="post" class="text-center mt-5" id="loginform">
                    <label for="email" class="form-label">Email cím</label><br>
                    <input type="email" id="email" name="email" class="form-control"><br>
                    <label for="jelszo" class="form-label">Jelszó</label><br>
                    <input type="password" id="jelszo" name="jelszo" class="form-control">
                    <div class="alert alert-success text-center mt-3 hidden" role="alert" id="alertbox_login">
                        <strong>Sikeres rögzítés!</strong>
                    </div>
                    <div class="text-center loginbutton">
                        <button type="button" class="btn btn-outline-dark" onclick="validation()">Bejelentkezés</button>
                    </div>
                </form>
            </div>
            <div class="col"></div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#registration').click(function() {
                $('.login').hide();
                $('.reg').show();

            });
        });

        function validation() {
            var form = $("#loginform");
            var data = getFormData(form);

            var url = 'authentication.php';
            $.ajax({
                method: 'post',
                url: url,
                data: {
                    "data": data
                },
                success: function(response) {
                    console.log(response);
                    switch (response) {
                        case 'ok':
                            window.location.replace("cegvalaszto.php");
                            break;
                        case 'psw':
                            window.location.replace("jelszomodositas.php");
                            break;
                        case 'rosszjelszo':
                            hiba("Hibás jelszó!", "alertbox_login");
                            break;
                        case 'nincsfelhasznalo':
                            hiba("Hibás felhasználónév!", "alertbox_login");
                            break;
                        case 'inaktivfelhasznalo':
                            hiba("Inaktív fiók!", "alertbox_login");
                            break;
                        default:
                            console.log(response);
                            break;
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Hiba");
                    console.log(textStatus, errorThrown);
                }
            });
        }

        function getFormData(form) {
            var unindexed_array = form.serializeArray();
            var indexed_array = {};

            $.map(unindexed_array, function(n, i) {
                indexed_array[n['name']] = n['value'];
            });

            return indexed_array;
        }
    </script>


</body>

</html>