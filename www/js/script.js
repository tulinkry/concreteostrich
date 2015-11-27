(function($, undefined) {

if (typeof $ != 'function') {
  return console.error('nette.ajax.js: jQuery is missing, load it please');
}

if (!(window.history && history.pushState && window.history.replaceState && !navigator.userAgent.match(/((iPod|iPhone|iPad).+\bOS\s+[1-4]|WebApps\/.+CFNetwork)/))) return;


$.nette.ext('dialogRemove', 
{
  success: function ()
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
    //that.select ( 'entity=' );
    //that.select ( 'parent=' );
  },
}, 
{
  select: function ( txt )
  {
    var url = window.location.toString();
    var pos1 = url.indexOf(txt);
    var pos2 = url.indexOf('&', pos1 );
    if (pos2 === -1)
      pos2 = url.length;
    if (pos1 !== -1)
    {
      window.history.replaceState({}, null, this.remove(url, pos1, pos2));
    }
  },
  remove: function (url, pos1, pos2) 
  {
    if ((url.substr(pos1 - 1, 1) === '?'))
      url = url.substr(0, pos2) + '?' + url.substr(pos2 + 1);
    url = url.substr(0, pos1) + url.substr(pos2);
    if ((url.substr(pos1 - 1, 1) === '?') || (url.substr(pos1 - 1, 1) === '&')) 
    {
      url = url.substr(0, pos1 - 1) + url.substr(pos1);
    }
    return url;
  }
});
/*
$.nette.ext('loading-indicator', 
{
  before: function () 
  {
    $('*').css({'cursor': 'progress'});
  },
  complete: function () 
  {
    $('*').css({'cursor': 'auto'});
    $('a').css({'cursor': 'pointer'});
    //Fix for firefox
    $('a *').css({'cursor': 'pointer'});
  }
});
*/
$.nette.ext('ajax-operations',
{
  init: function () 
  {
      spinner = $('<div></div>', { id: "ajax-spinner" });
      spinner.appendTo("body");
  },
  before: function ( xhr, settings )
  {
    $("#loading").show().button('loading');
    if ( settings . nette )
      $("#ajax-spinner").show().css({
              position: "absolute",
              left: settings.nette.e.pageX,
              top: settings.nette.e.pageY
          });
    else
      $("#ajax-spinner").show().css({
              position: "absolute",
              left: "50%",
              top: "50%"
          });
  },
  complete: function ()
  {
      $("#loading").button('reset').hide ();
      $("#ajax-spinner").hide().css({
            position: "fixed",
            left: "50%",
            top: "50%"
        });
      $.dependentselectbox.hideSubmits();
      $('.selectpicker').selectpicker();
      $(".noshow").css("display", "none"); 
      $('.date').datetimepicker({
        useSeconds: true
      });
      $('.timepicker').datetimepicker ({
        pickDate: false,
        useSeconds: true
      });
      $('.datetimepicker').datetimepicker ({
        useSeconds: true
      });
      $.nette.load();
  }
});

/*
$.nette.ext('spinner', {
    init: function () {
        spinner = $('<div></div>', { id: "ajax-spinner" });
        spinner.appendTo("body");
    },
    before: function (settings, ui, e) {
        $("#ajax-spinner").show().css({
            left: e.pageX,
            top: e.pageY
        });
    },
    complete: function () {
        $("#ajax-spinner").hide().css({
              position: "fixed",
              left: "50%",
              top: "50%"
          });
    }
});

*/

})(jQuery);








