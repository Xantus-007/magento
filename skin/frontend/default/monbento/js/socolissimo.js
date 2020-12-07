/**
 * Addonline_SoColissimo
 * 
 * @category    Addonline
 * @package     Addonline_SoColissimo
 * @copyright   Copyright (c) 2011 Addonline
 * @author 	    Addonline (http://www.addonline.fr)
 */

/**
 * Fonction startWith sur l'objet String de javascript
 */
String.prototype.startWith = function(t, i) { if (i==false) { return
(t == this.substring(0, t.length)); } else { return (t.toLowerCase()
== this.substring(0, t.length).toLowerCase()); } } 

/**
 * Variables globales
 */

var socolissimoMyPosition;
var socolissimoListRelais=new Array();
var socolissimoMap;
var socolissimoOpenedInfowindow;
var socolissimoRelaisChoisi;

/**
 * Initialisation au chargement de la page
 */
jQuery(function($) {
	
	//Cas du onestep checkout, si on change l'adresse de livraison aprÃ¨s avoir choisi socolissimo
	jQuery('.onestepcheckout-index-index .address-select').live("change", function() {
		if(jQuery('#socolissimo-location').size() <= 0 ){	
			$("#attentionSoColissimo").remove();
			$("label[for=\"billing-address-select\"]").parent().before('<p id="attentionSoColissimo" style="font-weight:bold;color:red;text-align:justify; padding-right:5px;">Suite Ã  la modification de votre adresse et si votre mode de livraison est So Colissimo, veuillez sÃ©lÃ©ctionner votre point de retrait en cliquant sur le mode de livraison.</p>');
		}
	});	
	
	/** 
	 * Sur l'Ã©vÃ©nement change des radios boutons de choix de mode de livraison
	 */
	$("input[id^=\"s_method_socolissimo_\"]").live("click", function() {
		shippingRadioCheck(this);
	});		

	initSocolissimoLogos();
	
});

/**
 * Initialise les logos, descriptions, style sur le radio bouttons socolissimo
 * ceci est fait en javascript pour ne pas surcharger le template available.phtml et ainsi Ã©viter des conflits avec d'autres modules.
 * (appelÃ© au chargement du DOM mais aussi au rechargement ajax (voir Checkout.prototype.setStepResponse dans  socolissimo\additional.phtml)
 */
function initSocolissimoLogos() {
	
	var sets = {};
	
	jQuery("input[id^=\"s_method_socolissimo_\"]").each(function(index, element){
            var $this = jQuery(this);

            if(!jQuery("body").hasClass("onestepcheckout-index-index")) {
                    jQuery(element).parents("dd").addClass("s_method_socolissimo");
            } else {
                    jQuery("input[id^=\"s_method_socolissimo_\"]").parents("dt").addClass("s_method_socolissimo");
                    var dd = jQuery("input[id^=\"s_method_socolissimo_\"]").eq(0).parents("dt").prev().addClass("s_method_socolissimo-title");
            }

            jQuery(element).prop("checked", "");
            var typeSocolissimo =  getTypeSocolissimoFromRadio(jQuery(element), false);
            if (typeSocolissimo) {
                var $radio = jQuery(element);
                var radioParent = jQuery(element).parent();
                var encart = false;

                //console.log(typeSocolissimo);

                switch(typeSocolissimo)
                {
                    case 'domicile':
                        if(!sets['domicile'])
                        {
                            sets['domicile'] = true;
                            encart = Translator.translate('À votre domicile');
                        }

                        break;
                    default:
                        if(!sets['close'])
                        {
                            sets['close'] = true;
                            encart  = Translator.translate('Près de chez vous');
                        }

                        break;
                }

                if(encart)
                {
                    //console.log('ADDING : '+encart);
                    radioParent.prepend('<h3>'+encart+'</h3>');
                }

                if (radioParent.children('img').size() == 0) {
                    var testClass = encart ? 'encart' : '';

                    radioParent.prepend('<img class="socolissimo_pictos ' + testClass + '" src="/skin/frontend/default/monbento/images/socolissimo/big/picto_'+typeSocolissimo+'.png" >');
                    var typeSocolissimoDesc =  getTypeSocolissimoFromRadio(jQuery(element), true);
                    jQuery("#socolissimo_description_"+typeSocolissimoDesc).clone().appendTo(radioParent).attr("style","display:block;");
                    if (typeSocolissimo=='rdv' || (typeSocolissimo=='domicile' && jQuery("input[id^=\"s_method_socolissimo_rdv\"]").length==0)) {
                        //radioParent.addClass("first");
                    }
                }
            }
	});
	if(!jQuery("body").hasClass("onestepcheckout-index-index")) {
		if (jQuery(".s_method_socolissimo").prev().children('img').size() == 0) {
			//jQuery(".s_method_socolissimo").prev().addClass("s_method_socolissimo-title").append('<img src="/skin/frontend/base/default/images/socolissimo/socolissimo.png" >');
		}
	} else {
		if (jQuery(".s_method_socolissimo-title").children('img').size() == 0) {
			//jQuery(".s_method_socolissimo-title").append('<img src="/skin/frontend/base/default/images/socolissimo/socolissimo.png" >');
		}
	}
}

