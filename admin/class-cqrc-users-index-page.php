<?php
/**
 * Showing the users details list for scanned QR code. 
 * @link       https://www.worldwebtechnology.com/
 * @since      1.0.0
 *
 * @package    Cqrc_Generator
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define the main class
class Scanned_QR_List_Table extends WP_List_Table {

	private $per_page = 10;

	public function __construct() {
		parent::__construct(array(
			'singular' => 'scanned_qr',
			'plural'   => 'scanned_qrs',
			'ajax'     => false,
		));
	}

	public function get_columns() {
		return array(
			'cb'            => '<input type="checkbox" />',
			'user_ip_address' => __('User IP Address', 'custom-qrcode-generator'),
			'device_type'   => __('Device Type', 'custom-qrcode-generator'),
			'location'      => __('Location', 'custom-qrcode-generator'),
			'qrid'          => __('QR Code ID', 'custom-qrcode-generator'),
			'created_at'    => __('Created At', 'custom-qrcode-generator'),
		);
	}

	public function get_bulk_actions() {
		return array(
			'delete' => __('Delete', 'custom-qrcode-generator'),
		);
	}

	public function process_bulk_action() {
		if ( 'delete' === $this->current_action() ) {
			if ( empty($_REQUEST['_wpnonce']) || ! wp_verify_nonce(sanitize_text_field( wp_unslash($_REQUEST['_wpnonce'], '_wpnonce'))) ) {
				wp_die( esc_html__('Security check failed.', 'custom-qrcode-generator' ) );
			}
			
			$delete_ids = !empty( $_POST['id'] ) ? array_map( 'absint', $_POST['id'] ) : array();
			foreach ( $delete_ids as $id ) {
				self::delete_qr( $id );
			}
		}
	}

	protected function column_default($item, $column_name) {
		switch ($column_name) {
			case 'user_ip_address':
			case 'device_type':
			case 'qrid':
			case 'created_at':
			return esc_html($item[$column_name]);
			case 'location':
			return $this->format_location($item['location']);
			default:
			return '';
		}
	}

	private function format_location($location_data) {
    	// Decode the JSON data into an associative array
		$data_array = json_decode($location_data, true);

    	// Check if the decoded data is an array
		if (is_array($data_array)) {
			$output = '';

        	// Check for the presence of the 'Response' key
			if (!empty($data_array['Response'])) {
				$output .= '<strong>' . esc_html__('Status:', 'custom-qrcode-generator') . '</strong> Private<br>';
			}

        	// Loop through the array and format the output
			foreach ($data_array as $key => $value) {
            	// Skip 'Response' and 'IP' keys
				if ($key !== 'Response' && $key !== 'IP') {
					$output .= sprintf('<strong>%s:</strong> %s<br>', esc_html($key), esc_html($value));
				}
			}
			return $output;
		}

    	// Return an error message if JSON decoding fails
		return esc_html__('Error decoding JSON.', 'custom-qrcode-generator');
	}
	public function get_custom_data_from_database($search_term = '') {
		global $wpdb;
		$table_name = QRCODE_INSIGHTS_TABLE; 
		if ( !empty( $search_term )) {
			$query = "SELECT * FROM {$table_name}";
			$search_term = esc_sql($wpdb->esc_like($search_term));
			$query .= " WHERE user_ip_address LIKE '%{$search_term}%' OR device_type LIKE '%{$search_term}%' OR location LIKE '%{$search_term}%'";
			$data = $wpdb->get_results($query, ARRAY_A); // phpcs:ignore
		} else {
			$data = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A ); // phpcs:ignore
		}

		if ( false === $data ) {
			echo 'Error: ' . esc_html( $wpdb->last_error );
			return array();
		}
		return $data;
	}

	public function prepare_items() {
		$this->process_bulk_action();

    	// Get custom data from the database, with optional search term
		$search_term = !empty( $_REQUEST['s'] ) ? sanitize_text_field(wp_unslash( trim( $_REQUEST['s'] ) ) ) : '';
		$data = $this->get_custom_data_from_database($search_term);

    	// Sorting logic
		usort( $data, array( &$this, 'usort_reorder' ) );

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

	    // Pagination
		$per_page = 10;
		$current_page = $this->get_pagenum();
		$total_items = count( $data );

    	// Slice the data for pagination
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );
		$this->items = $data;

    	// Set pagination arguments
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}


	public function usort_reorder( $a, $b ) {
    	// If no sort, default to sorting by ID.
		$orderby = ! empty( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'id';
		$order = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'asc';

    	// If columns are date fields or other special fields, ensure proper comparison.
		switch ($orderby) {
			case 'created_at':
			case 'updated_at':
			$result = strtotime( $a[$orderby] ) - strtotime( $b[$orderby] );
			break;
			case 'total_scans':
			$result = (int) $a[$orderby] - (int) $b[$orderby];
			break;
			default:
			$result = strcmp( $a[ $orderby ], $b[ $orderby ] );
		}

    	// Reverse order if descending.
		return ( 'desc' === $order ) ? $result : -$result;
	}


	public function column_cb($item) {
		return sprintf(
			'<input type="checkbox" name="id[]" value="%s" />',
			esc_attr( __( $item['id'], 'custom-qrcode-generator' ) )
		);
	}

	public function get_sortable_columns() {
		return array(
			'user_ip_address' => array('user_ip_address', false),
			'device_type'     => array('device_type', false),
			'qrid'            => array('qrid', false),
			'created_at'      => array('created_at', false),
		);
	}

	public static function delete_qr($id) {
		global $wpdb;
		$generator_table = QRCODE_GENERATOR_TABLE;
		$insights_table = QRCODE_INSIGHTS_TABLE;

        $qr_info = $wpdb->get_row($wpdb->prepare("SELECT qrid FROM $insights_table WHERE id = %d", $id)); // phpcs:ignore

        if ($qr_info) {
        	$qrid = $qr_info->qrid;
            $wpdb->delete($insights_table, array('id' => $id)); // phpcs:ignore
            $wpdb->query($wpdb->prepare("UPDATE $generator_table SET total_scans = total_scans - 1 WHERE id = %d", $qrid )); // phpcs:ignore
        }
    }
}

// Render the admin page
echo '<div class="wrap">';
echo '<h1>' . esc_html__('Scanned Data Overview', 'custom-qrcode-generator') . '</h1>';

$scanned_qr_list_table = new Scanned_QR_List_Table();
$scanned_qr_list_table->prepare_items();

echo '<form method="post" id="qr-listing-details">';
echo '<input type="hidden" name="page" value="custom-qrcode-generator">';
$scanned_qr_list_table->search_box( 'Search', 'search_id' );
$scanned_qr_list_table->display();
echo '</form>';
echo '</div>';
?>