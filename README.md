# Generator

适用于PHP项目的代码生成工具

## 环境要求

- php: >= 5.4

## 安装

通过 Composer

```php
$  composer require daijulong/generator
```

composer.json

```php
"daijulong/generator": "^1.0"
```

重要：安装好后须进行资源发布

```php
./vendor/bin/generator publish
```

> 执行发布命令后将在当前工作目录生成 generator ， generator.json ，resources/stubs 等文件资源

## 使用

```php
php generator 
```

将显示使用手册

### 命令手册

显示 ```make``` 命令的使用手册

```php
php generator make -h
```

> 所有支持的命令，加上 ```-h``` 选项即显示该命令的使用手册

### 生成文件示例

> 以下内容以默认发布的资源（配置）为依据演示，命令相关说明请依照命令行手册

示例：生成的资源名称为： Admin/UserAuth

```php
php generator make Admin/UserAuth
```

将会依次生成以下文件：

1. app/Http/Controllers/Admin/UserAuthController.php

1. app/Models/UserAuth.php

1. resources/views/admin/user_auth/index.php

1. resources/views/admin/user_auth/create.php

1. resources/views/admin/user_auth/edit.php

1. resources/views/admin/user_auth/show.php

> 上述文件生成规则皆由配置文件 ```generator.json``` 中定义的规则来决定

> 资源名称建议以类名为准，遵循 psr-4 规范

## 配置

配置文件为 ```generator.json``` ，生成规则在此定义

### resources-path

定义资源目录名称，默认为 resources ，内含模板目录 stubs 

### modules

定义 ```make``` 命令中所支持的模块的默认设置，如 controller 模块的定义：

```
{
  ...
  "modules": {
    ...
    "contorller": {
      "confirm": false,
      "confirm-default": "yes",
      "override": "ask",
      "stub": "controller",
      "save-path": "app/Http/Controllers/{space}",
      "save-file": "{class}.php",
      "class-prefix": "",
      "class-postfix": "Controller",
      "namespace": "App\\Http\\Controllers",
      "ask": {}
    },
    ...
  },
  ...
}
```

模块字段说明：

confirm         ： true 或 false ，生成该模块文件前是否需要确认

confirm-default ： 确认默认值，yes 或 no

override        ： 如果文件已经存在，是否覆盖，默认 ask 为询问，也可指定为 true 或 false 来决定是否直接覆盖

stub            ： 模板文件，在 resources/stubs 目录下的文件，并以 “.stub” 为后缀

save-path       ： 生成文件保存路径，支持占位符，建议仅声明路径部分，包括资源名中可能包含的路径部分

save-file       ： 生成文件的文件名（包括后缀），支持占位符，建议仅声明文件名本身

class-prefix    ： 资源名作为类时，类名的前缀

class-postfix   ： 资源名作为类时，类名的后缀

namespace       ： 资源名作为类时，命名空间前缀

ask             ： 其他附加数据，通过询问方式获取值


ask 示例：

```
{
  "table_name" : "表名是什么？",     直接提问
  "is_man" : {
    'question' => '你是帅锅？',      问题   
    'type' => 'confirm',            答案类型，confirm 为确认型，回答 yes 或 no ，其他为文字型
    'default' => '',                默认答案
    'must' => false,                是否必须回答
  },
}
```

> ask 所得数据可以在模板中以占位符替代

### groups

对于不同的组合要求，可以定义不同的组，并在 ```make``` 命令中指定要适用的组配置，如使用 ```default``` 组的配置 ：

```php
php generator make Admin/UserAuth -g=default
```

示例配置：

```
{
  ...
  "groups": {
    "default": {
      "make": [
        "controller",
        "model",
        "view-index",
        "view-create",
        "view-edit",
        "view-show"
      ],
      "controller": {
        "module": "controller"
      },
      "model": {
        "module": "model"
      },
      "view-index": {
        "module": "view",
        "stub": "views/index"
      },
      "view-create": {
        "module": "view",
        "stub": "views/create"
      },
      "view-edit": {
        "module": "view",
        "stub": "views/edit"
      },
      "view-show": {
        "module": "view",
        "stub": "views/show"
      }
    }
  },
  ...
}
```

> 每个组中必须有 ```make``` 选项，此定义了 ```make``` 命令所要生成的模块
> 并且每个模块都需要在后续进行声明配置
> 每个模块的配置与 ```modules``` 中相关内容完全相同，如果该模块仅是对 ```modules``` 中某模块的配置进行微调，则可以以 ```module``` 选项指定默认模块配置并对需要修改的选项进行配置

## 占位符

在前述内容中提到的占位符，在此集中说明

> 与资源名称有关的，以 Admin/UserAuth 为例, 模块配置以默认组的 controller 模块为例

### 配置中的占位符

#### save-path 和 save-file

```
{name}   ： 原始资源名称               UserAuth

{NAME}   ： 大写的资源名称              USER_AUTH

{_name_} ： 小写的资源名称              user_auth

{space}  ： 资源名称中路径部分          Admin

{_space_}： 小写资源名称中路径部分      admin

{class}  ： 资源类名，注意前后缀        UserAuthController

{stub}   ： 模板文件名，不含路径和后缀   controller
```

时间相关：{Y} {y} {m} {d} {H} {i} {s}，对应 ```date()``` 函数中的格式

### 模板中的占位符

```
DummyNamespace     ： 命名空间            Admin
DummyFullNamespace ： 完整的命名空间       App\Http\Controllers\Admin
DummyClass         ： 类名                UserAuthController
DummyName          ： 原始资源名称        UserAuth
DummyCamelName     ： 驼峰命名的资源名称   UserAuth
DummyLowerName     ： 小写的资源名称      user_auth
DummyUpperName     ： 大写的资源名称      USER_AUTH
```

另外，还有前述 ask 配置的问题答案，可以用 ```Dummy-问题名``` 来表示，如 ```Dummy-table_name``` ，```Dummy-is_man```

# License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.