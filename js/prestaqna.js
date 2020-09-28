$(document).ready(function() {


$('#qna_ask').validate({
	submitHandler: function(form) {
		var $serialized = $(form).serialize();
		ajaxCall($('#qna_ask'),$serialized);
	},
	errorClass: "invalid",
	rules: {
		qna_q: "required",
	    qna_email: {
	    	required: true,
	        email: true
		}
	},
	messages: {
		qna_q: '',
		qna_email: {
			required: '',
			email: qna_bademail
		}
	}

})

$('#submitQNA').click(function() {
	$('#qna_ask').submit();
});	

/* Ajax add request*/
function ajaxCall(caller,data) {
	$('#submitQNA').fadeOut();
	caller.append($('<div class="ajaxloader"><img src="'+baseDir+'modules/prestaqna/ajax-loader.gif"/></div>'));
	$('.qna_confirm, .qna_error').fadeOut('normal', function(){$(this).remove});
	$.ajax({
	type: 'POST',
	data: data,
	url: baseDir+'modules/prestaqna/ajax.php',
	success: function(data){
		if(data !=1)
			$('#submitQNA').fadeIn();

		if(data == 'err')
			$('<p class="qna_error">'+qna_error+'</p>').hide().appendTo(caller).fadeIn();
		else if(data == 'mex')
			$('<p class="qna_error">'+qna_badcontent+'</p>').hide().appendTo(caller).fadeIn();
		else if(data == 'name')
			$('<p class="qna_error">'+qna_badname+'</p>').hide().appendTo(caller).fadeIn();
		else if(data == 'mail')
			$('<p class="qna_error">'+qna_bademail+'</p>').hide().appendTo(caller).fadeIn();
		else if (data == 1) // Okay
		{
			$('<p class="qna_confirm confirmation">'+qna_confirm+'</p>').hide().appendTo(caller).fadeIn();
		}
			
		else alert(data);
		$('.ajaxloader').fadeOut('normal', function(){$(this).remove()}); //remove spinner
	}
	})
}

if(window.location.hash == '#qnaTab'){
	$('.qnaTabPointer').click();
	$('body').animate({ scrollTop: $("#qna_pointer").offset().top }, 500);
}

});