(function($, undefined) {




/**
 * Nette snippet watchdog extension.
 * 
 * This extension allows to call a function when certain 
 * snippet comes be redrawn
 */
$.nette.ext('watchdog', {
    
        /**
         * This method is called after extension addition:
         */
        init: function() {
            // Register "public" members:
            var that = this;
            $.nette.watchSnippet = function(name, callback) {
                that.addWatch(name, callback);
            }
            $.nette.updateSnippets = function(payload) {
                that.updateSnippets(payload);
            }
        },
        
        /**
         * When ajax request returns some snippet, check whether it is watched.
         */
        success: function(payload) {
            if (payload.snippets) {
                var that = this;
                // Ensure the callbacks will be executed after the snippets have been redrawn:
                setTimeout(function() {
                    that.fireCallbacks(payload);
                }, 0);                
            }
        }
        
}, {
    
    snippets: [],
    
    /**
     * Adds a watch for requested snippet.
     * @param {string} name Snippet name
     * @param {function} callback Callback function
     */
    addWatch: function(name, callback) {
        name = 'snippet--' + name;
        if (this.snippets[name] == undefined) {
            this.snippets[name] = [];
        }
        this.snippets[name].push(callback);
    },
    
    /**
     * Fires all associated callbacks.
     */
    fireCallbacks: function(payload) {
        for (var s in payload.snippets) {
            
            // If the snippet is on the list:
            if (this.snippets[s] != undefined) {
                for (var f in this.snippets[s]) {
                    var fnc = this.snippets[s][f];
                    fnc(payload);
                }
            }
            
        }
    },
    
    /**
     * Updates snippets using snippets extension and
     * given payload.
     */
    updateSnippets: function(payload) {
        if (payload.snippets) {
            for (var i in payload.snippets) {
                $.nette.ext('snippets').updateSnippet($('#' + i), payload.snippets[i]);
            }
        }
    }
        
    
});



/**
 * Extra post and get extension
 *
 * This extension adds extra methods replacing $.post by $.nette.post and 
 * $.get by $.nette.get which (beside default capabilities) also call event 
 * handlers of other Nette extensions (automatic snippet dispatching after
 * ajax call etc).
 */
$.nette.ext('extrapost', {
    
        /**
         * This method is called after extension addition:
         */
        init: function() {
            
            // $.post complementary method:
            $.nette.post = function(url, data, success, dataType) {
                $.fn.netteAjax(null, {
                    url: url,
                    data: data,
                    success: success,
                    dataType: dataType,
                    type: 'post'
                });
            }
            
            // $.get complementary method:
            $.nette.get = function(url, data, success, dataType) {
                $.fn.netteAjax(null, {
                    url: url,
                    data: data,
                    success: success,
                    dataType: dataType,
                    type: 'get'
                });
            }
            
        }
});

/**
 * Nette ajax dialogs extension:
 */
$.nette.ext('dialogs', {
    
        /**
         * This method is called after extension addition:
         */
        init: function() {
            // Register "public" members:
            var that = this;
            $.nette.showDialog = function(url, snippet, args, options) {
                that.showDialog(url, snippet, args, options);
            }
        },
        
        load: function (){
            //$.nette.showDialog({link Update $lm -> getId ()}, 'entityForm', {}, { width: 600 }); return false;
            $(".window").attr("onclick","").unbind("click")
            $(".window").click ( function ( e ) {
              $.nette.showDialog ( $(this) . attr ( "href" ), 'entityForm', {}, { width: 600 } );
              return false;
            })
        },

        /**
         * When ajax request returns check whether there is a dialog command:
         */
        success: function(payload) {

            // Close selected dialogs:
            if (payload.dialogs) {
                for (var i in payload.dialogs) {
                    // Close all dialogs:
                    if (i == 'all') {
                        $('.nette-dialog') . modal('hide');
                         this . dialog = []
                        break;
                    }
                    
                    // Close only one dialog:
                    $('.nette-dialog-' + i) . modal('hide');
                    this . dialog . splice ( i, 1 )

                }
            }
        }
        
}, {
    
    dialog: [],
    datePickerClass : 'datepicker',
    
    createDialog: function ( name, options )
    {

      options = options || {};
      options = $.extend(true, {
        keyboard: true,
        show: true
      }, options);
      
      if ( ! this . dialog [ name ] )
      {
        var h = "<div class=\"modal-dialog\">" +
            "<div class=\"modal-content\">" +
              "<div class=\"modal-header\">" +
                "<button type=\"button\" class=\"close\" data-dismiss=\"modal\"><span aria-hidden=\"true\">&times;</span><span class=\"sr-only\">Close</span></button>" +
                "<h4 class=\"modal-title\">Modal title</h4>" +
              "</div>" +
              "<div class=\"modal-body\">" +
              "</div>" +
              "<div class=\"modal-footer\">" +
                "<button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button>" +
              "</div>" +
            "</div>" +
          "</div>"

        var modal_window = $('<div>').addClass ( 'modal' ) 
                              . addClass ( 'fade' ) 
                              . addClass ( 'nette-dialog' ) 
                              . addClass ( 'nette-dialog-' + name )
        modal_window . html ( h )
        b = modal_window.modal(options)
        this.dialog [ name ] = $(b).appendTo('body');

        $('.nette-dialog').on('hidden.bs.modal', function (e) {
            $(this).remove()
        })

      }
    },

    /**
     * Downloads a form snippet from given URL 
     * and displays it in jQuery UI dialog.
     * @param {string} url URL to retrieve the form from
     * @param {string} snippet Snippet to be loaded
     * @param {object} args Arguments to pass along the request
     * @param {object} options Options to pass to the modal dialog
     */
    showDialog: function(url, snippet, args, options) {
        
        // Default dialog options:
        var that = this;

        $.nette.ext ( "history", false )

        this.createDialog ( snippet, options );

        var h = "<h1>Loading...</h1><div class=\"progress progress-striped active\">" +
            "<div class=\"progress-bar\" style=\"width: 100%;\"></div>" +
            "</div>"


        snippet_name = 'snippet--' + snippet;
        var content = $('<div>').attr("id", snippet_name).html(h)
        this.dialog [ snippet ].find(".modal-body").html(content)
        
        this.dialog [ snippet ].modal('show');

        // Dialog has to be updated after each snippet refresh:
        $.nette.watchSnippet(snippet, function() {
            that.updateDialog(that.dialog);
        });


        // Download form snippet and place it to the dialog:
        $.nette.post(url, args || {}, function(payload) {
            // If the snippet was received:
  
             if (payload.snippets && payload.snippets[snippet]) {
                $.nette.updateSnippets(payload);
                that.updateDialog(that.dialog);
            } else {
                that.showError('Nepodařilo se stáhnout formulář');
            }
        });
   
    },
    
    /**
     * Displays an error message inside the dialog.
     * @param {string} message Message to display
     */
    showError: function(message) {
        if (this.dialog) {
            this.dialog;//text(message)
        } else {
            alert(message);
        }
    },
    
    /**
     * Updates dialog to look cool and working.
     * @param {jQuery} dialog Dialog div element
     */
    updateDialog: function(dialog) {

        // Remove first h1 and make a title from it:
       /* if (dialog.find('h1')) {
            var heading = dialog.find('h1').first();
            dialog.dialog('option', 'title', heading.text());
            heading.remove();                    
        }
        
        // Style dialog inputs:
        dialog.find('input[type=text], input[type=date], input[type=time]').addClass('text ui-widget-content ui-corner-all');
        
        // Center dialog:
        dialog.dialog({position: "center"});
        
        // Initialize the form:
        dialog.find('form').addClass('ajax');
        var fst = dialog.find('input[type=text]').first();
        
        // If the first input is a datepicker, do not focus it because it would pop up immediatelly.
        if (fst.attr('class').toLowerCase().indexOf(this.datePickerClass) == -1 || this.datePickerClass == null) {
            fst.focus();
        }*/
        //$.nette.load();        
        
    }
    
});


})(jQuery);


