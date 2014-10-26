<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

/**
 * Class ProfilPage
 */
class ProfilPage
{

	public $userID;

	function __construct($id)
	{
		$this->userID = $id;
	}

    /**
     * Speichert die bearbeiteten Daten des Users in die Datenbank
     * @param $vorname
     * @param $nachname
     * @param $mail
     * @param $passwort
     * @param $isVisible
     * @param $birthday
     * @return bool
     */
    public function editUser($data)
	{
		$db = Database::getDB();

		$password = hash('sha256',$data["passwort"].$mail);

		$params = array(
			":vorname" => $data["vorname"],
			":nachname" => $data["nachname",
			":isVisible" => $data["isVisible"],
			":id" => $data["id"],
			":birthday" => $data["birthday"]
		);

		$sth = $db->prepare("UPDATE ".DB_PREFIX."_user SET vorname = :vorname, nachname = :nachname, is_visible = :isVisible, geburtsdatum = :birthday WHERE id = :id");

		$sth->execute($params);

		if($data["passwort"] != "")
		{
			$params = array(
				":password" => $password,
				":id" => $data["id"]
			);

			$sth = $db->prepare("UPDATE ".DB_PREFIX."_user SET password = :password WHERE id = :id");

			$sth->execute($params);
		}

		return true;

	}

    /**
     * Gibt die Profildaten eines Users zurück
     * @return mixed
     */
    public function getUserData()
	{
		$db = Database::getDB();

		$params = array(
			":id" => $this->userID
		);

		$sth = $db->prepare("SELECT id,vorname,nachname,is_visible,avatar,geburtsdatum FROM ".DB_PREFIX."_user WHERE id = :id");

		$sth->execute($params);		

		return $sth->fetch();
	}

    /**
     * Gibt die Vereine zurück, in denen der User aktiv ist
     * @return array
     */
    public function getVereineAktiv()
	{
		$db = Database::getDB();

		$params = array(
			":id" => $this->userID
		);

		$sth = $db->prepare("SELECT v.name, v.id FROM ".DB_PREFIX."_user_vereine uv LEFT JOIN ".DB_PREFIX."_vereine v ON uv.verein_id = v.id WHERE uv.user_id = :id && uv.bis = 0 ORDER BY uv.von ASC");

		$sth->execute($params);		

		return $sth->fetchAll();		
	}

    /**
     * Gibt die Vereine zurück, in denen der User aktiv war
     * @return array
     */
    public function getVereine()
	{
		$db = Database::getDB();

		$params = array(
			":id" => $this->userID
		);

		$sth = $db->prepare("SELECT v.name, v.id FROM ".DB_PREFIX."_user_vereine uv LEFT JOIN ".DB_PREFIX."_vereine v ON uv.verein_id = v.id WHERE uv.user_id = :id && uv.bis > 0 ORDER BY uv.von ASC");

		$sth->execute($params);		

		return $sth->fetchAll();		
	}

}

?>