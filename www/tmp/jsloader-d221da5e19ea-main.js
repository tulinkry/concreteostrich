

$(function(){
	$(".noshow").hide() 
	var show = $.fn.show
	$.fn.show = function ()
	{
		$(this).removeClass ( "hidden" );
		return show.apply(this,arguments);
	};
	var hide = $.fn.hide
	$.fn.hide = function ()
	{
		$(this).addClass ( "hidden" );
		return hide.apply(this,arguments);
	};

});


$.nette.ext('ajax-operations',
{
  init: function () 
  {
      spinner = $('<div></div>', { id: "ajax-spinner" });
      spinner.appendTo("body");
      $(function(){
        $('.date').datetimepicker();
        $('.datetimepicker').datetimepicker();
      });


      $(".event-show").click(function(e)
      {
        $(this).closest(".event").find(".noshow, .tohide").slideToggle();
        e.preventDefault();
      });

      // add after handler to correctly set up listeners
      $.nette.ext("snippets").after(function(el)
      {
        $(el).find(".event-show").click(function(e)
        {
          $(this).closest(".event").find(".noshow, .tohide").slideToggle();
          e.preventDefault();
        });      
      });


  },
  before: function ( xhr, settings )
  {/*
    if ( settings . nette )
    {
      $("#ajax-spinner").show().css({
              position: "absolute",
              left: settings.nette.e.pageX,
              top: settings.nette.e.pageY
          });
      $(document).mousemove( function(e)
      {
        $("#ajax-spinner").css({left:e.pageX, top:e.pageY});
      });      
    }
    else
      alert ( "no nette!" )

*/

  },
  complete: function ()
  {
      /*$("#ajax-spinner").hide().css({
            position: "fixed",
            left: "50%",
            top: "50%"
        });*/
      $(".noshow").css("display", "none"); 
      $('.date').datetimepicker();
     /* $('.timepicker').datetimepicker ({
        pickDate: false,
      });*/
      $('.datetimepicker').datetimepicker ();

      $.nette.load();

  }
});



/*
$(function(){
  $('.datetimepicker').datetimepicker ({
    useSeconds: true
  });
});*/
/*
$(function(){
  $('.timepicker').datetimepicker ({
    pickDate: false,
    useSeconds: true
  });
});

*/
(function($, undefined) {

if (typeof $ != 'function') {
  return console.error('nette.ajax.js: jQuery is missing, load it please');
}

if (!(window.history && history.pushState && window.history.replaceState && !navigator.userAgent.match(/((iPod|iPhone|iPad).+\bOS\s+[1-4]|WebApps\/.+CFNetwork)/))) return;


$.nette.ext('fileuploadchecker', 
{
  always: function ()
  {
    var that = this;

    that.select ( 'entityControl-entity=' );
    that.select ( 'entityControl-parent=' );
    //that.select ( 'entity=' );
    //that.select ( 'parent=' );
  },
  init: function ()
  {
    var that = this;

    that.select ( 'entityControl-entity=' );
    that.select ( 'entityControl-parent=' );

    var fileInputs = $( "input[type=file]" ).each ( function () {
    	//$(this).value ( "data.c" ); 
    });

    //that.select ( 'entity=' );
    //that.select ( 'parent=' );
  },
},
{});

});


// run the currently selected effect
function runEffect() 
{
 // $(".content").hide ()
  //$('body').removeClass('start-scrolling');
  //$('body').addClass('stop-scrolling');

  /*$(".menu").click ( function (e) {
    $(".content").stop();
    $(".content").animate ( { opacity: 1 }, 500 );
    $('body').removeClass('stop-scrolling');
    $('body').addClass('start-scrolling');

    $( ".bg-image .img" ).css( { opacity: 0.7 } );
    e.stopPropagation()

  });*/


	//$( ".menu" ).toggle( "slide", options, 500 );
	//$(".menu li a").hide();
	//$( ".menu a" ).each(function(){
	//	$(this).animate ( { width: "toggle", opacity: "toggle" },
	//					  { duration: 7000, specialEasing: { width: "easeOutBounce" } } );
	//});
};
// set effect from select menu value
function runEffect2() 
{
	// since we are trying to slide up from bottom
	// we disable scrolling down
	$('html, body').css({
	    'overflow': 'hidden',
	    'height': '100%'
	})
	// after successful animation we enable it back
	done = function() 
	{ 
		$(".loading").hide(); 
		$('html, body').css({'overflow': 'auto','height': 'auto'});
	};
	$( ".content" ).css({ position: "relative", top: $( window ).height() + "px", opacity: "0.5" });

	$( ".content" ).animate( { "top": "0", opacity: 1 }, 10000, done );

	//$( ".loading" ).show().find(".progress-bar").animate( { "width": "100%" }, 10000 );
	/*
	    jQuery(".block").hover(
        function(){
            jQuery(".slidemeup").slideDown();
        },
        function(){
        jQuery(".slidemeup").slideUp('fast');
    });
	*/
};
// set effect from select menu value
function runEffect3() 
{
	//$( ".menu" ).toggle( "slide", options, 500 );
	$( ".content" ).show().animate( { opacity: "1" }, 5000, function() { $(".loading").hide(); } );
	//$( ".loading" ).show().find(".progress-bar").animate( { "width": "100%" }, 10000 );
};
function runEffect4() 
{
	//$( ".menu" ).toggle( "slide", options, 500 );
	$( ".bg-image .img" ).css( { opacity: 0.7 } );
	$( ".bg-image .img" ).animate( { opacity: 0.1 }, 7000 + 4000 );
	//setTimeout ( function () { $(".bg-image").css({ position:"static" } );}, 7000 + 2000 );
	//$("body").css ( { "background-image": "url('./logo/left.png'), url('./logo/right.png')", opacity: "1" } );
	//$(".container-fluid").animate ( { opacity: 0.3, }, 7000 + 2000 );
};