function getTypeSocolissimoFromRadio(radio, forDescription) {
	var shippingMethod = radio.attr("value");
	var typeSocolissimo = shippingMethod.replace("socolissimo_","");
	if (typeSocolissimo.startWith("poste")) {
		if (forDescription) {
			if (typeSocolissimo.startWith("poste_be")) {
				return 'poste_be';
			}
		}
		return 'poste';
	} else if (typeSocolissimo.startWith("cityssimo")) {
		return 'cityssimo';
	} else if (typeSocolissimo.startWith("commercant")){ 
		if (forDescription) {
			if (typeSocolissimo.startWith("commercant_be")) {
				return 'commercant_be';
			}
		}
		return 'commercant';
	} else if (typeSocolissimo.startWith("domicile")) {
		if (forDescription) {
			if (typeSocolissimo.startWith("domicile_sign")) {
				return 'domicile_sign';
			}
		}
		return 'domicile';
	} else if (typeSocolissimo.startWith("rdv")){ 
		return 'rdv';
	} else {
		//Sinon c'est un type de livraison inconnu
		alert("Mauvaise configuration du module Socolissimo : dans le champ configuration le code doit commencer par domicile, domicile_sign, rdv, poste, poste_be, commercant, commercant_be, ou cityssimo");
		return false;
	}
}

