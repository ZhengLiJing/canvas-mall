<?php 
	//开启session
	session_start();
	// var_dump($_SESSION['username']);die;
	if(isset($_SESSION['user'])&&!empty($_SESSION['user'])){
	    header("Location:index.php");
	    exit();		
	}
//表单进行了提交处理
	if(!empty($_POST['username'])){

		include './lib/fun.php';
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
		// $username = trim(mysqli_real_escape_string($_POST['username']));
		// $password = trim(mysqli_real_escape_string($_POST['password']));

     //判断用户名不能为空
        if(empty($username)){

        	msg(2,'用户名不能为空');
        }
        if(empty($password)){

            msg(2,'密码不能为空');
        }

        //数据库连接
        $mysqli = mysqlInit('localhost','root','root','imooc_mall');

        $query = "SELECT * FROM `im_user` WHERE `username`='{$username}' LIMIT 1";
        $obj = $mysqli->query($query);
        $result = $obj->fetch_assoc();

        if(is_array($result)&&!empty($result)){

	        $password = createPassword($password);

	        if($password === $result['password']){

	        	// $_SESSION['username'] = $result['username'];
                $_SESSION['user'] = $result;
	        	header("Location:msg.php?type=1&msg=登录成功&url=index.php");
	        	exit();
	        }else{

	        	msg(2,'密码不正确，请重新输入');
	        }
        }else{

        	msg(2,'该用户不存在，请注册','register.php');       	
        }
	}



 ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>M-GALLARY|用户登录</title>
    <link type="text/css" rel="stylesheet" href="./static/css/common.css">
    <link type="text/css" rel="stylesheet" href="./static/css/add.css">
    <link rel="stylesheet" type="text/css" href="./static/css/login.css">	
</head>
<body>
	<div class="header">
    <div class="logo f1">
        <img src="./static/image/logo.png">
    </div>
    <div class="auth fr">
        <ul>
            <li><a href="login.php">登录</a></li>
            <li><a href="register.php">注册</a></li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="center">
        <div class="center-login">
            <div class="login-banner">
                <a href="#"><img src="./static/image/login_banner.png" alt=""></a>
            </div>
            <div class="user-login">
                <div class="user-box">
                    <div class="user-title">
                        <p>用户登录</p>
                    </div>
                    <form class="login-table" name="login" id="login-form" action="login.php" method="post">
                        <div class="login-left">
                            <label class="username">用户名</label>
                            <input type="text" class="yhmiput" name="username" placeholder="Username" id="username">
                        </div>
                        <div class="login-right">
                            <label class="passwd">密码</label>
                            <input type="password" class="yhmiput" name="password" placeholder="Password" id="password">
                        </div>
                        <div class="login-btn">
                            <button type="submit">登录</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="footer">
    <p><span>M-GALLARY</span> ©2017 POWERED BY IMOOC.INC</p>
</div>

</body>
<script src="./static/js/jquery-1.10.2.min.js"></script>
<script src="./static/js/layer/layer.js"></script>
<script>
    $(function () {
        $('#login-form').submit(function () {
            var username = $('#username').val(),
                password = $('#password').val();
            if (username == '' || username.length <= 0) {
                layer.tips('用户名不能为空', '#username', {time: 2000, tips: 2});
                $('#username').focus();
                return false;
            }

            if (password == '' || password.length <= 0) {
                layer.tips('密码不能为空', '#password', {time: 2000, tips: 2});
                $('#password').focus();
                return false;
            }


            return true;
        })

    })
</script>
</html>