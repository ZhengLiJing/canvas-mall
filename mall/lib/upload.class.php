<?php 
header('Content-Type:text/html;charset=utf-8');
class upload{
	protected $filename;
	protected $maxSize;
	protected $allowExt;
	protected $allowMime;
	protected $uploadPath;
	protected $imgFlag;
	protected $fileInfo;
	protected $error;
	protected $ext;
	protected $destination;
	protected $imgUrl;
	//构造函数
	public function __construct($uploadPath='uploads',$filename='myFile',$maxSize=5242880,$allowExt=array('png','jpeg','jpg','gif'),$allowMime=array('image/jpeg','image/jpg','image/png','image/gif'),$imgFlag=1){
		// var_dump($filename);exit();
		$this->filename = $filename;
		// print_r($this->filename);exit();
		$this->maxSize = $maxSize;
		$this->allowExt = $allowExt;
		$this->allowMime = $allowMime;
		$this->uploadPath = $uploadPath;
		$this->imgFlag = $imgFlag;
		$this->fileInfo = $_FILES[$this->filename];
		// $this->fileInfo = ($this->filename)[0];
	}
	/**
	 * 检测上传文件是否出错
	 */
	protected function checkError(){
		// echo $this->fileInfo['error'];exit();
		if(!is_null($this->fileInfo)){
			if($this->fileInfo['error']>0){
				switch ($this->fileInfo['error']) {
					case 1:
						$this->error = "超过了PHP配置文件中upload_max_filesize选项的值";
						break;
					case 2:
						$this->error = "超过了表单中MAX_FILE_SIZE设置的值";
						break;
					case 3:
						$this->error = "文件部分被上传";
						break;
					case 4:
						$this->error = "没有选择上传文件";
						break;
					case 6:
						$this->error = "没有找到临时目录";	
						break;
					case 7:
						$this->error = "文件不可写";
						break;					
					case 8:
						$this->error = "由于PHP的扩展程序中断文件上传";
						break;					
				}
				return false;
			}
			return true;			
		}else{
			$this->error = "文件上传出错";
		}

	}

	/**
	 * 检测文件大小
	 */
	protected function checkSize(){
		if($this->fileInfo['size']>$this->maxSize){
			$this->error = "上传文件过大";
			return false;
		}
		return true;
	}

	/**
	 * 检测扩展名是否正确
	 */
	protected function checkExt(){
		$this->ext = strtolower(pathinfo($this->fileInfo['name'],PATHINFO_EXTENSION));
		if(!in_array($this->ext,$this->allowExt)){
			$this->error = "文件扩展名不正确";
			return false;
		}
		return true;
	}

	/**
	 * 检测文件类型
	 */
	protected function checkMime(){
		if(!in_array($this->fileInfo['type'], $this->allowMime)){
			$this->error = "不允许的文件类型";
			return false;
		}
		return true;
	}

	/**
	 * 检测是否为真实图片
	 */
	protected function checkTrueImg(){
		if($this->imgFlag){
			if(@!getimagesize($this->fileInfo['tmp_name'])){
				$this->error = "不是真实图片";
				return false;
			}
			return true;
		}
	}

	/**
	 * 检测是否通过HTTP POST上传的文件
	 */
	protected function checkHttpPost(){
		if(@!is_uploaded_file($this->fileInfo['tmp_name'])){
			$this->error = "不是通过HTTP POST上传的文件";
			return false;
		}
		return true;
	}

	/**
	 * 检测上传文件目录是否存在，不存在则创建
	 */
	protected function checkUploadPath(){
		if(!file_exists($this->uploadPath)){
			mkdir($this->uploadPath,0777,true);
		}
	}

	/**
	 * 得到唯一名字
	 */
	protected function getUniName(){
		return md5(uniqid(microtime(time()),true));
		// return md5(microtime(time()),true);
	}

	protected function showError(){
		exit("<span style=color:red>".$this->error."</span>");
	}
	public function uploadFile(){
		if($this->checkError()&&$this->checkSize()&&$this->checkExt()&&$this->checkMime()&&$this->checkTrueImg()&&$this->checkHttpPost()){
			$this->checkUploadPath();
			$this->uniName = $this->getUniName();
			$this->destination = $this->uploadPath.'/'.$this->uniName.'.'.$this->ext;
			$this->imgUrl = "http://localhost/mall/".$this->destination;
			if(@!move_uploaded_file($this->fileInfo['tmp_name'], $this->destination)){
				$this->error = "上传文件失败";
			}
			return $this->imgUrl;
		}else{
			$this->showError();
		}
	}
}