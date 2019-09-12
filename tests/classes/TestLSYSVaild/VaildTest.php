<?php
namespace TestLSYSVaild;
use PHPUnit\Framework\TestCase;
use LSYS\Validation;
use LSYS\Validation\Valid;
class VaildTest extends TestCase
{
    public function testVaild()
    {
        $data=array(
            "username"=>"ddddd",
        );
        $validation = Validation::factory($data);
        //给字段添加别名,显示用
        $validation->label("username",__("username"));
        //添加判断规则
        $validation->rule('username', 'notEmpty');
        $this->assertTrue($validation->check());
        $validation->rule('username', 'minLength', array(':value', 10));
        $this->assertFalse($validation->check());
        $this->assertArrayHasKey("username",$validation->errors(true));
        $data=array(
            "username"=>"ddddd",
        );
        $validation = Validation::factory($data);
        $validation->rule("username",function($valid,$value){
                //自定义校验函数
                //添加错误消息,校验未通过时赋值
                $valid->error("username", "bbbb");
                $valid->message("username.bbbb",__("bad msg"));
        },array(':validation',':value')/*第三个参数为参数定义*/)
            ;
        $this->assertFalse($validation->check());
        $this->assertArrayHasKey("username",$validation->errors(true));
        $this->assertEquals($validation->get("username"), "ddddd");
        
        $this->assertTrue(Valid::alpha("aaa"));
        $this->assertTrue(Valid::alphaDash("aaa"));
        $this->assertTrue(Valid::digit(111));
        $this->assertTrue(Valid::email("ss@dd.com"));
        $this->assertTrue(Valid::emailDomain("ss@aliyun.com"));
        $this->assertTrue(Valid::comp(1, 1,'='));
        $this->assertTrue(Valid::date("1988-12-11"));
        $this->assertTrue(Valid::equals("a","a"));
        $this->assertTrue(Valid::ip("128.132.1.1"));
        $this->assertTrue(Valid::maxLength("aa",3));
        $this->assertTrue(Valid::minLength("aa3",3));
        $this->assertTrue(Valid::existKeys(["a"=>'1'], ["a"]));
        $this->assertFalse(Valid::chinaId("441421198711242222"));
        
    }
}