var bFan = 0 ;
var aFriendsMale = [] ;
var aFriendsPrevious = [] ;
var id_user = 0 ;
var token = '' ;
var nav_version = parseInt($.browser.version) ;
var parrain = 0 ;
var sFname = '' ;
var nbDays = 14 ;

function loginSave()
{
	FB.login(startSave, {scope:'email,user_birthday'}) ;
	
	trackPreview('/login') ;
}

function startSave(response)
{
	if (response.authResponse)
	{
    	id_user = response.authResponse.userID ;
    	token = response.authResponse.accessToken ;
    	saveUser() ;
    }
}

function loadPage(page, params)
{
	var oParams = params ? params : {} ;
	oParams.signed_request = signed_request ;
	
	$.post('pages/' + page + '.php', oParams, function(data)
	{
		$('#content').html(data) ;
		
		FB.Canvas.setSize({height: $('#main').height()}) ;
		FB.Canvas.scrollTo(0, 0);
	});
	
	trackPreview('/' + page) ;
}

function launch()
{
	loadPage('fan') ;
}

function showKidsOption()
{
	loadPage('kids') ;
}

function save()
{
	if(id_user)
	{
		saveUser() ;
	}else{
		loginSave() ;
	}
}

function saveUser()
{
	$.post('pages/save.php', {id_user:id_user,signed_request:signed_request,token:token}, function(data)
	{
		alert(text_email_saved) ;
	});
}

function login()
{
	if(id_user)
	{
		FB.login(start, {scope:'publish_actions'}) ;
	}else{
		FB.login(start, {scope:'email,user_birthday'}) ;
	}
	
	trackPreview('/login') ;
}

function start(response)
{
	if (response.authResponse)
	{
    	id_user = response.authResponse.userID ;
    	token = response.authResponse.accessToken ;
    	savePlay() ;
	}
}

function play()
{
	if($('#optin1').val() == -1 || $('#optin2').val() == -1)
	{
		alert(text_optins_alert) ;
	}else{
		login() ;
	}
}

function savePlay()
{
	$.post('pages/game.php', {id_user:id_user,optin1:$('#optin1').val(),optin2:$('#optin2').val(),signed_request:signed_request,token:token,pid:parrain}, function(data)
	{
		$('#content').html(data) ;
	});
	
	trackPreview('/play') ;
}

var bReinvite = false ;
function inviteFriends()
{
	bReinvite = false ;
	
	FB.ui({ method: 'apprequests', exclude_ids:aFriendsMale, title : text_invite_title, message: text_invite_msg}, onInvitedFriends);
	
	trackPreview('/game/inviteFriends/dialog') ;
}

function inviteFriendsPrev(lot)
{
	bReinvite = true ;
	
	FB.ui({ method: 'apprequests', to:aFriendsPrevious, title : text_invite_title, message: text_invite_msg}, onInvitedFriends);
	
	trackPreview('/game/invitePreviousFriends/dialog') ;
}

function onInvitedFriends(response)
{
	if(response && response['to'])
	{
		$.post('pages/save_invitations.php', {id_user:id_user,request_id:response['request'],friends:response['to']});
		
		if(bReinvite)
		{
			$('.button_invite2').css('display', 'none') ;
			trackPreview('/game/invitePreviousFriends/invited') ;
			alert(text_invite_alert2) ;
		}else{
			trackPreview('/game/inviteFriends/invited') ;
			alert(text_invite_alert) ;
			
		}
		
		if($('.option_kids').length)
		{
			$('.option_kids').css('display', 'block') ;
		}
		
		jQuery.merge(aFriendsMale, response['to']) ;
	}
}

function shareFriends(url)
{
	var url = "http://www.facebook.com/share.php?u=" + encodeURIComponent(url) + "&ref=share" ;
	
	var left = (screen.width/2)-(700/2);
	var top = (screen.height/2)-(400/2);
	
	window.open(url, 'facebook', 'scrollbars=0,width=700,height=400,top='+top+',left='+left) ;
	
	trackPreview('/game/shareFriends/dialog') ;
}

function sendFriends(url)
{	
	var params = {
		method: 'send',
		link:url
	};
	
	FB.ui(params, onPublishedSend) ;
	
	trackPreview('/game/sendFriends/dialog') ;
}

function onPublishedSend(response)
{
	if(response)
	{
		if($('.option_kids').length)
		{
			$('.option_kids').css('display', 'block') ;
		}
		
		trackPreview('/game/sendFriends/published') ;
	}
}

function publishWin(id_lot, nb, lot)
{
	var params = {
		method: 'feed',
		name:text_publish_win_title,
		picture:share_img,
		link:url_tab,
		ref:'publication_win',
		caption:text_contest,
		description:text_publish_win_desc
	};
	
	FB.ui(params) ;
	
	trackPreview('/game/publishWin/dialog') ;
}

function onPublishedWin(response)
{
	if(response)
	{
		trackPreview('/game/publishWin/published') ;
	}
}


function updateState(n, v)
{
	var bYes = v == 1 ;
	
	if(bYes)
	{
		$('#fan_optin' + n + ' .text_none').css('display','none') ;
		$('#fan_optin' + n + ' .text_yes').css('display', 'block') ;
	}else{
		$('#fan_optin' + n).css('display','none') ;
		//$('.fan_bloc2 b').css('display','none') ;
	}
	$('#optin' + n).val(v) ;
}

function showPopup(page)
{
	var width = 490 ;
	var height = 300 ;
	
	$('#smoke').css('display', 'block');
	$('#smoke').css('height', $(document).height());
	$('#popup').css('display', 'block');
	
	$('#popup').html('<div class="loader"></div>') ;
	$.post('pages/' + page + '.php?signed_request=' + signed_request, function(data)
	{
		$('#popup').html(data) ;
		
		FB.XFBML.parse() ;
	});
}

function closePopup()
{
	$('#smoke').css('display', 'none');
	$('#popup').css('display', 'none');
}

function trackPreview(url)
{
	if(isdefined('_gaq')) _gaq.push(['_trackPageview', url]);
}

function delegate(obj, func)
{	
	var args = [obj] ;
	
	for(var i = 2 ; i < arguments.length ; i++)
	{
		args.push(arguments[i]) ;
	}
	
	return function() { return func.apply(null, args); };
}

function isdefined( variable)
{
    return (typeof(window[variable]) == "undefined")?  false: true;
}