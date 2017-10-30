<?php 
    //表单进行了提交处理
    if(!empty($_POST['username'])){

        include './lib/fun.php';
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $repassword = trim($_POST['repassword']);

        //判断用户名不能为空
        if(empty($username)){

            echo "用户名不能为空";exit();
        }
        if(empty($password)){

            echo "密码不能为空";exit();
        }
        if($password !== $repassword){

            echo "两次密码输入不一致,请重新输入";exit();
        }

        //数据库连接
        $mysqli = mysqlInit('localhost','root','root','imooc_mall');

        //判断用户是否在数据表存在
        $sql = "SELECT COUNT(`id`) AS total
         FROM `im_user` 
         WHERE `username`='{$username}'";

        $result= $mysqli->query($sql);        
        $row = $result->fetch_assoc();

        if(isset($row['total'])&&$row['total']>0){
            // echo "用户名已存在，请重新输入";exit();
            msg(2,'用户名已存在，请重新输入','register.php');
        }

        unset($obj,$row,$sql);

        $password = createPassword($password);

        //插入数据
        $query = "INSERT `im_user`(`username`,`password`,`create_time`) values('{$username}','{$password}','{$_SERVER['REQUEST_TIME']}')";

        $obj = $mysqli->query($query);

        if($obj){
            
            $userId = $mysqli->insert_id;//插入成功的主键id4
            msg(1,"恭喜你注册成功,用户名是:{$username},用户id:{$userId}",'login.php');
        }else{

            echo $mysqli->error;
            exit();
        }
    }
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>M-GALLARY|用户注册</title>
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
                        <p>用户注册</p>
                    </div>
                    <form class="login-table" name="register" id="register-form" action="register.php" method="post">
                        <div class="login-left">
                            <label class="username">用户名</label>
                            <input type="text" class="yhmiput" name="username" placeholder="Username" id="username">
                        </div>
                        <div class="login-right">
                            <label class="passwd">密码</label>
                            <input type="password" class="yhmiput" name="password" placeholder="Password" id="password">
                        </div>
                        <div class="login-right">
                            <label class="passwd">确认</label>
                            <input type="password" class="yhmiput" name="repassword" placeholder="Repassword"
                                   id="repassword">
                        </div>
                        <div class="login-btn">
                            <button type="submit">注册</button>
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
        $('#register-form').submit(function () {
            var username = $('#username').val(),
                password = $('#password').val(),
                repassword = $('#repassword').val();
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

            if(repassword == ''||repassword.length <= 0){
                layer.tips('请输入确认密码','#repassword',{time:2000,tip:2});
                return false;
            }

            if ((password != repassword)) {
                layer.tips('两次密码输入不一致', '#repassword', {time: 2000, tips: 2});
                $('#repassword').focus();
                return false;
            }

            return true;
        })

    })
</script>
</html>