<?php

/**
 * @file
 * Contains \Netzstrategen\Gallerya\WooCommerce.
 */

namespace Netzstrategen\Gallerya;

/**
 * WooCommerce integration.
 */
class WooCommerce {

  /**
   * Adds data-srcset and data-sizes attributes to the wrapper to make images reponsive in lightGallery.
   *
   * Also adds data-sizes attributes to the image wrapper, so images are not
   * enlarged more than the original size ('full').
   *
   * @see https://sachinchoolur.github.io/lightGallery/demos/responsive.html
   *
   * @implements woocommerce_single_product_image_thumbnail_html
   */
  public static function woocommerce_single_product_image_thumbnail_html($html, $thumbnail_id) {
    $srcset = wp_get_attachment_image_srcset($thumbnail_id, 'shop_single');
    $srcsizes = wp_get_attachment_image_sizes($thumbnail_id, 'full');
    return preg_replace('/(<a\s+)/i', '<a data-srcset="' . $srcset . '" data-sizes="' . $srcsizes . '" ', $html);
  }

  /**
   * Adds thumbnail slider with variation images to products on listing pages.
   *
   * @implements woocommerce_template_loop_product_thumbnail
   */
  public static function woocommerce_template_loop_product_thumbnail() {
    global $product;

    if ($product->is_type('variable')) {

      // TODO: Make sure the Sale labels etc are still working.

      // TODO: Set pageDots=false in JS

      $attachment_ids = [];

      // Get the main product image.
      $attachment_ids[] = $product->get_image_id();

      // Get the first image of each product variation.
      $variations = $product->get_available_variations();
      foreach ($variations as $variation) {
        $attachment_ids[] = $variation['image_id'];
      }


      if (count($attachment_ids) > 1) {
        // TODO: Remove wrapping product link if we have multiple images.

        // Needs to be done through removing and re-adding hooks in Plugin.php:
        // woocommerce_template_loop_product_link_open() needs to be removed from woocommerce_before_shop_loop_item
        // and re-added to woocommerce_bevore_shop_look_item_title with low priority like 20

      }

      $args['post_type'] = 'attachment';
      $args['include'] = $attachment_ids;
      $args['orderby'] = 'post__in';

      Plugin::renderTemplate(['templates/layout-product-variation-slider.php'], [
        'images' => get_posts($args),
      ]);

    }
    else {
      // This isn't a variable product, output default thumbnail markup.
      echo woocommerce_get_product_thumbnail();
    }
  }

}
