(function ($) {
    $(document).ready(function(){
        $('button').each(function(id, button){
            var $button = $(button);
            var content = $button.text();
            
            $button.find('span').remove();
            
            $button.text(content);
        })
        
        //$("select, input:not([id^=agreement]), .controls a").uniform({
        $("select, input, .controls a").not('.noUniform').uniform({
            fileButtonHtml: translations.chooseFile, 
            fileDefaultHtml: translations.fileDefault
        });
        $('input[type=file]').each(function(id, input){
            var $input = $(input);
            if(!uploadAllowed)
            {
                $input.parent().replaceWith('<div>' + translations.uploadDisabled + '</div>')
            }
        })
        
        //Patch for agreement click on ios
        $('.agree label').click(function(){
            //console.log('OK');
        })
    })
}(jQuery));