    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/mmp1/design/js/bootstrap.min.js"></script>

	<footer id="footer">

		<div class="container">

			<span class="glyphicon glyphicon-copyright-mark"></span> Multimediaprojekt 1 von Lukas Klappert &copy; 2014

			<?php

			if(DEBUG)
			{
				echo '<br><br><pre>DEBUG:<br><br>';
				echo $module.".php";
				echo '<br>';
				foreach($_GET as $key => $value)
				{
					echo '$_GET[\''.$key."'] : ".$value."<br>";
				}
				echo '</pre>';
			}

			?>

		</div>

	</footer>

  </body>
</html>