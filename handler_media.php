<?php

   session_start();
   include_once('fnctns.php');

   $id = $_GET['id'] ?? null;
   $img = $_FILES['img']['tmp_name'];
   
   uploadAvatar($id, $img);
   setFlashMessage('success', 'Аватар успешно изменен!');
   redirectTo('page_profile.php?id=' . $id);

?>