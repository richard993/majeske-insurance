<!DOCTYPE html>
<html lang="en">
<head>
    <script type="text/javascript" src="{{ asset('assets/frontend/extras/jquery.min.1.7.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/extras/jquery-ui-1.8.20.custom.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/extras/jquery.mousewheel.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/extras/modernizr.2.5.3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/lib/hash.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('assets/frontend/css/jquery.ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/steve-jobs.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/jquery.ui.html4.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/steve-jobs-html4.css') }}">

    <script type="text/javascript" src="{{ asset('assets/frontend/js/steve-jobs.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/lib/turn.html4.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/lib/turn.min.js') }}"></script>
    @routes
</head>

<body>
    <form action="{{ route('page.process') }}" method="post">
        @csrf
        <div id="canvas">
            <div id="book-zoom">
                <div class="sj-book">
                    <div depth="5" class="hard">
                        <div class="side"></div>
                    </div>
                    <div depth="5" class="hard front-side">
                        <div class="depth"></div>
                    </div>
                    <div class="own-size"></div>
                    <div class="own-size even"></div>
                    <div class="hard fixed back-side p11">
                        <div class="depth"></div>
                    </div>
                    <div class="hard p12"></div>
                </div>
            </div>
    </form>
    <!-- 	<div id="book-wrapper">
   <div id="book-zoom"></div>
  </div> -->


    <div id="slider-bar" class="turnjs-slider">
        <div id="slider"></div>
    </div>
    <div class="thanks-pop">
        <h3>Thank You</h3>
        <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Nulla fugit ad ducimus deserunt voluptatum
            blanditiis perspiciatis reprehenderit in, minima deleniti dignissimos. Reiciendis aut quo velit
            vitae,
            minima veritatis perferendis necessitatibus!</p>
    </div>

    </div>


    <script type="text/javascript">
        function loadApp() {

            var flipbook = $('.sj-book');

            // Check if the CSS was already loaded

            if (flipbook.width() == 0 || flipbook.height() == 0) {
                setTimeout(loadApp, 10);
                return;
            }

            // Mousewheel

            $('#book-zoom').mousewheel(function(event, delta, deltaX, deltaY) {

                var data = $(this).data(),
                    step = 30,
                    flipbook = $('.sj-book'),
                    actualPos = $('#slider').slider('value') * step;

                if (typeof(data.scrollX) === 'undefined') {
                    data.scrollX = actualPos;
                    data.scrollPage = flipbook.turn('page');
                }

                data.scrollX = Math.min($("#slider").slider('option', 'max') * step,
                    Math.max(0, data.scrollX + deltaX));

                var actualView = Math.round(data.scrollX / step),
                    page = Math.min(flipbook.turn('pages'), Math.max(1, actualView * 2 - 2));

                if ($.inArray(data.scrollPage, flipbook.turn('view', page)) == -1) {
                    data.scrollPage = page;
                    flipbook.turn('page', page);
                }

                if (data.scrollTimer)
                    clearInterval(data.scrollTimer);

                data.scrollTimer = setTimeout(function() {
                    data.scrollX = undefined;
                    data.scrollPage = undefined;
                    data.scrollTimer = undefined;
                }, 1000);

            });

            // Slider

            $("#slider").slider({
                min: 1,
                max: 100,

                start: function(event, ui) {

                    if (!window._thumbPreview) {
                        _thumbPreview = $('<div />', {
                            'class': 'thumbnail'
                        }).html('<div></div>');
                        setPreview(ui.value);
                        _thumbPreview.appendTo($(ui.handle));
                    } else
                        setPreview(ui.value);

                    moveBar(false);

                },

                slide: function(event, ui) {

                    setPreview(ui.value);

                },

                stop: function() {

                    if (window._thumbPreview)
                        _thumbPreview.removeClass('show');

                    $('.sj-book').turn('page', Math.max(1, $(this).slider('value') * 2 - 2));

                }
            });


            // URIs

            Hash.on('^page\/([0-9]*)$', {
                yep: function(path, parts) {

                    var page = parts[1];

                    if (page !== undefined) {
                        if ($('.sj-book').turn('is'))
                            $('.sj-book').turn('page', page);
                    }

                },
                nop: function(path) {

                    if ($('.sj-book').turn('is'))
                        $('.sj-book').turn('page', 1);
                }
            });

            // Arrows

            $(document).keydown(function(e) {

                var previous = 37,
                    next = 39;

                switch (e.keyCode) {
                    case previous:

                        $('.sj-book').turn('previous');

                        break;
                    case next:

                        $('.sj-book').turn('next');

                        break;
                }

            });


            // Flipbook

            flipbook.bind(($.isTouch) ? 'touchend' : 'click', zoomHandle);

            flipbook.turn({
                elevation: 50,
                acceleration: !isChrome(),
                autoCenter: true,
                gradients: true,
                duration: 1000,
                pages: 12,
                when: {
                    turning: function(e, page, view) {
                        var book = $(this),
                            currentPage = book.turn('page'),
                            pages = book.turn('pages');

                        if (currentPage > 3 && currentPage < pages - 3) {

                            if (page == 1) {
                                book.turn('page', 2).turn('stop').turn('page', page);
                                e.preventDefault();
                                return;
                            } else if (page == pages) {
                                book.turn('page', pages - 1).turn('stop').turn('page', page);
                                e.preventDefault();
                                return;
                            }
                        } else if (page > 3 && page < pages - 3) {
                            if (currentPage == 1) {
                                book.turn('page', 2).turn('stop').turn('page', page);
                                e.preventDefault();
                                return;
                            } else if (currentPage == pages) {
                                book.turn('page', pages - 1).turn('stop').turn('page', page);
                                e.preventDefault();
                                return;
                            }
                        }

                        updateDepth(book, page);

                        if (page >= 2)
                            $('.sj-book .p2').addClass('fixed');
                        else
                            $('.sj-book .p2').removeClass('fixed');

                        if (page < book.turn('pages'))
                            $('.sj-book .p11').addClass('fixed');
                        else
                            $('.sj-book .p11').removeClass('fixed');

                        Hash.go('page/' + page).update();

                    },

                    turned: function(e, page, view) {

                        var book = $(this);

                        if (page == 2 || page == 3) {
                            book.turn('peel', 'br');
                        }

                        updateDepth(book);

                        $('#slider').slider('value', getViewNumber(book, page));

                        book.turn('center');

                    },

                    start: function(e, pageObj) {

                        moveBar(true);

                    },

                    end: function(e, pageObj) {

                        var book = $(this);

                        updateDepth(book);

                        setTimeout(function() {

                            $('#slider').slider('value', getViewNumber(book));

                        }, 1);

                        moveBar(false);

                    },

                    missing: function(e, pages) {

                        for (var i = 0; i < pages.length; i++) {
                            addPage(pages[i], $(this));
                        }

                    }
                }
            });


            $('#slider').slider('option', 'max', numberOfViews(flipbook));
            flipbook.addClass('animated');

            $(".p3").load(route('load.page', 3));
            $(".p4").load(route('load.page', 4));


            // Show canvas
            $('#canvas').css({
                visibility: ''
            });
        }

        // Hide canvas
        $('#canvas').css({
            visibility: 'hidden'
        });

        // Load turn.js

        yepnope({
            test: Modernizr.csstransforms,
            yep: [window.location.href + '/assets/frontend/lib/turn.min.js'],
            nope: [window.location.href + '/assets/frontend/lib/turn.html4.min.js', window.location.href +
                '/assets/frontend/css/jquery.ui.html4.css', window.location.href +
                '/assets/frontend/css/steve-jobs-html4.css'
            ],
            both: [window.location.href + '/assets/frontend/js/steve-jobs.js', window.location.href +
                '/assets/frontend/css/jquery.ui.css', window.location.href +
                '/assets/frontend/css/steve-jobs.css'
            ],
            complete: loadApp
        });
    </script>

</body>

</html>
