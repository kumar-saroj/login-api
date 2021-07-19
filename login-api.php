<?php
/////////// Login Api /////////////
add_action( 'rest_api_init', 'register_api_hooks' );

function register_api_hooks() {
  register_rest_route(
    'recite', '/login/',
    array(
      'methods'  => 'POST',
      'callback' => 'login',
    )
  );
}

function login($request){
    $creds = array();
    $creds['user_login'] = $request["username"];
    $creds['user_password'] =  md5($request["password"]);
    $creds['remember'] = true;
    //$user = wp_signon( $creds, false );
    //return "hhhhhhhhhhhhhhhh";die(0);
    global $wpdb;
    $table_name = $wpdb->prefix . "users";
   // return "SELECT * FROM $table_name WHERE user_email='".$request['username']."'";
   $query = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_email='".$request['username']."' OR user_login='".$request['username']."'");
   $password_hash= $query[0]->user_pass;
   $checkpass = wp_check_password($request["password"],$password_hash,$query[0]->ID);
  
  if(!$checkpass)
  {
    $json = array('code'=>'0','msg'=>'Email & Password Not Matched!');
     echo json_encode($json);
     exit; 
  }
	else
	{
    //wp_send_json_success($query); 
      $userid = $query[0]->ID;
	    $user_meta=get_userdata($userid);
      
			$return = array(
			'ID'      => $userid,
			'username'      => $query[0]->user_login,
			'email'      => $query[0]->user_email,
			'role'      => $user_meta->roles[0]
		);
		wp_send_json_success( $return );
	}
}
?>
