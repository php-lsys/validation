# 数组校验
> 实现对数据的有效性的校验,常用的$_POST 和$_GET的有效性的检测

示例代码:
```php
use LSYS\Validation;
include __DIR__."/Bootstarp.php";
//$data=$_POST;
$data=array(
	"username"=>"ddddd",
);
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
	},array(':validation',':value')/*第三个参数为参数定义*/);
//进行校验
if (!$validation->check()){
	//校验未通过
	print_r($validation->errors(TRUE));
}
```