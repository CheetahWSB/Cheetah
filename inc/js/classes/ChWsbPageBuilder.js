/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

function ChWsbPageBuilder(options) {
	this.options = options;
	this.loadAreas();
}

ChWsbPageBuilder.prototype.resetPage = function() {
	var $this = this;
	$(document).dolPopupConfirm({
		message: _t('_adm_pbuilder_Reset_page_warning'),
		onClickYes: function() {
			$.post(
			$this.options.parser,
			{
				action: 'resetPage',
				Page: $this.options.page
			},
			function() {
				location.reload();
			}
			);
		}
	});
};

ChWsbPageBuilder.prototype.loadAreas = function() {
	var _builder = this;

	this.activeArea = $('#activeBlocksArea').get(0);
	this.inactiveArea = $('#inactiveBlocksArea').get(0);
	this.samplesArea = $('#samplesBlocksArea').get(0);
	this.eAllAreas = $(this.activeArea).add(this.inactiveArea).add(this.samplesArea).parent().parent().get(0);

	$.getJSON(
	this.options.parser,
	{
		action: 'load',
		Page: this.options.page
	},
	function(oJSON) {
		_builder.loadJSON(oJSON);
	}
	);
};

ChWsbPageBuilder.prototype.loadJSON = function(oJSON) {
	var _builder = this;

	if(!oJSON.active || !oJSON.widths || !oJSON.inactive || !oJSON.samples || !oJSON.min_widths)
		return false;

	$(this.activeArea).html('');
	$(this.inactiveArea).html('');
	$(this.samplesArea).html('');

	this.minWidths = oJSON.min_widths;

	var iColumns = 0;

	//active blocks
	for(var iColumn in oJSON.widths) {
		var iWidth = oJSON.widths[iColumn];
		var aBlocks = oJSON.active[iColumn];
		var aBlocksOrder = oJSON.active_order[iColumn];
		this.drawColumn(iColumn, iWidth, aBlocks, aBlocksOrder, true);

		iColumns++;
	}

	this.checkAddColumn();

	//inactive blocks
	for(var iBlockID in oJSON.inactive) {
		var sBlockCaption = oJSON.inactive[iBlockID];
		this.drawBlock(parseInt(iBlockID), sBlockCaption, this.inactiveArea);
	}

	//sample blocks
	for(var iBlockID in oJSON.samples) {
		var sBlockCaption = oJSON.samples[iBlockID];
		this.drawBlock(iBlockID, sBlockCaption, this.samplesArea);
	}

	$(this.inactiveArea).append('<div class="buildBlockFake"></div> <div class="clear_both"></div>');
	$(this.samplesArea).append('<div class="clear_both"></div>');

	this.initPageWidthSlider();
	this.initOtherPagesWidthSlider();
	this.initColsSlider();
	this.checkBlocksMaxWidths();
	this.activateSortables();
};

ChWsbPageBuilder.prototype.initPageWidthSlider = function() {
	var _builder = this;
	var $slider = $('#pageWidthSlider');

	if(!$slider.length)
		return false;

	$slider.slider({
		min: this.options.pageWidthMin,
		max: this.options.pageWidthMax + 1,
		value: this.width2slider(this.options.pageWidth),
		change: function(e, ui) {
			_builder.onWidthSliderStop(ui.value)
		},
		slide: function(e, ui) {
			_builder.onWidthSliderMove(ui.value)
		}
	});

	$('#pageWidthValue').html(this.options.pageWidth);
};

ChWsbPageBuilder.prototype.initOtherPagesWidthSlider = function() {
	var _builder = this;
	var $slider = $('#pageWidthSlider1');

	if(!$slider.length)
		return false;

	$slider.slider({
		min: this.options.pageWidthMin,
		max: this.options.pageWidthMax + 1,
		value: this.width2slider(this.options.otherPagesWidth),
		change: function(e, ui) {
			_builder.onOtherWidthSliderStop(ui.value)
		},
		slide: function(e, ui) {
			_builder.onOtherWidthSliderMove(ui.value)
		}
	});

	$('#pageWidthValue1').html(this.options.otherPagesWidth);
};

ChWsbPageBuilder.prototype.width2slider = function(sCurWidth) {
	if(sCurWidth == '100%')
		return this.options.pageWidthMax + 1;

	return parseInt(sCurWidth);
};

