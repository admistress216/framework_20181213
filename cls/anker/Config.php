<?php
namespace app\cls\anker;
class Config
{
    private static $configPath = APP_ROOT."/config/";
    private static $config;
    /**
     * 注册配置文件
     * @param $configFileName string 配置文件名称
     * @param string $reName string 重定向
     */
    public static function load($configFileName, $reName = '')
    {
        if (empty($reName)) {
            $reName = strval($configFileName);
        } else {
            $reName = strval($reName);
        }
        if (isset(static::$config[$reName])) {
            return static::$config[$reName];
        }
        $fullPath = static::$configPath.$configFileName.'.php';
        if (file_exists($fullPath)) {
            static::$config[$reName] = include $fullPath;
            return static::$config[$reName];
        } else {
            throw new \Exception("{$configFileName} file not exist !");
        }
    }

    /**
     * 从注册的配置文件中找出配置项
     * @param $key string 配置文件名称
     * @param string $itemName 配置文件元素
     * @return bool|null
     */
    public static function item($key, $itemName = '')
    {
        if(empty($itemName)) {
            return empty(static::$config[$key]);
        }
        if(isset(static::$config[$key][$itemName])) {
            return static::$config[$key][$itemName];
        } else {
            return NULL;
        }
    }
}