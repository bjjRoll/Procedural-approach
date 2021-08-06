<?php

   session_start();
   include_once('fnctns.php');

   $id = $_GET['id_user'];
   $name = trim($_POST['name']);
   $title = trim($_POST['title']);
   $tel = trim($_POST['tel']);
   $adress = trim($_POST['adress']);

   editInfo($id, $name, $title, $tel, $adress);
   setFlashMessage('success', 'Профиль успешно обновлен!');
   redirectTo('page_profile.php?id=' . $id);
   
?>