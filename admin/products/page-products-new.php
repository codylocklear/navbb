<?php

//Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function navbb_donation_render_add_products_page(){

	// check if user is allowed access
	if ( ! current_user_can( 'edit_posts' ) ) return;
	if ( ! isset( $_GET['donation_id'] ) ) return;

	global $wpdb;
	$donation_id = $_GET['donation_id'];
	$products = $wpdb->get_results(
			$wpdb->prepare( "
					SELECT * FROM bloodbank_products
					WHERE donation_id = %d",
					$donation_id
			)
	);

	echo "<div class='navbb-metabox-container'>";
	echo "<h3>Products Generated From Donation:</h3>";
	echo "<table class='navbb_table' id='products_table'>";
	echo "<thead><tr>";
	echo "<th>Product ID</th>";
	echo "<th>Product Name</th>";
	echo "<th>Status</th>";
	echo "</tr></thead>";

	foreach ($products as $product) {
		echo "<tr>";
		echo "<td>".$product->id."</td>";
		echo "<td>".$product->product_name."</td>";
		echo "<td>".$product->product_status."</td>";
		echo "</tr>";
	}

	echo "</table>";
	echo "</div><br><br>";

	?>
	<div class="navbb-product-box">

		<form type="POST" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>"  method="POST" >

			<select name="product" id="product">
				<option value="" selected="selected" disabled hidden>Select Product:</option>
				<option value="prbc250">Packed Red Blood Cells 250ml</option>
				<option value="prbc125">Packed Red Blood Cells 125ml</option>
				<option value="ffp250">Fresh Frozen Plasma 250ml</option>
				<option value="ffp125">Fresh Frozen Plasma 125ml</option>
				<option value="whole">Whole Blood</option>
			</select>

			<select name="status" id="status">
				<option value="" disabled hidden>Select Status </option>
				<option value="available">Available</option>
				<option value="sold">Sold</option>
				<option value="viable">Not Viable</option>
				<option value="artemis">Artemis Blood</option>
			</select>

			<input type="hidden" name="donation_id" value="<?php echo $donation_id; ?>">
			<input type="hidden" name="action" value="addNewProductForm">
			<br><br>
			<input type="submit" value="Add Product">

		</form>

	</div>

	<?php

}
