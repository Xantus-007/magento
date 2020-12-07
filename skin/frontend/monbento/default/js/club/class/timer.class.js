var Club_Timer;

(function($){
    Club_Timer = Klass.create({
        durationLeft:0,
        isCanceled:false,
        timerID:null,
        momentum:100,

        TIMER_COMPLETE:'timerEvent',
        TIMER_CANCEL:'timerStop',

        start:function(duration){
            this.isCanceled = false;
            this.durationLeft = parseInt(duration);
            var _this = this;

            var handler = function(){
                if(_this.durationLeft <= 0 || _this.isCanceled)
                {
                    if(_this.isCanceled)
                    {
                        _this.cancelHandler();
                    }
                    else
                    {
                        _this.completeHandler();
                    }
                }

                _this.durationLeft -= _this.momentum;
            }

            if(!this.timerID)
            {
                this.timerID = setInterval(handler, _this.momentum);
            }
        },

        completeHandler:function(){
            this.durationLeft = 0;
            clearInterval(this.timerID);
            this.timerID = null;
            $(this).trigger(this.TIMER_COMPLETE)
        },

        cancel:function(){
            this.isCanceled = true;
        },

        cancelHandler:function(){
            this.durationLeft = 0;
            clearInterval(this.timerID);
            this.timerID = null;
            $(this).trigger(this.TIMER_CANCEL);
        }
    })
})(jQuery);