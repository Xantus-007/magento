$(function() {
	//_gaq.push(['_trackEvent', 'CATEGORY', 'ACTION', 'LIBELE']);

	$( window ).scroll(function(e) {
  		var current = (($(window).scrollTop() + $(window).height()) / $(document).height())*100;
	  	var percent = 0;
		if(current >= 25 && current < 50 ){
	  		percent = 25;
	  	}
  		if(current >= 50 && current < 75 ){
	  		percent = 50;
  		}
  		if(current >= 75 && current < 100 ){
	  		percent = 75;
  		}
	  	if(current == 100){
	  		percent = 100;
  		}
  		if(percent > 0){
  			console.log(['_trackEvent', 'navigation', 'scroll', percent]);
			_gaq.push(['_trackEvent', 'navigation', 'scroll', percent + '%']);
  		}
	});

	//le clic sur tous les champs ( donc 4 tracking différents) 
	$('input').focus(function(){
		var section = '';
		var $parent = $(this).parent();

		while(!$parent.hasClass('form')){
			$parent = $parent.parent();
		}

		_gaq.push(['_trackEvent', 'formulaire', 'clickChamp' + $(this).attr('data--ganame') +  $parent.attr('data--ga')]);
	});

	//les clics sur les deux boutons d'actions
	$('form button').click(function(){
		var section = '';
		var $parent = $(this).parent();

		while(!$parent.hasClass('form')){
			$parent = $parent.parent();
		}

		_gaq.push(['_trackEvent', 'formulaire', 'clickSubmit' + $parent.attr('data--ga')]);
	});

	//les clics sur tous les liens sortants (flicker, twitter ,pinterest etc ) du footer
	$('.linktomainsite a').click(function(){
		_gaq.push(['_trackEvent', 'link', 'clickOut', $(this).attr('href')]);
	});

	$('.footer a').click(function(){
		_gaq.push(['_trackEvent', 'link', 'clickOut', $(this).attr('href')]);
	});
});

window.fbAsyncInit = function(){
	FB.Event.subscribe('edge.create', _gaTrackFbLike);
	FB.Event.subscribe('edge.remove', _gaTrackFbUnlike);
}

var _gaTrackFbLike = function gaTrackFbLike(){
	_gaq.push(['_trackEvent', 'facebook', 'Like']);
}

var _gaTrackFbUnlike  = function gaTrackFbUnlike(){
	_gaq.push(['_trackEvent', 'facebook', 'Unlike']);
}