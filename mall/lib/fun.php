<?php 
/**
 * 数据库连接初始化
 * @param $host
 * @param $username
 * @param $password
 * @param $dbName
 * @return bool|resource
 */
function mysqlInit($host,$username,$password,$dbName){
	$mysqli = new mysqli($host,$username,$password,$dbName);
	if($mysqli->connect_errno){
		echo $mysqli->connect_error;
		return false;
	}
	$mysqli->set_charset("UTF8");
	return $mysqli;
}

/**
 * 密码加密
 * @param $password
 * @return bool|string
 */
function createPassword($password){
	if(empty($password)){
		return false;
	}
	return md5(md5($password)."imooc");
}

/**
 * 消息提示
 * @param int $type 1:成功 2:失败
 * @param null $msg
 * @param null $url
 */
function msg($type,$msg=null,$url=null){

	$toUrl = "location:msg.php?type={$type}";
	$toUrl .= $msg ? "&msg={$msg}" : "";
	$toUrl .= $url ? "&url={$url}" : "";

	header($toUrl);
	exit();
}

function checkLogin(){
	//开启session
	session_start();
	//判断session中是否有登录用户
	if(!isset($_SESSION['user']) && empty($_SESSION['user'])){
		return false;
	}
	return true;
}

/**
 * $total 总数据
 * $pageSize 每页显示的条数
 * $curPage 当前页码
 * $showPages 要显示页码，如要显示 4 5 6 7 8 9共6个页码显示
 * 返回字符串，包含HTML代码
 */

function showPages($total,$pageSize,$curPage,$showPages){
	$pageStr = '';
	//当总页数大于每页显示的条数时才进行分页处理
	if($total>$pageSize){

		$pageStr .= '<div class="page-nav">';
        $pageStr .= '<ul>';

		//总页码数为
		$totalPages = ceil($total / $pageSize);//10

		//当前页码的容错,若当前页码大于最大的页码时，取最大的页码，否则为本身
		$curPage = $curPage > $totalPages ? $totalPages: $curPage;//

		

		//起始页
		$from = max(1,$curPage - intval($showPages/2));
		// var_dump($from);die;

		//终止页
		$to = $from + $showPages -1;

		//当终止页码大于最大的页码时，取最大页码，起始页码则要根据终止页码来算。
		if($to>$totalPages){

			$to = $totalPages;
			$from = max(1,$to-$showPages+1);
		}


		//显示首页、上一页
		if($curPage>1){

			$pageStr .= "<li><a href='".pageUrl(1)."'>首页</a></li>";
            // $pageStr .= "<li><a href='" . pageUrl(1) . "'>首页</a></li>";
            $pageStr .= "<li><a href='".pageUrl($curPage-1)."'>上一页</a></li>";
            // $pageStr .= "<li><a href='" . pageUrl($currentPage - 1) . "'>上一页</a></li>";

            //显示...
            $pageStr .= '<li>...</li>';
		}




		//显示中间部分的显示页码
		for($i=$from;$i<=$to;$i++){

			if($i != $curPage){

				// $pageStr .= "<li><a href='" . pageUrl($i) . "'>{$i}</a></li>";
				$pageStr .= "<li><a href='".pageUrl($i)."'>{$i}</a></li>";
			}else{

				$pageStr .= "<li><span class='curr-page'>{$i}</span></li>";
			}
		}

		//显示...
		if($curPage<$totalPages){

			$pageStr .= '<li>...</li>';
		}

		//和尾页、下一页
		if($curPage<$totalPages){

			$pageStr .= "<li><a href='".pageUrl($totalPages)."'>尾页</a></li>";
            // $pageStr .= "<li><a href='" . pageUrl(1) . "'>尾页</a></li>";
            $pageStr .= "<li><a href='".pageUrl($curPage+1)."'>下一页</a></li>";
            // $pageStr .= "<li><a href='" . pageUrl($currentPage + 1) . "'>下一页</a></li>";
		}
		//闭合标签
		$pageStr .= '</ul>';
		$pageStr .= '</div>';
	}

	return $pageStr;
}

/**
 * 获得url
 */
function getUrl(){
	$url = '';
	//判断是HTTPS还是HTTP
	$url .= $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
	//获得域名
	$url .= $_SERVER['HTTP_HOST'];
	//获取URL参数
	$url .= $_SERVER['REQUEST_URI'];
	return $url;
	}

function pageUrl($page,$url=''){
	//判断url是否为空
	$url = empty($url) ? getUrl() : $url;

	//没有带参数的情况
	$pos = strpos($url,'?');
	if(!$pos){
		$url .= "?page={$page}";
	}
	//带有参数的情况
	else{
		$params = substr($url,$pos+1);
		//解析params为数组
		parse_str($params,$paramsArr);
		//释放数组中的page参数
		if(isset($paramsArr['page'])){
			unset($paramsArr['page']);
		}
		$paramsArr['page'] = $page;
		//将paramsArr重新拼接成params
		$params = http_build_query($paramsArr);

		//拼装url
		$url = substr($url,0,$pos).'?'.$params;
	}
	return $url;
}