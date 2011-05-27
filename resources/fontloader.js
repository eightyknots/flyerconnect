/* Google Font Loader */


WebFontConfig = {
google: { families: [ 'Mako:regular', 'Arimo:regular,bold', 'PT Sans:regular,italic,bold,bolditalic' ] }
};

/* Lazyload async function taken from friendlybit.com */


(function() {
    function trial() {
        $('#content-box').fadeIn();
    }
    function llwfc() {
        var wf = document.createElement('script');
        wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
            '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
        wf.type = 'text/javascript';
        wf.async = 'true';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(wf, s);
        if (wf.attachEvent) {
            wf.attachEvent('onload', trial);
        } else {
            wf.addEventListener('load', trial, false);
        }
    }
    if (window.attachEvent) {
        window.attachEvent('onload', llwfc);
    } else {
        window.addEventListener('load', llwfc, false);
    }
})();