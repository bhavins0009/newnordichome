<?php
if (!class_exists('WP_List_Table')) {
	require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Milcom_Order_Table extends WP_List_Table
{
	public function __construct()
	{
		parent::__construct(array(
			'singular' => 'Milcomeorder',
			'plural' => 'Milcomeorders',
			'ajax' => false
		));		
	}

	public function getMilcomeOrderStatus($postId) {

		global $wpdb;
		$sql = "SELECT is_milcom_approved FROM `clk_42491aa6f3_wp_wc_order_stats` where order_id=".$postId." ";
		
		$result = $wpdb->get_results($sql);
		
		$approved = '';
		if(!empty($result)){
			$approved = $result[0]->is_milcom_approved;
			if($approved == 'No') {
				$approvedBtn = '<mark class="order-status status-processing tips">
					<span> 
						<a href="'.get_theme_file_uri().'/inc/admin/update_milcom_order.php?orderId='.$postId.'"> 
							Approve
						</a>
					</span>	
				</mark>';
			} else {
				$approvedBtn = '';
			}
		} else {
			$approvedBtn = '';
		}
		return $approvedBtn;
	}	

}
?>