function shippingRadioCheck(element) {	
	var socoRadio = jQuery(element);	

	//on affiche le picto de chargement Ã©tape suivante du opc
	jQuery("#shipping-method-please-wait").show();

	//on charge en ajax le layer socolissimo (carte choix relais et/ou saisie numÃ©ro de tÃ©lÃ©phone)
	socoUrl = socolissimoBaseUrl + "selector/type/";
	var typeSocolissimo =  getTypeSocolissimoFromRadio(socoRadio, false);
	if (typeSocolissimo) {
		socoUrl = socoUrl + typeSocolissimo;
	} else {
		return;
	}

	jQuery.ajax({
		url: socoUrl,
		success: function(data){

			//une fois chargÃ©, on cache le picto de chargement, on ouvre un layer et on met de rÃ©sultat dedans:
			jQuery("#shipping-method-please-wait").hide();

			if (jQuery("#socolissimo-hook").size()==0) {
				socoRadio.parent().parent().append("<div id=\"socolissimo-hook\" ></div>");
			}
			jQuery("#layer_socolissimo_wrapper").html(data);
			//on supprime les filtres si le mode de livraison correspondant n'est pas proposÃ©
			if (jQuery("input[id^=\"s_method_socolissimo_poste\"]").length == 0) {
				jQuery("#filtre_poste").remove();
			}
			if (jQuery("input[id^=\"s_method_socolissimo_cityssimo\"]").length == 0) {
				jQuery("#filtre_cityssimo").remove();
			}
			if (jQuery("input[id^=\"s_method_socolissimo_commercant\"]").length == 0) {
				jQuery("#filtre_commercant").remove();
			}
			//on affiche le layer
			if (jQuery("#layer_socolissimo").data("overlay") == undefined) {
				jQuery("#layer_socolissimo").overlay({
					mask: {
						color: '#000', 
						loadSpeed: 200, 
						opacity: 0.5 
					}, 
					load: true,
					closeOnClick: false,
					top: "center",
					onBeforeClose : function(event){
						//si on n'a pas choisi de type de livraison socolissimo, on dÃ©coche le mode de livraison socolissimo 
						var telephoneElt = jQuery("#socolissimo-hook input[name='tel_socolissimo']");
						if (!telephoneElt || telephoneElt.val() == undefined) {
							resetShippingMethod();
						} else {
							var shippingMethod = jQuery("input[name='shipping_method']:checked").val();
							if (shippingMethod.startWith("socolissimo_poste") || shippingMethod.startWith("socolissimo_commercant") || shippingMethod.startWith("socolissimo_cityssimo")) {
								var relaisElt = jQuery("#socolissimo-hook input[name='relais_socolissimo']");
								if (!relaisElt || relaisElt.val() == undefined) {
									resetShippingMethod();
								}
							} else {
                                                            jQuery("#socolissimo-hook input[name='relais_socolissimo']").remove();
                                                        }
						}
					},
					fixed: false
				});
			} else {
				jQuery("#layer_socolissimo").data("overlay").load();
			}

			if (typeSocolissimo.startWith("poste") || typeSocolissimo.startWith("cityssimo") || typeSocolissimo.startWith("commercant")){ 

				//initialisation des champs "adresse" du layer qui peuvent Ãªtre vides dans le cas du onestepcheckout avec un nouveau client sans adresse enregistrÃ©e
				if (jQuery("#socolissimo_postcode").val() == '') {
					jQuery("#socolissimo_street").val(jQuery("input[name='billing[street][]']").val());
					jQuery("#socolissimo_postcode").val(jQuery("input[name='billing[postcode]']").val());
					jQuery("#socolissimo_city").text(jQuery("input[name='billing[city]']").val());
					jQuery("#socolissimo_country").val(jQuery("select[name='billing[country_id]']").find("option:selected").val());
				}
				
				//initialisation de la liste dÃ©roulantes des villes "personnalisÃ©e"
				jQuery("#socolissimo_city_select").change(function() {
					jQuery(this).prevAll("span").eq(0).text(jQuery(this).find("option:selected").text());
				});
				//initilisation du rechargement de la liste dÃ©roulante des villes 
				jQuery("#socolissimo_postcode").change(function(){
					var postcode = this.value; 
					var country = jQuery('#socolissimo_country').val();
					socoUrlCities = socolissimoBaseUrl + "getcitynamebyzipcode/country/"+country+"/zipcode/"+postcode;					
					jQuery.ajax({						
						url: socoUrlCities,
						dataType:'jsonp',
						jsonpCallback: 'reloadCities',
						success: function(json){
							var options = '<option selected >Choisissez une commune</option>';
							for (i=0; i<json.postalCodes.length; i++){ 
								commune = json.postalCodes[i].placeName;
								options += '<option value="' + commune + '">' + commune + '</option>';
							}
							jQuery("#socolissimo_city_select").html(options);
							jQuery("#socolissimo_city").text("Choisissez une commune");
						}
					});
				});

				//on localise l'adresse qui est prÃ©chargÃ©e (adresse de livraison par dÃ©faut du compte client) 
				if (jQuery("#socolissimo_postcode").val() != "") {
					geocodeAdresse();
				}

			}					


		},
		error : function(jqXHR, textStatus){
			alert("Erreur de chargement des donnÃ©es "+textStatus);
		}
	});



}

function resetShippingMethod() {
	jQuery("input[name='shipping_method']:checked").prop("checked","");
}

