<?php

namespace uhi67\umenu;

use Yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * UMenu -- universal menu with features:
 *		- embedded input fields
 * 		- context menu
 *
 * ## Structure of *items* property:
 * ### menu item properties:
 * - caption: caption text os item (after icon is exists)
 * - icon: icon before or instead of caption (image filename or glyphicon-* or fa-*)
 * - title: html title attribute
 * - class: html class of __a__ or __span__. default is none.
 * - action: url for <a> or javascript:function for <span>
 * - enabled: if false, button is not clickable and gray. html class will contain "disabled". Default is true (enabled).
 * - visible: boolean, default true
 * - group: if not empty, button is visible only when selection is present in connected grid. The button will submit the form, and send this value.
 * - display: input|button|normal(default)
 * - name: name of embedded input field or submit button
 * - value: value of embedded input field or submit button
 * - confirm: if given, the text of confirmation before performing action
 * - data: array of data-* items' values. data-action may be set as url for group button action
 * - items: subitems of multilevel menu
 * - disposable: disables itself on click (default on actions)
 *
 * ### Extra attributes of the menu itself (non-numeric indices)
 *
 * - form (boolean): the menu will be wrapped into form
 * - action (string): the action of the form
 *
 * Not all properties are applied when used in bootstrap nav.
 */
class UMenu extends BaseObject {

    /** @var array -- list of menu items and menu options. {@see UMenu} */
	public $items = [];
	/**
	 * @var $options array The HTML attributes for the menu's ul tag
	 */
	public $options;
	/**
	 * @var $options array The HTML attributes for the wrapper div tag
	 */
	public $wrapperOptions;

	public function render() {
		self::showMenu($this->items, ArrayHelper::getValue($this->options, 'class'), ArrayHelper::getValue($this->wrapperOptions, 'class'));
	}

	/**
	 * Renders a menu
	 *
	 * @param array $menu [['caption'=>caption, 'action'=>url],...] see $item property of class for details
	 * @param string $class -- class of ul (default is 'menu')
	 * @param string $wrapper -- class of wrapper div if given, default no wrapper at all.
	 * @return string
	 */
	static public function showMenu($menu, $menuclass='menu', $wrapper=null) {
		$r = '';
		if($menu) {
			$r = Html::tag('input', '', ['type'=>'hidden', 'class'=>'data-group', 'name'=>'group']);
			$last = max(array_keys($menu));
			foreach($menu as $i=>$item) {
				if(is_numeric($i)) {
					$caption = ArrayHelper::getValue($item, 'caption');
					$action = ArrayHelper::getValue($item, 'action');
					$display = ArrayHelper::getValue($item, 'display');
					$name = ArrayHelper::getValue($item, 'name');
					$class = ArrayHelper::getValue($item, 'class');
					$icon = ArrayHelper::getValue($item, 'icon');
					$value = ArrayHelper::getValue($item, 'value');
					$disabled = !ArrayHelper::getValue($item, 'enabled', true);
					$visible = ArrayHelper::getValue($item, 'visible', true);
					$group = ArrayHelper::getValue($item, 'group');
					$confirm = ArrayHelper::getValue($item, 'confirm');
					$items = ArrayHelper::getValue($item, 'items');
					$data = ArrayHelper::getValue($item, 'data');
					$disposable = ArrayHelper::getValue($item, 'disposable', $action!='');

					if(!$visible) continue;

					$liclass = array();
					if($i==0) $liclass = ['first'];
					if($i==$last) $liclass[] = 'last';
					if($disabled) $liclass[] = 'disabled';
					if($group) $liclass[] = 'group';

					$confirmx = $confirm ? "if(!confirm('$confirm')) return false; " : '';

					$options = [];
					if(isset($item['title'])) $options['title'] = $item['title'];
					if(is_array($class)) $class = implode(' ', $class);
					$options['class'] = 'menuitem';
					if($disposable) $options['class'] .= ' disposable';
					if($class) $options['class'] .= ' '.$class;
					if(is_array($data)) foreach($data as $k=>$v) $options['data-'.$k] = $v;
					if($group) $options['data-group'] = $group;

					$iconx = self::iconItem($icon);

					if($display=='input') {
						$liclass[] = 'form form-input';
						if($disabled) $options['disabled']='disabled';
						$options = array_merge($options, ['type'=>'text', 'name'=>$name, 'value'=>$value]);
						$s = $caption.' '.Html::tag('input', '', $options);
					}
					elseif($display=='button') {
						$liclass[] = 'form form-button';
						if($disabled) $options['disabled']='disabled';
						$options = array_merge($options, ['type'=>'submit', 'name'=>$name, 'value'=>$caption, 'onclick'=>"$confirmx this.value='$value'"]);
						$s = Html::tag('input', '', $options);
					}
					elseif($item===null) {
						continue;
					}
					elseif($disabled) {
						$s = Html::tag('span', $iconx.$caption, $options);
					}
					else { // Normál menupont
						if(substr($action, 0, 11)=='javascript:') {
							$options['onclick'] = $confirmx.substr($action,11);
							$s = Html::tag('span', $iconx.$caption, $options);
						}
						else {
							if($confirm) $options['onclick'] = "confirm('$confirm')";
							$s = Html::a($iconx.$caption, $action, $options);
						}
					}

					if($items) {
						$s .= self::showMenu($items, 'dropdown');
					}

					$lioptions = [];
					if(count($liclass)) $lioptions['class'] = implode(' ', $liclass);
					if($group) $lioptions['style'] = "display:none";
					$r .= Html::tag('li', $s, $lioptions);
				}
			}
			$r = Html::tag('ul', $r, ['class' => "umenu $menuclass"]);
			if($wrapper) $r = Html::tag('div', $r, ['class'=>$wrapper]);
			// Form wrapper
			if(isset($menu['form'])) {
				$options = [];
				if(isset($item['action'])) $options['action'] = $item['action'];
				$r = Html::tag('form', $r, $options);
			}
		}
		return $r;
	}

