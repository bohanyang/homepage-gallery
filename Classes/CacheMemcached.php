<?php

class Cache
{
    public $mc;

    public function __construct($config = null)
    {
        $mc = new Memcached();

        if ($mc != false) {
            $this->mc = $mc;
        }
    }

    public function func($key, $func, $expireAt = null)
    {
        $mc = $this->mc;
        $result = $mc->get($key);
        if (!empty($result)) {
            return $result;
        } else {
            $value = $func();
            if ($mc->add($key, $value, $expireAt)) {
                return $value;
            }
        }
        echo 'Error occurred.';
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
