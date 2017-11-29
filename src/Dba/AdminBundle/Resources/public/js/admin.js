/*jshint esversion: 6 */
var DbaAdmin = function($)
{
    'use strict';
    const MODE_SELECT = 'select';
    const MODE_BOX = 'box';
    const MODE_MULTI = 'multi';
    const MODE_BLOCK = 'block';

    var $document = $(document),
        $selectedBoxes = {},
        $changesBoxes = {},
        $blockBeginX = 0,
        $blockBeginY = 0,
        $blockEndX = 0,
        $blockEndY = 0,
        $nbSelectedBoxes = 0;

    return {
        initialize: function()
        {
            this._options = new Hash();
            this.setOption('mode', MODE_SELECT);
        },
        setOption: function($key, $value)
        {
            this._options.set($key, $value);
        },
        getOption: function($key)
        {
            return this._options.get($key);
        },
        emptySelection: function()
        {
            $.each($('.map-generator img.selected'), function() {
                $(this).removeClass('selected');
            });
            $selectedBoxes = {};
            $nbSelectedBoxes = 0;
        },
        deselectBox: function($box)
        {
            $box.removeClass('selected');
            delete $selectedBoxes[$box.prop('id')];
            $nbSelectedBoxes -= 1;
        },
        selectBoxAndSwap: function($box)
        {
            if (this.swapImage($box)) {
                $nbSelectedBoxes += 1;
            }
        },
        selectBox: function($box)
        {
            if ($box.data('image') !== undefined) {
                $('#available-images option[data-image="' + $box.data('image') + '"]').prop('selected', true);
            }

            if ($box.data('bonus') !== undefined) {
                $('#available-bonus').val($box.data('bonus'));
            }

            $box.addClass('selected');
            $nbSelectedBoxes += 1;
        },
        swapImage: function($box)
        {
            var $selectedImage = $('#available-images option:selected');
            var $selectedBonus = $('#available-bonus option:selected');
            if (!$selectedImage.val()) {
                return false;
            }

            $box.addClass('selected');
            $box.attr('src', this.getOption('assetPath') + $selectedImage.data('image'));
            $box.data('image', $selectedImage.data('image'));
            $box.data('bonus', $selectedBonus.val());

            $selectedBoxes[$box.prop('id')] = {
                'image_id': $selectedImage.val(),
                'bonus_id': $selectedBonus.val()
            };

            $changesBoxes[$box.prop('id')] = {
                'image_id': $selectedImage.val(),
                'bonus_id': $selectedBonus.val()
            };

            return true;
        },
        mapGenerator: function()
        {
            var $this = this;

            setInterval(function () {
                $('#nb-selected-boxes').val($nbSelectedBoxes);
            }, 500);

            $document.on('click', '.map-generator img', function() {
                var $x = $(this).data('x');
                var $y = $(this).data('y');

                switch ($this.getOption('mode')) {
                    case MODE_SELECT:
                        $this.emptySelection();
                        $this.selectBox($(this));
                        break;
                    case MODE_BOX:
                        $this.emptySelection();
                        $this.selectBoxAndSwap($(this));
                        break;
                    case MODE_MULTI:
                        if ($(this).hasClass('selected')) {
                            $this.deselectBox($(this));
                        } else {
                            $this.selectBoxAndSwap($(this));
                        }
                        break;
                    case MODE_BLOCK:
                        if ($blockBeginX !== 0 && $blockEndX !== 0) {
                            $this.emptySelection();
                            $blockBeginX = 0;
                            $blockBeginY = 0;
                            $blockEndX = 0;
                            $blockEndY = 0;
                        }

                        if ($blockBeginX === 0 && $blockEndX === 0) {
                            $blockBeginX = $x;
                            $blockBeginY = $y;
                            $this.selectBoxAndSwap($(this));
                        } else if ($blockBeginX !== 0 && $blockEndX === 0) {
                            if ($y < $blockBeginY) {
                                $blockEndY = $blockBeginY;
                                $blockBeginY = $y;
                            } else {
                                $blockEndY = $y;
                            }

                            if($x < $blockBeginX) {
                                $blockEndX = $blockBeginX;
                                $blockBeginX = $x;
                            } else {
                                $blockEndX = $x;
                            }
                            $nbSelectedBoxes -= 1;

                            for (var $h = $blockBeginY; $h <= $blockEndY; $h++) {
                                for (var $w = $blockBeginX; $w <= $blockEndX; $w++) {
                                    $this.selectBoxAndSwap($('.map-generator img[data-x="' + $w + '"][data-y="' + $h + '"]'));
                                }
                            }
                        }

                        break;
                }
            });

            $('#available-modes').on('change keyup', function() {
                $this.setOption('mode', $(this).val());
            }).trigger('change');

            $('#available-bonus, #available-images').on('change keyup', function() {
                $.each($('.map-generator img.selected'), function() {
                    $this.swapImage($(this));
                });
            }).trigger('change');

            $('select[name="zoom"]').on('change keyup', function() {
                $('.map-generator img').width($(this).val());
                $('.map-generator img').height($(this).val());
            }).trigger('change');

            $('.map-generator img').hover(function() {
                $('#position-x').val($(this).data('x'));
                $('#position-y').val($(this).data('y'));
            }, function() {
                $('#position-x').val(0);
                $('#position-y').val(0);
            });

            $('#save-map-form').on('submit', function() {
                $('#serialized-data').val(JSON.stringify($changesBoxes));
            });

            $('#save-map-form button[name="clear-selected-boxes"]').on('click', function() {
                $this.emptySelection();
            });
        },
        displayImage: function($images, selector)
        {
            $images.on('change keyup', function() {
                if ($(this).data('asset-path')) {
                    $(selector).attr('src', $(this).data('asset-path') + '/' + $(this).val());
                } else {
                    $(selector).attr('src', $(this).val());
                }
            }).trigger('change');
        },
        news: function($images, $message)
        {
            this.displayImage($images, '#news-image-displayed');
            CKEDITOR.replace($message.prop('id'));
        },
        clickable: function()
        {
            $('.table-clickable > tbody td').on('click', function($event) {
                if($event.target.tagName !== 'IMG' && $event.target.tagName !== 'A') {
                    $(this).parent().find('a')[0].click();
                }
            });
        },
        object: function($images)
        {
            this.displayImage($images, '#object-image-displayed');
            Dba.collectionType();
        },
        building: function($images)
        {
            this.displayImage($images, '#building-image-displayed');
        },
        quest: function($images)
        {
            this.displayImage($images, '#quest-image-displayed');
            Dba.collectionType();
        },
        players: function($assetPath, $formChoice, $defaultImage)
        {
            Dba.appearance($assetPath, $formChoice, $defaultImage);
            $('input[type="password"]').val('');
            this.clickable();
        },
        confirmation: function()
        {
            $('[data-toggle=confirmation]').confirmation({
                rootSelector: '[data-toggle=confirmation]'
            });
        },
        imageGenerator: function()
        {
            $('.image-generator .image-part').on('change keyup', function() {
                var $value = $(this).val();
                var $elt = $($(this).data('target'));
                if ($value == '') {
                    $elt.addClass('hide');
                    $elt.prop('src', '');
                } else {
                    $elt.removeClass('hide');
                    $elt.prop('src', $(this).val());
                }
            }).trigger('change');
        }
    };
}(jQuery);

DbaAdmin.initialize();
