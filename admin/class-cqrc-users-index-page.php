<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Showing the users details list for scanned QR code. 
 * @link       https://www.worldwebtechnology.com/
 * @since      1.0.0
 *
 * @package    Cqrc_Generator
 */

// Define the main class
class Cqrc_Scanned_QR_List_Table extends WP_List_Table {

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
			'user_ip_address' => esc_html__('User IP Address', 'custom-qr-code-generator'),
			'device_type'   => esc_html__('Device Type', 'custom-qr-code-generator'),
			'location'      => esc_html__('Location', 'custom-qr-code-generator'),
			'qrid'          => esc_html__('QR Code ID', 'custom-qr-code-generator'),
			'qr_usage_count'   => esc_html__('QR Scan Count', 'custom-qr-code-generator'),
			'created_at'    => esc_html__('Created At', 'custom-qr-code-generator'),
			'updated_at'    => esc_html__('Updated At', 'custom-qr-code-generator'),
		);
	}

	public function get_bulk_actions() {
		return array(
			'delete' => esc_html__('Delete', 'custom-qr-code-generator'),
		);
	}

	private function cqrc_process_bulk_action() {
		if ( 'delete' === $this->current_action() ) {
			if ( empty($_REQUEST['_wpnonce']) && ! wp_verify_nonce(sanitize_text_field( wp_unslash($_REQUEST['_wpnonce'], '_wpnonce'))) ) {
				wp_die( esc_html__('Nonce verification failed. Please refresh and try again', 'custom-qr-code-generator' ) );
			}
			
			// Check if 'id' is set in the POST request
			if ( ! empty( $_POST['id'] ) && is_array( $_POST['id'] ) ) {
				$delete_ids = array_map( 'absint', $_POST['id'] );

            	// Proceed to delete the IDs
				if ( ! empty( $delete_ids ) ) {
					foreach ( $delete_ids as $id ) {
						$this->cqrc_delete_qr( $id );
					}
				}
			} else {
				wp_die( esc_html__( 'No IDs provided for deletion.', 'custom-qr-code-generator' ) );
			}
		}
	}

	protected function column_default($item, $column_name) {
		switch ($column_name) {
			case 'user_ip_address':
			case 'device_type':
			case 'qrid':
			case 'qr_usage_count':
			case 'created_at':
			case 'updated_at':
			return esc_html($item[$column_name]);
			case 'location':
			return $this->cqrc_format_location($item['location']);
			default:
			return '';
		}
	}

	private function cqrc_format_location($location_data) {
    	// Decode the JSON data into an associative array
		$data_array = json_decode($location_data, true);

    	// Check if the decoded data is an array
		if (is_array($data_array)) {
			$output = '';

        	// Check for the presence of the 'Response' key
			if (!empty($data_array['Response'])) {
				$output .= '<strong>' . esc_html__('Status:', 'custom-qr-code-generator') . '</strong> Private<br>';
			}

        	// Loop through the array and format the output
			foreach ($data_array as $key => $value) {
				if ($key !== 'Response' && $key !== 'IP') {
					$output .= sprintf('<strong>%s:</strong> %s<br>', esc_html($key), esc_html($value));
				}
			}
			return $output;
		}

    	// Return an error message if JSON decoding fails
		return esc_html__('Error decoding JSON.', 'custom-qr-code-generator');
	}
	
	private function cqrc_get_custom_data_from_database($search_term = '') {
		global $wpdb;
		$table_name = esc_sql( QRCODE_INSIGHTS_TABLE ); 
		// phpcs:disable
		
		if ( !empty( $search_term )) {
			// Prepare the search term for LIKE queries
			$search_term = '%' . $wpdb->esc_like($search_term) . '%';

        	// Prepare the SQL query with placeholders
			$query = $wpdb->prepare(
				"SELECT * FROM `{$table_name}` WHERE `user_ip_address` LIKE %s OR `device_type` LIKE %s OR `location` LIKE %s",
				$search_term,
				$search_term,
				$search_term
			);
			$data = $wpdb->get_results($query, ARRAY_A); // phpcs:ignore
		} else {
			$data = $wpdb->get_results( "SELECT * FROM `$table_name`", ARRAY_A ); // phpcs:ignore
		}
		
		// phpcs:enable
		if ( false === $data ) {
			echo 'Error: ' . esc_html( $wpdb->last_error );
			return array();
		}
		return $data;
	}

	public function prepare_items() {
		$this->cqrc_process_bulk_action();

    	// Get custom data from the database, with optional search term
		$search_term = !empty( $_REQUEST['s'] ) ? sanitize_text_field(wp_unslash( trim( $_REQUEST['s'] ) ) ) : ''; // phpcs:ignore
		$data = $this->cqrc_get_custom_data_from_database($search_term);

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
		$orderby = ! empty( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'created_at'; // phpcs:ignore
		$order = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'asc'; // phpcs:ignore

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
			absint( $item['id'] )
		);
	}

	public function get_sortable_columns() {
		return array(
			'user_ip_address' => array('user_ip_address', false),
			'device_type'     => array('device_type', false),
			'qrid'            => array('qrid', false),
			'qr_usage_count'  => array('qr_usage_count', false),
			'created_at'      => array('created_at', false),
			'updated_at'      => array('updated_at', false),
		);
	}

	private function cqrc_delete_qr( $id ) {
		global $wpdb;
		$generator_table = esc_sql( QRCODE_GENERATOR_TABLE );
		$insights_table  = esc_sql( QRCODE_INSIGHTS_TABLE );
        $qr_info         = $wpdb->get_row( $wpdb->prepare( "SELECT `qrid` FROM {$insights_table} WHERE id = %d", $id ) );  // phpcs:ignore
        
        // phpcs:ignore
        if ( $qr_info ) {
        	$qrid = $qr_info->qrid;
            $wpdb->delete( $insights_table, array( 'id' => $id ) ); // phpcs:ignore
            $wpdb->query( $wpdb->prepare( "UPDATE `$generator_table` SET `total_scans` = `total_scans` - 1 WHERE `id` = %d", $qrid ) ); // phpcs:ignore
        }
    }
}

// Render the admin page
echo '<div class="wrap">';
echo '<h1>' . esc_html__('Scanned Data Overview', 'custom-qr-code-generator') . '</h1>';

$scanned_qr_list_table = new Cqrc_Scanned_QR_List_Table();
$scanned_qr_list_table->prepare_items();

echo '<form method="post" id="qr-listing-details">';
echo '<input type="hidden" name="page" value="custom-qr-code-generator">';
$scanned_qr_list_table->search_box( 'Search', 'search_id' );
$scanned_qr_list_table->display();
echo '</form>';
echo '</div>';
