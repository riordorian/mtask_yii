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
        '//cdn.datatables.net/1.10.12/css/dataTables.bootstrap4.min.css'
    ];
    public $js = [
        'assets/js/portal.js',
        '//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js',
        '//cdn.datatables.net/1.10.12/js/dataTables.bootstrap4.min.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
