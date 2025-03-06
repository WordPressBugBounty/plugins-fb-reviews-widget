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
        return '' +
        '<svg viewBox="0 0 1792 1792" width="' + size + '" height="' + size + '">' +
            '<use xlink:href="#' + TRUSTREVIEWS_VARS.slg + '-star' + prefix + '" fill="' + color + '"/>' +
        '</svg>';
    },

    review: function(review) {
        return '' +
        ('<div class="{slg}-list-review' + (review.hide == '' ? '' : ' wp-review-hidden') + '" data-rev="' + review.provider + '">' +
            '<div class="{slg}-right">' +
                '<a href="' + review.author_url + '" class="{slg}-name" target="_blank" rel="nofollow noopener">' + review.author_name + '</a>' +
                '<div class="{slg}-time" data-time="' + review.time + '"></div>' +
                '<div class="{slg}-feedback">' +
                    this.stars(review.rating, '#fb8e28', 16) +
                    '<span class="{slg}-text">' + this.trimtext(review.text, 50) + '</span>' +
                '</div>' +
                '<a href="#" class="wp-review-hide" data-id="' + review.id + '">' + (review.hide == '' ? 'Hide' : 'Show') + ' review</a>' +
            '</div>' +
        '</div>').replace(/{slg}/g, TRUSTREVIEWS_VARS.slg);
    },

    trimtext: function(text, size) {
        if (size && text && text.length > size) {
            var subtext = text.substring(0, size),
                idx = subtext.indexOf(' ') + 1;

            if (idx < 1 || size - idx > (size / 2)) {
                idx = size;
            }

            var visibletext = '', invisibletext = '';
            if (idx > 0) {
                visibletext = text.substring(0, idx - 1);
                invisibletext = text.substring(idx - 1, text.length);
            }

            return visibletext +
                   (invisibletext ?
                   '<span>... </span>' +
                   '<span class="wp-more">' + invisibletext + '</span>' +
                   '<span class="wp-more-toggle">read more</span>' : '');
        } else {
            return text;
        }
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
        $('svg', $rateus).click(function() {
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
                            '<div class="{slg}-overview-h">' + place.name + '</div>' +
                            '<div>' +
                                '<span class="{slg}-rating">' + res.rating + '</span>' +
                                '<span class="{slg}-stars">' + TrustReviews.Admin.stars(res.rating, '#fb8e28', 20) + '</span>' +
                            '</div>' +
                            '<div class="{slg}-powered">Based on ' + res.review_count + ' reviews</div>' +

                            (place.updated ?
                            '<div class="{slg}-powered">Last updated: ' +
                                '<span class="{slg}-time">' +
                                    WPacTime.getTime(parseInt(place.updated), TrustReviews.Plugin.lang(), 'ago') +
                                '</span>' +
                            '</div>' : '') +
                        '</div>').replace(/{slg}/g, slg)
                    );

                    /*
                     * Render reviews
                     */
                    var list = '';
                    $.each(res.reviews, function(i, review) {
                        list += TrustReviews.Admin.review(review);
                    });
                    $reviews.html('<div class="' + slg + ' wpac">' + list + '</div>');
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