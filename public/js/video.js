
(function ($) {
    "use strict";

    var player;
    function onYouTubePlayerAPIReady() { player = new YT.Player('player'); }
    //so on jquery event or whatever call the play or stop on the video.
    //to play player.playVideo();
    //to stop player.stopVideo();


    // When the window is resized
    // (You'll probably want to debounce this)
    $(window).resize(function () {

        player.stopVideo(); // don't work yet

    });



})(jQuery);