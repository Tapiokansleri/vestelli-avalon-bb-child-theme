<?php
/**
 * Shop Now and Back Button Module
 *
 * Product page buttons: Add to cart / Ask for quote + Back to category
 *
 * @package Vestelli_Avalon
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class AvalonButton extends FLBuilderModule {

  protected static $modals = array();

  public function __construct() {
    parent::__construct( array(
      'name'            => __( 'Shop now and back -button', 'vestelli-avalon' ),
      'description'     => __( 'Add to cart / quote request button + back to category button for product pages', 'vestelli-avalon' ),
      'category'        => __( 'Vestelli', 'vestelli-avalon' ),
      'dir'             => VESTELLI_MODULES . '/avalon-button/',
      'url'             => VESTELLI_MODULES_URL . '/avalon-button/',
      'icon'            => 'button.svg',
      'editor_export'   => true,
      'enabled'         => true,
      'partial_refresh' => false,
    ) );
  }

  public function render( $settings ) {
    $module = $this;
    include $this->dir . 'includes/frontend.php';
  }

  public function enqueue_styles() {
    $this->add_css( 'frontend', $this->url . 'css/frontend.css' );
  }

  public static function register_modal( $id, $html ) {
    self::$modals[ $id ] = $html;
  }

  public static function render_footer_modals() {
    if ( empty( self::$modals ) ) {
      return;
    }
    foreach ( self::$modals as $modal_id => $modal_html ) {
      ?>
      <div id="<?php echo esc_attr( $modal_id ); ?>" class="avalon-modal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="avalon-modal-backdrop" data-modal-close></div>
        <div class="avalon-modal-panel">
          <button type="button" class="avalon-modal-close" aria-label="<?php esc_attr_e( 'Sulje', 'vestelli-avalon' ); ?>" data-modal-close>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M18 6L6 18M6 6l12 12"></path>
            </svg>
          </button>
          <div class="avalon-modal-content">
            <?php echo $modal_html; // Trusted admin-supplied HTML from BB module setting. ?>
          </div>
        </div>
      </div>
      <?php
    }
    ?>
    <script>
    (function(){
      if (window.__avalonModalInit) return;
      window.__avalonModalInit = true;
      function openModal(m) {
        m.classList.add('is-open');
        m.setAttribute('aria-hidden', 'false');
        document.body.classList.add('avalon-modal-open');
      }
      function closeModal(m) {
        m.classList.remove('is-open');
        m.setAttribute('aria-hidden', 'true');
        if (!document.querySelector('.avalon-modal.is-open')) {
          document.body.classList.remove('avalon-modal-open');
        }
      }
      document.addEventListener('click', function(e) {
        var t = e.target;
        if (!t || !t.closest) return;
        var trigger = t.closest('[data-modal-target]');
        if (trigger) {
          e.preventDefault();
          e.stopPropagation();
          var m = document.getElementById(trigger.getAttribute('data-modal-target'));
          if (m) openModal(m);
          return;
        }
        var closer = t.closest('[data-modal-close]');
        if (closer) {
          e.preventDefault();
          e.stopPropagation();
          var m = closer.closest('.avalon-modal');
          if (m) closeModal(m);
        }
      }, true);
      document.addEventListener('keydown', function(e){
        if (e.key !== 'Escape') return;
        document.querySelectorAll('.avalon-modal.is-open').forEach(closeModal);
      });
    })();
    </script>
    <?php
  }
}

add_action( 'wp_footer', array( 'AvalonButton', 'render_footer_modals' ), 99 );

/**
 * Register the module
 */
