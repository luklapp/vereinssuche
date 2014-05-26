<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

/**
 * Session Klasse
 */
class Session
{

    private $user = array();

    public function __construct()
    {

        $this->checkLoginStatus();

    }

    /**
     * Überprüft den Loginstatus
     * @return bool
     */
    public function checkLoginStatus()
    {
        if (isset($_COOKIE['PHPSESSID']) && isset($_COOKIE['userID']) && $this->checkCorrectLogin($_COOKIE['PHPSESSID'],$_COOKIE['userID'])) {
            $this->loadUserData();
            return true;
        }

        $this->userLogout();
        return false;
    }

    /**
     * Überprüft, ob der Login korrekt ist
     * @param $sessID
     * @param $userID
     * @return bool
     */
    public function checkCorrectLogin($sessID,$userID)
    {

        $params = array(
            ':userID' => $sessID,
            ':sessID' => $userID,
            ':ip' => $_SERVER["REMOTE_ADDR"],
            ':userAgent' => $_SERVER['HTTP_USER_AGENT']
        );

        $query = Database::getDB()->prepare("SELECT COUNT(*) FROM ".DB_PREFIX."_user_session WHERE userID = :userID && session_id = :sessID && ip = :ip && userAgent = :userAgent");
        $query->execute($params);

        if($query->rowCount() > 0) return true;
        else return false;

    }


    /**
     * Lädt die Daten des Users und speichert sie in $user
     */
    private function loadUserData()
    {
        $params = array(
            ':userID' => $_COOKIE['userID']
        );

        $query = Database::getDB()->prepare("SELECT id,email,vorname,nachname,geburtsdatum,is_visible FROM ".DB_PREFIX."_user WHERE id = :userID");

        $query->execute($params);

        $result = $query->fetch();

        $this->user = $result;
    }

    /**
     * Login Funktion
     * @param $email
     * @param $password
     * @param $permanent
     * @return bool
     */
    public function userLogin($email, $password, $permanent)
    {
        $params = array(
            ':email' => $email
        );

        $query = Database::getDB()->prepare("SELECT id, password, email FROM ".DB_PREFIX."_user WHERE email = :email");
        $query->execute($params);
        $result = $query->fetch();

        if($result == false) return false;

        if(hash('sha256',$password.$email) != $result->password) return false;

        // Login erfolgreich
        else
        {

            session_regenerate_id();

            if($permanent == 0) $cookieTime = 0;
            else $cookieTime = time() + (365*24*60*60);

            setcookie('PHPSESSID', session_id(), $cookieTime, '/');
            setcookie('userID', $result->id, $cookieTime, '/');

            // Update Session in Database
            $params = array(
                ':userID' => $result->id,
                ':sessID' => session_id(),
                ':ip' => $_SERVER["REMOTE_ADDR"],
                ':userAgent' => $_SERVER['HTTP_USER_AGENT']
            );

            $query = Database::getDB()->prepare("INSERT INTO ".DB_PREFIX."_user_session (session_id,userAgent,ip,userID) VALUES (:sessID,:userAgent,:ip,:userID) ");
            $query->execute($params);

            $query = Database::getDB()->prepare("UPDATE ".DB_PREFIX."_user SET forgot_password = '' WHERE email = :email");
            $query->execute((array(':email' => $email)));

            return true;

        }

    }

    /**
     * Speichert das neue Passwort in die Datenbank
     * @param $key
     * @param $mail
     * @param $pw
     */
    public function resetPassword($key,$mail,$pw)
    {
        $params = array(
            ':mail' => $mail,
            ':key' => $key,
            ':password' => hash('sha256',$pw.$mail)
        );

        $query = Database::getDB()->prepare("UPDATE ".DB_PREFIX."_user SET password = :password, forgot_password = '' WHERE email = :mail && forgot_password = :key");
        $query->execute($params);

    }

    /**
     * Generiert einen zufälligen Key zur Passwortwiederherstellung und sendet diesen per Mail
     * @param $mail
     * @return int
     */
    public function forgotPassword($mail)
    {

        $uniqid = uniqid("",true);

        $params = array(
            ':email' => $mail,
            ':uniqid' => $uniqid
        );

        $query = Database::getDB()->prepare("UPDATE ".DB_PREFIX."_user SET forgot_password = :uniqid WHERE email = :email");
        $query->execute($params);

        $message = "Hallo ".$this->getUserName.",\nbitte klicke auf folgenden Link, um dein Passwort zurückzusetzen:\n\nhttp://multimediatechnology.at/~fhs36110/wp2/mmp1/Passwort?key=".$uniqid."&mail=".$mail."\n\nWenn du dein Passwort nicht zurücksetzen möchtest, kannst du diese Mail einfach ignorieren.";

        mail($mail,"Dein Passwort auf Vereinssuche",$message);

        return $query->rowCount();

    }

    /**
     * Überprüft, ob ein User eingeloggt ist
     * @return bool
     */
    public function isLoggedIn()
    {
        return isset($_COOKIE['userID']);
    }

    /**
     * Logout Funktion
     */
    public function userLogout()
    {
        if(isset($_COOKIE['userID'])) setcookie('userID', '', time()-3600, '/');

    }

    /**
     * Gibt die Daten des Users zurück
     * @return array
     */
    public function getUserData()
    {
        return $this->user;
    }

    public function getUserName()
    {
        return $this->user->vorname;
    }

    public function getUserID()
    {
        return $this->user->id;
    }

    public function setAvatar($userID)
    {

        $params = array(
            ":id" => $userID
        );

        $query = Database::getDB()->prepare("UPDATE ".DB_PREFIX."_user SET avatar = true WHERE id = :id");
        $query->execute($params);
    }


    public function getAdminVereine()
    {

        $params = array(
            ":userID" => $_COOKIE["userID"]
        );

        $query = Database::getDB()->prepare("SELECT v.name, v.id FROM ".DB_PREFIX."_vereine v JOIN ".DB_PREFIX."_user_vereine uv ON v.id = uv.verein_id WHERE uv.user_id = :userID && is_admin = 1");
        $query->execute($params);
        $result = $query->fetchAll();

        return $result;

    }

    public function isInVerein($vereinID)
    {

        $params = array(
            ":userID" => $_COOKIE["userID"],
            ":vereinID" => $vereinID
        );

        $query = Database::getDB()->prepare("SELECT COUNT(*) FROM ".DB_PREFIX."_user_vereine WHERE user_id = :userID && verein_id = :vereinID");
        $query->execute($params);
        $result = $query->fetchColumn();

        if($result > 0) return true;
        else return false;
    }

    public function addVerein($verein)
    {

        if($verein["von"] > $verein["bis"] && $verein["bis"] > 0) return false;

        $params = array(
            ":userID" => $_COOKIE["userID"],
            ":vereinID" => $_POST['vereinID'],
            ":von" => $_POST['von'],
            ":bis" => $_POST['bis']
        );

        $query = Database::getDB()->prepare("INSERT INTO ".DB_PREFIX."_user_vereine (verein_id,user_id,von,bis) VALUES (:vereinID,:userID,:von,:bis)");
        $bool = $query->execute($params);

        return $bool;
    }

    public function removeVerein($verein)
    {

        $params = array(
            ":userID" => $_COOKIE["userID"],
            ":vereinID" => $verein
        );

        $query = Database::getDB()->prepare("DELETE FROM ".DB_PREFIX."_user_vereine WHERE user_id = :userID && verein_id = :vereinID && is_admin = 0");
        $bool = $query->execute($params);

        return $query->rowCount();
    }



}
