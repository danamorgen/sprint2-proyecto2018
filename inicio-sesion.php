<?php
require_once("funciones.php");

if (estaLogueado()) {
		header('location: perfil.php');
		exit;
	}
	// Variables para persistencia
	$email = '';
	// Array de errores vacío
	$errores = [];
	// Si envían algo por $_POST
	if ($_POST) {
		$email = trim($_POST['e-mail']);
		$errores = validarLogin($_POST);
		if (empty($errores)) {
			$usuario = existeMail($email);
			loguear($usuario);
			// Seteo la cookie
			if (isset($_POST["recordar"])) {
	        setcookie('id', $usuario['id'], time() + 3600 * 24 * 30);
	      }
			header('location: perfil.php');
			exit;
		}
	}
 ?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Voyager - Planificá tu viaje</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">
    <link rel="stylesheet" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        p {
            font-size: 1.2em;
        }
    </style>
</head>

<body>
  <header class="fixed-top row bg-blue justify-content-md-between mb-3 p-1 pl-2 d-flex align-items-center">
      <div class="col-12 col-sm-6 col-md-2 col-lg-3 ">
          <img alt="logotipo" src="imagenes/logo.png" class="d-block logotipo">
          </div>
      <div class="select-dropdown d-none d-md-inline col-md-5 col-lg-6">
          <select class="select-dropdown__language">
          <option value="en_US">English</option>
          <option selected="selected" value="es_ES">Español</option>
          <option value="fr_FR">Français</option>
          <option value="pt_BR">Portugués</option>
          <option value="de_DE">Deutsch</option></select>
          <a href="faq.php" class="p-2"> PREGUNTAS FRECUENTES </a>
          <a href="#" class="p-2"> SEGURIDAD </a>
          <a href="#ancla-contacto" class="p-2"> CONTACTO </a>
      </div>
      <div class="col-12 col-sm-6 col-md-3 col-lg-3">
          <div>
              <button class="toggle-nav d-md-none d-lg-none mt-2">
                                <span class="ion-navicon-round"></span>
                              </button>
              <nav class="main-nav d-md-none d-lg-none" style="display: none;">
                  <ul>
                      <li><a href="faq.php">preguntas frecuentes</a></li>
                      <li><a href="#">seguridad</a></li>
                      <li><a href="#ancla-contacto">contacto</a></li>
                      <li><a href="#">idioma</a></li>
                    </ul>
              </nav>
              <a href="registro.php" class="btn btn-outline-light mt-1">Registrarse</a>
              <a href="index.php" class="btn btn-outline-light mt-1">Volver al Inicio</a>

          </div>
      </div>

  </header>



<div class="contenido">
          <div class="contenedor-registracion container-fluid imgFondo d-flex flex-column justify-content-center align-items-center w-100">
              <div class="p-4">

                <form class="form-control p-5 margin-auto" method="post">
									<?php
									  	if (isset($_GET['primeraVez'])) {
									  		echo "<div class='alert alert-info text-center'>
    <strong><h3>¡Gracias por registrarte!</h3></strong>
		<h5>Iniciá sesión para acceder a tu perfil</h5></div>";
		}
									?>
                  <h2 class="mb-3 text-center">Bienvenido</h2>
                    <label class="input-group input-group-lg"> Ingresa tu e-mail o nombre de usuario</label>
                    <input type="email" name="e-mail" class="w-100 mb-3 mt-2"   value="<?=$email?>">
                    <?php if (isset($errores['email'])): ?>
											<div class="alert alert-danger"> <?=$errores['email'];?>
              									<b class="ion-android-alert"></b>
              								</div>
							      <?php endif; ?>

                    <label class="input-group input-group-lg ">Ingresa tu contraseña</label>
                    <input type="password" name="password" class="w-100 mb-3 mt-2"> <!-- placeholder="***********"  ESTO LO SAQUE ASI NO ME MUESTRA LOS ASTERISCOS POR AHORA      ESTABA ADENTRO DEL IMPUT -->
										<?php if (isset($errores['pass'])): ?>
											<div class="alert alert-danger"> <?=$errores['pass'];?>
																<b class="ion-android-alert"></b>
															</div>
										<?php endif; ?>

                  <button type="submit" class="btn btn-lg btn-primary btn-block btn-signin mt-3 mb-3">Log in </button>
                    <div class="align-items-center">
      <label>Recordarme</label>
      <input type="checkbox" name="recordar" value="Recordarme">
      <div class="opcioncontra"><a href="#!" class="text-dark"> ¿Olvidaste tu contraseña?</a></div>

      </form>
    </div>

  </div>
  </div>


    <footer class="bg-blue margin mt-3 text-white text-center">
      <a name="ancla-contacto"></a>
        <p class="pt-2">SEGUINOS</p>
        <a href="https://www.facebook.com/" target="_blank"><span class="ion-social-facebook-outline m-2"></span></a>
        <a href="https://twitter.com/?lang=es" target="_blank"><span class="ion-social-twitter-outline m-2"></span></a>
        <a href="https://www.instagram.com/" target="_blank"><span class="ion-social-instagram-outline m-2"></span></a>
        <a href="https://www.tumblr.com/" target="_blank"><span class="ion-social-tumblr-outline m-2"></span></a>
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        /* global $ */
        $('.toggle-nav').click(function() {
            $('.main-nav').slideToggle('fast');
        });
        window.onscroll = function() {
            myFunction()
        };
    </script>

</body>

</html>
