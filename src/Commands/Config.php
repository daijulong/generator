<?php

namespace Daijulong\Generator\Commands;

use Daijulong\Generator\Abstracts\Command;
use Daijulong\Generator\Helper;

class Config extends Command
{

    /**
     * 运行命令
     */
    public function run()
    {
        $node = $this->getArgument(0);
        $config = \Daijulong\Generator\Config::get($node);
        $this->show($config);
    }

    /**
     * 显示配置内容
     *
     * 仅显示两层，再多层级则以 [...] 显示
     *
     * @param array $config
     * @throws \ReflectionException
     */
    protected function show($config)
    {
        if (is_string($config)) {
            $this->console->line($config);
            $this->console->blankLine();
        } elseif (is_array($config)) {
            if (empty($config)) {
                $this->console->line('无内容！');
            } else {
                $indent = '    ';
                $left_length = Helper::getMaxLength(array_keys($config));
                foreach ($config as $key => $item) {
                    $output = $this->console->colored(str_pad($key, $left_length + 4, ' ', STR_PAD_RIGHT), 'green');
                    if (is_string($item)) {
                        $output .= $this->console->colored($item);
                    } elseif (is_bool($item)) {
                        $output .= $this->console->colored($item ? 'true' : 'false');
                    } elseif (is_array($item)) {
                        if (!empty($item)) {
                            $sub_left_length = Helper::getMaxLength(array_keys($item));
                            $output .= PHP_EOL;
                            foreach ($item as $k => $d) {
                                $output .= $this->console->colored($indent . str_pad($k, $sub_left_length + 4, ' ', STR_PAD_RIGHT), 'yellow');
                                $output .= $this->console->colored(!is_array($d) ? $d : '[...]') . PHP_EOL;
                            }
                        }
                    } else {
                        $output .= $this->console->colored(strval($item));
                    }
                    echo $output;
                    $this->console->blankLine();
                }
            }
        }
    }

}
