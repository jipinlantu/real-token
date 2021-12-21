<p align="center">
	<img alt="logo" src="https://www.jmwl51.com/uploads/20211015/6f09c3714c780e92694318f8d935e2ca.png" width="150"
		height="150">
</p>
<h1 align="center" style="margin: 30px 0 30px; font-weight: bold;">real-Token v1.28.0</h1>
<h4 align="center">一个轻量级 thinkphp6 权限认证框架，让鉴权变得简单、优雅！</h4>
<p align="center">
	<a href="https://gitee.com/jinan-jimeng-network_0/real-token/stargazers"><img
			src="https://gitee.com/jinan-jimeng-network_0/real-token/badge/star.svg?theme=gvp"></a>
	<a href="https://gitee.com/jinan-jimeng-network_0/real-token/members"><img
			src="https://gitee.com/jinan-jimeng-network_0/real-token/badge/fork.svg?theme=gvp"></a>
	<!--<a href="https://github.com/jinan-jimeng-network_0/real-token/stargazers"><img
			src="https://img.shields.io/github/stars/jinan-jimeng-network_0/real-token?style=flat-square&logo=GitHub"></a>
	<a href="https://github.com/jinan-jimeng-network_0/real-token/network/members"><img
			src="https://img.shields.io/github/forks/jinan-jimeng-network_0/real-token?style=flat-square&logo=GitHub"></a>
	<a href="https://github.com/jinan-jimeng-network_0/real-token/watchers"><img
			src="https://img.shields.io/github/watchers/jinan-jimeng-network_0/real-token?style=flat-square&logo=GitHub"></a>
	<a href="https://github.com/jinan-jimeng-network_0/real-token/issues"><img
			src="https://img.shields.io/github/issues/jinan-jimeng-network_0/real-token.svg?style=flat-square&logo=GitHub"></a>
	<a href="https://github.com/jinan-jimeng-network_0/real-token/blob/master/LICENSE"><img
			src="https://img.shields.io/github/license/jinan-jimeng-network_0/real-token.svg?style=flat-square"></a>-->
</p>

# 基础介绍
 基于tp6的权限验证模块,参考fastadmin的jwt模块

# 目录结构

- token/Driver.php规范token的方法和功能
- token/driver/Mysql token的数据库驱动
- token/driver/Redis token的Redis驱动 
- token.php token的链接token驱动并执行驱动的方法
- auth.php  权限管理类
# 数据库
如果多模块的话，需要建立对应的用户表、用户分组表、用户权限表
```sql
/*
Navicat MySQL Data Transfer

Source Server         : real-think
Source Server Version : 50734
Source Host           : 8.136.216.46:3306
Source Database       : real-think

Target Server Type    : MYSQL
Target Server Version : 50734
File Encoding         : 65001

Date: 2021-10-17 15:18:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for jm_admin
-- ----------------------------
DROP TABLE IF EXISTS `jm_admin`;
CREATE TABLE `jm_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '组别ID',
  `username` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '昵称',
  `password` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '密码',
  `salt` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '密码盐',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '电子邮箱',
  `mobile` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '手机号',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '等级',
  `gender` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别:0=女,1=男',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `bio` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '格言',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `score` int(10) NOT NULL DEFAULT '0' COMMENT '积分',
  `successions` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '连续登录天数',
  `maxsuccessions` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '最大连续登录天数',
  `prevtime` int(10) DEFAULT NULL COMMENT '上次登录时间',
  `logintime` int(10) DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '登录IP',
  `loginfailure` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '失败次数',
  `joinip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '加入IP',
  `jointime` int(10) DEFAULT NULL COMMENT '加入时间',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Token',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '状态',
  `verification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '验证',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `mobile` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员表';

-- ----------------------------
-- Table structure for jm_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `jm_admin_group`;
CREATE TABLE `jm_admin_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '组名',
  `rules` text COLLATE utf8mb4_unicode_ci COMMENT '权限节点',
  `createtime` int(10) DEFAULT NULL COMMENT '添加时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `status` enum('normal','hidden') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员组表';

-- ----------------------------
-- Table structure for jm_admin_rule
-- ----------------------------
DROP TABLE IF EXISTS `jm_admin_rule`;
CREATE TABLE `jm_admin_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) DEFAULT NULL COMMENT '父ID',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '名称',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '标题',
  `remark` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `ismenu` tinyint(1) DEFAULT NULL COMMENT '是否菜单',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(10) DEFAULT '0' COMMENT '权重',
  `status` enum('normal','hidden') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员规则表';

