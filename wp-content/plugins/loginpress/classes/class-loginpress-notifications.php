<?php
if ( ! defined( 'ABSPATH' ) ) {
  // Exit if accessed directly.
  exit;
}

/**
* Handling all the Notification calls in LoginPress.
*
* @since  1.1.14
* @class LoginPress_Notification
*/

if ( ! class_exists( 'LoginPress_Notification' ) ) :

  class LoginPress_Notification {

    /* * * * * * * * * *
    * Class constructor
    * * * * * * * * * */
    public function __construct() {

      $this->_hooks();
    }


    /**
    * Hook into actions and filters
    * @since  1.0.0
    */
    private function _hooks() {
      add_action( 'admin_init',             array( $this, 'loginpress_review_notice' ) );
      // add_action( 'admin_init' ,            array( $this, 'loginpress_addon_notice' ) );
      add_action( 'admin_init', array( $this, 'loginpress_friday_sale_notice' ) );
    }

    /**
  	 * Ask users to review our plugin on wordpress.org
  	 *
  	 * @since 1.0.11
  	 * @return boolean false
  	 * @version 1.1.3
  	 */
  	public function loginpress_review_notice() {

  		$this->loginpress_review_dismissal();
  		$this->loginpress_review_pending();

  		$activation_time 	= get_site_option( 'loginpress_active_time' );
  		$review_dismissal	= get_site_option( 'loginpress_review_dismiss' );

  		if ( 'yes' == $review_dismissal ) return;

  		if ( ! $activation_time ) :

  			$activation_time = time();
  			add_site_option( 'loginpress_active_time', $activation_time );
  		endif;

  		// 1296000 = 15 Days in seconds.
  		if ( time() - $activation_time > 1296000 ) :

        wp_enqueue_style( 'loginpress_review_stlye', plugins_url( '../css/style-review.css', __FILE__ ), array(), LOGINPRESS_VERSION );
  			add_action( 'admin_notices' , array( $this, 'loginpress_review_notice_message' ) );
  		endif;

  	}


    /**
  	 *	Check and Dismiss review message.
  	 *
  	 *	@since 1.0.11
  	 */
  	private function loginpress_review_dismissal() {

  		if ( ! is_admin() ||
  			! current_user_can( 'manage_options' ) ||
  			! isset( $_GET['_wpnonce'] ) ||
  			! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'loginpress-review-nonce' ) ||
  			! isset( $_GET['loginpress_review_dismiss'] ) ) :

  			return;
  		endif;

  		add_site_option( 'loginpress_review_dismiss', 'yes' );
  	}

    /**
  	 * Set time to current so review notice will popup after 14 days
  	 *
  	 * @since 1.0.11
  	 */
  	function loginpress_review_pending() {

  		if ( ! is_admin() ||
  			! current_user_can( 'manage_options' ) ||
  			! isset( $_GET['_wpnonce'] ) ||
  			! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'loginpress-review-nonce' ) ||
  			! isset( $_GET['loginpress_review_later'] ) ) :

  			return;
  		endif;

  		// Reset Time to current time.
  		update_site_option( 'loginpress_active_time', time() );
  	}

    /**
  	 * Review notice message
  	 *
  	 * @since  1.0.11
  	 */
  	public function loginpress_review_notice_message() {

  		$scheme      = ( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY ) ) ? '&' : '?';
  		$url         = $_SERVER['REQUEST_URI'] . $scheme . 'loginpress_review_dismiss=yes';
  		$dismiss_url = wp_nonce_url( $url, 'loginpress-review-nonce' );

  		$_later_link = $_SERVER['REQUEST_URI'] . $scheme . 'loginpress_review_later=yes';
  		$later_url   = wp_nonce_url( $_later_link, 'loginpress-review-nonce' );
      ?>

  		<div class="loginpress-review-notice">
  			<div class="loginpress-review-thumbnail">
  				<img src="<?php echo plugins_url( '../img/thumbnail/gray-loginpress.png', __FILE__ ) ?>" alt="">
  			</div>
  			<div class="loginpress-review-text">
  				<h3><?php _e( 'Leave A Review?', 'loginpress' ) ?></h3>
  				<p><?php _e( 'We hope you\'ve enjoyed using LoginPress! Would you consider leaving us a review on WordPress.org?', 'loginpress' ) ?></p>
  				<ul class="loginpress-review-ul">
            <li><a href="https://wordpress.org/support/view/plugin-reviews/loginpress?rate=5#postform" target="_blank"><span class="dashicons dashicons-external"></span><?php _e( 'Sure! I\'d love to!', 'loginpress' ) ?></a></li>
            <li><a href="<?php echo $dismiss_url ?>"><span class="dashicons dashicons-smiley"></span><?php _e( 'I\'ve already left a review', 'loginpress' ) ?></a></li>
            <li><a href="<?php echo $later_url ?>"><span class="dashicons dashicons-calendar-alt"></span><?php _e( 'Maybe Later', 'loginpress' ) ?></a></li>
            <li><a href="<?php echo $dismiss_url ?>"><span class="dashicons dashicons-dismiss"></span><?php _e( 'Never show again', 'loginpress' ) ?></a></li></ul>
  			</div>
  		</div>
  	<?php
  	}

    /**
     * Review notice message
     *
     * @since  1.1.3
     */
    public function loginpress_addon_notice_text() {

      $scheme      = ( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY ) ) ? '&' : '?';
      $url         = $_SERVER['REQUEST_URI'] . $scheme . 'loginpress_addon_dismiss_1=yes';
      $dismiss_url = wp_nonce_url( $url, 'loginpress-addon-nonce' );
      wp_enqueue_style( 'loginpress_review_stlye', plugins_url( '../css/style-review.css', __FILE__ ), array(), LOGINPRESS_VERSION );
      ?>
      <div class="loginpress-alert-notice">
        <a href="<?php echo $dismiss_url ?>" class="notice-dismiss" ><span class="screen-reader-text"></span></a>
        <a href="https://wpbrigade.com/wordpress/plugins/loginpress/addons/?utm_source=loginpress-lite&utm_medium=addons-notice-banner&utm_campaign=pro-upgrade" class="loginpress-addon-notice-link" target="_blank">
        <div class="loginpress-alert-thumbnail">
          <img src="<?php echo plugins_url( '../img/notification_logo.svg', __FILE__ ) ?>" alt="">
        </div>
        <div class="loginpress-alert-text">
          <h3><?php _e( 'Introducing LoginPress Addons!', 'loginpress' ) ?></h3>
          <p><?php _e( 'Extend LoginPress with these add-ons and supercharge your login pages.', 'loginpress' ) ?></p>
        </div>
        </a>
        <div class="loginpress-alert-button-section">
          <a href="https://wpbrigade.com/wordpress/plugins/loginpress/addons/?utm_source=loginpress-lite&utm_medium=addons-notice-more&utm_campaign=pro-upgrade" class="loginpress-alert-button" target="_blank"><?php _e( 'Learn More', 'loginpress' ) ?></a>
        </div>
      </div>
    <?php
    }

    /**
  	 *	Check and Dismiss addon message.
  	 *
  	 *	@since 1.1.3
  	 */
  	private function loginpress_addon_dismissal() {

  		if ( ! is_admin() ||
  			! current_user_can( 'manage_options' ) ||
  			! isset( $_GET['_wpnonce'] ) ||
  			! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'loginpress-addon-nonce' ) ||
  			! isset( $_GET['loginpress_addon_dismiss_1'] ) ) :

  			return;
  		endif;

  		add_site_option( 'loginpress_addon_dismiss_1', 'yes' );
  	}

    /**
     * Ask users to review our plugin on wordpress.org
     *
     * @since 1.1.3
     * @return boolean false
     */
    public function loginpress_addon_notice() {

      $this->loginpress_addon_dismissal();

      $activation_time 	= get_site_option( 'loginpress_addon_active_time' );
      $addon_dismissal	= get_site_option( 'loginpress_addon_dismiss_1' );

      if ( 'yes' == $addon_dismissal ) return;

      if ( ! $activation_time ) :

        $activation_time = time();
        add_site_option( 'loginpress_addon_active_time', $activation_time );
      endif;

      // 432000 = 5 Days in seconds.
      // if ( time() - $activation_time > 432000 ) :

        add_action( 'admin_notices' , array( $this, 'loginpress_addon_notice_text' ) );
      // endif;

    }

    /**
     * Ask users to review our plugin on wordpress.org
     *
     * @since 1.1.3
     * @return boolean false
     */
    public function loginpress_friday_sale_notice() {

      $this->loginpress_friday_sale_dismissal();

      $activation_time 	= get_site_option( 'loginpress_friday_sale_active_time' );
      $addon_dismissal	= get_site_option( 'loginpress_friday_sale_dismiss' );

      if ( 'yes' == $addon_dismissal ) return;

      if ( ! $activation_time ) :

        $activation_time = time();
        add_site_option( 'loginpress_friday_sale_active_time', $activation_time );
      endif;

      // 432000 = 5 Days in seconds.
      // if ( time() - $activation_time > 432000 ) :

        add_action( 'admin_notices' , array( $this, 'loginpress_friday_sale_notice_text' ) );
      // endif;

    }

    /**
     * Review notice message
     *
     * @since  1.1.14
     */
    public function loginpress_friday_sale_notice_text() {

      $scheme      = ( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY ) ) ? '&' : '?';
      $url         = $_SERVER['REQUEST_URI'] . $scheme . 'loginpress_friday_sale_dismiss=yes';
      $dismiss_url = wp_nonce_url( $url, 'loginpress-friday-sale-nonce' );

      wp_enqueue_style( 'loginpress_review_stlye', plugins_url( '../css/style-review.css', __FILE__ ), array(), LOGINPRESS_VERSION );
      ?>
      <div class="loginpress-alert-notice black_friday_notic">
        <a href="<?php echo $dismiss_url ?>" class="notice-dismiss" ><span class="screen-reader-text"></span></a>
        <a href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=freepluginbanner&utm_campaign=blackfriday2018" target="_blank">
        <div class="loginpress-alert-thumbnail">
          <img src="<?php echo plugins_url( '../img/notification_logo.svg', __FILE__ ) ?>" alt="">
        </div>
        <div class="loginpress-alert-text black_friday">
          <img src="<?php echo plugins_url( '../img/black-friday.png', __FILE__ ) ?>" alt="">
        
         <div class="loginpress-alert-button-section black_friday_sale_btn">
          <a href="https://wpbrigade.com/wordpress/plugins/loginpress-pro/?utm_source=loginpress-lite&utm_medium=freepluginbanner-button&utm_campaign=blackfriday2018" class="loginpress-alert-button" target="_blank"><?php _e( 'GET ALL 51% OFF', 'loginpress' ) ?></a>
        </div>  
        </div>
        </a>
       
      </div>
    <?php
    }

    /**
  	 *	Check and Dismiss addon message.
  	 *
  	 *	@since 1.1.3
  	 */
  	private function loginpress_friday_sale_dismissal() {

  		if ( ! is_admin() ||
  			! current_user_can( 'manage_options' ) ||
  			! isset( $_GET['_wpnonce'] ) ||
  			! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'loginpress-friday-sale-nonce' ) ||
  			! isset( $_GET['loginpress_friday_sale_dismiss'] ) ) :

  			return;
  		endif;

  		add_site_option( 'loginpress_friday_sale_dismiss', 'yes' );
  	}

  }

endif;
new LoginPress_Notification();
?>
