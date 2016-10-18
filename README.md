# JGService-For-Laravel5
交管服务平台信息查询接口封装，包括违章信息、驾照查分。

---

##使用注意
>1. 本分支为Laravel5扩展包
>2. 需要开启 php-curl 扩展
>3. 验证码自动识别接口来自[易源数据-图形验证码识别](https://www.showapi.com/api/lookPoint/184)

---

## 安装

####步骤1. composer
1. 执行 
```php 
composer require steve-liuxu/jg-service-for-laravel ~0.8
```

2. 执行 `composer update` 或者 `composer install` 引入开发包

####步骤2. laravel5安装
找到 `config/app.php` 配置文件中，key为 `providers` 的数组，在数组中添加服务提供者。

```php
    'providers' => [
        // ...
        SteveLiuxu\JGService\JGServiceProvider::class,,
    ]
```

运行 `php artisan vendor:publish` 命令，发布配置文件到你的项目中。


#### 说明
  配置文件 `config/JGService-showapi.php` 为验证码识别配置信息文件，接口申请地址为[易源数据-图形验证码识别](https://www.showapi.com/api/lookPoint/184)

---

##开源协议
遵循[MIT](https://github.com/steve-liuxu/JGService/blob/master/LICENSE)开源协议。