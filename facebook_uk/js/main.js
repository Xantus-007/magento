var bFan = true ;
var id_user = 0 ;
var token = '' ;
function loginSave()
{
	FB.login(startSave, {perms:'publish_stream,email,user_birthday'}) ;
}

function startSave(response)
{
	if (response.session)
	{
	    if (response.perms)
	    {
	    	id_user = response.session.uid ;
	    	token = response.session.access_token ;
	    	saveUser() ;
	    }
	}
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
		$('#notice').html('<b>Thanks, you will receive a email when a new contest starts</b>') ;
	});
}

function login()
{
	FB.login(start, {perms:'publish_stream,email,user_birthday'}) ;
}

function beFan()
{
	alert('You have to like the page (on the top)') ;
}

function start(response)
{
	if (response.session)
	{
	    if (response.perms)
	    {
	    	id_user = response.session.uid ;
	    	token = response.session.access_token ;
	    	saveAnswer() ;
	    }
	}
}

function answer()
{
	if(id_user)
	{
		saveAnswer() ;
	}else{
		login() ;
	}
}

function saveAnswer()
{
	var notif = $('#notif').val() ;
	var wall = $('#wall').val() ;
	
	$.post('pages/game.php', {id_user:id_user,notif:notif,wall:wall,signed_request:signed_request,token:token}, function(data)
	{
		$('#content').html(data) ;
	});
}

function inviteFriends(lot)
{
	FB.ui({ method: 'apprequests',  title : 'Invite your friends, if they win you win', message: 'Try your luck to win 1 ' + lot + ', I just tried and it is for free. Click to accept.'});
	
	if($('#inviteFriends'))
	{
		$('#inviteFriends').html("There's not limitation number, you can invite even more friends!") ;
	}
}

function publishWin(id_lot, nb, lot)
{
	var params = {
		method: 'feed',
		name:'I Won ' + lot + ', Try your luck now !',
		picture:site + 'lots/' + id_lot + '.jpg',
		link:url_tab,
		caption:'MonBento.com',
		description:'MonBento.com enables you to win ' + nb + ' ' + lot + ', Try your luck now',
		display:'popup'
	};
	
	FB.ui(FB.JSON.flatten(params)) ;
}

function publishFriend(id_friend, id_lot, nb, lot)
{
	var params = {
		method: 'feed',
		to:id_friend,
		name:'We both won 1 ' + lot + '',
		picture:site + 'lots/' + id_lot + '.jpg',
		link:url_tab,
		caption:'MonBento.com',
		description:'MonBento.com enables you to win ' + nb + ' ' + lot + ', Try your luck now',
		display:'popup'
	};
	
	FB.ui(FB.JSON.flatten(params)) ;
}

function showFaq()
{
	var left = (screen.width/2)-(700/2);
	var top = (screen.height/2)-(400/2);
	
	window.open('faq.php', 'faq', 'scrollbars=0,width=700,height=400,top='+top+',left='+left) ;
}

function showReglement()
{
	var left = (screen.width/2)-(700/2);
	var top = (screen.height/2)-(400/2);
	
	window.open('reglement.php', 'faq', 'scrollbars=1,width=700,height=400,top='+top+',left='+left) ;
}


function checkBox(item)
{
	var input = '#' + item.attr('for') ;
	
	if($(input).val() == 0)
	{
		$(input).val(1) ;
		item.addClass('checkbox_checked') ;
	}else{
		$(input).val(0) ;
		item.removeClass('checkbox_checked') ;
	}
}

$(document).ready(function() {
$('.checkbox').each(function (item){
	$(this).bind('click', delegate($(this), checkBox));
}) ;
}) ;

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