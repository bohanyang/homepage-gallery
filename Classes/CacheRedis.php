<?php

class Cache
{
    public $redis;

    public function __construct($config = null)
    {
        $config = json_decode($config);

        $host = $config->host ?: '127.0.0.1';
        $port = $config->port ?: null;
        $password = $config->password ?: null;

        $redis = new Redis();

        if ($redis->connect($host, $port)) {
            if ($password === null || $redis->auth($password)) {
                $this->redis = $redis;
            }
        }
    }

    public function func($key, $func, $expireAt = null)
    {
        $redis = $this->redis;
        if ($redis->exists($key)) {
            return $redis->get($key);
        } else {
            $value = $func();
            if ($redis->set($key, $value)) {
                if (!empty($expireAt)) {
                    $redis->expireAt($key, $expireAt);
                }

                return $value;
            }
        }
    }

    public function dailyExpire($time)
    {
        $expire = strtotime(date('Y-m-d').' '.$time);
        if (time() >= $expire) {
            $expire = strtotime(date('Y-m-d').'+1 day '.$time);
        }

        return $expire;
    }
}
