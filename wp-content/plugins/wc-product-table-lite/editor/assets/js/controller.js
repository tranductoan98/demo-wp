if( typeof wcpt === "undefined" ){
  var wcpt = {
    controller: {},
    data: {},
  };
}

jQuery(function($){

  var controller = wcpt.controller,
      data = wcpt.data;

  /* handler functions */

  // update table title/name
  controller.update_table_title = function(){
    var $this = $(this),
        new_title = $this.val();
    $('.wcpt-editor-save-table [name="title"]').val(new_title);
  }

  // switch editor tabs
  controller.switch_editor_tabs = function(){
    var $this = $(this),
    tab = $this.attr('data-wcpt-tab'),
    $labels = $this.siblings('.wcpt-tab-label'),
    $contents = $this.siblings('.wcpt-tab-content'),
    $target_content = $contents.filter('[data-wcpt-tab='+ tab +']'),
    active_class = 'active';

    $labels.removeClass(active_class);
    $this.addClass(active_class);

    $contents.removeClass(active_class);
    $target_content.addClass(active_class);

    window.location.hash = tab;
  }

  // toggle sub categories
  controller.toggle_sub_categories = function(){
    var $this   = $(this);
    $this.parent().toggleClass('wcpt-show-sub-categories');
  }

  // auto select on click
  controller.auto_select_on_click = function(){
    var node = this;
    if (document.body.createTextRange) {
        const range = document.body.createTextRange();
        range.moveToElementText(node);
        range.select();
    } else if (window.getSelection) {
        const selection = window.getSelection();
        const range = document.createRange();
        range.selectNodeContents(node);
        selection.removeAllRanges();
        selection.addRange(range);
    }
  }   

  // checklist
  //-- saved
  $('body').on('wcpt_save', function(){
    $('[data-wcpt-ck="saved"]').addClass('wcpt-done');
  })
  //-- query_selected
  $('body').on('change', '.wcpt-editor > [wcpt-model-key="query"]', function(){
    if(
      ( typeof wcpt.data.query.category !== 'undefined' && wcpt.data.query.category.length ) ||
      wcpt.data.query.ids ||
      wcpt.data.query.skus
    ){
      $('[data-wcpt-ck="query_selected"]').addClass('wcpt-done');
    }else{
      $('[data-wcpt-ck="query_selected"]').removeClass('wcpt-done');
    }
  })
  //-- column_element_created
  $('body').on('change', '.wcpt-editor > [wcpt-model-key="columns"]', function(){
    var column_element_created = false;
    if( wcpt.data.columns.laptop.length ){
      $.each(wcpt.data.columns.laptop, function(i_col, col){
        // heading content element
        if( col.heading.content[0].elements.length ){
          column_element_created = true;
          return false;
        }
        // cell template[row] element
        $.each(col.cell.template, function( i_row, row ){
          if( row.elements.length ){
            column_element_created = true;
            return false;
          }
        })
      })
    }

    if(  column_element_created ){
      $('[data-wcpt-ck="column_element_created"]').addClass('wcpt-done');
    }else{
      $('[data-wcpt-ck="column_element_created"]').removeClass('wcpt-done');
    }
  })

  // save JSON data to server
  controller.save_data = function( e ){

    e.preventDefault();

    // ensure change is triggered on any focused input
    var $focused_input = $('input:focus, textarea:focus');
    if( $focused_input.length && $focused_input.attr('wcpt-model-key') ){
      $focused_input.trigger('change');
    }

    $('body').trigger('wcpt_save');

    data.version = window.wcpt_version;

    var $this = $(this), // form
        post_id = $this.find("input[name='post_id']").val(),
        title = $this.find("input[name='title']").val(),
        nonce = $this.find("input[name='nonce']").val(),
        json_data = JSON.stringify( data ),
        $button = $this.find( ".wcpt-save" ),
        action = $this.attr('action');

    if( ! $this.hasClass("wcpt-saving") ){
      $.ajax( {
        type: "POST",
        url: ajaxurl,

        beforeSend: function(){
          $this.addClass("wcpt-saving");
          $button.addClass("disabled");
        },

        data: {
          action: action,
          wcpt_post_id: post_id,
          wcpt_title: title,
          wcpt_nonce: nonce,
          wcpt_data: json_data,
        },

        success: function(data){
          $this.removeClass("wcpt-saving");
          $button.removeClass("disabled");

          // success
          if( typeof data == 'string' && -1 !== data.indexOf( "WCPT success:" ) ){
            console.log( data );

          // failure
          }else{
            alert( data );

          }

        }
      } );
    }

  }

  /* dynamic input wrapper */

  controller.open_dynamic_input_wrapper = function () {
    var $input = $(this);

    if(
      $input.parent().hasClass('wcpt-diw') ||
      $input.hasClass('wcpt-diw--disabled')
    ){
      return;
    }

    var prev_style = $input.attr('style'),
      style = { 'width': $input.outerWidth() };

    $.each( ['float', 'margin', 'top', 'right', 'bottom', 'left'], function( key, prop ){
      style[prop] = $input.css(prop);
    } )

    if( $input.css('position') == 'absolute' ){
      style['position'] = 'absolute';
    }

    var $wrap = $('<div class="wcpt-diw">')
    $wrap.css(style);
    $input.wrap($wrap);

    $input.focus();

    $('body').on('blur mousedown keydown', controller.close_dynamic_input_wrapper);

    $input.after('<div class="wcpt-diw-tray">');
    var $tray = $input.next('.wcpt-diw-tray');

    if (
      $input.attr('wcpt-model-key').indexOf('color') !== -1 ||
      $input.attr('wcpt-model-key') == 'background' ||
      $input.attr('wcpt-model-key') == 'fill'
    ){
      $tray.append('<input type="color">');
      $tray.css({'height': 0});
      var $color = $tray.find('input[type="color"]');

      $color
        .spectrum({
          color: $input.val(),
          flat: true,
          allowEmpty: true,
          showAlpha: true,
          preferredFormat: 'rgba',
          clickoutFiresChange: true,
          showInput: false,
          showButtons: false,
          move: function(color) {
            $input.val(color.toRgbString()).change();
          }
        })
    }

  }

  controller.close_dynamic_input_wrapper = function(e){
    var $origin = $(e.target),
      $wrap = $origin.closest('.wcpt-diw');

    if (!$wrap.length) {
      $('.wcpt-diw').each(function() {
        var $this = $(this),
          $input = $this.children('input');

        $this.replaceWith($input);
        $('body').off('blur mousedown keydown', controller.close_dynamic_input_wrapper);

        $input.change();
      })
    }
  }

  /* increase/decrease number with arrow keys */
  $('body').on('keydown', 'input[wcpt-model-key]', function(e){
    if( ! e.key || -1 === $.inArray( e.key, ['ArrowUp', 'ArrowDown'] ) ){
      return;
    }

    if( -1 === $.inArray( $(this).attr('wcpt-model-key'), [
      'font-size', // permitted
      'custom_zoom_scale',
      'line-height',
      'letter-spacing',
      'stroke-width',
      'top', 'left', 'right', 'bottom',
      'width', 'max-width', 'min-width',
      'height', 'max-height', 'min-height',
      'border-radius', 'border-width', 'border-left-width', 'border-right-width', 'border-top-width', 'border-bottom-width',
      'divider-border-width',
      'padding', 'padding-left', 'padding-right', 'padding-top', 'padding-bottom',
      'section-padding', 'section-padding-left', 'section-padding-right', 'section-padding-top', 'section-padding-bottom',
      'margin', 'margin-left', 'margin-right', 'margin-top', 'margin-bottom',
      'gap', 'row_gap'
    ] ) ){
     return;
    }

    if( ! e.target.value ){
      e.target.value = '0px';
    }

    var suffix = e.target.value.slice(-2),
        val = e.target.value;

    if( val.length > 2 && isNaN(suffix) ){
      val = val.substring(0, val.length - 2);
    }else{
      suffix = '';
    }

    var is_float = ! ( ( parseInt(val) + '' ).length === ( val + '' ).length );

    if( e.key== 'ArrowUp' ){
      e.target.value = ( ( ( val * 10 ) + ( is_float ? 1 : 10 ) ) / 10 );
    }else if( e.key== 'ArrowDown' ){
      e.target.value = ( ( ( val * 10 ) - ( is_float ? 1 : 10 ) ) / 10 );
    }

    // convert '2' back to float: '2.0'
    val = e.target.value;
    if( is_float ){
      if( ( ( parseInt(val) + '' ).length === ( val + '' ).length ) ){ // float turned to int, let's fix this
        e.target.value = e.target.value + '.0';
      }
    }

    e.target.value += suffix;

  })

  /* attach event handlers */

  // switch editor tabs
  $('body').on('click', '.wcpt-tab-label', controller.switch_editor_tabs);

  // title
  $('body').on('blur', '.wcpt-table-title', controller.update_table_title);

  // toggle sub categories
  $('body').on('click', '.wcpt-toggle-sub-categories', controller.toggle_sub_categories);

  // dynamic input wrapper
  $('body').on('focus', 'input[type="text"][wcpt-model-key], input[type="number"][wcpt-model-key]', controller.open_dynamic_input_wrapper);

  // auto select
  $('body').on('click', '.wcpt-auto-select-on-click', controller.auto_select_on_click);

  // data hook up
  dominator_ui.init( $('.wcpt-editor, .wcpt-settings'), data );

  // other toggle
  $('body').on('click', '.wcpt-toggle-label', function(){
    var $this = $(this),
        $container = $this.closest('.wcpt-toggle-options'),
        toggle = $container.attr('wcpt-model-key');

    $container.toggleClass('wcpt-open');
    if( toggle && $container.parent().hasClass('wcpt-settings') ){
      window.location.hash = toggle;
    }
  });

  // toggle option rows
  $('body').on('click', '.wcpt-editor-row-handle', function(e){
    var $target = $(e.target),
        $row = $target.closest('.wcpt-editor-row');
    if(
      ! $target.closest('.wcpt-editor-row-no-toggle').length &&
      $target.closest('.wcpt-editor-row-handle-data, .wcpt-editor-row-toggle').length
    ){
      $row.toggleClass('wcpt-editor-row-toggle-opened');
    }
  });

  // toggle
  $('body')
    .on('click', '.wcpt-toggle > .wcpt-toggle-trigger', function(e){
      var $toggle = $(this).closest('.wcpt-toggle');
      $toggle.toggleClass('wcpt-toggle-on wcpt-toggle-off');
      $('body').off('click.wcpt_toggle_blur');
      if( $toggle.hasClass('wcpt-toggle-on') ){
        // blurrable toggle is opened
        // close on blur
        // add this to array
        $('body').on('click.wcpt_toggle_blur', function(e){
          if( ! $(e.target).closest($toggle).length ){
            $toggle.children('.wcpt-toggle-trigger').click();
            $('body').off('click.wcpt_toggle_blur');
          }
        })
      }
    })
    .on('click', '.wcpt-toggle-x', function(e){
      $(this).closest('.wcpt-toggle').toggleClass('wcpt-toggle-on wcpt-toggle-off');
    })

  // resume editor tab
  if( window.location.hash ){
    $('[data-wcpt-tab="'+ window.location.hash.substr(1) +'"].wcpt-tab-label').trigger('click');
    $('.wcpt-settings > [wcpt-model-key="'+ window.location.hash.substr(1) +'"] > .wcpt-toggle-label').trigger('click');
  }     

  // submit
  // -- button click
  $('body').on('submit', '.wcpt-save-data', controller.save_data);
  // -- keyboard: Ctrl/Cmd + s
  $(window).bind('keydown', function( e ) {
    if ( ( e.ctrlKey || e.metaKey ) && String.fromCharCode( e.which ).toLowerCase( ) === 's' ){
       e.preventDefault( );
      $('.wcpt-save-data').submit( );
    }
  });
  // -- submit save from text
  // $('body').on('click', '.wcpt-save-keys', function(){
  //   $('.wcpt-save-data').submit();
  // });

  // floating save button 
  if( $('.wcpt-editor, .wcpt-settings').length ){
    $(window).on('scroll', wcpt_maybe_floating_save_button);
    $('body').on('click', '.wcpt-toggle-options', wcpt_maybe_floating_save_button);
    wcpt_maybe_floating_save_button();
  }

  function wcpt_maybe_floating_save_button (){
    var $save_clear = $('.wcpt-editor-save-table-clear');
    if( ( window.scrollY + window.innerHeight ) < $save_clear.offset().top ){
      $('.wcpt-editor-save-table').addClass('wcpt-editor-save-table--floating');
    }else{
      $('.wcpt-editor-save-table').removeClass('wcpt-editor-save-table--floating');
    }
  }

  // column link scroll
  $('body').on('click', '.wcpt-column-links a', function(e){
    e.preventDefault();

    var $this = $(this),
        $device_columns = $this.closest('.wcpt-editor-columns-container'),
        column_index = $this.attr('data-wcpt-index'),
        $target = $device_columns.children('[wcpt-model-key-index="'+ column_index +'"]'),
        $heading = $('> .wcpt-editor-light-heading', $device_columns),
        offset = $heading.outerHeight() + 50;

    if( column_index == 'add' ){
      var $target = $device_columns.find('.wcpt-button[wcpt-add-row-template]');
      offset += 40;
      $target.click();
    }

    $([document.documentElement, document.body]).animate({
        scrollTop: $target.offset().top - offset
    }, 300, 'linear');
  })

  // column name input toggle
  //-- open
  $('body').on('click', '.wcpt-column-index', function(){
    var $this = $(this);
    $this
      .addClass('wcpt-column-index--input-on')
      .find('input').focus()
      .end().find('.wcpt-diw input').focus(); // diw workaround
  })
  //-- close
  $('body').on('click', '.wcpt-close-column-name-input', function(e){
    var $this = $(this),
        $input = $this.prev('input');

    $input.val('').change();
    e.stopPropagation();
  })  

  // columns toggle
  //-- central buttons
  $('body').on('click', '.wcpt-device-columns-toggle', function(e){
    var $this = $(this),
        $device_columns = $this.closest('.wcpt-editor-columns-container'),
        $columns = $device_columns.find('.wcpt-column-settings'),
        $heading = $('> .wcpt-editor-light-heading', $device_columns),
        offset = 50;

    if( $(e.target).closest('.wcpt-device-columns-toggle__expand').length ){
      $columns.addClass('wcpt-toggle-column-expand');

    }else if( $(e.target).closest('.wcpt-device-columns-toggle__contract').length ){
      $columns.removeClass('wcpt-toggle-column-expand');

    }

    $([document.documentElement, document.body]).animate({
      scrollTop: $device_columns.offset().top - offset
    }, 300, 'linear');    

    e.preventDefault();
  })
  //-- column buttons
  $('body').on('click', '.wcpt-editor-row-expand, .wcpt-editor-row-contract', function(e){
    var $this = $(this),
        $column = $this.closest('.wcpt-column-settings');

    if( $this.hasClass('wcpt-editor-row-expand') ){
      $column.addClass('wcpt-toggle-column-expand');

    }else{
      $column.removeClass('wcpt-toggle-column-expand');

    }

    e.preventDefault();
  })
  //-- column body
  $('body').on('click', '.wcpt-column-toggle-capture', function(e){
    var $column = $(this).closest('.wcpt-column-settings');
    $column.toggleClass('wcpt-toggle-column-expand'); 
  })

  // dev's little helper
  window.wcpt_duplicate_laptop_to_phone = function(){
    wcpt_duplicate_device( 'laptop', 'phone' );
  }

  window.wcpt_duplicate_laptop_to_tablet = function(){
    wcpt_duplicate_device( 'laptop', 'tablet' );
  }  

  window.wcpt_duplicate_device = function( source, destination ){
    if( ! source ){
      source = 'laptop';
    }

    if( ! destination ){
      destination = 'phone';
    }

    $.each(data.columns[source], function( index, col ){
      data.columns[destination].push( dominator_ui.refresh_ids( $.extend(true, {}, col) ) );
    })

    $('.wcpt-editor-save-button').click();
    
    window.location.reload();

  }

  // settings

  //-- reset settings

  $('body').on('click', '.wcpt-reset-global-settings', function(e){
    if( window.confirm('Are you sure you want to reset WCPT global settings? This will not delete your tables. It will only reset the global settings for this plugin.') ){
      return;
    }
    e.preventDefault();    
  })
    
  //-- license activation
  $('body').on('click', '.wcpt-activate-license, .wcpt-deactivate-license', function(){
    var $this = $(this),
        $buttons = $this.siblings().addBack(),
        action = 'wcpt_manage_license',
        purpose = $this.attr('data-wcpt-purpose'),
        nonce = $this.attr('data-wcpt-nonce'),
        $container = $this.closest('[wcpt-model-key="pro_license"]'),
        key = $container.find('[wcpt-model-key="key"]').val(),
        $feedback = $container.find('.wcpt-license-feedback');

    if( ! key || key.length != 32 ){
      alert('Please enter the valid 32 character license key received in your purchase email.');
      return;
    }

    $.ajax( {
      type: "POST",
      url: ajaxurl,

      beforeSend: function(){
        $('>span', $feedback).addClass('wcpt-hide');
        $container.addClass("wcpt-verifying-license");
        $buttons.prop("disabled", true);
      },

      data: {
        action: action,
        wcpt_nonce: nonce,
        wcpt_purpose: purpose,
        wcpt_key: key,
      },

      success: function(data){
        $container.removeClass("wcpt-verifying-license");
        $buttons.prop("disabled", false);

        switch (data) {
          case 'deactivated':
            $feedback.find('.wcpt-response-deactivated').removeClass('wcpt-hide');
            $buttons.filter('[data-wcpt-purpose="deactivate"]').prop('disabled', true);
            break;

          case 'activated':
            $feedback.find('.wcpt-response-activated').removeClass('wcpt-hide');
            $buttons.filter('[data-wcpt-purpose="activate"]').prop('disabled', true);
            break;

          case 'active_elsewhere':
            $feedback.find('.wcpt-response-active-elsewhere').removeClass('wcpt-hide');
            break;

          case 'invalid_key':
            $feedback.find('.wcpt-response-invalid-key').removeClass('wcpt-hide');
            break;

          default: // invalid response
            $feedback.find('.wcpt-response-invalid-response').removeClass('wcpt-hide');
        }

      }
    } );

  })

  //-- pre-open license activation
  if( window.location.hash && window.location.hash.substr(1) == 'pro_license' ){
    $('[wcpt-model-key="pro_license"]').addClass('wcpt-open');
  }

  // WCPT Lite - PRO feature arrangement

  // shortcode otions
  var $shortcode_ops = $('.wcpt-shortcode-info'),
      $pro_op_row = $('tr', $shortcode_ops).filter(function(){
          var $this = $(this);
          return $this.find('.wcpt-pro-badge').length;
        }),
      $pro_msg_row = $('<tr><td colspan="2">Following are all <span class="wcpt-pro-badge">PRO</span> version options:</td></tr>');
      
  if( $pro_op_row.length ){
    $('td', $pro_msg_row).css({'font-size': '22px',
      'font-weight': 'bold',
      'text-transform': 'capitalize',
      'padding': '40px 10px'
    });

    $.merge($pro_msg_row, $pro_op_row).appendTo('tbody', $shortcode_ops);
  }

});