ChWsbPageBuilder.prototype.slider2width = function(iSliderVal) {
	if(iSliderVal > this.options.pageWidthMax)
		return '100%';

	return iSliderVal + 'px';
};

ChWsbPageBuilder.prototype.onWidthSliderStop = function(value) {
	var _builder = this;

	//set current page width
	this.options.pageWidth = this.slider2width(value);

	//submit page width
	$.post(this.options.parser, {
		action: 'savePageWidth',
		Page: this.options.page,
		width: this.options.pageWidth
	},
	function(sResponse) {
		if(sResponse != 'OK')
			//alert(sResponse);
      alert('Error saving page width.');
	});

	//update columns headers
	$('.buildColumn', this.activeArea).each(function(iInd) {
		_builder.setColumnHeader(this, (iInd + 1));
	});

	this.checkBlocksMaxWidths();
};

ChWsbPageBuilder.prototype.onWidthSliderMove = function(value) {
	var sCurPageWidth = this.slider2width(value);
	$('#pageWidthValue').html(sCurPageWidth);
};

ChWsbPageBuilder.prototype.onOtherWidthSliderStop = function(value) {
	var _builder = this;

	//set current page width
	this.options.otherPagesWidth = this.slider2width(value);

	//submit page width
	$.post(this.options.parser, {
		action: 'saveOtherPagesWidth',
		Page: this.options.page,
		width: this.options.otherPagesWidth
	},
	function(sResponse) {
		if(sResponse != 'OK')
			//alert(sResponse);
      alert('Error saving other pages width.');
	});
};

ChWsbPageBuilder.prototype.onOtherWidthSliderMove = function(value) {
	var sCurPageWidth = this.slider2width(value);
	$('#pageWidthValue1').html(sCurPageWidth);
};

ChWsbPageBuilder.prototype.checkBlocksMaxWidths = function() {
	//remove alerts
	$('.blockAlert').remove();

	if(this.options.pageWidth == '100%')
		return; //do not check

	for(var iBlockID in this.minWidths) {
		var iBlockMinWidth = this.minWidths[iBlockID];

		var $block = $('#buildBlock_' + iBlockID);
		var iColumnWidth = Math.round(parseInt(this.options.pageWidth) * parseInt(getCssWidth($block.parent().parent()[0])) / 100);
		if(iColumnWidth < iBlockMinWidth) {
			$('<span class="sys-icon exclamation-triangle blockAlert"></span>')
			.appendTo($block)
			.hover(
			function() {
				showFloatDesc(_t('_adm_pbuilder_Column_non_enough_width_warn', iBlockMinWidth));
			},
			function() {
				hideFloatDesc();
			}
			)
			.mousemove(function(e) {
				moveFloatDesc(e)
			});
		}
	}
};

ChWsbPageBuilder.prototype.checkAddColumn = function() {
	var iColumns = $('.buildColumn', this.activeArea).length;

	if(iColumns >= this.options.maxCols)
		$('#addColumnButton').attr('disabled', 'disabled');
	else
		$('#addColumnButton').removeAttr('disabled');

	if(this.options.page == undefined || this.options.page == '')
		$('#resetPage').attr('disabled', 'disabled');
	else
		$('#resetPage').removeAttr('disabled');
};

ChWsbPageBuilder.prototype.addColumn = function() {
	this.drawColumn($('.buildColumn', this.activeArea).length, 0, {}, {});
	this.checkAddColumn();
	this.refreshSortables();
	this.reArrangeColumns();
};

ChWsbPageBuilder.prototype.addFullColumn = function() {
	this.drawColumn($('.buildColumn', this.activeArea).length, 100, {}, {});
	this.checkAddColumn();
	this.refreshSortables();
	//this.reArrangeColumns();
};

ChWsbPageBuilder.prototype.initColsSlider = function() {
	var iSliderValue = 0;
	var aSliderValues = [];
	var _builder = this;

	var rows = this.getColumnsRows();
	var aColumns = [];
	rows.map(function(columns) {
		if(columns.length > aColumns.length) {
			aColumns = columns;
		}
	});

	if(aColumns.length < 2)
		return; //dont insert

	for(var iSliderNum = 0; iSliderNum < (aColumns.length - 1); iSliderNum++) {
		iSliderValue += parseFloat(getCssWidth(aColumns[iSliderNum]));
		aSliderValues[iSliderNum] = 10 * iSliderValue;
	}

	//init slider
	if($('#columnsSlider').is(":ui-slider"))
		$('#columnsSlider').slider('destroy');

	$('#columnsSlider').slider({
		change: function(e, ui) {
			_builder.onColsSliderStop(ui)
		},
		slide: function(e, ui) {
			_builder.onColsSliderMove(ui)
		},
		max: 1000,
		values: aSliderValues
	});
};

