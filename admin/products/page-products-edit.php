<?php

//Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function navbb_donation_render_edit_products_page (){

  	// check if user is allowed access
  	if ( ! current_user_can( 'edit_posts' ) ) return;

  	//Make sure that we at least have the donation id to run our query
    if ( ! isset( $_GET['product_id'] ) ) return;

    global $wpdb;
    $product_id = $_GET['product_id'];
    $product = $wpdb->get_row(
  			$wpdb->prepare( "
  					SELECT * FROM bloodbank_products
  					WHERE id = %d",
  					$product_id
  			)
  	);

    $current_donation_id = ( isset( $product->donation_id ) ? $product->donation_id : '');
    $current_product_name = ( isset( $product->product_name ) ? $product->product_name : '');
    $current_product_status = ( isset( $product->product_status ) ? $product->product_status : '');


    ?>
    <div class='navbb-donation-page-container'>
      <div class="navbb-product-box">
        <h1>Edit Product</h1>
          <form type="POST" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>"  method="POST" >

            <select name="product" id="product">
              <option value="" <?php if( empty($current_product_name) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Product:</option>
              <option value="prbc250"<?php if( "prbc250" == $current_product_name ): ?> selected="selected"<?php endif; ?>>Packed Red Blood Cells 250ml</option>
              <option value="prbc125"<?php if( "prbc125" == $current_product_name ): ?> selected="selected"<?php endif; ?>>Packed Red Blood Cells 125ml</option>
              <option value="ffp250"<?php if( "ffp250" == $current_product_name ): ?> selected="selected"<?php endif; ?>>Fresh Frozen Plasma 250ml</option>
              <option value="ffp125"<?php if( "ffp125" == $current_product_name ): ?> selected="selected"<?php endif; ?>>Fresh Frozen Plasma 125ml</option>
            </select>

            <select name="status" id="status">
              <option value="" <?php if( empty($current_product_status) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Status </option>
              <option value="available"<?php if( "available" == $current_product_status ): ?> selected="selected"<?php endif; ?>>Available</option>
              <option value="sold"<?php if( "sold" == $current_product_status ): ?> selected="selected"<?php endif; ?>>Sold</option>
              <option value="expired"<?php if( "expired" == $current_product_status ): ?> selected="selected"<?php endif; ?>>Expired</option>
              <option value="viable"<?php if( "viable" == $current_product_status ): ?> selected="selected"<?php endif; ?>>Not Viable</option>
              <option value="artemis"<?php if( "artemis" == $current_product_status ): ?> selected="selected"<?php endif; ?>>Artemis Blood</option>
            </select>


            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="donation_id" value="<?php echo $current_donation_id; ?>">
            <input type="hidden" name="action" value="editProductForm">
            <br><br>
            <input type="submit" value="Save Changes">

          </form>

      </div>
    </div>



    <?php



}
