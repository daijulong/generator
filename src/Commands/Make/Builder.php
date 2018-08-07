<?php

namespace Daijulong\Generator\Commands\Make;

use Daijulong\Console\Console;
use Daijulong\Generator\Config;
use Daijulong\Generator\Helper;

class Builder
{

    /**
     * 名称
     *
     * @var array
     */
    protected $names;

    /**
     * 模块
     *
     * @var string
     */
    protected $module;

    /**
     * 模块默认配置
     *
     * @var array
     */
    protected $config = [
        'confirm' => true, //是否需要确认再生成
        'confirm-default' => 'yes', //确认默认值
        'override' => false,//文件已存在时是否直接覆盖
        'stub' => '', //模板
        'save-path' => '', //保存路径
        'save-file' => '',//保存文件名
        'namespace' => '',//命名空间
        'class-prefix' => '',
        'class-postfix' => '',
        'ask' => [],//附加信息，通过询问取得
    ];

    /**
     * @var Console
     */
    protected $console;

    /**
     * 扩展数据，来自于ask
     *
     * @var array
     */
    protected $extra = [];

    public function __construct($parsed_names, $module, $config = [])
    {
        $this->names = $parsed_names;
        $this->module = $module;
        $this->config = array_merge($this->config, $config);
        $this->console = Console::instance();
    }

    public function handle()
    {
        //确认是否生成
        if ($this->config['confirm'] && !$this->console->yesOrNo('是否生成' . $this->module . '？', $this->config['confirm-default'])) {
            return;
        }
        //开始生成
        $stub = $this->config['stub'] ? trim(Config::get('resources-path', 'resources'), '/\\') . '/stubs/' . $this->config['stub'] . '.stub' : '';
        if (!$stub || !is_file($stub)) {
            $this->console->error('模板文件不存在，请检查 “ stub ” 配置');
            return;
        }

        //ask 扩展数据，需要向使用者询问
        $this->ask();
        //构建参数
        $params = $this->buildParams();

        //文件内容
        $content = str_replace(array_keys($params['stub_replacements']), array_values($params['stub_replacements']), file_get_contents($stub));

        //检查并创建目录
        if (!is_dir($params['save_path'])) {
            if (!mkdir($params['save_path'], 0777, true)) {
                $this->console->error('生成' . $this->module . '失败：无法创建目录：' . $params['save_path']);
                return;
            }
        }
        //完整的文件名
        $file_name = rtrim($params['save_path'], '/\\') . DIRECTORY_SEPARATOR . ltrim($params['save_file'], '/\\');

        //如文件已存在是否覆盖
        if (file_exists($file_name) && $this->config['override'] !== true) {
            if ($this->config['override'] === false || !$this->console->yesOrNo('文件 ' . $file_name . ' 已经存在，是否覆盖？')) {
                $this->console->line('放弃生成 ' . $this->module . ' ，文件已经存在：' . $file_name);
                return;
            }
        }

        //写入文件
        $write = file_put_contents($file_name, $content);
        if ($write === false) {
            $this->console->error('生成' . $this->module . '失败！无法写入文件：' . $file_name);
            return;
        }
        $this->console->success('生成' . $this->module . '成功！文件：' . $file_name);
    }

    /**
     * 询问
     */
    protected function ask()
    {
        if (!is_array($this->config['ask']) || empty($this->config['ask'])) {
            return;
        }
        foreach ($this->config['ask'] as $key => $params) {
            if (is_string($params)) {
                $this->extra[$key] = $this->console->ask($params);
            } elseif (is_array($params)) {
                $_ask_default_params = [
                    'question' => '',
                    'type' => 'confirm',
                    'default' => '',
                    'must' => false,
                ];
                $_ask_params = array_merge($_ask_default_params, $params);
                $this->extra[$key] = $_ask_params['type'] == 'confirm' ?
                    $this->console->yesOrNo($_ask_params['question'], $_ask_params['default'])
                    :
                    $this->console->ask($_ask_params['question'], $_ask_params['default'], $_ask_params['must']);
            }
        }
    }

    /**
     * 构建参数
     *
     * @return array
     */
    protected function buildParams()
    {
        //'name', 'name_lower', 'name_upper', 'name_camel', 'name_pascal', 'name_space', 'path', 'path_lower');
        //命名空间
        $namespace = trim($this->config['namespace'], '\\');
        //完整的命名空间
        $namespace_full = trim($this->config['namespace'], '\\') . '\\' . $this->names['name_space'];
        //类名
        $this->names['name_class'] = Helper::camelCase($this->config['class-prefix']) . $this->names['name_camel'] . Helper::camelCase($this->config['class-postfix']);

        //保存目录及文件名
        $time_params = explode(' ', date('{Y} {y} {m} {d} {H} {i} {s}'));
        $stubs = explode('/', $this->config['stub']);
        $save_replacements = array_merge([
            '\\' => DIRECTORY_SEPARATOR,
            '/' => DIRECTORY_SEPARATOR,
            '{name}' => $this->names['name'],
            '{NAME}' => $this->names['name_upper'],
            '{_name_}' => $this->names['name_lower'],
            '{space}/' => $this->names['path'] == '' ? '' : $this->names['path'] . DIRECTORY_SEPARATOR,
            '{space}' => $this->names['path'],
            '{_space_}/' => $this->names['path_lower'] == '' ? '' : $this->names['path_lower'] . DIRECTORY_SEPARATOR,
            '{_space_}' => $this->names['path_lower'],
            '{class}' => $this->names['name_class'],
            '{stub}' => end($stubs),
        ], $time_params);
        $save_replacement_keys = array_keys($save_replacements);
        $save_replacement_values = array_values($save_replacements);
        //保存目录
        $save_path = str_replace($save_replacement_keys, $save_replacement_values, trim($this->config['save-path'], '/\\'));
        //保存文件名
        $save_file = str_replace($save_replacement_keys, $save_replacement_values, trim($this->config['save-file'], '/\\'));

        //模板中可替换点位符
        $stub_replacements = [
            'DummyNamespace' => $namespace,
            'DummyFullNamespace' => $namespace_full,
            'DummyClass' => $this->names['name_class'],
            'DummyName' => $this->names['name'],
            'DummyCamelName' => $this->names['name_camel'],
            'DummyLowerName' => $this->names['name_lower'],
            'DummyUpperName' => $this->names['name_upper'],
        ];
        if (!empty($this->extra)) {
            foreach ($this->extra as $key => $value) {
                $stub_replacements['Dummy-' . $key] = $value;
            }
        }

        return compact('save_path', 'save_file', 'stub_replacements');
    }
}
