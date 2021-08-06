<?php

   session_start();
   include_once('fnctns.php');

   $loggedUserId = $_SESSION['id_user'];
   $userId = $_GET['id'] ?? null;
   $user = getUserById($userId);

   if(isNotLoggedIn()){
      redirectTo('page_login.php');
   }

   if(checkForRights($loggedUserId, $userId)){

      unlink('img/avatars/' . $user['avatar']);
      deleteUser($userId);
      setFlashMessage('success', 'Пользователь успешно удален!');

      if($loggedUserId == $userId){
         logOut();
         redirectTo('page_register.php');
      }else{
         redirectTo('users.php');
      }

   }
   else{
      setFlashMessage('danger', 'Можно редактировать только свой профиль!');
      redirectTo('users.php');
   }







?>