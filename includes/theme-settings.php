<?php
/**
 * Theme Settings Page
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Add theme settings page
 */
add_action( 'admin_menu', function() {
  add_theme_page(
    __( 'Teeman asetukset', 'vestelli-avalon' ),
    __( 'Teeman asetukset', 'vestelli-avalon' ),
    'manage_options',
    'va-settings',
    'va_settings_page'
  );
});

/**
 * Sanitize logo URL to ensure HTTPS when saving
 */
add_filter( 'sanitize_option_va_logo', function( $value ) {
  if ( ! empty( $value ) ) {
    $value = set_url_scheme( $value, 'https' );
  }
  return $value;
});

/**
 * Ensure logo URL uses HTTPS when retrieved
 */
add_filter( 'option_va_logo', function( $value ) {
  if ( ! empty( $value ) ) {
    $value = set_url_scheme( $value, 'https' );
  }
  return $value;
});

/**
 * Register settings
 */
add_action( 'admin_init', function() {
  register_setting( 'va_settings', 'va_header_design' );
  register_setting( 'va_settings', 'va_enable_portfolio' );
  register_setting( 'va_settings', 'va_logo' );
  register_setting( 'va_settings', 'va_cta_text' );
  register_setting( 'va_settings', 'va_cta_link' );
  register_setting( 'va_settings', 'va_header_type' );
  register_setting( 'va_settings', 'va_bb_header_template' );
  register_setting( 'va_settings', 'va_header_opacity' );
  register_setting( 'va_settings', 'va_social_facebook' );
  register_setting( 'va_settings', 'va_social_instagram' );
  register_setting( 'va_settings', 'va_social_linkedin' );
  register_setting( 'va_settings', 'va_social_youtube' );
  register_setting( 'va_settings', 'va_show_social_icons' );
  register_setting( 'va_settings', 'va_show_search' );
  register_setting( 'va_settings', 'va_show_language_switcher' );
  register_setting( 'va_settings', 'va_show_cta' );
  register_setting( 'va_settings', 'va_show_cart' );
  register_setting( 'va_settings', 'va_custom_scripts', array(
    'sanitize_callback' => 'va_sanitize_custom_scripts',
  ) );
  
  add_settings_section(
    'va_header_section',
    __( 'Header Asetukset', 'vestelli-avalon' ),
    'va_header_section_callback',
    'va-settings'
  );
  
  add_settings_field(
    'va_header_design',
    __( 'Header-ulkoasu', 'vestelli-avalon' ),
    'va_header_design_field_callback',
    'va-settings',
    'va_header_section'
  );

  add_settings_field(
    'va_enable_portfolio',
    __( 'Portfolio-sisältötyyppi', 'vestelli-avalon' ),
    'va_enable_portfolio_field_callback',
    'va-settings',
    'va_header_section'
  );

  add_settings_field(
    'va_header_type',
    __( 'Header-tyyppi', 'vestelli-avalon' ),
    'va_header_type_field_callback',
    'va-settings',
    'va_header_section'
  );
  
  add_settings_field(
    'va_bb_header_template',
    __( 'Beaver Builder Header Template', 'vestelli-avalon' ),
    'va_bb_header_template_field_callback',
    'va-settings',
    'va_header_section'
  );
  
  add_settings_field(
    'va_logo',
    __( 'Logo', 'vestelli-avalon' ),
    'va_logo_field_callback',
    'va-settings',
    'va_header_section'
  );
  
  add_settings_field(
    'va_cta_text',
    __( 'CTA-painikkeen teksti', 'vestelli-avalon' ),
    'va_cta_text_field_callback',
    'va-settings',
    'va_header_section'
  );
  
  add_settings_field(
    'va_cta_link',
    __( 'CTA-painikkeen linkki', 'vestelli-avalon' ),
    'va_cta_link_field_callback',
    'va-settings',
    'va_header_section'
  );
  
  add_settings_field(
    'va_header_opacity',
    __( 'Headerin läpinäkyvyys etusivulla', 'vestelli-avalon' ),
    'va_header_opacity_field_callback',
    'va-settings',
    'va_header_section'
  );
  
  add_settings_field(
    'va_show_social_icons',
    __( 'Näytä someikonit headerissa', 'vestelli-avalon' ),
    'va_show_social_icons_callback',
    'va-settings',
    'va_header_section'
  );
  
  add_settings_field(
    'va_show_search',
    __( 'Näytä hakupalkki headerissa', 'vestelli-avalon' ),
    'va_show_search_callback',
    'va-settings',
    'va_header_section'
  );
  
  add_settings_field(
    'va_show_language_switcher',
    __( 'Näytä kielivalinta headerissa', 'vestelli-avalon' ),
    'va_show_language_switcher_callback',
    'va-settings',
    'va_header_section'
  );
  
  add_settings_field(
    'va_show_cta',
    __( 'Näytä CTA-painike headerissa', 'vestelli-avalon' ),
    'va_show_cta_callback',
    'va-settings',
    'va_header_section'
  );
  
  add_settings_field(
    'va_show_cart',
    __( 'Näytä WooCommerce cart icon headerissa', 'vestelli-avalon' ),
    'va_show_cart_callback',
    'va-settings',
    'va_header_section'
  );
  
  add_settings_section(
    'va_social_section',
    __( 'Sosiaalinen media', 'vestelli-avalon' ),
    'va_social_section_callback',
    'va-settings'
  );
  
  add_settings_field( 'va_social_facebook', __( 'Facebook URL', 'vestelli-avalon' ), 'va_social_facebook_callback', 'va-settings', 'va_social_section' );
  add_settings_field( 'va_social_instagram', __( 'Instagram URL', 'vestelli-avalon' ), 'va_social_instagram_callback', 'va-settings', 'va_social_section' );
  add_settings_field( 'va_social_linkedin', __( 'LinkedIn URL', 'vestelli-avalon' ), 'va_social_linkedin_callback', 'va-settings', 'va_social_section' );
  add_settings_field( 'va_social_youtube', __( 'YouTube URL', 'vestelli-avalon' ), 'va_social_youtube_callback', 'va-settings', 'va_social_section' );
  
  add_settings_section(
    'va_scripts_section',
    __( 'Scriptit', 'vestelli-avalon' ),
    'va_scripts_section_callback',
    'va-settings'
  );
  
  add_settings_field(
    'va_custom_scripts',
    __( 'Lisäscriptit (head)', 'vestelli-avalon' ),
    'va_custom_scripts_callback',
    'va-settings',
    'va_scripts_section'
  );
});

