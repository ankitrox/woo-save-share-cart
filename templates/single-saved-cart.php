<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">

	<thead>
		<tr>
			<th class="product-name" colspan="2"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
			<th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
			<th class="product-subtotal"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php foreach( $cart as $cart_item_key => $cart_item ){

				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );?>
					
					<tr class="woocommerce-cart-form__cart-item">

						<td class="product-thumbnail">
						<?php
						$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail'), $cart_item, $cart_item_key );

						if ( ! $product_permalink ) {
							echo wp_kses_post( $thumbnail );
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) );
						}
						?>

						</td>

						<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
						<?php
						if ( ! $product_permalink ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
						} else {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
						}

						do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

						// Meta data.
						echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>' ) );
						}
						?>
						</td>

						<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
						</td>

						<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
						<?php
						if ( $_product->is_sold_individually() ) {
							$product_quantity = 1;
						} else {
							$product_quantity = $cart_item['quantity'];
						}

						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
						?>
						</td>

						<td class="product-subtotal" data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
						</td>

					</tr><?php
				}
		} ?>
	</tbody>
</table>

<?php global $wcssc_saved_cart, $wcssc; ?>

<div class="wcssc-share-load">
	
	<div class="wcssc-share">

		<ul id="wcssc-saved-cart-share">
			
			<?php $cart_link = get_permalink(); ?>

			<?php if( !empty($wcssc->configuration['wcssc_sm_fb']) && 'yes' == $wcssc->configuration['wcssc_sm_fb'] ){?>
				<li class="wcssc-icon-holder">
					<a target="_blank" href="<?php echo wcssc_social_media_url( 'facebook', $cart_link ); ?>">
						<i class="fa fa-facebook"></i>
					</a>
				</li>
			<?php } ?>

			<?php if( !empty($wcssc->configuration['wcssc_sm_tw']) && 'yes' == $wcssc->configuration['wcssc_sm_tw'] ){?>
				<li class="wcssc-icon-holder">
					<a target="_blank" href="<?php echo wcssc_social_media_url( 'twitter', $cart_link ); ?>">
						<i class="fa fa-twitter"></i>
					</a>
				</li><?php
			} ?>

			<?php if( !empty($wcssc->configuration['wcssc_sm_wp']) && 'yes' == $wcssc->configuration['wcssc_sm_wp'] ){?>
				<li class="wcssc-icon-holder">
					<a target="_blank" href="<?php echo wcssc_social_media_url( 'whatsapp', $cart_link ); ?>">
						<i class="fa fa-whatsapp"></i>
					</a>
				</li><?php
			} ?>

			<?php if( !empty($wcssc->configuration['wcssc_sm_skype']) && 'yes' == $wcssc->configuration['wcssc_sm_skype'] ){?>
				<li class="wcssc-icon-holder">
					<a target="_blank" href="<?php echo wcssc_social_media_url( 'skype', $cart_link ); ?>">
						<i class="fa fa-skype"></i>
					</a>
				</li><?php
			} ?>
			
		</ul>
		
	</div>
	
	<div class="wcssc-load">
		<a class="button btn" href="<?php echo $wcssc_saved_cart->get_load_cart_link( get_the_ID() ); ?>"><?php _e('Load This Cart', 'wcssc') ?></a>
	</div>

</div>