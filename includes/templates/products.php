<?php
/**
 * Template for products.
 *
 * @package Lvfw
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || die( 'Cheatin&#8217; uh?' );

$current_product_id = get_the_ID();

if ( $products ) : ?>

	<div class="woo-linked-variation-wrap">
		<div class="woo-linked-variation">

			<div class="linked-variations">
				<ul class="linked-variations-buttons">
					<?php foreach ( $products as $product_id ) : ?>
						<li class="linked-variations-item show-image">

							<?php
							if ( $current_product_id !== (int) $product_id ) :
								$image_url = has_post_thumbnail( $product_id ) ? get_the_post_thumbnail_url( $product_id, 'thumbnail' ) : wc_placeholder_img_src( 'thumbnail' );
								?>
								<a
								href="<?php echo get_the_permalink( $product_id ); ?>"
								title="<?php echo get_the_title( $product_id ); ?>"
								class="variation-item"
								>
									<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo get_the_title( $product_id ); ?>">
								</a>
							<?php elseif ( $current_product_id === (int) $product_id ) : ?>
								<span class="variation-item active-variation" title="<?php echo get_the_title( $product_id ); ?>">
									<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo get_the_title( $product_id ); ?>">
								</span>
							<?php endif; ?>

						</li>
					<?php endforeach; ?>
				</ul>
			</div>

		</div>
	</div>

	<?php
endif;
