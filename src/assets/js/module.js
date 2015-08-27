(function($) {
	// disable fields when they are not "compatible"
	sweelix.register(
		'updateNodeDisplayMode',
		function (mode) {
			switch(mode) {
				case 'list':
					jQuery('select.modeRedirection:enabled').attr('disabled','disabled');
					// jQuery('select.modeList:disabled').removeAttr('disabled');
					break;
				case 'redirect':
					jQuery('select.modeRedirection:disabled').removeAttr('disabled');
					// jQuery('select.modeList:enabled').attr('disabled','disabled');
					break;
				case 'first':
					jQuery('select.modeRedirection:enabled').attr('disabled','disabled');
					// jQuery('select.modeList:enabled').attr('disabled','disabled');
					break;
			}
			jQuery("select").trigger("liszt:updated")
		}
	);
	/**
	 * hideDateFields
	 * used when we have a checkbox to show/hide the datepicker
	 */
	sweelix.register(
		'hideDateFields',
		function () {
			jQuery('input[type=checkbox].activeDate').each(function(i, el){
				if(jQuery(el).is(':checked') === false) {
					var targetElement = $(el).attr('id').replace('Active','');
					jQuery('#'+targetElement).hide();
				}
			});
		}				
	);
	function activateSortable() {
		jQuery('.sortable').sortable({
			'items':'tr',
			'helper':function(e, ui) {
				ui.children().each(function(){
					jQuery(this).width(jQuery(this).width());
				});
				return ui;
			},
			'update':function(e, ui){
				var data = {};
				var moveUrl = jQuery(ui.item).data('url-move');
				var nextContent = jQuery(ui.item).next().data('content-id');
				var previousContent = jQuery(ui.item).prev().data('content-id');
				if(typeof(nextContent) != 'undefined') {
					data['target'] = 'before';
					data['targetId'] = nextContent;
				} else if(typeof(previousContent) != 'undefined') {
					data['target'] = 'after';
					data['targetId'] = previousContent;
				}
				var mode = jQuery(ui.item).data('mode');
				if(typeof(mode) == 'undefined') {
					mode = null;
				}
				var target = jQuery(ui.item).data('target');
				if(typeof(target) == 'undefined') {
					target = null;
				}
				sweelix.raise('ajaxRefreshHandler', {
					'targetUrl' : moveUrl,
					'data' : data,
					'mode' : mode,
					'targetSelector' : target
				});
			}
		}).disableSelection();
	}
	function activateSortableTree() {
		jQuery('ul.sortableTree').nestedSortable({
            handle: 'a',
            items: 'li',
            listType: 'ul',
            // rootID:'#tree',
            toleranceElement: '> a',
            protectRoot: true,
            placeholder: 'sortable-placeholder',
            stop: function( event, ui ) {
            	var data = {};
            	var moveUrl = jQuery(ui.item).data('url-move');
            	data['sourceId'] = jQuery(ui.item).data('node-id');
            	var previousNode = jQuery(ui.item).prev().data('node-id');
            	var nextNode = jQuery(ui.item).next().data('node-id')
            	if(typeof(previousNode) != 'undefined') {
            		data['target'] = 'after';
            		data['targetId'] = jQuery(ui.item).prev().data('node-id');
            	} else if(typeof(nextNode) != 'undefined') {
            		data['target'] = 'before';
            		data['targetId'] = jQuery(ui.item).next().data('node-id');
            	} else {
            		data['target'] = 'in';
            		data['targetId'] = jQuery(ui.item).parent().parent().first('li').data('node-id');
            	}
            	
				var mode = jQuery(ui.item).data('mode');
				if(typeof(mode) == 'undefined') {
					mode = null;
				}
				var target = jQuery(ui.item).data('target');
				if(typeof(target) == 'undefined') {
					target = null;
				}
				sweelix.raise('ajaxRefreshHandler', {
					'targetUrl' : moveUrl,
					'data' : data,
					'mode' : mode,
					'targetSelector' : target
				});
            }
        }).removeClass('sortableTree');
	}
	function attachCalendarHandler() {
		jQuery('input[type=text].calendar').datepicker({
            'showAnim':'fold',
            'beforeShow':function(input, inst) {
                    var startDate = jQuery('input[type=text].calendar.startdate').first();
                    var endDate = jQuery('input[type=text].calendar.enddate').first();
                    if(input.id == startDate.attr('id')) {
                    	return {'maxDate':(endDate.nextAll('input[type=checkbox].activeDate').first().is(':checked') ? endDate.val() : '')};
                    }else if(input.id == endDate.attr('id')) {
                            return {'minDate':(startDate.nextAll('input[type=checkbox].activeDate').first().is(':checked') ? startDate.val() : '')};
                    }
            }
    });
	}
	jQuery(document).ready(function(){
		activateSortable();
		activateSortableTree();
		attachCalendarHandler();
		sweelix.raise('hideDateFields');
		
		sweelix.raise('updateNodeDisplayMode', jQuery('input[type=radio].displayMode:checked').val());
		// register delegated event
		jQuery('body').on('click', 'input[type=radio].displayMode', function(evt) {
			sweelix.raise('updateNodeDisplayMode', jQuery(this).val());
		});
		jQuery('body').on('change', 'input[type=checkbox].activeDate', function(evt){
			var targetElement = jQuery(this).attr('id').replace('Active','');
			if($(this).is(':checked') === true) {
				$('#'+targetElement).show();
			} else {
				$('#'+targetElement).hide();
			}
		});
		jQuery('body').on('click', '.ajaxRefresh', function(evt){
			evt.preventDefault();
			var mode = jQuery(this).data('mode');
			if(typeof(mode) == 'undefined') {
				mode = null;
			}
			var target = jQuery(this).data('target');
			if(typeof(target) == 'undefined') {
				target = null;
			}
			sweelix.raise('ajaxRefreshHandler', {
				'targetUrl' : jQuery(this).attr('href'),
				'mode' : mode,
				'targetSelector' : target
			})
		});
		// register after ajax event
		jQuery('body').on('afterAjax', 'form', function(){
			sweelix.raise('updateNodeDisplayMode', jQuery('input[type=radio].displayMode:checked').val());
		});
		jQuery('body').on('afterAjax', '*', function(){
			// needed to rest sortable stuff should be better if we can check if sortable is still active
			activateSortable();
			activateSortableTree();
			attachCalendarHandler();
			sweelix.raise('hideDateFields');
		});
	});
})(jQuery);