/**
 * Sanitize custom scripts - preserve script tags for tracking/chat bots
 */
function va_sanitize_custom_scripts( $value ) {
  if ( empty( trim( $value ) ) ) {
    return '';
  }
  return wp_unslash( $value );
}

/**
 * Section callback
 */
function va_header_section_callback() {
  echo '<p>' . __( 'Muokkaa headerin asetuksia', 'vestelli-avalon' ) . '</p>';
}

/**
 * Header design field callback
 */
function va_header_design_field_callback() {
  $design = get_option( 'va_header_design', 'avalon' );
  ?>
  <select name="va_header_design" id="va_header_design">
    <option value="avalon" <?php selected( $design, 'avalon' ); ?>><?php _e( 'Avalon Nordic (tummansininen, fixed)', 'vestelli-avalon' ); ?></option>
    <option value="vestelli" <?php selected( $design, 'vestelli' ); ?>><?php _e( 'Vestelli (kaksiväri, valkoinen/sininen)', 'vestelli-avalon' ); ?></option>
  </select>
  <p class="description"><?php _e( 'Valitse headerin ulkoasu.', 'vestelli-avalon' ); ?></p>
  <?php
}

/**
 * Enable portfolio field callback
 */
function va_enable_portfolio_field_callback() {
  $enabled = get_option( 'va_enable_portfolio', '0' );
  ?>
  <label>
    <input type="checkbox" name="va_enable_portfolio" value="1" <?php checked( $enabled, '1' ); ?> />
    <?php _e( 'Ota Portfolio-sisältötyyppi käyttöön', 'vestelli-avalon' ); ?>
  </label>
  <?php
}

/**
 * Header type field callback
 */
