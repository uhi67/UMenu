<?php

namespace uhi67\umenu;

use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class UMenu extends Object {
	/**
	 * Renders a menu
	 * 
	 * @param array $menu [['caption'=>caption, 'action'=>url],...]
	 * 		Array of menu items. Item properties:
	 * 			caption: caption text os item (after icon is exists)
	 * 			icon: icon before or instead of caption (image filename or glyphicon-* or fa-*)
	 * 			title: html title attribute
	 * 			class: html class of <a> or <span>. default is none.
	 * 			action: url for <a> or javascript:function for <span>
	 * 			enabled: if false, button is not clickable and gray. html class will contain "disabled". Default is true (enabled).
	 * 			visible: boolean, default true
	 * 			group: if true, button is visible only when selection is present in connected grid.
	 * 			display: input|button|normal(default)
	 * 			name: name of embedded input field or submit button
	 * 			value: value of embedded input field or submit button
	 * 			confirm: text of confirmation
	 * 			data: array of data-* items' values
	 * 			items: subitems of multilevel menu
	 * 			disposable: disables itself on click (default on urls)
	 * @param string $class -- default class of ul (class property will override it)
	 * @param string $wrapper -- class of wrapper div if given, default no wrapper 
	 * @return string
	 */
	static public function showMenu($menu, $class='menu', $wrapper=null) {
		$r = ''; $q = '';
		if($menu) {
			if($wrapper) $r = '<div class="'.$wrapper.'">';
			$r .= '<ul class="umenu '.$class.'">';
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
					$disposable = ArrayHelper::getValue($item, 'disposable', true);

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

					if($icon) {
						if(substr($icon, 0, 10)=='glyphicon-') $iconx = '<span class="glyphicon '.$icon.'"></span>';
						else if(substr($icon, 0, 3)=='fa-') $iconx = '<i class="fa '.$icon.'"></i>';
						else $iconx = '<img class="icon" src="/img/'.$icon.'" />';
					}
					else 
						$iconx = '';
					
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

					$liclassx = $liclass ? ' class="'.implode(' ', $liclass).'"' : '';
					$displayx = $group ? ' style="display:none"' : ''; 
					$r .= '<li'.$displayx.$liclassx.'>' . $s . '</li>';
				}
				else {
					if($i=='form') {
						$action = isset($item['action']) ? 'action="'.$item['action'].'"' : ''; 
						$r = "<form $action>" . $r;
						$q = '</form>';
						
					}
				}				
			}
			$r .= '</ul>';
			if($wrapper) $r .= '</div class="'.$wrapper.'">';
		}
		return $r . $q;
	}	
}
