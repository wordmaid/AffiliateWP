<?php

/**
 * AffWP_Reports_Table Class
 *
 * Renders the Affiliates table on the Affiliates page
 *
 * @since 1.8
 */

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class AffWP_Export_Reports_List_Table extends WP_List_Table {

    /**
     * Default number of items to show per page
     *
     * @var string
     * @since 1.8
     */
    public $per_page = 30;

    /**
     * Base url of this page
     *
     * @var string
     * @since 1.8
     */
    public $base_url;

    /**
     * Total number of affiliates found
     *
     * @var int
     * @since 1.8
     */
    public $total_count;

    /**
     * Number of active affiliates found
     *
     * @var string
     * @since 1.8
     */
    public $active_count;

    /**
     *  Number of inactive affiliates found
     *
     * @var string
     * @since 1.8
     */
    public $inactive_count;

    /**
     * Number of pending affiliates found
     *
     * @var string
     * @since 1.8
     */
    public $pending_count;

    /**
     * Number of rejected affiliates found
     *
     * @var string
     * @since 1.8
     */
    public $rejected_count;

    /**
     * Get things started
     *
     * @since 1.8
     * @uses AffWP_Reports_Table::get_affiliate_counts()
     * @see WP_List_Table::__construct()
     */
    public function __construct() {
        global $status, $page;

        parent::__construct( array(
            'singular'  => 'affiliate',
            'plural'    => 'affiliates',
            'ajax'      => false
        ) );

        $this->get_affiliate_counts();
        $this->base_url = admin_url( 'admin.php?page=affiliate-wp-reports&tab=affiliates' );
    }


    /**
     * Render advanced filters for the list table
     *
     *
     * @since 1.8
     * @uses AffWP_Reports_Table::get_affiliate_counts()
     * @see WP_List_Table::__construct()
     */
    public function advanced_filters() {
        $status              = isset( $_GET['status'] )              ? sanitize_text_field( $_GET['status'] )     : null;
        $earnings            = isset( $_GET['status'] )              ? sanitize_text_field( $_GET['earnings'] )   : null;
        $start_date          = isset( $_GET['start-date'] )          ? sanitize_text_field( $_GET['start-date'] ) : null;
        $end_date            = isset( $_GET['end-date'] )            ? sanitize_text_field( $_GET['end-date'] )   : null;
        $reg_start_date      = isset( $_GET['reg-start-date'] )      ? sanitize_text_field( $_GET['reg-start-date'] ) : null;
        $reg_end_date        = isset( $_GET['reg-end-date'] )        ? sanitize_text_field( $_GET['reg-end-date'] )   : null;
        $earnings_start_date = isset( $_GET['earnings-start-date'] ) ? sanitize_text_field( $_GET['earnings-start-date'] ) : null;
        $earnings_end_date   = isset( $_GET['earnings-end-date'] )   ? sanitize_text_field( $_GET['earnings-end-date'] )   : null;
?>
        <div id="affwp-report-filters">
        <table class="affwp_table affwp-report-filters-table">
            <tbody>
                <tr id="affwp-report-date-filters">
                    <td>
                        <span>
                        <h4><?php echo __( 'Had referrals between', 'affiliate-wp' ); ?></h4>
                            <label for="start-date"><?php _e( 'Start date', 'affiliate-wp' ); ?></label>
                            <input type="text" id="start-date" name="start-date" class="affwp-datepicker" value="<?php echo $start_date; ?>" placeholder="mm/dd/yyyy"/>
                            <label for="end-date"><?php _e( 'End date', 'affiliate-wp' ); ?></label>
                            <input type="text" id="end-date" name="end-date" class="affwp-datepicker" value="<?php echo $end_date; ?>" placeholder="mm/dd/yyyy"/>
                        </span>
                    </td>
                    <td>
                        <span id="affwp-report-earnings-filters" class="clearfix">
                        <h4><?php echo __( 'Earned more than', 'affiliate-wp' ); ?></h4>
                            <label for="earnings"><?php _e( 'Earned more than', 'affiliate-wp' ); ?></label>
                            <input type="text" id="earnings" name="earnings" class="" value="<?php echo $earnings; ?>" placeholder="enter an amount"/>
                        </span>
                    </td>

                </tr>
                <tr>
                    <td>
                        <span>
                            <h4><?php echo __( 'Registered between', 'affiliate-wp' ); ?></h4>
                            <label for="reg-start-date"><?php _e( 'Start date', 'affiliate-wp' ); ?></label>
                            <input type="text" id="reg-start-date" name="reg-start-date" class="affwp-datepicker" value="<?php echo $reg_start_date; ?>" placeholder="mm/dd/yyyy"/>
                            <label for="reg-end-date"><?php _e( 'End date', 'affiliate-wp' ); ?></label>
                            <input type="text" id="reg-end-date" name="reg-end-date" class="affwp-datepicker" value="<?php echo $reg_end_date; ?>" placeholder="mm/dd/yyyy"/>
                        </span>
                    </td>
                    <td>
                        <span>
                            <h4><?php echo __( 'During this timeframe', 'affiliate-wp' ); ?></h4>
                            <label for="earnings-start-date"><?php _e( 'Start date', 'affiliate-wp' ); ?></label>
                            <input type="text" id="earnings-start-date" name="earnings-start-date" class="affwp-datepicker" value="<?php echo $earnings_start_date; ?>" placeholder="mm/dd/yyyy"/>
                            <label for="earnings-end-date"><?php _e( 'End date', 'affiliate-wp' ); ?></label>
                            <input type="text" id="earnings-end-date" name="earnings-end-date" class="affwp-datepicker" value="<?php echo $earnings_end_date; ?>" placeholder="mm/dd/yyyy"/>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

            <input type="submit" class="button-secondary" value="<?php _e( 'Apply', 'affiliate-wp' ); ?>"/>
            <?php if( ! empty( $status ) ) : ?>
                <input type="hidden" name="status" value="<?php echo esc_attr( $status ); ?>"/>
            <?php endif; ?>
            <?php if( ! empty( $start_date )          ||
                      ! empty( $end_date )            ||
                      ! empty( $reg_start_date )      ||
                      ! empty( $reg_end_date )        ||
                      ! empty( $earnings_start_date ) ||
                      ! empty( $earnings_end_date ) ): ?>
                <a href="<?php echo admin_url( 'admin.php?page=affiliate-wp-reports&tab=affiliates' ); ?>" class="button-secondary"><?php _e( 'Clear Filter', 'affiliate-wp' ); ?></a>
            <?php endif; ?>
            <?php if( ! empty( $earnings ) ) : ?>
                <input type="hidden" name="status" value="<?php echo esc_attr( $earnings ); ?>"/>
            <?php endif; ?>
            <?php do_action( 'affwp_reports_advanced_filters_row' ); ?>
            <?php //$this->search_box( __( 'Search', 'affiliate-wp' ), 'affwp-reports' ); ?>
        </div>

<?php
    }

    /**
     * Show the search field
     *
     * @access public
     * @since 1.8
     *
     * @param string $text Label for the search box
     * @param string $input_id ID of the search box
     *
     * @return void
     */
    public function search_box( $text, $input_id ) {
        if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
            return;

        $input_id =  $input_id . '-search-input';

        if ( ! empty( $_REQUEST['orderby'] ) )
            echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
        if ( ! empty( $_REQUEST['order'] ) )
            echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
        ?>
        <p class="search-box">
            <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
            <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
            <?php submit_button( $text, 'button', false, false, array( 'ID' => 'search-submit' ) ); ?>
        </p>
    <?php
    }

    /**
     * Retrieve the view types
     *
     * @access public
     * @since 1.8
     * @return array $views All the views available
     */
    public function get_views() {
        $base           = admin_url( 'admin.php?page=affiliate-wp-reports&tab=affiliates' );

        $current        = isset( $_GET['status'] ) ? $_GET['status'] : '';
        $total_count    = '&nbsp;<span class="count">(' . $this->total_count    . ')</span>';
        $active_count   = '&nbsp;<span class="count">(' . $this->active_count . ')</span>';
        $inactive_count = '&nbsp;<span class="count">(' . $this->inactive_count  . ')</span>';
        $pending_count  = '&nbsp;<span class="count">(' . $this->pending_count  . ')</span>';
        $rejected_count = '&nbsp;<span class="count">(' . $this->rejected_count  . ')</span>';

        $views = array(
            'all'       => sprintf( '<a href="%s"%s>%s</a>', esc_url( remove_query_arg( 'status', $base ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __('All', 'affiliate-wp') . $total_count ),
            'active'    => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'active', $base ) ), $current === 'active' ? ' class="current"' : '', __('Active', 'affiliate-wp') . $active_count ),
            'inactive'  => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'inactive', $base ) ), $current === 'inactive' ? ' class="current"' : '', __('Inactive', 'affiliate-wp') . $inactive_count ),
            'pending'   => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'pending', $base ) ), $current === 'pending' ? ' class="current"' : '', __('Pending', 'affiliate-wp') . $pending_count ),
            'rejected'  => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'rejected', $base ) ), $current === 'rejected' ? ' class="current"' : '', __('Rejected', 'affiliate-wp') . $rejected_count ),
        );

        return $views;
    }

    /**
     * Retrieve the table columns
     *
     * @access public
     * @since 1.8
     * @return array $columns Array of all the list table columns
     */
    public function get_columns() {
        $columns = array(
            'name'            => __( 'Name', 'affiliate-wp' ),
            'username'        => __( 'Username', 'affiliate-wp' ),
            'affiliate_id'    => __( 'Affiliate ID', 'affiliate-wp' ),
            'earnings'        => __( 'Earnings', 'affiliate-wp' ),
            'rate'            => __( 'Rate', 'affiliate-wp' ),
            'unpaid'          => __( 'Unpaid Referrals', 'affiliate-wp' ),
            'referrals'       => __( 'Paid Referrals', 'affiliate-wp' ),
            'visits'          => __( 'Visits', 'affiliate-wp' ),
            'status'          => __( 'Status', 'affiliate-wp' ),
            'date_registered' => __( 'Registered', 'affiliate-wp' ),
        );

        return apply_filters( 'affwp_reports_table_columns', $columns );
    }

    /**
     * Retrieve the tables' sortable columns
     *
     * @access public
     * @since 1.8
     * @return array Array of all the sortable columns
     */
    public function get_sortable_columns() {
        return array(
            'name'            => array( 'name',         false ),
            'username'        => array( 'username',     false ),
            'affiliate_id'    => array( 'affiliate_id', false ),
            'earnings'        => array( 'earnings',     false ),
            'rate'            => array( 'rate',         false ),
            'unpaid'          => array( 'unpaid',       false ),
            'referrals'       => array( 'referrals',    false ),
            'visits'          => array( 'visits',       false ),
            'status'          => array( 'status',       false ),
            'date_registered' => array( 'registered',   false ),
        );
    }

    /**
     * This function renders most of the columns in the list table.
     *
     * @access public
     * @since 1.8
     *
     * @param array $affiliate Contains all the data of the affiliate
     * @param string $column_name The name of the column
     *
     * @return string Column Name
     */
    function column_default( $affiliate, $column_name ) {
        switch( $column_name ){

            default:
                $value = isset( $affiliate->$column_name ) ? $affiliate->$column_name : '';
                break;
        }

        return apply_filters( 'affwp_reports_table_' . $column_name, $value );
    }

    /**
     * Render the Name Column
     *
     * @access public
     * @since 1.8
     * @param array $affiliate Contains all the data of the affiliate
     * @return string Data shown in the Name column
     */
    function column_name( $affiliate ) {
        $base         = admin_url( 'admin.php?page=affiliate-wp-reports&tab=affiliates&affiliate_id=' . $affiliate->affiliate_id );
        $row_actions  = array();
        $name         = affiliate_wp()->affiliates->get_affiliate_name( $affiliate->affiliate_id );

        if( $name ) {
            $value = sprintf( '<a href="%s">%s</a>', get_edit_user_link( $affiliate->user_id ), $name );
        } else {
            $value = __( '(user deleted)', 'affiliate-wp' );
        }

        return apply_filters( 'affwp_reports_table_name', $value, $affiliate );
    }

    /**
     * Render the Username Column
     *
     * @access public
     * @since 1.8
     * @param array $affiliate Contains all the data of the affiliate
     * @return string Data shown in the Username column
     */
    function column_username( $affiliate ) {

        $row_actions = array();
        $user_info = get_userdata( $affiliate->user_id );
        $username  = $user_info->user_login;

        if ( $username ) {
            $value = $username;
        } else {
            $value = __( '(user deleted)', 'affiliate-wp' );
        }

        return apply_filters( 'affwp_reports_table_username', $value, $affiliate );

    }

    /**
     * Render the earnings column
     *
     * @access public
     * @since 1.8
     * @param array $affiliate Contains all the data for the earnings column
     * @return string earnings link
     */
    function column_earnings( $affiliate ) {

        $value = affwp_currency_filter( affwp_format_amount( affwp_get_affiliate_earnings( $affiliate->affiliate_id ) ) );
        return apply_filters( 'affwp_reports_table_earnings', $value, $affiliate );
    }

    /**
     * Render the rate column
     *
     * @access public
     * @since 1.8
     * @param array $affiliate Contains all the data for the earnings column
     * @return string earnings link
     */
    function column_rate( $affiliate ) {
        $value = affwp_get_affiliate_rate( $affiliate->affiliate_id, true );
        return apply_filters( 'affwp_reports_table_rate', $value, $affiliate );
    }


    /**
     * Render the unpaid referrals column
     *
     * @access public
     * @since 1.7.5
     * @param array $affiliate Contains all the data for the unpaid referrals column
     * @return string unpaid referrals link
     */
    function column_unpaid( $affiliate ) {
        $unpaid_count = affiliate_wp()->referrals->unpaid_count( '', $affiliate->affiliate_id );

        $value = '<a href="' . admin_url( 'admin.php?page=affiliate-wp-referrals&affiliate_id=' . $affiliate->affiliate_id . '&status=unpaid' ) . '">' . $unpaid_count . '</a>';
        return apply_filters( 'affwp_reports_table_unpaid', $value, $affiliate );
    }


    /**
     * Render the referrals column
     *
     * @access public
     * @since 1.8
     * @param array $affiliate Contains all the data for the referrals column
     * @return string referrals link
     */
    function column_referrals( $affiliate ) {
        $value = '<a href="' . admin_url( 'admin.php?page=affiliate-wp-referrals&affiliate_id=' . $affiliate->affiliate_id . '&status=paid' ) . '">' . $affiliate->referrals . '</a>';
        return apply_filters( 'affwp_reports_table_referrals', $value, $affiliate );
    }

    /**
     * Render the visits column
     *
     * @access public
     * @since 1.8
     * @param array $affiliate Contains all the data for the visits column
     * @return string visits link
     */
    function column_visits( $affiliate ) {
        $value = '<a href="' . admin_url( 'admin.php?page=affiliate-wp-visits&affiliate=' . $affiliate->affiliate_id ) . '">' . affwp_get_affiliate_visit_count( $affiliate->affiliate_id ) . '</a>';
        return apply_filters( 'affwp_reports_table_visits', $value, $affiliate );
    }

    /**
     * Render the user registered column
     *
     * @access public
     * @since 1.8
     * @param array $affiliate Contains all the data for the registered column
     * @return string visits link
     */
    function column_registered( $affiliate ) {
        $value = get_userdata( $affiliate->affiliate_id )->user_registered;
        return apply_filters( 'affwp_reports_table_registered', $value, $affiliate );
    }

    /**
     * Message to be displayed when there are no items
     *
     * @since  1.7.2
     * @access public
     */
    function no_items() {
        _e( 'No affiliates found.', 'affiliate-wp' );
    }

    /**
     * Returns boolean indicating whether
     * an affiliate was registered between two datetime ranges given.
     * The affiliate id, start date, and end date must be specified.
     *
     * @since   1.8
     * @access  public
     * @param   $reg_start_date  The beginning datetime to search against
     * @param   $reg_end_date    The ending datetime to search against
     * @return  boolean          Indicates whether an affiliate was registered during this period.
     */
    function was_affiliate_registered_between( $start = '', $end = '' ) {

        $user_query = '';

        $args = array(
            'date_registered' => $registered,
            'orderby'         => 'user_registered',
            'date_query'      => array(
                array( 'before'    => $end,
                       'after'     => $start,
                       'inclusive' => true,
                )
            )
        );

        $user_query = new WP_User_Query( $args );
    }

    /**
     * Retrieve the discount code counts
     *
     * @access public
     * @since  1.8
     * @return void
     */
    public function get_affiliate_counts() {

        $search               = isset( $_GET['s'] )           ? sanitize_text_field( $_GET['s'] )          : null;

        $this->active_count   = affiliate_wp()->affiliates->count( array( 'status' => 'active', 'search' => $search ) );
        $this->inactive_count = affiliate_wp()->affiliates->count( array( 'status' => 'inactive', 'search' => $search ) );
        $this->pending_count  = affiliate_wp()->affiliates->count( array( 'status' => 'pending', 'search' => $search ) );
        $this->rejected_count = affiliate_wp()->affiliates->count( array( 'status' => 'rejected', 'search' => $search ) );
        $this->total_count    = $this->active_count + $this->inactive_count + $this->pending_count + $this->rejected_count;
    }

    /**
     * Retrieve all the data for all the Affiliates
     *
     * @access public
     * @since 1.8
     * @return array $affiliate_data Array of all the data for the Affiliates
     */
    public function affiliate_data() {

        $search              = isset( $_GET['s'] )              ? sanitize_text_field( $_GET['s'] ) : null;
        $page                = isset( $_GET['paged'] )          ? absint( $_GET['paged'] ) : 1;
        $status              = isset( $_GET['status'] )         ? $_GET['status']          : '';
        $order               = isset( $_GET['order'] )          ? $_GET['order']           : 'DESC';
        $orderby             = isset( $_GET['orderby'] )        ? $_GET['orderby']         : 'affiliate_id';
        $start_date          = isset( $_GET['start-date'] )     ? sanitize_text_field( $_GET['start-date'] ) : null;
        $end_date            = isset( $_GET['end-date'] )       ? sanitize_text_field( $_GET['end-date'] )   : $start_date;
        $reg_start_date      = isset( $_GET['reg-start-date'] ) ? sanitize_text_field( $_GET['reg-start-date'] ) : null;
        $reg_end_date        = isset( $_GET['reg-end-date'] )   ? sanitize_text_field( $_GET['reg-end-date'] )   : null;
        $earnings_start_date = isset( $_GET['earnings-start-date'] ) ? sanitize_text_field( $_GET['earnings-start-date'] ) : null;
        $earnings_end_date   = isset( $_GET['earnings-end-date'] )   ? sanitize_text_field( $_GET['earnings-end-date'] )   : $earnings_start_date;

        $per_page            = $this->get_items_per_page( 'affwp_reports_items_per_page', $this->per_page );

        $affiliates          = affiliate_wp()->affiliates->get_affiliates( array(
            'number'              => $per_page,
            'offset'              => $per_page * ( $page - 1 ),
            'status'              => $status,
            'search'              => $search,
            'orderby'             => sanitize_text_field( $orderby ),
            'order'               => sanitize_text_field( $order ),
            'start_date'          => $start_date,
            'end_date'            => $end_date,
            'date_registered'     => $this->was_affiliate_registered_between( $reg_start_date, $reg_end_date ),
            'earnings_start_date' => $earnings_start_date,
            'earnings_end_date'   => $earnings_end_date

        ) );
        return $affiliates;
    }

    /**
     * Setup the final data for the table
     *
     * @access public
     * @since 1.8
     * @uses AffWP_Reports_Table::get_columns()
     * @uses AffWP_Reports_Table::get_sortable_columns()
     * @uses AffWP_Reports_Table::process_bulk_action()
     * @uses AffWP_Reports_Table::affiliate_data()
     * @uses WP_List_Table::get_pagenum()
     * @uses WP_List_Table::set_pagination_args()
     * @return void
     */
    public function prepare_items() {
        $per_page = $this->get_items_per_page( 'affwp_reports_screen_options', $this->per_page );

        $columns = $this->get_columns();

        $hidden = array();

        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array( $columns, $hidden, $sortable );

        $data = $this->affiliate_data();

        $current_page = $this->get_pagenum();

        $status = isset( $_GET['status'] ) ? $_GET['status'] : 'any';

        switch( $status ) {
            case 'active':
                $total_items = $this->active_count;
                break;
            case 'inactive':
                $total_items = $this->inactive_count;
                break;
            case 'pending':
                $total_items = $this->pending_count;
                break;
            case 'rejected':
                $total_items = $this->rejected_count;
                break;
            case 'any':
                $total_items = $this->total_count;
                break;
        }

        $this->items = $data;

        $this->set_pagination_args( array(
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil( $total_items / $per_page )
            )
        );
    }
}
