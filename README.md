# UMenu
Universal menu for Yii
With footer form support in gridview

# Installation

The preferred way to install this extension is through composer.

To install, either run

    composer require uhi67/umenu "1.0.*" 

or add

    "uhi67/umenu" : "1.0.*"

or clone form github

    git clone https://github.com/uhi67/umenu
    
# Usage

## Title menu

In controller action, use

    return [
        'model' => $this->findModel($id),
        'mode' => $editmode ? 'edit' : 'view',
		'titleMenuItems' => [
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

In your view use

	uhi67\umenu\UMenuAsset::register($this); // or put this into the global layout 
	UMenu::showMenu($titleMenuItems, 'title-menu');

See UMenu::showMenu for detailed menu properties.

## Context menu and footer form functions in GridView

Context menu is a menu associated to a gridview or any similar list object.
Context menu may cointain group items, which are visible only if one or more ites are selected in the associated gridview.
GridView association is automatic if you put the menu in the same form with gridView.

Footer form is a hidden form in the footer row of a gridView object.
A context menu button shows the form. The form contains a close button which hides it again.
Actions with hidden footer-form do not send footer-form fields (and nor validation is performed as well). 

In your controller class use

		// action on selection
		$selection = Yii::$app->request->post('selection');		

		// list action
		return [
			...
			'$contextMenuItems' => [
				[
					'enabled' => $can_grant, 
					'caption' => Yii::t('app', 'Add'),
					'icon' => 'glyphicon-plus', 
					'class' => 'footer-form-show', 		// no action needed: button will show the footer form
					'title' => '...',
				],
				[
					'enabled'=>$can_grant, 
					'caption' => Yii::t('app', 'Delete'), 
					'icon'=>'glyphicon-trash',
					'data' => ['action'=>'...'], // Action will get keys of the selected rows (see above)
					'group'=>1, 	// Indicates this button is visible only and operates on selected rows. Uses data-action to send the form to
					'title' => '...',
				],
			],
			'footerForm' => new RoleForm(['userid'=>$this->itemid]),	// Model of footer form data
		];

In your view use

	// Wrap into form (even if footer form is not used: use for row selection)
	$form = ActiveForm::begin([
		...
	    'layout' => 'inline',
	    ...
	]);
	
	// Indicate using of context-menu and/or footer-form in class 
	 <?= GridView::widget([ 
		'options' => ['class'=>'grid context-menu footer-form'],
		'showFooter' => true,
		'footerRowOptions' => ['class'=>'footer-form'],	// must be hidden first
		...
		'columns' => [
			// First column
			[
				'class' => 'yii\grid\CheckboxColumn',
				'footer' => Html::button(Html::tag('span', '', ['class' => 'glyphicon glyphicon-remove footer-form-close'])),
				'visible'=>...,	// If user has permission on any group action
			],
			// Other columns
			[
				...
				'footer'=>$form->field($roleForm, ...) ... 
				
			]
			// Last column
			[
				...
				'footer' => Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-primary']),
			],
		],
		'showOnEmpty' => true,
		'emptyText' => '...',
	]);
	?>
	<?= Html::activeHiddenInput($roleForm, 'userid') ?>
	<?= \uhi67\umenu\UMenu::showMenu($contextMenuItems, 'context-menu'); ?>
	<?php ActiveForm::end() ?>
