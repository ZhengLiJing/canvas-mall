<?php 
error_reporting(-1);
	include './lib/fun.php';
	include './lib/upload.class.php';
	//检测用户是否登录
	if(!checkLogin()){
		msg(2,'请重新登录','login.php');
	}
	$user = $_SESSION['user'];

	if(!empty($_POST['name'])){

 		$mysqli = new mysqli('localhost','root','root','imooc_mall');

 		$name = $mysqli->real_escape_string(trim($_POST['name']));

 		$price = $mysqli->real_escape_string(trim($_POST['price']));



		$obj = new upload('static/file/uploads','file');
		$pic = $obj->uploadFile();

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
		
		$userId = $user['id'];
		$now = $_SERVER['REQUEST_TIME'];


		//商品名称唯一性验证
		$sql = "SELECT COUNT(`name`) AS name
		 FROM `im_goods` WHERE `name`='{$name}'";
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();

		if(!empty($row['name']) && $row['name']>0){
			msg(2,'该画品名已存在，请重新输入','publish.php');
		}

		unset($result,$row,$sql);


		$sql = "INSERT `im_goods`(`name`,`price`,`des`,`content`,`pic`,`user_id`,`create_time`,`update_time`,`view`)
		VALUES('{$name}','{$price}','{$des}','{$content}','{$pic}','{$userId}','{$now}','{$now}',0)";


		if($obj = $mysqli->query($sql)){

			msg(1,'操作成功1111','index.php');
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
    <title>M-GALLARY|发布画品</title>
    <link type="text/css" rel="stylesheet" href="./static/css/common.css">
    <link type="text/css" rel="stylesheet" href="./static/css/add.css">
</head>
<body>
<div class="header">
    <div class="logo f1">
        <img src="./static/image/logo.png">
    </div>
    <div class="auth fr">
        <ul>
            <li><span>管理员：<?php echo $user['username'] ?></span></li>
            <li><a href="logout.php">退出</a></li>
        </ul>
    </div>
</div>
<div class="content">
    <div class="addwrap">
        <div class="addl fl">
            <header>发布画品</header>
            <form name="publish-form" id="publish-form" action="publish.php" method="post"
                  enctype="multipart/form-data">
                <div class="additem">
                    <label id="for-name">画品名称</label><input type="text" name="name" id="name" placeholder="请输入画品名称">
                </div>
                <div class="additem">
                    <label id="for-price">价值</label><input type="text" name="price" id="price" placeholder="请输入画品价值">
                </div>
                <div class="additem">
                    <!-- 使用accept html5属性 声明仅接受png gif jpeg格式的文件                -->
                    <label id="for-file">画品</label><input type="file" accept="image/png,image/gif,image/jpeg" id="file"
                                                          name="file">
                </div>
                <div class="additem textwrap">
                    <label class="ptop" id="for-des">画品简介</label><textarea id="des" name="des"
                                                                           placeholder="请输入画品简介"></textarea>
                </div>
                <div class="additem textwrap">
                    <label class="ptop" id="for-content">画品详情</label>
                    <div style="margin-left: 120px" id="container">
                        <textarea id="content" name="content"></textarea>
                    </div>

                </div>
                <div style="margin-top: 20px">
                    <button type="submit">发布</button>
                </div>

            </form>
        </div>
        <div class="addr fr">
            <img src="./static/image/index_banner.png">
        </div>
    </div>

</div>
<div class="footer">
    <p><span>M-GALLARY</span>©2017 POWERED BY IMOOC.INC</p>
</div>
</body>
<script src="./static/js/jquery-1.10.2.min.js"></script>
<script src="./static/js/layer/layer.js"></script>
<script src="./static/js/kindeditor/kindeditor-all-min.js"></script>
<script src="./static/js/kindeditor/lang/zh_CN.js"></script>
<script>
    var K = KindEditor;
    K.create('#content', {
        width      : '475px',
        height     : '400px',
        minWidth   : '30px',
        minHeight  : '50px',
        items      : [
            'undo', 'redo', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'clearhtml',
            'fontsize', 'forecolor', 'bold',
            'italic', 'underline', 'link', 'unlink', '|'
            , 'fullscreen'
        ],
        afterCreate: function () {
            this.sync();
        },
        afterChange: function () {
            //编辑器失去焦点时直接同步，可以取到值
            this.sync();
        }
    });
</script>

<script>
    $(function () {
        $('#publish-form').submit(function () {
            var name = $('#name').val(),
                price = $('#price').val(),
                file = $('#file').val(),
                des = $('#des').val(),
                content = $('#content').val();
            if (name.length <= 0 || name.length > 30) {
                layer.tips('画品名应在1-30字符之内', '#name', {time: 2000, tips: 2});
                $('#name').focus();
                return false;
            }
            //验证为正整数
            if (!/^[1-9]\d{0,8}$/.test(price)) {
                layer.tips('请输入最多9位正整数', '#price', {time: 2000, tips: 2});
                $('#price').focus();
                return false;
            }

            if (file == '' || file.length <= 0) {
                layer.tips('请选择图片', '#file', {time: 2000, tips: 2});
                $('#file').focus();
                return false;

            }

            if (des.length <= 0 || des.length >= 100) {
                layer.tips('画品简介应在1-100字符之内', '#content', {time: 2000, tips: 2});
                $('#des').focus();
                return false;
            }

            if (content.length <= 0) {
                layer.tips('请输入画品详情信息', '#container', {time: 2000, tips: 3});
                $('#content').focus();
                return false;
            }
            return true;

        })
    })
</script>
</html>