function va_header_type_field_callback() {
  $header_type = get_option( 'va_header_type', 'custom' );
  
  // Check if BB Themer header exists
  $themer_header_exists = false;
  $themer_header_id = false;
  
  // Method 1: Check if Themer header layout function exists and returns a value
  if ( function_exists( 'fl_theme_builder_get_header_layout' ) ) {
    $themer_header_id = fl_theme_builder_get_header_layout();
    $themer_header_exists = ! empty( $themer_header_id ) && is_numeric( $themer_header_id );
  }
  
  // Method 2: Check if Themer has published header layouts
  if ( ! $themer_header_exists && post_type_exists( 'fl-theme-layout' ) ) {
    $themer_headers = get_posts( array(
      'post_type'      => 'fl-theme-layout',
      'posts_per_page' => 1,
      'post_status'    => 'publish',
      'meta_query'     => array(
        array(
          'key'   => '_fl_theme_layout_type',
          'value' => 'header',
        ),
      ),
    ) );
    $themer_header_exists = ! empty( $themer_headers );
    if ( $themer_header_exists && ! empty( $themer_headers[0] ) ) {
      $themer_header_id = $themer_headers[0]->ID;
    }
  }
  ?>
  <?php if ( $themer_header_exists ) : ?>
    <div class="notice notice-info inline" style="margin: 10px 0;">
      <p>
        <?php 
        printf( 
          __( 'BB Themer header on aktiivinen ja käytössä (ID: %s). Se on korkeimmalla prioriteetilla.', 'vestelli-avalon' ),
          esc_html( $themer_header_id )
        ); 
        ?>
      </p>
    </div>
  <?php else : ?>
    <div class="notice notice-warning inline" style="margin: 10px 0;">
      <p><?php _e( 'BB Themer headeria ei löytynyt. Käytetään asetuksissa valittua headeria.', 'vestelli-avalon' ); ?></p>
    </div>
  <?php endif; ?>
  <select name="va_header_type" id="va_header_type">
    <option value="custom" <?php selected( $header_type, 'custom' ); ?>><?php _e( 'Mukautettu header', 'vestelli-avalon' ); ?></option>
    <option value="beaver-builder" <?php selected( $header_type, 'beaver-builder' ); ?>><?php _e( 'Beaver Builder header', 'vestelli-avalon' ); ?></option>
  </select>
  <p class="description">
    <?php 
    if ( $themer_header_exists ) {
      _e( 'Nämä asetukset käytetään vain jos BB Themer headeria ei ole määritelty.', 'vestelli-avalon' );
    } else {
      _e( 'Valitse käytettävä header-tyyppi', 'vestelli-avalon' );
    }
    ?>
  </p>
  <?php
}

/**
 * Beaver Builder header template field callback
 */
function va_bb_header_template_field_callback() {
  $header_type = get_option( 'va_header_type', 'custom' );
  $template_id = get_option( 'va_bb_header_template', '' );
  
  // Get Beaver Builder templates
  $templates = array();
  if ( class_exists( 'FLBuilder' ) && function_exists( 'FLBuilderModel' ) ) {
    $posts = get_posts( array(
      'post_type'      => 'fl-builder-template',
      'posts_per_page' => -1,
      'post_status'    => 'publish',
      'meta_query'     => array(
        array(
          'key'   => '_fl_builder_template_type',
          'value' => 'header',
        ),
      ),
    ) );
    
    foreach ( $posts as $post ) {
      $templates[ $post->ID ] = $post->post_title;
    }
  }
  ?>
  <select name="va_bb_header_template" id="va_bb_header_template" <?php echo $header_type !== 'beaver-builder' ? 'style="display:none;"' : ''; ?>>
    <option value=""><?php _e( '-- Valitse template --', 'vestelli-avalon' ); ?></option>
    <?php foreach ( $templates as $id => $title ) : ?>
      <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $template_id, $id ); ?>>
        <?php echo esc_html( $title ); ?>
      </option>
    <?php endforeach; ?>
  </select>
  <?php if ( empty( $templates ) && $header_type === 'beaver-builder' ) : ?>
    <p class="description" style="color: #d63638;">
      <?php _e( 'Ei löytynyt Beaver Builder header-templatteja. Luo ensin header-template Beaver Builderissa.', 'vestelli-avalon' ); ?>
    </p>
  <?php else : ?>
    <p class="description">
      <?php _e( 'Valitse Beaver Builder header-template', 'vestelli-avalon' ); ?>
    </p>
  <?php endif; ?>
  
  <script>
  jQuery(document).ready(function($) {
    function toggleHeaderFields() {
      var headerType = $('#va_header_type').val();
      if (headerType === 'beaver-builder') {
        $('#va_bb_header_template').show();
        $('#va_logo_wrapper').hide();
        $('#va_cta_text_wrapper').hide();
        $('#va_cta_link_wrapper').hide();
        $('#va_header_opacity_wrapper').hide();
        $('#va_show_social_icons_wrapper').hide();
        $('#va_show_search_wrapper').hide();
        $('#va_show_language_switcher_wrapper').hide();
        $('#va_show_cta_wrapper').hide();
        $('#va_show_cart_wrapper').hide();
      } else {
        $('#va_bb_header_template').hide();
        $('#va_logo_wrapper').show();
        $('#va_cta_text_wrapper').show();
        $('#va_cta_link_wrapper').show();
        $('#va_header_opacity_wrapper').show();
        $('#va_show_social_icons_wrapper').show();
        $('#va_show_search_wrapper').show();
        $('#va_show_language_switcher_wrapper').show();
        $('#va_show_cta_wrapper').show();
        $('#va_show_cart_wrapper').show();
      }
    }
    
    $('#va_header_type').on('change', toggleHeaderFields);
    toggleHeaderFields(); // Run on page load
  });
  </script>
  <?php
}

