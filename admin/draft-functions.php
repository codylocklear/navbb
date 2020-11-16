<?php

//Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//This edits the item meta beneath items on the order page in woocommerce
//You can find this result in the admin section
add_action( 'woocommerce_after_order_itemmeta', 'custom_link_after_order_itemmeta', 20, 3 );
function custom_link_after_order_itemmeta( $item_id, $item, $product ) {

		global $wpdb;
		$product_name = "prbc250";

		$prbcnegatives = $wpdb->get_results(
				$wpdb->prepare( "
						SELECT * FROM bloodbank_products
						WHERE product_name = %s AND product_status = 'available'",
						$product_name
				)
		);

    // Only for "line item" order items
    if( ! $item->is_type('line_item') ) return;

    // Only for backend and for product ID 123
    if( $product->get_id() == 193 && is_admin() )
        echo '<a href="http://example.com/new-view/?id='.$item->get_order_id().'">'.__("Click here to view this").'</a>';

				// foreach ($prbcnegatives as $prbcnegative) {
				// 	$productstatus = $prbcnegative->product_status;
				// 	echo 'DEA Neative 1.1 250ml PRBC: ' . $productstatus;
				// 	console_log($productstatus);
				// }

				echo "<select name='the_name'>";
			 	foreach ( $prbcnegatives as $option ){
					echo "<option value='";
					echo $option->product_status;
					echo "'>";
					echo $option->product_name;
					echo "</option>";
				}
				echo "</select>";

}
