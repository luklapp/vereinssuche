<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

/**
 * Class LoginPage
 */
class LoginPage
{
    /**
     * Gibt das Loginformular aus
     */
    public function printLoginForm()
	{
		echo '
		<form action="" method="POST">
			<div class="form-group">
	          <label>E-Mail-Adresse</label>
	          <input name="mail" type="email" class="form-control" placeholder="mail@provider.tld" required>
	        </div>
	        <div class="form-group">
	          <label>Passwort</label>
	          <input name="passwort" type="password" class="form-control" required>
	        </div>

	        <button type="submit" class="btn btn-primary" name="submit">Einloggen</button>
	    </form>
	    ';
	}

}

?>