function leadingZeros ( d, max, amount )
{
  var mm = d . getMinutes ();


  if ( d . getMinutes () > max )
  {
    if ( amount < 0 )
    {
      mm = ( mm - ( mm - max * parseInt ( 60 / max ) ) - 1 ) % max + 1;
    }
    else
    {
      mm = ( mm ) % ( max + 1 );
    }
    d . setMinutes ( mm );
    d . setHours ( 0 );
  }
  if ( d . getMinutes () < 10 )
    mm = '0' + mm;

  var ss = d . getSeconds ();
  if ( d . getMinutes () > max )
    ss = "0";
  if ( d . getSeconds () < 10 )
    ss = '0' + ss;

  return mm + ":" + ss;
}

function date ( strmmss, amount )
{
  var d = new Date( new Date ( '2000/01/01 00:' + strmmss ) . getTime () + amount );
  d . setYear ( 2000 );
  d . setMonth ( 1 );
  d . setDate ( 1 );
  d . setHours ( 0 );
  return d;
}

function addTime ( el, amount, className )
{
  var d = date($(el).siblings(className).val(), amount);

  var max = parseInt ( $('#max_time').text() );
  //alert ( max );
  var time = leadingZeros ( d, max, amount );

  //alert ( time );

  $(el).siblings(className).val( time );
  $(el).siblings(".val").text( time );

  if ( className == ".gk" )
  {
    var d1 = date ( $(el).closest('tr').find('input.time').val(), 0 );

    if ( d . getTime () > d1 . getTime () )
    {
      $(el).closest('tr').find('input.time').val(  leadingZeros ( d, max )  );
      $(el).closest('tr').find('input.time').siblings('.val').text(  leadingZeros ( d, max )  );
    }
  }
 
   if ( className == ".time" )
  {
    var d1 = date ( $(el).closest('tr').find('input.gk').val(), 0 );
    if ( d . getTime () < d1 . getTime () )
    {
      $(el).closest('tr').find('input.gk').val(  leadingZeros ( d, max )  );
      $(el).closest('tr').find('input.gk').siblings('.val').text(  leadingZeros ( d, max )  );
    }
  }

  return false; 
}

