(function($){
    $(document).ready(function(){
        $('.clubElementFormButtonHide button').click(function(){
            $(this).fadeOut('fast');
            $('.clubElementForm').slideDown('slow');
            return false;
        });
        
        if (typeof Globals !== 'undefined')
        {
            $('#postElementForm input[type!=submit],textarea,select').not('.noUniform').uniform({
                fileButtonHtml:Globals.translations.chooseFile,
                fileDefaultHtml:Globals.translations.fileDefault
            });
        }
    })
})(jQuery);
