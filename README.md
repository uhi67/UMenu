# UMenu
Universal menu for Yii

# Installation

The preferred way to install this extension is through composer.

To install, either run

    composer require uhi67/umenu "1.0.*" 

or add

    "uhi67/umenu" : "1.0.*"

or clone form github

    git clone https://github.com/uhi67/umenu
    
# Usage

In controller action, use

    return [
        'model' => $this->findModel($id),
        'mode' => $editmode ? 'edit' : 'view',
		'titlemenu' => [
			[
				'visible' => $editmode, 
				'enabled' => Yii::$app->user->can('/contract/update'), 
				'title' => Yii::t('app', 'Save details data'), 
				'icon' => 'glyphicon-ok', 
				'action' => "javascript:$(this).closest('div.block').find('form').get(0).submit();",
			],
			[
				'enabled' => Yii::$app->user->can('/contract/update'), 
				//'caption'=>'Edit', 
				'title' => Yii::t('app', 'Edit contract details'), 
				'icon' => $editmode ? 'glyphicon-remove' : 'glyphicon-pencil', 
				'action' => $editmode ? '/contract/view/'.$id : '/contract/edit/'.$id,
			],
			[
				'enabled' => Yii::$app->user->can('/contract/delete'), 
				//'caption'=>'Delete', 
				'title' => Yii::t('app', 'Delete contract'), 
				'icon'=>'glyphicon-trash', 
				'action'=>'/contract/delete/'.$id, 
				'confirm' => Yii::t('app', 'This will delete the contract. Ary you sure?')
			],
			[
				'title' => 'Lista',
				'icon' => 'glyphicon-list',
				'action' => '/contract',
			],
		],
	];

In view use

	<?= UMenu::showMenu($titlemenu); ?>

See UMenu::showMenu for detailed menu properties.
