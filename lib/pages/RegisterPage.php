<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

/**
 * Class RegisterPage
 */
class RegisterPage
{

    /**
     * Registriert einen neuen User in der Datenbank
     * @param $vorname
     * @param $nachname
     * @param $mail
     * @param $passwort
     * @param $isVisible
     * @return int
     */
    public function registerUser($vorname,$nachname,$mail,$passwort,$isVisible)
	{
		$db = Database::getDB();


		// Überprüfe, ob die Mail bereits existiert
		$sth = $db->prepare("SELECT COUNT(*) FROM ".DB_PREFIX."_user WHERE email = :email");
		$sth->execute(array(":email" => $mail));
		if($sth->fetchColumn() > 0) return -1;

		// Passwort generieren
		$password = hash('sha256',$passwort.$mail);

		$params = array(
			":vorname" => $vorname,
			":nachname" => $nachname,
			":mail" => $mail,
			":passwort" => $password,
			":isVisible" => $isVisible
		);

		$sth = $db->prepare("INSERT INTO ".DB_PREFIX."_user (vorname,nachname,email,password,is_visible) VALUES (:vorname,:nachname,:mail,:passwort,:isVisible)");

		$sth->execute($params);

		return $db->lastInsertId();

	}

}

?>