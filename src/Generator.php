<?php

namespace Daijulong\Generator;

use Daijulong\Generator\Commands\Manual;

class Generator
{
    /**
     * 执行命令
     *
     * @param array $argv
     */
    public static function command(array $argv = [])
    {
        $command = array_shift($argv);
        $command_class = '\\Daijulong\\Generator\\Commands\\' . Helper::camelCase($command);
        list($options, $arguments) = static::parseArguments($argv);
        if (!class_exists($command_class)) {
            $command_class = Manual::class;
        }
        //如果出现h或help，则显示帮助信息
        if (isset($options['h']) || isset($options['help'])) {
            if (empty($arguments)) {
                $arguments = [$command];
            }
            $command_class = Manual::class;
        }

        $command_instance = new $command_class($options, $arguments);
        $command_instance->run();
    }

    /**
     * 解析选项及参数
     *
     * @param array $argv
     * @return array
     */
    private static function parseArguments($argv = [])
    {
        $options = [];
        $arguments = [];

        foreach ($argv as $arg) {
            if ($arg[0] == '-') {
                $_arg = explode('=', $arg, 2);
                $_option = trim($_arg[0], '-');
                if ($_option != '') {
                    $options[mb_strtolower($_option)] = isset($_arg[1]) ? $_arg[1] : '';
                }
            } else {
                $arguments[] = $arg;
            }
        }

        return [$options, $arguments];
    }
}
