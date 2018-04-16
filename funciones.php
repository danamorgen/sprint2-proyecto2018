<?php
session_start();

if (isset($_COOKIE['id'])) {
		$_SESSION['id'] = $_COOKIE['id'];
	}

function crearUsuario($data, $imagen){
    $usuario = [
        'id' => traerUltimoId(),
        'name' => $data['name'],
        'email' => $data['email'],
        'username' => $data['username'],
        'pass' => password_hash($data['pass'], PASSWORD_DEFAULT),
        'pais' => $data['pais'],
        'imagen' => 'imagenUsuarios/' . $data['email'] . '.' . pathinfo($imagen['name'], PATHINFO_EXTENSION)

    ];

    return $usuario;
}

function guardarImagen($imagen){   // aca modifique $imagen antes estaba $_FILES[$imagen]
    // $errores = [];
   if ($imagen['error'] == UPLOAD_ERR_OK) {

     $nombreArchivo = $imagen['name'];

     $ext = pathinfo($nombreArchivo, PATHINFO_EXTENSION);

     $archivoFisico = $imagen['tmp_name'];
     // Pregunto si la extensión es la deseada
    // if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png') {   VAMOS A ELIMINAR ESTO PORQUE EN TEORIA YA LO COMPRUEBA EN LA FUNCION VALIDAR
       // Armo la ruta donde queda gurdada la imagen
       $direccionReal = dirname(__FILE__);
       $rutaFinalConNombre = $direccionReal . '/imagenUsuarios/' . $_POST['email'] . '.' . $ext;
       // Subo la imagen definitivamente
       move_uploaded_file($archivoFisico, $rutaFinalConNombre);
                  /* } else {
                       $errores['imagen'] = 'El formato tiene que ser JPG, JPEG, PNG o GIF';
                     }
                   } else {
                     // Genero error si no se puede subir
                     $errores['imagen'] = 'No subiste nada';
                   }
                    return $errores;*/
                    // VAMOS A PROBAR HACIENDO QUE guardarImagen no devuelva nada simplemente se dedique a guardar la imagen

 }
}

function guardarUsuario($data, $imagen){
   $usuario = crearUsuario($data, $imagen);
   		$usuarioJSON = json_encode($usuario);

   		file_put_contents('usuarios.json', $usuarioJSON . PHP_EOL, FILE_APPEND);
   		// Devuelvo al usuario para poder auto loguearlo después del registro
   		return $usuario;
}

function traerTodos(){ /*trae todos los usuarios que estan en el json*/
  // Traigo la data de todos los usuarios de 'usuarios.json'
  		$todosJson = file_get_contents('usuarios.json');
  		// Esto me arma un array con todos los usuarios
  		$usuariosArray = explode(PHP_EOL, $todosJson);    // creo que usuariosArray tiene solo 2 posiciones la ultima es vacia
  		// Saco el último elemento que es una línea vacia
  		array_pop($usuariosArray);
  		// Creo un array vacio, para guardar los usuarios
  		$todosPHP = [];
  		// Recorremos el array y generamos por cada usuario un array del usuario
  		foreach ($usuariosArray as $usuario) {
  			$todosPHP[] = json_decode($usuario, true);   // PREGUNTAR PORQUE HACEMOS ESTO EN UN foreach !!!!!!!!!!!!!!!!!!!!!!!!!!!
  		}
  		return $todosPHP;

}

function traerUltimoId(){
  $arrayDeUsuarios=traerTodos();

  if(empty($arrayDeUsuarios)){
      return 1;
  }

  		$elUltimo = array_pop($arrayDeUsuarios);
  		$id = $elUltimo['id'];
  		return $id + 1;
}

function existeMail($email){

  		$todos = traerTodos();

  		foreach ($todos as $unUsuario) {
  			if ($unUsuario['email'] == $email) {
  				return $unUsuario;
  			}
  		}
  		return false;

}

function existeUsuario($username){

  		$todos = traerTodos();

  		foreach ($todos as $unUsuario) {
  			if ($unUsuario['username'] == $username) {
  				return $unUsuario;
  			}
  		}
  		return false;

}

function validar($data, $imagen){
  global $errores;     // ME CONVIENE ESTO O SIMPLEMENTE DECLARAR UNA VARIABLE LOCAL??? PREG
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $pais = trim($_POST['pais']);
  $pass = trim($_POST['pass']);
  $rpass = trim($_POST['rpass']);
  $username = trim($_POST['username']);


  if ($name == '') {
      $errores['name'] = "Completa tu nombre";
  }


  if ($username == '') {
      $errores['username'] = "Completa tu nombre de usuario";
  } elseif (existeUsuario($username)) {
		$errores['username'] = "El usuario ya existe";
	}

  if ($pais == '') {
      $errores['pais'] = "Completa tu país de origen";
  }
  if ($email == '') {
      $errores['email'] = "Completa tu email";
  }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errores['email'] = "El email indicado es incorrecto";
  } elseif (existeMail($email)) {
    $errores['email'] = "Este mail ya se encuentra registrado";
  }


	//agrega validacion de mas de 7 caracteres pass
	 if (strlen($pass)<7){
	   $errores['pass']="El password debe tener minimo 7 caracteres";
	 }
	if ($pass == '' || $rpass == '') {
      $errores['pass'] = "Por favor completa tu password";
  } elseif ($pass != $rpass) {
      $errores['pass'] = "Las contraseñas no coinciden";
  } // elseif (!password_verify($pass, $usuario["pass"])) {
    // $errores['pass'] = "Error de credenciales";   <-- NO FUNCA!!!

  if($imagen["error"] !== UPLOAD_ERR_OK){
          $errores["imagen"] = 'Por favor subí tu foto de perfil';
  } else {
           $ext = strtolower(pathinfo($imagen["name"], PATHINFO_EXTENSION)); // VAMOS A PASAR A MINUSCULA

           if ($ext !== "jpg" && $ext !== "jpeg" && $ext !== "png")  {
             $errores["imagen"]= "Formato no reconocido, subi solamente jpg, jpeg, JPG o png: La extension subida es: $ext";
           }
            else {
                guardarImagen($imagen);
                }
   }
   return $errores;
 }

function validarLogin($data){  // ACA HAY ALGO QUE TENGO QUE PREGUNTAR
  $arrayADevolver = [];
		$email = trim($data['e-mail']);
		$pass = trim($data['password']);
		if ($email == '') {
			$arrayADevolver['email'] = 'Completá tu email';
		} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$arrayADevolver['email'] = 'Poné un formato de email válido';
		} elseif (!$usuario = existeMail($email)) {   //                             <--- MIRAR ACA ME GENERA DUDA
			$arrayADevolver['email'] = 'Este email no está registrado';
		} else {
			// Si el mail existe, me guardo al usuario dueño del mismo
			// $usuario = existeEmail($email);

 			// Pregunto si coindice la password escrita con la guardada en el JSON
      	if (!password_verify($pass, $usuario["pass"])) {
         	$arrayADevolver['pass'] = "Credenciales incorrectas";
      	}
		}
		return $arrayADevolver;
}

function loguear($usuario) {
		// Guardo en $_SESSION el ID del USUARIO
	   $_SESSION['id'] = $usuario['id'];
		header('location: perfil.php');
		exit;
	}


  function estaLogueado() {
		return isset($_SESSION['id']);
	}

  function traerPorId($id){
		// me traigo todos los usuarios
		$todos = traerTodos();
		// Recorro el array de todos los usuarios
		foreach ($todos as $usuario) {
			if ($id == $usuario['id']) {
				return $usuario;
			}
		}
		return false;
	}
 ?>
