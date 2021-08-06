<?php

   session_start();
   include_once('fnctns.php');

   if($_SERVER['REQUEST_METHOD'] == 'POST'){

      $email = trim($_POST['email']);
      $pwd   = trim($_POST['pwd']);

      $user = login($email, $pwd);

      if($user){
         redirectTo('users.php');
      }else{
         redirectTo('page_login.php');
      }

   }else{

      $email = '';
      $pwd   = '';
      
   }

   

?>