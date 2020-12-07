/**
 *	Basic Slideshow App
 *	
 *	Plagerised from Tom Doyle by Isotope Communications Ltd, Dec 2008
 *	http://www.tomdoyletalk.com/2008/10/28/simple-image-gallery-slideshow-with-scriptaculous-and-prototype/
 *	
 *	Published [16/12/08]:
 *	http://www.icommunicate.co.uk/articles/all/simple_slide_show_for_prototype_scriptaculous_38/	
 *
 *	Changes: Basically made it an object so that you can run multiple instances and so that
 *			 it doesn't get interfered with by other scripts on the page.
 *	
 *			 Have also added a few things, like "Captions" and the option of changing the
 *			 effects..
 *
 *	[24/01/10]: J. Johanson, fixed "this.play.bind" bug
 *	[27/01/10]: J. Johanson, added goTo method
 *	[27/01/10]: J. Johanson, added runrandom flag
 *	[02/02/10]: J. Johanson, fixed bug where image goes blank when goNext, goPrevious, goTo called during fade
 *
 *	Example:
 *			Event.observe(window, 'load', function(){
 *				oMySlides = new iSlideShow({
 *					autostart 	: true		// optional, boolean (default:true)
 *					runrandom 	: false		// optional, boolean (default:false)
 *					start		: 0,	 	// optional, slides[start] (default:0) 
 *					wait 		: 4000, 	// optional, milliseconds (default:4s)
 *					duration	: 0.5, 		// optional, seconds (default:0.5s)
 *					slides 		: [
 *						'image-div-a', 
 *						'image-div-b', 
 *						'image-div-c', 
 *						'image-div-d' 
 *					],
 *					counter		: 'counter-div-id', // optional...
 *					caption 	: 'caption-div-id', // optional... 
 *					playButton	: 'PlayButton', 	// optional (default:playButton)
 *					pauseButton	: 'PauseButton', 	// optional (default:PauseButton)
 *				});
 *			});
 *
 *			To start the slideshow:
 *				oMySlides.startSlideShow();
 *
 *			To skip forward, back, stop:
 *				oMySlides.goNext();
 *				oMySlides.goPrevious();
 *				oMySlides.stop();
 *				oMySlides.goTo(iImageIdNext);
 */

var iSlideShow = new Class.create();

iSlideShow.prototype = {
	
	initialize : function (oArgs){
		this.wait 			= oArgs.wait ? oArgs.wait : 4000;
		this.start 			= oArgs.start ? oArgs.start : 0;
		this.duration		= oArgs.duration ? oArgs.duration : 0.5;
		this.autostart		= (typeof(oArgs.autostart)=='undefined') ? true : oArgs.autostart;
		this.runrandom		= (typeof(oArgs.runrandom)=='undefined') ? false : oArgs.runrandom;
		this.slides 		= oArgs.slides;
		this.counter		= oArgs.counter;
		this.caption		= oArgs.caption;
		this.playButton		= oArgs.playButton ? oArgs.playButton : 'PlayButton';
		this.pauseButton	= oArgs.pauseButton ? oArgs.pauseButton : 'PauseButton';
		this.iImageId		= this.start;
		this.eAppear		= null;
		this.eFade			= null;
		if ( this.slides ) {
			this.numOfImages	= this.slides.length;
			if ( !this.numOfImages ) {
				alert('No slides?');
			}
		}
		
		if (this.runrandom) {
			var a,b;		
			a = this.slides.slice( 0, 1);
			b = this.slides.slice( 1).sort(function() {return 0.5 - Math.random()})
			this.slides = a.concat( b);
		}
		
		if ( this.autostart ) {
			this.startSlideShow();
		}
	},
	
	// The Fade Function
	swapImage: function (x,y) {
		$(this.slides[x]) && ( this.eAppear = new Effect.Appear( this.slides[x]), { duration: this.duration });
		this.eFade && this.eAppear && ( this.eFade.element === this.eAppear.element) && this.eFade.cancel();
		$(this.slides[y]) && ( this.eFade   = new Effect.Fade  ( this.slides[y]), { duration: this.duration });
	},
	
	// the onload event handler that starts the fading.
	startSlideShow: function () {
		this.playid = setInterval(this.play.bind(this),this.wait);
		if ($(this.playButton)) $(this.playButton).hide();
		if ($(this.pauseButton)) $(this.pauseButton).appear({ duration: 0});

		this.updatecounter();
									
	},
	
	play: function () {
		
		var imageShow, imageHide;
	
		imageShow = this.iImageId+1;
		imageHide = this.iImageId;
		
		if (imageShow == this.numOfImages) {
			this.swapImage(0,imageHide);	
			this.iImageId = 0;					
		} else {
			this.swapImage(imageShow,imageHide);			
			this.iImageId++;
		}
		
		this.updatecounter();
	},
	
	stop: function  () {
		clearInterval(this.playid);				
		if ($(this.playButton)) $(this.playButton).appear({ duration: 0});
		if ($(this.pauseButton)) $(this.pauseButton).hide();
	},
	
	goNext: function () {
		clearInterval(this.playid);
		if ($(this.playButton)) $(this.playButton).appear({ duration: 0});
		if ($(this.pauseButton)) $(this.pauseButton).hide();
		
		var imageShow, imageHide;
	
		imageShow = this.iImageId+1;
		imageHide = this.iImageId;
		
		if (imageShow == this.numOfImages) {
			this.swapImage(0,imageHide);	
			this.iImageId = 0;					
		} else {
			this.swapImage(imageShow,imageHide);			
			this.iImageId++;
		}
	
		this.updatecounter();
	},
	
	goPrevious: function () {
		clearInterval(this.playid);
		if ($(this.playButton)) $(this.playButton).appear({ duration: 0});
		if ($(this.pauseButton)) $(this.pauseButton).hide();
	
		var imageShow, imageHide;
					
		imageShow = this.iImageId-1;
		imageHide = this.iImageId;
		
		if (this.iImageId == 0) {
			this.swapImage(this.numOfImages-1,imageHide);	
			this.iImageId = this.numOfImages-1;		
		} else {
			this.swapImage(imageShow,imageHide);			
			this.iImageId--;
		}
		
		this.updatecounter();
	},
	
	goTo: function (iImageIdNext) {
		var imageShow, imageHide;
					
		clearInterval(this.playid);
		if ($(this.playButton)) $(this.playButton).appear({ duration: 0});
		if ($(this.pauseButton)) $(this.pauseButton).hide();

		imageShow = iImageIdNext;
		imageHide = this.iImageId;
		
		if( imageShow != imageHide) {
			this.swapImage(imageShow,imageHide);			
			this.iImageId = iImageIdNext;
			this.updatecounter();
		}
	},
	
	updatecounter: function () {
		var textIn = this.iImageId+1 + ' of ' + this.numOfImages;
		$(this.counter) && ( $(this.counter).innerHTML = textIn );
		if ( $(this.caption) && ( oNewCaption = $(this.slides[this.iImageId]).down('.image-caption') ) ) {
			$(this.caption).innerHTML = oNewCaption.innerHTML;
		}
	}
}