function runEffect5()
{
  var delay = 7000
  var duration = 10000

	var out = function ( e )
	{
    if ( $('body') . hasClass ( "fading" ) )
      return;

    $('body').addClass ( "fading" )
    $('body').removeClass("showing");

		console . log ( "fading" );


		$(".content").stop();
		$(".content").delay(delay).animate ( { opacity: 0 },
    { duration: duration, 
      easing: 'easeInQuint',
      complete: function() 
      {
        $('body').removeClass('start-scrolling');
        $('body').addClass('stop-scrolling');
        //$('body').removeClass("fading");
      }
    } );

		$( ".bg-image .img" ).delay(delay).animate( 
      { opacity: 1 }, 
      duration
    );


		e.stopPropagation();
	}

	var inn = function (e)
	{
    if ( $('body') . hasClass ( "showing" ) )
      return;

    $('body').addClass ( "showing" )
    $('body').removeClass("fading");

		console . log ( "showing" );

		$(".content").stop();
		$(".content").animate ( { opacity: 1 },
    { duration: 500, 
      complete: function() 
      {
        $('body').removeClass('stop-scrolling');
        $('body').addClass('start-scrolling');
        //$('body').removeClass("showing");
      }
    } );


		$( ".bg-image .img" ).css( { opacity: 0.7 } );

		e.stopPropagation()
	};


	$(function()
	{
    $(".fadeeffect").each ( function () {
      $(this).hover ( inn, out );
    });
    $(".fadeeffect > .navbar").each ( function () {
      $(this).hover(function (e) { e.stopPropagation(); }, function (e) { e.stopPropagation(); })
    });

      //$(".datetimepicker").hover(function (e) { e.stopPropagation(); }, function (e) { e.stopPropagation(); })
      //$(".date").hover(function (e) { e.stopPropagation(); }, function (e) { e.stopPropagation(); })



		$("body").hover ( out, function(){ $("body").unbind("hover"); } )
	});

}



$(window).load(function () { $.nette.init(); });

$(function()
{
// Callback for successful uploads:
// done: function (e, data) {}, // .bind('fileuploaddone', func);


//$.nette.ext('snippets').updateSnippet($('#' + i), payload.snippets[i])

$("#fileupload").bind( "fileuploaddone", function ( e, data ) {
		payload = data . result
        if (payload.snippets) {
            for (var i in payload.snippets) {
                $.nette.ext('snippets').updateSnippet($('#' + i), payload.snippets[i]);
            }
        }
        $.nette.load();
} );

});


(function($){

    /**
     * Copyright 2012, Digital Fusion
     * Licensed under the MIT license.
     * http://teamdf.com/jquery-plugins/license/
     *
     * @author Sam Sehnert
     * @desc A small plugin that checks whether elements are within
     *       the user visible viewport of a web browser.
     *       only accounts for vertical position, not horizontal.
     */
    var $w = $(window);
    $.fn.visible = function(partial,hidden,direction){

        if (this.length < 1)
            return;

        var $t        = this.length > 1 ? this.eq(0) : this,
            t         = $t.get(0),
            vpWidth   = $w.width(),
            vpHeight  = $w.height(),
            direction = (direction) ? direction : 'both',
            clientSize = hidden === true ? t.offsetWidth * t.offsetHeight : true;

        if (typeof t.getBoundingClientRect === 'function'){

            // Use this native browser method, if available.
            var rec = t.getBoundingClientRect(),
                tViz = rec.top    >= 0 && rec.top    <  vpHeight,
                bViz = rec.bottom >  0 && rec.bottom <= vpHeight,
                lViz = rec.left   >= 0 && rec.left   <  vpWidth,
                rViz = rec.right  >  0 && rec.right  <= vpWidth,
                vVisible   = partial ? tViz || bViz : tViz && bViz,
                hVisible   = partial ? lViz || rViz : lViz && rViz;

            if(direction === 'both')
                return clientSize && vVisible && hVisible;
            else if(direction === 'vertical')
                return clientSize && vVisible;
            else if(direction === 'horizontal')
                return clientSize && hVisible;
        } else {

            var viewTop         = $w.scrollTop(),
                viewBottom      = viewTop + vpHeight,
                viewLeft        = $w.scrollLeft(),
                viewRight       = viewLeft + vpWidth,
                offset          = $t.offset(),
                _top            = offset.top,
                _bottom         = _top + $t.height(),
                _left           = offset.left,
                _right          = _left + $t.width(),
                compareTop      = partial === true ? _bottom : _top,
                compareBottom   = partial === true ? _top : _bottom,
                compareLeft     = partial === true ? _right : _left,
                compareRight    = partial === true ? _left : _right;

            if(direction === 'both')
                return !!clientSize && ((compareBottom <= viewBottom) && (compareTop >= viewTop)) && ((compareRight <= viewRight) && (compareLeft >= viewLeft));
            else if(direction === 'vertical')
                return !!clientSize && ((compareBottom <= viewBottom) && (compareTop >= viewTop));
            else if(direction === 'horizontal')
                return !!clientSize && ((compareRight <= viewRight) && (compareLeft >= viewLeft));
        }
    };

})(jQuery);



(function($, undefined) {

$.nette.ext({
  before: function (xhr, settings) {
		var question = settings.nette.el.data('confirm');
		if (question) {
			return confirm(question);
		}
	}
});

})(jQuery);

(function($, undefined) {

$(window).scroll(function()
{
	if ( $("#next").visible() )
	{
		$("#next").click ( function () { $(this).button("reset"); });
		$("#next").trigger("click");
		$("#next").button("loading");
	}
});

})(jQuery);



$.nette.ext('unique', null);