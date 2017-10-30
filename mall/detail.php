<?php
	include_once './lib/fun.php';

	if($login = checkLogin()){
		$user = $_SESSION['user'];
	}

	    	//数据库连接
	$mysqli = new mysqli('localhost','root','root','imooc_mall');

	//goodsNums为id最大值
	$sql = "SELECT MAX(`id`) as goodsNums ,`name`
	FROM im_goods";
	$result = $mysqli->query($sql);
	$goodsNumsArr = $result->fetch_assoc();
	// var_dump($goodsNumsArr);die;
	$goodsNums = $goodsNumsArr['goodsNums'];

	unset($sql,$results);

	   //校验 url中商品id
    // $goodId = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : '';

	$goodId = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 1;

	//对用户传递过来的page参数进行容错
	$goodId = max($goodId,1);

	$mysqli = new mysqli('localhost','root','root','imooc_mall');
	//goodsNums为id最大值
	$sql = "SELECT MAX(`id`) as goodsNums
	FROM im_goods";
	$result = $mysqli->query($sql);
	//查询结果包含最大的id号
	$goodsNumsArr = $result->fetch_assoc();
	//最大的id号
	$goodsNums = $goodsNumsArr['goodsNums'];

	unset($sql,$result);

	$sql = "SELECT `name` AS name 
	FROM `im_goods` WHERE `id`={$goodId}";
	$result = $mysqli->query($sql);
	//查询的结果数组包含name
	$NameArr = $result->fetch_assoc();
	//查询到传递的id是否存在name,从而判断该画品是否存在
	$name = $NameArr['name'];

    //判断$goodId是否为空
    if(!$goodId){
        msg(2,'该画品不存在');
    }

    if($goodId>$goodsNums){
    	msg(2,'该画品不存在');
    }

    if(!isset($name)){
    	msg(2,'该画品不存在');
    }
    unset($sql,$result);




    	//数据库连接
	$mysqli = new mysqli('localhost','root','root','imooc_mall');

	// $sql = "SELECT * FROM `im_goods` LIMIT {$offset},{$pageSize}";
	$sql = "SELECT `id`,`name`,`price`,`pic`,`des`,`content`,`create_time`,`update_time`,`view`,`user_id` FROM `im_goods` WHERE id={$goodId}";
	$result = $mysqli->query($sql);
	$goods = array();
	while ($row = $result->fetch_assoc()) {
		$goods = $row;
	}
	unset($sql,$result);
	//连表查询发布人，因为im_goods里有user_id,可以根据这个在im_user表里查询发布人
	$sql = "SELECT `username` FROM im_user WHERE `id`={$goods['user_id']}";
	$result = $mysqli->query($sql);
	//发布人数组，里面包含发布人姓名
	$user = $result->fetch_assoc();
	unset($sql,$result);

	//浏览次数的更新
	$sql = "UPDATE `im_goods` SET `view`=`view`+1 WHERE `id`={$goodId}";
	$result = $mysqli->query($sql);


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>M-GALLARY|<?php echo $goods['name']?></title>
    <link rel="stylesheet" type="text/css" href="./static/css/common.css" />
    <link rel="stylesheet" type="text/css" href="./static/css/detail.css" />
</head>
<body class="bgf8">
<div class="header">
    <div class="logo f1">
        <img src="./static/image/logo.png">
    </div>
    <div class="auth fr">
        <ul>
        <?php if(isset($user)): ?>
        	<li><span> 用户名:  <?php echo $user['username'] ?></span></li>
        	<li><a href="logout.php">退出</a></li>		
        <?php else: ?>
            <li><a href="login.php">登录</a></li>
            <li><a href="register.php">注册</a></li>
        <?php endif; ?>
        </ul>
    </div>
</div>
<div class="content">
    <div class="section" style="margin-top:20px;">
        <div class="width1200">
            <div class="fl"><img src="<?php echo $goods['pic']?>" width="720px" height="432px"/></div>
            <div class="fl sec_intru_bg">
                <dl>
                    <dt><?php echo $goods['name']?></dt>
                    <dd>
                        <p>发布人：<span><?php echo $user['username']?></span></p>
                        <p>发布时间：<span><?php echo date('Y年m月d日',$goods['create_time'])?></span></p>
                        <p>修改时间：<span><?php echo date('Y年m月d日',$goods['update_time'])?></span></p>
                        <p>浏览次数：<span><?php echo $goods['view']?></span></p>
                    </dd>
                </dl>
                <ul>
                    <li>售价：<br/><span class="price"><?php echo $goods['price'] ?></span>元</li>
                    <li class="btn"><a href="javascript:;" class="btn btn-bg-red" style="margin-left:12px;width:120px;">立即购买</a></li>
                    <li class="btn"><a href="javascript:;" class="btn btn-sm-white" style="margin-left:8px;">收藏</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="secion_words">
        <div class="width1200">
            <div class="secion_wordsCon">
                <?php echo $goods['content']?>
            </div>
        </div>
    </div>
</div>
<div class="footer">
    <p><span>M-GALLARY</span>©2017 POWERED BY IMOOC.INC</p>
</div>
</div>
</body>
</html>