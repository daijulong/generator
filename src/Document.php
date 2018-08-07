<?php

namespace Daijulong\Generator;

use Daijulong\Console\Console;

class Document
{

    /**
     * 显示文档
     *
     * @param $name
     */
    public static function show($name)
    {
        $console = Console::instance();
        $manual = static::getManual($name);
        if (!empty($manual)) {
            $console->blankLine();
            foreach ($manual as $key => $item) {
                if (!is_int($key)) {
                    $console->text($key, 'yellow');
                }
                if (is_string($item)) {
                    $console->text($item);
                } elseif (is_array($item) && !empty($item)) {
                    $left_length = Helper::getMaxLength(array_keys($item));//左侧宽度
                    foreach ($item as $k => $d) {
                        $left_str = $console->colored('  ' . str_pad((is_int($k) ? '' : $k), $left_length + 2, ' ', STR_PAD_RIGHT), 'green');
                        $console->text($left_str . (is_string($d) ? $d : ''));
                    }
                }
                $console->blankLine();
            }
        } else {
            $console->text('无帮助内容！');
        }
    }

    /**
     * 读取文档文件内容
     *
     * @param $name
     * @return array|mixed
     */
    private static function getManual($name)
    {
        $manual_file = implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            'resources',
            'manual',
            $name . '.php'
        ]);
        if (!file_exists($manual_file)) {
            return [];
        }
        return require $manual_file;
    }
}
