<?php

namespace Daijulong\Generator;

use Daijulong\Console\Console;

class Config
{

    /**
     * 配置，来自于 generator.json
     *
     * @var null
     */
    protected static $config = null;

    /**
     * 加载配置
     */
    protected static function load()
    {

        if (!static::$config) {
            $config_file = getcwd() . DIRECTORY_SEPARATOR . 'generator.json';
            $config_file_content = file_exists($config_file) ? file_get_contents($config_file) : null;
            $config = json_decode($config_file_content, true);
            if (is_null($config)) {
                $error_message = '加载配置失败了，请检查配置文件：./generator.json';
                Console::instance()->error($error_message);
                die(1);
            }
            static::$config = $config;
        }
    }

    /**
     * 获取配置值
     *
     * @param string $node
     * @param null $default
     * @return null
     */
    public static function get($node = '', $default = null)
    {
        static::load();
        $config = static::$config;
        if ($node) {
            $nodes = explode('.', $node);
            $_config = static::$config;
            foreach ($nodes as $_node) {
                if (!isset($_config[$_node])) {
                    $_config = $default;
                    break;
                }
                $_config = $_config[$_node];
            }
            $config = $_config;
        }
        return $config;
    }
}
