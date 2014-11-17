<?php
// inline-save-post.php

add_action('wp_footer', 'add_inline_js');

function add_inline_js() {
  if ( 'level' == get_post_type() && current_user_can( 'edit_post', get_the_ID() ) ) {
    $link = admin_url('admin-ajax.php?action=inline_save_post&post_id='.$post->ID.'&nonce='.$nonce);
    ?>
<script type="text/javascript">
  var nonce = "<?php echo wp_create_nonce('inline_save_post'); ?>";
  var process_text;
  // Doc ready
  jQuery( document ).ready( function( $ ) {
    var qs_timers = {};


// Check for a status field
    var check_status_field = function( id ) {
      if ( !$( '#post-status-'+ id ).length ) {
        // Create a new field since it doesn't exist yet
        var status_field = $('<div>');
        status_field.attr( 'id', 'post-status-'+ id );
        status_field.addClass('post-status');
        status_field.hide();

        if ( $( '#post-'+ id +' .inline-edit-title' ).length ) {
          $( '#post-'+ id +' .inline-edit-title' ).after( status_field );
        }
        else {
          $( '#post-'+ id ).prepend( status_field );
        }
      }
    };


// Save for real the posts
    var ajax_save = function( obj ) {
      var post_ID = obj.id;
      var attr = {
        action: 'inline_save_post',
        post_id: obj.id,
        nonce: nonce
      };
      $( '#post-status-'+ post_ID ).text( 'Listening...' ).filter(':not(:visible)').fadeIn( 500 );
      return function() {
        $( '#post-status-'+ post_ID ).text( 'Saving...' );
        if ( obj.content ) {
          attr.content = obj.content.val();
        }
        if ( obj.title ) {
          attr.title = obj.title.val();
        }
        console.log( attr );
        $.post( '/wp-admin/admin-ajax.php', attr, function( data ) {
          if ( '0' === data )
            $( '#post-status-'+ post_ID ).text( 'Failed!' ).fadeOut( 2000 );
          else
            $( '#post-status-'+ post_ID ).text( 'Saved!' ).fadeOut( 2000 );
        } );
      };
    };


// Queue saving of the post
    var queue_save = function( post_ID, which, what ) {
      return function () {
        if ( !qs_timers[post_ID] ) {
          qs_timers[post_ID] = {id: post_ID};
        }
        qs_timers[post_ID][which] = what;
        var obj = qs_timers[post_ID];
        if ( qs_timers[post_ID].timer ) {
          qs_timers[post_ID].timer = clearTimeout( qs_timers[post_ID].timer );
        }
        qs_timers[post_ID].timer = setTimeout( ajax_save( obj ), 4000 );
      };
    };

    var expand_ta = function() {
      var text = $( this ).val();
      // look for any "\n" occurences
      var matches = text.match( /\n/g );
      var breaks = matches ? matches.length + 1 : 1;
      $( this ).height( '' );
      var h = $( this )[0].scrollHeight;
      $( this ).height( h );
    };

// Colourized inline text editing
    $( '.inline-edit' ).each( function() {
      var orig = $( this );
      var id = orig.parent().parent().attr( 'id' );

      if ( id ) {
        id = id.replace( 'post-', '' );
      }
      orig.hide();
      check_status_field( id );

      var edit = $( '<div>' );
      edit.addClass( 'inline-editable-content' );
      var ta = $( '<textarea>' );
      ta.addClass( 'inline-textarea' );

      ta.text( orig.text() );

      ta.on( 'keyup', expand_ta );
      setTimeout( function() { ta.each( expand_ta ); }, 500 );
      ta.on( 'keyup', function() {
        edit.html( process_text( $(this).val() ).replace( /\n/g, '<br>' ) );
      } );
      edit.html( process_text( ta.val() ).replace( /\n/g, '<br>' ) );
      orig.after( $( '<div>' ).addClass( 'inline-wrapper' ).append( edit, ta ) );
      if ( id ) {
        ta.on( 'keyup', queue_save( id, 'content', ta ) );
      }
    } );


// Edit titles
    $( '.inline-edit-title' ).each( function() {
      var orig = $( this );
      var id = orig.parent().parent().attr( 'id' );

      if ( id ) {
        id = id.replace( 'post-', '' );
      }
      orig.hide();
      check_status_field( id );

      var edit = $( '<textarea>' );
      edit.addClass( 'inline-editable-title h1' );
      edit.val( orig.html() );
      edit.attr('rows', 1);
      edit.on( 'keyup', expand_ta );
      setTimeout( function() { edit.each( expand_ta ); }, 500 );
      orig.after( edit );
      if ( id ) {
        edit.on( 'keyup', queue_save( id, 'title', edit ) );
      }
    } );

  } ); // - doc ready


// Gen HTML
  var gen_html = function( obj, text, attr_class, prefix ) {
    if ( !prefix ) {
      prefix = '';
    }
    if ( typeof obj !== 'object' ) {
      return;
    }
    var keys = Object.keys(obj);
    for (var i = keys.length - 1; i >= 0 ; i--) {
      var sound = keys[i];
      text = text.replace( new RegExp( prefix + obj[sound], 'g' ), '<span class="'+ attr_class +'">'+ sound +'</span>' );
    }

    return text;
  };


// Process html
  process_text = function( text ) {
      text = ' '+ text;
      var html = '';
      var diftonger = {}, _diftonger = ['øy', 'ai', 'ei', 'au', 'Øy', 'Ai', 'Ei', 'Au'];
      var konsonanter = {}, _konsonanter = 'QWRTPSDFGHJKLXCVBNMqwrtpsdfghjklxcvbnm'.split('');
      var vokaler = {}, _vokaler = ['E', 'Y', 'U', 'I', 'O', 'Å', 'A', 'Ø', 'Æ', 'e', 'y', 'u', 'i', 'o', 'å', 'a', 'ø', 'æ'];
      // Special rules for some words
      var special_rules = {
        // Non-silent d's
        'jo=d': 'jod',
        'sver=d': 'sverd',

        // Silent g's
        'morgen': 'mor=gen',
        'og': 'o=g',
        'også': 'o=gså',
        'selge': 'sel=ge',
        'følge': 'føl=ge',
        'fugl': 'fu=gl',

        // Stum H
        '([Hh])([jv])': '=$2$3',
        '([Ll])j': '=$2j',

        // Stum t i ordet "det" og bestemt artikkel intentkjønn
        'det': 'de=t',

        // Grønn misfarging; skriver e, men sier æ.
        'er': '~!er',
        'der': 'd~!er',
        'her': 'h~!er',
        'hver': 'hv~!er',

        // Blå misfarging Kj, Ky, Ki, Tj, Skje, Sj, Sh, g, rs
        '([Kk])([yi])': '~!!$2$3',
        '([Kk])j': '~!!$2~!!j',
        '([Tt])j': '~!!$2~!!j',
        '([Ss])j': '~!!$2~!!j',
        '([Ss])h': '~!!$2~!!h',
        '([Ss])k(i|y|øy)': '~!!$2~!!k$3',
        '([Ss])kj': '~!!$2~!!k~!!j',


        // Den grønne listen (korte ord)
        'an': '~~an',
        'at': '~~at',
        'bør': 'b~~ør',
        'den': 'd~~en',
        'et': '~~et',
        'for': 'f~~or',
        'han': 'h~~an',
        'hen': 'h~~en',
        'hos': 'h~~os',
        'hun': 'h~~un',
        'hvis': 'hv~~is',
        'igjen': 'igj~~en',
        'kan': 'k~~an',
        'kun': 'k~~un',
        'man': 'm~~an',
        'men': 'm~~en',
        'nok': 'n~~ok',
        'når': 'n~~år',
        'skal': 'sk~~al',
        'spør': 'sp~~ør',
        'til': 't~~il',
        'tør': 't~~ør',
        'vel': 'v~~el',
        'vil': 'v~~il',
      };
      var sound, i;


      // Colourless / Trollbokstaver
      for ( i = 0; i < _vokaler.length; i++ ) {
        vokaler['='+_vokaler[i]] = '='+i;
      }
      // Diftonger / Kjærestepar
      for ( i = 0; i < _diftonger.length; i++ ) {
        diftonger[_diftonger[i]] = '!'+i;
      }
      // Korte vokaler
      for ( i = 0; i < _vokaler.length; i++ ) {
        vokaler['~~'+_vokaler[i]] = '~'+i;
      }
      // Tryklette vokaler
      vokaler['~e'] = '~'+ _vokaler.length;
      vokaler['~E'] = '~'+ _vokaler.length + 1;

      // Vanlige vokaler
      for ( i = 0; i < _vokaler.length; i++ ) {
        vokaler[_vokaler[i]] = '~'+ ( _vokaler.length + 2 + i );
      }

      // Konsonanter
      for ( i = 0; i < _konsonanter.length; i++ ) {
        konsonanter[_konsonanter[i]] = '-'+i;
      }


      // Apply special rules
      for ( i in special_rules ) {
        special_rules[ i.charAt(0).toUpperCase() + i.substr(1) ] = special_rules[i].charAt(0).toUpperCase() + special_rules[i].substr(1);
      }
      for ( i in special_rules ) {
        text = text.replace( new RegExp( '(\\s)'+ i, 'g' ), '$1'+ special_rules[i] );
      }

      // Mis-colouring!
      text = text.replace( /u(ng|nk|ff|k[qwrtpsdfghjklxcvbnm]|m)/g, '~!u$1' );
      text = text.replace( /o(=)?([gv])/g, '~!o$1$2' );
      text = text.replace( /(\s)([Dd])e(\s)/g, '$1$2~!e$3' );
      text = text.replace( /(er)([qwrtpsdfghjklxcvbnm])/gi, '~!$1$2' );
      // console.log(text);

      // Replace æi sounds jeg, meg etc...
      text = text.replace( /([^Ll])eg/g, '$1~!!!e~!!!g');
      // Replace short vowels
      text = text.replace( /([EYUIOÅAØÆeyuioåaøæ][qwrtpsdfghjklxcvbnm]{2})/g, '~~$1');
      // Words that end with 'm' have short vowels (except i's)
      text = text.replace( /([EYUOÅAØÆeyuoåaøæ]|[^RrLl][Ii])m([\s\.!,\)\(\]\[])/g, '~~$1m$2');
      // D's in words that end with d should be colourless
      text = text.replace( /([EYUIOÅAØÆeyuioåaøæ])([rnl]?)d([\s\.!,\)\(\]\[])/g, '$1$2=d$3');
      // Replace light vowels (tryklette e'er)
      text = text.replace( /([^~])elige([\s\.!,\)\(\]\[])/g, '$1~elig~e$2');
      text = text.replace( /~else([\s\.!,\)\(\]\[])/g, 'els~e$1');
      text = text.replace( /([^~])ene([\s\.!,\)\(\]\[])/g, '$1~en~e$2');
      text = text.replace( /([^~])(e|elig|en|er)([\s\.!,\)\(\]\[])/g, '$1~$2$3');
      text = text.replace( /([\s\.!,\)\(\]\[])([Bb])(~~e|e)/g, '$1$2~e');


      // Words ending with rd, rl or rn should have long vowels
      text = text.replace( /~~([EYUIOÅAØÆeyuioåaøæ])(rd|rl|rn)/g, '$1$2');
      // console.log( text );

      // Replace sounds (turn letters into numbers)
      for ( sound in diftonger ) {
        text = text.replace( new RegExp( sound, 'g' ), diftonger[sound] );
      }
      for ( sound in vokaler ) {
        text = text.replace( new RegExp( sound, 'g' ), vokaler[sound] );
      }
      for ( sound in konsonanter ) {
        text = text.replace( new RegExp( sound, 'g' ), konsonanter[sound] );
      }

      // Generate the html
      text = gen_html( konsonanter, text, 'red-miscolour', '~!!!');
      text = gen_html( vokaler, text, 'red-miscolour', '~!!!' );
      text = gen_html( diftonger, text, 'diftong' );
      text = gen_html( vokaler, text, 'green-miscolour', '~!' );
      text = gen_html( vokaler, text, 'vokal' );
      text = gen_html( konsonanter, text, 'blue-miscolour', '~!!' );
      text = gen_html( konsonanter, text, 'troll', '=' );
      text = gen_html( konsonanter, text, 'konsonant' );

      // Short vowels
      text = text.replace( /">~~([a-zæøå])/ig, ' kort">$1' );
      text = text.replace( /">~([Ee])/g, ' trykklett">$1' );

      return text;
    };
</script>
    <?php
    echo '<a class="user_vote" data-nonce="' . $nonce . '" data-post_id="' . $post->ID . '" href="' . $link . '">vote for this article</a>';
  }
}

function inline_save_post() {
  header( 'Content-Type: application/json' );
  if ( !isset($_POST['post_id']) && !wp_verify_nonce( $_POST['nonce'], "inline_save_post" )) {
    die( "0" );
  }
  if ( !current_user_can( 'edit_post', get_the_ID() ) ) {
    die( "0" );
  }
  $post_data = array(
    'ID' => $_POST['post_id']
  );
  if ( isset($_POST['content']) && $_POST['content'] ) {
    $post_data['post_content'] = $_POST['content'];
  }
  if ( isset($_POST['title']) && $_POST['title'] ) {
    $post_data['post_title'] = $_POST['title'];
  }
  if ( 1 === count( $post_data ) ) {
    die( "0" );
  }
  wp_update_post( $post_data );

  die( json_encode( $_POST ) );
}
add_action("wp_ajax_inline_save_post", "inline_save_post");
