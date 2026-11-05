
    <?php
    session_start();
    require_once 'config/db.php';

    //If session is arleady in, skip login page
    if(isset($_SESSION['user_id'])){
        header('Location: dashboard.php');
        exit;
    }
    $errors= [];
    $username = '';

    if($_SERVER['REQUEST_METHOD']=== 'POST'){
        $username = trim($_POST['username']?? "");
        $password = $_POST['password']?? "";


        //validation

        if($username==''){
            $errors[]='Username is required.';
        }
        if($password == ''){
            $errors[]='password is required';
        }


        if(empty($errors)){
            try{
                $stmt = $pdo->prepare("SELECT id,username,password,role FROM users WHERE username = :username");
                $stmt->bindValue(':username',$username);
                $stmt->execute();
                $user = $stmt->fetch();

                if($user && password_verify($password,$user['password'])){
                    session_regenerate_id(true);
                    $_SESSION['user_id']  = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role']     = $user['role'];

                    header('Location: dashboard.php');
                    exit;


                }
                else{
                    $errors[]="Invalid username or password.";
                } 

            }
            Catch (PDOException $e){
                    error_log($e->getMessage());
                    $errors[]='Something went wrong. please try again later.';
                }
        }
    }



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FarumaCare Login</title>
</head>
<body>



    <?php if (!empty($errors)): ?>
        <div style="color:red;">
            <?php foreach ($errors as $errors): ?>
                <p><?= htmlspecialchars($errors) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="Username">Username</label><br>
        <input type="text" name="username" id=""><br>
        <label for="password">Password</label><br>
        <input type="password" name="password" id="">
        <button type="submit" name="submit">Login</button>

    </form>
    
</body>
</html>