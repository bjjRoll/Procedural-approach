<?php

session_start();
include_once('fnctns.php');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
   
   $email = trim($_POST['email']);
   $pwd   = trim($_POST['pwd']);

   $result = getUserByEmail($email);

   if(!empty($result)){
      
      setFlashMessage('danger', 'Этот эл.адрес уже занят другим пользователем');
      redirectTo('page_register.php');

   }
   else{
   
      setFlashMessage('success', 'Регистрация успешна');
      addUser($email, $pwd);
      redirectTo('page_login.php');
   
   }
}
else{

   $email = '';
   $pwd = '';
   
}



