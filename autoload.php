<?php
class Loader {
    public static $alias = ['app' => __DIR__];
    public static function getAlias($name){
        return isset(static::$alias[$name]) ? static::$alias[$name] :"";
    }
    public static function loadClass($className) {
        $newClassName = str_replace("\\","/",$className);
        $aliasPos = strpos($className,"\\");
        if($aliasPos !== false){
            $alias = substr($className,0,$aliasPos);
        }else{
            $alias = $className;
        }
        $rootDir = static::getAlias($alias);
        if($alias === ""){
            return ;
        }
        $pos = strpos($newClassName, '/');
        $newClassName = substr($newClassName, $pos+1);
        $fullPath = $rootDir."/".$newClassName.".php";
        if (!is_file($fullPath)) {
            throw new \Exception("类{$className}不存在,请检查");
        }
        include $fullPath;
        if(!class_exists($className) && !interface_exists($className)) {
            throw new \Exception("类{$className}不存在,请检查");
        }
    }
}
spl_autoload_register("Loader::loadClass");