/**
 * Logo field callback
 */
function va_logo_field_callback() {
  $header_type = get_option( 'va_header_type', 'custom' );
  $logo = get_option( 'va_logo' );
  $logo_https = ! empty( $logo ) ? set_url_scheme( $logo, 'https' ) : '';
  ?>
  <div class="vestelli-logo-upload" id="va_logo_wrapper" <?php echo $header_type !== 'custom' ? 'style="display:none;"' : ''; ?>>
    <input type="hidden" id="va_logo" name="va_logo" value="<?php echo esc_attr( $logo_https ); ?>" />
    <div id="va_logo_preview" style="margin-bottom: 10px;">
      <?php if ( $logo_https ) : ?>
        <img src="<?php echo esc_url( $logo_https ); ?>" style="max-width: 200px; height: auto; display: block;" />
      <?php endif; ?>
    </div>
    <button type="button" class="button" id="va_upload_logo_btn">
      <?php _e( 'Valitse logo', 'vestelli-avalon' ); ?>
    </button>
    <button type="button" class="button" id="va_remove_logo_btn" style="margin-left: 10px; <?php echo ! $logo ? 'display: none;' : ''; ?>">
      <?php _e( 'Poista logo', 'vestelli-avalon' ); ?>
    </button>
  </div>
  <script>
  jQuery(document).ready(function($) {
    var mediaUploader;
    
    $('#va_upload_logo_btn').on('click', function(e) {
      e.preventDefault();
      
      if (mediaUploader) {
        mediaUploader.open();
        return;
      }
      
      mediaUploader = wp.media({
        title: '<?php _e( 'Valitse logo', 'vestelli-avalon' ); ?>',
        button: {
          text: '<?php _e( 'Käytä tätä kuvaa', 'vestelli-avalon' ); ?>'
        },
        multiple: false
      });
      
      mediaUploader.on('select', function() {
        var attachment = mediaUploader.state().get('selection').first().toJSON();
        // Convert to HTTPS
        var logoUrl = attachment.url.replace(/^http:/, 'https:');
        $('#va_logo').val(logoUrl);
        $('#va_logo_preview').html('<img src="' + logoUrl + '" style="max-width: 200px; height: auto; display: block;" />');
        $('#va_remove_logo_btn').css('display', 'inline-block');
      });
      
      mediaUploader.open();
    });
    
    $('#va_remove_logo_btn').on('click', function(e) {
      e.preventDefault();
      $('#va_logo').val('');
      $('#va_logo_preview').html('');
      $(this).hide();
    });
  });
  </script>
  <?php
}

/**
 * CTA text field callback
 */
