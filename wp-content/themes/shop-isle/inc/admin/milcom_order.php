<style type="text/css">
	.statistics-data { padding-right: 20px }
	.statistics-data .col { float: left  }
	.statistics-data .col.text { margin:0; margin-top: 17px}
	.statistics-data .col input[type='text']{ width: 100px; margin-right: 10px }
	.statistics-data  p.search-box { float: left;margin: 1em 10px 1em 10px }
	.statistics-data  p.search-box input[type='submit']{ margin-left: 10px;margin-right: 10px }
	.video-data { position: relative }
	.video-data .form-clr { position: absolute; left:641px; top:49px }
	.video-data .form-clr input[type='submit'] { text-shadow: none }
	.video-data .top.tablenav{ float: right; clear: none; margin:8px 0 0; }
	.video-data .bottom.tablenav.bottom { float: right; clear: none; margin-top: 15px }
	.video-data .form-export { float: right; position: absolute; right: 20px; top:49px; }
	.video-data .search-box input[type='submit']{background-color: #28a745;border-color: #1f8837;
    box-shadow: 0 1px 0 #1f8837;color: #fff}
    .video-data .form-export .button-primary {color: #fff; margin-left: 15px !important; text-shadow:none;background-color: #6c757d;border-color: #5a5d5f;box-shadow: 0 1px 0 #5a5d5f}
    .video-data .search-box .button-danger {color: #fff; margin-right: 10px !important; text-shadow:none;background-color: #ff0000;border-color: #bb1313;box-shadow: 0 1px 0 #bb1313}
    .video-data .top.tablenav { display: none !important }
    .popup-box-overlay { width: 100%; height:100%;position: absolute; text-align: center; top:0; left:0; right: 0; bottom:0; background:rgba(0,0,0,0.7); z-index: 99999; display: none; text-align: center}   
    .popup-box-overlay:after { content:''; display: inline-block; vertical-align: middle; height: 100%; font-size: 0; width: 0}
    .popup-box-inner {  padding:0; text-align: left; margin:0 auto; display: inline-block; max-width:480px; background:#fff;width: 100%; vertical-align: middle; position: relative;}
    .popup-box-inner p{ padding:30px 20px; font-size: 18px; line-height: 1.2;  margin: 0 }
    .popup-box-inner hr { background-color: #eee; margin:0 20px 0; }
    .popup-box-inner .form-group { padding:20px 20px 20px; width: 100%; float:left; box-sizing: border-box}
    .popup-box-inner h4{ font-size:20px; margin:0; padding:15px 20px; font-weight: 600; background-color:#0085ba; color:#fff; }
    .popup-box-inner .cancel-btn { color: #fff; margin-right: 10px !important; text-shadow:none;background-color: #0085ba; font-size: 14px;line-height: 26px;height: 32px;margin: 0;padding: 0 10px 1px;
       white-space: nowra; float: left; border:0; cursor: pointer;}
    .popup-box-inner .delete-btn { color: #fff; text-shadow:none;background-color: #ff0000; font-size: 14px; float:right;line-height: 26px;height: 32px;margin: 0;padding: 0 10px 1px;white-space: nowrap;border:0; cursor: pointer;}
    .popup-box-inner .ui-button { height:27px !important; position: absolute !important; background-color: #fff; color:#333; right: 3px; top:11px;  }
</style>

<?php
if (!class_exists('WP_List_Table')) {
	require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Event_Reg_Table extends WP_List_Table
{
	public function __construct()
	{
		parent::__construct(array(
			'singular' => 'Milcomeorder',
			'plural' => 'Milcomeorders',
			'ajax' => false
		));		
	}

	public static function get_records($per_page = 20, $page_number = 1)
	{
		global $wpdb;
		$sql = "SELECT * FROM `clk_42491aa6f3_wp_woocommerce_order_items`";
		$sql.= ' WHERE 1=1 ';
		$sql.= " LIMIT $per_page";
		$sql.= ' OFFSET ' . ($page_number - 1) * $per_page;

		$result = $wpdb->get_results($sql, 'ARRAY_A');
		return $result;
	}

	function get_columns()
	{
		$columns = [
			'cb' => '<input type="checkbox" />', 
			'order_item_id' => __('Order Item Id', 'wth') , 
			'order_item_name' => __('Order Item Name', 'wth') , 			 
			'order_item_type' => __('Order Item Type', 'wth') , 			
			'order_id' => __('Order Id', 'wth')
		];
		return $columns;
	}

	public function get_hidden_columns()
	{
		array([
			'video_timestamp' => __('video_timestamp','wth')
		]);
		return array();
	}

	public function get_sortable_columns()
	{
		$sortable_columns = array(
			'order_item_id' => array('order_item_id',true),
			'order_item_name' => array('order_item_name',true),
			'order_item_type' => array('order_item_type',true),
			'order_id' => array('order_id',true)
		);
		return $sortable_columns;
	}	

	public function column_default( $item, $column_name ) 
	{
		switch ( $column_name ) {
			case 'order_item_id':
			case 'order_item_name':
			case 'order_item_type':
			case 'order_id':
			return $item[ $column_name ];
			default:
			return print_r( $item, true );
		}
	}

	function column_cb($item)
	{
		return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['order_item_id']);
	}

	function column_your_name( $item ) 
	{
		$actions = array(
			'edit'      => sprintf('<a href="?page=%s&action=%s&record=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
			'delete'    => sprintf('<a href="?page=%s&action=%s&record=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id']),
		);
		return sprintf('%1$s %2$s', $item['your_name'], $this->row_actions($actions) );
	}

	public function get_bulk_actions()
	{
		$actions = ['bulk-delete' => 'Delete'];
		return $actions;
	}

	public static function delete_records($id)
	{
		global $wpdb;
		$wpdb->delete("video_statistics", ['id' => $id], ['%d']);
	}

	public static function record_count()
	{
		global $wpdb;
		$sql = "SELECT COUNT(*) FROM `clk_42491aa6f3_wp_woocommerce_order_items`";
		$sql.= ' WHERE 1=1 ';

		return $wpdb->get_var($sql);
	}

	public function no_items()
	{
		_e('No record found in the database.', 'wth');
	}	

	public function process_bulk_action() 
	{
		if ( 'delete' === $this->current_action() ) {	    
			self::delete_records( absint( $_GET['record'] ) );
		}

		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )) {
			$delete_ids = esc_sql( $_POST['bulk-delete'] );
			foreach ( $delete_ids as $id ) {
				self::delete_records( $id );
			}
		}
	}

	public function deleted_items($per_page = 20, $page_number = 1){

		global $wpdb;
		$sql = "SELECT GROUP_CONCAT(`order_item_id`) FROM `clk_42491aa6f3_wp_woocommerce_order_items`";
		$sql.= ' WHERE 1=1 ';
		$result = $wpdb->get_results($sql);
		if(!empty($result)){
			//$vId = $result[0]->order_item_id;
			$vId = '';
		} else {
			$vId = '';
		}

		return '<input name="deleted-video-items" type="hidden" value="'.$vId.'">';
	}

	public function prepare_items()
	{
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		//$this->process_bulk_action();
		$per_page = $this->get_items_per_page('records_per_page', 10);
		$current_page = $this->get_pagenum();
		$total_items = self::record_count();
		$data = self::get_records($per_page, $current_page);
		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page' => $per_page,
		]);

		$this->items = $data;
	}



}
?>