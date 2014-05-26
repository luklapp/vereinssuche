<?php
/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */
?>
<!DOCTYPE html>
<html lang="de">
  <head>
    <base href="http://mmt.dev/mmp1/">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pagetitle; ?> - Vereinssuche</title>

    <!-- Bootstrap -->
    <link href="design/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="design/css/style.css" media="all">

    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=false"></script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

    <script type="application/x-social-share-privacy-settings">{"path_prefix":"http://panzi.github.io/SocialSharePrivacy/","layout":"line","services":{"buffer":{"status":false},"delicious":{"status":false},"disqus":{"status":false},"fbshare":{"status":false},"flattr":{"status":false},"hackernews":{"status":false},"linkedin":{"status":false},"mail":{"status":false},"pinterest":{"status":false},"reddit":{"status":false},"stumbleupon":{"status":false},"tumblr":{"status":false},"xing":{"status":false}}}</script>

    <script src="3rd-party/ckeditor/ckeditor.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="design/js/bootstrap.min.js"></script>


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>
  <body>  

<nav class="navbar navbar-default" role="navigation">
  <div class="container">
    <div class="container-fluid">
      <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
        <a class="navbar-brand" href="index.php">Vereinssuche</a>
      </div>

      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav navbar-right">
          <?php if($session->checkLoginStatus() == false)
          { ?>
          <li><a href="Login" data-toggle="modal" data-target="#loginModal" data-remote="false">Login</a></li>
          <li><a href="Registrieren">Für Sportler</a></li>
          <li><a href="Verein/create">Für Vereine</a></li>
          <?php } else { ?>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Mein Konto <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="User/<?php echo $session->getUserID();?>">Mein Profil</a></li>
              <li class="divider"></li>
              <?php 
              foreach($session->getAdminVereine() as $vereinHeader)
              {
                echo '<li><a href="Verein/'.$vereinHeader->id.'">'.$vereinHeader->name.'</a></li>';
              }
              ?>
              <li><a href="Verein/create">Verein erstellen</a></li>
              <li class="divider"></li>
              <li><a href="Logout">Abmelden</a></li>
            </ul>
          </li>
          
          <?php } ?>
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
  </div><!-- /.container -->
</nav>