function va_cta_text_field_callback() {
  $header_type = get_option( 'va_header_type', 'custom' );
  $cta_text = get_option( 'va_cta_text', 'Pyydä tarjous' );
  ?>
  <div id="va_cta_text_wrapper" <?php echo $header_type !== 'custom' ? 'style="display:none;"' : ''; ?>>
    <input type="text" name="va_cta_text" value="<?php echo esc_attr( $cta_text ); ?>" class="regular-text" />
  </div>
  <?php
}

/**
 * CTA link field callback
 */
function va_cta_link_field_callback() {
  $header_type = get_option( 'va_header_type', 'custom' );
  $cta_link = get_option( 'va_cta_link', '#' );
  ?>
  <div id="va_cta_link_wrapper" <?php echo $header_type !== 'custom' ? 'style="display:none;"' : ''; ?>>
    <input type="url" name="va_cta_link" value="<?php echo esc_url( $cta_link ); ?>" class="regular-text" />
    <p class="description"><?php _e( 'Syötä täydellinen URL-osoite (esim. https://example.com/yhteys)', 'vestelli-avalon' ); ?></p>
  </div>
  <?php
}

/**
 * Header opacity field callback
 */
function va_header_opacity_field_callback() {
  $header_type = get_option( 'va_header_type', 'custom' );
  $opacity = get_option( 'va_header_opacity', '80' );
  // Ensure opacity is between 0 and 100
  $opacity = max( 0, min( 100, intval( $opacity ) ) );
  ?>
  <div id="va_header_opacity_wrapper" <?php echo $header_type !== 'custom' ? 'style="display:none;"' : ''; ?>>
    <input type="number" name="va_header_opacity" value="<?php echo esc_attr( $opacity ); ?>" min="0" max="100" step="1" class="small-text" />
    <span>%</span>
    <p class="description"><?php _e( 'Headerin läpinäkyvyys etusivulla (0-100). 0 = täysin läpinäkyvä, 100 = täysin peittävä. Oletus: 80%', 'vestelli-avalon' ); ?></p>
  </div>
  <?php
}

/**
 * Header extras visibility callbacks (yes/no)
 */
function va_show_social_icons_callback() {
  $header_type = get_option( 'va_header_type', 'custom' );
  $value = get_option( 'va_show_social_icons', '1' );
  ?>
  <div id="va_show_social_icons_wrapper" <?php echo $header_type !== 'custom' ? 'style="display:none;"' : ''; ?>>
    <label>
      <input type="checkbox" name="va_show_social_icons" value="1" <?php checked( $value, '1' ); ?> />
      <?php _e( 'Kyllä (näytä)', 'vestelli-avalon' ); ?>
    </label>
  </div>
  <?php
}

function va_show_search_callback() {
  $header_type = get_option( 'va_header_type', 'custom' );
  $value = get_option( 'va_show_search', '1' );
  ?>
  <div id="va_show_search_wrapper" <?php echo $header_type !== 'custom' ? 'style="display:none;"' : ''; ?>>
    <label>
      <input type="checkbox" name="va_show_search" value="1" <?php checked( $value, '1' ); ?> />
      <?php _e( 'Kyllä (näytä)', 'vestelli-avalon' ); ?>
    </label>
  </div>
  <?php
}

function va_show_language_switcher_callback() {
  $header_type = get_option( 'va_header_type', 'custom' );
  $value = get_option( 'va_show_language_switcher', '1' );
  ?>
  <div id="va_show_language_switcher_wrapper" <?php echo $header_type !== 'custom' ? 'style="display:none;"' : ''; ?>>
    <label>
      <input type="checkbox" name="va_show_language_switcher" value="1" <?php checked( $value, '1' ); ?> />
      <?php _e( 'Kyllä (näytä)', 'vestelli-avalon' ); ?>
    </label>
  </div>
  <?php
}

function va_show_cta_callback() {
  $header_type = get_option( 'va_header_type', 'custom' );
  $value = get_option( 'va_show_cta', '1' );
  ?>
  <div id="va_show_cta_wrapper" <?php echo $header_type !== 'custom' ? 'style="display:none;"' : ''; ?>>
    <label>
      <input type="checkbox" name="va_show_cta" value="1" <?php checked( $value, '1' ); ?> />
      <?php _e( 'Kyllä (näytä)', 'vestelli-avalon' ); ?>
    </label>
  </div>
  <?php
}

