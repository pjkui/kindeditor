<?php

namespace pjkui\kindeditor\tests;

use PHPUnit\Framework\TestCase;
use pjkui\kindeditor\KindEditorAsset;

/**
 * @covers \pjkui\kindeditor\KindEditorAsset
 */
class KindEditorAssetTest extends TestCase
{
    public function testBundleDeclaresRequiredJsFiles(): void
    {
        $bundle = new KindEditorAsset();

        $this->assertContains('kindeditor-min.js', $bundle->js);
        $this->assertContains('lang/zh_CN.js', $bundle->js);
    }

    public function testBundleDeclaresDefaultTheme(): void
    {
        $bundle = new KindEditorAsset();

        $this->assertContains('themes/default/default.css', $bundle->css);
    }

    public function testBundleUsesUtf8Charset(): void
    {
        $bundle = new KindEditorAsset();

        $this->assertSame(['charset' => 'utf8'], $bundle->jsOptions);
    }

    public function testInitSetsSourcePathToPackageDirectory(): void
    {
        $bundle = new KindEditorAsset();
        $bundle->init();

        $this->assertNotEmpty($bundle->sourcePath);
        $this->assertDirectoryExists($bundle->sourcePath);

        // The source path must include the bundled KindEditor JS so the asset
        // manager can publish it.
        $this->assertFileExists($bundle->sourcePath . 'kindeditor-min.js');
    }
}
