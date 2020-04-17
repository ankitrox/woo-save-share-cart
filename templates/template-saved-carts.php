<?php
$saved_carts = new WP_Query( array(
	'post_type'   => 'wcssc-cart',
	'author'      => get_current_user_id(),
	'post_status' => 'publish'
) );

if ( $saved_carts->have_posts() ) {

	$count = 1;

	do_action( 'wcssc_show_message' );
	?>
    <table id="wcssc-saved-cart-table">
    <thead>
    <th><?php _e( 'Sr. no', 'wcssc' ); ?></th>
    <th><?php _e( 'Cart Name', 'wcssc' ); ?></th>
    <th><?php _e( 'Remove Cart', 'wcssc' ); ?></th>
    </thead>
    <tbody>
	<?php
	while ( $saved_carts->have_posts() ) {

		$saved_carts->the_post(); ?>
        <tr>
        <td><?php echo $count; ?></td>
        <td><a href="<?php echo get_permalink(); ?>"><?php echo get_the_title(); ?></a></td>
        <td><a href="<?php echo add_query_arg( array(
				'wcssc_nonce' => wp_create_nonce( 'wcssc_nonce' ),
				'remove_cart' => get_the_ID()
			) ); ?>"><?php _e( 'Remove', 'wcssc' ); ?></a></td>
        </tr><?php

		$count ++;
	}
	wp_reset_postdata(); ?>
    </tbody>
    </table><?php
}