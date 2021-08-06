<?php

   session_start();
   include_once('fnctns.php');

   $email = trim($_POST['email']);
   $pwd  = trim($_POST['pwd']);
   $userId = $_GET['id'] ?? null;

   $user = getUserById($userId);
   $userEmail = getUserByEmail($email);

   $verifyEmail = $user['email'];
   $existEmail = $userEmail['email'];

   if(empty($pwd)){
      setFlashMessage('danger', 'Пароль должен быть заполнен!');
      redirectTo('security.php?id=' . $userId);
   }

   checkEmailAndUpdate($userId, $pwd, $email, $verifyEmail, $existEmail);
   

   


 
?>