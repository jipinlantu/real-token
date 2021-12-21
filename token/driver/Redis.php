<?php

namespace jmwl\realjwt\token\driver;

use jmwl\realjwt\token\Driver;

/**
 * Token操作类
 */
class Redis extends Driver
{

    protected $options = [
        'host'        => '127.0.0.1',
        'port'        => 6379,
        'password'    => '',
        'select'      => 0,
        'timeout'     => 0,
        'expire'      => 0,
        'persistent'  => false,
        'userprefix'  => 'up:',
        'tokenprefix' => 'tp:',
    ];

    /**
     * 构造函数
     * @param array $options 缓存参数
     * @throws \BadFunctionCallException
     * @access public
     */
    public function __construct($options = [])
    {
        //判断扩展是否安装
        if (!extension_loaded('redis')) {
            //异常类 $message= "" , int $code= 0 , ? Throwable $previous
            throw new \BadFunctionCallException('not support: redis');
        }
        //合并配置
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        // var_dump(  $this->options);die;
        //创建链接
        $this->handler = new \Redis;
        //判断是否是持续的
        if ($this->options['persistent']) {
            //脚本结束之后连接不释放，连接保持在php-fpm进程中。
            //host，port，timeout，持续的id
            $this->handler->pconnect($this->options['host'], $this->options['port'], $this->options['timeout'], 'persistent_id_' . $this->options['select']);
        } else {
            //脚本结束之后连接就释放了。
            $this->handler->connect($this->options['host'], $this->options['port'], $this->options['timeout']);
        }

        if ('' != $this->options['password']) {
            $this->handler->auth($this->options['password']);
        }

        if (0 != $this->options['select']) {
            //Redis Select 命令用于切换到指定的数据库，数据库索引号 index 用数字值指定，以 0 作为起始索引值。
            $this->handler->select($this->options['select']);
        }
    }

    /**
     * 获取加密后的Token
     * @param string $token Token标识
     * @return string
     */
    protected function getEncryptedToken($token)
    {
        $config = \think\Config::get('token');
        return $this->options['tokenprefix'] . hash_hmac($config['hashalgo'], $token, $config['key']);
    }

    /**
     * 获取会员的key
     * @param $user_id
     * @return string
     */
    protected function getUserKey($user_id)
    {
        return $this->options['userprefix'] . $user_id;
    }

    /**
     * 存储Token
     * @param   string $token   Token
     * @param   int    $user_id 会员ID
     * @param   int    $expire  过期时长,0表示无限,单位秒
     * @return bool
     */
    public function set($token, $user_id, $expire = 0)
    {
        //过期时间
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        if ($expire instanceof \DateTime) {
            $expire = $expire->getTimestamp() - time();
        }
        //获取加密后的Token
        $key = $this->getEncryptedToken($token);
        
        // Redis 字符串(string)
        if ($expire) {
            $result = $this->handler->setex($key, $expire, $user_id);
        } else {
            $result = $this->handler->set($key, $user_id);
        }
        //写入会员关联的token Redis Sadd 命令将一个或多个成员元素加入到集合中，已经存在于集合的成员元素将被忽略。
        $this->handler->sAdd($this->getUserKey($user_id), $key);
        return $result;
    }

    /**
     * 获取Token内的信息
     * @param   string $token
     * @return  array
     */
    public function get($token)
    {
        $key = $this->getEncryptedToken($token);
        $value = $this->handler->get($key);
        if (is_null($value) || false === $value) {
            return [];
        }
        //获取有效期
        $expire = $this->handler->ttl($key);
        $expire = $expire < 0 ? 365 * 86400 : $expire;
        $expiretime = time() + $expire;
        //解决使用redis方式储存token时api接口Token刷新与检测因expires_in拼写错误报错的BUG
        $result = ['token' => $token, 'user_id' => $value, 'expiretime' => $expiretime, 'expires_in' => $expire];

        return $result;
    }

    /**
     * 判断Token是否可用
     * @param   string $token   Token
     * @param   int    $user_id 会员ID
     * @return  boolean
     */
    public function check($token, $user_id)
    {
        $data = self::get($token);
        return $data && $data['user_id'] == $user_id ? true : false;
    }

    /**
     * 删除Token
     * @param   string $token
     * @return  boolean
     */
    public function delete($token)
    {
        $data = $this->get($token);
        if ($data) {
            $key = $this->getEncryptedToken($token);
            $user_id = $data['user_id'];
            $this->handler->del($key);
            $this->handler->sRem($this->getUserKey($user_id), $key);
        }
        return true;

    }

    /**
     * 删除指定用户的所有Token
     * @param   int $user_id
     * @return  boolean
     */
    public function clear($user_id)
    {
        $keys = $this->handler->sMembers($this->getUserKey($user_id));
        $this->handler->del($this->getUserKey($user_id));
        $this->handler->del($keys);
        return true;
    }

}
