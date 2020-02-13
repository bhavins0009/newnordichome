<?php namespace ShipmondoForWooCommerce\Plugin\Controllers;

use ShipmondoForWooCommerce\Lib\Tools\Loader;
use ShipmondoForWooCommerce\Plugin\Plugin;

class Migrations {

	private $backup_id;
	private $migration_version = '1.0.7';

	public function __construct() {
		$this->registerActions();
	}

	public function registerActions() {
		Loader::addAction('init', $this, 'maybeMigrate');
	}

	/**
	 * Migrate if migration version is less than the one in WordPress options.
	 */
	public function maybeMigrate() {
		if(is_Admin() && $this->migration_version > get_option('shipmondo_migration_version', '0.0.0')) {
			$this->migrate();
		}
	}

	/**
	 * Migrate
	 */
	private function migrate() {
		$this->migrateDeliveryOptions();
		$this->migrateOrderItemMeta();
		$this->migratePluginOptions();
		$this->migrateUserMeta();

		update_option('shipmondo_migration_version', $this->migration_version, true); // autoload for performance
	}

	/**
	 * Migrate delivery options
	 */
	private function migrateDeliveryOptions() {
		// Shipping Settings
		$this->replaceInDB('options', 'option_name', 'woocommerce_pakkelabels_shipping_', 'woocommerce_shipmondo_shipping_');

		// Shipping zone settings
		$this->replaceInDB('woocommerce_shipping_zone_methods', 'method_id', 'pakkelabels_shipping_', 'shipmondo_shipping_');
	}

	/**
	 * Migrate plugin options
	 */
	private function migratePluginOptions() {
		// Pakkelabel_settings
		$this->replaceInDB('options', 'option_value', 'Pakkelabel_', 'shipmondo_', "option_name = 'Pakkelabel_settings'");
		$this->replaceInDB('options', 'option_value', 'pakkelabel_', 'shipmondo_', "option_name = 'Pakkelabel_settings'");
		$this->replaceInDB('options', 'option_name', 'Pakkelabel_', 'shipmondo_', "option_name = 'Pakkelabel_settings'");
	}

	/**
	 * Migrate user options - shipping_method
	 */
	private function migrateUserMeta() {
		$this->replaceInDB('usermeta', 'meta_value', 'pakkelabels_', 'shipmondo_', "meta_key = 'shipping_method'");
	}

	/**
	 * Migrate order item meta
	 */
	private function migrateOrderItemMeta() {
		$this->replaceInDB('woocommerce_order_itemmeta', 'meta_value', 'pakkelabels_shipping_', 'shipmondo_shipping_', "meta_key = 'method_id'");
	}

	/**
	 * Replace in WP
	 * @param      $table
	 * @param      $field
	 * @param      $search
	 * @param      $replace
	 * @param null $where
	 */
	private function replaceInDB($table, $field, $search, $replace, $where = '') {
		// Get data
		global $wpdb;

		if(!empty($where)) {
			$where .= " AND ";
		}

		$where .= "$field LIKE '%$search%'";

		$data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}$table WHERE $where");

		$primary_key = $wpdb->get_row("SHOW KEYS FROM {$wpdb->prefix}$table WHERE Key_name = 'PRIMARY'");
		if(isset($primary_key->Column_name)) {
			$primary_key = $primary_key->Column_name;
		}

		// backup
		$this->backupData($data, $table, $field, $search);

		// replace
		foreach((array) $data as $original) {
			if(isset($original->$field) && isset($original->$primary_key)) {
				$new_value = $this->searchReplace($original->$field, $search, $replace);

				$sql = $wpdb->prepare("UPDATE {$wpdb->prefix}$table SET $field = %s WHERE $primary_key = {$original->{$primary_key}}", array($new_value));

				$wpdb->query($sql);
			}
		}

		return;
	}

	private function searchReplace($data, $search, $replace, $serialize = false) {
		try {
			if(is_string($data) && is_serialized($data) && !is_serialized_string($data) && ($unserialized = @unserialize($data)) !== false) {
				$data = $this->searchReplace($unserialized, $search, $replace, true);
			} elseif(is_array($data)) {
				$_tmp = array();
				foreach($data as $key => $value) {
					$_tmp[str_replace($search, $replace, $key)] = $this->searchReplace($value, $search, $replace);
				}
				$data = $_tmp;
				unset($_tmp);
			} elseif(is_object($data)) {
				$_tmp = $data;
				$props = get_object_vars($data);
				foreach($props as $key => $value) {
					$key = str_replace($search, $replace, $key);
					$_tmp->{$key} = $this->searchReplace($value, $search, $replace);
				}
			} elseif(is_serialized_string($data) && is_serialized($data)) {
				if(($data = @unserialize($data)) !== false) {
					$data = str_replace($search, $replace, $data);
					$data = serialize($data);
				}
			} else {
				if(is_string($data)) {
					$data = str_replace($search, $replace, $data);
				}
			}

			if($serialize) {
				return serialize($data);
			}
		} catch(\Exception $error) {
			error_log($error->getMessage());
		}

		return $data;
	}

	/**
	 * Backup data we replace - just in case...
	 * @param $table
	 * @param $field
	 * @param $search
	 * @param $where
	 */
	private function backupData($data, $table, $field, $search) {
		$backup = fopen($this->getBackupFilePath("{$table}_{$field}_{$search}"), 'w');
		fwrite($backup, "<?php exit('No Access');?>" . PHP_EOL); // For security reasons
		fwrite($backup, json_encode($data));
		fclose($backup);
	}

	/**
	 *
	 * @param $filename
	 *
	 * @return string
	 */
	private function getBackupFilePath($filename) {
		$parts = array(
			ABSPATH,
			'wp-content',
			'shipmondo',
			'migrate',
			'backup',
			$this->getBackupID(),
		);

		$path = trailingslashit(join('/', array_filter($parts)));

		if(!is_dir($path)) {
			mkdir($path, 0770, true);
		}

		$filename = $filename . '.php';

		return $path . $filename;

	}

	/**
	 * Get current backup ID or generate new
	 * @return string
	 */
	private function getBackupID() {
		if(!isset($this->backup_id)) {
			$this->backup_id = uniqid();
		}

		return $this->backup_id;
	}
}