ChWsbPageBuilder.prototype.onColsSliderStop = function() {
	this.checkBlocksMaxWidths();
	this.submitWidths();
};

ChWsbPageBuilder.prototype.onColsSliderMove = function(slider) {
	var _builder = this;
	var aValues = new Array();

	if(typeof slider.values == 'object') {
		var iCounter = 0;
		for(var iInd in slider.values)
			aValues[iCounter++] = slider.values[iInd] / 10;
	} else if(typeof slider.values == 'number')
		aValues[0] = slider.values / 10;
	aValues[aValues.length] = 100;

	var iMinusWidth = iInd = 0;
	$('.buildColumn', this.activeArea).each(function() {
		if($(this).is('.buildColumnFull')) {
			iInd = iMinusWidth = 0;
			return;
		}

		var iNewWidth = aValues[iInd] - iMinusWidth;

		$(this).css('width', iNewWidth + '%');
		_builder.setColumnHeader(this, (iInd + 1));

		iMinusWidth += iNewWidth;
		iInd++;
	});
};

ChWsbPageBuilder.prototype.submit = function() {
	var _builder = this;
	var aColumns = new Array();

	//get columns
	$('.buildColumn', this.activeArea).each(function() {
		var iColumn = aColumns.length;

		//get blocks
		aColumns[iColumn] = new Array();
		$('.buildBlock', this).each(function() {
			var iItemID = parseInt(this.id.substr('buildBlock_'.length));
			aColumns[iColumn].push(iItemID);
		});
		aColumns[iColumn] = aColumns[iColumn].join(',');

		iColumn++;
	});

	$.post(
	this.options.parser, {
		action: 'saveBlocks',
		Page: this.options.page,
		'columns[]': aColumns,
		_t: new Date()
	},
	function(sResponse) {
		if(sResponse != 'OK')
			//alert(sResponse);
      alert('Error saving block. Block may not have been dropped into a column.');

		_builder.submitWidths();
	}
	);
};

ChWsbPageBuilder.prototype.submitWidths = function() {
	var aWidths = new Array();

	$('.buildColumn', this.activeArea).each(function() {
		aWidths[aWidths.length] = parseFloat(getCssWidth(this));
	});

	$.post(
	this.options.parser,
	{
		action: 'saveColsWidths',
		Page: this.options.page,
		'widths[]': aWidths
	},
	function(sResponse) {
		if(sResponse != 'OK')
			//alert(sResponse);
      alert('Error saving column widths. Block may not have been dropped into a column.');
	}
	);
};

ChWsbPageBuilder.prototype.setColumnHeader = function(parent, iNum, bIgnoreColsNum) {
	var _builder = this;
	var bIgnoreColsNum = bIgnoreColsNum || false;

	var iPerWidth = parseFloat(getCssWidth($(parent)[0]));
	var sPixAdd = '';

	if(this.options.pageWidth.substr(-2) == 'px') {
		var iPixWidth = Math.round(( parseInt(this.options.pageWidth) * iPerWidth ) / 100);
		sPixAdd = '/' + iPixWidth + 'px';
	}

	var $header = $('.buildColumnHeader', parent).html(_t('_adm_btn_Column') + ' ' + iNum + ' (' + iPerWidth + '%' + sPixAdd + ')');

	if(!$(parent).hasClass('buildColumnSpecial') && (bIgnoreColsNum || $('.buildColumn:not(.buildColumnSpecial)', this.activeArea).length > this.options.minCols)) {
		$header.append('<a href="#" title="Delete" id="linkDelete" title="' + _t('_delete') + '"><i class="sys-icon times"></i></a>').children('a').click(function() {
			$(document).dolPopupConfirm({
				message: _t('_adm_pbuilder_Column_delete_confirmation'),
				onClickYes: function() {
					_builder.deleteColumn(parent);
				}
			});

			return false;
		});
	}
};

