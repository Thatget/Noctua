require(
[
	'jquery'
],
function($){
	$(document).ready(function () {
		$(window).on('resize',function(){
			if($(this).width() >= 1001){
				$('#desktop').css('display','none');
				$('#mobile').css('display','none');
			}
			else{
				if(getCookie('view')){
					if(getCookie('view') === 'desktop'){
						$('#desktop').css('display','none');
						$('#mobile').css('display','block');
					}else if(getCookie('view') === 'mobile'){
						$('#desktop').css('display','block');
						$('#mobile').css('display','none');
					}
				}
				else{
					$('#desktop').css('display','block');
					$('#mobile').css('display','none');
				}
			}
		});

		if($(window).width() >= 1001){
			$('#desktop').css('display','none');
			$('#mobile').css('display','none');
		}
		else{
			if(getCookie('view')){
				if(getCookie('view') === 'desktop'){
					$('#desktop').css('display','none');
					$('#mobile').css('display','block');
				}else if(getCookie('view') === 'mobile'){
					$('#desktop').css('display','block');
					$('#mobile').css('display','none');
				}
			}
			else{
				$('#desktop').css('display','block');
				$('#mobile').css('display','none');
			}
		}

		function getCookie(name) {
		  var value = "; " + document.cookie;
		  var parts = value.split("; " + name + "=");
		  if (parts.length == 2) return parts.pop().split(";").shift();
		}


	});
	$('#mobile').click(function () {
		window.localStorage.clear();
    	$.cookie('view','mobile',{path:'/'});
    	var location = window.location;
    	window.location.href = location;
    });

    $('#desktop').click(function () {
    	window.localStorage.clear();
    	$.cookie('view','desktop',{path:'/'});
    	var location = window.location;
    	window.location.href = location;
    });
});