function addInt ( el, amount )
{
  var val = parseInt($(el).siblings('.int').val()) + amount;
  if ( val < 0 )
    val = 0;
  $(el).siblings('.int').val(val);
  $(el).siblings(".val").text( val );

  return false;
}


//$(function() { $( "#ticket" ).accordion({ heightStyle: "content" } ); } ); // akordeon

$(function () {
  $( ".statsForm table tr input.time" ) . each ( function ( index ) {
    $( "<a href=\"#\" onclick=\"return addTime( $(this), 1*60000, '.time' );\">+</a>" ) . insertBefore ( $(this) );
    $( "<a href=\"#\" onclick=\"return addTime( $(this), -1*60000, '.time' );\">-</a>" ) . insertAfter ( $(this) );
    var el = $( "<span class='val'></span>" ) . insertBefore ( $(this) );
    $(el) . text ( $(this) . val () );
    $(this) . css ( "display", "none" ); } );
});
$(function () {
  $( ".statsForm table tr input.int" ) . each ( function ( index ) { 
    $( "<a href=\"#\" onclick=\"return addInt( $(this), 1 );\">+</a>" ) . insertBefore ( $(this) );
    $( "<a href=\"#\" onclick=\"return addInt( $(this), -1 );\">-</a>" ) . insertAfter ( $(this) );
    var el = $( "<span class='val'></span>" ) . insertBefore ( $(this) );
    $(el) . text ( $(this) . val () );
    $(this) . css ( "display", "none" ); } );
});

$(function () {
  $( ".statsForm table tr input.gk" ) . each ( function ( index ) { //$(this).closest('tr').find('.time').val()
    $( "<a href=\"#\" onclick=\"return addTime( $(this), 1*60000, '.gk' );\">+</a>" ) . insertBefore ( $(this) );
    $( "<a href=\"#\" onclick=\"return addTime( $(this), -1*60000, '.gk' );\">-</a>" ) . insertAfter ( $(this) );
    var el = $( "<span class='val'></span>" ) . insertBefore ( $(this) );
    $(el) . text ( $(this) . val () );
    $(this) . css ( "display", "none" ); } );
});

//$(this).closest('.int').val ( parseInt ( $(this).closest('.int').val() ) + 1 );
//$(this).prev('.time').val(new Date(new Date($(this).prev('.time').val()).getTime()+1*60000).toString('mm:ss'))


 $(function(){
  $('.selectpicker').selectpicker();
});


$(function(){
  $("#selectControl, .noshow") . css( "display", "none" );
});

$(function(){
  $(".btn-bet") . click ( function () { 
    $(this) . parent () . parent () . find ( ".btn-warning" ) . removeClass ( "btn-warning" ) . addClass ( "btn-primary" );
    $(this) . addClass ( "btn-warning" );
  });
});

function removeItem ( el )
{
  var al = $(el) . parent () . parent () . attr ( "class" );
  var data = al . split ( " " );
  for ( var i = 0; i < data . length; i ++ )
  {
    if ( /bet-delete/ . test ( data [ i ] ) )
      $( "." + data [ i ]) . removeClass ( "btn-warning" ) . addClass ( "btn-primary" );
  }
}


$(function(){
  $(".js-off").show();
});



$(function(){
  $('.datetimepicker').datetimepicker ({
    useSeconds: true
  });
});

$(function(){
  $('.timepicker').datetimepicker ({
    pickDate: false,
    useSeconds: true
  });
});

/*
$(function(){
$(document).ajaxStop(function(){
  $("#loading").button('reset').hide ();
  $("#ajax-spinner").hide().css({
  position: "fixed",
  left: "50%",
  top: "50%"
  });
});
$(document).ajaxStart(function(){
  $("#loading").show().button('loading');
  $("#ajax-spinner").show().css({
  position: "absolute",
  left: "50%",
  top: "50%"
  });
})
$(document).ajaxStop(function(){
  $.dependentselectbox.hideSubmits();
  $('.selectpicker').selectpicker();
  $(".noshow").css("display", "none");
  })

});
*/

$(window).load(function () { $.nette.init(); });

