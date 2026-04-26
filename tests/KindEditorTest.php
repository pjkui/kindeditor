<?php

namespace pjkui\kindeditor\tests;

use PHPUnit\Framework\TestCase;
use pjkui\kindeditor\KindEditor;
use Yii;
use yii\base\Controller;
use yii\base\Module;

/**
 * @covers \pjkui\kindeditor\KindEditor
 */
class KindEditorTest extends TestCase
{
    /**
     * The widget calls Url::to(['Kupload', ...]) during init(), which requires
     * an active controller on the application. Install one before every test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $module = new Module('test', Yii::$app);
        Yii::$app->controller = new Controller('site', $module);
    }

    protected function tearDown(): void
    {
        Yii::$app->controller = null;
        parent::tearDown();
    }

    public function testInitMergesUserOptionsOverDefaults(): void
    {
        $widget = new KindEditor([
            'id' => 'editor-1',
            'clientOptions' => [
                'height' => '600',
                'items'  => ['bold', 'italic'],
            ],
        ]);
        $widget->init();

        // User-supplied keys must win.
        $this->assertSame('600', $widget->clientOptions['height']);
        $this->assertSame(['bold', 'italic'], $widget->clientOptions['items']);

        // Defaults should still be present when not overridden.
        $this->assertSame('100%', $widget->clientOptions['width']);
        $this->assertArrayHasKey('fileManagerJson', $widget->clientOptions);
        $this->assertArrayHasKey('uploadJson',      $widget->clientOptions);
    }

    public function testInitWiresUploadUrlsToKuploadAction(): void
    {
        $widget = new KindEditor(['id' => 'editor-2']);
        $widget->init();

        $this->assertStringContainsString('Kupload', $widget->clientOptions['uploadJson']);
        $this->assertStringContainsString('action=uploadJson', $widget->clientOptions['uploadJson']);

        $this->assertStringContainsString('Kupload', $widget->clientOptions['fileManagerJson']);
        $this->assertStringContainsString('action=fileManagerJson', $widget->clientOptions['fileManagerJson']);
    }

    public function testRunRendersTextareaByDefault(): void
    {
        $widget = new KindEditor(['id' => 'content']);
        $widget->init();

        $html = $widget->run();

        $this->assertStringContainsString('<textarea', $html);
        $this->assertStringContainsString('id="content"', $html);
    }

    /**
     * @dataProvider editorTypeButtonProvider
     */
    public function testRunRendersButtonForNonDefaultEditorTypes(string $editorType, string $expectedButtonId): void
    {
        $widget = new KindEditor([
            'id'         => 'field',
            'editorType' => $editorType,
        ]);
        $widget->init();

        $html = $widget->run();

        $this->assertStringContainsString('<input type="text"', $html);
        $this->assertStringContainsString('id="' . $expectedButtonId . '"', $html);
    }

    public function editorTypeButtonProvider(): array
    {
        return [
            'upload button' => ['uploadButton', 'uploadButton'],
            'color picker'  => ['colorpicker',  'colorpicker'],
            'file manager'  => ['file-manager', 'filemanager'],
            'image dialog'  => ['image-dialog', 'imageBtn'],
            'file dialog'   => ['file-dialog',  'insertfile'],
        ];
    }

    public function testUnknownEditorTypeFallsBackToTextarea(): void
    {
        $widget = new KindEditor([
            'id'         => 'unknown',
            'editorType' => 'this-mode-does-not-exist',
        ]);
        $widget->init();

        $html = $widget->run();

        $this->assertStringContainsString('<textarea', $html);
    }
}
