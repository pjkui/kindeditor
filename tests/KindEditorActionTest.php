<?php

namespace pjkui\kindeditor\tests;

use PHPUnit\Framework\TestCase;
use pjkui\kindeditor\KindEditorAction;
use Yii;
use yii\base\Controller;

/**
 * @covers \pjkui\kindeditor\KindEditorAction
 */
class KindEditorActionTest extends TestCase
{
    private function makeAction(array $config = []): KindEditorAction
    {
        $controller = new Controller('test', Yii::$app);

        return new KindEditorAction('Kupload', $controller, $config);
    }

    public function testDefaultExtensionWhitelistCoversAllCategories(): void
    {
        $action = $this->makeAction();

        $this->assertArrayHasKey('image', $action->ext_arr);
        $this->assertArrayHasKey('flash', $action->ext_arr);
        $this->assertArrayHasKey('media', $action->ext_arr);
        $this->assertArrayHasKey('file',  $action->ext_arr);

        $this->assertContains('jpg', $action->ext_arr['image']);
        $this->assertContains('png', $action->ext_arr['image']);
        $this->assertContains('gif', $action->ext_arr['image']);

        // Dangerous extensions must NOT be in the default whitelist.
        $this->assertNotContains('php',  $action->ext_arr['file']);
        $this->assertNotContains('phtml', $action->ext_arr['file']);
        $this->assertNotContains('exe',  $action->ext_arr['file']);
        $this->assertNotContains('sh',   $action->ext_arr['file']);
    }

    public function testDefaultMaxSizeIsOneMegabyte(): void
    {
        $action = $this->makeAction();

        $this->assertSame(1000000, $action->max_size);
    }

    public function testInitResolvesPathsFromYiiAliases(): void
    {
        $action = $this->makeAction();
        $action->init();

        $webroot = Yii::getAlias('@webroot') . '/';
        $web     = Yii::getAlias('@web') . '/';

        $this->assertSame($webroot, $action->php_path);
        $this->assertSame($web,     $action->php_url);
        $this->assertSame($webroot . 'upload/', $action->root_path);
        $this->assertSame($web     . 'upload/', $action->root_url);
        $this->assertSame($action->root_path, $action->save_path);
        $this->assertSame($action->root_url,  $action->save_url);
    }

    public function testInitRespectsExplicitlyConfiguredPaths(): void
    {
        $action = $this->makeAction([
            'php_path'  => '/srv/www/',
            'php_url'   => 'https://cdn.example.com/',
            'root_path' => '/var/attached/',
            'root_url'  => 'https://cdn.example.com/attached/',
            'max_size'  => 5 * 1024 * 1024,
        ]);
        $action->init();

        $this->assertSame('/srv/www/', $action->php_path);
        $this->assertSame('https://cdn.example.com/', $action->php_url);
        $this->assertSame('/var/attached/', $action->root_path);
        $this->assertSame('https://cdn.example.com/attached/', $action->root_url);
        $this->assertSame(5 * 1024 * 1024, $action->max_size);
    }

    public function testInitDisablesCsrfValidation(): void
    {
        Yii::$app->request->enableCsrfValidation = true;

        $action = $this->makeAction();
        $action->init();

        $this->assertFalse(
            Yii::$app->request->enableCsrfValidation,
            'KindEditorAction must disable CSRF because the KindEditor client does not send Yii CSRF tokens.'
        );
    }

    public function testComparatorSortsDirectoriesBeforeFiles(): void
    {
        $action = $this->makeAction();

        $dir  = ['is_dir' => true,  'filesize' => 0,   'filetype' => '',    'filename' => 'zzz'];
        $file = ['is_dir' => false, 'filesize' => 100, 'filetype' => 'jpg', 'filename' => 'aaa'];

        $this->assertSame(-1, $action->cmp_func($dir, $file));
        $this->assertSame(1,  $action->cmp_func($file, $dir));
    }

    public function testComparatorSortsByFilenameAlphabetically(): void
    {
        $action = $this->makeAction();

        $GLOBALS['order'] = 'name';

        $a = ['is_dir' => false, 'filesize' => 100, 'filetype' => 'jpg', 'filename' => 'alpha.jpg'];
        $b = ['is_dir' => false, 'filesize' => 100, 'filetype' => 'jpg', 'filename' => 'beta.jpg'];

        $this->assertLessThan(0,    $action->cmp_func($a, $b));
        $this->assertGreaterThan(0, $action->cmp_func($b, $a));
        $this->assertSame(0,         $action->cmp_func($a, $a));
    }

    public function testComparatorSortsBySizeWhenOrderIsSize(): void
    {
        $action = $this->makeAction();

        $GLOBALS['order'] = 'size';

        $small = ['is_dir' => false, 'filesize' => 10,   'filetype' => 'jpg', 'filename' => 'a'];
        $large = ['is_dir' => false, 'filesize' => 9000, 'filetype' => 'jpg', 'filename' => 'b'];

        $this->assertSame(-1, $action->cmp_func($small, $large));
        $this->assertSame(1,  $action->cmp_func($large, $small));
        $this->assertSame(0,  $action->cmp_func($small, $small));
    }

    public function testComparatorSortsByTypeWhenOrderIsType(): void
    {
        $action = $this->makeAction();

        $GLOBALS['order'] = 'type';

        $jpg = ['is_dir' => false, 'filesize' => 100, 'filetype' => 'jpg', 'filename' => 'a'];
        $png = ['is_dir' => false, 'filesize' => 100, 'filetype' => 'png', 'filename' => 'b'];

        $this->assertLessThan(0,    $action->cmp_func($jpg, $png));
        $this->assertGreaterThan(0, $action->cmp_func($png, $jpg));
    }
}