FLBuilder::register_module( 'AvalonButton', array(
  'general' => array(
    'title'    => __( 'Yleiset', 'vestelli-avalon' ),
    'sections' => array(
      'layout' => array(
        'title'  => __( 'Asettelu', 'vestelli-avalon' ),
        'fields' => array(
          'alignment' => array(
            'type'    => 'select',
            'label'   => __( 'Tasaus', 'vestelli-avalon' ),
            'default' => 'left',
            'options' => array(
              'left'   => __( 'Vasen', 'vestelli-avalon' ),
              'center' => __( 'Keskitetty', 'vestelli-avalon' ),
              'right'  => __( 'Oikea', 'vestelli-avalon' ),
            ),
          ),
        ),
      ),
      'shop_button' => array(
        'title'  => __( 'Osta / Tarjouspyyntö -painike', 'vestelli-avalon' ),
        'fields' => array(
          'shop_action' => array(
            'type'    => 'select',
            'label'   => __( 'Painikkeen toiminta', 'vestelli-avalon' ),
            'default' => 'add_to_cart',
            'options' => array(
              'add_to_cart' => __( 'Lisää ostoskoriin', 'vestelli-avalon' ),
              'modal'       => __( 'Avaa modal (HTML)', 'vestelli-avalon' ),
            ),
            'toggle'  => array(
              'modal' => array(
                'fields' => array( 'modal_content' ),
              ),
            ),
          ),
          'modal_content' => array(
            'type'    => 'textarea',
            'label'   => __( 'Modalin HTML-sisältö', 'vestelli-avalon' ),
            'rows'    => 10,
            'default' => '',
            'help'    => __( 'Modaliin upotettava HTML. Voit liittää esim. lomakkeen, iframen tai vapaamuotoista sisältöä.', 'vestelli-avalon' ),
          ),
          'shop_label' => array(
            'type'    => 'text',
            'label'   => __( 'Painikkeen teksti (normaali)', 'vestelli-avalon' ),
            'default' => 'Lisää ostoskoriin',
            'help'    => __( 'Näytetään kun tarjouspyyntötila EI ole päällä.', 'vestelli-avalon' ),
          ),
          'quote_label' => array(
            'type'    => 'text',
            'label'   => __( 'Painikkeen teksti (tarjouspyyntö)', 'vestelli-avalon' ),
            'default' => 'Pyydä tarjous',
            'help'    => __( 'Näytetään kun tarjouspyyntötila ON päällä. Jos tyhjä, käytetään teeman asetuksista.', 'vestelli-avalon' ),
          ),
          'shop_style' => array(
            'type'    => 'select',
            'label'   => __( 'Painikkeen tyyli', 'vestelli-avalon' ),
            'default' => 'default',
            'options' => array(
              'default'       => __( 'Tummansininen', 'vestelli-avalon' ),
              'light-blue'    => __( 'Vaaleansininen', 'vestelli-avalon' ),
              'bordered-blue' => __( 'Sininen reunus', 'vestelli-avalon' ),
            ),
          ),
        ),
      ),
      'back_button' => array(
        'title'  => __( 'Takaisin-painike', 'vestelli-avalon' ),
        'fields' => array(
          'show_back_button' => array(
            'type'    => 'select',
            'label'   => __( 'Näytä takaisin-painike', 'vestelli-avalon' ),
            'default' => 'yes',
            'options' => array(
              'yes' => __( 'Kyllä', 'vestelli-avalon' ),
              'no'  => __( 'Ei', 'vestelli-avalon' ),
            ),
            'toggle'  => array(
              'yes' => array(
                'fields' => array( 'back_label', 'back_style' ),
              ),
            ),
          ),
          'back_label' => array(
            'type'    => 'text',
            'label'   => __( 'Painikkeen teksti', 'vestelli-avalon' ),
            'default' => 'Takaisin tuoteryhmän tuotteisiin',
          ),
          'back_style' => array(
            'type'    => 'select',
            'label'   => __( 'Painikkeen tyyli', 'vestelli-avalon' ),
            'default' => 'light-blue',
            'options' => array(
              'default'       => __( 'Tummansininen', 'vestelli-avalon' ),
              'light-blue'    => __( 'Vaaleansininen', 'vestelli-avalon' ),
              'bordered-blue' => __( 'Sininen reunus', 'vestelli-avalon' ),
            ),
          ),
        ),
      ),
    ),
  ),
) );
