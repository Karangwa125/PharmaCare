<!DOCTYPE html>
<html lang="en">
<head>
   <style>
    fieldset{
        width:400px;
        margin-top: 100px;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            background-color: #fff;
            height: 500px;

    }
      input[type="text"], input[type="password"]{
            width: 90%;
            height: 40px;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
        }
           input[type="submit"]{
            width: 70%;
            
            padding: 10px;
            border-radius: 10px;
            border: none;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
         input[type="submit"]:hover{
            background-color: #45a049;
        }
        body{
            background-color: #f2f2f2;
        }
        input[placeholder="Username"], input[placeholder="Password"]{
            background-repeat: no-repeat;
            background-position: 10px center;
            padding-left: 50px;
        }
       
   </style>

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

   
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login </title>
</head>
<body>
     <?php if (!empty($errors)): ?>
        <div style="color:red;">
            <?php foreach ($errors as $errors): ?>
                <p><?= htmlspecialchars($errors) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>


    <center>
        <fieldset>
            <form action="" method="POST">
                <center><b><h1>LOGIN PAGE</h1></b></center>
        
                <input type="text"name="username" required placeholder="Username">
            
                <br><br>
                <input type="password"name="password" required placeholder="Password">
                <br><br>
                <input type="submit" value="Login">
                <br><br>
                <p><a href="">Forgot Password?</a></p><br>
                <p>Don't have an account?<a href="create.php">Create Account</a></p>
            </form>
        </fieldset>
    </center>
</body>
</html>