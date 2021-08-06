<?php

   session_start();
   include_once('fnctns.php');

   $status = $_POST['status'];
   $id = $_GET['id'];

   setStatus($id, $status);
   setFlashMessage('success', 'Статус пользователя успешно изменен!');
   redirectTo('page_profile.php?id=' . $id);



?>