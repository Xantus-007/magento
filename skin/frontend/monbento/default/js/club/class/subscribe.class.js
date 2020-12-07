var Club_Subscribe;

(function($){
Club_Subscribe = Klass.create({
    $elements:null,
    
    init: function()
    {
        this.$elements = $('button[class*=customer_]');
        var self = this;
        
        this.$elements.each(function(id, val){
            var $button = $(val);

            $button.mouseenter(function(){
                var $this = $(this);

                if($this.hasClass('subscribed'))
                {
                    $this.text(Globals.translations.unsubscribe)
                }
                
                if($this.hasClass('subscribe'))
                {
                    $this.text(Globals.translations.subscribe)
                }
            });

            $button.mouseleave(function(){
                var $this = $(this);
                if($this.hasClass('subscribed'))
                {
                    $this.text(Globals.translations.subscribed)
                }
                
                if($this.hasClass('subscribe'))
                {
                    $this.text(Globals.translations.unsubscribed)
                }

            })
        })
        
        this.refresh();
    },
    
    refresh: function()
    {
        var self = this;
        this.$elements.click(function(){
            
            var $this = $(this);
            var id = self.getIdFromClass($this);
           
            if(id > 0)
            {
                $.ajax({
                    url:Globals.SUBSCRIBE_URL,
                    data: {
                        id:id
                    },
                    dataType:'json',
                    success: function(result){
                        self.subscribeSuccess(result, $this);
                    }
                })
            }
        });
    },
    
    subscribeSuccess: function(result, $element)
    {
        if(result.isValid)
        {
            if(result.action == 1)
            {
                $element.removeClass('subscribe').addClass('subscribed');
            }
            else
            {
                $element.removeClass('subscribed').addClass('subscribe');
            }
            
            $element.text(result.label);
        }
    },
    
    getIdFromClass: function($el)
    {
        var preg = /customer_([^ ]*)/i;
        var res = preg.exec($el.attr('class'));
        var id = res[1];

        return id;
    }
});
})(jQuery);