ChWsbPageBuilder.prototype.refreshColumnHeaders = function() {
	var _builder = this;
	$('.buildColumn', this.activeArea).each(function(iInd) {
		//update column header
		_builder.setColumnHeader(this, (iInd + 1));
	});
};

ChWsbPageBuilder.prototype.deleteColumn = function(column) {
	$('.buildBlock', column).prependTo(this.inactiveArea);
	$(column).remove();

	this.checkAddColumn();
	this.reArrangeColumns();
};

ChWsbPageBuilder.prototype.reArrangeColumns = function() {
	var _builder = this;

	var rows = this.getColumnsRows();

	var aRowWithMaxColumns = [];
	rows.map(function(columns) {
		if(columns.length > aRowWithMaxColumns.length) {
			aRowWithMaxColumns = columns;
		}
	});

	var $columns = $('.buildColumn:not(.buildColumnFull)', this.activeArea);
	var iNewWidth = Math.floor(10 * (100 / aRowWithMaxColumns.length)) / 10;

	$columns.css('width', iNewWidth + '%').each(function(iInd) {
		_builder.setColumnHeader(this, (iInd + 1));
	});

	this.initColsSlider();
	this.submit();
};

ChWsbPageBuilder.prototype.destroySortables = function() {
	if(this.oSIColumns)
		this.oSIColumns.destroy();

	if(this.oSIBlocks)
		this.oSIBlocks.destroy();
};

ChWsbPageBuilder.prototype.activateSortables = function() {
	var _builder = this;

	//--- Make Columns sortable ---//
	if($(this.activeArea).is(":ui-sortable"))
		$(this.activeArea).sortable('destroy');

	$(this.activeArea).sortable({
		items: '.buildColumn',
		hoverClass: 'buildHover',
		forceHelperSize: true,
		//appendTo: 'body',
		cancel: '.buildBlock',
		placeholder: 'buildColumn ui-sortable-placeholder',
		forcePlaceholderSize: true,
		stop: function() {
			_builder.columnsStopSort();
		}
	});

	//--- Make Blocks sortable ---//
	var $bl = $('.buildColumnCont', this.eAllAreas).add(this.inactiveArea).add(this.samplesArea);
	$bl.each(function() {
		if($(this).is(":ui-sortable"))
			$(this).sortable('destroy');
	});

	$bl.sortable({
		items: '.buildBlock,.buildBlockFake',
		connectWith: $bl,
		placeholder: 'buildBlock ui-sortable-placeholder',
		forcePlaceholderSize: true,
		start: function(e, ui) {
			_builder.blocksStartSort(ui.item[0]);
		},
		stop: function(e, ui) {
			_builder.blocksStopSort(ui.item[0]);
		}
	});
};

ChWsbPageBuilder.prototype.refreshSortables = function() {
	this.activateSortables();
};

ChWsbPageBuilder.prototype.getColumnsRows = function() {
	var rows = [];
	var rowsCount = 0;
	$('.buildColumn', this.activeArea).each(function() {
		if($(this).is('.buildColumnFull')) {
			rowsCount++;
			return;
		}

		if(typeof rows[rowsCount] === 'undefined') {
			rows[rowsCount] = [];
		}

		rows[rowsCount].push(this);
	});

	return rows;
};

ChWsbPageBuilder.prototype.columnsStopSort = function(cycled) {
	var _builder = this;
	var columnsSlider = $('#columnsSlider');

	if(cycled == undefined) {
		setTimeout(function() {
			_builder.columnsStopSort(true)
		}, 600);
		return;
	}

	var rows = this.getColumnsRows();

	var aRowWithMaxColumns = [];
	rows.map(function(columns) {
		if(columns.length > aRowWithMaxColumns.length) {
			aRowWithMaxColumns = columns;
		}
	});

	var iCounter = 0;
	rows.map(function(row) {
		var iColumnCount = 0;

		row.map(function(column) {
			iCounter++;

			var iWidth = parseFloat(getCssWidth(aRowWithMaxColumns[iColumnCount]));
			$(column).css('width', iWidth + '%');

			iColumnCount++;
		});
	});

	this.refreshColumnHeaders();
	this.initColsSlider();

	this.submit();
};

