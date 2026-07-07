<?php
/**
 * Hero Split Block Render Template
 *
 * @package Vestelli
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    InnerBlocks rendered output.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_array( $attributes ) ) {
	$attributes = array();
}

$bg_url         = ! empty( $attributes['backgroundImageUrl'] ) ? esc_url( $attributes['backgroundImageUrl'] ) : '';
$overlay_color  = ! empty( $attributes['overlayColor'] ) ? $attributes['overlayColor'] : '#000000';
$overlay_op     = isset( $attributes['overlayOpacity'] ) && is_numeric( $attributes['overlayOpacity'] ) ? floatval( $attributes['overlayOpacity'] ) : 0.4;
$text_color     = ! empty( $attributes['textColor'] ) ? $attributes['textColor'] : '#ffffff';
$min_height     = ! empty( $attributes['minHeight'] ) ? $attributes['minHeight'] : '70vh';
$vertical_align = ! empty( $attributes['verticalAlign'] ) ? $attributes['verticalAlign'] : 'center';
$left_width     = isset( $attributes['leftWidth'] ) && is_numeric( $attributes['leftWidth'] ) ? max( 10, min( 90, (int) $attributes['leftWidth'] ) ) : 50;
$column_gap     = ! empty( $attributes['columnGap'] ) ? $attributes['columnGap'] : '48px';
$stack_mobile   = isset( $attributes['stackOnMobile'] ) ? (bool) $attributes['stackOnMobile'] : true;

if ( ! function_exists( 'va_hex_to_rgba' ) && function_exists( 'avalon_hex_to_rgba' ) ) {
	$overlay_rgba = avalon_hex_to_rgba( $overlay_color, $overlay_op );
} elseif ( function_exists( 'va_hex_to_rgba' ) ) {
	$overlay_rgba = va_hex_to_rgba( $overlay_color, $overlay_op );
} else {
	$hex = ltrim( $overlay_color, '#' );
	if ( strlen( $hex ) !== 6 ) { $hex = '000000'; }
	$overlay_rgba = sprintf(
		'rgba(%d, %d, %d, %s)',
		hexdec( substr( $hex, 0, 2 ) ),
		hexdec( substr( $hex, 2, 2 ) ),
		hexdec( substr( $hex, 4, 2 ) ),
		max( 0, min( 1, $overlay_op ) )
	);
}

$align_map = array(
	'top'    => 'flex-start',
	'center' => 'center',
	'bottom' => 'flex-end',
);
$align_css = isset( $align_map[ $vertical_align ] ) ? $align_map[ $vertical_align ] : 'center';

$wrapper_classes = array( 'va-hero-split' );
if ( $stack_mobile ) {
	$wrapper_classes[] = 'va-hero-split--stack-mobile';
}

$wrapper_style = sprintf(
	'min-height: %s; color: %s; --va-hero-left: %d%%; --va-hero-right: %d%%; --va-hero-gap: %s; --va-hero-align: %s;',
	esc_attr( $min_height ),
	esc_attr( $text_color ),
	$left_width,
	100 - $left_width,
	esc_attr( $column_gap ),
	esc_attr( $align_css )
);

$wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
	? get_block_wrapper_attributes( array(
		'class' => implode( ' ', $wrapper_classes ),
		'style' => $wrapper_style,
	) )
	: 'class="' . esc_attr( implode( ' ', $wrapper_classes ) ) . '" style="' . esc_attr( $wrapper_style ) . '"';
?>
<div <?php echo $wrapper_attributes; ?>>
	<?php if ( ! empty( $bg_url ) ) : ?>
		<div class="va-hero-split__bg" style="background-image: url('<?php echo esc_url( $bg_url ); ?>');"></div>
	<?php endif; ?>
	<div class="va-hero-split__overlay" style="background-color: <?php echo esc_attr( $overlay_rgba ); ?>;"></div>
	<div class="va-hero-split__content">
		<?php echo $content; ?>
	</div>
</div>
