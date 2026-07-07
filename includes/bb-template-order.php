<?php
/**
 * Beaver Builder Template list-table: editable Order column
 *
 * Adds an inline-editable "Järjestys" column on the wp-admin list view of
 * fl-builder-template (saved rows/columns/templates). Default sort is by
 * menu_order ASC, then title.
 *
 * @package Vestelli_Avalon
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

add_filter( 'manage_fl-builder-template_posts_columns', function( $cols ) {
  $new = array();
  foreach ( $cols as $key => $label ) {
    $new[ $key ] = $label;
    if ( $key === 'title' ) {
      $new['va_menu_order'] = __( 'Järjestys', 'vestelli-avalon' );
    }
  }
  if ( ! isset( $new['va_menu_order'] ) ) {
    $new['va_menu_order'] = __( 'Järjestys', 'vestelli-avalon' );
  }
  return $new;
} );

add_action( 'manage_fl-builder-template_posts_custom_column', function( $col, $post_id ) {
  if ( $col !== 'va_menu_order' ) {
    return;
  }
  $order = (int) get_post_field( 'menu_order', $post_id );
  printf(
    '<input type="number" class="va-bb-order" data-id="%d" value="%d" step="1" style="width:70px;" />',
    (int) $post_id,
    $order
  );
}, 10, 2 );

add_filter( 'manage_edit-fl-builder-template_sortable_columns', function( $cols ) {
  $cols['va_menu_order'] = 'menu_order';
  return $cols;
} );

add_action( 'pre_get_posts', function( $q ) {
  if ( ! is_admin() || ! $q->is_main_query() ) {
    return;
  }
  if ( $q->get( 'post_type' ) !== 'fl-builder-template' ) {
    return;
  }
  if ( ! $q->get( 'orderby' ) ) {
    $q->set( 'orderby', 'menu_order title' );
    $q->set( 'order', 'ASC' );
  }
} );

add_action( 'admin_footer-edit.php', function() {
  $screen = get_current_screen();
  if ( ! $screen || $screen->post_type !== 'fl-builder-template' ) {
    return;
  }
  $nonce = wp_create_nonce( 'va_save_bb_order' );
  ?>
  <script>
  (function($){
    var nonce = <?php echo wp_json_encode( $nonce ); ?>;
    $(document).on('change', '.va-bb-order', function(){
      var $i = $(this);
      var id = parseInt($i.data('id'), 10);
      var val = parseInt($i.val(), 10);
      if (isNaN(val)) val = 0;
      var orig = $i.css('background-color');
      $i.prop('disabled', true);
      $.post(ajaxurl, {
        action: 'va_save_bb_order',
        post_id: id,
        menu_order: val,
        _ajax_nonce: nonce
      }).done(function(res){
        if (res && res.success) {
          $i.css('background-color', '#e6ffed');
        } else {
          $i.css('background-color', '#ffe6e6');
        }
      }).fail(function(){
        $i.css('background-color', '#ffe6e6');
      }).always(function(){
        $i.prop('disabled', false);
        setTimeout(function(){ $i.css('background-color', orig); }, 800);
      });
    });
  })(jQuery);
  </script>
  <?php
} );

add_action( 'wp_ajax_va_save_bb_order', function() {
  check_ajax_referer( 'va_save_bb_order' );
  $post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
  $order   = isset( $_POST['menu_order'] ) ? (int) $_POST['menu_order'] : 0;
  if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
    wp_send_json_error();
  }
  $result = wp_update_post( array(
    'ID'         => $post_id,
    'menu_order' => $order,
  ), true );
  if ( is_wp_error( $result ) ) {
    wp_send_json_error( $result->get_error_message() );
  }
  wp_send_json_success( array( 'menu_order' => $order ) );
} );
