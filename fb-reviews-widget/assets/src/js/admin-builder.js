var TrustReviews = TrustReviews || {};

/**
 * Reviews widget builder
 */
TrustReviews.Builder = function($, data) {

    const AUTOSAVE_KEYUP_TIMEOUT = 1500;
    var AUTOSAVE_TIMEOUT = null;

    const LANGS = [
        ['ar', 'Arabic'],
        ['bg', 'Bulgarian'],
        ['bn', 'Bengali'],
        ['ca', 'Catalan'],
        ['cs', 'Czech'],
        ['da', 'Danish'],
        ['de', 'German'],
        ['el', 'Greek'],
        ['en', 'English'],
        ['es', 'Spanish'],
        ['eu', 'Basque'],
        ['eu', 'Basque'],
        ['fa', 'Farsi'],
        ['fi', 'Finnish'],
        ['fil', 'Filipino'],
        ['fr', 'French'],
        ['gl', 'Galician'],
        ['gu', 'Gujarati'],
        ['hi', 'Hindi'],
        ['hr', 'Croatian'],
        ['hu', 'Hungarian'],
        ['id', 'Indonesian'],
        ['it', 'Italian'],
        ['iw', 'Hebrew'],
        ['ja', 'Japanese'],
        ['kn', 'Kannada'],
        ['ko', 'Korean'],
        ['lt', 'Lithuanian'],
        ['lv', 'Latvian'],
        ['ml', 'Malayalam'],
        ['mr', 'Marathi'],
        ['nl', 'Dutch'],
        ['no', 'Norwegian'],
        ['pl', 'Polish'],
        ['pt', 'Portuguese'],
        ['pt-BR', 'Portuguese (Brazil)'],
        ['pt-PT', 'Portuguese (Portugal)'],
        ['ro', 'Romanian'],
        ['ru', 'Russian'],
        ['sk', 'Slovak'],
        ['sl', 'Slovenian'],
        ['sr', 'Serbian'],
        ['sv', 'Swedish'],
        ['ta', 'Tamil'],
        ['te', 'Telugu'],
        ['th', 'Thai'],
        ['tl', 'Tagalog'],
        ['tr', 'Turkish'],
        ['uk', 'Ukrainian'],
        ['vi', 'Vietnamese'],
        ['zh', 'Chinese (Simplified)'],
        ['zh-Hant', 'Chinese (Traditional)']
    ];

    var HTML_CONTENT = '' +

        '<div class="{slg}-builder-platforms {slg}-builder-inside">' +

            '<div class="{slg}-connect-text">Connect Reviews</div>' +

            '<div class="{slg}-builder-connect {slg}-connect-facebook">' +
                '<svg viewBox="0 0 100 100" width="24" height="24" style="border-radius:50%;background:#0866FF;padding:1px;box-sizing:border-box;">' +
                    '<use xlink:href="#{slg}-logo-f"></use>' +
                '</svg>' +
            '</div>' +

            '<div class="{slg}-builder-connect" data-platform="google">' +
                '<svg viewBox="0 0 512 512" width="24" height="24"><use xlink:href="#{slg}-logo-g"></use></svg>' +
            '</div>' +

            '<div class="{slg}-builder-connect" data-platform="yelp">' +
                '<svg viewBox="0 0 533.33 533.33" width="24" height="24"><use xlink:href="#{slg}-logo-y"></use></svg>' +
            '</div>' +

            '<div class="{slg}-connections"></div>' +
        '</div>' +

        '<div class="{slg}-connect-options">' +

            '<div class="{slg}-builder-inside">' +

                '<div class="{slg}-builder-option">' +
                    'Layout' +
                    '<select id="view_mode" name="view_mode">' +
                        '<option value="slider" selected="selected">Slider</option>' +
                        '<option value="grid">Grid</option>' +
                        '<option value="list">List</option>' +
                    '</select>' +
                '</div>' +

            '</div>' +

            /* Common Options */
            '<div class="{slg}-builder-top {slg}-toggle">Common Options</div>' +
            '<div class="{slg}-builder-inside" style="display:none">' +
                '<div class="{slg}-builder-option">' +
                    'Pagination' +
                    '<input type="text" name="pagination" value="">' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    'Maximum characters before \'read more\' link' +
                    '<input type="text" name="text_size" value="">' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="header_center" value="">' +
                        'Show rating by center' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="header_hide_photo" value="">' +
                        'Hide business photo' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="header_hide_name" value="">' +
                        'Hide business name' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="hide_based_on" value="">' +
                        'Hide \'Based on ... reviews\'' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="hide_writereview" value="">' +
                        'Hide \'review us on G\' button' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="header_hide_social" value="">' +
                        'Hide rating header, leave only reviews' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="hide_reviews" value="">' +
                        'Hide reviews, leave only rating header' +
                    '</label>' +
                '</div>' +
            '</div>' +

            /* Slider Options */
            '<div class="{slg}-builder-top {slg}-toggle">Slider Options</div>' +
            '<div class="{slg}-builder-inside" style="display:none">' +
                '<div class="{slg}-builder-option">' +
                    'Speed in second' +
                    '<input type="text" name="slider_speed" value="" placeholder="Default: 5">' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    'Text height' +
                    '<input type="text" name="slider_text_height" value="" placeholder="Default: 100px">' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="slider_autoplay" value="" checked>' +
                        'Auto-play' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="slider_hide_prevnext" value="">' +
                        'Hide prev & next buttons' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="slider_hide_dots" value="">' +
                        'Hide dots' +
                    '</label>' +
                '</div>' +
            '</div>' +

            /* Style Options */
            '<div class="{slg}-builder-top {slg}-toggle">Style Options</div>' +
            '<div class="{slg}-builder-inside" style="display:none">' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="dark_theme">' +
                        'Dark background' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="hide_backgnd" value="">' +
                        'Hide reviews background' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="show_round" value="">' +
                        'Round reviews borders' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="show_shadow" value="">' +
                        'Show reviews shadow' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="centered" value="">' +
                        'Place by center (only if max-width is set)' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    'Container max-width' +
                    '<input type="text" name="max_width" value="" placeholder="for instance: 300px">' +
                    '<small>Be careful: this will make reviews unresponsive</small>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    'Container max-height' +
                    '<input type="text" name="max_height" value="" placeholder="for instance: 500px">' +
                '</div>' +
            '</div>' +

            /* Advance Options */
            '<div class="{slg}-builder-top {slg}-toggle">Advance Options</div>' +
            '<div class="{slg}-builder-inside" style="display:none">' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="lazy_load_img" checked>' +
                        'Lazy load images' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="google_def_rev_link">' +
                        'Use default Google reviews link' +
                    '</label>' +
                    '<span class="{slg}-quest {slg}-quest-top {slg}-toggle" title="Click to help">?</span>' +
                    '<div class="{slg}-quest-help" style="display:none;">If the direct link to all reviews <b>https://search.google.com/local/reviews?placeid=&lt;PLACE_ID&gt;</b> does not work with your Google place (leads to 404), please use this option to use the default reviews link to Google map.</div>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="open_link" checked>' +
                        'Open links in new Window' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="nofollow_link" checked>' +
                        'Use no follow links' +
                    '</label>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    'Reviewer avatar size' +
                    '<select name="reviewer_avatar_size">' +
                        '<option value="56" selected="selected">Small: 56px</option>' +
                        '<option value="128">Medium: 128px</option>' +
                        '<option value="256">Large: 256px</option>' +
                    '</select>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    'Cache data' +
                    '<select name="cache">' +
                        '<option value="1">1 Hour</option>' +
                        '<option value="3">3 Hours</option>' +
                        '<option value="6">6 Hours</option>' +
                        '<option value="12" selected="selected">12 Hours</option>' +
                        '<option value="24">1 Day</option>' +
                        '<option value="48">2 Days</option>' +
                        '<option value="168">1 Week</option>' +
                        '<option value="">Disable (NOT recommended)</option>' +
                    '</select>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    'Reviews limit' +
                    '<input type="text" name="reviews_limit" value="">' +
                '</div>' +
            '</div>' +

        '</div>' +

        '<div id="{slg}-connect-wizard" title="Easy steps to connect reviews" style="display:none;">' +
            '<div data-platform="google">' +
                /*'<p>' +
                    '<span>1</span> ' +
                    'Find your Google place on the map below (<u class="{slg}-wiz-arr">Enter a location</u>) and copy found <u><b>Place ID</b></u>' +
                '</p>' +
                '<iframe src="https://geo-devrel-javascript-samples.web.app/samples/places-placeid-finder/app/dist" loading="lazy" style="width:100%;height:250px"></iframe>' +
                '<small style="font-size:13px;color:#555">If you can\'t find your place on this map, please read <a href="' + data.supportUrl + '&{slg}_tab=fig#pid" target="_blank">this manual how to find any Google Place ID</a>.</small>' +
                '<p>' +
                    '<span>2</span> ' +
                    'Paste copied <u><b>Place ID</b></u> in this field and select language if needed' +
                '</p>' +*/
                '<iframe id="grc" src="https://app.trustembed.com/grc?authcode={{authcode}}" style="width:100%;height:400px"></iframe>' +
                '<small class="grw-connect-error"></small>' +
            '</div>' +
            '<div data-platform="other">' +
                '<p>' +
                    '<span>1</span> ' +
                    'Find your Yelp business on yelp.com' +
                '</p>' +
                '<p>' +
                    '<span>2</span> ' +
                    'Copy & paste Yelp business URL to the field below' +
                '</p>' +
                '<p>' +
                    '<input type="text" class="{slg}-connect-id" value="" placeholder="Yelp business link" />' + lang('Choose language if needed') +
                '</p>' +
                '<p>' +
                    '<span>3</span> Click CONNECT REVIEWS button' +
                '</p>' +
                '<button class="{slg}-connect-btn">Connect Reviews</button>' +
            '</div>' +
            '<small class="{slg}-connect-error"></small>' +
        '</div>';

    function feed_save_ajax() {
        if (!el('title').value) {
            el('title').focus();
            return false;
        }

        el('save').innerText = 'Auto save, wait';
        el('save').disabled = true;

        $.post(ajaxurl, {
            post_id  : el('post_id').value,
            title    : el('title').value,
            content  : content().value,
            action   : data.slg + '_feed_save_ajax',
            _wpnonce : $('#_wpnonce').val()
        }, function(res) {

            var wpgr = document.querySelectorAll('.' + data.slg);
            for (var i = 0; i < wpgr.length; i++) {
                wpgr[i].parentNode.removeChild(wpgr[i]);
            }

            el('collection_preview').innerHTML = res;

            $('.wp-review-hide').unbind('click').click(function() {
                TrustReviews.Admin.review_hide($(this));
                return false;
            });

            if (!el('post_id').value) {
                var post_id = document.querySelector('.' + data.slg).getAttribute('data-id');
                el('post_id').value = post_id;
                window.location.href = window.location.href + '&' + data.slg + '_feed_id=' + post_id + '&' + data.slg + '_feed_new=1';
            } else {
                var $rateus = jq('#rate_us');
                if ($rateus.length && !$rateus.hasClass(data.slg + '-flash-visible') && !window[data.slg + '_rateus']) {
                    $rateus.addClass(data.slg + '-flash-visible');
                }
            }

            el('save').innerText = 'Save & Update';
            el('save').disabled = false;
            AUTOSAVE_TIMEOUT = null;
        });
    }

    function feed_save() {
        if (!el('title').value) {
            el('title').focus();
            return false;
        }

        var content = content().value;
        if (content) {
            var json = JSON.parse(content)
            if (json) {
                if (json.connections && json.connections.length) {
                    return true;
                }
            }
        }

        alert("Please click 'CONNECT GOOGLE' and connect your Google reviews then save this widget");
        return false;
    }

    function connection(authcode) {
        var connect_btn = jq('.connect-btn');

        connect_btn.click(function() {

            var connect_id_el = jq('.connect-id'),
                platform = connect_btn.parent().attr('data-platform');

            if (!connect_id_el.val()) {
                connect_id_el.focus();
                return false;
            }

            var id = (platform == 'yelp' ? /.+\/biz\/(.*?)(\?|\/|$)/.exec(connect_id_el.val())[1] : connect_id_el.val()),
                lang = jq('.connect-lang').val();

            connect_btn[0].innerHTML = 'Please wait...';
            connect_btn[0].disabled = true;

            connect_ajax({id: id, lang: lang, platform: platform, local_img: true}, authcode, 1);
            return false;
        });
    }

    function connect_ajax(params, authcode, attempt) {

        var platform = params.platform,
            connect_btn = jq('.connect-btn');

        el('save').innerText = 'Auto save, wait';
        el('save').disabled = true;

        $.post(ajaxurl, {
            id        : decodeURIComponent(params.id),
            lang      : params.lang,
            local_img : params.local_img || false,
            token     : params.token,
            feed_id   : $('input[name="' + data.slg + '_feed[post_id]"]').val(),
            action    : data.slg + '_connect_' + platform,
            v         : new Date().getTime(),
            _wpnonce  : $('#_wpnonce').val()
        }, function(res) {

            console.log('connect_debug:', res);

            //connect_btn[0].innerHTML = 'Connect ' + (platform.charAt(0).toUpperCase() + platform.slice(1));
            //connect_btn[0].disabled = false;

            var error_el = jq('.connect-error');

            if (res.status == 'success') {

                //error_el[0].innerHTML = '';

                try { jq('#connect-wizard').dialog('close'); } catch (e) {}

                var connection_params = {
                    id        : res.result.id,
                    lang      : params.lang,
                    name      : res.result.name,
                    photo     : res.result.photo,
                    refresh   : true,
                    local_img : params.local_img,
                    platform  : platform,
                    props     : {
                        default_photo : res.result.photo
                    }
                };

                connection_add(connection_params, authcode);
                serialize_connections();

            } else {

                switch (res.result.error_message) {

                    case 'usage_limit':
                        $('#dialog').dialog({width: '50%', maxWidth: '600px'});
                        break;

                    case 'bot_check':
                        if (attempt > 1) {
                            return;
                        }
                        popup(data.gAppUrl + '/botcheck?authcode=' + authcode, 640, 480, function() {
                            connect_ajax(params, authcode, attempt + 1);
                        });
                        break;

                    default:
                        if (res.result.error_message.indexOf('The provided Place ID is no longer valid') >= 0) {
                            error_el[0].innerHTML = 'It seems Google place which you are trying to connect ' +
                                'does not have a physical address (it\'s virtual or service area), ' +
                                'unfortunately, Google Places API does not support such locations, it\'s a limitation of Google, not the plugin.<br><br>' +
                                'However, you can try to connect your Google reviews in our new cloud service ' +
                                '<a href="https://trust.reviews" target="_blank">Trust.Reviews</a> ' +
                                'and show it on your WordPress site through universal <b>HTML/JavaScript</b> code.';
                        } else {
                            error_el[0].innerHTML = '<b>Error</b>: ' + res.result.error_message;
                        }
                }
            }

        }, 'json');
    }

    function connection_fb(authcode) {
        // Facebook connection click
        jq('.connect-facebook').click(function() {
            var temp_code = randstr(16),
                url = data.fbAuthUrl + '?state=' + data.authcode + ':' + temp_code;

            popup(url, 670, 520, function() {

                $.ajax({
                    url      : data.fbAppUrl + '/accounts?temp_code=' + temp_code,
                    dataType : 'jsonp',
                    success  : function (pages) {

                        if (!pages || !pages.length) {
                            return;
                        }

                        //let checked = pages.length == 1 ? true : false;

                        $.each(pages, function(i, page) {
                            connection_add({
                                id           : page.id,
                                name         : page.name,
                                photo        : 'https://graph.facebook.com/' + page.id +  '/picture',
                                platform     : 'facebook',
                                rating_count : '',
                                props        : {
                                    default_photo : 'https://graph.facebook.com/' + page.id +  '/picture',
                                }
                            }, authcode/*, checked*/);
                        });
                        serialize_connections();

                        /*if (checked) {
                            serialize_connections();
                        } else {
                            var connections_el = jq('.connections')[0];
                            $(connections_el).addClass(data.slg + '-platform-multiple');
                        }*/
                    }
                });
            });
            return false;
        });
    }

    function connection_add(conn, authcode, checked) {

        var connected_id = connection_id(conn),
            connected_el = $('#' + connected_id);

        if (!connected_el.length) {
            connected_el = $('<div class="' + data.slg + '-connection"></div>')[0];
            connected_el.id = connected_id;
            if (conn.lang != undefined) {
                connected_el.setAttribute('data-lang', conn.lang);
            }
            connected_el.setAttribute('data-platform', conn.platform);
            connected_el.innerHTML = connection_render(conn, checked);

            var connections_el = jq('.connections')[0];
            connections_el.appendChild(connected_el);

            jq('.toggle', connected_el).unbind('click').click(function () {
                $(this).toggleClass('toggled');
                $(this).next().slideToggle();
            });

            var file_frame;
            jq('.connect-photo-change', connected_el).on('click', function(e) {
                e.preventDefault();
                upload_photo(connected_el, file_frame, function() {
                    serialize_connections();
                });
                return false;
            });

            jq('.connect-photo-default', connected_el).on('click', function(e) {
                change_photo(connected_el, conn.props.default_photo);
                serialize_connections();
                return false;
            });

            $('input[type="text"]', connected_el).keyup(function() {
                clearTimeout(AUTOSAVE_TIMEOUT);
                AUTOSAVE_TIMEOUT = setTimeout(serialize_connections, AUTOSAVE_KEYUP_TIMEOUT);
            });

            $('input[type="checkbox"]', connected_el).click(function() {
                serialize_connections();
            });

            $('select.' + data.slg + '-connect-lang', connected_el).change(function() {
                conn.lang = this.value;
                connected_el.id = connection_id(conn);
                connected_el.setAttribute('data-lang', this.value);
                connect_ajax(conn, authcode, 1);
                return false;
            });

            $('input[name="local_img"]', connected_el).unbind('click').click(function() {
                conn.local_img = this.checked;
                connect_ajax(conn, authcode, 1);
            });

            jq('.connect-reconnect', connected_el).click(function() {
                connect_ajax(conn, authcode, 1);
                return false;
            });

            jq('.connect-delete', connected_el).click(function() {
                if (confirm('Are you sure to delete this business?')) {
                    $(connected_el).remove();
                    serialize_connections();
                }
                return false;
            });
        }
    }

    function connection_id(conn) {
        var id = data.slg + '-' + conn.platform + '-' + conn.id.replace(/\//g, '');
        if (conn.lang != null) {
            id += conn.lang;
        }
        return id;
    }

    function connection_render(conn, checked) {
        var name = conn.name;
        if (conn.lang) {
            name += ' (' + conn.lang + ')';
        }

        conn.photo = conn.photo || 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

        var option = document.createElement('option');
        if (conn.platform == 'google' && conn.props && conn.props.pid) {
            option.value = conn.props.pid;
        } else {
            option.value = conn.id;
        }
        option.text = capitalize(conn.platform) + ': ' + conn.name;

        var html = '' +
            '<div class="{slg}-toggle {slg}-builder-connect {slg}-connect-business">' +
                '<input type="checkbox" class="{slg}-connect-select" onclick="event.stopPropagation();" ' + (checked?'checked':'') + ' /> ' +
                name + (conn.address ? ' (' + conn.address + ')' : '') +
            '</div>' +
            '<div style="display:none">' +
                (function(props) {
                    var result = '';
                    for (prop in props) {
                        if (prop != 'platform' && Object.prototype.hasOwnProperty.call(props, prop)) {
                            result += '<input type="hidden" name="' + prop + '" value="' + props[prop] + '" class="{slg}-connect-prop" readonly />';
                        }
                    }
                    return result;
                })(conn.props) +

                '<input type="hidden" name="id" value="' + conn.id + '" readonly />' +
                (conn.address ? '<input type="hidden" name="address" value="' + conn.address + '" readonly />' : '') +
                (conn.access_token ? '<input type="hidden" name="access_token" value="' + conn.access_token + '" readonly />' : '') +

                '<div class="{slg}-builder-option">' +
                    '<img src="' + conn.photo + '" alt="' + conn.name + '" class="{slg}-connect-photo">' +
                    '<a href="#" class="{slg}-connect-photo-change">Change</a>' +
                    '<a href="#" class="{slg}-connect-photo-default">Default</a>' +
                    '<input type="hidden" name="photo" class="{slg}-connect-photo-hidden" value="' + conn.photo + '" tabindex="2"/>' +
                '</div>' +
                '<div class="{slg}-builder-option">' +
                    '<input type="text" name="name" value="' + conn.name + '" />' +
                '</div>' +

                (conn.website != undefined ?
                '<div class="{slg}-builder-option">' +
                    '<input type="text" name="website" value="' + conn.website + '" />' +
                '</div>' : '' ) +

                (conn.lang != undefined ?
                '<div class="{slg}-builder-option">' +
                    //'<input type="text" name="lang" value="' + conn.lang + '" placeholder="Default language (English)" />' +
                    lang('Show all connected languages', conn.lang) +
                '</div>' : '' ) +

                (conn.review_count != undefined ?
                '<div class="{slg}-builder-option">' +
                    '<input type="text" name="review_count" value="' + conn.review_count + '" placeholder="Total number of reviews" />' +
                    '<span class="{slg}-quest {slg}-toggle" title="Click to help">?</span>' +
                    '<div class="{slg}-quest-help">Google return only 5 most helpful reviews and does not return information about total number of reviews and you can type here it manually.</div>' +
                '</div>' : '' ) +

                (conn.platform == 'facebook' ?
                '<div class="{slg}-builder-option">' +
                    '<input type="text" name="rating_count" value="' + (conn.rating_count || '') + '" placeholder="Reviews count adder" />' +
                    '<span class="{slg}-quest {slg}-toggle" title="Click to help">?</span>' +
                    '<div class="{slg}-quest-help">Facebook returns incorrect number of reviews for some pages. We reported a <a href="https://developers.facebook.com/support/bugs/570160061284085/" target="_blank">bug</a>, which, unfortunately, has not been fixed.<br>If you have this situation and your FB reviews count is incorrect, just <b>enter the difference between the current and the correct reviews count</b> in this option and the plugin will show the correct count.</div>' +
                '</div>' : '' ) +

                (conn.refresh != undefined ?
                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="refresh" ' + (conn.refresh ? 'checked' : '') + '>' +
                        'Update reviews daily' +
                    '</label>' +
                    '<span class="{slg}-quest {slg}-quest-top {slg}-toggle" title="Click to help">?</span>' +
                    '<div class="{slg}-quest-help">' +
                        (conn.platform == 'google' ? 'The plugin uses the Google Places API to get your reviews. <b>The API only returns the 5 most helpful reviews (it\'s a limitation of Google, not the plugin)</b>. This option calls the Places API once in 24 hours (to keep the plugin\'s free and avoid a Google Billing) to check for a new reviews and if there are, adds to the plugin. Thus slowly building up a database of reviews.<br><br>Also if you see the new reviews on Google map, but after some time it\'s not added to the plugin, it means that Google does not include these reviews to the API and the plugin can\'t get this.<br><br>If you need to show <b>all reviews</b>, please use <a href="https://trust.reviews" target="_blank">Business plugin</a> which uses a Google My Business API without API key and billing.' : '') +
                        (conn.platform == 'yelp' ? 'The plugin uses the Yelp API to get your reviews. <b>The API only returns the 3 most helpful reviews without sorting possibility.</b> When Yelp changes the 3 most helpful the plugin will automatically add the new one to your database. Thus slowly building up a database of reviews.' : '') +
                    '</div>' +
                '</div>' : '' ) +

                '<div class="{slg}-builder-option">' +
                    '<label>' +
                        '<input type="checkbox" name="local_img" ' + (conn.local_img ? 'checked' : '') + '>' +
                        'Save images locally (GDPR)' +
                    '</label>' +
                '</div>' +

                (conn.platform != 'facebook' ?
                '<div class="{slg}-builder-option">' +
                    '<button class="{slg}-connect-reconnect">Reconnect</button>' +
                '</div>' : '' ) +

                '<div class="{slg}-builder-option">' +
                    '<button class="{slg}-connect-delete">Delete connection</button>' +
                '</div>' +
            '</div>';

        return html.replace(/{slg}/g, data.slg);
    }

    function serialize_connections() {

        var connections = [],
            connections_el = document.querySelectorAll('.' + data.slg + '-connection');

        for (var i in connections_el) {
            if (Object.prototype.hasOwnProperty.call(connections_el, i)) {

                var select_el = connections_el[i].querySelector('.' + data.slg + '-connect-select');
                if (select_el && !is_hidden(select_el) && !select_el.checked) {
                    continue;
                }

                var connection = {},
                    lang       = connections_el[i].getAttribute('data-lang'),
                    platform   = connections_el[i].getAttribute('data-platform'),
                    inputs     = connections_el[i].querySelectorAll('input');

                //connections[platform] = connections[platform] || [];

                if (lang != undefined) {
                    connection.lang = lang;
                }

                for (var j in inputs) {
                    if (Object.prototype.hasOwnProperty.call(inputs, j)) {
                        var input = inputs[j],
                            name = input.getAttribute('name');

                        if (!name) continue;

                        if (input.className == data.slg + '-connect-prop') {
                            connection.props = connection.props || {};
                            connection.props[name] = input.value;
                        } else {
                            connection[name] = (input.type == 'checkbox' ? input.checked : input.value);
                        }
                    }
                }
                connection.platform = platform;
                connections.push(connection);
            }
        }

        var options = {},
            options_el = document.querySelector('.' + data.slg + '-connect-options').querySelectorAll('input[name],select,textarea');

        for (var o in options_el) {
            if (Object.prototype.hasOwnProperty.call(options_el, o)) {
                var input = options_el[o],
                    name  = input.getAttribute('name');

                if (input.type == 'checkbox') {
                    options[name] = input.checked;
                } else if (input.value != undefined) {
                    options[name] = (
                                        input.type == 'textarea'     ||
                                        name       == 'word_filter'  ||
                                        name       == 'word_exclude' ?
                                        encodeURIComponent(input.value) : input.value
                                    );
                }
            }
        }

        content().value = JSON.stringify({connections: connections, options: options});

        if (connections.length) {
            var first = connections[0],
                title = el('title').value;

            if (!title) {
                el('title').value = first.name;
            }
            feed_save_ajax();
        }
    }

    function deserialize_connections(OPTS_EL, data) {
        var connections = data.conns,
            options = connections.options;

        if (Array.isArray(connections.connections)) {
            connections = connections.connections;
        } else {
            var temp_conns = [];
            if (Array.isArray(connections.google)) {
                for (var c = 0; c < connections.google.length; c++) {
                    connections.google[c].platform = 'google';
                }
                temp_conns = temp_conns.concat(connections.google);
            }
            if (Array.isArray(connections.facebook)) {
                for (var c = 0; c < connections.facebook.length; c++) {
                    connections.facebook[c].platform = 'facebook';
                }
                temp_conns = temp_conns.concat(connections.facebook);
            }
            if (Array.isArray(connections.yelp)) {
                for (var c = 0; c < connections.yelp.length; c++) {
                    connections.yelp[c].platform = 'yelp';
                }
                temp_conns = temp_conns.concat(connections.yelp);
            }
            connections = temp_conns;
        }

        for (var i = 0; i < connections.length; i++) {
            connection_add(connections[i], data.authcode, true);
        }

        for (var opt in options) {
            if (Object.prototype.hasOwnProperty.call(options, opt)) {
                var control = OPTS_EL.querySelector('input[name="' + opt + '"],select[name="' + opt + '"],textarea[name="' + opt + '"]');
                if (control) {
                    var name = control.getAttribute('name');
                    if (typeof(options[opt]) === 'boolean') {
                        control.checked = options[opt];
                    } else {
                        control.value = (
                                            control.type == 'textarea'     ||
                                            name         == 'word_filter'  ||
                                            name         == 'word_exclude' ?
                                            decodeURIComponent(options[opt]) : options[opt]
                                        );
                        if (opt.indexOf('_photo') > -1 && control.value) {
                            control.parentNode.querySelector('img').src = control.value;
                        }
                    }
                }
            }
        }
    }

    function upload_photo(cnt, file_frame, cb) {
        if (file_frame) {
            file_frame.open();
            return;
        }

        file_frame = wp.media.frames.file_frame = wp.media({
            title: $(this).data('uploader_title'),
            button: {text: $(this).data('uploader_button_text')},
            multiple: false
        });

        file_frame.on('select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            change_photo(cnt, attachment.url);
            cb && cb(attachment.url);
        });
        file_frame.open();
    }

    function change_photo(cnt, photo_url) {
        var place_photo_hidden = jq('.connect-photo-hidden', cnt),
            place_photo_img = jq('.connect-photo', cnt);

        place_photo_hidden.val(photo_url);
        place_photo_img.attr('src', photo_url);
        place_photo_img.show();

        serialize_connections();
    }

    function popup(url, width, height, cb) {
        var top = top || (screen.height/2)-(height/2),
            left = left || (screen.width/2)-(width/2),
            win = window.open(url, '', 'location=1,status=1,resizable=yes,width='+width+',height='+height+',top='+top+',left='+left);
        function check() {
            if (!win || win.closed != false) {
                cb();
            } else {
                setTimeout(check, 100);
            }
        }
        setTimeout(check, 100);
    }

    function randstr(len) {
       var result = '',
           chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
           charsLen = chars.length;
       for ( var i = 0; i < len; i++ ) {
          result += chars.charAt(Math.floor(Math.random() * charsLen));
       }
       return result;
    }

    function is_hidden(e) {
        return e.offsetParent === null;
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function lang(defname, lang) {
        var html = '';
        for (var i = 0; i < LANGS.length; i++) {
            html += '<option value="' + LANGS[i][0] + '"' + (lang == LANGS[i][0] ? ' selected="selected"' : '') + '>' + LANGS[i][1] + '</option>';
        }
        return '<select class="{slg}-connect-lang" name="lang">' +
                   '<option value=""' + (lang ? '' : ' selected="selected"') + '>' + defname + '</option>' +
                   html +
               '</select>';
    }

    function el(id) {
        return window[data.slg + '_' + id];
    }

    function jq(c, r) {
        return $(c.replace(/(\.|\#)/g, '$1' + data.slg + '-').replace(/\-\_/g, '_'), r);
    }

    function content() {
        return document.getElementById(data.slg + '-builder-connection');
    }

    return THIS = {

        init: function() {
            var OPTS_EL = document.querySelector(data.opt_el);
            if (!OPTS_EL) return;

            OPTS_EL.innerHTML = HTML_CONTENT.replace(/{slg}/g, data.slg).replace('{{authcode}}', data.authcode);

            if (data.conns && data.conns.connections && data.conns.connections.length) {
                deserialize_connections(OPTS_EL, data);
            }

            var $connect_wizard_el = jq('#connect-wizard');

            jq('.builder-connect[data-platform]').click(function () {
                let platform = this.getAttribute('data-platform');
                $connect_wizard_el.attr('data-platform', platform);
                $connect_wizard_el.dialog({modal: true, width: '50%', maxWidth: '600px'});
            });

            // GRC
            window.onmessage = function(e) {
                if (e.origin !== 'https://app.trustembed.com') return;
                if (e.data) {
                    let data = e.data;
                    switch (data.action) {
                        case 'connect':
                            connect_ajax(data, data.authcode, 1);
                            break;
                    }
                }
            };

            // Google & Yelp Connects
            connection(data.authcode);

            // Facebook Connect
            connection_fb(data.authcode);

            jq('.connect-options input[type="text"],.connect-options textarea').keyup(function() {
                clearTimeout(AUTOSAVE_TIMEOUT);
                AUTOSAVE_TIMEOUT = setTimeout(serialize_connections, AUTOSAVE_KEYUP_TIMEOUT);
            });
            jq('.connect-options input[type="checkbox"],.connect-options select').change(function() {
                serialize_connections();
            });

            jq('.toggle', OPTS_EL).unbind('click').click(function () {
                $(this).toggleClass('toggled');
                $(this).next().slideToggle();
            });

            if (jq('.connections').sortable) {
                jq('.connections').sortable({
                    stop: function(event, ui) {
                        serialize_connections();
                    }
                });
                jq('.connections').disableSelection();
            }

            $('.wp-review-hide').click(function() {
                TrustReviews.Admin.review_hide($(this));
                return false;
            });

            jq('#_save').click(function() {
                serialize_connections();
                return false;
            });

            window.addEventListener('beforeunload', function(e) {
                if (!AUTOSAVE_TIMEOUT) return undefined;

                var msg = 'It looks like you have been editing something. If you leave before saving, your changes will be lost.';
                (e || window.event).returnValue = msg;
                return msg;
            });
        }

    };

};