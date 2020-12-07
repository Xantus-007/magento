document.observe("dom:loaded", function() {
  if (document.getElementById("productImgDefaut") && document.getElementById("attribute76") ) {
    // On document ready hide all images first
    $("productImgDefaut").style.display='block';
    $("attribute76").observe('change', function(event){
      // Hide all images on slect element change action
      $$('#imageShowcase img').each(function(elem){
        elem.style.display='none';
      });
				
      for(var i=0; i<this.options.length; i++){
        if(this.options[i].selected){
          var optionValue = this.options[i].text;
          break;
        }
      }
      
      if (!$("productImg" + optionValue.replace(/ /g,""))) {
        $("productImgDefaut").style.display='block';
      } else {
        $("productImg" + optionValue.replace(/ /g,"")).style.display='block';
      }
    });
  }
  if ($$('.more-views ul li a')) {
		countImages = 0;
    $$('.more-views ul li a').each(function(item) {
		 var myView = item;
		 myView.countImages = countImages;
      Event.observe(item, 'mouseover', function(event){
        Event.stop(event);
				countImages2 = 0;
        $('imageShowcase').childElements().each(function(item) {
			    if (myView.countImages==countImages2) item.childElements()[0].style.display = 'block';
				  else item.childElements()[0].style.display = 'none';
			    countImages2++;
        });
      });
			countImages++;
    }.bind(this));
		
	}
});