<?php

   //? Соединение с БД

   function dbConnect(){

      static $pdo;

      if($pdo === null){
         $pdo = new PDO('mysql:host=localhost;dbname=blog', 'root', '');
         $pdo->exec('SET NAMES UTF8');
      }
   
      return $pdo; 

   }

   //? Проверка ошибок в БД

   function dbCheckError($stmt){

      $info = $stmt->errorInfo();

      if($info[0] != PDO::ERR_NONE){

         exit($info[2]);
      }
   }

   //? Запрос к БД

   function dbStatement($sql, $params = []){

      $pdo = dbConnect();
      $stmt = $pdo->prepare($sql);
      $stmt->execute($params);
      dbCheckError($stmt);

      return $stmt;
      
   }

   //? Поиск пользователя по эл.адресу

   function getUserByEmail($email) {

      $sql = 'SELECT * FROM users_diving WHERE email = :email';
      $pdo = dbConnect();
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
         ':email' => $email
      ]);

      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result;

   }

   //? Поиск пользователя по id

   function getUserById($id) {

      $sql = 'SELECT * FROM users_diving WHERE id_user = :id';
      $pdo = dbConnect();
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
         ':id' => $id
      ]);

      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      return $user;

   }

   //? Добавить пользователя в БД

   function addUser($email, $pwd) : int{

      $sql = 'INSERT INTO users_diving (email, password, role) VALUES (:email, :pwd, :role)';
      $pdo = dbConnect();
      $stmt = $pdo->prepare($sql);
      $pwd = password_hash($pwd, PASSWORD_DEFAULT);
      $user = $stmt->execute([
         ':email' => $email,
         ':pwd' => $pwd,
         ':role' => 'user'
      ]);

      $user = getUserByEmail($email);
      return $user['id_user'];

   }

   //? Подготовить флэш сообщение

   function setFlashMessage($name, $message){
      
      $_SESSION[$name] = $message;

   }

   //? Вывести флэш сообщение

   function displayFlashMessage($name){

      if(isset($_SESSION[$name])){

         echo "<div class=\"alert alert-{$name} text-{$name}\" role=\"alert\">{$_SESSION[$name]}</div>";
         unset($_SESSION[$name]);

      }

   }
   
   //? Перенаправить на другую страницу

   function redirectTo($path){

      header('Location:' . $path);
      exit();

   }

   //? Авторизовать пользователя

   function login($email, $pwd) : bool{

      $_SESSION['is_auth'] = false;
      $pdo = dbConnect();
      $sql = 'SELECT * FROM users_diving WHERE email = :email';
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
         ':email' => $email
      ]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if($user){
         if(password_verify($pwd, $user['password'])){
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['email'] = $email;
            $_SESSION['pwd'] = $pwd;
            $_SESSION['role'] = $user['role'];
            $_SESSION['is_auth'] = true;
            if(isset($_POST['remember'])){
               setcookie('login', hash('sha256', $email), time() + 3600 * 24 *7, '/');
               setcookie('pwd', hash('sha256', $pwd), time() + 3600 * 24 *7, '/');
            }
            return true;
         }else{
            setFlashMessage('danger', 'Ошибка! Неверный пароль!');
            return false;
         }
      }else{
         setFlashMessage('danger', 'Такого пользователя не существует!');
         return false;
      }

   }

   //? Не авторизован ли пользователь?
   
   function isNotLoggedIn() : bool{

      if(isset($_SESSION['is_auth']) && $_SESSION['is_auth']){
         return false;
      }else{
         return true;
      }

   }

   //? Внести недостающую информацию в БД (редактировать профиль)

   function editInfo($id, $name, $title, $tel, $adress){
     
      $sql = 'UPDATE users_diving SET name = :name, title = :title, tel = :tel, adress = :adress WHERE id_user = :id';
      $pdo = dbConnect();
      $stmt = $pdo->prepare($sql);
      $user = $stmt->execute([
         ':name' => $name,
         ':title' => $title,
         ':tel' => $tel,
         ':adress' => $adress,
         ':id' => $id
      ]);

      return $user;
      
   }

   //? Установить статус

   function setStatus($id, $status){

      $sql = 'UPDATE users_diving SET status = :status  WHERE id_user = :id';
      $pdo = dbConnect();
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
         ':id' => $id,
         ':status' => $status
      ]);

   }

   //? Загрузить аватар

   function uploadAvatar($id, $img){

      if(!empty($img)){

         $fileName = $_FILES['img']['name'];
         $ext = pathinfo($fileName, PATHINFO_EXTENSION);
         $fileName = uniqid('img_') . '.' . $ext;
         $saveTo = 'img/avatars/' . $fileName;

         // Если уже есть аватар, то удалить старый из папки на сервере.
         $user = getUserById($id);
         unlink('img/avatars/' . $user['avatar']);

         // Сохранить новый аватар в папку на сервере
         move_uploaded_file($img, $saveTo);

         // Обновить аватар в таблице БД
         $sql = 'UPDATE users_diving SET avatar = :avatar  WHERE id_user = :id';
         $pdo = dbConnect();
         $stmt = $pdo->prepare($sql);
         $stmt->execute([
            ':id' => $id,
            ':avatar' => $fileName
         ]);
         
      }

      

   }

   //? Добавить ссылки на соцсети

   function addSocialLinks($id, $vk, $tg, $ig){

      $sql = 'UPDATE users_diving SET vk = :vk, tg = :tg, ig = :ig WHERE id_user = :id';
      $pdo = dbConnect();
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
         ':id' => $id,
         ':vk' => $vk,
         ':tg' => $tg,
         ':ig' => $ig
      ]);

   }

   //? Имеет ли право доступа к записи

   function checkForRights($loggedUserId, $editUserId) : bool{

      if($_SESSION['role'] == 'admin' || $loggedUserId == $editUserId){
         return true;
      }else{
         return false;
      }

   }

   //? Редактировать пароль и емэйл

   function editCredentials($userId, $email, $pwd){

      $sql = 'UPDATE `users_diving` SET `email` = :email, `password` = :pwd WHERE `id_user` = :userId';
      $pdo = dbConnect();
      $stmt = $pdo->prepare($sql);
      $pwd = password_hash($pwd, PASSWORD_DEFAULT);
      $user = $stmt->execute([
         ':userId' => $userId,
         ':email' => $email,
         ':pwd' => $pwd
      ]);

      return $user;

   }

   //? Проверить есть ли аватар у пользоватиеля

   function hasImage($image){

      if(empty($image)){
         echo "<img src=\"img/demo/authors/josh.png\" class=\"img-responsive\" width=\"200\">";
      }else{ 
         echo "<img src=\"img/avatars/$image\" class=\"rounded-circle img-responsive\" style=\"height: 13rem; width: 13rem\">";
      }

   }

   function checkEmailAndUpdate($userId, $pwd, $email, $verifyEmail, $existEmail){

      if($email == $verifyEmail){
         editCredentials($userId, $email, $pwd);
         setFlashMessage('success', 'Данные успешно обновлены!');
         redirectTo('page_profile.php?id=' . $userId);
      }elseif($email == $existEmail){
         setFlashMessage('danger', 'Такой емэйл уже существует!');
         redirectTo('security.php?id=' . $userId);
      }else{
         editCredentials($userId, $email, $pwd);
         setFlashMessage('success', 'Данные успешно обновлены!');
         redirectTo('page_profile.php?id=' . $userId);
      }

   }

   //? Удалить пользователя

   function deleteUser($userId){

      $pdo = dbConnect();
      $sql = 'DELETE FROM users_diving WHERE id_user = :id';
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
         ':id' => $userId
      ]);

   }

   //? Выход из системы

   function Logout(){
      
      $_SESSION['is_auth'] = false;
      setcookie('login', '', 1, '/');
      setcookie('pwd', '', 1, '/');

   }

   //? Функции для картинок Images

   function uploadImage($tmpName, $fileName){

      $ext = pathinfo($fileName, PATHINFO_EXTENSION);
      $fileName = uniqid('rand_') . '.' . $ext;

      $saveTo = 'pictures/' . $fileName;

      move_uploaded_file($tmpName, $saveTo);

      $pdo = dbConnect();
      $sql = 'INSERT INTO images (image) VALUES (:fileName)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
         ':fileName' => $fileName
      ]);

      dbCheckError($stmt);

   }

   function selectAllImages(){

      $stmt = dbStatement('SELECT * FROM images ORDER BY id_img DESC');

      return $stmt->fetchAll(PDO::FETCH_ASSOC);

   }







?>