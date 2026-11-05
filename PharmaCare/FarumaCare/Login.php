    <!DOCTYPE html>
    <html lang="en">
    <head>
    <link rel="stylesheet" href="login.css">
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
<div class="container">
 <div class="image">
                    <img src="image/FarumaCare Pharmacy Logo.png" alt="Logo" class="logo" style="width:200px;
                     height:200px;   ">
                     <div class="collage">
                     <p class="P1">Your Health, Our Priority</p>
                     <img src="image/collage.png" alt="Healthcare Team" style="width:480px; height:390px;">
                    </div>

                </div>
        <div class="center">
               
                <form action="" method="POST">
                   
            <div class="input">
                 <h1 class="H1">Log into FarumaCare</h1><br>
                    <input type="text"name="username" required placeholder="Username">
                
                    <br><br>
                    <input type="password"name="password" required placeholder="Password">
                    <br><br>
                    <input class="Login" type="submit" value="Login">
                    <br>
                    <p><a href="" class="Forg">Forgot Password?</a></p><br>
                        <button class="Creat" >Create Account</button>
                    

                    </div>
                   
                </form>
            </div>
            </div>
    </body>
    </html>