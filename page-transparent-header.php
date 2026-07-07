<?php
/**
 * Template Name: Transparent Header
 * Template Post Type: page
 *
 * This template displays pages with a transparent header that overlays content.
 * Supports Gutenberg blocks including Hero block.
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
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php
			// Check if page uses Gutenberg editor
			if ( has_blocks( get_the_content() ) ) {
				// Display Gutenberg content (including Hero block)
				// Hero block will be full-width automatically if aligned full
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
