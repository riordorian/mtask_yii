<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '/assets/mtask/css/style.css',
        '/assets/mtask/css/animate.css',
        '/assets/mtask/font-awesome/css/font-awesome.css',
        '/assets/mtask/css/dataTables.css',
    ];
    public $js = [
        'assets/mtask/js/plugins/metisMenu/jquery.metisMenu.js',
        'assets/mtask/js/plugins/slimscroll/jquery.slimscroll.min.js',
        'assets/mtask/js/plugins/pace/pace.min.js',
        'assets/mtask/js/inspinia.js',
        'assets/mtask/js/portal.js',
        'assets/mtask/js/dataTables.js',
        'assets/mtask/js/dataTables_bootstrap.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
