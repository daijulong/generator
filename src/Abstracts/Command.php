<?php

namespace Daijulong\Generator\Abstracts;

use Daijulong\Console\Console;
use Daijulong\Generator\Interfaces\Command as CommandInterface;

abstract class Command implements CommandInterface
{

    /**
     * 命令行选项
     *
     * @var array
     */
    protected $options = [];

    /**
     * 命令行选项别名
     *
     * @var array
     */
    protected $option_alias = [];

    /**
     * 命令行参数
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * 控制台
     *
     * @var Console
     */
    protected $console;

    public function __construct($options = [], $arguments = [])
    {
        $this->console = Console::instance();
        $this->options = $options;
        $this->arguments = $arguments;
    }

    /**
     * 运行命令
     *
     * @return mixed
     */
    abstract function run();

    /**
     * 获取选项值
     *
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    protected function getOption($name, $default = null)
    {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }
        //检查别名
        if (isset($this->option_alias[$name]) && $this->option_alias[$name] != '' && isset($this->options[$this->option_alias[$name]])) {
            return $this->options[$this->option_alias[$name]];
        }

        return $default;
    }

    /**
     * 获取参数值
     *
     * @param int $position
     * @param null $default
     * @return mixed|null
     */
    protected function getArgument($position = 0, $default = null)
    {
        return isset($this->arguments[$position]) ? $this->arguments[$position] : $default;
    }

}
