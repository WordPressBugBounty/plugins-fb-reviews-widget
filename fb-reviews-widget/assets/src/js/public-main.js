var TrustReviews = TrustReviews || {};

TrustReviews.slg = 'trustreviews';

/**
 * Widget
 */
TrustReviews.Plugin = {

    next: function(slg, pagin) {
        var parent = this.parentNode,
            selector = '.' + TrustReviews.slg + '-hide';
            reviews = parent.querySelectorAll(selector);
        for (var i = 0; i < pagin && i < reviews.length; i++) {
            if (reviews[i]) {
                reviews[i].className = reviews[i].className.replace(TrustReviews.slg + '-hide', ' ');
            }
        }
        reviews = parent.querySelectorAll(selector);
        if (!reviews.length) {
            parent.removeChild(this);
        }
        return false;
    },

    leave_popup: function() {
        TrustReviews.Plugin.popup(this.getAttribute('href'), 620, 500);
        return false;
    },

    lang: function() {
        var n = navigator;
        return (n.language || n.systemLanguage || n.userLanguage ||  'en').substr(0, 2).toLowerCase();
    },

    popup: function(url, width, height, prms, top, left) {
        top = top || (screen.height/2)-(height/2);
        left = left || (screen.width/2)-(width/2);
        return window.open(url, '', 'location=1,status=1,resizable=yes,width='+width+',height='+height+',top='+top+',left='+left);
    },

    timeago: function() {
        let els = document.querySelectorAll('.' + TrustReviews.slg + ' [data-rev]');
        for (var i = 0; i < els.length; i++) {
            let time,
                rev = els[i].getAttribute('data-rev'),
                tel = els[i].querySelector('[data-time]'),
                dat = tel.getAttribute('data-time');

            //if (rev == 'google') {
                time = parseInt(dat);
                time *= 1000;
            /*} else if (rev == 'facebook') {
                time = new Date(dat.replace(/\+\d+$/, '')).getTime();
            } else {
                time = new Date(dat.replace(/ /, 'T')).getTime();
            }*/
            tel.innerHTML = WPacTime.getTime(time, this.lang(), 'ago');
        }
    },

    read_more: function() {
        var read_more = document.querySelectorAll('.wp-more-toggle');
        for (var i = 0; i < read_more.length; i++) {
            (function(rm) {
            rm.onclick = function() {
                rm.parentNode.removeChild(rm.previousSibling.previousSibling);
                rm.previousSibling.className = '';
                rm.textContent = '';
            };
            })(read_more[i]);
        }
    },

    get_parent: function(el, cl) {
        cl = cl || TrustReviews.slg;
        if (el.className.split(' ').indexOf(cl) < 0) {
            // the last semicolon (;) without braces ({}) in empty loop makes error in WP Faster Cache
            //while ((el = el.parentElement) && el.className.split(' ').indexOf(cl) < 0);
            while ((el = el.parentElement) && el.className.split(' ').indexOf(cl) < 0){}
        }
        return el;
    },

    init_slider: function(slg, cnt) {
        const SLIDER_ELEM  = el('row'),
              SLIDER_OPTS  = JSON.parse(SLIDER_ELEM.getAttribute('data-options')),
              SLIDER_SPEED = SLIDER_OPTS.speed * 1000,
              REVIEWS_ELEM = el('reviews'),
              REVIEW_ELEMS = el('review', 1);

        var rootElSize   = '',
            resizeTimout = null,
            swipeTimout  = null;

        var init = function() {
            if (isVisible(SLIDER_ELEM)) {
                setTimeout(resize, 1);
                if (REVIEW_ELEMS.length && SLIDER_OPTS.autoplay) {
                    setTimeout(swipe, SLIDER_SPEED);
                }
            } else {
                setTimeout(init, 300);
            }
        }
        init();

        window.addEventListener('resize', function() {
            var vv = reviewsBack();
            clearTimeout(resizeTimout);
            resizeTimout = setTimeout(resize, 150, vv);
        });

        if (REVIEWS_ELEM) {
            REVIEWS_ELEM.addEventListener('scroll', function() {
                setTimeout(dotsinit, 200);
            });
        }

        function resize(vv) {
            var size,
                row_elems = el('row', 1);

            for (let i = 0; i < row_elems.length; i++) {
                let row_elem = row_elems[i],
                    offsetWidth = row_elem.offsetWidth;
                if (offsetWidth < 510) {
                    size = 'xs';
                } else if (offsetWidth < 750) {
                    size = 'x';
                } else if (offsetWidth < 1100) {
                    size = 's';
                } else if (offsetWidth < 1450) {
                    size = 'm';
                } else if (offsetWidth < 1800) {
                    size = 'l';
                } else {
                    size = 'xl';
                }
                row_elem.className = slg + '-row ' + slg + '-row-' + size;
            }

            if (REVIEW_ELEMS.length && rootElSize != size) {
                setTimeout(function() {
                    if (REVIEWS_ELEM.scrollLeft != vv * REVIEW_ELEMS[0].offsetWidth) {
                        REVIEWS_ELEM.scrollLeft = vv * REVIEW_ELEMS[0].offsetWidth;
                    }
                    dotsinit();
                    rootElSize = size;
                }, 200);
            }
        }

        function dotsinit() {
            var reviews_elem = el('reviews'),
                review_elems = el('review', 1),
                t = review_elems.length,
                v = Math.round(reviews_elem.offsetWidth / review_elems[0].offsetWidth);

            var dots = Math.ceil(t/v),
                dotscnt = el('dots');

            if (!dotscnt) return;

            dotscnt.innerHTML = '';
            for (var i = 0; i < dots; i++) {
                var dot = document.createElement('div');
                dot.className = slg + '-dot';

                var revWidth = review_elems[0].offsetWidth;
                var center = (reviews_elem.scrollLeft + (reviews_elem.scrollLeft + revWidth * v)) / 2;

                x = Math.ceil((center * dots) / reviews_elem.scrollWidth);
                if (x == i + 1) dot.className = slg + '-dot active';

                dot.setAttribute('data-index', i + 1);
                dot.setAttribute('data-visible', v);
                dotscnt.appendChild(dot);

                dot.onclick = function() {
                    var curdot = el('dot.active'),
                        ii = parseInt(curdot.getAttribute('data-index')),
                        i = parseInt(this.getAttribute('data-index')),
                        v = parseInt(this.getAttribute('data-visible'));

                    if (ii < i) {
                        scrollNext(v * Math.abs(i - ii));
                    } else {
                        scrollPrev(v * Math.abs(i - ii));
                    }

                    el('dot.active').className = slg + '-dot';
                    this.className = slg + '-dot active';

                    if (swipeTimout) {
                        clearInterval(swipeTimout);
                    }
                };
            }
        }

        function isVisible(elem) {
            return !!(elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length) && window.getComputedStyle(elem).visibility !== 'hidden';
        }

        function isVisibleInParent(elem) {
            var elemRect = elem.getBoundingClientRect(),
                parentRect = elem.parentNode.getBoundingClientRect();

            return (Math.abs(parentRect.left - elemRect.left) < 2 || parentRect.left <= elemRect.left) && elemRect.left < parentRect.right &&
                   (Math.abs(parentRect.right - elemRect.right) < 2 || parentRect.right >= elemRect.right) && elemRect.right > parentRect.left;
        }

        function scrollPrev(offset) {
            REVIEWS_ELEM.scrollBy(
                -REVIEW_ELEMS[0].offsetWidth * offset, 0
            );
        }

        function scrollNext(offset) {
            REVIEWS_ELEM.scrollBy(
                REVIEW_ELEMS[0].offsetWidth * offset, 0
            );
        }

        var prev = el('prev');
        if (prev) {
            prev.onclick = function() {
                scrollPrev(1);
                if (swipeTimout) {
                    clearInterval(swipeTimout);
                }
            };
        }

        var next = el('next');
        if (next) {
            next.onclick = function() {
                scrollNext(1);
                if (swipeTimout) {
                    clearInterval(swipeTimout);
                }
            };
        }

        function swipe() {
            if (isVisibleInParent(el('review:last-child'))) {
                scrollPrev(REVIEW_ELEMS.length - reviewsPerView());
            } else {
                scrollNext(1);
            }
            swipeTimout = setTimeout(swipe, SLIDER_SPEED);
        }

        function reviewsBack() {
            return Math.round(REVIEWS_ELEM.scrollLeft / REVIEW_ELEMS[0].offsetWidth);
        }

        function reviewsPerView() {
            return Math.round(REVIEWS_ELEM.offsetWidth / REVIEW_ELEMS[0].offsetWidth);
        }

        function el(n, m) {
            return m ? cnt.querySelectorAll('.' + slg + '-' + n) : cnt.querySelector('.' + slg + '-' + n);
        }
    },

    init: function(slg, el, layout) {
        this.timeago();
        this.read_more();

        el = this.get_parent(el, slg);
        if (el && el.getAttribute('data-exec') != 'true' && (layout == 'slider' || layout == 'grid')) {
            el.setAttribute('data-exec', 'true');
            this.init_slider(slg, el);
        }
    }

}

document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('.' + TrustReviews.slg + '[data-exec="false"]');
    for (var i = 0; i < elems.length; i++) {
        (function(elem) {
            TrustReviews.Plugin.init(TrustReviews.slg, elem, elem.getAttribute('data-layout'));
        })(elems[i]);
    }
});