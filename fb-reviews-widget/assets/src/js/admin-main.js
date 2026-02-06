var TrustReviews = TrustReviews || {};

/**
 * Admin
 */
TrustReviews.Admin = {

    stars: function(rating, color, size) {
        var str = '';
        for (var i = 1; i < 6; i++) {
            var score = rating - i;
            if (score >= 0) {
                str += this.star('', color, size);
            } else if (score > -1 && score < 0) {
                if (score < -0.75) {
                    str += this.star('-o', '#ccc', size);
                } else if (score > -0.25) {
                    str += this.star('', color, size);
                } else {
                    str += this.star('-half', color, size);
                }
            } else {
                str += this.star('-o', '#ccc', size);
            }
        }
        return str;
    },

    star: function(prefix, color, size) {
        let use;
        switch (prefix) {
            case '-half':
                use = '<use href="#' + TRUSTREVIEWS_VARS.slg + '-star-o" style="stroke:' + color + ';stroke-width:1.5"/>' +
                      '<use href="#' + TRUSTREVIEWS_VARS.slg + '-star-half" style="fill:' + color + '" clip-path="url(#' + TRUSTREVIEWS_VARS.slg + '-clip-half)"/>';
                break;
            case '-o':
                use = '<use href="#' + TRUSTREVIEWS_VARS.slg + '-star-o" style="stroke:#ccc;stroke-width:1.5"/>';
                break;
            default:
                use = '<use href="#' + TRUSTREVIEWS_VARS.slg + '-star" style="fill:' + color + '"/>';
        }
        return '<svg viewBox="0 0 24 24" width="' + size + '" height="' + size + '">' + use + '</svg>';
    },

    review: function(review) {
        const slg = TRUSTREVIEWS_VARS.slg;

        const root = document.createElement('div');
        root.className = slg + '-list-review' + (review.hide === '' ? '' : ' wp-review-hidden');
        root.dataset.rev = String(review.provider || '');

        const right = document.createElement('div');
        right.className = slg + '-flex';
        root.appendChild(right);

        const lnk = document.createElement('a');
        lnk.className = slg + '-name';
        lnk.target = '_blank';
        lnk.rel = 'nofollow noopener';
        lnk.href = this.safeUrl(review.author_url);
        lnk.textContent = review.author_name || '';
        right.appendChild(lnk);

        const time = document.createElement('div');
        time.className = slg + '-time';
        time.dataset.time = String(review.time || '');
        right.appendChild(time);

        const feedback = document.createElement('div');
        feedback.className = slg + '-feedback';
        right.appendChild(feedback);

        const stars = document.createElement('span');
        stars.className = 'rpi-stars';
        stars.setAttribute('style', '--rpi-fill-percent:' + (review.rating * 20) + '%;--rpi-star-size:16px;vertical-align:middle!important');
        feedback.appendChild(stars);

        const text = document.createElement('span');
        text.className = slg + '-text';
        this.trimtext(text, review.text || '', 50);
        feedback.appendChild(text);

        const toggle = document.createElement('a');
        toggle.href = '#';
        toggle.className = 'wp-review-hide';
        toggle.dataset.id = String(review.id || '');
        toggle.textContent = (review.hide === '' ? 'Hide' : 'Show') + ' review';
        right.appendChild(toggle);

        return root;
    },

    trimtext: function(el, text, size) {
        if (!text) return;

        text = String(text);

        if (size && text.length > size) {
            var subtext = text.substring(0, size);
            var idx = subtext.indexOf(' ') + 1;

            if (idx < 1 || size - idx > (size / 2)) {
                idx = size;
            }

            var visibletext = '';
            var invisibletext = '';

            if (idx > 0) {
                visibletext = text.substring(0, idx - 1);
                invisibletext = text.substring(idx - 1);
            } else {
                visibletext = text;
            }

            el.appendChild(document.createTextNode(visibletext));

            if (invisibletext) {
                var dots = document.createElement('span');
                dots.textContent = '... ';
                el.appendChild(dots);

                var more = document.createElement('span');
                more.className = 'wp-more';
                more.textContent = invisibletext;
                el.appendChild(more);

                var toggle = document.createElement('span');
                toggle.className = 'wp-more-toggle';
                toggle.textContent = 'read more';
                el.appendChild(toggle);
            }
        } else {
            el.textContent = text;
        }
    },

    safeUrl: function(url) {
        if (!url) return '#';
        url = String(url).trim();
        if (/^https?:\/\//i.test(url)) {
            return url;
        }
        return '#';
    },

    s2dmy: function(s) {
        let d = (s / (60 * 60 * 24)).toFixed(0);
        if (d > 30) {
            if (d > 365) {
                return Math.round(d / 365) + ' years';
            }
            return Math.round(d / 30) + ' months';
        }
        return d + ' days';
    },

    review_hide: function($btn) {
        let slg = TRUSTREVIEWS_VARS.slg;
        jQuery.post(ajaxurl, {
            id       : $btn.attr('data-id'),
            feed_id  : jQuery('input[name="' + slg + '_feed[post_id]"]').val(),
            action   : slg + '_hide_review',
            _wpnonce : jQuery('#_wpnonce').val()
        }, function(res) {
            var parent = $btn.parent().parent();
            if (res.hide) {
                $btn.text('show review');
                parent.addClass('wp-review-hidden');
            } else {
                $btn.text('hide review');
                parent.removeClass('wp-review-hidden');
            }
        }, 'json');
    }
}

