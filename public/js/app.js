var $grid = $('.grid').imagesLoaded( function() {
    // init Masonry after all images have loaded
    $grid.masonry({
        // options
        itemSelector: '.grid-item',
        columnWidth: 200,
        gutter: 0,
        fitWidth: true,
        transitionDuration: '1s',
        stagger: 30
    });
});