<?php

namespace Daijulong\Generator\Commands;

use Daijulong\Generator\Abstracts\Command;
use Daijulong\Generator\Document;
use Daijulong\Generator\Helper;

class Manual extends Command
{

    /**
     * 命令行选项别名
     *
     * @var array
     */
    protected $option_alias = [
        'help' => 'h',
    ];

    /**
     * 运行命令
     *
     * @return mixed
     */
    public function run()
    {
        $manual = 'manual';
        $name = $this->getArgument(0);
        if ($name != '') {
            $manual = 'commands' . DIRECTORY_SEPARATOR . Helper::snakeCase($name);
        }
        Document::show($manual);
    }
}
