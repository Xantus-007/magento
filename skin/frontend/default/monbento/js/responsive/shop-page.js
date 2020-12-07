jQuery(document).ready(function($){
    if ($('#listCatShop').length > 0) {
        var $bigBloc = $('.big-bloc'),
            $prevBigBloc = $bigBloc.prev('.columns'),
            $nextBigBloc = $bigBloc.next('.columns');        

        enquire.register("screen and (max-width:40em)", {
            match: function() {
                $bigBloc.insertAfter($nextBigBloc);
            },
            unmatch: function() {
                $bigBloc.insertAfter($prevBigBloc);
            }
        });
    }
});