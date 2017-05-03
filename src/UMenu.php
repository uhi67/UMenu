<?php

namespace uhi67\umenu;

use yii\base\Object;
use \yii\helpers\ArrayHelper;

class UMenu extends Object {
	/**
	 * Renders a menu
	 * 
	 * @param array $menu [['caption'=>caption, 'action'=>url],...]
	 * 		Array of menu items. Item properties:
	 * 			caption: caption text os item (after icon is exists)
	 * 			icon: icon before or instead of caption (image filename or glyphicon-* or fa-*)
	 * 			title: html title attribute
	 * 			class: html class of <a> or <span>. default is none
	 * 			action: url for <a> or javascript:function for <span>
	 * 			enabled: if false, button is not clickable and gray. html class will contain "disabled" -- TODO
	 * 			visible: boolean, default true
	 * 			group: if true, button is visible only when selection is present in connected grid.		-- TODO
	 * 			display: input|button|normal(default)
	 * 			name: name of embedded input field or submit button
	 * 			value: value of embedded input field or submit button
	 * 			confirm: text of confirmation 															-- TODO
	 * 			items: subitems of multilevel menu
	 * @param string $class -- default class of ul (class property will override it)
	 * @param string $wrapper -- class of wrapper div if given, default no wrapper 
	 * @return string
	 */
	static public function showMenu($menu, $class='menu', $wrapper=null) {
		$r = ''; $q = '';
		if($menu) {
			if($wrapper) $r = '<div class="'.$wrapper.'">';
			$r .= '<ul class="'.$class.'">';
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

					if(!$visible) continue;
					
					$liclass = array();
					if($i==0) $liclass = ['first'];
					if($i==$last) $liclass[] = 'last';
					if($disabled) $liclass[] = 'disabled';
					if($group) $liclass[] = 'group';

					$confirmx = $confirm ? "if(!confirm('$confirm')) return false; " : ''; 

					if($icon) {
						if(substr($icon, 0, 10)=='glyphicon-') $iconx = '<span class="glyphicon '.$icon.'"></span>';
						else if(substr($icon, 0, 3)=='fa-') $iconx = '<i class="fa '.$icon.'"></i>';
						else $iconx = '<img class="icon" src="/img/'.$icon.'" />';
					}
					else 
						$iconx = '';
					
					if($display=='input') {
						$liclass[] = 'form form-input';
						$disabledx = $disabled ? 'disabled="disabled' : '';
						$s = $caption.' <input type="text" name="'.$name.'" value="'.$value.'" '.$disabledx.' />'; 
					}
					elseif($display=='button') {
						$liclass[] = 'form form-button';
						$disabledx = $disabled ? 'disabled="disabled' : '';
						$s = '<input type="submit" name="'.$name.'" value="$caption" '.$disabledx.' onclick="$confirmx this.value=\''.$value.'\'"/>'; 
					}
					elseif($item===null) {
						continue;
					}
					elseif($disabled) {
						$titlex = isset($item['title']) ? 'title="'.$item['title'].'"' : '';
						$s = "<span $titlex>".$iconx.$caption.'</span>'; // $options;
					}
					else { // Normál menupont
						if(substr($action, 0, 11)=='javascript:') {
							$titlex = isset($item['title']) ? 'title="'.$item['title'].'"' : '';
							$clickx = 'onclick="'.$confirmx.substr($action,11).'"'; 
							$s = "<span $clickx $titlex>".$iconx.$caption.'</span>'; // $options;
						}
						else {
							$options = [];
							if(isset($item['title'])) $options['title'] = $item['title'];
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
