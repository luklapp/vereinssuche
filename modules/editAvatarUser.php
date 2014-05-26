<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

$pagetitle = "Avatar bearbeiten";

$profileEditPage = new ProfilPage($_COOKIE['userID']);

include("includes/header.php");

?>

    <div id="mapWrapper">

        <div id="map"></div>

        <div id="mapOverlay"></div>

    </div>

<div id="content" class="container">

<?php $user = $session->getUserData(); ?>

    <h1>Profil bearbeiten</h1>

<?php

if(isset($_POST['submitStep1']))
{

    if($_FILES["file"]["type"] != "image/jpg" && $_FILES["file"]["type"] != "image/jpeg" && $_FILES["file"]["type"] != "image/png" && $_FILES["file"]["type"] != "image/gif")
    {
        echo "Fehler! Nur JPG, PNG und GIF erlaubt!";
        echo $_FILES["file"]["type"];
    }
    elseif($_FILES["file"]["error"] != 0)
    {
        echo "Es ist ein unbekannter Fehler aufgetreten. Bitte versuche es erneut!";
    }
    else
    {

        $uploaddir = __DIR__.'/../design/images/temp/';
        $filename = basename($_FILES['file']['name']);

        if($_FILES["file"]["type"] == "image/jpg" || $_FILES["file"]["type"] == "image/jpeg") $src = imagecreatefromjpeg($_FILES['file']['tmp_name']);
        elseif($_FILES["file"]["type"] == "image/png") $src = imagecreatefrompng($_FILES['file']['tmp_name']);
        elseif($_FILES["file"]["type"] == "image/gif") $src = imagecreatefromgif($_FILES['file']['tmp_name']);

        list($width,$height)=getimagesize($_FILES['file']['tmp_name']);

        if($width < 150 || $height < 150) echo '<div class="alert alert-danger">Das Bild muss mindestens 150 Pixel breit und hoch sein!</div>';

        else
        {

            if($width > 800 || $height > 800)
            {
                if($width > $height) {$newHeight = $height*(800/$width); $newWidth = 800;}
                else {$newWidth = $width*(800/$height); $newHeight = 800;}
            }
            else
            {
                $newWidth = $width;
                $newHeight = $height;
            }


            $tmp = imagecreatetruecolor($newWidth,$newHeight);

            imagecopyresampled($tmp,$src,0,0,0,0,$newWidth,$newHeight,$width,$height);

            $uploadfile = $uploaddir . $filename;

            if(!imagejpeg($tmp,$uploadfile,100)){
                echo "Problem beim Speichern der Datei.\n";
            } else {

                ?>

                <script src="3rd-party/jquery.imgareaselect.min.js"></script>

                <script type="text/javascript">
                    $(document).ready(function () {
                        $('img#photo').imgAreaSelect({ aspectRatio: '1:1', handles: true, onSelectEnd: function (img, selection) {
                            $('input[name="x1"]').val(selection.x1);
                            $('input[name="y1"]').val(selection.y1);
                            $('input[name="x2"]').val(selection.x2);
                            $('input[name="y2"]').val(selection.y2);
                        } });
                    });
                </script>

                Bitte wähle nun den Ausschnitt, den du verwenden möchtest <br><br>

                <img id="photo" src="design/images/temp/<?php echo $filename;?>">


                <form action="" method="POST">

                    <input type="hidden" name="x1" value="" />
                    <input type="hidden" name="y1" value="" />
                    <input type="hidden" name="x2" value="" />
                    <input type="hidden" name="y2" value="" />
                    <input type="hidden" name="imagename" value="<?php echo $filename;?>" />

                    <input type="submit" name="submitStep2" value="Bildausschnitt speichern">

                </form>

            <?php

            }

        }

    }

}



elseif(isset($_POST['submitStep2']))
{

    $filename = __DIR__.'/../design/images/temp/'.$_POST['imagename'];

    if(!file_exists($filename) || !ctype_digit(strval(getimagesize($filename)[0])))
    {
        echo 'Es ist ein unbekannter Fehler aufgetreten. Bitte versuche es erneut!';
    }
    else
    {

        // Neue Höhe + Breite
        list($width, $height) = getimagesize($filename);
        $new_width = $_POST['x2']-$_POST['x1'];
        $new_height = $_POST['y2']-$_POST['y1'];

        $image_p = imagecreatetruecolor($new_width, $new_height);
        $image = imagecreatefromjpeg($filename);
        imagecopyresampled($image_p, $image, 0, 0, $_POST['x1'], $_POST['y1'], $new_width, $new_height, $_POST['x2']-$_POST['x1'], $_POST['y2']-$_POST['y1']);

        if(imagejpeg($image_p, __DIR__.'/../design/images/profilbilder/'.$user->id.'.jpg', 100))
        {
            $session->setAvatar($_COOKIE['userID']);
            echo "Avatar erfolgreich geändert.";
        }
        else
        {
            echo "Es ist ein unbekannter Fehler aufgetreten. Bitte versuche es erneut!";
        }

    }

}

else
{

    ?>

    <form method="POST" enctype="multipart/form-data">


        <div class="form-group">
            <label>Bitte wähle die Datei aus, die du als Avatar verwenden möchtest</label>
            <input type="file" name="file">
        </div>


        <input name="submitStep1" value="Profil speichern" type="submit" >
    </form>

    </div>

<?php } ?>