<?php

namespace Daijulong\Generator\Commands;

use Daijulong\Generator\Abstracts\Command;
use Daijulong\Generator\Commands\Make\Builder;
use Daijulong\Generator\Helper;

class Make extends Command
{
    /**
     * 命令行选项别名
     *
     * @var array
     */
    protected $option_alias = [
        'group' => 'g',
        'only' => 'o',
    ];

    /**
     * 运行命令
     *
     * @return mixed
     */
    public function run()
    {
        //检查参数：名称
        $name = $this->getArgument(0);
        if (!preg_match("/^[a-zA-Z][a-zA-Z0-9\/_-]*$/", $name)) {
            $this->console->error('名称格式不正确！');
            return;
        }

        //检查组
        $group = $this->getOption('group', 'default');
        $group_config = \Daijulong\Generator\Config::get('groups.' . $group);
        if (!$group_config) {
            $this->console->error('指定了一个未在配置中声明的组：' . $group);
            die(1);
        }

        //检查use配置
        $config_use_module = (isset($group_config['make']) && !empty($group_config['make'])) ? $group_config['make'] : [];
        $only = $this->getOption('only', '');
        $use_modules = !empty($only) ? array_intersect($config_use_module, explode(',', $only)) : $config_use_module;
        if (empty($use_modules)) {
            $this->console->warning('执行完成了，但是似乎并没有生成什么，可能因为：');
            $this->console->line('    1、配置中该组的use字段未设置内容');
            $this->console->line('    2、命令参数中指定生成的模块全部为无效的');
            die(1);
        }

        foreach ($use_modules as $module) {
            $this->build($name, $module, isset($group_config[$module]) ? $group_config[$module] : []);
        }

    }

    /**
     * 构建并生成
     *
     * @param string $name
     * @param string $module
     * @param array $module_config
     */
    protected function build($name, $module, $module_config)
    {
        $default_module_config = [];
        if (isset($module_config['module']) && $module_config['module'] != '' && \Daijulong\Generator\Config::get('modules.' . $module_config['module'])) {
            $default_module_config = (array)\Daijulong\Generator\Config::get('modules.' . $module_config['module']);
        }
        $build = new Builder($this->parseName($name), $module, array_merge($default_module_config, $module_config));
        $build->handle();
    }

    /**
     * 解析名称
     *
     * @param string $origin_name
     * @return array
     */
    protected function parseName($origin_name)
    {
        $names = explode('/', str_replace('\\', '/', $origin_name));
        //名称（排除空间后）
        $name = array_pop($names);
        //名称（小写）
        $name_lower = Helper::snakeCase($name);
        //名称（大写）
        $name_upper = mb_strtoupper(Helper::snakeCase($name));
        //名称（驼峰）
        $name_camel = Helper::camelCase($name);
        //命名空间（类）
        $name_space = empty($names) ? '' : implode('\\', (array)call_user_func_array(function ($name) {
            return Helper::camelCase($name);
        }, $names));
        //存储路径
        $path = implode(DIRECTORY_SEPARATOR, $names);
        //蛇形命名的存储路径
        $path_lower = empty($names) ? '' : implode(DIRECTORY_SEPARATOR, (array)call_user_func_array(function ($name) {
            return Helper::snakeCase($name);
        }, $names));

        return compact('name', 'name_lower', 'name_upper', 'name_camel', 'name_space', 'path', 'path_lower');
    }

}
