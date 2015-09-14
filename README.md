KindEditor
===========

### install / 安装
Either run

`
$ php composer.phar require pjkui/kindeditor "*"
`

or add

`
"pjkui/kindeditor": "*"
`

to the `require` section of your `composer.json` file.

或者直接将程序文件放到系统的vendor下面,其实建议用compaser,这个是比较方便和规范的安装方法，如果是拷贝的话，有一个文件需要修改，以保证这个kindeditor类被加载。
这个文件是`/vendor/composer/autoload_psr4.php`.添加一行
`'pjkui\\kindeditor\\'=>array($vendorDir . '/pjkui/kindeditor')`,
### Usage example / 应用方法

####controller / 控制器:  

```
public function actions()
{
    return [
        'Kupload' => [
            'class' => 'pjkui\kindeditor\KindEditorAction',
        ]
    ];
}
```

####view / 视图:  
```

echo \pjkui\kindeditor\KindEditor::widget([]);
```

or / 或者：

```
echo $form->field($model,'colum')->widget('pjkui\kindeditor\KindEditor',[]);
```

or / 或者：
```
<?= $form->field($model, 'content')->widget('pjkui\kindeditor\Kindeditor',['clientOptions'=>['allowFileManager'=>'true','allowUpload'=>'true']]) ?>
```
## configure / 配置相关
##### you can configure `clientOption` and `editorType` to change the kindeditor's preference, the detail configure see the official website[KindEditor website](http://kindeditor.net/doc.php). / 编辑器相关配置，请在`view 中配置，参数为`clientOptions，比如定制菜单，编辑器大小等等，具体参数请查看[KindEditor官网文档](http://kindeditor.net/doc.php)。

######`editorType` configure / `editorType`配置
1. `editor` work as editor，default configure./配置为富文本编辑器，默认配置
 ```
 usage:
 <?= $form->field($model, 'content')->widget('pjkui\kindeditor\Kindeditor',['clientOptions'=>['allowFileManager'=>'true','allowUpload'=>'true']]) ?>
 ```
2. `uploadButton`Kindediotr work as a upload file button ,can upload file/picture to the server automatic /这时候配置kindeditor为上传文件按钮，可以自动上传文件到服务器
```
usage:<?= $form->field($model, 'article_pic')->widget('pjkui\kindeditor\Kindeditor',['clientOptions'=>['allowFileManager'=>'true','allowUpload'=>'true'],'editorType'=>'uploadButton]) ?>
```
3. `colorpicker`kindeditor work as color picker / 配置kindeditor为取色器
```
usage:<?= $form->field($model, 'content')->widget('pjkui\kindeditor\Kindeditor','editorType'=>'colorpicker']) ?>
```
4. `file-manager`kindeditor work as file manager,can view and select the file which uploaded by it . / 配置kindeditor为文件管理器，可以查看和选着其上传的文件。
```
usage:`<?= $form->field($model, 'article_pic')->widget('pjkui\kindeditor\Kindeditor',['clientOptions'=>['allowFileManager'=>'true','allowUpload'=>'true'],'editorType'=>'file-manager']) ?>
```
5. `image-dialog`kindeditor work as image upload dialog. / 配置kindeditor为图片上传对话框。
```
usage:<?= $form->field($model, 'article_pic')->widget('pjkui\kindeditor\Kindeditor',['clientOptions'=>['allowFileManager'=>'true','allowUpload'=>'true'],'editorType'=>'image-dialog']) ?>
```
6. `file-dialog`kindeditor work as file upload dialog. / 配置kindeditor为文件上传对话框。
```
usage:<?= $form->field($model, 'article_pic')->widget('pjkui\kindeditor\Kindeditor',['clientOptions'=>['allowFileManager'=>'true','allowUpload'=>'true'],'editorType'=>'file-dialog']) ?>
```


简单实例:  
```
php
use \pjkui\kindeditor\KindEditor;
echo KindEditor::widget([
    'clientOptions' => [
        //编辑区域大小
        'height' => '500',
        //定制菜单
        'items' => [
        'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste',
        'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
        'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
        'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
        'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
        'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'multiimage',
        'flash', 'media', 'insertfile', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak',
        'anchor', 'link', 'unlink', '|', 'about'
        ]
]);
```
