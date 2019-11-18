$(document).ready(function() {
    // setInterval(function() {
        let docHeight = $(window).height();
        let footerHeight = $('#footer').height();
        let footerTop = $('#footer').position().top + footerHeight;
        let marginTop = (docHeight - footerTop);
        // var marginTop = (docHeight - footerTop + 10);

        if (footerTop < docHeight)
            $('#footer').css('margin-top', marginTop + 'px'); // padding of 30 on footer
        else
            $('#footer').css('margin-top', '0px');
        // console.log("docheight: " + docHeight + "\n" + "footerheight: " + footerHeight + "\n" + "footertop: " + footerTop + "\n" + "new docheight: " + $(window).height() + "\n" + "margintop: " + marginTop);
    // }, 250);
});