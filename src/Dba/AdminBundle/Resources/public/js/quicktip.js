//JQuery Quick Tip
//Initial Author: Owain Lewis
//Initial Author URL: www.Owainlewis.com
// Modified by Bastien Mourgues for DBAdventure http://www.jdr-dba.com

jQuery.fn.quicktip = function(options){

    var defaults = {
        speed: 700,
        xOffset: 25,
        yOffset: 25,
        id: 'tooltip',
        stackable: true,
        separator: '<br />'
    };

    var options = $.extend(defaults, options);

    var set_coords = function(e) {
        var $minx = $(document).scrollLeft();
        var $maxx = $minx + $(window).width();
        var $width = $('#'+defaults.id).outerWidth(true);
        var $x = e.pageX + defaults.xOffset;

        var $miny = $(document).scrollTop();
        var $maxy = $miny + $(window).height();
        var $height = $('#'+defaults.id).outerHeight(true);
        var $y = e.pageY + defaults.yOffset;


        if((($x + $width) > $maxx) && (($y + $height) > $maxy)) {
            $x = e.pageX - defaults.xOffset - $width;
            $y = e.pageY - defaults.yOffset - $height;
        } else {
            if(($x + $width) > $maxx) {
                $x = $maxx - $width;
            }
            if(($y + $height) > $maxy) {
                $y = $maxy - $height;
            }
        }

        if($x < $minx) {
            $x = $minx;
        }

        if($y < $miny) {
            $y = $miny;
        }

        //$('#coords').text('width:'+$width+' minx: '+$minx+' x:'+$x+' maxx: '+$maxx+' height:'+$height+' miny: '+$miny+' y:'+$y+' maxy: '+$maxy);

        $('#'+defaults.id)
            .css('top',  $y + 'px')
            .css('left', $x + 'px');

    }

    var check_size = function() {
        var $tooltip = $('#'+defaults.id);

        if($tooltip.is(':hidden')) {
            return;
        }
        $tooltip.width('auto');
        $tooltip.height('auto');

        var $maxwidth = $(window).width();
        var $width = $tooltip.outerWidth(true) + defaults.xOffset;

        var $maxheight = $(window).height();
        var $height = $tooltip.outerHeight(true) + defaults.yOffset;

        //$('#log').text($(this).data('tip')+' Mw:'+$maxwidth+' w:'+$width+' Mh:'+$maxheight+' h:'+$height);

        if($maxwidth < $width) {
            $width = $maxwidth - ($tooltip.outerWidth(true) - $tooltip.width())
            $width = $width > 0 ? $width : $maxwidth;
            $tooltip.width($width).css({overflow: 'hidden'});
        }

        if($maxheight < $height) {
            $height = $maxheight - ($tooltip.outerHeight(true) - $tooltip.height());
            $height = $height > 0 ? $height : $maxheight;
            $tooltip.height($height).css({overflow: 'hidden'});
        }
    }

    return this.each(function(){

        var $this = jQuery(this)
        var tipTitle;
        var $tipbox = jQuery('#'+defaults.id);

        //Pass the title to a variable
        if ($this.attr('title') != ''){
            $this.data('tip', $this.attr('title'));
        }
        //Remove title attribute
        $this.removeAttr('title');

        $(this).hover(function(e){
            //$(this).css('cursor', 'pointer')

            if(defaults.stackable) {
                tipTitle = $this.parents('.quicktip').andSelf().map( function() { return $(this).data('tip'); }).get().join(defaults.separator);
            } else {
                tipTitle = $(this).data('tip');
            }

            if(!tipTitle || tipTitle.lenght == 0) {
                return;
            }

            if($('#'+defaults.id).is(':visible')) {
                // Change Tip with new value
                $('#'+defaults.id).html(tipTitle);
            } else {
                // Create Tip
                $("body").append('<div id="'+defaults.id+'">' + tipTitle + '</div>');
                $('#'+defaults.id).fadeIn(options.speed);
            }

            check_size();
            set_coords(e);
            e.stopPropagation();

        }, function() {

            if(defaults.stackable) {
                tipTitle = $this.parents('.quicktip').map( function() { return $(this).data('tip'); }).get().join(defaults.separator);
            } else {
                
                tipTitle = $this.parents('.quicktip').data('tip');
            }
            if(tipTitle) {
                // Change Tip with ancestor's value
                $('#'+defaults.id).html(tipTitle);
                check_size();
                set_coords(e);


            } else {
                // Remove the tooltip from the DOM
                $('#'+defaults.id).remove();
            }

        });
        
        $(this).mousemove(function(e) {
            set_coords(e);
        });

    });
    
};