-- ----------------------------
-- Table structure for jm_admin_token
-- ----------------------------
DROP TABLE IF EXISTS `jm_admin_token`;
CREATE TABLE `jm_admin_token` (
  `token` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Token',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `expiretime` int(10) DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员Token表';
 
```

# 引用
下载到tp6的extend\jmwl下



```php 
use jmwl\realjwt\Token;
use jmwl\realjwt\Auth;
``` 
# 驱动和配置

 ```php    
   $init= [
            // 驱动方式
            'type'     => 'Mysql',
            'table'     =>"admin_token",//如果是Mysql使用的存储token的表名
            
            'userprefix'=>"up:",//如果是Redis使用的存储用户名前缀
            'tokenprefix'=>"tp:",//如果是Redis使用的存储token前缀
            // 缓存前缀
            'key'      => '30tvUyXl8NkOx7YKDAoLu5FQRIGTh4qa',
            // 加密方式
            'hashalgo' => 'ripemd160',
            // 缓存有效期 0表示永久缓存
            'expire'   => 0,
        ];  
        $usertable="admin";//用户表名，自动对应用户分组表、用户权限表 如用户表名：admin，那么用户分组名：admin_group,用户权限表：admin_rule
```
# token类的使用和功能
这里基本不会用到，主要用的是auth类
 ```php 
    /**
        
        Token::init($init);
        
        //判断Token是否可用
        Token::check($token, $user_id);
        //获取Token内的信息
        Token::get($token, $default = false);
        //写入Token
        Token::set($token, $user_id, $expire = null);
        //删除Token
        Token::delete($token);
        //清除Token
        Token::clear($user_id = null);
 ``` 
 # auth的羁绊用法
 基础用法
  ```php       
                //登录
                // $username="admin";
                // $password="123456"; 
                //  $this->auth->login($username, $password);
                //   $this->auth->getUser()
                // $this->auth->getUserinfo()
                //读取当前token
                // $this->auth->getToken()
                // 判断是否登录
                // $this->auth->isLogin()
                //获取会员组别规则列表
                // $this->auth->getRuleList() 
                  
                // 设置当前请求的URI
                // $controllername= $this->request->controller();  
                // $actionname= $this->request->action();  
                // $path = str_replace('.', '/', $controllername) . '/' . $actionname;
                // $this->auth->setRequestUri($path);  
                // 获取当前请求的URI
                // $this->auth->getRequestUri()
                //检测是否是否有对应权限
                // $this->auth-> check($path)
                //根据Token初始化
                //  $token="1f52e96b-8919-49d0-8ba9-6c4619aa4b8a";
                //  $this->auth->init($token);
                //注册
                // $username="admin2";
                // $password="admin2"; 
                // $email = '18354393242@163.com';
                // $mobile= '18354393243';
                // $this->auth-> register($username, $password, $email, $mobile, $extend = [])
                //加密密码
                //$this->auth->getEncryptPassword($oldpassword, $this->_user->salt);
                //退出
                 //  $this->auth->logout()
                 //真实删除
                //  $this->auth->delete($user_id)
                
                //查看报错
                // $this->auth->getError()  
 ``` 

 php的公共构造方法使用 验证权限
  ```php 
        //权限验证
        $this->auth = Auth::instance($init, $usertable);
        $this->request = Request::instance();

        // token
        $token = $this->request->server('HTTP_TOKEN');

        $controllername = $this->request->controller();
        $actionname = $this->request->action();
        $path = str_replace('.', '/', $controllername) . '/' . $actionname;
        $this->auth->setRequestUri($path);
        // 检测是否需要验证登录
        if (!$this->auth->match($this->noNeedLogin)) {

            //初始化
            $this->auth->init($token);
            //检测是否登录
            if (!$this->auth->isLogin()) {

                return $this->error('Please login first', null, 401);
            }
            // 判断是否需要验证权限
            if (!$this->auth->match($this->noNeedRight)) {
                // 判断控制器和方法判断是否有对应权限
                if (!$this->auth->check($path)) {
                    return $this->error('You have no permission', null, 403);
                }
            }
        } else {

            // 如果有传递token才验证是否登录状态
            if ($token) {
                $this->auth->init($token);
            }
        }
        var_dump($this->auth->getUser());`
 ```         
 
 
