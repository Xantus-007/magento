// Uses CommonJS, AMD or browser globals to create a jQuery plugin.
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = function( root, jQuery ) {
            if ( jQuery === undefined ) {
                // require('jQuery') returns a factory that requires window to
                // build a jQuery instance, we normalize how we use modules
                // that require this pattern but the window provided is a noop
                // if it's defined (how jquery works)
                if ( typeof window !== 'undefined' ) {
                    jQuery = require('jquery');
                }
                else {
                    jQuery = require('jquery')(root);
                }
            }
            factory(jQuery);
            return jQuery;
        };
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {
    /**
     *  DBM - DbmYoutubePlayer
     *  @author  : Lily Bourdareau
     *  @description : Get Youtube Data & manage youtube embed videos (iframes)
     *  version : 1.0.0
     */

     /**
    * [DbmYoutubePlayer]
    * @param  {[type]} object - Plugin Options
    */
    DbmYoutubePlayer = function(options) {
        /**
         * [Initialize variables]
         */
        this.ytPlayer = undefined;
        /**
        * [restrictions]
        * Restrictions : https://developers.google.com/youtube/v3/getting-started#quota
        * Queries per day 1 000 000
        * Queries per 100 seconds per user 300 000
        * Queries per 100 seconds 3 000 000
        */
        this.ytSearch = "https://www.googleapis.com/youtube/v3/search";
        this.ytVideos = "https://www.googleapis.com/youtube/v3/videos";

        /**
        * [settings holder]
        * @deep {[type]} string - allow $.extend to merge recursively option object
        * [settings]
        * @keyAPI {[type]} string - API Key used for Data API // default : undefined
        * @mute {[type]} boolean - Mute the current player // default : false
        * @videoID {[type]} string - Object where the scroll happen // default : 'html, body'
        * @fitVidsWrapper {[type]} string - ClassName of fitvids Wrapper // default : undefined
        * @videoButton {[type]} string - Active mode for play/pause video with a custom button
        * @getThumbnail {[type]} boolean - Active mode for get thumbnail video // default : false
        * @videoProperties {[type]} object - Video properties

        * [videoProperties]
        * @url {[type]} string - unique id of the youtube video // default : undefined
        * @width {[type]} string - width of the expected iframe (inline) // default : '820'
        * @height {[type]} string - height of the expected iframe (inline) // default : '432'
        * @title {[type]} string - title of the expected iframe (attribute) // default : 'YouTube video player'
        * @volume {[type]} integer - volume of the video min:0 max:100 // default : 100
        * @speed {[type]} float - speed of the video // default : 1
        * @mute {[type]} boolean - mute the video // default : false
        * [url properties]
        * @autoplay {[type]} integer - autoplay the video when it's loaded -  valeurs : 0 / 1 // default : 0
        * @controls {[type]} integer - show/hide video controls -  valeurs : 0 / 1 // default : 0
        * @showinfo {[type]} integer - show/hide video title -  valeurs : 0 / 1 // default : 0
        * @fullscreen {[type]} integer - show/hide fullscreen button (when controls:1) - valeurs : 0 / 1 // default : 0
        * @modestbranding {[type]} integer - show less youtube branding -  valeurs : 0 / 1 // default : 0
        * @loop {[type]} integer - loop the video - valeurs : 0 / 1 // default : 0

        * @latests {[type]} object - Latests videos
        * [latests]
        * @channelID {[type]} string - channel ID // default : undefined
        * @orderBy {[type]} string - Method used to order datas - valeurs : 'date' / 'rating' / 'relevance' / 'title' / 'videoCount' / 'viewCount' // default : 'date'
        * @channelID {[type]} integer - Max results // default : 1
        */
        this.settings = $.extend(true, {
            keyAPI : undefined,
            videoID : 'js-video--yt__video',
            fitVidsWrapper : undefined,
            videoButton : false,
            getThumbnail : false,
            videoProperties : {
                url : undefined,
                width: '1440',
                height: '580',
                title: 'YouTube video player',
                volume : 100,
                speed : 1,
                mute : false,
                autoplay: 0,
                controls: 1,
                showinfo : 1,
                fullscreen : 1,
                modestbranding : 0,
                loop : 0,
            },
            latests : {
                channelID : undefined,
                orderBy : 'date',
                maxResults : 1,
            }
        }, options);

        /**
        * [plugin variables]
        * @videoTitle {[type]} string - Title of the video (different from iframe title attribute) // default : undefined
        * @videoThumbnail {[type]} string - Mute the current player // default : undefined
        * @videoDescription {[type]} string - Mute the current player // default : undefined
        * @videoTitles {[type]} array - Mute the current player // default : []
        * @videoChannel {[type]} object - Mute the current player // default : undefined

        * [videoChannel]
        * @channelTitle {[type]} object - Mute the current player // default : false
        */
        this.videoTitle = undefined;
        this.videoThumbnail = undefined;
        this.videoDescription = undefined;
        this.videoTitles = new Array;
        this.videoChannel = {
            channelTitle : undefined,
        };

        /**
         * [u : verify a property is not undefined]
         * @param  {[type]} variable
         * @return {[type]} boolean - if false the property type is undefined
         */
        u = function(property) {
            if(typeof property != undefined) { return true; }
            else { return false; }
        };


        if(u(this.settings.videoProperties.url) && u(this.settings.videoProperties.width) && u(this.settings.videoProperties.height)) {
            this._initVideo();
        }

        /**
         * [call _getThumbnail when getThumbnail mode on]
         */
        if(this.settings.getThumbnail == true) {
            this._getThumbnail();
        }

        /**
         * [call _getLatestsVideos when channelID is defined]
         */
        if(u(this.settings.latests.channelID)) {
            this._getLatestsVideos();
        }
    };

    /**
     * [_initVideo : Initialize YT.Player and create iframe with settings datas]
     */
    DbmYoutubePlayer.prototype._initVideo = function() {
        var self = this, p = this.settings.videoProperties;
        if($('#' + this.settings.videoID).length > 0) {
            this.ytPlayer = new YT.Player(this.settings.videoID, {
                height: p.height,
                width: p.width,
                videoId: p.url,
                playerVars : { 'autoplay' : p.autoplay, 'controls' : p.controls, 'rel' : 0, 'loop' : p.loop, 'showinfo' : p.showinfo, 'fs' : p.fullscreen, 'modestbranding' : p.modestbranding },
                events : {
                    /**
                     * [onReady : When player is initialized, fire an event]
                     */
                    onReady: function(e) {
                        $('#' + self.settings.videoID).closest('.js-fitvids--yt').trigger('dbm.youtube.loaded');
                        self._onPlayerReady();
                    },
                    /**
                     * [onError : When player is not initialized]
                     */
                    onError : function(data) {
                        console.log("ERROR : " + data);
                    },
                    onStateChange : function(e) {
                        if (e.data == YT.PlayerState.ENDED) {
                            $('#' + self.settings.videoID).closest('.js-fitvids--yt').trigger('dbm.youtube.ended');
                        }
                    }
                }
            });
        }
    };

    /**
     * [_volume : Manage video volume, video speed and mute mode]
     */
    DbmYoutubePlayer.prototype._volume = function() {
        if(this.ytPlayer.isMuted() == false && this.settings.videoProperties.mute == true) {
            this.ytPlayer.mute();
        }
        else if(this.ytPlayer.isMuted() == true && this.settings.videoProperties.mute == false) {
            this.ytPlayer.unMute();
        }

        this.ytPlayer.setVolume(parseInt(this.settings.videoProperties.volume));
        this.ytPlayer.setPlaybackRate(parseFloat(this.settings.videoProperties.speed));
    };

    /**
     * [_onPlayerReady : called when the player is initialized]
     */
    DbmYoutubePlayer.prototype._onPlayerReady = function(e) {
        var self = this;

        /**
         * [call fitVids for a responsive iframe]
         */
        if(u(this.settings.fitVidsWrapper)) {
            this._customfitVids();
            this.settings.fitVidsWrapper = undefined;
        }

        /**
        * [play the video when no autoplay and button mode off]
        */
        if(this.settings.videoButton === false && this.settings.videoProperties.autoplay != 1) {
            this.ytPlayer.playVideo();
        }
        /**
        * [manage play/pause with the button when button mode on]
        */
        else {
            if(this.settings.videoButton != false && typeof this.settings.videoButton === 'string') {
                $(this.settings.videoButton).on("click", function(event) {
                    event.preventDefault();
                    if( self.ytPlayer.getPlayerState() ===  YT.PlayerState.PLAYING) {
                        self.ytPlayer.pauseVideo();
                    }
                    else if ( self.ytPlayer.getPlayerState() !==  YT.PlayerState.PLAYING ){
                        self.ytPlayer.playVideo();
                    }
                });
            }
        }
    };
    /**
     * [_destroy : destroy player]
     */
    DbmYoutubePlayer.prototype._destroy = function(e) {
        this.ytPlayer.destroy();
    };
    /**
     * [_customfitVids : instrisic ratios]
     */
    DbmYoutubePlayer.prototype._customfitVids = function(e) {
        var $wrapper = $('.' + this.settings.fitVidsWrapper),
        $iframe = $wrapper.find('iframe'),
        w = parseInt($iframe.attr('width')),
        h = parseInt($iframe.attr('height')),
        smaller = Math.min(w, h),
        bigger  = Math.max(w, h);

        // Event responsive
        $wrapper.css({
            paddingBottom: (smaller / bigger)*100 + "%"
        })
    };

    /**
     * [_getLatestsVideos : manage latests videos for the selected channel]
     */
    DbmYoutubePlayer.prototype._getLatestsVideos = function() {
        var self = this,
        t = this.settings.latests;
        if(t.channelID !== undefined && this.settings.keyAPI !== undefined) {
            /**
             * [$.get : Ajax call to Youtube Data API with given parameters]
             * @param  {[type]} string - concatened parameters
             * @return {[type]} object - data
             */
            $.get(this.ytSearch + "?part=snippet&channelId=" + t.channelID + "&maxResults=" + t.maxResults + "&order=" + t.orderBy + "&type=video&key=" + this.settings.keyAPI, function(data, status) {
                console.log(data, status);
                $(data.items).each(function(index) {
                    var video = {
                        title : this.snippet.title,
                        videoId : this.id.videoId,
                        description : this.snippet.description,
                        thumbnails : this.snippet.thumbnails.high.url
                    }

                    self.videoTitles.push(video);
                });
                /**
                 * [set videoChannel.channelTitle with data]
                 */
                self.videoChannel.channelTitle = data.items[0].snippet.channelTitle;
                /**
                 * [fire event for channel data loaded]
                 */
                $('#' + self.settings.videoID).trigger('dbm.youtube.channel.loaded');
            });
        }
    };

    /**
     * [_getThumbnail : manage thumbnails and video data (don't need Youtube Data API)]
     */
    DbmYoutubePlayer.prototype._getThumbnail = function() {
        var self = this;
        if(this.settings.getThumbnail == true) {
            /**
             * [$.get : Ajax call to Youtube Data API with given parameters]
             * @param  {[type]} string - concatened parameters
             * @return {[type]} object - data
             */
            $.get(this.ytVideos + "?part=snippet&id=" + this.settings.videoProperties.url + "&key=" + this.settings.keyAPI, function(data, status) {
                console.log(data, status);
                /**
                 * [set variables with data]
                 */
                self.videoThumbnail = data.items[0].snippet.thumbnails.high.url;
                self.videoDescription = data.items[0].snippet.description;
                self.videoTitle = data.items[0].snippet.title;

                /**
                 * [fire event for thumbnail data loaded]
                 */
                $('#' + self.settings.videoID).trigger('dbm.youtube.thumbnail.loaded');
            });
        }
        else { return; }
    };

    /**
     * [METHODS]
     * [_mute : mute current video player]
     */
    DbmYoutubePlayer.prototype._mute = function() {
        this.settings.videoProperties.mute = true;
        this._volume();
    };
    /**
     * [_unmute : unmute current video player]
     */
    DbmYoutubePlayer.prototype._unmute = function() {
        this.settings.videoProperties.mute = false;
        this._volume();
    };
    /**
     * [_play : play current video player]
     */
    DbmYoutubePlayer.prototype._play = function() {
        if ( this.ytPlayer.getPlayerState() !==  YT.PlayerState.PLAYING ){
            this.ytPlayer.playVideo();
        }
        else return;
    };
    /**
     * [_pause : pause current video player]
     */
    DbmYoutubePlayer.prototype._pause = function() {
        if( this.ytPlayer.getPlayerState() ===  YT.PlayerState.PLAYING) {
            this.ytPlayer.pauseVideo();
        }
        else return;
    };
}));
