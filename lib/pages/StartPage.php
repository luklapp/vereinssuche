<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

/**
 * Class LoginPage
 */
class StartPage
{
    /**
     * Gibt die Vereinsnews für eingeloggte User aus
     * @return bool
     */
    public function printVereinNews()
    {
        $db = new Database();

        echo 'Hier findest du eine Liste mit News deiner Teams:<br>';

        $profilPage = new ProfilPage($_COOKIE['userID']);

        $where = "";
        $i = 0;

        foreach($profilPage->getVereine() as $verein)
        {
            if($i == 0) $where .= "vn.verein_id = ".$verein->id;
            else $where .= " || vn.verein_id = ".$verein->id;
            $i++;
        }

        foreach($profilPage->getVereineAktiv() as $verein)
        {
            if($i == 0) $where .= "vn.verein_id = ".$verein->id;
            else $where .= " || vn.verein_id = ".$verein->id;
            $i++;
        }

        if($where == "") return false;

        $sth = Database::getDB()->prepare("SELECT vn.verein_id, vn.titel, vn.time, vn.text, v.name FROM ".DB_PREFIX."_verein_news vn LEFT JOIN ".DB_PREFIX."_vereine v ON vn.verein_id = v.id WHERE $where ORDER BY vn.time DESC LIMIT 10");
        $sth->execute();

        $result = $sth->fetchAll();

        if(sizeof($result) == 0){
            echo '<p>Deine Vereine haben noch keine News gepostet oder du bist noch keinem Verein beigetreten.</p>';
            return false;
        }

        else
        {
            foreach($result as $news)
            {
                echo '<h2>'.$news->titel.'</h2>';
                echo '<span class="date"><span class="glyphicon glyphicon-time"></span> Geschrieben am '.date('d.m.Y, H:i',$news->time).' Uhr von <a href="Verein/'.$news->verein_id.'">'.$news->name.'</a></span>';
                echo '<p>'.$news->text.'</p>';
                echo '<hr>';
            }
        }



        return true;

    }

    /**
     * Gibt die Startseite für Gäste aus
     */
    public function printStartseite()
    {

    $session = new Session();

    echo "<h3>Worum geht es hier?</h3>

    <p>Auf dieser Seite hast du die Möglichkeit, nach Sportvereinen in deiner Nähe zu suchen. Nutze den Button 'Jetzt Verein suchen', um das Suchformular aufzurufen.</p>

    <h3>Wonach kann ich filtern</h3>

    <p>Du hast die Möglichkeit, nach dem Radius und der Sportart zu filtern. Zudem kannst du den Ort eingeben, in dessen Umkreis du suchen möchtest.</p>

    <h3>In meiner Umgebung werden keine Vereine gefunden?</h3>

    <p>Bei dieser Plattform handelt es sich um eine Community. Das heißt, dass die Vereine sich selbst eintragen müssen. Du bist eine Verein und möchtest, dass auch
       dein Verein auf dieser Seite zu finden sein soll? Dann nutze den Link 'Für Vereine' oben rechts!
    </p>

    <h3>Jetzt registrieren!</h3><p>Wenn du dich registrierst, hast du die Möglichkeit, dich mit deinem Verein zu verbinden oder neue Vereine zu erstellen. <a href='Registrieren'>Melde dich noch heute kostenlos an</a>!";

    }

}

?>