function va_show_cart_callback() {
  $header_type = get_option( 'va_header_type', 'custom' );
  $value = get_option( 'va_show_cart', '' );
  if ( $value === '' ) {
    // Backward compatibility: if old hide_cart is used, invert it as initial default.
    $value = ( get_option( 'vestelli_hide_cart', '0' ) === '1' ) ? '0' : '1';
  }
  ?>
  <div id="va_show_cart_wrapper" <?php echo $header_type !== 'custom' ? 'style="display:none;"' : ''; ?>>
    <label>
      <input type="checkbox" name="va_show_cart" value="1" <?php checked( $value, '1' ); ?> />
      <?php _e( 'Kyllä (näytä)', 'vestelli-avalon' ); ?>
    </label>
  </div>
  <?php
}

/**
 * Social section callback
 */
function va_social_section_callback() {
  echo '<p>' . __( 'Sosiaalisen median -linkit headerissa. Jätä tyhjäksi, jos et halua näyttää kuvaketta.', 'vestelli-avalon' ) . '</p>';
}

function va_social_facebook_callback() {
  $url = get_option( 'va_social_facebook', '' );
  echo '<input type="url" name="va_social_facebook" value="' . esc_url( $url ) . '" class="regular-text" placeholder="https://facebook.com/..." />';
}

function va_social_instagram_callback() {
  $url = get_option( 'va_social_instagram', '' );
  echo '<input type="url" name="va_social_instagram" value="' . esc_url( $url ) . '" class="regular-text" placeholder="https://instagram.com/..." />';
}

function va_social_linkedin_callback() {
  $url = get_option( 'va_social_linkedin', '' );
  echo '<input type="url" name="va_social_linkedin" value="' . esc_url( $url ) . '" class="regular-text" placeholder="https://linkedin.com/company/..." />';
}

function va_social_youtube_callback() {
  $url = get_option( 'va_social_youtube', '' );
  echo '<input type="url" name="va_social_youtube" value="' . esc_url( $url ) . '" class="regular-text" placeholder="https://youtube.com/..." />';
}

/**
 * Scripts section callback
 */
function va_scripts_section_callback() {
  echo '<p>' . __( 'Lisää esim. Leadoo-, analytics- tai chat-skriptejä. Scriptit lisätään sivuston &lt;head&gt;-osion loppuun.', 'vestelli-avalon' ) . '</p>';
}

/**
 * Custom scripts field callback
 */
function va_custom_scripts_callback() {
  $scripts = get_option( 'va_custom_scripts', '' );
  ?>
  <textarea name="va_custom_scripts" id="va_custom_scripts" rows="8" class="large-text code"><?php echo esc_textarea( $scripts ); ?></textarea>
  <p class="description">
    <?php _e( 'Liitä script-tagit tähän. Esimerkki:', 'vestelli-avalon' ); ?><br>
    <code>&lt;script type="text/javascript" async src="https://example.com/script.js?id=YOUR_ID"&gt;&lt;/script&gt;</code>
  </p>
  <?php
}

/**
 * Output custom scripts in head
 */
add_action( 'wp_head', function() {
  $scripts = get_option( 'va_custom_scripts', '' );
  if ( ! empty( trim( $scripts ) ) ) {
    echo "\n" . $scripts . "\n";
  }
}, 99 );

/**
 * Settings page callback
 */
function va_settings_page() {
  if ( ! current_user_can( 'manage_options' ) ) {
    return;
  }
  
  if ( isset( $_GET['settings-updated'] ) ) {
    add_settings_error( 'vestelli_messages', 'vestelli_message', __( 'Asetukset tallennettu', 'vestelli-avalon' ), 'updated' );
  }
  
  settings_errors( 'vestelli_messages' );
  ?>
  <div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <form action="options.php" method="post">
      <?php
      settings_fields( 'va_settings' );
      do_settings_sections( 'va-settings' );
      submit_button( __( 'Tallenna asetukset', 'vestelli-avalon' ) );
      ?>
    </form>
  </div>
  <?php
}

/**
 * Enqueue media uploader scripts
 */
add_action( 'admin_enqueue_scripts', function( $hook ) {
  if ( 'appearance_page_vestelli-settings' !== $hook ) {
    return;
  }
  
  wp_enqueue_media();
});
