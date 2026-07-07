<?php
/**
 * Frontend rendering for Yhteystiedot-moduulille.
 *
 * @package Vestelli_Avalon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'get_field' ) ) {
	echo '<div class="vy-empty">' . esc_html__( 'ACF-laajennus ei ole käytössä.', 'vestelli-avalon' ) . '</div>';
	return;
}

$field_name     = ! empty( $settings->field_name ) ? sanitize_key( $settings->field_name ) : 'henkilokunta';
$source_id_raw  = isset( $settings->source_post_id ) ? trim( (string) $settings->source_post_id ) : '';
$source_id      = $source_id_raw !== '' && is_numeric( $source_id_raw ) ? intval( $source_id_raw ) : get_the_ID();
$columns        = isset( $settings->columns ) ? max( 2, min( 4, intval( $settings->columns ) ) ) : 4;
$show_titles    = ! isset( $settings->show_section_titles ) || 'yes' === $settings->show_section_titles;
$photo_style    = isset( $settings->photo_style ) ? $settings->photo_style : 'grayscale';

$rows = $source_id ? get_field( $field_name, $source_id ) : array();

if ( empty( $rows ) || ! is_array( $rows ) ) {
	echo '<div class="vy-empty">' . esc_html__( 'Ei yhteystietoja lisätty.', 'vestelli-avalon' ) . '</div>';
	return;
}

$labels = vestelli_yhteystiedot_osasto_labels();

// Group by osasto, preserving repeater order within each group.
$groups = array();
foreach ( $rows as $row ) {
	if ( ! is_array( $row ) ) {
		continue;
	}
	$osasto = isset( $row['osasto'] ) ? (string) $row['osasto'] : '';
	$key    = $osasto !== '' ? $osasto : '_muut';
	if ( ! isset( $groups[ $key ] ) ) {
		$groups[ $key ] = array();
	}
	$groups[ $key ][] = $row;
}

// Sort groups by key (ACF choices use numeric prefix `01_`, `02_`, ... so this orders them).
ksort( $groups, SORT_NATURAL );

$photo_class = 'vy-card__photo' . ( 'grayscale' === $photo_style ? ' vy-card__photo--grayscale' : '' );
?>

<div class="vy-yhteystiedot vy-cols-<?php echo esc_attr( $columns ); ?>">
	<?php foreach ( $groups as $osasto_key => $items ) : ?>
		<?php
		$label = isset( $labels[ $osasto_key ] ) ? $labels[ $osasto_key ] : ( '_muut' === $osasto_key ? __( 'Muut', 'vestelli-avalon' ) : $osasto_key );
		?>
		<section class="vy-section">
			<?php if ( $show_titles ) : ?>
				<h2 class="vy-section__title"><?php echo esc_html( $label ); ?></h2>
			<?php endif; ?>

			<div class="vy-grid">
				<?php foreach ( $items as $item ) : ?>
					<?php
					$kuva    = isset( $item['kuva'] ) ? $item['kuva'] : '';
					$nimi    = isset( $item['nimi'] ) ? trim( (string) $item['nimi'] ) : '';
					$titteli = isset( $item['titteli'] ) ? trim( (string) $item['titteli'] ) : '';
					$puh     = isset( $item['puhelinnumero'] ) ? trim( (string) $item['puhelinnumero'] ) : '';
					$email   = isset( $item['sahkoposti'] ) ? trim( (string) $item['sahkoposti'] ) : '';

					$kuva_url = '';
					$kuva_alt = $nimi;
					if ( is_array( $kuva ) ) {
						$kuva_url = isset( $kuva['url'] ) ? $kuva['url'] : '';
						if ( ! empty( $kuva['alt'] ) ) {
							$kuva_alt = $kuva['alt'];
						}
					} elseif ( is_numeric( $kuva ) ) {
						$kuva_url = wp_get_attachment_image_url( intval( $kuva ), 'medium' );
					} elseif ( is_string( $kuva ) ) {
						$kuva_url = $kuva;
					}

					// Build tel: link by stripping non-numeric chars, preserving leading +.
					$tel_href = '';
					if ( $puh !== '' ) {
						$cleaned  = preg_replace( '/[^0-9+]/', '', $puh );
						$tel_href = $cleaned !== '' ? 'tel:' . $cleaned : '';
					}
					?>
					<article class="vy-card">
						<?php if ( $kuva_url ) : ?>
							<div class="<?php echo esc_attr( $photo_class ); ?>">
								<img src="<?php echo esc_url( $kuva_url ); ?>" alt="<?php echo esc_attr( $kuva_alt ); ?>" loading="lazy" />
							</div>
						<?php else : ?>
							<div class="<?php echo esc_attr( $photo_class ); ?> vy-card__photo--placeholder" aria-hidden="true"></div>
						<?php endif; ?>

						<?php if ( $nimi !== '' ) : ?>
							<h3 class="vy-card__name"><?php echo esc_html( $nimi ); ?></h3>
						<?php endif; ?>

						<?php if ( $titteli !== '' ) : ?>
							<p class="vy-card__title"><?php echo esc_html( $titteli ); ?></p>
						<?php endif; ?>

						<?php if ( $puh !== '' ) : ?>
							<p class="vy-card__phone">
								<?php if ( $tel_href !== '' ) : ?>
									<a href="<?php echo esc_attr( $tel_href ); ?>"><?php echo esc_html( $puh ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $puh ); ?>
								<?php endif; ?>
							</p>
						<?php endif; ?>

						<?php if ( $email !== '' ) : ?>
							<p class="vy-card__email">
								<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
							</p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endforeach; ?>
</div>
