<?php

namespace Daijulong\Generator\Commands;

use Daijulong\Generator\Abstracts\Command;

class Publish extends Command
{
    /**
     * 命令行选项别名
     *
     * @var array
     */
    protected $option_alias = [
        'only' => 'o'
    ];

    /**
     * 运行命令
     *
     * @return mixed
     */
    public function run()
    {
        $dir = getcwd();
        $only = $this->getOption('only', 'bin,config,stub');
        $publish_items = explode(',', $only);
        if (in_array('bin', $publish_items)) {
            $this->publishBin($dir);
        }
        if (in_array('config', $publish_items)) {
            $this->publishConfig($dir);
        }
        if (in_array('stub', $publish_items)) {
            $this->publishStubs($dir);
        }
    }

    protected function publishStubs($dir)
    {
        $source_dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'stubs';
        $dest_dir = $dir . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'stubs';
        $this->copyDir($source_dir, $dest_dir, 0644);
    }

    /**
     * 发布配置文件 generator.json
     *
     * @param $dir
     */
    protected function publishConfig($dir)
    {
        $bin_file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'generator.json';
        $target_file = $dir . DIRECTORY_SEPARATOR . 'generator.json';
        $this->copyFile($bin_file, $target_file, 0644);
    }

    /**
     * 发布可执行脚本文件
     *
     * @param $dir
     */
    protected function publishBin($dir)
    {
        $bin_file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'generator';
        $target_file = $dir . DIRECTORY_SEPARATOR . 'generator';
        $this->copyFile($bin_file, $target_file, 0755);
    }


    /**
     * 复制文件
     *
     * @param string $source
     * @param string $dest
     */
    protected function copyFile($source, $dest, $mode = null)
    {
        if (!is_file($source)) {
            $this->console->error('原始文件 ' . $source . ' 不存在！');
            die(1);
        }
        $dir = dirname($dest);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                $this->console->error('创建目录 ' . $dir . ' 失败！');
                die(1);
            }
        }
        if (is_file($dest) && !$this->console->yesOrNo('文件 ' . $dest . ' 已经存在，是否覆盖？')) {
            return;
        }
        if (!@copy($source, $dest)) {
            $this->console->error('复制文件 ' . $source . ' 到 ' . $dir . DIRECTORY_SEPARATOR . 'generator' . ' 失败！');
            die(1);
        }
        if ($mode) {
            @chmod($dest, $mode);
        }
        $this->console->success('已生成文件：' . $dest);
    }

    /**
     * 复制目录
     *
     * @param string $source_dir
     * @param string $dest_dir
     * @param null $file_mode
     */
    protected function copyDir($source_dir, $dest_dir, $file_mode = null)
    {
        $copy_dir = function ($source_dir, $dest_dir) use (&$copy_dir) {
            if (!file_exists($dest_dir)) {
                mkdir($dest_dir, 0755, true);
            }
            $files = scandir($source_dir);
            foreach ($files as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                }
                $real_file = $source_dir . DIRECTORY_SEPARATOR . $file;
                if (is_file($real_file)) {
                    $this->copyFile($real_file, $dest_dir . DIRECTORY_SEPARATOR . $file);
                } elseif (is_dir($real_file)) {
                    $copy_dir($real_file, $dest_dir . DIRECTORY_SEPARATOR . $file);
                }
            }
        };
        $copy_dir($source_dir, $dest_dir, $file_mode);
    }

}