	static public function iconItem($icon) {
		if($icon=='') return '';
		if(substr($icon, 0, 10)=='glyphicon-')
			$iconx = Html::tag('span', '', ['class'=>"glyphicon $icon"]);
		else if(substr($icon, 0, 3)=='fa-')
			$iconx = Html::tag('i', '', ['class'=>"fa $icon"]);
		else
			$iconx = '<img class="icon" src="/img/'.$icon.'" />';
		return $iconx;
	}

	/**
	 * Returns data for bootstrap nav widget's items property
	 * 
	 * ## Usage in view:
	 *
	 * <?php NavBar::begin([...]); ?>
	 * <?= Nav::widget([
	 *     'options' => ['class' => 'navbar-nav navbar-right'],
	 *     'items' => $menu->navItems(),
	 * ]); ?>
	 * <?php NavBar::end(); ?>
	 *
	 */
	public function navItems($items = null) {
		if($items === null) $items = $this->items;
		return array_map(function($item, $index) {
			if(is_string($item)) return $item;

			if($subItems = ArrayHelper::getValue($item, 'items')) {
				$subItems = $this->navItems($subItems);
			}
			$class = ArrayHelper::getValue($item, 'class', '');
			if(!ArrayHelper::getValue($item, 'enabled', true)) {
				$class .= ' disabled';
				$url = '';
				$subItems = null;
			}
			$url = ArrayHelper::getValue($item, 'action');
			$navItem = [
				'label'=> ArrayHelper::getValue($item, 'caption'),
				'url' => $url,
			];
			if($subItems) $navItem['items'] = $subItems;
			if(!ArrayHelper::getValue($item, 'visible', true)) $navItem['visible'] = false;
			if($class) $navItem['linkOptions'] = ['class'=>$class];
			foreach(ArrayHelper::getValue($item, 'data', []) as $k=>$v) $navItem['linkOptions']['data-'.$k] = $v;
			return $navItem;
		}, array_values($items), array_keys($items));
	}

}
