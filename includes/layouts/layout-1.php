<?php defined('ABSPATH') || die('Cheatin&#8217; uh?'); // Cannot access pages directly. 
?>

<div class="woo-linked-variation-wrap">

    <?php foreach ($variations as $variation) :
        $current_product_id = get_the_ID();
        $current_variation_name = $this->get_variation_data(get_the_ID(), $variation['slug']); ?>
        <div class="woo-linked-variation">
            <div class="linked-variation-label">
                <strong class="variation-label"><?php echo sprintf('%1$s:', $variation['name']); ?></strong>
                <span class="variation-selection" data-variant="<?php echo esc_attr($current_variation_name); ?>"><?php echo esc_html($current_variation_name); ?></span>
            </div>

            <?php if ($variation['products']) : ?>
                <div class="linked-variations">
                    <ul class="linked-variations-buttons">
                        <?php foreach ($variation['products'] as $product_id) :
                            $variation_name = $this->get_variation_data($product_id, $variation['slug']);
                            $variation_class = $variation['show_image'] ? 'linked-variations-item show-image' : 'linked-variations-item'; ?>
                            <li class="<?php echo esc_attr($variation_class); ?>" data-variant="<?php echo esc_attr($variation_name); ?>">

                                <?php if ($variation['show_image'] && ($current_product_id !== $product_id)) : ?>
                                    <a href="<?php echo get_the_permalink($product_id); ?>" title="<?php echo get_the_title($product_id); ?>" class="variation-item">
                                        <img src="<?php echo has_post_thumbnail($product_id) ? get_the_post_thumbnail_url($product_id, 'thumbnail') : wc_placeholder_img_src('thumbnail'); ?>" alt="<?php echo esc_attr($variation_name); ?>">
                                    </a>
                                <?php elseif ($variation['show_image'] && ($current_product_id === $product_id)) : ?>
                                    <span class="variation-item active-variation" title="<?php echo get_the_title($product_id); ?>">
                                        <img src="<?php echo has_post_thumbnail($product_id) ? get_the_post_thumbnail_url($product_id, 'thumbnail') : wc_placeholder_img_src('thumbnail'); ?>" alt="<?php echo esc_attr($variation_name); ?>">
                                    </span>
                                <?php elseif (!$variation['show_image'] && ($current_product_id !== $product_id)) : ?>
                                    <a href="<?php echo get_the_permalink($product_id); ?>" title="<?php echo get_the_title($product_id); ?>" class="variation-item"><?php echo esc_html($variation_name); ?></a>
                                <?php else : ?>
                                    <span class="variation-item active-variation" title="<?php echo get_the_title($product_id); ?>"><?php echo esc_attr($variation_name); ?></span>
                                <?php endif; ?>

                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>

</div>