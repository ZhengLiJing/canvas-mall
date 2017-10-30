<?php  
	include_once './lib/fun.php';


	if($login = checkLogin()){
		$user = $_SESSION['user'];
	}

	//数据库连接
	$mysqli = new mysqli('localhost','root','root','imooc_mall');

	$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;

	//对用户传  递过来的page参数进行容错
	$page = max($page,1);
	$pageSize = 3;

	$offset = ($page-1)*$pageSize;

	// $sql = "SELECT * FROM `im_goods` LIMIT {$offset},{$pageSize}";
	$sql = "SELECT `id`,`name`,`pic`,`des` FROM `im_goods` ORDER BY `id` asc,`view` desc limit {$offset},{$pageSize} ";
	$result = $mysqli->query($sql);
	$goods = array();
	while ($row = $result->fetch_assoc()) {
		$goods[] = $row;
	}

	unset($sql,$result,$row);

	$sql = "SELECT COUNT(*) AS total
	 FROM `im_goods`";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();

	$total = isset($row['total']) ? $row['total'] : 0;
	unset($sql,$result,$row);


	//  * $total 总数据
    // * $pageSize 每页显示的条数
    // * $curPage 当前页码
    // * $showPages 要显示页码，如要显示 4 5 6 7 8 9共6个页码显示
	$pages = showPages($total,$pageSize,$page,6);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>M-GALLARY|首页</title>
    <link rel="stylesheet" type="text/css" href="./static/css/common.css"/>
    <link rel="stylesheet" type="text/css" href="./static/css/index.css"/>
</head>
<body>
<div class="header">
    <div class="logo f1">
        <a href="index.php">
           <img src="./static/image/logo.png">           
        </a>
    </div>
    <div class="auth fr">
        <ul>

			<?php if($login): ?>
				<li><span>管理员：<?php echo $user['username'] ?></span></li>
		        <li><a href="publish.php">发布</a></li>
		        <li><a href="logout.php">退出</a></li>
			<?php else: ?>
				<li><a href="login.php">登录</a></li>
				<li><a href="register.php">注册</a></li>
			<?php endif; ?>
        </ul>
    </div>
</div>
<div class="content">
    <div class="banner">
        <img class="banner-img" src="./static/image/welcome.png" width="732px" height="372" alt="<?php echo $goods['name'] ?>">
    </div>
    <div class="img-content">
        <ul>
            <?php foreach($goods as $v):?>
            <li>
                <img class="img-li-fix" src="<?php echo $v['pic']?>" alt="<?php echo $v['name']?>">
                <div class="info">
                    <a href="detail.php?id=<?php echo $v['id']?>"><h3 class="img_title"><?php echo $v['name']?></h3></a>
                    <p>
                        <?php echo $v['des']?>
                    </p>
                    <div class="btn">
                        <a href="edit.php?id=<?php echo $v['id']?>" class="edit">编辑</a>
                        <a href="delete.php?id=<?php echo $v['id']?>" class="del">删除</a>
                    </div>
                </div>
            </li>
            <?php endforeach;?>
			<?php echo $pages ?>
        </ul>
    </div>
    
</div>

<div class="footer">
    <p><span>M-GALLARY</span>©2017 POWERED BY IMOOC.INC</p>
</div>
</body>
<script src="./static/js/jquery-1.10.2.min.js"></script>
<script>
    $(function () {
        $('.del').on('click',function () {
            if(confirm('确认删除该画品吗?'))
            {
               window.location = $(this).attr('href');
            }
            return false;
        })
    })
</script>


</html>