<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

/**
 * Class VereinPage
 */
class VereinPage
{
	/**
	 * Erstellt einen Verein in der Datenbank
	 * @param  Array $verein Vereinsdaten aus dem Formular
	 * @return Int         	 ID des neu erstellten Vereins
	 */
	public function createVerein($verein)
	{

		if($verein["name"] == "" || $verein["ort"] == "" || !ctype_digit($verein["sportart"])) return -2;

		$db = Database::getDB();

		$sth = $db->prepare("INSERT INTO ".DB_PREFIX."_vereine (name,gruendung,adresse_strasse,adresse_strasse_nr,adresse_plz,adresse_ort,sportartID,adresse_lat,adresse_lng) 
														VALUES (:name,:gruendung,:adresse_strasse,:adresse_strasse_nr,:adresse_plz,:adresse_ort,:sportartID,:adresse_lat,:adresse_lng)");


		if(isset($verein['strasse'])) $adresse = $verein['strasse']." ".$verein["strasse_nr"].", ";
		$adresse .= $verein["plz"]." ".$verein["ort"];

		$latlng = file_get_contents("http://maps.google.com/maps/api/geocode/json?sensor=false&address=".urlencode($adresse));
		$latlng = json_decode($latlng, true);
		
		if(!isset($latlng['results'][0])) return -4;

		$latlng = $latlng['results'][0]['geometry']['location'];

		foreach($verein as $key => $value)
		{
			$verein[$key] = $purifier->purify($value);
		}

		$params = array(
  			":name" => $verein['name'],
  			":gruendung" => $verein['gruendung'],
  			":adresse_strasse" => $verein['strasse'],
  			":adresse_strasse_nr" => $verein['strasse_nr'],
  			":adresse_plz" => $verein['plz'],
  			":adresse_ort" => $verein['ort'],
  			":sportartID" => $verein['sportart'],
  			":adresse_lat" => $latlng["lat"],
  			":adresse_lng" => $latlng["lng"]
		);

		$sth->execute($params);

		$vereinID = $db->lastInsertId();

		if($vereinID < 1) return -2;

		// User anlegen, falls nötig	
		if(!isset($_COOKIE["userID"])) 
		{
			
			if($verein["passwort"] == "" || $verein["passwort"] != $verein["passwort2"]) return -3;

			$registerUser = new RegisterPage();
			$userID = $registerUser->registerUser($verein["vorname"],$verein["nachname"],$verein["mail"],$verein["passwort"],0);	

			$session = new Session();
			$session->userLogin($verein['mail'],$verein['passwort'],0);

			// Setze Admin
			$sth = $db->prepare("INSERT INTO ".DB_PREFIX."_user_vereine (user_id,verein_id,von,bis,is_admin) VALUES (:user_id,:verein_id,".date("Y").",0,1)");


			$params = array(
	  			":user_id" => $userID,
	  			":verein_id" => $vereinID
			);

			$sth->execute($params);			

		}
		else
		{
			// Setze Admin
			$sth = $db->prepare("INSERT INTO ".DB_PREFIX."_user_vereine (user_id,verein_id,von,bis,is_admin) VALUES (:user_id,:verein_id,".date(Y).",0,1)");


			$params = array(
	  			":user_id" => $_COOKIE['userID'],
	  			":verein_id" => $vereinID
			);

			$sth->execute($params);					
		}

		return $vereinID;

	}

	public function save($verein)
	{

		$config = HTMLPurifier_Config::createDefault();
		$purifier = new HTMLPurifier($config);

		if(!$this->isAdmin($_COOKIE['userID'],$verein['id'])) return false;

		$sth = Database::getDB()->prepare("UPDATE ".DB_PREFIX."_vereine SET name = :name, gruendung = :gruendung, adresse_strasse = :adresse_strasse, adresse_strasse_nr = :adresse_strasse_nr, adresse_plz = :adresse_plz, adresse_ort = :adresse_ort, sportartID = :sportartID, adresse_lat = :adresse_lat, adresse_lng = :adresse_lng WHERE id = :id");

		$adresse = "";

		if(isset($verein['strasse'])) $adresse = $verein['strasse']." ".$verein["strasse_nr"].", ";
		$adresse .= $verein["plz"]." ".$verein["ort"];

		$latlng = file_get_contents("http://maps.google.com/maps/api/geocode/json?sensor=false&address=".urlencode($adresse));
		$latlng = json_decode($latlng, true);
		$latlng = $latlng['results'][0]['geometry']['location'];

		$config->set('HTML.Allowed','');

		foreach($verein as $key => $value)
		{
			$verein[$key] = $purifier->purify($value);
		}

		$params = array(
  			":name" => $verein['name'],
  			":gruendung" => $verein['gruendung'],
  			":adresse_strasse" => $verein['strasse'],
  			":adresse_strasse_nr" => $verein['strasse_nr'],
  			":adresse_plz" => $verein['plz'],
  			":adresse_ort" => $verein['ort'],
  			":sportartID" => $verein['sportart'],
  			":id" => $verein['id'],
  			":adresse_lat" => $latlng["lat"],
  			":adresse_lng" => $latlng["lng"]
		);

		if(!isset($latlng)) return false;

		$bool = $sth->execute($params);

		return $bool;		
	}

	/**
	 * Liest Daten eines bestimmten Vereins aus der Datenbank
	 * @param  Int 		$vereinID ID des Vereins, dessen Daten ausgelesen werden sollen
	 * @return Array    Array mit den Vereinsdaten
	 */
	public function getVereinData($vereinID)
	{

		$sth =  Database::getDB()->prepare("SELECT * FROM ".DB_PREFIX."_vereine WHERE id = :id ");

		$sth->execute(array( ":id" => $vereinID ));

		$data = $sth->fetch();

		return $data;

	}

    /**
     * Liest Mitglieder eines Vereins aus
     * @param $vereinID
     * @return array
     */
    public function getVereinMembers($vereinID)
	{
		$sth =  Database::getDB()->prepare("SELECT u.id, u.vorname, u.nachname, u.is_visible, u.avatar, uv.von, uv.bis FROM ".DB_PREFIX."_user_vereine uv JOIN ".DB_PREFIX."_user u ON uv.user_id = u.id WHERE uv.verein_id = :vereinID ORDER BY u.nachname");

		$sth->execute(array( ":vereinID" => $vereinID ));

		$data = $sth->fetchAll();

		return $data;		
	}

    /**
     * Speichert neue News in der Datenbank
     * @param $news
     * @return bool
     */
    public function createVereinNews($news)
	{

		$config = HTMLPurifier_Config::createDefault();
		$purifier = new HTMLPurifier($config);
		
		if($news["titel"] == "" || $news["text"] == "" || $news["vereinID"] == "") return false;
		if(!$this->isAdmin($_COOKIE['userID'],$news['vereinID'])) return false;

		$sth =  Database::getDB()->prepare("INSERT INTO ".DB_PREFIX."_verein_news (titel,verein_id,time,text) VALUES (:titel,:verein_id,:time,:text) ");

		$params = array(
  			":titel" => $purifier->purify($news["titel"]),
  			":verein_id" => $news["vereinID"],
  			":time" => time(),
  			":text" => $purifier->purify($news["text"])
		);

		$bool = $sth->execute($params);

		return $bool;			
			
	}

    /**
     * Liest Vereinsnews aus der Datenbank aus
     * @param $vereinID
     * @return array
     */
    public function getVereinNews($vereinID)
	{
		$sth =  Database::getDB()->prepare("SELECT * FROM ".DB_PREFIX."_verein_news WHERE verein_id = :vereinID ORDER BY time DESC");

		$sth->execute(array( ":vereinID" => $vereinID ));

		$data = $sth->fetchAll();

		return $data;		
	}

    /**
     * Überprüft, ob ein User Admin eines Vereins ist
     * @param $userID
     * @param $vereinID
     * @return string
     */
    public function isAdmin($userID,$vereinID)
	{
			$sth = Database::getDB()->prepare("SELECT COUNT(*) FROM ".DB_PREFIX."_user_vereine WHERE verein_id = :vereinID && user_id = :userID && is_admin = 1");

			$params = array(
	  			":userID" => $userID,
	  			":vereinID" => $vereinID
			);

			$sth->execute($params);		

			return $sth->fetchColumn();
	}


}

?>