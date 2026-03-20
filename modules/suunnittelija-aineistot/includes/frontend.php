<?php
/**
 * Frontend rendering for Suunnittelija-aineistot module
 *
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $settings->material_sections ) ) {
	return;
}

?>
<div class="sa-aineistot-wrap">
	<?php foreach ( $settings->material_sections as $section ) : ?>
		<?php
		$heading = ! empty( $section->heading ) ? $section->heading : '';
		$text    = ! empty( $section->text_content ) ? $section->text_content : '';
		$files   = ! empty( $section->material_files ) ? $section->material_files : array();

		if ( empty( $heading ) && empty( $files ) ) {
			continue;
		}
		?>
		<div class="sa-product-section">
			<?php if ( ! empty( $heading ) ) : ?>
				<h3 class="sa-product-title"><?php echo esc_html( $heading ); ?></h3>
			<?php endif; ?>

			<?php if ( ! empty( $text ) ) : ?>
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
						$name     = ! empty( $file->file_name ) ? $file->file_name : '';
						$pdf_link = ! empty( $file->pdf_link ) ? $file->pdf_link : '';
						$dwg_link = ! empty( $file->dwg_link ) ? $file->dwg_link : '';

						if ( empty( $name ) ) {
							continue;
						}
						?>
						<div class="sa-files-row">
							<span class="sa-file-name-col"><?php echo esc_html( $name ); ?></span>
							<span class="sa-file-icon-col">
								<?php if ( ! empty( $pdf_link ) ) : ?>
									<a href="<?php echo esc_url( $pdf_link ); ?>" target="_blank" rel="noopener" title="<?php echo esc_attr( $name ); ?> (PDF)">
										<svg class="sa-download-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 16l-5-5h3V4h4v7h3l-5 5zm-7 2h14v2H5v-2z"/></svg>
									</a>
								<?php endif; ?>
							</span>
							<span class="sa-file-icon-col">
								<?php if ( ! empty( $dwg_link ) ) : ?>
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
