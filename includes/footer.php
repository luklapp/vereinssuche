<?Php
/**
* @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
* Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
*/
?>

	<footer id="footer">

		<div class="container">

			<span class="glyphicon glyphicon-copyright-mark"></span> Multimediaprojekt 1 von Lukas Klappert &copy; 2014

			<br><br>

			<a href="Impressum">Impressum</a>

		</div>

	</footer>

</div>

<!-- Login Modal -->
<div class="modal fade bs-example-modal-sm" id="loginModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Login</h4>
      </div>
      <form action="Login" method="POST">
      <div class="modal-body">
        
        <div class="form-group">
          <label>E-Mail-Adresse</label>
          <input name="mail" type="email" class="form-control" placeholder="mail@provider.tld" required>
        </div>
        <div class="form-group">
          <label>Passwort</label>
          <input name="passwort" type="password" class="form-control" required>
        </div>
        <div class="form-group">
        	<a href="Passwort">&raquo; Passwort vergessen</a>
        </div>
		<div class="form-group">
          <input name="keepLoggedIn" type="checkbox" value="1"> Eingeloggt bleiben
        </div>

        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
        <button type="submit" class="btn btn-primary" name="submit">Einloggen</button>

      </div>
      </form>
    </div>
  </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Vereinssuche</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label>Dein Ort</label>
                    <input id="adresse" class="form-control" placeholder="Ort, in dessen Umkreis du nach Vereinen suchen möchtest">
                </div>

                <br>

                <div class="form-group">
                    <label>Sportart</label>
                    <select id="sportart" class="form-control" name="sportart">
                        <option value="0">Alle</option>
                        <?php
                        $sportarten = get_sportarten();
                        foreach($sportarten as $sportart)
                        {
                            echo '<option value="'.$sportart->id.'">'.$sportart->name.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Umkreis (in km)</label>
                    <select id="umkreis" class="form-control" name="umkreis">
                        <option>5</option>
                        <option>10</option>
                        <option>15</option>
                        <option>20</option>
                        <option>50</option>
                    </select>
                </div>

                <p class="center"><b>- oder -</b></p>

                <div class="form-group">
                    <label>Verein nach Name suchen</label>
                    <input id="name" class="form-control" placeholder="Vereinsname">
                </div>

            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="javascript:loadMap();">Suche starten</button>
            </div>
        </div>
    </div>
</div>



  </body>
</html>