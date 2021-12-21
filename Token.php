<?php

namespace jmwl\realjwt;

use jmwl\realjwt\token\Driver;
use think\facade\Config;
use jmwl\realjwt\token\driver\Mysql;
use jmwl\realjwt\token\driver\Redis;

/**
 * Token操作类
 */
class Token
{
    /**
     * @var array Token的实例
     */
    public static $instance = [];

    /**
     * @var object 操作句柄
     */
    public static $handler;

    /**
     * 连接Token驱动
     * @access public
     * @param  array       $options 配置数组
     * @param  bool|string $name    Token连接标识 true 强制重新连接
     * @return Driver
     */
    public static function connect(array $options = [], $name = false)
    {
        // var_dump(new Mysql($options));die;
        $type = !empty($options['type']) ? $options['type'] : 'File';

        if (false === $name) {
            $name = md5(serialize($options));
        }

        if (true === $name || !isset(self::$instance[$name])) {
            $class =   "jmwl\\realjwt\\token\\driver\\".ucwords($type);

            // 记录初始化信息
        //   var_dump($class);die;

            if (true === $name) {
                return new $class($options);
            }

            self::$instance[$name] = new $class($options);
        }

        return self::$instance[$name];
    }
    /*
    后期可以加入登录端（pc、app等）
    根据用户id获取用户token
    根据token获取用户uid
    同端互斥
    
    */
    /**
     * 自动初始化Token
     * @access public
     * @param  array $options 配置数组 如果这个被定义，按照这个来 [
        // 驱动方式
        'type'     => 'Mysql',
        'table'     =>"user_token",
        
        'userprefix'=>"up:",
        'tokenprefix'=>"tp:",
        // 缓存前缀
        'key'      => '30tvUyXl8NkOx7YKDAoLu5FQRIGTh4qa',
        // 加密方式
        'hashalgo' => 'ripemd160',
        // 缓存有效期 0表示永久缓存
        'expire'   => 0,
    ]
     * @return Driver
     */
    public static function init(array $options = [])
    {
        if (is_null(self::$handler)) {
            if (empty($options) && 'complex' == Config::get('app.token.type')) {
                $default = Config::get('app.token.default');
                // 获取默认Token配置，并连接
                $options = Config::get('app.token.' . $default['type']) ?: $default;
            } elseif (empty($options)) {
                $options = Config::get('app.token');
            }

            self::$handler = self::connect($options);
        }

        return self::$handler;
    }

    /**
     * 判断Token是否可用(check别名)
     * @access public
     * @param  string $token Token标识
     * @return bool
     */
    public static function has($token, $user_id)
    {
        return self::check($token, $user_id);
    }

    /**
     * 判断Token是否可用
     * @param string $token Token标识
     * @return bool
     */
    public static function check($token, $user_id)
    {
        return self::init()->check($token, $user_id);
    }

    /**
     * 读取Token
     * @access public
     * @param  string $token   Token标识
     * @param  mixed  $default 默认值
     * @return mixed
     */
    public static function get($token, $default = false)
    {
        return self::init()->get($token) ?: $default;
    }

    /**
     * 写入Token
     * @access public
     * @param  string   $token   Token标识
     * @param  mixed    $user_id 存储数据
     * @param  int|null $expire  有效时间 0为永久
     * @return boolean
     */
    public static function set($token, $user_id, $expire = null)
    {
        return self::init()->set($token, $user_id, $expire);
    }

    /**
     * 删除Token(delete别名)
     * @access public
     * @param  string $token Token标识
     * @return boolean
     */
    public static function rm($token)
    {
        return self::delete($token);
    }

    /**
     * 删除Token
     * @param string $token 标签名
     * @return bool
     */
    public static function delete($token)
    {
        return self::init()->delete($token);
    }

    /**
     * 清除Token
     * @access public
     * @param  int user_id 用户编号
     * @return boolean
     */
    public static function clear($user_id = null)
    {
        return self::init()->clear($user_id);
    }

}
