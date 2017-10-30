<?php 

	include_once './lib/fun.php';
	include './lib/upload.class.php';
    //登录检测
    if(!checkLogin()){
        msg(2,'请登录','login.php');
    }

 	if(!empty($_POST['name'])){
 		$mysqli = new mysqli('localhost','root','root','imooc_mall');

		//通过隐藏域提交的表单商品id

   		 $goodId = isset($_POST['id']) && is_numeric($_POST['id']) ? intval($_POST['id']) : '';

	    //判断$goodId是否为空
	    if(!$goodId){
	        msg(2,'该画品不存在');
	    } 		

 		$name = $mysqli->real_escape_string(trim($_POST['name']));

 		$price = $mysqli->real_escape_string(trim($_POST['price']));

 		$des = $mysqli->real_escape_string(trim($_POST['des']));


		$content = $mysqli->real_escape_string(trim($_POST['content']));


		$nameLength = mb_strlen($name,'utf-8');
		if($nameLength <= 0 || $nameLength > 30){
			msg(2,'画品名应在1-30字符之内');
		}

		if($price <=0 || $price > 99999999){
			msg(2,'画品价格应小于99999999');
		}

		$desLength = mb_strlen($des,'UTF-8');
		if($desLength <= 0 || $desLength > 100){
			msg(2,'画品描述应在1-100字符之内');
		}

		if(empty($content)){
			msg(2,'画品详情不能为空');
		}

		$update = array(

				'name'=>$name,//这个$name是有转义字符\的。
				'price'=>$price,
				'des'=>$des,
				'content'=>$content
			);		

		//当有用户更改了照片时，才处理文件上传

		if($_FILES['file']['error']==0){
			$obj = new upload('static/file/uploads','file');
			$pic = $obj->uploadFile();	
			$update['pic']=$pic;
		}


		$sql = "SELECT * FROM `im_goods` WHERE `id`={$goodId}";

		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();


		$updateSql = '';

		// foreach($update as $k=>$v){
		// 	// $row[$k]从数据库取出的值是没有转义字符的，$v是用户编辑过的，可能有转义字符的。
		// 	// var_dump($row[$k]);die;
		// 	$v = stripslashes($v);//去掉转义字符
		// 	if($row[$k] == $v){
		// 		unset($update[$k]);
		// 	}
		// }
		// var_dump($update);die;

		foreach($update as $k=>$v){

			$v = stripslashes($v);//去掉转义字符
			//更新那些值不一样的值，一样的值不做更新处理
			if($row[$k] != $v){
				//最后多了一个,后面增加了一个updata_time字段，所以不用去掉这个,
				$updateSql .= "`$k`='{$v}',";
			}
		}

		if(empty($updateSql)){
			msg(1, '操作成功33','index.php');
		}

		$now = $_SERVER['REQUEST_TIME'];

		$updateSql .= "`update_time`={$now}";

		$updateSql = "UPDATE `im_goods` SET {$updateSql} WHERE `id`={$goodId}";

		//数据库更新用户编辑更改的地方。
		if($mysqli->query($updateSql)){
			msg(1,'操作成功1','index.php');
		}else{
			msg(1,'操作成功2','index.php');
		}

 	}   