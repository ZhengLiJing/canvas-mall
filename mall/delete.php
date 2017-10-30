<?php 

	include_once './lib/fun.php';
	if(!checkLogin()){
		msg(2,'请登录','login.php');
	}

   		$goodId = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : '';

	    //判断$goodId是否为空
	    if(!$goodId){
	        msg(2,'该画品不存在');
	    } 		

	    $mysqli = new mysqli('localhost','root','root','imooc_mall');

	    $sql = "SELECT * FROM `im_goods` WHERE `id`={$goodId}";

	    $result = $mysqli->query($sql);

	    if(!$result->fetch_assoc()){
	    	msg(2,'无该画品');
	    }


	    $sql = "DELETE FROM `im_goods` WHERE `id`={$goodId}";

	   	if($mysqli->query($sql)){
	   		msg(1,'删除成功','index.php');
	   	}else{
	   		msg(2,'删除失败','index.php');
	   	}
