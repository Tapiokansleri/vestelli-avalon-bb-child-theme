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
 * Match logo URLs to the site URL scheme (http locally, https in production).
 */
function va_normalize_logo_url( $value ) {
  if ( empty( $value ) ) {
    return $value;
  }

  $scheme = wp_parse_url( home_url(), PHP_URL_SCHEME );
  if ( ! $scheme ) {
    $scheme = is_ssl() ? 'https' : 'http';
  }

  return set_url_scheme( $value, $scheme );
}

add_filter( 'sanitize_option_va_logo', 'va_normalize_logo_url' );
add_filter( 'option_va_logo', 'va_normalize_logo_url' );

/**
 * Normalize checkbox values from theme settings.
 */
function va_sanitize_checkbox( $value ) {
  return ! empty( $value ) && $value !== '0' ? '1' : '0';
}

/**
 * Render a theme settings checkbox that saves "0" when unchecked.
 */
function va_render_settings_checkbox( $name, $value, $label, $id = '' ) {
  ?>
  <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="0" />
  <label>
    <input
      type="checkbox"
      name="<?php echo esc_attr( $name ); ?>"
      value="1"
      <?php echo $id ? 'id="' . esc_attr( $id ) . '"' : ''; ?>
      <?php checked( $value, '1' ); ?>
    />
    <?php echo esc_html( $label ); ?>
  </label>
  <?php
}

/**
 * Check whether a BB Themer header layout is active.
 */
function va_settings_themer_header_active() {
  if ( function_exists( 'fl_theme_builder_get_header_layout' ) ) {
    $themer_header_id = fl_theme_builder_get_header_layout();
    if ( ! empty( $themer_header_id ) && is_numeric( $themer_header_id ) ) {
      return true;
    }
  }

  if ( post_type_exists( 'fl-theme-layout' ) ) {
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

    return ! empty( $themer_headers );
  }

  return false;
}

/**
 * Get themer header ID for admin notices.
 */
function va_settings_get_themer_header_id() {
  if ( function_exists( 'fl_theme_builder_get_header_layout' ) ) {
    $themer_header_id = fl_theme_builder_get_header_layout();
    if ( ! empty( $themer_header_id ) && is_numeric( $themer_header_id ) ) {
      return (int) $themer_header_id;
    }
  }

  if ( post_type_exists( 'fl-theme-layout' ) ) {
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

    if ( ! empty( $themer_headers[0] ) ) {
      return (int) $themer_headers[0]->ID;
    }
  }

  return 0;
}

/**
 * Open a settings field wrapper for conditional visibility.
 */
function va_settings_open_field( $field_id, $group = '' ) {
  echo '<div id="va_field_' . esc_attr( $field_id ) . '" class="va-settings-field"';
  if ( $group ) {
    echo ' data-va-group="' . esc_attr( $group ) . '"';
  }
  echo '>';
}

/**
 * Close a settings field wrapper.
 */
function va_settings_close_field() {
  echo '</div>';
}

/**
 * Render a subheading row inside a settings form table.
 */
function va_settings_render_subheading_row( $text, $group = '' ) {
  echo '<tr class="va-settings-subheading-row"';
  if ( $group ) {
    echo ' data-va-group="' . esc_attr( $group ) . '"';
  }
  echo '><th colspan="2"><h3>' . esc_html( $text ) . '</h3></th></tr>';
}

/**
 * Render ordered settings fields (and optional subheadings) for a section.
 */
function va_settings_render_fields( $page, $section_id, $items ) {
  global $wp_settings_fields;

  if ( empty( $wp_settings_fields[ $page ][ $section_id ] ) ) {
    return;
  }

  $fields = $wp_settings_fields[ $page ][ $section_id ];

  echo '<table class="form-table" role="presentation">';

  foreach ( $items as $item ) {
    if ( is_array( $item ) && isset( $item['subheading'] ) ) {
      va_settings_render_subheading_row( $item['subheading'], isset( $item['group'] ) ? $item['group'] : '' );
      continue;
    }

    $field_id = $item;
    if ( empty( $fields[ $field_id ] ) ) {
      continue;
    }

    $field = $fields[ $field_id ];
    $label_for = ! empty( $field['args']['label_for'] ) ? $field['args']['label_for'] : '';

    echo '<tr>';
    if ( $field['title'] ) {
      if ( $label_for ) {
        echo '<th scope="row"><label for="' . esc_attr( $label_for ) . '">' . esc_html( $field['title'] ) . '</label></th>';
      } else {
        echo '<th scope="row">' . esc_html( $field['title'] ) . '</th>';
      }
    } else {
      echo '<th scope="row" class="va-settings-field-spacer"></th>';
    }
    echo '<td>';
    call_user_func( $field['callback'], $field['args'] );
    echo '</td></tr>';
  }

  echo '</table>';
}

