<?php
/**
 * Suunnittelija-aineistot block render.
 *
 * @package Vestelli
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_array( $attributes ) ) {
	$attributes = array();
}

$sections = isset( $attributes['sections'] ) && is_array( $attributes['sections'] ) ? $attributes['sections'] : array();

$wrapper_class = 'sa-aineistot-wrap';

if ( function_exists( 'get_block_wrapper_attributes' ) ) {
	try {
		$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $wrapper_class ) );
	} catch ( Exception $e ) {
		$wrapper_attributes = 'class="' . esc_attr( $wrapper_class ) . '"';
	}
} else {
	$wrapper_attributes = 'class="' . esc_attr( $wrapper_class ) . '"';
}

if ( empty( $sections ) ) {
	return;
}
?>
<div <?php echo $wrapper_attributes; ?>>
	<?php foreach ( $sections as $section ) : ?>
		<?php
		$heading = isset( $section['heading'] ) ? (string) $section['heading'] : '';
		$text    = isset( $section['text'] ) ? (string) $section['text'] : '';
		$files   = isset( $section['files'] ) && is_array( $section['files'] ) ? $section['files'] : array();

		if ( '' === $heading && empty( $files ) ) {
			continue;
		}
		?>
		<div class="sa-product-section">
			<?php if ( '' !== $heading ) : ?>
				<h3 class="sa-product-title"><?php echo esc_html( $heading ); ?></h3>
			<?php endif; ?>

			<?php if ( '' !== $text ) : ?>
				<div class="sa-product-text"><?php echo wp_kses_post( nl2br( $text ) ); ?></div>
			<?php endif; ?>

			<?php if ( ! empty( $files ) ) : ?>
				<div class="sa-files-table">
					<div class="sa-files-header">
						<span class="sa-file-name-col"></span>
						<span class="sa-file-icon-col">PDF</span>
						<span class="sa-file-icon-col">DWG</span>
					</div>

					<?php foreach ( $files as $file ) : ?>
						<?php
						$name     = isset( $file['name'] ) ? (string) $file['name'] : '';
						$pdf_link = isset( $file['pdfUrl'] ) ? (string) $file['pdfUrl'] : '';
						$dwg_link = isset( $file['dwgUrl'] ) ? (string) $file['dwgUrl'] : '';

						if ( '' === $name ) {
							continue;
						}
						?>
						<div class="sa-files-row">
							<span class="sa-file-name-col"><?php echo esc_html( $name ); ?></span>
							<span class="sa-file-icon-col">
								<?php if ( '' !== $pdf_link ) : ?>
									<a href="<?php echo esc_url( $pdf_link ); ?>" target="_blank" rel="noopener" title="<?php echo esc_attr( $name ); ?> (PDF)">
										<svg class="sa-download-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 16l-5-5h3V4h4v7h3l-5 5zm-7 2h14v2H5v-2z"/></svg>
									</a>
								<?php endif; ?>
							</span>
							<span class="sa-file-icon-col">
								<?php if ( '' !== $dwg_link ) : ?>
									<a href="<?php echo esc_url( $dwg_link ); ?>" target="_blank" rel="noopener" title="<?php echo esc_attr( $name ); ?> (DWG)">
										<svg class="sa-download-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 16l-5-5h3V4h4v7h3l-5 5zm-7 2h14v2H5v-2z"/></svg>
									</a>
								<?php endif; ?>
							</span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