ChWsbPageBuilder.prototype.blocksStartSort = function(eDragged) {
	$(eDragged).find('.buildBlockCover').show();
};

ChWsbPageBuilder.prototype.blocksStopSort = function(eDragged, cycled) {
	var _builder = this;

	if(cycled == undefined) {
		setTimeout(function() {
			_builder.blocksStopSort(eDragged, true)
		}, 600);
		return;
	}

	//check if the dragged element is sample
	if($('#' + eDragged.id, this.activeArea).length) { // if it is dragged to the active area
		var iBlockID = parseInt(eDragged.id.substr('buildBlock_'.length));
		$.post(
		this.options.parser,
		{
			action: 'checkNewBlock',
			Page: this.options.page,
			id: iBlockID
		},
		function(sResponse) {
			$('#buildBlock_' + iBlockID + ' .buildBlockCover').hide();

			if(sResponse == '') {
				_builder.submit();
			} else {
				var iNewBlockID = parseInt(sResponse);
				if(iNewBlockID)
					_builder.addBlock(iNewBlockID, eDragged);
				_builder.submit();
			}
		}
		);
	}
	else {
		$(eDragged).find('.buildBlockCover').hide();

		this.submit();
	}
};

ChWsbPageBuilder.prototype.addBlock = function(iNewID, eBefore) {
	this.drawBlock(iNewID, $(eBefore).text(), this.samplesArea);

	$('#buildBlock_' + iNewID, this.samplesArea).insertBefore(eBefore);
	$(eBefore).prependTo(this.samplesArea);

	this.refreshSortables();
};

ChWsbPageBuilder.prototype.drawColumn = function(iColumnNum, iWidth, aBlocks, aBlocksOrder, bFirstLoad) {
	$('div.clear_both', this.activeArea).remove();

	var bFirstLoad = bFirstLoad || false;

	// lets find the last "non" full width column
	var lastColumn = $('.buildColumn:not(.buildColumnFull)', this.activeArea).last();

	var $newColumn = $(
	'<div class="buildColumn' + (iWidth == 100 ? ' buildColumnFull' : '') + '" style="width:' + iWidth + '%;">' +
	'<div class="buildColumnCont">' +
	'<div class="buildColumnHeader"></div>' +
  '<div id="buildColumnDragArea" style="width: 142px; margin: auto;">' +
	'<div class="buildBlockFake"></div>' +
	'</div>' +
  '</div>' +
	'</div>'
	);

	if(bFirstLoad) {
		$newColumn.appendTo(this.activeArea);
	} else if(iWidth == 100 || !lastColumn[0]) {
		$newColumn.appendTo(this.activeArea);
	} else {
		$newColumn.insertAfter(lastColumn);
	}

	this.setColumnHeader($newColumn, iColumnNum, true);

	//var eColumnCont = $('.buildColumnCont', $newColumn).get(0);
  var eColumnCont = $('#buildColumnDragArea', $newColumn).get(0);

	for(var i in aBlocksOrder) {
		var iBlockID = aBlocksOrder[i];
		var sBlockCaption = aBlocks[iBlockID];
		this.drawBlock(iBlockID, sBlockCaption, eColumnCont);
	}

	$(this.activeArea).append('<div class="clear_both"></div>');
};

ChWsbPageBuilder.prototype.drawBlock = function(iBlockID, sBlockCaption, eColumnCont) {
	var _builder = this;
	if(sBlockCaption.length == 0)
		sBlockCaption = _t('_Empty');

	$('<div class="buildBlock" id="buildBlock_' + iBlockID + '">' +
	'<i class="sys-icon arrows"></i>' +
	'<a href="#">' + sBlockCaption + '</a>' +
	'<div class="buildBlockCover"></div>' +
	'</div>').appendTo(eColumnCont).children('a').click(function() {
		_builder.openProperties(iBlockID);
		return false;
	});
};

