<?php

   session_start();
   include_once('fnctns.php');

   $name = trim($_POST['name']);
   $title = trim($_POST['title']);
   $tel = trim($_POST['tel']);
   $adress = trim($_POST['adress']);
   $email = trim($_POST['email']);
   $pwd = trim($_POST['pwd']);
   $status = $_POST['status'];
   $img = $_FILES['img']['tmp_name'] ?? null;
   $vk = trim($_POST['vk']);
   $tg = trim($_POST['tg']);
   $ig = trim($_POST['ig']);

   if(getUserByEmail($email)){

      setFlashMessage('danger', 'Такой e-mail уже существует');
      redirectTo('create_user.php');

   }else{

      $id = addUser($email, $pwd);
      editInfo($id, $name, $title, $tel, $adress);
      setStatus($id, $status);
      uploadAvatar($id, $img);
      addSocialLinks($id, $vk, $tg, $ig);
      setFlashMessage('success', 'Пользователь успешно добавлен!');
      redirectTo('users.php');

   }



?>