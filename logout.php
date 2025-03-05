<!-- 
 Autor: Flavio Henrique Guedes Nobre;
 Version: 1.0.1
 "Você é livre para usar e modificar com sabedoria esse código ele é aberto, só não deixe de 
 dar os créditos ao autor"
-->
 
<?php
session_start();
session_destroy();
header("Location: index.php");
exit;
?>