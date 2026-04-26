# CODEBUDDY.md

This file provides guidance to CodeBuddy Code when working with code in this repository.

## Repository purpose

This package (`pjkui/kindeditor`) is a **Yii2 widget wrapper** around the bundled KindEditor JavaScript rich-text editor. The PHP layer is small (three files at the repo root); the bulk of the tree is the upstream KindEditor JS/CSS assets.

## Installation / usage

This is a Composer library, not an application — there is no build, lint, or test suite.

- Install into a Yii2 project: `php composer.phar require pjkui/kindeditor "*"`
- PSR-4 autoload: `pjkui\kindeditor\` → repo root (see `composer.json`)
- If installed by copy (not Composer), `/vendor/composer/autoload_psr4.php` must be patched to add `'pjkui\\kindeditor\\' => array($vendorDir . '/pjkui/kindeditor')`.

Consumers use it in a Yii2 controller + view:

```php
// controller
public function actions() {
    return ['Kupload' => ['class' => 'pjkui\kindeditor\KindEditorAction']];
}

// view
echo \pjkui\kindeditor\KindEditor::widget(['clientOptions' => [...]]);
```

See `README.md` for the full list of `editorType` modes and `clientOptions` examples.

## Architecture

Three PHP classes form the entire server-side surface; everything else is the vendored KindEditor 4.x JS distribution.

### `KindEditor.php` — the widget (`KindEditor extends yii\widgets\InputWidget`)
- `init()` merges user-supplied `clientOptions` over defaults that wire `uploadJson` / `fileManagerJson` URLs to the `Kupload` action via `Url::to(['Kupload', 'action' => ...])`. This **hard-coded route name `Kupload`** is the contract between the widget and the action — the action must be registered under exactly that id in the controller's `actions()`.
- `$editorType` selects one of six render modes: default textarea (full editor), `uploadButton`, `colorpicker`, `file-manager`, `image-dialog`, `file-dialog`. Each mode emits a different HTML fragment (input + button) AND a different inline JS snippet in `registerClientScript()`. When adding a new mode, both the `run()` switch and the `registerClientScript()` switch must be updated together.
- Client JS is injected with `View::POS_READY` via heredoc templates that interpolate `$this->id` and the upload URLs.

### `KindEditorAction.php` — the upload/file-manager endpoint (`extends yii\base\Action`)
- Single action dispatched by the `?action=` query param: `uploadJson` (file upload) or `fileManagerJson` (list files). The widget generates URLs pointing at this same action with different `action` params.
- Disables CSRF in `init()` (`Yii::$app->request->enableCsrfValidation = false`) — required because KindEditor's client does not send Yii's CSRF token. Preserve this when modifying.
- Paths default to `@webroot` / `@web` but every path property (`php_path`, `root_path`, `save_path`, `save_url`, etc.) is overridable from the controller's `actions()` array.
- `$ext_arr` and `$max_size` gate what can be uploaded, keyed by `dir` (image/flash/media/file). Uses bundled `Services_JSON` for output.

### `KindEditorAsset.php` — the asset bundle
- Registers `kindeditor-min.js` + `lang/zh_CN.js` + `themes/default/default.css`.
- `sourcePath` is set to the package directory itself, so Yii's asset manager publishes the whole repo (including `plugins/`, `themes/`, `lang/`) to `web/assets/...`. The JS runtime loads plugins and themes lazily from those published paths.
- To switch default UI language, edit the `lang/zh_CN.js` entry; the available language files live in `lang/` (`en.js`, `zh_CN.js`, `zh_TW.js`, `ko.js`, `ar.js`).

### JS / asset tree (not hand-edited here)
- `kindeditor.js` / `kindeditor-min.js` — core editor (no jQuery dependency variant).
- `kindeditor-all.js` / `kindeditor-all-min.js` — core + all plugins bundled.
- `plugins/<name>/<name>.js` — individual feature plugins (image, table, code, baidumap, …). Loaded on demand by `editor.loadPlugin('<name>', …)`.
- `themes/{default,simple,qq,common}/` — CSS skins. `KindEditorAsset` uses `default`.
- `examples/` — static HTML demos for each feature (not used at runtime; useful reference when debugging client behavior).

## Things to know when editing

- There is no build step — changes to PHP or JS files are live. The minified `*-min.js` files are pre-built upstream artifacts; if you touch `kindeditor.js`, the corresponding `-min.js` will be out of sync unless you reminify.
- The widget assumes the action is routed as `Kupload` on the current controller. If a consumer names it differently, uploads break. Don't change this name without updating both sides.
- README notes the fork fixes Linux-specific bugs vs. the upstream YiiChina package; keep that in mind when diffing against other `pjkui/kindeditor` variants.
