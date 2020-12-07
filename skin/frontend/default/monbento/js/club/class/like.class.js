var Club_Like;

(function($){
Club_Like = Klass.create({
    $elements:null,

    init: function() 
    {
        this.$elements = $('.miniLike');
        this.refresh();
    },

    refresh:function()
    {
        var self = this;
        
        this.$elements.click(function(){
            if($(this).hasClass(Globals.LOGGED_IN_CLASS))
            {
                var $this = $(this);
                var id = self.getIdFromClass($this);

                if(id > 0)
                {
                    $.ajax({
                        url:Globals.LIKE_URL,
                        data:{
                            id:id
                        },
                        dataType:'json',
                        success:function(result){
                            self.likeSuccess(result, $this);
                        }
                    });
                }
            }

            return false;
        });
    },

    likeSuccess: function(result, $element)
    {
        if(result.isValid)
        {
            $element.find('img').attr('src', Globals['LIKE_IMAGE_'+result.action]);
            $element.find('.count').text(result.newCount);
        }
    },

    likeError: function()
    {

    },

    getIdFromClass: function($el)
    {
        var preg = /element_([^ ]*)/i;
        var res = preg.exec($el.attr('class'));
        var id = res[1];

        return id;
    }
});
})(jQuery);