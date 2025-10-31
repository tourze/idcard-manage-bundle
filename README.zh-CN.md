# idcard-manage-bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/idcard-manage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/idcard-manage-bundle)
[![PHP Version Require](https://img.shields.io/packagist/dependency-v/tourze/idcard-manage-bundle/php?style=flat-square)](https://packagist.org/packages/tourze/idcard-manage-bundle)
[![License](https://img.shields.io/packagist/l/tourze/idcard-manage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/idcard-manage-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/idcard-manage-bundle/ci.yml?branch=master&style=flat-square)](https://github.com/tourze/idcard-manage-bundle/actions)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/idcard-manage-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/idcard-manage-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/idcard-manage-bundle?style=flat-square)](https://codecov.io/gh/tourze/idcard-manage-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/idcard-manage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/idcard-manage-bundle)

中国身份证管理和验证的 Symfony Bundle。

## 功能特性

- ✅ 中国身份证格式验证
- ✅ 生日提取支持自定义分隔符
- ✅ 性别检测使用枚举支持（GB/T 2261 标准）
- ✅ 完整的 Symfony 框架集成
- ✅ 完整的测试覆盖
- ⚠️ 二元素验证（外部 API 集成占位符）

## 安装

```bash
composer require tourze/idcard-manage-bundle
```

## 快速开始

```php
<?php

use Tourze\IdcardManageBundle\Service\IdcardService;
use Tourze\GBT2261\Gender;

class YourController
{
    public function __construct(
        private IdcardService $idcardService
    ) {}

    public function validateIdcard(string $idNumber): void
    {
        // 验证身份证号码
        if ($this->idcardService->isValid($idNumber)) {
            echo "有效的身份证号码";
            
            // 获取生日
            $birthday = $this->idcardService->getBirthday($idNumber);
            if ($birthday !== false) {
                echo "生日: " . $birthday;
            }
            
            // 获取性别
            $gender = $this->idcardService->getGender($idNumber);
            match ($gender) {
                Gender::MAN => echo "男",
                Gender::WOMAN => echo "女",
                default => echo "未知性别",
            };
        } else {
            echo "无效的身份证号码";
        }
    }
}
```

## 配置

在 `config/bundles.php` 中添加 Bundle：

```php
<?php

return [
    // ... 其他 bundles
    Tourze\IdcardManageBundle\IdcardManageBundle::class => ['all' => true],
];
```

## API 文档

### 服务方法

#### `isValid(string $number): bool`
验证身份证号码是否符合基础格式规则。

#### `getBirthday(string $number, string $sep = '-'): string|false`
从身份证号码中提取生日。
- `$number`: 身份证号码
- `$sep`: 日期分隔符（默认：'-'）
- 返回值: 生日字符串或无效时返回 `false`

#### `getGender(string $number): Gender`
从身份证号码中提取性别。
- 返回值: `Gender` 枚举值（MAN、WOMAN、UNKNOWN）

#### `twoElementVerify(string $certName, string $certNo): bool`
使用外部 API 验证身份证号码与持有人姓名。
- 目前返回 `false`，需要第三方 API 集成

## 测试

```bash
# 运行测试
./vendor/bin/phpunit packages/idcard-manage-bundle/tests

# 运行静态分析
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/idcard-manage-bundle
```

## 贡献

详情请参阅 [CONTRIBUTING.md](CONTRIBUTING.md)。

## 依赖项

- PHP 8.1+
- Symfony 6.4+
- [ionepub/idcard](https://github.com/ionepub/idcard) - 核心身份证验证库
- [tourze/gb-t-2261](../gb-t-2261) - 性别枚举定义

## 许可证

MIT 许可证。详情请参阅 [License File](LICENSE)。