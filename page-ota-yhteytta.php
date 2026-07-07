<?php
/**
 * Template Name: Ota yhteyttä
 * Template Post Type: page
 *
 * Sivupohja yhteydenottosivulle. Tukee Gutenberg-lohkoja (mm. Hero) ja
 * klassista editoria fallback-näkymänä.
 *
 * @package Avalon_Nordic
 */

get_header();
?>

<?php
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-ota-yhteytta' ); ?>>
			<?php
			// Check if page uses Gutenberg editor
			if ( has_blocks( get_the_content() ) ) {
				// Display Gutenberg content (including Hero block)
				the_content();
			} else {
				// Fallback for classic editor
				?>
				<div class="fl-content-full container">
					<div class="row">
						<div class="fl-content col-md-12">
							<header class="entry-header">
								<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
							</header>

							<div class="entry-content">
								<?php
								the_content();

								wp_link_pages( array(
									'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'avalon-nordic' ),
									'after'  => '</div>',
								) );
								?>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</article>
		<?php
	endwhile;
endif;
?>

<?php
get_footer();
?>
