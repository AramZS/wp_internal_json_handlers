<?php 
if (!class_exists('ZS_JSON_Workers')){
	class ZS_JSON_Workers {
		
		public static $ver = '1.0.0';
		
		public static function init() {
            static $instance;

            if ( ! is_a( $instance, 'ZS_JSON_Workers' ) ) {
                $instance = new self();
            }

            return $instance;
        }  
		
		public function __construct() {
			#Stuff
			
		}
		
		public function head(){
			header('Content-Type: application/json');
		}
		
		private function handle($json_result, $error_reporting){
			if ($error_reporting && !$json_result){
				return json_last_error_msg();
			}
			return $json_result;		
		}
			
		public function create($args, $error_reporting = false){
			$json = json_encode($args);
			return self::handle($json, $error_reporting);
		}
		
		public function create_the($args, $error_reporting = false){
			echo self::to($args, $error_reporting);
			return;
		}
		
		public function take($json, $is_array = false, $error_reporting = false){
			$php_ready = json_decode($json, $is_array);
			return self::handle($php_ready, $error_reporting);
		}
		
		public function take_the($json, $is_array, $error_reporting){
			echo self::take($json, $is_array, $error_reporting);
			return;
		}
		
		public function post($url, $args = array(), $settings = array(), $use_get = false, $error_reporting = false){
			if (!$use_get){
				return self::post_by_post($url, $args, $settings);
			} else {
				return self::post_by_get($url, $args, $settings);
			}
		}
		
		private function post_returned($result, $error_reporting = false){
			if (is_wp_error($result)){
				wp_die($result->get_error_message());
			} elseif (WP_DEBUG){
				wp_naytev()->util->logger($result);
			}
			$r = $result['body'];
			return $r;
		}
		
		private function post_by_post($url, $args, $settings = array()){
				$default_post_args = array(
					'method' => 'POST',
					'timeout' => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => array(),
					'body' => array(),
					'cookies' => array()
				);
				$post_args = wp_parse_args($settings, $default_post_args);
				$post_args['body'] = $args;
				$result = wp_remote_post($url, $post_args);
				$posted = self::post_returned($result, false);
				return $posted;			
		}
		
		private function post_by_get($url, $args, $settings = array()){
			$url_complete = add_query_arg($args, $url);
			$result = wp_remote_get($url_complete, $settings);	
			$posted = self::post_returned($result, false);
			return $posted;
		}
		
		public function get($url, $args, $settings = array()){
			return self::post($url, $args, $settings, true);		
		}
	}
}
