var Dba = function($)
{
    'use strict';
    var $document = $(document),
        $window = $(window);

    return {
        initialize: function()
        {
            this._options = new Hash();
            this.tooltip();
            this.confirmation();
        },

        confirmation: function()
        {
            $('[data-toggle=confirmation]').confirmation({
                rootSelector: '[data-toggle=confirmation]'
            });
        },

        tooltip: function()
        {
            $('[data-toggle="popover"]').popover({html: true});
            $('[data-toggle="tooltip"]').tooltip();
        },

        registerForm: function($assetPath, $formChoice)
        {
            $('#player_registration_class').on('change keyup', function() {
                $('.class-list').addClass('hide').removeClass('show');
                $('.class-list' + $(this).val()).addClass('show');
            });
            this.appearance($assetPath, $formChoice);
        },

        setOption: function($key, $value)
        {
            this._options.set($key, $value);
        },

        getOption: function($key)
        {
            return this._options.get($key);
        },

        reload: function($onlyMenu)
        {
            var $this = this;
            if ($onlyMenu === true) {
                $.getJSON(
                    $this.getOption('router').refresh,
                    function (data) {
                        $('.player-info').html(data.content.player);
                        $('.movement-info').html(data.content.movement);
                    }
                );
            } else {
                $.getJSON(
                    $this.getOption('router').map,
                    function (data) {
                        $('.map-container').html(data.content);
                        $this.tooltip();
                        $this.centerMap();
                    }
                );
                $.getJSON(
                    $this.getOption('router').search,
                    function (data) {
                        $('.search-container').html(data.content);
                    }
                );
            }
        },

        minimap: function()
        {
            $("#minimap area").on('mouseover', function(e) {
                var $coords = $(this).attr('coords').split(','),
                    $tooltip = $('#' + $(this).attr('aria-describedby'));
                $tooltip.css({
                    top: parseInt($coords[1]) - Math.floor(($tooltip.height() * 2) / 1.5),
                    left: parseInt($coords[0]) - ($tooltip.width() / 2)
                });
            }).on('click', function() {
                return false;
            });
        },

        centerMap: function()
        {
            var $mapContainer = $('.map-container');
            var $maxWidth = $window.width();
            var $maxHeight   = $window.height();
            var $width  = $('.map-container .map').outerWidth(true);
            var $height = $('.map-container .map').outerHeight(true);

            $mapContainer.scrollLeft($maxWidth > $width ? $maxWidth : $width);
            $mapContainer.scrollTop($maxHeight > $height ? $maxHeight : $height);
            $mapContainer.scrollLeft(Math.round($mapContainer.scrollLeft() / 2));
            $mapContainer.scrollTop(Math.round($mapContainer.scrollTop() / 2));
        },

        map: function()
        {
            var $this = this;
            $this.centerMap();
            $document.on('click', '#refresh, .back-to-map', function($event) {
                $this.reload();
                return false;
            });

            $document.on('click', '.search-container a', function () {
                if ($(this).data('ajax') === false) {
                    return true;
                }

                $.getJSON(
                    $(this).prop('href'),
                    function (data) {
                        if ($.isEmptyObject(data.content)) {
                            $this.reload();
                        } else {
                            $('.search-container').html(data.content);
                            $this.reload(true);
                            $this.scrollTo($('.search-container'));
                        }
                    }
                );
                return false;
            });

            $document.on('submit', '.search-container form', function () {
                $.post(
                    $(this).prop('action'),
                    $(this).serializeObject(),
                    function (data) {
                        if ($.isEmptyObject(data.content)) {
                            $this.reload();
                        } else {
                            $('.search-container').html(data.content);
                            $this.reload(true);
                            $this.scrollTo($('.search-container'));
                        }
                    }
                );
                return false;
            });
        },

        scrollTo: function($element)
        {
            $('html, body').animate({
                scrollTop: $element.offset().top
            }, 1000);
        },

        movement: function()
        {
            var $this = this;
            $(document).on('click', '#move-block a, a[data-ajax=true]', function() {
                $.getJSON(
                    $(this).prop('href'),
                    function (data) {
                        $('.player-info').html(data.content.player);
                        $('.movement-info').html(data.content.movement);
                        $this.reload();
                    }
                );
                return false;
            });
        },

        inbox: function()
        {
            $document.on('click', '#inbox a', function() {
                var url =$(this).prop('href');
                $.getJSON(
                    url,
                    function (data, textStatus, jqXHR) {
                        $('#inbox-content').html(data.content);
                        history.pushState(null, null, url);
                    }
                );
                return false;
            });
            this.collectionType();
        },

        collectionType: function()
        {
            $document.on('click', '.btn-add[data-target]', function($event) {
                var $collectionHolder = $('#' + $(this).attr('data-target'));
                if (!$collectionHolder.attr('data-counter')) {
                    $collectionHolder.attr('data-counter', $collectionHolder.children().length);
                }

                var $prototype = $collectionHolder.attr('data-prototype');
                var $form = $prototype.replace(/__name__/g, $collectionHolder.attr('data-counter'));

                $collectionHolder.attr('data-counter', Number($collectionHolder.attr('data-counter')) + 1);
                $collectionHolder.append($form);
                return $event && $event.preventDefault();
            });

            $document.on('click', '.btn-remove[data-related]', function($event) {
                $('*[data-content="' + $(this).attr('data-related') + '"]').remove();
                return $event && $event.preventDefault();
            });
        },

        appearance: function($assetPath, $formChoice, $defaultImage)
        {
            var $playerAppearanceImage = $('#player_registration_appearance_image,#player_appearance_image'),
                $playerAppearanceType = $('#player_registration_appearance_type,#player_appearance_type'),
                $playerRace = $('#player_registration_race,#player_race');

            if (!$playerRace.is(':hidden')) {
                $playerRace.prepend(new Option($formChoice, '0')).val(0);
            }

            $playerRace.on('change keyup', function() {
                var $race = parseInt($(this).val()),
                    $list = $playerAppearanceType.data('list')[$race];

                $('.race-list').addClass('hide').removeClass('show');
                $('.race-list' + $race).addClass('show');
                $playerAppearanceImage.prop('disabled', true).addClass('hide').removeClass('show');
                $playerAppearanceType.prop('disabled', true).addClass('hide').removeClass('show');
                $playerAppearanceType.html('');

                if ($list === undefined || $list.length === 0) {
                    $playerAppearanceType.prop('disabled', true).addClass('hide');
                } else {
                    $.each($list, function(text, value) {
                        $playerAppearanceType.append(new Option(text, value));
                    });
                    $playerAppearanceType.prop('disabled', false).addClass('show');
                }
            });

            $playerAppearanceType.on('change keyup', function() {
                var $type = $(this).val(),
                    $list = $playerAppearanceImage.data('list')[$type];

                $playerAppearanceImage.html('');
                $.each($list, function(text, value) {

                    $playerAppearanceImage.append(new Option(text, value));
                });
                $playerAppearanceImage.prop('disabled', false).addClass('show');
            });

            $playerAppearanceImage.on('change keyup', function() {
                if ($(this).val() !== '') {
                    $('.perso-images').prop('src', $assetPath + '/' + $(this).val());
                }
            });

            $playerRace.trigger('change');
            if ($defaultImage !== undefined) {
                var $list = $playerAppearanceImage.data('list'),
                    $found = false;
                $.each($list, function($type, $values) {
                    $.each($values, function($key, $value) {
                        if ($value === $defaultImage) {
                            $playerAppearanceType.val($type).trigger('change');
                            $playerAppearanceImage.val($value).trigger('change');
                            $found = true;
                            return false;
                        }
                    });

                    if ($found) {
                        return false;
                    }
                });
            }
        }
    };
}(jQuery);

Dba.initialize();
