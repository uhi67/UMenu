/**
 * umenu.js
 * javascript support for UMenu extension
 * - gridview context-menu (bottom-menu is a style)
 * - gridview footer input form  
 */

console.log('umenu.js v1.0.1.2');

jQuery.fn.extend({
	// disables a button/menu item with style and click
	disable: function() {
		this.addClass('disabled');
		this.first().data('saved-click', this.get(0).onclick);
		this.get(0).onclick = null;
	},
	enable: function() {
		this.removeClass('disabled');
		this.get(0).onclick = this.data('saved-click');
	}
});

$(function() {
	// Disables hidden footer form at start
	var $trfoot = $('div.footer-form tfoot tr'); 
	$('input, select, textarea', $trfoot).attr('disabled', 'disabled');
	$trfoot.hide();
	
	// Dispose used item
	$('ul.umenu .disposable').click(function(){
		$(this).disable();
	});
	
	// Disabled menu items
	$('li.disabled a, dt.disabled a, a.disabled').each(function() {
		$(this).click(function() {return false;});
	});
	
	// Drop-down menu
	$('ul.dropdown').each(function() {
		var $submenu = $(this);
		//console.log('submenu: ',$submenu);
		$submenu.closest('li').hover(
			function(){
				console.log('hover: ',$submenu);
				if(!$(this).hasClass('disabled')) $submenu.show();
			}, 
			function(){$submenu.hide()}
		);
	});
	
	// Updates context-menu 'group' items on selection change
	var $grid = $('.grid.context-menu');
	$('input[name="selection_all"], input[name="selection[]"]', $grid).change(function() {
		var $grid = $(this).closest('.grid.context-menu');
		console.log('update contex-menu visibility');
		var keys = $grid.yiiGridView('getSelectedRows');
		var $groupitems = $grid.closest('form').find('.context-menu li.group');
		if(keys.length) $groupitems.show(); else $groupitems.hide();
	});
	
	// Group action on group button
	$('.context-menu li.group .menuitem').click(function() {
		console.log('group action');
		
		var keys = $grid.yiiGridView('getSelectedRows');
		var $form = $(this).closest('form');
		
		console.log('keys: '+keys + ' form:', $form);
		var action = $(this).data('action');
		if(action) $form.get(0).action = $form.get(0).action = action;
		
		var group = $(this).data('group');
		if(group) $('input.data-group').val(group);
		
		$form.submit();
	});
	
	// Open input row (gridview footer)
	$('.footer-form-show').click(function() {
		//console.log('show footer form');
		var $button = $(this);
		var $trfoot = $('.table tfoot tr', $button.closest('form')); 
		$trfoot.show();
		// Enable all inputs in footer-form 
		$('input, select, textarea', $trfoot).removeAttr('disabled');
		
		$button.disable();
	});
	
	// Close input row (gridview footer)
	$('.footer-form-close').click(function() {
		var $trfoot = $(this).closest('tr'); 
		$trfoot.hide();
		var $button = $('.footer-form-show', $(this).closest('form').parent());
		$button.enable();
		// Disable all inputs to ensure to pass validation on footer-form 
		$('input, select, textarea', $trfoot).attr('disabled', 'disabled');
	});
});
