<?php

namespace Gerke\Imagetotext;

if(is_file($_SESSION["profilkep"])){
    echo '
    <div class="container pt-2 navbar">
        <div class="col menunav p-2 border-top border-bottom">
            <div class="row">
                <div class="col ms-2">
                    <div class="row ">
                        <a class="btn w-75" title="felhasználói fiók" class="" href="felhasznalo.php" style="font-variant: small-caps;">
                            <div style="display: flex; align-items: center;">
                                <img src="' . $_SESSION["profilkep"] . '" style="width:40px;height:40px;border-radius: 30px;">
                                <div style="margin-left: 10px; display: flex; flex-direction: column; justify-content: center;">
                                    <div class="text-white" style="margin-bottom:-4px; margin-left:-75px; font-size: 18px">'.$_SESSION["felhasznalo"].'</div>
                                    <div class="text-white" style="font-size:13px;">'.$_SESSION["felhasznalo_email"].'</div> 
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col text-center">
                    <a class="btn text-center w-50" title="cégváltás" href="cegvalaszto.php">
                        <div class="text-white" style="font-variant: small-caps; font-size: larger; font-weight: bold">' . $_SESSION["cegnev"] . '</div>
                        <div class="text-white" style="font-variant: small-caps; font-size: smaller;">' . $_SESSION["cegadoszam"] . '</div>
                    </a>
                </div>
                <div class="col text-end pt-2">
                    <a title="home" class="btn btn-outline-white border" href="homepage.php"><i class="fa-solid fa-house text-white"></i></a>
                    <a title="kijelentkezés" class="btn btn-outline-white border" href="login.php"><i class="fa-solid fa-arrow-right-from-bracket text-white mx-1"><span id="timer" class="ps-3" style="font-family:helvetica;">15:00</span></i></a>
                </div>
            </div>
        </div>
    </div>';
}else{
    echo '
    <div class="container pt-2">
        <div class="col menunav p-2 border-top border-bottom">
            <div class="row">
                <div class="col ms-2">
                    <div class="row ">
                        <a class="btn w-75" title="felhasználói fiók" class="" href="../felhasznalo.php" style="font-variant: small-caps;">
                            <div style="display: flex; align-items: center;">
                                <img src="../' . $_SESSION["profilkep"] . '" style="width:40px;height:40px;border-radius: 30px;">
                                <div style="margin-left: 10px; display: flex; flex-direction: column; justify-content: center;">
                                    <div class="text-white" style="margin-bottom:-4px; margin-left:-75px; font-size: 18px">'.$_SESSION["felhasznalo"].'</div>
                                    <div class="text-white" style="font-size:13px;">'.$_SESSION["felhasznalo_email"].'</div> 
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col text-center">
                    <a class="btn text-center w-50" title="cégváltás" href="../cegvalaszto.php">
                        <div class="text-white" style="font-variant: small-caps; font-size: larger; font-weight: bold">' . $_SESSION["cegnev"] . '</div>
                        <div class="text-white" style="font-variant: small-caps; font-size: smaller;">' . $_SESSION["cegadoszam"] . '</div>
                    </a>
                </div>
                <div class="col text-end pt-2">
                    <a title="home" class="btn btn-outline-white border" href="../homepage.php"><i class="fa-solid fa-house text-white"></i></a>
                    <a title="kijelentkezés" class="btn btn-outline-white border" href="../logout.php"><i class="fa-solid fa-arrow-right-from-bracket text-white mx-1"><span id="timer" class="ps-3" style="font-family:helvetica;">15:00</span></i></a>
                </div>
            </div>
        </div>
    </div>';
}


