# KindEditor for Yii2

[![Latest Stable Version](https://img.shields.io/packagist/v/pjkui/kindeditor.svg)](https://packagist.org/packages/pjkui/kindeditor)
[![Total Downloads](https://img.shields.io/packagist/dt/pjkui/kindeditor.svg)](https://packagist.org/packages/pjkui/kindeditor)
[![License](https://img.shields.io/packagist/l/pjkui/kindeditor.svg)](https://github.com/pjkui/kindeditor/blob/master/license.txt)
[![CI](https://github.com/pjkui/kindeditor/actions/workflows/ci.yml/badge.svg)](https://github.com/pjkui/kindeditor/actions/workflows/ci.yml)

A [Yii2](https://www.yiiframework.com/) widget wrapper for the [KindEditor](http://kindeditor.net) WYSIWYG HTML editor, with file upload and file-manager endpoints included.

> **Note:** This fork patches several Linux-specific bugs that the upstream YiiChina package could not be updated with. Please follow the usage instructions in this README — examples elsewhere may contain typos that cannot be corrected there.

---

**Languages:** [English](#english) · [中文](#中文)

---

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Editor Types](#editor-types)
- [Configuration](#configuration)
- [Upload Action Options](#upload-action-options)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)
- [中文文档](#中文)

---

## English

### Features

- Full Yii2 `InputWidget` integration — works with or without an `ActiveForm` model.
- Six editor modes: full rich-text editor, upload button, color picker, file manager, image dialog, file dialog.
- Bundled upload + file-manager action with extension whitelisting and size limits.
- Ships the complete KindEditor 4.x assets (plugins, themes, i18n).

### Requirements

| Component | Version |
|-----------|---------|
| PHP       | >= 5.6 (7.4+ recommended) |
| Yii2      | ^2.0 |

### Installation

Install via [Composer](https://getcomposer.org/):

```bash
composer require pjkui/kindeditor "*"
```

Or add the following to the `require` section of your `composer.json`:

```json
"pjkui/kindeditor": "*"
```

<details>
<summary>Manual installation (not recommended)</summary>

If you copy the package into `vendor/` without Composer, add the following line to `vendor/composer/autoload_psr4.php` so the classes are autoloaded:

```php
'pjkui\\kindeditor\\' => array($vendorDir . '/pjkui/kindeditor'),
```
</details>

### Quick Start

**1. Register the upload action in your controller:**

```php
public function actions()
{
    return [
        'Kupload' => [
            'class' => 'pjkui\kindeditor\KindEditorAction',
        ],
    ];
}
```

> ⚠️ The action id **must** be `Kupload` — the widget hard-codes this route when generating `uploadJson` / `fileManagerJson` URLs.

**2. Render the widget in your view:**

```php
use pjkui\kindeditor\KindEditor;

// Standalone
echo KindEditor::widget([]);

// Bound to a model attribute
echo $form->field($model, 'content')->widget(KindEditor::class, [
    'clientOptions' => [
        'allowFileManager' => 'true',
        'allowUpload'      => 'true',
    ],
]);
```

### Editor Types

Set `editorType` to switch between rendering modes:

| `editorType`   | Description                                               |
|----------------|-----------------------------------------------------------|
| *(default)*    | Full rich-text editor on a `<textarea>`                   |
| `uploadButton` | Text input + "Upload" button for single-file upload       |
| `colorpicker`  | Text input + button that opens a color picker             |
| `file-manager` | Text input + button that opens the server file browser    |
| `image-dialog` | Text input + button that opens the image upload dialog    |
| `file-dialog`  | Text input + button that opens the generic file dialog    |

Example — image upload dialog bound to an `article_pic` attribute:

```php
echo $form->field($model, 'article_pic')->widget(KindEditor::class, [
    'clientOptions' => [
        'allowFileManager' => 'true',
        'allowUpload'      => 'true',
    ],
    'editorType' => 'image-dialog',
]);
```

### Configuration

All editor options are passed through the `clientOptions` array. See the [official KindEditor documentation](http://kindeditor.net/doc.php) for the full list.

Full example with a custom toolbar:

```php
use pjkui\kindeditor\KindEditor;

echo KindEditor::widget([
    'id' => 'thisID', // id of the generated textarea
    'clientOptions' => [
        'height' => '500',
        'items'  => [
            'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code',
            'cut', 'copy', 'paste', 'plainpaste', 'wordpaste', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyfull',
            'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent',
            'subscript', 'superscript', 'clearhtml', 'quickformat', 'selectall', '|',
            'fullscreen', '/',
            'formatblock', 'fontname', 'fontsize', '|',
            'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', 'strikethrough',
            'lineheight', 'removeformat', '|',
            'image', 'multiimage', 'flash', 'media', 'insertfile', 'table', 'hr',
            'emoticons', 'baidumap', 'pagebreak', 'anchor', 'link', 'unlink', '|', 'about',
        ],
    ],
]);
```

### Upload Action Options

`KindEditorAction` accepts the following properties when registered in `actions()`:

| Property     | Default                  | Description                                    |
|--------------|--------------------------|------------------------------------------------|
| `php_path`   | `@webroot/`              | Server filesystem path used as the PHP root    |
| `php_url`    | `@web/`                  | URL that maps to `php_path`                    |
| `root_path`  | `{php_path}upload/`      | Upload root on the filesystem                  |
| `root_url`   | `{php_url}upload/`       | Upload root URL                                |
| `save_path`  | `{root_path}`            | Where new files are written                    |
| `save_url`   | `{root_url}`             | URL prefix for new files                       |
| `max_size`   | `1000000` (bytes)        | Maximum upload size                            |
| `ext_arr`    | see source               | Allowed extensions keyed by `image`/`flash`/`media`/`file` |

Example — override the upload directory:

```php
public function actions()
{
    return [
        'Kupload' => [
            'class'     => 'pjkui\kindeditor\KindEditorAction',
            'root_path' => Yii::getAlias('@webroot') . '/attached/',
            'root_url'  => Yii::getAlias('@web')    . '/attached/',
            'max_size'  => 5 * 1024 * 1024, // 5 MB
        ],
    ];
}
```

### Testing

This repository uses [PHPUnit](https://phpunit.de/) for unit tests.

```bash
composer install
composer test
# or run directly:
vendor/bin/phpunit
```

### Contributing

Bug reports and pull requests are welcome on [GitHub](https://github.com/pjkui/kindeditor). Please make sure `composer test` passes before opening a PR.

### License

Released under the [MIT License](license.txt). KindEditor itself is distributed under its original [LGPL license](http://kindeditor.net/license.php).

---

## 中文

Yii2 专用的 KindEditor 富文本编辑器封装，内置上传与文件管理 Action。

> **说明：** 本分支修复了一些 Linux 环境下的 Bug，由于 Yii China 无法更新上游，请以本 README 为准。

### 环境要求

| 组件  | 版本 |
|-------|------|
| PHP   | >= 5.6（推荐 7.4+） |
| Yii2  | ^2.0 |

### 安装

推荐使用 Composer：

```bash
composer require pjkui/kindeditor "*"
```

或将以下内容加入 `composer.json` 的 `require` 部分：

```json
"pjkui/kindeditor": "*"
```

<details>
<summary>手动安装（不推荐）</summary>

如果直接把包放入 `vendor/`，需在 `vendor/composer/autoload_psr4.php` 中添加：

```php
'pjkui\\kindeditor\\' => array($vendorDir . '/pjkui/kindeditor'),
```
</details>

### 快速开始

**1. 在控制器中注册上传 Action：**

```php
public function actions()
{
    return [
        'Kupload' => [
            'class' => 'pjkui\kindeditor\KindEditorAction',
        ],
    ];
}
```

> ⚠️ Action 名字**必须**是 `Kupload`，因为 Widget 在生成上传 URL 时硬编码了这个名字。

**2. 在视图中渲染：**

```php
use pjkui\kindeditor\KindEditor;

echo KindEditor::widget([]);

// 或绑定到模型字段
echo $form->field($model, 'content')->widget(KindEditor::class, [
    'clientOptions' => [
        'allowFileManager' => 'true',
        'allowUpload'      => 'true',
    ],
]);
```

### `editorType` 配置

| `editorType`   | 说明                 |
|----------------|----------------------|
| *(默认)*       | 富文本编辑器         |
| `uploadButton` | 自定义文件上传按钮   |
| `colorpicker`  | 取色器               |
| `file-manager` | 文件管理器           |
| `image-dialog` | 图片上传对话框       |
| `file-dialog`  | 文件上传对话框       |

示例 —— 图片上传对话框：

```php
echo $form->field($model, 'article_pic')->widget(KindEditor::class, [
    'clientOptions' => [
        'allowFileManager' => 'true',
        'allowUpload'      => 'true',
    ],
    'editorType' => 'image-dialog',
]);
```

### 完整示例

```php
use pjkui\kindeditor\KindEditor;

echo KindEditor::widget([
    'id' => 'thisID',
    'clientOptions' => [
        'height' => '500',
        'items'  => [
            'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code',
            'cut', 'copy', 'paste', 'plainpaste', 'wordpaste', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyfull',
            'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent',
            'subscript', 'superscript', 'clearhtml', 'quickformat', 'selectall', '|',
            'fullscreen', '/',
            'formatblock', 'fontname', 'fontsize', '|',
            'forecolor', 'hilitecolor', 'bold', 'italic', 'underline', 'strikethrough',
            'lineheight', 'removeformat', '|',
            'image', 'multiimage', 'flash', 'media', 'insertfile', 'table', 'hr',
            'emoticons', 'baidumap', 'pagebreak', 'anchor', 'link', 'unlink', '|', 'about',
        ],
    ],
]);
```

更多 `clientOptions` 参数请参考 [KindEditor 官方文档](http://kindeditor.net/doc.php)。

### 测试

```bash
composer install
composer test
```

### 许可证

MIT License，详见 [license.txt](license.txt)。
