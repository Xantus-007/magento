/*

A REFAIRE EN CSS PUR

(function($){
    $(document).ready(function(){
        $('.elementCell').mouseenter(function(){
            var $this = $(this);
            var descr = $this.find('.description');
            
            descr.stop().animate({
                bottom:0
            }, 'fast')
        });
        
        $('.elementCell').mouseleave(function(){
            var $this = $(this);
            var descr = $this.find('.description');
            
            descr.stop().animate({
                bottom:'-75px'
            }, 'fast');
        })
        
    })
})(jQuery);

*/

