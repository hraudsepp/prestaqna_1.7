$(document).ready(function() {


	// Change it to yours

	var affiliate_url = '002';

	var licontent  = '<li class="submenu_size  maintab" id="maintab-Dh42">';
			licontent += '<a href="javascript:void(0)" style="cursor:pointer;font-weight: bold; color:#ff5450; padding-left:5px;padding-right:5px" class="title">';
				licontent += 'Get Prestashop Support';
			licontent += '</a>';
		licontent += '</li>';



	$('#menu').append(licontent);

		
	$('<div>').css({
			'position': 'fixed',
			'left' : 0,
			'top' : 0,
			'z-index' : 98,
			'width': '100%',
			'height': '100%',
			'background-color':'#000',
			'opacity': .5
		}).addClass('dhoverlay').hide().prependTo('body');

		$('<div>').css({
			'position': 'fixed',
			'z-index' : 99,
			'left' : '50%',
			'top' : '10%',
			'margin-left' : '-488px',
			'width': '976px',
			'height': '500px',
			'color': '#555',
			'background-color':'#fcfcfa',
		'-webkit-box-shadow': '0px 0px 10px 0px #222',
		'-moz-box-shadow': '0px 0px 10px 0px #222',
		'box-shadow': '0px 0px 10px 0px #222',
		'-moz-background-clip': 'padding',
		'-webkit-background-clip': 'padding-box',
		'background-clip': 'padding-box'
		}).addClass('dh42support').hide().prependTo('body');

	$('.dh42support>div.wrapper')
		.append('<a id="closeDialog" style="-webkit-border-radius:13px;-moz-border-radius: 5px;border-radius:5px;background-color: #fcfcfa;cursor: pointer;position: absolute;right:-5px;top:-5px;padding:3px;"><img src="../img/admin/module_notinstall.png"/></a>');
	function closeDialog() {
		$('.dh42support').fadeOut(400, function(){$(this).remove()});
		$('.dhoverlay').fadeOut(400, function(){$(this).remove()});
	}

	$('#closeDialog').click(function(){return closeDialog()});
	$('.dhoverlay').click(function(){return closeDialog()});

	$('#maintab-Dh42 a').click(function(e) {

		e.preventDefault();

		if($('#dh42-support-container').length == 0)
			$('.dh42support').append('<div id="dh42-support-container"style="height:100%; width:100%"><iframe frameborder="0" src="https://dh42.com/support/aff.php?aff='+affiliate_url+'&support=true&iframe=yes" style="width:100%; height:100%"></iframe><style>#main-header {display:none}</style></div>');



		$('.dhoverlay').fadeIn();
		$('.dh42support').fadeIn();




	});

});