jQuery(document).ready(function($) {

    let slg = TRUSTREVIEWS_VARS.slg;

    $('.' + slg + '-admin-page a.nav-tab').on('click', function(e)  {
        var $this = $(this), activeId = $this.attr('href');
        $(activeId).show().siblings('.tab-content').hide();
        $this.addClass('nav-tab-active').siblings().removeClass('nav-tab-active');
        e.preventDefault();
    });

    /**
     * Rate us feedback
     */
    var $rateus = jq('#rate_us');
    if ($rateus.length) {
        var $rateus_dlg = jq('#rate_us-feedback'),
            $rateus_stars = jq('#rate_us-feedback-stars');

        if (window.location.href.indexOf('_feed_id=') > -1 && !window[slg + '_rateus']) {
            $rateus.addClass(n('-flash-visible'));
        }
        $('i', $rateus).click(function() {
            var rate = $(this).index() + 1;
            if (rate > 3) {
                $.post({
                    url      : ajaxurl,
                    type     : 'POST',
                    dataType : 'json',
                    data     : {
                        rate     : rate,
                        action   : n('_rateus_ajax'),
                        _wpnonce : $('#_wpnonce').val()
                    },
                    success  : function(res) {
                        console.log(res);
                    }
                });
                var askUrl = (Math.random() * 1).toFixed(0) > 0 ?
                    'https://g.page/r/CZIR4rrOZJnLEBM/review' :
                    'https://wordpress.org/support/plugin/' + TRUSTREVIEWS_VARS.pluginName + '/reviews/?rate=' + rate + '#new-post';
                window.open(askUrl, '_blank');
                rateus_close();
            } else {
                $rateus_stars.attr('data-rate', rate);
                $rateus_stars.html(TrustReviews.Admin.stars(rate, '#fb8e28', 24));
                $rateus_dlg.dialog({modal: true, width: '50%', maxWidth: '600px'});
                $('.ui-widget-overlay').bind('click', function() {
                    $rateus_dlg.dialog('close');
                });
            }
        });

        jq('.rate_us-cancel').click(function() {
            $rateus_dlg.dialog('close');
        });

        jq('.rate_us-send').click(function() {
            $.post({
                url      : ajaxurl,
                type     : 'POST',
                dataType : 'json',
                data     : {
                    action   : n('_rateus_ajax_feedback'),
                    rate     : $rateus_stars.attr('data-rate'),
                    email    : $('input', $rateus_dlg).val(),
                    msg      : $('textarea', $rateus_dlg).val(),
                    _wpnonce : $('#_wpnonce').val()
                },
                success  : function(res) {
                    $rateus_dlg.dialog({'title': 'Feedback sent'})
                    $rateus_dlg.html('<b style="color:#4cc74b">Thank you for your feedback!<br>' +
                                     'We received it and will investigate your suggestions.</b>');

                    rateus_close();
                    setTimeout(function() {
                        $rateus_dlg.fadeOut(500, function() { $rateus_dlg.dialog('close'); });
                    }, 1500);
                }
            });
        });

        function rateus_close() {
            setTimeout(function() {
                $rateus.addClass(n('-flash-gout'));
                $rateus.removeClass(n('-flash-visible'));
                $rateus.removeClass(n('-flash-gout'));
                window[slg + '_rateus'] = 1;
            }, 1000);
        }
    }

    /**
     * Overview page
     */
    var $overviewRating = jq('#overview-rating');
    if ($overviewRating.length) {

        var MONTHS = 6,
            $places  = jq('#overview-places'),
            $months  = jq('#overview-months'),
            $rating  = jq('#overview-rating'),
            $reviews = jq('#overview-reviews'),
            chart    = null;

        $places.change(function() {
            ajax(this.value);
        });

        $months.change(function() {
            MONTHS = this.value;
            ajax($places.val());
        });

        ajax(0, function(res) {
            /*
             * Places select filled
             */
            $.each(res.places, function(i, place) {
                $places.append($('<option>', {
                    value: place.id,
                    text : place.name
                }));
            });
        });

        function ajax(pid, cb) {
            var data = {
                action   : n('_overview_ajax'),
                _wpnonce : jQuery('#_wpnonce').val()
            };

            if (pid) {
                data.pid = pid;
            }
            jQuery.post({
                url      : ajaxurl,
                type     : 'POST',
                dataType : 'json',
                data     : data,
                success  : function(res) {

                    if (!res) {
                        window.location.href = TRUSTREVIEWS_VARS.builderUrl;
                        return;
                    }

                    var place = res.places.length > 1 ? res.places.find(x => x.id == pid) : res.places[0];

                    /*
                     * Stats minmax grouping and calculate result
                     */
                    var stats_result = null;

                    if (res.stats_minmax.length) {

                        let minmax = {},
                            mintime = 0,
                            nowtime = (new Date().getTime() / 1000).toFixed(0);

                        for (let i = 0; i < res.stats_minmax.length; i++) {

                            let mm = res.stats_minmax[i],
                                gpid = mm.biz_id;

                            mintime = !mintime || mm.time < mintime ? mm.time : mintime;

                            if (minmax[gpid]) {

                                minmax[gpid] = {
                                    time         : parseInt(nowtime - minmax[gpid].time),
                                    rating       : parseFloat((mm.rating - minmax[gpid].rating).toFixed(1)),
                                    review_count : parseInt(mm.review_count - minmax[gpid].review_count)
                                };

                                if (stats_result) {
                                    stats_result = {
                                        time         : minmax[gpid].time,
                                        rating       : stats_result.rating + minmax[gpid].rating,
                                        review_count : stats_result.review_count + minmax[gpid].review_count
                                    }
                                } else {
                                    stats_result = minmax[gpid];
                                }

                                delete minmax[gpid];

                            } else {
                                minmax[gpid] = {
                                    time         : mintime,
                                    rating       : mm.rating,
                                    review_count : mm.review_count
                                };
                            }

                        }
                    }

                    let $stats = jq('#overview-stats');
                    $stats.html('Not calculated yet');

                    if (stats_result) {
                        let sr = stats_result.rating,
                            src = stats_result.review_count;
                        $stats.html(
                            ('<div class="{slg}-overview-h">While using the plugin</div>' +
                            '<div>' +
                                'Usage time: ' +
                                '<span class="{slg}-stat-val {slg}-stat-up">' + TrustReviews.Admin.s2dmy(stats_result.time) + '</span>' +
                            '</div>' +
                            '<div>' +
                                'Rating up: ' +
                                '<span class="{slg}-stat-val {slg}-stat-' + (sr < 0 ? 'down' : (sr > 0 ? 'up' : '')) + '">' + sr + '</span>' +
                            '</div>' +
                            '<div>' +
                                'Reviews up: ' +
                                '<span class="{slg}-stat-val {slg}-stat-' + (src < 0 ? 'down' : (src > 0 ? 'up' : '')) + '">' + src + '</span>' +
                            '</div>').replace(/{slg}/g, slg)
                        );
                    }

                    /*
                     * Render rating
                     */
                    $rating.html(
                        ('<div class="{slg}">' +
                            '<div class="{slg}-flex" style="--dir:column;--gap:8px;--align:center">' +
                                '<div class="{slg}-overview-h">' + place.name + '</div>' +
                                '<div class="{slg}-flex" style="--dir:row;--align:center">' +
                                    '<span class="{slg}-rating">' + res.rating + '</span>' +
                                    '<span class="rpi-stars" style="--rpi-fill-percent:' + (res.rating * 20) + '%"></span>' +
                                '</div>' +
                                '<div class="{slg}-powered">Based on ' + res.review_count + ' reviews</div>' +
                                (place.updated ?
                                '<div class="{slg}-powered">Last updated: ' +
                                    '<span class="{slg}-time">' +
                                        rpi.Time.getTimeAgo(parseInt(place.updated), TrustReviews.Plugin.lang()) +
                                    '</span>' +
                                '</div>' : '') +
                            '</div>' +
                        '</div>').replace(/{slg}/g, slg)
                    );

                    /*
                     * Render reviews
                     */
                    const list = document.createElement('div');
                    list.className = slg + ' wpac';
                    $.each(res.reviews, function(i, review) {
                        list.appendChild(TrustReviews.Admin.review(review));
                    });
                    $reviews.html('');
                    $reviews[0].appendChild(list);
                    TrustReviews.Plugin.timeago();
                    TrustReviews.Plugin.read_more();

                    $('.wp-review-hide', $reviews).unbind('click').click(function() {
                        TrustReviews.Admin.review_hide($(this));
                        return false;
                    });

                    /*
                     * Render stats
                     */

                    // 1) Grouped by Google place ID
                    var gs = {};
                    for (var s = 0; s < res.stats.length; s++) {
                        var stat = res.stats[s],
                            gpi = stat.biz_id;

                        gs[gpi] = gs[gpi] || [];
                        gs[gpi].push({
                            time: parseInt(stat.time),
                            rating: parseFloat(stat.rating),
                            review_count: parseInt(stat.review_count)
                        });
                    }

                    // 2) Calculate how many months needs
                    var period = parseInt((res.stats[0].time - res.stats[res.stats.length - 1].time) / (60 * 60 * 24 * 30)),
                        months = period > 4 ? MONTHS : (period || 1);

                    // 2) Calculate stats by months (last six)
                    var ms = {},
                        today = new Date();

                    for (var i = 0; i < months; i++) {
                        var startDay = new Date(today.getFullYear(), today.getMonth() - i, 1),
                            endTime  = new Date(today.getFullYear(), today.getMonth() + 1 - i, 0).getTime(),
                            month    = startDay.toLocaleString('default', {month: 'short'}) + ' ' + startDay.getFullYear().toString().slice(-2);

                        ms[month] = ms[month] || {};

                        for (g in gs) {
                            var j = 0, xx = gs[g];

                            do {
                                var stat = xx[j++],
                                    time = stat.time * 1000;

                                ms[month][g] = ms[month][g] || {};
                                ms[month][g].count = parseInt(stat.review_count);

                            } while(time > endTime && j < xx.length);
                        }
                    }

                    // 3) Summary and normalize
                    var cat = [], data = [], series = []; var ttt = {};
                    for (m in ms) {
                        var count = 0;
                        for (p in ms[m]) {
                            count += ms[m][p].count;

                            // --- TEMP ---
                            var pp = res.places.find(x => x.id == p)
                            ttt[pp.name] = ttt[pp.name] || {};
                            ttt[pp.name].data = ttt[pp.name].data || [];
                            ttt[pp.name].data.unshift(ms[m][p].count);
                            // --- TEMP ---

                        }
                        cat.unshift(m);
                        data.unshift(count);
                    }

                    // --- TEMP ---
                    for (tt in ttt) {
                        series.push({name: tt, data: ttt[tt].data});
                    }
                    // --- TEMP ---

                    // 4) Render chart
                    var options = {
                        series: [{
                            name: 'Reviews',
                            data: data
                        }],
                        chart: {
                            height: 350,
                            type: 'bar',
                            //stacked: true,
                        },
                        plotOptions: {
                            bar: {
                                dataLabels: {
                                    position: 'top', // top, center, bottom
                                },
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            offsetY: -20,
                            style: {
                                fontSize: '12px',
                                colors: ["#304758"]
                            }
                        },
                        tooltip: {
                            enabled: true,
                            intersect: false,
                            custom: function() { return ''; }
                        },
                        xaxis: {
                            categories: cat,
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false
                            },
                            tooltip: {
                                enabled: true
                            }
                        },
                        yaxis: {
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false,
                            }
                        },
                        title: {
                            text: 'Monthly reviews count',
                            align: 'center',
                            style: {
                                color: '#444'
                            }
                        }
                    };

                    if (chart) {
                        chart.updateOptions({series: [{name: 'Reviews', data: data}], xaxis: {categories: cat}});
                    } else {
                        chart = new ApexCharts(document.querySelector('#chart'), options);
                        chart.render();
                    }

                    cb && cb(res);
                }
            });
        }
    }

    function jq(c, r) {
        return $(c.replace(/(\.|\#)/g, '$1' + slg + '-').replace(/\-\_/g, '_'), r);
    }

    function n(n) {
        return slg + n;
    }

});