/**
 * Render a settings section inside a tab panel.
 */
function va_settings_render_tab_section( $page, $section_id, $tab_id, $field_items = null ) {
  global $wp_settings_sections;

  if ( empty( $wp_settings_sections[ $page ][ $section_id ] ) ) {
    return;
  }

  $section = $wp_settings_sections[ $page ][ $section_id ];
  $active  = ( 'header' === $tab_id ) ? ' is-active' : '';

  echo '<div id="va-tab-panel-' . esc_attr( $tab_id ) . '" class="va-settings-tab-panel' . esc_attr( $active ) . '" data-va-tab-panel="' . esc_attr( $tab_id ) . '">';

  if ( ! empty( $section['callback'] ) ) {
    call_user_func( $section['callback'], $section );
  }

  if ( null !== $field_items ) {
    va_settings_render_fields( $page, $section_id, $field_items );
  } else {
    echo '<table class="form-table" role="presentation">';
    do_settings_fields( $page, $section_id );
    echo '</table>';
  }

  echo '</div>';
}

/**
 * Register settings
 */
add_action( 'admin_init', function() {
  if ( get_option( 'va_header_mobile_breakpoint', '' ) === '' && get_option( 'va_vestelli_mobile_breakpoint', '' ) !== '' ) {
    update_option( 'va_header_mobile_breakpoint', get_option( 'va_vestelli_mobile_breakpoint' ) );
  }

  $checkbox_setting = array( 'sanitize_callback' => 'va_sanitize_checkbox' );

  register_setting( 'va_settings', 'va_brand_color', array(
    'sanitize_callback' => 'sanitize_hex_color',
  ) );
  register_setting( 'va_settings', 'va_accent_color', array(
    'sanitize_callback' => 'sanitize_hex_color',
  ) );
  register_setting( 'va_settings', 'va_button_radius', array(
    'sanitize_callback' => 'absint',
  ) );
  register_setting( 'va_settings', 'va_header_design' );
  register_setting( 'va_settings', 'va_header_mobile_breakpoint', array(
    'sanitize_callback' => 'va_sanitize_header_mobile_breakpoint',
  ) );
  register_setting( 'va_settings', 'va_enable_portfolio', $checkbox_setting );
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
  register_setting( 'va_settings', 'va_show_social_icons', $checkbox_setting );
  register_setting( 'va_settings', 'va_show_search', $checkbox_setting );
  register_setting( 'va_settings', 'va_show_language_switcher', $checkbox_setting );
  register_setting( 'va_settings', 'va_show_cta', $checkbox_setting );
  register_setting( 'va_settings', 'va_show_cart', $checkbox_setting );
  register_setting( 'va_settings', 'va_custom_scripts', array(
    'sanitize_callback' => 'va_sanitize_custom_scripts',
  ) );
  register_setting( 'va_settings', 'va_quote_mode', $checkbox_setting );
  register_setting( 'va_settings', 'va_hide_prices', $checkbox_setting );
  register_setting( 'va_settings', 'va_hide_catalog_ordering', $checkbox_setting );
  register_setting( 'va_settings', 'va_quote_button_text', array(
    'sanitize_callback' => 'sanitize_text_field',
  ) );
  register_setting( 'va_settings', 'va_quote_email', array(
    'sanitize_callback' => 'sanitize_email',
  ) );
  
  add_settings_section(
    'va_header_section',
    __( 'Header Asetukset', 'vestelli-avalon' ),
    'va_header_section_callback',
    'va-settings'
  );
  
  add_settings_field(
    'va_themer_header_notice',
    '',
    'va_themer_header_notice_field_callback',
    'va-settings',
    'va_header_section'
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

  add_settings_field(
    'va_header_mobile_breakpoint',
    __( 'Mobiilivalikon breakpoint (px)', 'vestelli-avalon' ),
    'va_header_mobile_breakpoint_field_callback',
    'va-settings',
    'va_header_section'
  );
  
  add_settings_section(
    'va_colors_section',
    __( 'Värit', 'vestelli-avalon' ),
    'va_colors_section_callback',
    'va-settings'
  );

  add_settings_field(
    'va_brand_color',
    __( 'Brändiväri', 'vestelli-avalon' ),
    'va_brand_color_field_callback',
    'va-settings',
    'va_colors_section'
  );

  add_settings_field(
    'va_accent_color',
    __( 'Korostusväri', 'vestelli-avalon' ),
    'va_accent_color_field_callback',
    'va-settings',
    'va_colors_section'
  );

  add_settings_field(
    'va_button_radius',
    __( 'Painikkeiden reunan pyöristys', 'vestelli-avalon' ),
    'va_button_radius_field_callback',
    'va-settings',
    'va_colors_section'
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

  // WooCommerce quote mode section
  add_settings_section(
    'va_woocommerce_section',
    __( 'WooCommerce', 'vestelli-avalon' ),
    'va_woocommerce_section_callback',
    'va-settings'
  );

  add_settings_field(
    'va_quote_mode',
    __( 'Tarjouspyyntötila', 'vestelli-avalon' ),
    'va_quote_mode_callback',
    'va-settings',
    'va_woocommerce_section'
  );

  add_settings_field(
    'va_hide_prices',
    __( 'Piilota hinnat', 'vestelli-avalon' ),
    'va_hide_prices_callback',
    'va-settings',
    'va_woocommerce_section'
  );

  add_settings_field(
    'va_hide_catalog_ordering',
    __( 'Piilota lajitteluvalikko', 'vestelli-avalon' ),
    'va_hide_catalog_ordering_callback',
    'va-settings',
    'va_woocommerce_section'
  );

  add_settings_field(
    'va_quote_button_text',
    __( 'Painikkeen teksti', 'vestelli-avalon' ),
    'va_quote_button_text_callback',
    'va-settings',
    'va_woocommerce_section'
  );

  add_settings_field(
    'va_quote_email',
    __( 'Tarjouspyyntöjen sähköposti', 'vestelli-avalon' ),
    'va_quote_email_callback',
    'va-settings',
    'va_woocommerce_section'
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
 * BB Themer header notice field callback.
 */
function va_themer_header_notice_field_callback() {
  $themer_header_id = va_settings_get_themer_header_id();
  va_settings_open_field( 'va_themer_header_notice', 'themer-notice' );

  if ( va_settings_themer_header_active() ) {
    ?>
    <div class="notice notice-info inline" style="margin: 0;">
      <p>
        <?php
        printf(
          __( 'BB Themer header on aktiivinen ja käytössä (ID: %s). Se on korkeimmalla prioriteetilla, joten alla olevat header-asetukset eivät vaikuta sivustoon.', 'vestelli-avalon' ),
          esc_html( $themer_header_id )
        );
        ?>
      </p>
    </div>
    <?php
  }

  va_settings_close_field();
}

/**
 * Header design field callback
 */
function va_header_design_field_callback() {
  $design = get_option( 'va_header_design', 'avalon' );
  va_settings_open_field( 'va_header_design', 'header-general' );
  ?>
  <select name="va_header_design" id="va_header_design">
    <option value="avalon" <?php selected( $design, 'avalon' ); ?>><?php _e( 'Avalon Nordic (tummansininen, fixed)', 'vestelli-avalon' ); ?></option>
    <option value="vestelli" <?php selected( $design, 'vestelli' ); ?>><?php _e( 'Vestelli (kaksiväri, valkoinen/sininen)', 'vestelli-avalon' ); ?></option>
  </select>
  <p class="description"><?php _e( 'Valitse headerin ulkoasu.', 'vestelli-avalon' ); ?></p>
  <?php
  va_settings_close_field();
}

/**
 * Shared header mobile breakpoint field callback.
 */
function va_sanitize_header_mobile_breakpoint( $value ) {
  return (string) va_get_header_mobile_breakpoint_from_value( $value );
}

function va_header_mobile_breakpoint_field_callback() {
  $breakpoint = get_option( 'va_header_mobile_breakpoint', '' );
  if ( $breakpoint === '' || $breakpoint === false ) {
    $breakpoint = get_option( 'va_vestelli_mobile_breakpoint', '1200' );
  }
  va_settings_open_field( 'va_header_mobile_breakpoint', 'header-responsive' );
  ?>
  <input
    type="text"
    name="va_header_mobile_breakpoint"
    id="va_header_mobile_breakpoint"
    value="<?php echo esc_attr( $breakpoint ); ?>"
    class="small-text"
  />
  <span>px</span>
  <p class="description">
    <?php _e( 'Leveys (px), jossa header vaihtuu mobiilivalikkoon (Vestelli ja Avalon). Oletus: 1200.', 'vestelli-avalon' ); ?>
  </p>
  <?php
  va_settings_close_field();
}

/**
 * Enable portfolio field callback
 */
function va_enable_portfolio_field_callback() {
  $enabled = get_option( 'va_enable_portfolio', '0' );
  va_settings_open_field( 'va_enable_portfolio', 'header-general' );
  va_render_settings_checkbox( 'va_enable_portfolio', $enabled, __( 'Ota Portfolio-sisältötyyppi käyttöön', 'vestelli-avalon' ) );
  va_settings_close_field();
}

/**
 * Header type field callback
 */
function va_header_type_field_callback() {
  $header_type = get_option( 'va_header_type', 'custom' );
  $themer_header_exists = va_settings_themer_header_active();
  va_settings_open_field( 'va_header_type', 'header-general' );
  ?>
  <?php if ( ! $themer_header_exists ) : ?>
    <div class="notice notice-warning inline" style="margin: 0 0 10px;">
      <p><?php _e( 'BB Themer headeria ei löytynyt. Käytetään asetuksissa valittua headeria.', 'vestelli-avalon' ); ?></p>
    </div>
  <?php endif; ?>
  <select name="va_header_type" id="va_header_type">
    <option value="custom" <?php selected( $header_type, 'custom' ); ?>><?php _e( 'Mukautettu header', 'vestelli-avalon' ); ?></option>
    <option value="beaver-builder" <?php selected( $header_type, 'beaver-builder' ); ?>><?php _e( 'Beaver Builder header', 'vestelli-avalon' ); ?></option>
  </select>
  <p class="description"><?php _e( 'Valitse käytettävä header-tyyppi.', 'vestelli-avalon' ); ?></p>
  <?php
  va_settings_close_field();
}

/**
 * Beaver Builder header template field callback
 */
function va_bb_header_template_field_callback() {
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
  va_settings_open_field( 'va_bb_header_template', 'beaver-header' );
  ?>
  <select name="va_bb_header_template" id="va_bb_header_template">
    <option value=""><?php _e( '-- Valitse template --', 'vestelli-avalon' ); ?></option>
    <?php foreach ( $templates as $id => $title ) : ?>
      <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $template_id, $id ); ?>>
        <?php echo esc_html( $title ); ?>
      </option>
    <?php endforeach; ?>
  </select>
  <?php if ( empty( $templates ) ) : ?>
    <p class="description" style="color: #d63638;">
      <?php _e( 'Ei löytynyt Beaver Builder header-templatteja. Luo ensin header-template Beaver Builderissa.', 'vestelli-avalon' ); ?>
    </p>
  <?php else : ?>
    <p class="description">
      <?php _e( 'Valitse Beaver Builder header-template', 'vestelli-avalon' ); ?>
    </p>
  <?php endif; ?>
  <?php
  va_settings_close_field();
}

/**
 * Logo field callback
 */
function va_logo_field_callback() {
  $logo = get_option( 'va_logo' );
  va_settings_open_field( 'va_logo', 'custom-header' );
  ?>
  <div class="vestelli-logo-upload">
    <input type="hidden" id="va_logo" name="va_logo" value="<?php echo esc_attr( $logo ); ?>" />
    <div id="va_logo_preview" style="margin-bottom: 10px;">
      <?php if ( $logo ) : ?>
        <img src="<?php echo esc_url( $logo ); ?>" style="max-width: 200px; height: auto; display: block;" />
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
        var logoUrl = attachment.url;
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
  va_settings_close_field();
}

/**
 * CTA text field callback
 */
function va_cta_text_field_callback() {
  $cta_text = get_option( 'va_cta_text', 'Pyydä tarjous' );
  va_settings_open_field( 'va_cta_text', 'custom-header' );
  ?>
  <input type="text" name="va_cta_text" value="<?php echo esc_attr( $cta_text ); ?>" class="regular-text" />
  <?php
  va_settings_close_field();
}

/**
 * CTA link field callback
 */
function va_cta_link_field_callback() {
  $cta_link = get_option( 'va_cta_link', '#' );
  va_settings_open_field( 'va_cta_link', 'custom-header' );
  ?>
  <input type="url" name="va_cta_link" value="<?php echo esc_url( $cta_link ); ?>" class="regular-text" />
  <p class="description"><?php _e( 'Syötä täydellinen URL-osoite (esim. https://example.com/yhteys)', 'vestelli-avalon' ); ?></p>
  <?php
  va_settings_close_field();
}

/**
 * Header opacity field callback
 */
function va_header_opacity_field_callback() {
  $opacity = get_option( 'va_header_opacity', '80' );
  $opacity = max( 0, min( 100, intval( $opacity ) ) );
  va_settings_open_field( 'va_header_opacity', 'avalon-opacity' );
  ?>
  <input type="number" name="va_header_opacity" value="<?php echo esc_attr( $opacity ); ?>" min="0" max="100" step="1" class="small-text" />
  <span>%</span>
  <p class="description"><?php _e( 'Headerin läpinäkyvyys etusivulla (0-100). 0 = täysin läpinäkyvä, 100 = täysin peittävä. Oletus: 80%', 'vestelli-avalon' ); ?></p>
  <?php
  va_settings_close_field();
}

/**
 * Header extras visibility callbacks (yes/no)
 */
function va_show_social_icons_callback() {
  $value = get_option( 'va_show_social_icons', '1' );
  va_settings_open_field( 'va_show_social_icons', 'custom-header' );
  va_render_settings_checkbox( 'va_show_social_icons', $value, __( 'Kyllä (näytä)', 'vestelli-avalon' ) );
  va_settings_close_field();
}

function va_show_search_callback() {
  $value = get_option( 'va_show_search', '1' );
  va_settings_open_field( 'va_show_search', 'custom-header' );
  va_render_settings_checkbox( 'va_show_search', $value, __( 'Kyllä (näytä)', 'vestelli-avalon' ) );
  va_settings_close_field();
}

function va_show_language_switcher_callback() {
  $value = get_option( 'va_show_language_switcher', '1' );
  va_settings_open_field( 'va_show_language_switcher', 'custom-header' );
  va_render_settings_checkbox( 'va_show_language_switcher', $value, __( 'Kyllä (näytä)', 'vestelli-avalon' ) );
  va_settings_close_field();
}

function va_show_cta_callback() {
  $value = get_option( 'va_show_cta', '1' );
  va_settings_open_field( 'va_show_cta', 'custom-header' );
  va_render_settings_checkbox( 'va_show_cta', $value, __( 'Kyllä (näytä)', 'vestelli-avalon' ) );
  va_settings_close_field();
}

function va_show_cart_callback() {
  $value = get_option( 'va_show_cart', '' );
  if ( $value === '' ) {
    $value = ( get_option( 'vestelli_hide_cart', '0' ) === '1' ) ? '0' : '1';
  }
  va_settings_open_field( 'va_show_cart', 'custom-header' );
  va_render_settings_checkbox( 'va_show_cart', $value, __( 'Kyllä (näytä)', 'vestelli-avalon' ) );
  va_settings_close_field();
}

/**
 * Colors section callback
 */
function va_colors_section_callback() {
  echo '<p>' . __( 'Sivuston brändivärit. Väriä käytetään painikkeissa, taulukoiden otsikoissa, portfoliossa ym.', 'vestelli-avalon' ) . '</p>';
}

/**
 * Brand color field callback
 */
function va_brand_color_field_callback() {
  $color = get_option( 'va_brand_color', '#012b55' );
  ?>
  <input type="text" name="va_brand_color" value="<?php echo esc_attr( $color ); ?>" class="va-color-picker" data-default-color="#012b55" />
  <p class="description"><?php _e( 'Valitse sivuston pääväri. Käytetään painikkeissa, taulukoissa, portfoliossa ja muissa elementeissä. Oletus: #012b55', 'vestelli-avalon' ); ?></p>
  <?php
}

/**
 * Accent color field callback
 */
function va_accent_color_field_callback() {
  $color = get_option( 'va_accent_color', '#30CBD3' );
  ?>
  <input type="text" name="va_accent_color" value="<?php echo esc_attr( $color ); ?>" class="va-color-picker" data-default-color="#30CBD3" />
  <p class="description"><?php _e( 'Korostusväri CTA-painikkeille, ostoskori-badgeille ja hakukenttien korostuksille. Oletus: #30CBD3', 'vestelli-avalon' ); ?></p>
  <?php
}

/**
 * Button radius field callback
 */
function va_button_radius_field_callback() {
  $radius = get_option( 'va_button_radius', '10' );
  ?>
  <input type="number" name="va_button_radius" value="<?php echo esc_attr( $radius ); ?>" min="0" max="50" step="1" class="small-text" />
  <span>px</span>
  <p class="description"><?php _e( 'Painikkeiden reunojen pyöristys pikseleinä. 0 = suorakulmainen, 50 = täysin pyöreä. Oletus: 10px', 'vestelli-avalon' ); ?></p>
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
 * WooCommerce section callback
 */
function va_woocommerce_section_callback() {
  echo '<p>' . __( 'Tarjouspyyntötila ja WooCommerce-asetukset. Kun tarjouspyyntötila on päällä, asiakkaat voivat pyytää tarjousta ostamisen sijaan.', 'vestelli-avalon' ) . '</p>';
}

function va_quote_mode_callback() {
  $value = get_option( 'va_quote_mode', '0' );
  ?>
  <?php va_render_settings_checkbox( 'va_quote_mode', $value, __( 'Ota tarjouspyyntötila käyttöön', 'vestelli-avalon' ), 'va_quote_mode' ); ?>
  <p class="description"><?php _e( 'Kun päällä, ostoskori toimii tarjouspyyntölomakkeena ilman maksua.', 'vestelli-avalon' ); ?></p>
  <?php
}

function va_hide_prices_callback() {
  $value = get_option( 'va_hide_prices', '0' );
  va_settings_open_field( 'va_hide_prices' );
  va_render_settings_checkbox( 'va_hide_prices', $value, __( 'Piilota kaikki hinnat sivustolta', 'vestelli-avalon' ) );
  va_settings_close_field();
}

function va_hide_catalog_ordering_callback() {
  $value = get_option( 'va_hide_catalog_ordering', '0' );
  ?>
  <?php va_render_settings_checkbox( 'va_hide_catalog_ordering', $value, __( 'Piilota tuotelistauksen lajitteluvalikko', 'vestelli-avalon' ) ); ?>
  <p class="description"><?php _e( 'Hyödyllinen kun hintoja ei vielä näytetä, jolloin esim. "halvin ensin" -lajittelu olisi turhaan listalla.', 'vestelli-avalon' ); ?></p>
  <?php
}

function va_quote_button_text_callback() {
  $value = get_option( 'va_quote_button_text', 'Pyydä tarjous' );
  va_settings_open_field( 'va_quote_button_text' );
  ?>
  <input type="text" name="va_quote_button_text" value="<?php echo esc_attr( $value ); ?>" class="regular-text" />
  <p class="description"><?php _e( 'Teksti joka näytetään "Lisää ostoskoriin" -painikkeen sijaan. WPML-käännettävä.', 'vestelli-avalon' ); ?></p>
  <?php
  va_settings_close_field();
}

function va_quote_email_callback() {
  $value = get_option( 'va_quote_email', get_option( 'admin_email' ) );
  va_settings_open_field( 'va_quote_email' );
  ?>
  <input type="email" name="va_quote_email" value="<?php echo esc_attr( $value ); ?>" class="regular-text" />
  <p class="description"><?php _e( 'Sähköpostiosoite johon tarjouspyynnöt lähetetään.', 'vestelli-avalon' ); ?></p>
  <?php
  va_settings_close_field();
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

  $tabs = array(
    'header'  => __( 'Header', 'vestelli-avalon' ),
    'brand'   => __( 'Brändi', 'vestelli-avalon' ),
    'social'  => __( 'Some', 'vestelli-avalon' ),
  );

  if ( class_exists( 'WooCommerce' ) ) {
    $tabs['woocommerce'] = __( 'WooCommerce', 'vestelli-avalon' );
  }

  $tabs['scripts'] = __( 'Scriptit', 'vestelli-avalon' );

  $header_fields = array(
    'va_themer_header_notice',
    array(
      'subheading' => __( 'Yleiset', 'vestelli-avalon' ),
      'group'      => 'header-general',
    ),
    'va_header_design',
    'va_enable_portfolio',
    'va_header_type',
    array(
      'subheading' => __( 'Beaver Builder header', 'vestelli-avalon' ),
      'group'      => 'beaver-header',
    ),
    'va_bb_header_template',
    array(
      'subheading' => __( 'Mukautettu header', 'vestelli-avalon' ),
      'group'      => 'custom-header',
    ),
    'va_logo',
    'va_cta_text',
    'va_cta_link',
    'va_show_social_icons',
    'va_show_search',
    'va_show_language_switcher',
    'va_show_cta',
    'va_show_cart',
    array(
      'subheading' => __( 'Responsiivisuus', 'vestelli-avalon' ),
      'group'      => 'header-responsive',
    ),
    'va_header_mobile_breakpoint',
    array(
      'subheading' => __( 'Läpinäkyvyys (Avalon)', 'vestelli-avalon' ),
      'group'      => 'avalon-opacity',
    ),
    'va_header_opacity',
  );
  ?>
  <div class="wrap va-settings-wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <nav class="nav-tab-wrapper">
      <?php foreach ( $tabs as $tab_id => $tab_label ) : ?>
        <a
          href="#va-tab-panel-<?php echo esc_attr( $tab_id ); ?>"
          class="nav-tab va-settings-nav-tab<?php echo 'header' === $tab_id ? ' nav-tab-active' : ''; ?>"
          data-va-tab="<?php echo esc_attr( $tab_id ); ?>"
        >
          <?php echo esc_html( $tab_label ); ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <form action="options.php" method="post">
      <?php
      settings_fields( 'va_settings' );
      va_settings_render_tab_section( 'va-settings', 'va_header_section', 'header', $header_fields );
      va_settings_render_tab_section( 'va-settings', 'va_colors_section', 'brand' );
      va_settings_render_tab_section( 'va-settings', 'va_social_section', 'social' );
      if ( class_exists( 'WooCommerce' ) ) {
        va_settings_render_tab_section( 'va-settings', 'va_woocommerce_section', 'woocommerce' );
      }
      va_settings_render_tab_section( 'va-settings', 'va_scripts_section', 'scripts' );
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
  if ( 'appearance_page_va-settings' !== $hook ) {
    return;
  }

  $theme_version = wp_get_theme()->get( 'Version' );
  $admin_css     = get_stylesheet_directory() . '/assets/css/theme-settings-admin.css';
  $admin_js      = get_stylesheet_directory() . '/assets/js/theme-settings-admin.js';

  wp_enqueue_media();
  wp_enqueue_style( 'wp-color-picker' );
  wp_enqueue_style(
    'va-theme-settings-admin',
    get_stylesheet_directory_uri() . '/assets/css/theme-settings-admin.css',
    array(),
    file_exists( $admin_css ) ? filemtime( $admin_css ) : $theme_version
  );
  wp_enqueue_script( 'wp-color-picker' );
  wp_enqueue_script(
    'va-theme-settings-admin',
    get_stylesheet_directory_uri() . '/assets/js/theme-settings-admin.js',
    array( 'jquery' ),
    file_exists( $admin_js ) ? filemtime( $admin_js ) : $theme_version,
    true
  );
  wp_localize_script(
    'va-theme-settings-admin',
    'vaThemeSettings',
    array(
      'themerHeaderActive' => va_settings_themer_header_active(),
      'headerType'         => get_option( 'va_header_type', 'custom' ),
      'headerDesign'       => get_option( 'va_header_design', 'avalon' ),
      'woocommerceActive'  => class_exists( 'WooCommerce' ),
      'quoteMode'          => get_option( 'va_quote_mode', '0' ),
    )
  );
  wp_add_inline_script( 'wp-color-picker', "
    jQuery(document).ready(function($) {
      $('.va-color-picker').wpColorPicker();
    });
  " );
});
