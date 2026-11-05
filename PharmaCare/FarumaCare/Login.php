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
   
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
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