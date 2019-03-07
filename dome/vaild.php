<?php
use LSYS\Validation;
include __DIR__."/Bootstarp.php";
//$data=$_POST;
$data=array(
	"username"=>"ddddd",
);




class bb{
	public static function bdb(){
		var_dump(func_get_args());
		return false;
	}
}

$validation = Validation::factory($data); 


//给字段添加别名,显示用
$validation->label("username",__("username"));
//添加判断规则
$validation->rule('username', 'not_empty')
	->rule('username', 'min_length', array(':value', 4))
	->rule("username",function($valid,$value){
	//自定义校验函数
	//添加错误消息,校验未通过时赋值
	$valid->error("username", "bbbb");
	$valid->message("username.bbbb",__("bad msg"));
	},array(':validation',':value')/*第三个参数为参数定义*/)
	//外部函數
	->rule('username', 'bb::bdb')
	->message("username.bb::bdb", "ffff")
;
//批量添加规则
// $validation->rules('nickname', array (
// 		array('notEmpty',array(':value')),
// 		array('in_array',array(':value', [1,2])),
// 		array('maxLength', array(':value', 32)),
// ));
//进行校验
if (!$validation->check()){
	//校验未通过
	print_r($validation->errors(TRUE));
	exit;
}
//检测不通过抛出异常
//$validation->throwCheck();
//取出单个数据
$username=$validation->get("username");
//按指定KEY生成一个数组
$data=$validation->gets(array(    
    "username"=>[]//['不存在时默认值','键值不是username时使用']
));
// $data=$validation->gets(array(
//     "UserName"=>[null,'username']
// ));