ChWsbPageBuilder.prototype.openProperties = function(iBlockID) {
	var $this = this;
	var oDate = new Date();

	$.post(
	this.options.parser,
	{
		action: 'loadEditForm',
		Page: this.options.page,
		id: iBlockID,
		_t: oDate.getTime()
	},
	function(sData) {
		var oPopup = $(sData).hide();
		$('#' + oPopup.attr('id')).remove();

		oPopup.prependTo('body').dolPopup({
			closeOnOuterClick: false,
			onHide: function() {
				if('undefined' != typeof(jQuery(document).tinymce))
					jQuery('#form_input_html' + iBlockID).tinymce().remove();

				$('#' + oPopup.attr('id')).remove();
			}
		});

		var oForm = oPopup.find('form');
		$(':reset[name=Cancel]', oForm).click(function() {
			oPopup.dolPopupHide({});
			return false;
		});

		$(':reset[name=Delete]', oForm).click(function() {
			$(document).dolPopupConfirm({
				message: _t('_adm_pbuilder_Want_to_delete'),
				onClickYes: function() {
					$this.deleteBlock(iBlockID);
					oPopup.dolPopupHide({});
				}
			});
		});

		oForm.ajaxForm({
			success: function(sResponse) {
				$this.updateBlock(iBlockID, sResponse);
				oPopup.dolPopupHide({});
			}
		});
	},
	'html'
	);
};

ChWsbPageBuilder.prototype.deleteCustomPage = function(sPageName) {
	var $this = this;
	$(document).dolPopupConfirm({
		onClickYes: function() {
			$.post(
			$this.options.parser,
			{
				action: 'deleteCustomPage',
				Page: $this.options.page
			},
			function(sErrorMessage) {
				if(sErrorMessage) {
					alert(sErrorMessage);
					return;
				}

				window.location = $this.options.parser;
			}
			);
		}
	});
};

ChWsbPageBuilder.prototype.deleteBlock = function(iBlockID) {
	$('#buildBlock_' + iBlockID).remove();
	$.post(this.options.parser, {
		action: 'deleteBlock',
		Page: this.options.page,
		id: iBlockID
	});
};

ChWsbPageBuilder.prototype.updateBlock = function(iBlockID, sCaption) {
	$('#buildBlock_' + iBlockID + ' a').html(sCaption);
};

ChWsbPageBuilder.prototype.getHorizScroll = function() {
	if(navigator.appName == "Microsoft Internet Explorer")
		return document.documentElement.scrollLeft;
	else
		return window.pageXOffset;
};

ChWsbPageBuilder.prototype.getVertScroll = function() {
	if(navigator.appName == "Microsoft Internet Explorer")
		return document.documentElement.scrollTop;
	else
		return window.pageYOffset;
};

function showNewPageDialog(sParserUrl) {
	$.get(
	sParserUrl, {
		action_sys: 'loadNewPageForm'
	},
	function(sData) {
		var oPopup = $(sData).hide();
		$('#' + oPopup.attr('id')).remove();

		oPopup.prependTo('body');
		oPopup.dolPopup();

		var oForm = oPopup.find('form');
		$(':reset[name=Cancel]', oForm).click(function() {
			oPopup.dolPopupHide({});
			return false;
		});

		oForm.ajaxForm({
			success: function(oData) {
				if(oData && oData.code != undefined && oData.code != 0) {
					alert('Cannot create page. ' + oData.message);
					return;
				}

				if(oData && oData.uri != undefined)
					sParserUrl = sParserUrl + '?Page=' + oData.uri;

				window.location = sParserUrl;
			},
			dataType: 'json'
		});
	},
	'html'
	);
}

function showRenamePageDialog(sParserUrl, current_page) {
	$.get(
	sParserUrl, {	action_sys: 'loadRenamePageForm', current_page: current_page },
	function(sData) {
		var oPopup = $(sData).hide();
		$('#' + oPopup.attr('id')).remove();

		oPopup.prependTo('body');
		oPopup.dolPopup();

		var oForm = oPopup.find('form');
		$(':reset[name=Cancel]', oForm).click(function() {
			oPopup.dolPopupHide({});
			return false;
		});

		oForm.ajaxForm({
			success: function(oData) {
				if(oData && oData.code != undefined && oData.code != 0) {
					alert('Cannot create page. ' + oData.message);
					return;
				}

				if(oData && oData.uri != undefined)
					sParserUrl = sParserUrl + '?Page=' + oData.uri;

				window.location = sParserUrl;
			},
			dataType: 'json'
		});
	},
	'html'
	);
}

function getCssWidth(e) {
	if(window.getComputedStyle)
		return e.style.getPropertyValue("width");
	else if(e.currentStyle)
		return e.currentStyle['width'];
	else
		return 0;
}
