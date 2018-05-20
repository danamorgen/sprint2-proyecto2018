<?php
require_once('DB.php');
 class dbMySql extends DB{

 private $pdo;  // ver si private o protected

 public function __construct(){
    $this->pdo = new PDO('mysql:host=localhost',"root", '');
 }

 public function my_db(){
   $crear_db = $this->pdo->prepare('CREATE DATABASE IF NOT EXISTS baseDePrueba');
   $crear_db->execute();

   if($crear_db){
   $use_db = $this->pdo->prepare('USE usuarios_db');
   $use_db->execute();
  }
  echo "Base de datos creada exitosamente";
  //header('script.php');
  header('location: script.php?cdb=ok');
  }

}

$dbMYSQL = new dbMySql();
$dbMYSQL->my_db();

?>
