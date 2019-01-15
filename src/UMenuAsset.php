<?php
namespace uhi67\umenu;

use yii\web\AssetBundle;

class UMenuAsset extends AssetBundle {
    public $sourcePath = '@vendor/uhi67/umenu/src/assets';
    public $css = [
        'umenu.less',
    ];
    public $js = [
    	'umenu.js',
	];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
	];
	public $publishOptions = [
		'forceCopy' => YII_ENV_DEV, 
	];
}