function geocodeAdresse() {

	if (jQuery("#socolissimo_city_select option").length > 0 && jQuery("#socolissimo_city_select")[0].selectedIndex == 0) {
		alert("Veuillez sélectionner une commune");
		return;
	}
	if (jQuery("#socolissimo_postcode").val() == "") {
		alert("Veuillez saisir un code postal");
		return;
	}
	
	if ((typeof google) != "undefined") {
		var geocoder = new google.maps.Geocoder();
		var searchAdress = jQuery("#socolissimo_street").val() + ' ' + jQuery("#socolissimo_postcode").val() + ' ' + jQuery("#socolissimo_city").text() + ', ' + jQuery('#socolissimo_country').val();
		//alert('Search adresse : ' + searchAdress);
		geocoder.geocode({'address': searchAdress}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
	 			socolissimoMyPosition = results[0].geometry.location;
	 			//on met Ã  jour la carte avec cette position
	 			changeMap();
			} else {
				alert('Adresse invalide '+searchAdress);
			}
	    });
	} else {
		alert("GÃ©olocalisation de l'adresse impossible, vÃ©rifiez votre connexion internet (Google inaccessible).");
		//pour tester quand mÃªme sans gÃ©olocalisation :
		var socolissimoMyPositionClass = Class.create();
		socolissimoMyPositionClass.prototype.lat = function() { return 5; }
		socolissimoMyPositionClass.prototype.lng = function() { return 60; }
		socolissimoMyPosition = new socolissimoMyPositionClass();
		changeMap();
	}
}

function changeMap() {
	if (socolissimoMyPosition!=undefined) {
		loadListeRelais();
	}
}

function loadListeRelais() {
	jQuery(".loader-wrapper").fadeTo(300, 1);
	socoUrl = socolissimoBaseUrl + "listrelais?"
	jQuery("#layer_socolissimo input:checkbox").each(function(index, element){
		check = jQuery(element);
		socoUrl = socoUrl + check.val() + "=" + check.is(":checked") + "&";
	});
	socoUrl = socoUrl + "adresse=" + jQuery("#socolissimo_street").val() + "&zipcode=" + jQuery("#socolissimo_postcode").val()+ "&ville=" + jQuery.trim(jQuery("#socolissimo_city").text()) + "&country=" + jQuery("#socolissimo_country").val();
	socoUrl = socoUrl + "&latitude=" + socolissimoMyPosition.lat() + "&longitude=" + socolissimoMyPosition.lng();
	socoUrl = encodeURI(socoUrl);
	jQuery.getJSON( socoUrl, function(response) {
		if (!response.error) {
			socolissimoListRelais = response.items;
			jQuery("#adresses_socolissimo").html(response.html);
		} else {
			socolissimoListRelais = new Array();
			jQuery("#adresses_socolissimo").html('');
			alert(response.error);
		}
		showMap();
		jQuery(".loader-wrapper").fadeTo(300, 0).hide();
	});
	
	
}

function showMap() {
	if ((typeof google)!="undefined") {
		var myOptions = {
		    	zoom: 15,
		    	center: socolissimoMyPosition,
		    	mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		socolissimoMap = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		iconUrl = jQuery("#layer_socolissimo .ligne1").css("background-image");
		iconMatch = iconUrl.match("url\\(\"(.*)\"\\)");
		if (iconMatch == null) {
			//chrome
			iconMatch = iconUrl.match("url\\((.*)\\)");
		}
		iconUrl = iconMatch[1];
		var marker = new google.maps.Marker({
		    map: socolissimoMap, 
		    position: socolissimoMyPosition,
		    icon : iconUrl
		});
		var init = false;
		google.maps.event.addListener(socolissimoMap, 'tilesloaded', function () {
			if (!init){			
				for (icounter=0; icounter<socolissimoListRelais.length; icounter++) {					
					relaisSocolissimo = socolissimoListRelais[icounter];	
					var relaisPosition =  new google.maps.LatLng(relaisSocolissimo.latitude, relaisSocolissimo.longitude);				
					marker = new google.maps.Marker({
					    map: socolissimoMap, 
					    position: relaisPosition,
					    title : relaisSocolissimo.libelle,
					    icon : relaisSocolissimo.urlPicto
					});								
					if (!socolissimoMap.getBounds().contains(relaisPosition)){
						newBounds = socolissimoMap.getBounds().extend(relaisPosition);
						socolissimoMap.fitBounds(newBounds);
					}								
					infowindow=infoBulleGenerator(relaisSocolissimo);				
					attachClick(marker,infowindow, icounter);								
				}			
			}		
			init=true;
		});
                
                jQuery(window).on('resize', function(){
                    socolissimoMap.setCenter(socolissimoMyPosition);
                });
	}
}

//gÃ©nÃ©rateur d'infobulle
function infoBulleGenerator(relaisSocolissimo) {
	contentString = '<div class="adresse">'+
    '<b>'+relaisSocolissimo.libelle+ '</b><br/>'+
    '<b>'+relaisSocolissimo.adresse+ ' ' + relaisSocolissimo.code_postal + ' ' + relaisSocolissimo.commune + '</b><br/>';
    if (relaisSocolissimo.conges_total) {
		contentString += '<b>En congÃ©s </b>';
	    contentString += '<p>';
	} else {
		contentString += '<b>Horaires d\'ouverture :</b><br/>';
		if (relaisSocolissimo.deb_periode_horaire) {
			contentString += '(valables du ' + relaisSocolissimo.deb_periode_horaire + ' au '+ relaisSocolissimo.fin_periode_horaire +')';
		}
	    contentString += '<p>';
	    if (relaisSocolissimo.horaire_lundi!='00:00-00:00 00:00-00:00') {contentString += '<b>Lundi:</b> '+ relaisSocolissimo.horaire_lundi + '<br/>'}
	    if (relaisSocolissimo.horaire_mardi!='00:00-00:00 00:00-00:00') {contentString += '<b>Mardi:</b> '+ relaisSocolissimo.horaire_mardi + '<br/>'}
	    if (relaisSocolissimo.horaire_mercredi!='00:00-00:00 00:00-00:00') {contentString += '<b>Mercredi:</b> '+ relaisSocolissimo.horaire_mercredi + '<br/>'}
	    if (relaisSocolissimo.horaire_jeudi!='00:00-00:00 00:00-00:00') {contentString += '<b>Jeudi:</b> '+ relaisSocolissimo.horaire_jeudi + '<br/>'}
	    if (relaisSocolissimo.horaire_vendredi!='00:00-00:00 00:00-00:00') {contentString += '<b>Vendredi:</b> '+ relaisSocolissimo.horaire_vendredi + '<br/>'}
	    if (relaisSocolissimo.horaire_samedi!='00:00-00:00 00:00-00:00') {contentString += '<b>Samedi:</b> '+ relaisSocolissimo.horaire_samedi + '<br/>'}
	    if (relaisSocolissimo.horaire_dimanche!='00:00-00:00 00:00-00:00') {contentString += '<b>Dimanche:</b> '+ relaisSocolissimo.horaire_dimanche+ '<br/>'}
	}
    if (relaisSocolissimo.parking==1) { 
    	contentString += '<img src="/skin/frontend/base/default/images/socolissimo/picto_parking.jpg" />'; 
    }
    if (relaisSocolissimo.manutention==1) { 
    	contentString += '<img src="/skin/frontend/base/default/images/socolissimo/picto_manutention.jpg" />'; 
    }
    if (relaisSocolissimo.indicateur_acces==1) { 
    	contentString += '<img src="/skin/frontend/base/default/images/socolissimo/picto_mobilite_reduite.jpg" />'; 
    }
    if (relaisSocolissimo.fermetures.totalRecords>0) { contentString += '<br/><b>Periodes de fermeture :</b>'; 
		for (i=0; i<relaisSocolissimo.fermetures.items.length; i++) {
			fermeture = relaisSocolissimo.fermetures.items[i];
			datedu = fermeture.deb_periode_fermeture;
			dateau = fermeture.fin_periode_fermeture;
			contentString += '<br/>du ' + datedu.substring(8,10) + '/' + datedu.substring(5,7) + '/' + datedu.substring(0,4) + ' au ' + dateau.substring(8,10) + '/' + dateau.substring(5,7) + '/' + dateau.substring(0,4);
		}
	}
    
    contentString += '</p></div>';
    contentString = contentString.replace(new RegExp(' 00:00-00:00', 'g'),''); //on enlÃ¨ve les horaires de l'aprÃ¨s midi si ils sont vides

	infowindow = new google.maps.InfoWindow({
		content: contentString
	});
	
	return infowindow;
}

function attachClick(marker,infowindow, index){
	//Clic sur le relais dans la colonne de gauche
	jQuery("#point_retrait_"+index).click(function() {
			//fermer la derniere infobulle ouverte
			if(socolissimoOpenedInfowindow) {
				socolissimoOpenedInfowindow.close();
		    }
			//ouvrir l'infobulle
		   infowindow.open(socolissimoMap,marker);
		   socolissimoOpenedInfowindow=infowindow;
		   
		});
		
	//Clic sur le marqueur du relais dans la carte
	google.maps.event.addListener(marker, 'click', function() {
			//fermer la derniere infobulle ouverte
			if(socolissimoOpenedInfowindow) {
				socolissimoOpenedInfowindow.close();
		    }
			//ouvrir l'infobulle
		   infowindow.open(socolissimoMap,marker);
		   socolissimoOpenedInfowindow=infowindow;
		   
		});
}

function choisirRelais(index) {
	
	socolissimoRelaisChoisi = socolissimoListRelais[index];
	jQuery("#socolissimo-hook").html('<input type="hidden" name="relais_socolissimo" value="'+socolissimoRelaisChoisi.identifiant+'" />'+
									'<input type="hidden" name="reseau_socolissimo" value="'+socolissimoRelaisChoisi.code_reseau+'" />');
	
	jQuery("input[id^=\"s_method_socolissimo_\"]").each(function(index, element){
		//on sÃ©lectionne le bon radio, si on a changÃ© de type de relais sur la carte, et on change le texte du numÃ©ro de tÃ©lÃ©phone
		var radio = jQuery(element);
		var types = new Array('poste','commercant','cityssimo');
		var len=types.length;
		for (var index=0; index<len; index++) {
			var socolissimoType = types[index];
			if (radio.val().startWith("socolissimo_"+socolissimoType)) {
				if (socolissimoRelaisChoisi.type==socolissimoType) {
					radio.prop("checked", "checked");	//on utilise prop au lieu de attr pour que le radio soit bien mis Ã  jour
					jQuery("#socolissimo-telephone span."+socolissimoType).attr("style","display:block;");
				} else {
					radio.prop("checked", "");	//on utilise prop au lieu de attr pour que le radio soit bien mis Ã  jour
					jQuery("#socolissimo-telephone span."+socolissimoType).attr("style","display:none;");
				}
			}
		}
	});
	
	jQuery("#socolissimo-map").hide();
	jQuery("#socolissimo-telephone").show();

	var $modal = jQuery('#layer_socolissimo');
    $modal.css({
        'top' : '50%',
        'margin-top' : -$modal.height()/2
    });
    jQuery('body').animate({
    	scrollTop: $modal.offset().top - $modal.height()
    }, 500);
	
	return;
}

function validerTelephone() {
    /*countryValidator = jQuery("select[name='shipping[country_id]']").find("option:selected").val();
    if(countryValidator != 'FR' || (socolissimoTelephoneForm.validator && socolissimoTelephoneForm.validator.validate())){*/
    	var telephone = jQuery("#socolissimo-telephone input[name='tel_socolissimo']").val();
    	jQuery("#socolissimo-hook").append('<input type="hidden" name="tel_socolissimo" value="'+telephone+'" />');
    	jQuery("#layer_socolissimo").data("overlay").close();
    //}
    return false;
}

/** ajout de la fonction de validation numÃ©ro de tÃ©lÃ©phone portable */
Validation.add('valid-telephone-portable', 'Veuillez saisir un numÃ©ro de tÃ©lÃ©phone portable correct', function(v) {
    return true;
    return ((/^0([1-9])\d{8}$/.test(v)) || (/^\+3\d{10}$/.test(v)) && !(/^0([1-9])(0{8}|1{8}|2{8}|3{8}|4{8}|5{8}|6{8}|7{8}|8{8}|9{8}|12345678)$/.test(v)) || (/^(\+|00)[1-9][0-9]\d{10}$/.test(v)));
});

Validation.add('valid-telephone-portable-belgique', 'Veuillez saisir un numÃ©ro de tÃ©lÃ©phone portable correct', function(v) {
    return true;
	//Pour les destinataires belges, le numÃ©ro de tÃ©lÃ©phone portable doit commencer par le caractÃ¨re + suivi de 324, suivi de 8 chiffres
	return (/^\+324\d{8}$/.test(v) && !(/^\+324(0{8}|1{8}|2{8}|3{8}|4{8}|5{8}|6{8}|7{8}|8{8}|9{8}|12345678)$/.test(v)));
});
