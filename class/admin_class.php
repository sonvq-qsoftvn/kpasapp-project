<?php 
  class admin extends DB_Sql 
{
    function getEventByArrayID($array_event_id)
    {
        $sql = "select E.event_id, T.county_name, V.venue_name, V.venue_name_sp, V.venue_id, "
                . "C.city_name, S.state_name, "
                . "E.event_name_en, E.event_name_sp, E.event_start_date_time, "
                . "E.event_start_ampm, E.event_end_ampm, E.r_span_end, "
                . "E.event_short_desc_en, E.event_short_desc_sp, "
                . "E.event_details_en, E.event_details_sp, E.event_end_date_time from " . 
                $this->prefix() . "general_events E LEFT join " . 
                $this->prefix() . "venue V on (E.event_venue = V.venue_id ) LEFT join " . 
                $this->prefix() . "city C on (E.event_venue_city = C.id ) LEFT join " . 
                $this->prefix() . "state S on (S.id = V.venue_state ) LEFT join " . 
                $this->prefix() . "county T on (T.id = E.event_venue_county) where E.event_id IN (" . 
                $array_event_id . ") ORDER BY E.event_start_date_time ASC";
        
        $this->query($sql);
    }

        function add_page($page_name, $title_sp, $page_content, $page_content_sp, $page_link, 
                $social, $path, $file_name, $publish) {
            $sql = "INSERT INTO " . $this->prefix() . "page set page_name='" . $page_name . 
                    "',title_sp='" . $title_sp . "',page_content='" . $page_content . 
                    "',page_content_sp='" . $page_content_sp . "',page_link='" . $page_link . 
                    "',path='" . $path . "',social='" . $social . "',photo='" . $file_name . 
                    "',create_time='" . time() . "',publish='" . $publish . "'";

            $this->query($sql);
        }	

		function getAdminUserDetails($user_id)
		{
			$sql = "SELECT * FROM ".$this->prefix()."admin WHERE admin_id=".$user_id;
			$this->query($sql);
			if($this->num_rows() > 0)
			{
				return 1;
			}
			return 0;
		}
		function updateAdminUserDetails($new_password,$user_id)
		{
			$sql="update ".$this->prefix()."admin set password='".$new_password."' WHERE  admin_id=".$user_id;
			$this->query($sql);
			return 1;
		}
        
        function update_user_venue($selected_venue, $selected_venue_state, $selected_venue_county, $selected_venue_city, $user_id) {
            $sql="update " . $this->prefix() . "admin set selected_venue='".
                    $selected_venue."', selected_venue_state='".
                    $selected_venue_state."', selected_venue_county='".
                    $selected_venue_county."', selected_venue_city='".
                    $selected_venue_city. "' WHERE admin_id=".$user_id;
			$this->query($sql);
			return 1;
        }
        
        function get_user_venue($user_id) {
            $sql = "SELECT selected_venue, selected_venue_state, selected_venue_county, selected_venue_city FROM ".$this->prefix()."admin WHERE admin_id=".$user_id;
			$this->query($sql);
        }
//==================================== End ============================================
// ================================== thumb nail ==================================

function create_thumbnail($infile,$outfile,$maxw,$maxh,$stretch = FALSE) {
  clearstatcache();
  if (!is_file($infile)) {
    trigger_error("Cannot open file: $infile",E_USER_WARNING);
    return FALSE;
  }
  if (is_file($outfile)) {
      trigger_error("Output file already exists: $outfile",E_USER_WARNING);
    return FALSE;
  }
 
  $functions = array(
    'image/png' => 'ImageCreateFromPng',
    'image/jpeg' => 'ImageCreateFromJpeg',
  );
 
  // Add GIF support if GD was compiled with it
  if (function_exists('ImageCreateFromGif')) { $functions['image/gif'] = 'ImageCreateFromGif'; }
 
  $size = getimagesize($infile);
 
  // Check if mime type is listed above
  if (!$function = $functions[$size['mime']]) {
      trigger_error("MIME Type unsupported: {$size['mime']}",E_USER_WARNING);
    return FALSE;
  }
 
  // Open source image
  if (!$source_img = $function($infile)) {
      trigger_error("Unable to open source file: $infile",E_USER_WARNING);
    return FALSE;
  }
 
  $save_function = "image" . strtolower(substr(strrchr($size['mime'],'/'),1));
 
  // Scale dimensions
  list($neww,$newh) = $this->scale_dimensions($size[0],$size[1],$maxw,$maxh,$stretch);
 
  if ($size['mime'] == 'image/png') {
    // Check if this PNG image is indexed
    $temp_img = imagecreatefrompng($infile);
    if (imagecolorstotal($temp_img) != 0) {
      // This is an indexed PNG
      $indexed_png = TRUE;
    } else {
      $indexed_png = FALSE;
    }
    imagedestroy($temp_img);
  }
 
  // Create new image resource
  if ($size['mime'] == 'image/gif' || ($size['mime'] == 'image/png' && $indexed_png)) {
    // Create indexed 
    $new_img = imagecreate($neww,$newh);
    // Copy the palette
    imagepalettecopy($new_img,$source_img);
 
    $color_transparent = imagecolortransparent($source_img);
    if ($color_transparent >= 0) {
      // Copy transparency
      imagefill($new_img,0,0,$color_transparent);
      imagecolortransparent($new_img, $color_transparent);
    }
  } else {
    $new_img = imagecreatetruecolor($neww,$newh);
  }
 
  // Copy and resize image
  imagecopyresampled($new_img,$source_img,0,0,0,0,$neww,$newh,$size[0],$size[1]);
 
  // Save output file
  if ($save_function == 'imagejpeg') {
      // Change the JPEG quality here
      if (!$save_function($new_img,$outfile,75)) {
          trigger_error("Unable to save output image",E_USER_WARNING);
          return FALSE;
      }
  } else {
      if (!$save_function($new_img,$outfile)) {
          trigger_error("Unable to save output image",E_USER_WARNING);
          return FALSE;
      }
  }
 
  // Cleanup
  imagedestroy($source_img);
  imagedestroy($new_img);
 
  return TRUE;
}
// Scales dimensions
function scale_dimensions($w,$h,$maxw,$maxh,$stretch = FALSE) {
    if (!$maxw && $maxh) {
      // Width is unlimited, scale by width
      $newh = $maxh;
      if ($h < $maxh && !$stretch) { $newh = $h; }
      else { $newh = $maxh; }
      $neww = ($w * $newh / $h);
    } elseif (!$maxh && $maxw) {
      // Scale by height
      if ($w < $maxw && !$stretch) { $neww = $w; }
      else { $neww = $maxw; }
      $newh = ($h * $neww / $w);
    } elseif (!$maxw && !$maxh) {
      return array($w,$h);
    } else {
      if ($w / $maxw > $h / $maxh) {
        // Scale by height
        if ($w < $maxw && !$stretch) { $neww = $w; }
        else { $neww = $maxw; }
        $newh = ($h * $neww / $w);
      } elseif ($w / $maxw <= $h / $maxh) {
        // Scale by width
        if ($h < $maxh && !$stretch) { $newh = $h; }
        else { $newh = $maxh; }
        $neww = ($w * $newh / $h);
      }
    }
    return array(round($neww),round($newh));
}

// ================================== End ==================================
//=====================================  crop image ===========
function cropimage($filename,$crop_width,$crop_height){
// Original image

 
// Get dimensions of the original image
list($current_width, $current_height) = getimagesize($filename);
 
// The x and y coordinates on the original image where we
// will begin cropping the image
$left = 10;
$top = 10;
 
// This will be the final size of the image (e.g. how many pixels
// left and down we will be going)
//$crop_width = 200;
//$crop_height = 200;
 
// Resample the image
$canvas = imagecreatetruecolor($crop_width, $crop_height);
$current_image = imagecreatefromjpeg($filename);
imagecopy($canvas, $current_image, 0, 0, $left, $top, $current_width, $current_height);
imagejpeg($canvas, $filename, 100);
}
// ================================== End ==================================



	

//====================================================== generate Password =======================================================
	function str_rand($length = 8, $seeds = 'alphanum')
		{
			// Possible seeds
			$seedings['alpha'] = 'abcdefghijklmnopqrstuvwqyz';
			$seedings['numeric'] = '0123456789';
			$seedings['alphanum'] = 'abcdefghijklmnopqrstuvwqyz0123456789';
			$seedings['hexidec'] = '0123456789abcdef';
			
			// Choose seed
			if (isset($seedings[$seeds]))
			{
				$seeds = $seedings[$seeds];
			}
			
			// Seed generator
			list($usec, $sec) = explode(' ', microtime());
			$seed = (float) $sec + ((float) $usec * 100000);
			mt_srand($seed);
			
			// Generate
			$str = '';
			$seeds_count = strlen($seeds);
			
			for ($i = 0; $length > $i; $i++)
			{
				$str .= $seeds{mt_rand(0, $seeds_count - 1)};
			}
			
			return $str;
		}
	//====================================================== end =======================================================
	//====================================================== admin setting =======================================================
	function admin_setting(){
	
			$sql = "SELECT * FROM ".$this->prefix()."setting WHERE id=1" ;
			$this->query($sql);
			
	}
	//====================================================== end =======================================================
	
	//====================================================== admin setting =======================================================
	function getAdminByemail_pass($email,$recover_pass)
		{
			$sql = "select * from ".$this->prefix()."admin WHERE email='".$email."' AND recover_pass='".$recover_pass."'";
			$this->query($sql);
			
		}
	function update_admin_pass($recover_pass,$password)
		{
			//$recover_pass=$this->str_rand();
			$sql="UPDATE ".$this->prefix()."admin SET password='".md5($password)."'
			WHERE recover_pass='".$recover_pass."'";
			$rs=$this->query($sql);
						
		}		
	//====================================================== end =======================================================
	
	



//==================================== End ============================================
// =========================== mail ================================	
	/*function send_mail($from,$to,$subject,$body,$name=''){
		$sql="SELECT * FROM ".$this->prefix()."setting WHERE id=1 ";
		$this->query($sql);
		$this->next_record();
		
		if($this->f('smtp_active')==1){
			require_once "Mail.php";
			
			$from1 = $name." <".$from.">";			
			$host = $this->f('smtp_host');
			$port = $this->f('smtp_port');
			$username = $this->f('smtp_username');
			$password = $this->f('smtp_password');
	 
			$headers = array("MIME-Version"=> '1.0', 
			"Content-type" => "text/html; charset=iso-8859-1",
			"From" => $from,
			"To" => $to, 
			"Subject" => $subject);			
			
			$smtp = Mail::factory('smtp',
			array ('host' => $host,
			'port' => $port,
			'auth' => true,
			'username' => $username,
			'password' => $password));			 
			$mail = $smtp->send($to, $headers, $body);
			
		}
		else{
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: '.$name.' <'.$from.'>' . "\r\n";
			// Mail it
			mail($to, $subject, $body, $headers);
		}
	
	}
	
	*/
	
	function send_mail($from,$to,$subject,$body,$name='',$attachment=false,$filename=false,$reply_to=false){
		$sql="SELECT * FROM ".$this->prefix()."setting WHERE id=1 ";
		$this->query($sql);
		$this->next_record();
		
		if($this->f('smtp_active')==1){
			require_once "Mail.php";
			
			$from1 = $name." <".$from.">";			
			$host = $this->f('smtp_host');
			$port = $this->f('smtp_port');
			$username = $this->f('smtp_username');
			$password = $this->f('smtp_password');
	 
			$headers = array("MIME-Version"=> '1.0', 
			"Content-type" => "text/html; charset=iso-8859-1",
			"From" => $from,
			"To" => $to, 
			"Subject" => $subject);			
			
			$smtp = Mail::factory('smtp',
			array ('host' => $host,
			'port' => $port,
			'auth' => true,
			'username' => $username,
			'password' => $password));			 
			$mail = $smtp->send($to, $headers, $body);
			
		}
		else{
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: '.$name.' <'.$from.'>' . "\r\n";
			if($reply_to){
				$headers .= 'Reply-To: Ticket Hype <'.$reply_to.'>' . "\r\n";
			}
			// Mail it
			if($attachment){
				
				$separator = md5(time());
				// carriage return type (we use a PHP end of line constant)
				$eol = PHP_EOL;
				// main header (multipart mandatory)
				$headers = "From: ".$name." <".$from.">".$eol;
				$headers .= "MIME-Version: 1.0".$eol;
				$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"".$eol.$eol;
				$headers .= "Content-Transfer-Encoding: 7bit".$eol;
				
				 
				// message
				$headers .= "--".$separator.$eol;
				$headers .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
				$headers .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
				$headers .= $body.$eol.$eol;
				 
				// attachment
				$headers .= "--".$separator.$eol;
				$headers .= "Content-Type: application/octet-stream; name=\"".$filename."\"".$eol;
				$headers .= "Content-Transfer-Encoding: base64".$eol;
				$headers .= "Content-Disposition: attachment".$eol.$eol;
				$headers .= $attachment.$eol.$eol;
				$headers .= "--".$separator."--";
				
			}
			if(mail($to, $subject, $body, $headers)){
			//if(mail('unified.ujjal@gmail.com', $subject, $body, $headers)){
				return true;
			}else{
				
			return false;	
			}
		}
	
	}

	// =========================== end ================================	
		
// ========================================================End=============================================





	function api_show()
	{
		$sql = "select * from ".$this->prefix()."setting " ;
		$this->query($sql);
	}

//========================= Admin login===================================
function check_admin_user($user_name,$pasword)
{
		//echo $this->prefix(); exit;
		$sql = "SELECT * FROM ".$this->prefix()."admin a WHERE (a.email = '".$user_name."' OR a.phone = '".$user_name."') AND a.password = '".md5($pasword)."' AND a.`activate_status` = 1 AND a.`account_type`!= 0 ";
		//echo $sql;exit;
		$this->query($sql);
		 if($this->num_rows()>0)
			{
			   return 1;
			}
			   return 0;
}
//============= Admin change password =========== 

    function getAccountTypeByUserId($user_id) {
        $sql = "SELECT * FROM ".$this->prefix()."admin a WHERE a.admin_id = '" . $user_id . "'";
        return $this->query($sql);
    }
    
    function check_is_admin_user_by_name($user_name) {
        //echo $this->prefix(); exit;
        $sql = "SELECT * FROM " . $this->prefix() . "admin a WHERE (a.email = '" . $user_name . "' OR a.phone = '" . $user_name . "') AND a.`activate_status` = 1 AND a.`account_type`!= 0 ";
        //echo $sql;exit;
        $this->query($sql);
        if ($this->num_rows() > 0) {
            return 1;
        }
        return 0;
    }
    
    function check_is_admin_user_by_id($admin_id) {
        //echo $this->prefix(); exit;
        $sql = "SELECT * FROM " . $this->prefix() . "admin a WHERE a.admin_id = '" . $admin_id . "' AND a.`activate_status` = 1 AND a.`account_type`!= 0 ";
        //echo $sql;exit;
        $this->query($sql);
        if ($this->num_rows() > 0) {
            return 1;
        }
        return 0;
    }

    function event_category_list($flag=false){
	if($flag!="")
	{
		$sql="SELECT * FROM ".$this->prefix()."event_category  ORDER BY category_id LIMIT 0,5";
	}
	else
	{
		$sql="SELECT * FROM ".$this->prefix()."event_category  ORDER BY category_name LIMIT 0,5";
	}
	//echo $sql;
	return $this->query($sql);
}
function getVenueById($venue_id,$organization_id){

	$sql = "SELECT * from ".$this->prefix()."venue WHERE venue_id='".$venue_id."' AND organization_id=".$organization_id;
	$this->query($sql);
}



//==================================== Dashboard ============================================
function event_list_upcoming($organization_id,$limit)
{
	$sql="SELECT * FROM ".$this->prefix()."events WHERE organization_id='".$organization_id."' AND now() < event_date   $limit";
	$this->query($sql);
}		
function event_list_upcoming_num($organization_id)
{
	$sql="SELECT * FROM ".$this->prefix()."events  WHERE organization_id='".$organization_id."' AND now() < event_date  ORDER BY event_id ";
	$this->query($sql);
}		
function total_sale_on_day($organization_id,$date)
{			
	$sql="SELECT * FROM ".$this->prefix()."order a INNER JOIN ".$this->prefix()."organization_sale b ON a.order_id=b.order_id  WHERE b.organization_id='".$organization_id."' AND a.date LIKE '".$date."%' ";
	$this->query($sql);
}
function total_sale_Ticket_on_day($organization_id,$date)
{			
	$sql="SELECT * FROM ".$this->prefix()."order a INNER JOIN ".$this->prefix()."organization_sale b ON a.order_id=b.order_id  INNER JOIN ".$this->prefix()."order_detail c ON c.order_id=a.order_id  WHERE b.organization_id='".$organization_id."' AND a.date LIKE '".$date."%' ";
	$this->query($sql);
}			
			
//==================================== End ============================================

function getAdminById($admin_id)
{
	$sql = "select * from ".$this->prefix()."admin A join ".$this->prefix()."countries D ON A.country_id=D.id WHERE admin_id=".$admin_id;
	$this->query($sql);
	
}

function event_total_sold_ticket($event_id)
{
	$sql="SELECT SUM(ticket_sold) as total_sold_event FROM ".$this->prefix()."price_level WHERE event_id='".$event_id."' GROUP BY event_id  ";
	$this->query($sql);
}	

function adminBillingDetails($admin_id){
	$sql = "SELECT * FROM ".$this->prefix()."admin_card_billing WHERE admin_id='".$admin_id."'" ;
	$this->query($sql);			
}
	

//=============  event =========== 

	//step 1
	function add_event($event_name,$event_date,$venue,$description,$on_sale_date,$sale_close_date,$category_id,$age,$event_web_site,$event_image,$icon_image,$admin_id,$organization_id,$send_newsletter)
	{
		$sql="INSERT INTO ".$this->prefix()."events set event_name='".$event_name."',event_date='".$event_date."',venue='".$venue."',description='".$description."',on_sale_date='".$on_sale_date."',sale_close_date='".$sale_close_date."',category_id='".$category_id."',age='".$age."',event_web_site='".$event_web_site."',event_image='".$event_image."' ,icon_image='".$icon_image."' ,admin_id='".$admin_id."',organization_id='".$organization_id."',send_newsletter='".$send_newsletter."' ";
		$rs=$this->query($sql);
		return mysql_insert_id();
	}
	
	//event step
	function event_step_no($event_id,$event_step){
		$sql="SELECT * FROM ".$this->prefix()."events  WHERE event_id='".$event_id."'  ";
		$this->query($sql);
		$this->next_record();
		if($event_step > $this->f('event_step')){
			$sql="UPDATE ".$this->prefix()."events SET event_step='".$event_step."' WHERE event_id='".$event_id."'   ";
			$rs=$this->query($sql);
		}
	}

	function edit_event_step_1($event_name,$event_date,$venue,$description,$on_sale_date,$sale_close_date,$category_id,$age,$event_web_site,$event_image,$icon_image,$event_id,$send_newsletter)
{
	$sql="UPDATE ".$this->prefix()."events set event_name='".$event_name."',event_date='".$event_date."',venue='".$venue."',description='".$description."',on_sale_date='".$on_sale_date."',sale_close_date='".$sale_close_date."',category_id='".$category_id."',age='".$age."',event_web_site='".$event_web_site."',event_image='".$event_image."',icon_image='".$icon_image."',send_newsletter='".$send_newsletter."'
	WHERE event_id='".$event_id."'
	  ";
	
	$rs=$this->query($sql);
	return mysql_insert_id();
}

//=============  event Category =========== 

function category_list(){
	
		$sql="SELECT * FROM ".$this->prefix()."event_category Where parent_category = 0  ORDER BY category_name ";
		return $this->query($sql);
	}
function category_sub_list($category_id){
	
		$sql="SELECT * FROM ".$this->prefix()."event_category Where parent_category = ".$category_id."  ORDER BY category_id ";
		//echo $sql;
		return $this->query($sql);
	}
	
	
	
	
	
	function addTempTicket($ticket_name_en,$ticket_name_sp,$description_en,$description_sp,$price_mx,$price_us,$ticket_num,$from_ticket,$to_ticket,$eairly_dis_percen,$eairly_days,$group_dis_per,$group_dis_days,$ticket_icon,$members_only,$unique_id,$add_event_id)
	{
		$sql="INSERT INTO ".$this->prefix()."final_tickets SET 
					event_id = '".$add_event_id."',
					ticket_name_en = '".$ticket_name_en."',
				   ticket_name_sp = '".$ticket_name_sp."',
				   description_en ='".$description_en."',
				   description_sp = '".$description_sp."',
				   price_mx = '".$price_mx."',
				   price_us = '".$price_us."',
				   ticket_num = '".$ticket_num."',
				   from_ticket = '".$from_ticket."',
				   to_ticket = '".$to_ticket."',	
				   eairly_dis_percen = '".$eairly_dis_percen."', 
				   eairly_days ='".$eairly_days."',
				   group_dis_per = '".$group_dis_per."',
				   group_dis_days = '".$group_dis_days."',
				   ticket_icon = '".$ticket_icon."',
				   members_only = '".$members_only."',	
				   unique_id = '".$unique_id."',
				   post_date = '".time()."'";
		$rs=$this->query($sql);	

	}
	


function getVenueState()
{
	$sql="SELECT * FROM ".$this->prefix()."state WHERE active_status = 1 order by state_name";
	return $this->query($sql);
}

function getVenueCounty($state)
{
	$sql="SELECT * FROM ".$this->prefix()."county WHERE state_id = '".$state."'   order by county_name  ASC";
	return $this->query($sql);
}

function getVenueCity($county)
{
	$sql="SELECT * FROM ".$this->prefix()."city WHERE county_id = '".$county."'  order by city_name ASC";
	return $this->query($sql);
}

function getVenueName($city)
{
        $sql="SELECT * FROM ".$this->prefix()."venue WHERE venue_city = '".$city."'   order by venue_name ASC";
	return $this->query($sql);
}

function getCountyNameByState($stateId)
{
	$sql="SELECT * FROM ".$this->prefix()."county WHERE state_id = '".$stateId."' ORDER BY id ASC";
	return $this->query($sql);
}

function getCityNameByCounty($countyId)
{
	$sql="SELECT * FROM ".$this->prefix()."city WHERE county_id = '".$countyId."' ORDER BY city_name ASC";
	return $this->query($sql);
}

function getVenueNameByCity($cityId)
{
        $sql="SELECT * FROM ".$this->prefix()."venue WHERE venue_city = '".$cityId."' ORDER BY venue_name ASC";
	return $this->query($sql);
}


function fetch_temp_tickets($uid)
{
  $sel="select * from  ".$this->prefix()."temporary_tickets where unique_id='".$uid."'";
  $res=mysql_query($sel);
  return $row=mysql_num_rows($res);
}
function addEvent($event_name_sp,$event_name_en,$short_desc_sp,$short_desc_en,$event_start_date_time,$event_start_ampm,$event_end_date_time,$event_end_ampm,$venue_state,$venue_county,$venue_city,$venue,$page_content_en,$page_content_sp,$event_tag,$file_name,$identical_function,$recurring,$sub_events,$Paypal,$Bank,$Oxxo,$Mobile,$Offline,$publish_date,$event_time,$event_time_period,$r_month,$r_month_day,$mon,$tue,$wed,$thu,$fri,$sat,$sun,$r_span_start,$r_span_end,$event_start,$event_end,$all_day,$event_lasts,$attendees,$invitation_only,$password_protect_check,$pass_protected,$radio_access,$pay_ticket_fee,$promo_charge,$paper_less_mob_ticket,$print,$will_call,$status)
{
	//echo "hii".$event_name_en; exit;
	$sql="INSERT INTO ".$this->prefix()."general_events SET 
															admin_id = '".$_SESSION['ses_user_id']."',
															event_name_sp = '".$event_name_sp."',
															event_name_en = '".$event_name_en."',
															
															event_short_desc_en = '".$short_desc_en."',
															event_short_desc_sp = '".$short_desc_sp."',
															
															event_start_date_time ='".$event_start_date_time."',
															event_start_ampm = '".$event_start_ampm."',
															event_end_date_time = '".$event_end_date_time."',
															event_end_ampm = '".$event_end_ampm."',
															event_venue_state = '".$venue_state."',
															event_venue_county = '".$venue_county."',
															event_venue_city = '".$venue_city."',
															event_venue = '".$venue."',
														    event_details_en = '".$page_content_en."',
														    event_details_sp = '".$page_content_sp."',
															event_tag = '".$event_tag."',
															event_photo = '".$file_name."',
														    identical_function = '".$identical_function."',
														    recurring = '".$recurring."',
															sub_events = '".$sub_events."',
															
															Paypal = '".$Paypal."',
															Bank_deposite = '".$Bank."',
															Oxxo_Payment = '".$Oxxo."',
															Mobile_Payment = '".$Mobile."',
															Offline_Payment = '".$Offline."',
															
															publish_date = '".$publish_date."',
															
															event_time = '".$event_time."',
															event_time_period = '".$event_time_period."',
															r_month = '".$r_month."',
															r_month_day = '".$r_month_day."',
															mon = '".$mon."',
															tue = '".$tue."',
															wed = '".$wed."',
															thu = '".$thu."',
															fri = '".$fri."',
															sat = '".$sat."',
															sun = '".$sun."',
															r_span_start = '".$r_span_start."',
															r_span_end = '".$r_span_end."',
															event_start = '".$event_start."',
															event_end = '".$event_end."',
															all_day = '".$all_day."',
															event_lasts = '".$event_lasts."',
															
															attendees_share = '".$attendees."',
															attendees_invitation = '".$invitation_only."',
															password_protect = '".$password_protect_check."',
															password_protect_text = '".$pass_protected."',
															
															all_access = '".$radio_access."',
															include_promotion = '".$promo_charge."',
															include_payment = '".$pay_ticket_fee."',
															
															paper_less_mob_ticket = '".$paper_less_mob_ticket."',
															print = '".$print."',
															will_call = '".$will_call."',
															
															post_date = '".time()."'";
	$rs=$this->query($sql);	
	return $last_event_id=mysql_insert_id();
}

function addCategoryByEvent($finalArray,$last_event_id)
{
   if(count($finalArray)>0)
   {
	   for($a=0;$a<count($finalArray);$a++)
	   {
			$sql="INSERT INTO ".$this->prefix()."category_by_event SET event_id = '".$last_event_id."',
																	   category_id = '".$finalArray[$a]."'";
			$rs=$this->query($sql);	
	   }
   }
}

function addFinalTicket($unique_id)
{
	$sql="INSERT INTO ".$this->prefix()."final_tickets (ticket_name_en, ticket_name_sp,description_en,description_sp,price_mx,price_us,ticket_num,from_ticket,to_ticket,eairly_dis_percen,eairly_days,group_dis_per,group_dis_days,ticket_icon,members_only,post_date,unique_id) (SELECT ticket_name_en, ticket_name_sp,description_en,description_sp,price_mx,price_us,ticket_num,from_ticket,to_ticket,eairly_dis_percen,eairly_days,group_dis_per,group_dis_days,ticket_icon,members_only,post_date,unique_id FROM ".$this->prefix()."temporary_tickets WHERE unique_id = '".$unique_id."')";
	//echo $sql;
	$this->query($sql);
	return $last_event_id=mysql_insert_id();
}


function addFinalTicket2($event_id,$ticket_name_en,$ticket_name_sp,$description_en,$description_sp,$price_mx,$price_us,$ticket_num,$from_ticket,$to_ticket,$eairly_dis_percen,$eairly_days,$group_dis_per,$group_dis_days,$ticket_icon,$members_only,$unique_id)
{
	$sql="INSERT INTO ".$this->prefix()."final_tickets SET ticket_name_en = '".$ticket_name_en."',
														   ticket_name_sp = '".$ticket_name_sp."',
														   description_en ='".$description_en."',
														   description_sp = '".$description_sp."',
														   price_mx = '".$price_mx."',
														   price_us = '".$price_us."',
														   ticket_num = '".$ticket_num."',
														   from_ticket = '".$from_ticket."',
														   to_ticket = '".$to_ticket."',	
														   eairly_dis_percen = '".$eairly_dis_percen."', 
														   eairly_days ='".$eairly_days."',
														   group_dis_per = '".$group_dis_per."',
														   group_dis_days = '".$group_dis_days."',
														   ticket_icon = '".$ticket_icon."',
														   members_only = '".$members_only."',	
														   unique_id = '".$unique_id."',
														   event_id = '".$event_id."',
														   post_date = '".time()."'";
	return $this->query($sql);
}

function deleteTicket($unique_id)
{
	$sql="DELETE FROM ".$this->prefix()."temporary_tickets WHERE unique_id = '".$unique_id."'";
	return $this->query($sql);
}

function deleteFinalTicket($ticket_id)
{
	$sql="DELETE FROM ".$this->prefix()."final_tickets WHERE ticket_id = '".$ticket_id."'";
	return $this->query($sql);
}

function deleteEvent($event_id)
{
	$sql="DELETE FROM ".$this->prefix()."general_events WHERE event_id = '".$event_id."'";
	return $this->query($sql);
}
function deleteCategoryByEvent($event_id)
{
	$sql="DELETE FROM ".$this->prefix()."category_by_event WHERE event_id = '".$event_id."'";
	return $this->query($sql);
}
function deleteTicketByEvent($event_id)
{
	$sql="DELETE FROM ".$this->prefix()."final_tickets WHERE event_id = '".$event_id."'";
	return $this->query($sql);
}

function fetchEventId($unique_id)
{
	$sql="SELECT * FROM ".$this->prefix()."general_events WHERE unique_id = '".$unique_id."'";
	$this->query($sql);
}

function changeEventStatus($id,$status)
{
	if($status == 'Y')
	{
	  $new_status = 'N';
	}
	else
	{
	  $new_status = 'Y';
	}

	$sql = "UPDATE ".$this->prefix()."general_events SET event_status='".$new_status."' WHERE event_id = '".$id."'";
	return $this->query($sql);
}

function allEventList($limit,$venue_state = false,$venue_county = false,$venue_city = false,$venue = false,$show_pastevent = false, $userType = -1)			
{
	$whereClause = '';
	if($venue_state)
		$whereClause = " AND A.venue_state = '".$venue_state."'";	
	
	if($venue_county)
		$whereClause .= " AND A.venue_county = '".$venue_county."'";	
	
	if($venue_city)
		$whereClause .= " AND  A.venue_city = '".$venue_city."'";	
	
	if($venue)
		$whereClause .= " AND  event_venue = '".$venue."'";	
		
	if($show_pastevent==0)	
		$whereClause .= " AND ( event_start_date_time >= now() OR ( r_span_end >= now() AND recurring = 1) )";	
		
    if ($userType != -1) {
        if($userType != 2 && $userType != 3) {
            $whereClause .= " AND  B.admin_id = '".$_SESSION['ses_user_id']."'";	
        }
    } else {
        if($_SESSION['ses_user_id']!=1) {
            $whereClause .= " AND  B.admin_id = '".$_SESSION['ses_user_id']."'";
        }
    }
	
	$sql="SELECT A.venue_name,C.city_name,S.county_name,B.* FROM ".$this->prefix()."venue AS A, ".$this->prefix()."general_events AS B, ".$this->prefix()."city AS C, ".$this->prefix()."county S  WHERE A.venue_id = B.event_venue AND C.id = A.venue_city AND S.id = A.venue_county $whereClause ORDER BY S.county_name,C.city_name,A.venue_name,B.event_start_date_time ASC $limit";
	
	//$sql = "SELECT A.venue_name,C.city_name,S.county_name,B.* FROM kcp_general_events AS B  LEFT JOIN kcp_venue AS A ON(A.venue_id = B.event_venue) LEFT JOIN kcp_city AS C  ON(C.id = B.event_venue_city) LEFT JOIN kcp_county S ON(S.id = B.event_venue_county) WHERE 1 = 1 $whereClause ORDER BY S.county_name,C.city_name,A.venue_name,B.event_start_date_time ASC $limit";
	
	//echo $sql; 
	$this->query($sql);
}

function allEventListNewsletter($limit,$venue_state = false,$venue_county = false,$venue_city = false,$venue = false,$show_pastevent = false, $userType = -1, $blog_start_date = false, $blog_end_date = false)			
{
	$whereClause = '';
	if($venue_state)
		$whereClause = " AND A.venue_state = '".$venue_state."'";	
	
	if($venue_county)
		$whereClause .= " AND A.venue_county = '".$venue_county."'";	
	
	if($venue_city)
		$whereClause .= " AND  A.venue_city = '".$venue_city."'";	
	
	if($venue)
		$whereClause .= " AND  event_venue = '".$venue."'";	
		
	//if($show_pastevent==0)	
		//$whereClause .= " AND ( event_start_date_time >= now() OR ( r_span_end >= now() AND recurring = 1) )";	
		
    if($blog_start_date && $blog_end_date) {
        $whereClause .= " AND ( ('$blog_start_date 00:00:00' <= event_start_date_time AND event_start_date_time <= '$blog_end_date 23:59:59') OR ('$blog_start_date 00:00:00' <= event_end_date_time AND event_end_date_time <= '$blog_end_date 23:59:59') )";	
    }
    
    if ($userType != -1) {
        if($userType != 2 && $userType != 3) {
            $whereClause .= " AND  B.admin_id = '".$_SESSION['ses_user_id']."'";	
        }
    } else {
        if($_SESSION['ses_user_id']!=1) {
            $whereClause .= " AND  B.admin_id = '".$_SESSION['ses_user_id']."'";
        }
    }
	
	$sql="SELECT A.venue_name,C.city_name,S.county_name,B.* FROM ".$this->prefix()."venue AS A, ".$this->prefix()."general_events AS B, ".$this->prefix()."city AS C, ".$this->prefix()."county S  WHERE A.venue_id = B.event_venue AND C.id = A.venue_city AND S.id = A.venue_county $whereClause ORDER BY B.event_start_date_time ASC $limit";
	
	//$sql = "SELECT A.venue_name,C.city_name,S.county_name,B.* FROM kcp_general_events AS B  LEFT JOIN kcp_venue AS A ON(A.venue_id = B.event_venue) LEFT JOIN kcp_city AS C  ON(C.id = B.event_venue_city) LEFT JOIN kcp_county S ON(S.id = B.event_venue_county) WHERE 1 = 1 $whereClause ORDER BY S.county_name,C.city_name,A.venue_name,B.event_start_date_time ASC $limit";
	
	//echo $sql; 
	$this->query($sql);
}

function allEventListNewsletterCount($venue_state = false,$venue_county = false,$venue_city = false,$venue = false,$show_pastevent = false, $userType = -1, $blog_start_date = false, $blog_end_date = false)			
{
	$whereClause = '';
	if($venue_state)
		$whereClause = " AND A.venue_state = '".$venue_state."'";	
	
	if($venue_county)
		$whereClause .= " AND A.venue_county = '".$venue_county."'";	
	
	if($venue_city)
		$whereClause .= " AND  A.venue_city = '".$venue_city."'";	
	
	if($venue)
		$whereClause .= " AND  event_venue = '".$venue."'";	
		
	//if($show_pastevent==0)	
		//$whereClause .= " AND ( event_start_date_time >= now() OR ( r_span_end >= now() AND recurring = 1) )";	
		
    if($blog_start_date && $blog_end_date) {
        $whereClause .= " AND ( ('$blog_start_date 00:00:00' <= event_start_date_time AND event_start_date_time <= '$blog_end_date 23:59:59') OR ('$blog_start_date 00:00:00' <= event_end_date_time AND event_end_date_time <= '$blog_end_date 23:59:59') )";	
    }
    
    if ($userType != -1) {
        if($userType != 2 && $userType != 3) {
            $whereClause .= " AND  B.admin_id = '".$_SESSION['ses_user_id']."'";	
        }
    } else {
        if($_SESSION['ses_user_id']!=1) {
            $whereClause .= " AND  B.admin_id = '".$_SESSION['ses_user_id']."'";	
        }
    }
	
	//$sql="SELECT A.venue_name,C.city_name,S.county_name,B.* FROM ".$this->prefix()."venue AS A, ".$this->prefix()."general_events AS B, ".$this->prefix()."city AS C, ".$this->prefix()."county S  WHERE A.venue_id = B.event_venue AND C.id = B.event_venue_city AND S.id = B.event_venue_county $whereClause ORDER BY S.county_name,C.city_name,A.venue_name,B.event_start_date_time ASC ";
	$sql="SELECT A.venue_name,C.city_name,S.county_name,B.* FROM ".$this->prefix()."venue AS A, ".$this->prefix()."general_events AS B, ".$this->prefix()."city AS C, ".$this->prefix()."county S  WHERE A.venue_id = B.event_venue AND C.id = A.venue_city AND S.id = A.venue_county $whereClause ORDER BY S.county_name,C.city_name,A.venue_name,B.event_start_date_time ASC ";

	$this->query($sql);
}

function allEventListWithPromo($limit,$venue_state = false,$venue_county = false,$venue_city = false,$venue = false,$show_pastevent = false, $userType = -1)			
{
	$whereClause = '';
	if($venue_state)
		$whereClause = " AND A.venue_state = '".$venue_state."'";	
	
	if($venue_county)
		$whereClause .= " AND A.venue_county = '".$venue_county."'";	
	
	if($venue_city)
		$whereClause .= " AND  A.venue_city = '".$venue_city."'";	
	
	if($venue)
		$whereClause .= " AND  event_venue = '".$venue."'";	
		
	if($show_pastevent==0)	
		$whereClause .= " AND ( event_start_date_time >= now() OR ( r_span_end >= now() AND recurring = 1) )";	
		
    if ($userType != -1) {
        if($userType != 2 && $userType != 3) {
            $whereClause .= " AND  B.admin_id = '".$_SESSION['ses_user_id']."'";	
        }
    } else {
        if($_SESSION['ses_user_id']!=1) {
            $whereClause .= " AND  B.admin_id = '".$_SESSION['ses_user_id']."'";
        }
    }
	
	$sql="SELECT CASE WHEN P.dpost1 IS NULL THEN 0 ELSE 1 END AS has_promo, A.venue_name,C.city_name,S.county_name,B.*, P.dpost1, P.dpost2, P.dpost3, P.dpost4, P.dpost5 FROM ".$this->prefix()."venue AS A, ".$this->prefix()."city AS C, ".$this->prefix()."county S, ".$this->prefix()."general_events AS B LEFT JOIN ".$this->prefix()."event_promo_instruction AS P ON B.event_id = P.event_id WHERE A.venue_id = B.event_venue AND C.id = A.venue_city AND S.id = A.venue_county $whereClause ORDER BY S.county_name,C.city_name,A.venue_name,B.event_start_date_time ASC $limit";

	$this->query($sql);
}

function allEventListCount($venue_state = false,$venue_county = false,$venue_city = false,$venue = false,$show_pastevent = false, $userType = -1)			
{
	$whereClause = '';
	if($venue_state)
		$whereClause = " AND A.venue_state = '".$venue_state."'";	
	
	if($venue_county)
		$whereClause .= " AND A.venue_county = '".$venue_county."'";	
	
	if($venue_city)
		$whereClause .= " AND  A.venue_city = '".$venue_city."'";	
	
	if($venue)
		$whereClause .= " AND  event_venue = '".$venue."'";	
		
	if($show_pastevent==0)	
		$whereClause .= " AND ( event_start_date_time >= now() OR ( r_span_end >= now() AND recurring = 1) )";	
		
    if ($userType != -1) {
        if($userType != 2 && $userType != 3) {
            $whereClause .= " AND  B.admin_id = '".$_SESSION['ses_user_id']."'";	
        }
    } else {
        if($_SESSION['ses_user_id']!=1) {
            $whereClause .= " AND  B.admin_id = '".$_SESSION['ses_user_id']."'";	
        }
    }
	
	//$sql="SELECT A.venue_name,C.city_name,S.county_name,B.* FROM ".$this->prefix()."venue AS A, ".$this->prefix()."general_events AS B, ".$this->prefix()."city AS C, ".$this->prefix()."county S  WHERE A.venue_id = B.event_venue AND C.id = B.event_venue_city AND S.id = B.event_venue_county $whereClause ORDER BY S.county_name,C.city_name,A.venue_name,B.event_start_date_time ASC ";
	$sql="SELECT A.venue_name,C.city_name,S.county_name,B.* FROM ".$this->prefix()."venue AS A, ".$this->prefix()."general_events AS B, ".$this->prefix()."city AS C, ".$this->prefix()."county S  WHERE A.venue_id = B.event_venue AND C.id = A.venue_city AND S.id = A.venue_county $whereClause ORDER BY S.county_name,C.city_name,A.venue_name,B.event_start_date_time ASC ";

	$this->query($sql);
}

function allSocialShare($limit)			
{	
	$sql="SELECT So.social_id, S.id, S.message, S.dpost1, S.dpost2, S.dpost3, S.dpost4, S.dpost5 FROM ".$this->prefix()."social_share as S LEFT JOIN " .$this->prefix()."social_schedule AS So on (S.id = So.share_id) ORDER BY S.id ASC $limit";

	$this->query($sql);
}

function allSocialShareCount()			
{		
	$sql="SELECT So.social_id, S.id, S.message, S.dpost1, S.dpost2, S.dpost3, S.dpost4, S.dpost5 FROM ".$this->prefix()."social_share AS S LEFT JOIN " .$this->prefix()."social_schedule AS So on (S.id = So.share_id) ORDER BY S.id ASC";

	$this->query($sql);
}

function getEventById($id)
{
	$sql = "SELECT * FROM ".$this->prefix()."general_events  WHERE event_id = '".$id."'";
	$this->query($sql);
}

function getSocialShareById($id)
{
	$sql = "SELECT * FROM ".$this->prefix()."social_share as S LEFT JOIN ".$this->prefix()."social_schedule as So on (S.id = So.share_id)  WHERE S.id = '".$id."'";

	return $this->query($sql);

}

function eventVenue($event_id)
{
	$sql = "SELECT * FROM ".$this->prefix()."venue AS A, ".$this->prefix()."general_events AS B WHERE A.venue_id = B.event_venue  AND B.event_id = ".$event_id;
	//echo $sql;;
	$this->query($sql);
}
function getCityByEventId($event_id)
{
	$sql = "SELECT * FROM ".$this->prefix()."general_events AS A, ".$this->prefix()."city AS B WHERE A.event_venue_city = B.id AND A.event_id = '".$event_id."'";
	$this->query($sql);
}


function editEvent($event_name_sp,$event_name_en,$event_start_date_time,$event_start_ampm,$event_end_date_time,$event_end_ampm,$venue_state,$venue_county,$venue_city,$venue,$page_content_en,$page_content_sp,$event_tag,$file_name,$identical_function,$recurring,$sub_events,$event_id)
{
	$sql="UPDATE ".$this->prefix()."general_events SET event_name_sp = '".$event_name_sp."',
													   event_name_en = '".$event_name_en."',
													   
													   event_short_desc_en = '".$short_desc_en."',
													   event_short_desc_sp = '".$short_desc_sp."',
													   
													   event_start_date_time ='".$event_start_date_time."',
													   event_start_ampm = '".$event_start_ampm."',
													   event_end_date_time = '".$event_end_date_time."',
													   event_end_ampm = '".$event_end_ampm."',
													   event_venue_state = '".$venue_state."',
													   event_venue_county = '".$venue_county."',
													   event_venue_city = '".$venue_city."',
													   event_venue = '".$venue."',
													   event_details_en = '".$page_content_en."',
													   event_details_sp = '".$page_content_sp."',
													   event_tag = '".$event_tag."',
													   event_photo = '".$file_name."',
													   identical_function = '".$identical_function."',
													   recurring = '".$recurring."',
													   sub_events = '".$sub_events."'
													   
														Paypal = '".$Paypal."',
														Bank_deposite = '".$Bank."',
														Oxxo_Payment = '".$Oxxo."',
														Mobile_Payment = '".$Mobile."',
														Offline_Payment = '".$Offline."',
														
														publish_date = '".$publish_date."',
														
														event_time = '".$event_time."',
														event_time_period = '".$event_time_period."',
														r_month = '".$r_month."',
														r_month_day = '".$r_month_day."',
														mon = '".$mon."',
														tue = '".$tue."',
														wed = '".$wed."',
														thu = '".$thu."',
														fri = '".$fri."',
														sat = '".$sat."',
														sun = '".$sun."',
														r_span_start = '".$r_span_start."',
														r_span_end = '".$r_span_end."',
														event_start = '".$event_start."',
														event_end = '".$event_end."',
														all_day = '".$all_day."',
														event_lasts = '".$event_lasts."',
														
														attendees_share = '".$attendees."',
														attendees_invitation = '".$invitation_only."',
														password_protect = '".$password_protect_check."',
														password_protect_text = '".$pass_protected."',
														
														all_access = '".$radio_access."',
														include_promotion = '".$promo_charge."',
														include_payment = '".$pay_ticket_fee."',
														
														paper_less_mob_ticket = '".$paper_less_mob_ticket."',
														print = '".$print."',
														will_call = '".$will_call."',

													   WHERE event_id = '".$event_id."'";
	$rs=$this->query($sql);	
	return $last_event_id=mysql_insert_id();
}

function catBySubEvent($event_id)
{
	$sql="SELECT * FROM ".$this->prefix()."category_by_sub_event WHERE sub_event_id = '".$event_id."'";
	$this->query($sql);
}

function categorylistByEvent($event_id)
{
	$sql="SELECT * FROM ".$this->prefix()."category_by_event WHERE event_id = '".$event_id."'";
	$this->query($sql);
}

/*function deleteCategoryByEvent($event_id)
{
	$sql="DELETE FROM ".$this->prefix()."category_by_event WHERE event_id = '".$event_id."'";
	return $this->query($sql);
}
*/
function most_used_category()
{
	$sql="SELECT count(*) as no_of_duplicate,category_id FROM `kcp_category_by_event` GROUP BY `category_id` HAVING no_of_duplicate>1 ORDER BY no_of_duplicate DESC";
	return $this->query($sql);
}
//function category_sub_list($category_id){
//	
//		$sql="SELECT * FROM ".$this->prefix()."event_category Where parent_category = ".$category_id." AND  category_status ='Y'  ORDER BY category_id ";
//		//echo $sql;
//		return $this->query($sql);
//	}





function allTicketList($event_id,$limit)			
{
	$sql="SELECT * FROM ".$this->prefix()."final_tickets WHERE event_id = '".$event_id."' ORDER BY ticket_id ASC $limit";
	$this->query($sql);
}

function allTicketListCount($event_id)			
{
	 $sql="SELECT * FROM ".$this->prefix()."final_tickets WHERE event_id = '".$event_id."'";
	$this->query($sql);
}	

function getTicketById($event_id)
{
	$sql = "SELECT * FROM ".$this->prefix()."final_tickets  WHERE event_id = ".$event_id;
	$this->query($sql);
}

// Get temp tickets

function get_temp_tickets($event_id)
{
	$sql = "SELECT * FROM ".$this->prefix()."final_tickets  WHERE event_id = '".$event_id."'";
	$this->query($sql);
}
function get_final_tickets($event_id)
{
	$sql = "SELECT * FROM ".$this->prefix()."final_tickets  WHERE event_id = '".$event_id."'";
	$this->query($sql);
}
function getTempTicketById_final($ticket_id)
{
	$sql = "SELECT * FROM ".$this->prefix()."final_tickets  WHERE ticket_id = '".$ticket_id."'";
	$this->query($sql);
}
function getTempTicketById($ticket_id)
{
	$sql = "SELECT * FROM ".$this->prefix()."final_tickets  WHERE ticket_id = '".$ticket_id."'";
	$this->query($sql);
}

function delete_temp_ticket($ticket_id)
{
	$sql="DELETE FROM ".$this->prefix()."final_tickets  WHERE ticket_id = '".$ticket_id."' "; 
	$this->query($sql);
}

function delete_final_ticket($ticket_id)
{
	$sql="DELETE FROM ".$this->prefix()."final_tickets  WHERE ticket_id = '".$ticket_id."' "; 
	$this->query($sql);
}

function getEventId($unique_id)
{
	$sql="SELECT * FROM ".$this->prefix()."general_events  WHERE unique_id = '".$unique_id."' "; 
	$this->query($sql);
}

function editTempTicket($ticket_name_en,$ticket_name_sp,$description_en,$description_sp,$price_mx,$price_us,$ticket_num,$from_ticket,$to_ticket,$eairly_dis_percen,$eairly_days,$group_dis_per,$group_dis_days,$ticket_icon,$members_only,$unique_id,$ticket_id)
{
		
		$sql="Update ".$this->prefix()."final_tickets SET
		 	   ticket_name_en = '".$ticket_name_en."',
			   ticket_name_sp = '".$ticket_name_sp."',
			   description_en ='".$description_en."',
			   description_sp = '".$description_sp."',
			   price_mx = '".$price_mx."',
			   price_us = '".$price_us."',
			   ticket_num = '".$ticket_num."',
			   from_ticket = '".$from_ticket."',
			   to_ticket = '".$to_ticket."',	
			   eairly_dis_percen = '".$eairly_dis_percen."', 
			   eairly_days ='".$eairly_days."',
			   group_dis_per = '".$group_dis_per."',
			   group_dis_days = '".$group_dis_days."',
			   ticket_icon = '".$ticket_icon."',
			   members_only = '".$members_only."'	
			   WHERE 		
				ticket_id='".$ticket_id."' ";

		$rs=$this->query($sql);
	}

function editFinalTicket($ticket_name_en,$ticket_name_sp,$description_en,$description_sp,$price_mx,$price_us,$ticket_num,$from_ticket,$to_ticket,$eairly_dis_percen,$eairly_days,$group_dis_per,$group_dis_days,$ticket_icon,$members_only,$unique_id,$ticket_id)
{
		
		$sql="Update ".$this->prefix()."final_tickets SET
		 	   ticket_name_en = '".$ticket_name_en."',
			   ticket_name_sp = '".$ticket_name_sp."',
			   description_en ='".$description_en."',
			   description_sp = '".$description_sp."',
			   price_mx = '".$price_mx."',
			   price_us = '".$price_us."',
			   ticket_num = '".$ticket_num."',
			   from_ticket = '".$from_ticket."',
			   to_ticket = '".$to_ticket."',	
			   eairly_dis_percen = '".$eairly_dis_percen."', 
			   eairly_days ='".$eairly_days."',
			   group_dis_per = '".$group_dis_per."',
			   group_dis_days = '".$group_dis_days."',
			   ticket_icon = '".$ticket_icon."',
			   members_only = '".$members_only."'	
			   WHERE 		
				ticket_id='".$ticket_id."' ";

		$rs=$this->query($sql);
	}
	

function ticket_info($ticket_id)
{
	$sql = "SELECT * FROM ".$this->prefix()."final_tickets WHERE ticket_id = '".$ticket_id."'";
	$this->query($sql);
}

function ticketById($ticket_id)
{
	$sql = "SELECT * FROM ".$this->prefix()."final_tickets WHERE ticket_id = '".$ticket_id."'";
	$this->query($sql);
}

function edit_tickets($ticket_name_en,$ticket_name_sp,$description_en,$description_sp,$price_mx,$price_us,$ticket_num,$eairly_dis_percen,$eairly_days,$group_dis_per,$group_dis_days,$members_only,$from_ticket,$to_ticket,$photoname,$exit_ticket_id)
{
	$sql="Update ".$this->prefix()."final_tickets SET
		 	   ticket_name_en = '".$ticket_name_en."',
			   ticket_name_sp = '".$ticket_name_sp."',
			   description_en ='".$description_en."',
			   description_sp = '".$description_sp."',
			   price_mx = '".$price_mx."',
			   price_us = '".$price_us."',
			   ticket_num = '".$ticket_num."',
			   from_ticket = '".$from_ticket."',
			   to_ticket = '".$to_ticket."',	
			   eairly_dis_percen = '".$eairly_dis_percen."', 
			   eairly_days ='".$eairly_days."',
			   group_dis_per = '".$group_dis_per."',
			   group_dis_days = '".$group_dis_days."',
			   ticket_icon = '".$photoname."',
			   members_only = '".$members_only."'	
			   WHERE 		
				ticket_id='".$exit_ticket_id."' ";

		$rs=$this->query($sql);
}

function add_tickets($ticket_name_en,$ticket_name_sp,$description_en,$description_sp,$price_mx,$price_us,$ticket_num,$eairly_dis_percen,$eairly_days,$group_dis_per,$group_dis_days,$members_only,$from_ticket,$to_ticket,$photoname,$event_id)
{
	$sql="INSERT INTO ".$this->prefix()."final_tickets SET
		 	   event_id = '".$event_id."',
		 	   ticket_name_en = '".$ticket_name_en."',
			   ticket_name_sp = '".$ticket_name_sp."',
			   description_en ='".$description_en."',
			   description_sp = '".$description_sp."',
			   price_mx = '".$price_mx."',
			   price_us = '".$price_us."',
			   ticket_num = '".$ticket_num."',
			   from_ticket = '".$from_ticket."',
			   to_ticket = '".$to_ticket."',	
			   eairly_dis_percen = '".$eairly_dis_percen."', 
			   eairly_days ='".$eairly_days."',
			   group_dis_per = '".$group_dis_per."',
			   group_dis_days = '".$group_dis_days."',
			   ticket_icon = '".$photoname."',
			   members_only = '".$members_only."',
			   post_date = '".time()."'";

		$rs=$this->query($sql);
}

function venue_details_eventId($event_id){

	$sql='SELECT V.*, S.state_name as st_name,C.city_name as city FROM '.$this->prefix().'general_events E Inner join '.$this->prefix().'venue V ON (E.event_venue = V.venue_id ) Inner join '.$this->prefix().'state S on (S.id = E.event_venue_state)  Inner join '.$this->prefix().'city C on (C.id = E.event_venue_city) WHERE E.event_id="'.$event_id.'" AND E.event_status="Y" ';
	//echo $sql;
	return $this->query($sql);	
}
function checkEventTicket($event_id)
{
	$sql = 'SELECT * FROM '.$this->prefix().'general_events E Inner join '.$this->prefix().'final_tickets T on (E.event_id = T.event_id ) where  E.event_id="'.$event_id.'" and E. 	event_status="Y" ' ;
	$this->query($sql);
}


// ============================== Saved Event =============================================
//function addSavedEvent($ses_user_id,$event_name_sp,$event_name_en,$short_desc_sp,$short_desc_en,$event_start_date_time,$event_start_ampm,$event_end_date_time,$event_end_ampm,$venue_state,$venue_county,$venue_city,$venue,$page_content_en,$page_content_sp,$event_tag,$file_name,$identical_function,$recurring,$sub_events,$Paypal,$Bank,$Oxxo,$Mobile,$Offline,$publish_date,$event_time,$event_time_period,$r_month,$r_month_day,$mon,$tue,$wed,$thu,$fri,$sat,$sun,$r_span_start,$r_span_end,$event_start,$event_end,$all_day,$event_lasts,$attendees,$invitation_only,$password_protect_check,$pass_protected,$radio_access,$pay_ticket_fee,$promo_charge,$paper_less_mob_ticket,$print,$will_call,$status,$unique_id)  //if require then use this field event_photo = '".$file_name."',//
function addSavedEvent($ses_user_id,$event_name_sp,$event_name_en,$short_desc_sp,$short_desc_en,$event_start_date_time,$event_start_ampm,$event_end_date_time,$event_end_ampm,$venue_state,$venue_county,$venue_city,$venue,$page_content_en,$page_content_sp,$event_tag,$identical_function,$recurring,$sub_events,$Paypal,$Bank,$Oxxo,$Mobile,$Offline,$publish_date,$event_time,$event_time_period,$r_month,$r_month_day,$mon,$tue,$wed,$thu,$fri,$sat,$sun,$r_span_start,$r_span_end,$event_start,$event_end,$all_day,$event_lasts,$attendees,$invitation_only,$password_protect_check,$pass_protected,$radio_access,$pay_ticket_fee,$promo_charge,$paper_less_mob_ticket,$print,$will_call,$status,$unique_id)
{
	$sql="INSERT INTO ".$this->prefix()."general_events 
			SET event_name_sp = '".$event_name_sp."',
			event_name_en = '".$event_name_en."',
			
			event_short_desc_en = '".$short_desc_en."',
			event_short_desc_sp = '".$short_desc_sp."',
			
			admin_id ='".$ses_user_id."',
			event_start_date_time ='".$event_start_date_time."',
			event_start_ampm = '".$event_start_ampm."',
			event_end_date_time = '".$event_end_date_time."',
			event_end_ampm = '".$event_end_ampm."',
			event_venue_state = '".$venue_state."',
			event_venue_county = '".$venue_county."',
			event_venue_city = '".$venue_city."',
			event_venue = '".$venue."',
			event_details_en = '".$page_content_en."',
			event_details_sp = '".$page_content_sp."',
			event_tag = '".$event_tag."',
			
			identical_function = '".$identical_function."',
			recurring = '".$recurring."',
			sub_events = '".$sub_events."',
			unique_id = '".$unique_id."',
			
			Paypal = '".$Paypal."',
			Bank_deposite = '".$Bank."',
			Oxxo_Payment = '".$Oxxo."',
			Mobile_Payment = '".$Mobile."',
			Offline_Payment = '".$Offline."',
			
			publish_date = '".$publish_date."',
			
			event_time = '".$event_time."',
			event_time_period = '".$event_time_period."',
			r_month = '".$r_month."',
			r_month_day = '".$r_month_day."',
			mon = '".$mon."',
			tue = '".$tue."',
			wed = '".$wed."',
			thu = '".$thu."',
			fri = '".$fri."',
			sat = '".$sat."',
			sun = '".$sun."',
			r_span_start = '".$r_span_start."',
			r_span_end = '".$r_span_end."',
			event_start = '".$event_start."',
			event_end = '".$event_end."',
			all_day = '".$all_day."',
			event_lasts = '".$event_lasts."',
			
			attendees_share = '".$attendees."',
			attendees_invitation = '".$invitation_only."',
			password_protect = '".$password_protect_check."',
			password_protect_text = '".$pass_protected."',
			
			all_access = '".$radio_access."',
			include_promotion = '".$promo_charge."',
			include_payment = '".$pay_ticket_fee."',
			
			paper_less_mob_ticket = '".$paper_less_mob_ticket."',
			print = '".$print."',
			will_call = '".$will_call."',
			status = '".$status."',

			post_date = '".time()."'"; 
	$rs=$this->query($sql);
	//echo $rs;
	return mysql_insert_id();
}


function addSavedCategoryByEvent($category_id,$last_event_id)
{
			$sql="INSERT INTO ".$this->prefix()."category_by_event SET event_id = '".$last_event_id."',
																	   category_id = '".$category_id."'";
			$rs=$this->query($sql);	
}

function chkExistSavedcat($last_event_id,$category_id)
{
	$sql="SELECT * FROM ".$this->prefix()."category_by_event WHERE category_id = '".$category_id."' AND event_id = '".$last_event_id."' ";
	//echo $sql;
	$query = $this->query($sql);
	return mysql_num_rows($query);
}

function deleteSavedTickets($unique_id)
{
	$sql="DELETE FROM ".$this->prefix()."saved_tickets WHERE unique_id = '".$unique_id."'";
	return $this->query($sql);
}

function addSavedTickets($unique_id)
{
	$sql="INSERT INTO ".$this->prefix()."saved_tickets (ticket_name_en, ticket_name_sp,description_en,description_sp,price_mx,price_us,ticket_num,from_ticket,to_ticket,eairly_dis_percen,eairly_days,group_dis_per,group_dis_days,ticket_icon,members_only,post_date,unique_id) (SELECT ticket_name_en, ticket_name_sp,description_en,description_sp,price_mx,price_us,ticket_num,from_ticket,to_ticket,eairly_dis_percen,eairly_days,group_dis_per,group_dis_days,ticket_icon,members_only,post_date,unique_id FROM ".$this->prefix()."temporary_tickets WHERE unique_id = '".$unique_id."')";
	//echo $sql;
	return $this->query($sql);
}

function editSavedTicketByEvent($unique_id,$last_event_id)
{
	$sql="UPDATE ".$this->prefix()."saved_tickets SET event_id = '".$last_event_id."' WHERE unique_id = '".$unique_id."'";
	return $this->query($sql);
}

function deleteSavedCategory($event_id)
{
	$sql="DELETE FROM ".$this->prefix()."category_by_event WHERE event_id = '".$event_id."'";
	return $this->query($sql);
}


//function editSavedEvent($ses_user_id,$event_name_sp,$event_name_en,$short_desc_sp,$short_desc_en,$event_start_date_time,$event_start_ampm,$event_end_date_time,$event_end_ampm,$venue_state,$venue_county,$venue_city,$venue,$page_content_en,$page_content_sp,$event_tag,$file_name,$identical_function,$recurring,$sub_events,$Paypal,$Bank,$Oxxo,$Mobile,$Offline,$publish_date,$event_time,$event_time_period,$r_month,$r_month_day,$mon,$tue,$wed,$thu,$fri,$sat,$sun,$r_span_start,$r_span_end,$event_start,$event_end,$all_day,$event_lasts,$attendees,$invitation_only,$password_protect_check,$pass_protected,$radio_access,$pay_ticket_fee,$promo_charge,$paper_less_mob_ticket,$print,$will_call,$status,$privacy,$unique_id) //if required then use this  field  event_photo = '".$file_name."',//

function editSavedEvent($ses_user_id,$event_name_sp,$event_name_en,$short_desc_sp,$short_desc_en,$event_start_date_time,$event_start_ampm,$event_end_date_time,$event_end_ampm,$venue_state,$venue_county,$venue_city,$venue,$page_content_en,$page_content_sp,$event_tag,$identical_function,$recurring,$sub_events,$Paypal,$Bank,$Oxxo,$Mobile,$Offline,$publish_date,$event_time,$event_time_period,$r_month,$r_month_day,$mon,$tue,$wed,$thu,$fri,$sat,$sun,$r_span_start,$r_span_end,$event_start,$event_end,$all_day,$event_lasts,$attendees,$invitation_only,$password_protect_check,$pass_protected,$radio_access,$pay_ticket_fee,$promo_charge,$paper_less_mob_ticket,$print,$will_call,$status,$privacy,$unique_id)
{
	$sql="UPDATE ".$this->prefix()."general_events SET event_name_sp = '".$event_name_sp."',
													   event_name_en = '".$event_name_en."',
													   
													   event_short_desc_en = '".$short_desc_en."',
													   event_short_desc_sp = '".$short_desc_sp."',
													   event_start_date_time ='".$event_start_date_time."',
													   event_start_ampm = '".$event_start_ampm."',
													   event_end_date_time = '".$event_end_date_time."',
													   event_end_ampm = '".$event_end_ampm."',
													   event_venue_state = '".$venue_state."',
													   event_venue_county = '".$venue_county."',
													   event_venue_city = '".$venue_city."',
													   event_venue = '".$venue."',
													   event_details_en = '".$page_content_en."',
													   event_details_sp = '".$page_content_sp."',
													   event_tag = '".$event_tag."',
													   
													   identical_function = '".$identical_function."',
													   recurring = '".$recurring."',
													   sub_events = '".$sub_events."',
													   
													    Paypal = '".$Paypal."',
														Bank_deposite = '".$Bank."',
														Oxxo_Payment = '".$Oxxo."',
														Mobile_Payment = '".$Mobile."',
														Offline_Payment = '".$Offline."',
														
														publish_date = '".$publish_date."',
														
														event_time = '".$event_time."',
														event_time_period = '".$event_time_period."',
														r_month = '".$r_month."',
														r_month_day = '".$r_month_day."',
														mon = '".$mon."',
														tue = '".$tue."',
														wed = '".$wed."',
														thu = '".$thu."',
														fri = '".$fri."',
														sat = '".$sat."',
														sun = '".$sun."',
														r_span_start = '".$r_span_start."',
														r_span_end = '".$r_span_end."',
														event_start = '".$event_start."',
														event_end = '".$event_end."',
														all_day = '".$all_day."',
														event_lasts = '".$event_lasts."',
														
														attendees_share = '".$attendees."',
														attendees_invitation = '".$invitation_only."',
														password_protect = '".$password_protect_check."',
														password_protect_text = '".$pass_protected."',
														
														all_access = '".$radio_access."',
														include_promotion = '".$promo_charge."',
														include_payment = '".$pay_ticket_fee."',
														
														paper_less_mob_ticket = '".$paper_less_mob_ticket."',
														print = '".$print."',
														will_call = '".$will_call."',
														status = '".$status."',
														set_privacy = '".$privacy."'
															
													   WHERE unique_id = '".$_SESSION['unique_id']."'";
													   //echo $sql;
	$rs=$this->query($sql);	
}

function checkSavedEvent($uid)
{
  $sql="select * from  ".$this->prefix()."general_events where unique_id='".$uid."'";
  //echo $sql;
  $rs=$this->query($sql);	
}

//function checkSavedEventEdit($uid)
//{
//  $sql="select * from  ".$this->prefix()."general_events where unique_id='".$uid."'";
//  //echo $sql;
//  $rs=$this->query($sql);	
//}

function checkSavedEventIdentical($uid)
{
  $sql="select * from  ".$this->prefix()."temporary_multi_events where unique_id='".$uid."'";
  //echo $sql;
  $rs=$this->query($sql);	
}

function checkSavedSubEvent($uid)
{
  $sql="select * from  ".$this->prefix()."general_subevents where unique_id='".$uid."'";
  //echo $sql;
  $rs=$this->query($sql);	
}


// ========================== Multiple Events ===============================

	function addTempMultiEvent($event_start_date_time,$event_start_ampm,$event_end_date_time,$event_end_ampm,$multi_venue_state,$venue_county_multi,$multi_venue_city,$multi_venue,$unique_id,$event_id)
	{
		$sql="INSERT INTO ".$this->prefix()."final_multi_event SET admin_id = '".$_SESSION['ses_user_id']."',
																   event_id = '".$event_id."',
																   event_start_date_time = '".$event_start_date_time."',
																   event_start_ampm ='".$event_start_ampm."',
																   event_end_date_time = '".$event_end_date_time."',
																   event_end_ampm ='".$event_end_ampm."',
																   multi_venue_state = '".$multi_venue_state."',
																   venue_county_multi = '".$venue_county_multi."',
																   multi_venue_city = '".$multi_venue_city."',
																   multi_venue = '".$multi_venue."',
																   unique_id = '".$unique_id."',
																   post_date = '".time()."'";
				   
		$rs=$this->query($sql);	
	}
	
	function SavedaddTempMultiEvent($event_start_date_time,$event_start_ampm,$multi_venue_state,$venue_county_multi,$multi_venue_city,$multi_venue,$unique_id,$status)
	{
		//echo $unique_id; exit;
		$sql="INSERT INTO ".$this->prefix()."temporary_multi_events 
				SET admin_id = '".$_SESSION['ses_user_id']."',
				   event_start_date_time = '".$event_start_date_time."',
				   event_start_ampm ='".$event_start_ampm."',
				   multi_venue_state = '".$multi_venue_state."',
				   venue_county_multi = '".$venue_county_multi."',
				   multi_venue_city = '".$multi_venue_city."',
				   multi_venue = '".$multi_venue."',
				   unique_id = '".$unique_id."',
				   status = '".$status."',
				   post_date = '".time()."'";
				   
		$rs=$this->query($sql);	
	}
	
	function SavededitTempMultiEvent($event_start_date_time,$event_start_ampm,$event_end_date_time,$event_end_ampm,$multi_venue_state,$venue_county_multi,$multi_venue_city,$multi_venue,$unique_id,$multi_event_id,$status)
	{
		$sql="UPDATE ".$this->prefix()."temporary_multi_events SET event_start_date_time = '".$event_start_date_time."',
																   event_start_ampm ='".$event_start_ampm."',
																   multi_venue_state = '".$multi_venue_state."',
																   venue_county_multi = '".$venue_county_multi."',
																   multi_venue_city = '".$multi_venue_city."',
																   status = '".$status."',
																   multi_venue = '".$multi_venue."'
																   WHERE unique_id = '".$unique_id."'";
		$rs=$this->query($sql);	
	}
	
	function addMultipleEvent($unique_id,$last_event_id)
	{
		
		$qry=mysql_query("select * from  ".$this->prefix()."temporary_multi_events where unique_id='".$unique_id."'");
		while($row = mysql_fetch_array($qry)){
		
		$sql="INSERT INTO ".$this->prefix()."final_multi_event 
				   SET admin_id = '".$_SESSION['ses_user_id']."',
				   event_id = '".$last_event_id."',
				   event_start_date_time = '".$row['event_start_date_time']."',
				   event_start_ampm ='".$row['event_start_ampm']."',
				   multi_venue_state = '".$row['multi_venue_state']."',
				   venue_county_multi = '".$row['venue_county_multi']."',
				   multi_venue_city = '".$row['multi_venue_city']."',
				   multi_venue = '".$row['multi_venue']."',
				   unique_id = '".$unique_id."',
				   post_date = '".time()."'";
				   
		$rs=$this->query($sql);	
		}
	}
	
function editTempMultiEvent($event_start_date_time,$event_start_ampm,$event_end_date_time,$event_end_ampm,$multi_venue_state,$venue_county_multi,$multi_venue_city,$multi_venue,$unique_id,$multi_event_id)
{
	$sql="UPDATE ".$this->prefix()."final_multi_event SET  event_start_date_time = '".$event_start_date_time."',
														   event_start_ampm ='".$event_start_ampm."',
														   event_end_date_time = '".$event_end_date_time."',
														   event_end_ampm ='".$event_end_ampm."',
														   multi_venue_state = '".$multi_venue_state."',
														   venue_county_multi = '".$venue_county_multi."',
														   multi_venue_city = '".$multi_venue_city."',
														   multi_venue = '".$multi_venue."'
														   WHERE multi_id = '".$multi_event_id."'";
	$rs=$this->query($sql);	
}

function deleteTempMultiEvent($temp_multi_event_id)
{
	$sql="DELETE FROM ".$this->prefix()."final_multi_event WHERE multi_id = '".$temp_multi_event_id."'";
	return $this->query($sql);
}

function get_temp_MultiEvent($event_id)
{
	$sql = "SELECT TM.*,C.city_name city_name_multi,S.state_name state_name_multi,V.venue_name venue_name_multi FROM ".$this->prefix()."final_multi_event TM LEFT join ".$this->prefix()."venue V ON (TM.multi_venue = V.venue_id ) LEFT join ".$this->prefix()."state S on (S.id = TM.multi_venue_state) LEFT join ".$this->prefix()."city C on (C.id = TM.multi_venue_city) WHERE TM.event_id = '".$event_id."'"; //echo $sql; exit;
	$this->query($sql);
}

function getTempMultiEvents($temp_multi_event_id)
{
  $sql="select * from  ".$this->prefix()."final_multi_event where multi_id='".$temp_multi_event_id."'";
  //echo $sql;
  $rs=$this->query($sql);	
}
function AddTempMultiEvnt($event_id)
{
		$qry=mysql_query("select * from  ".$this->prefix()."final_multi_event where event_id='".$event_id."'");
		while($row = mysql_fetch_array($qry)){
		
		$sql="INSERT INTO ".$this->prefix()."temporary_multi_events 
				   SET admin_id = '".$_SESSION['ses_user_id']."',
				   event_start_date_time = '".$row['event_start_date_time']."',
				   event_start_ampm ='".$row['event_start_ampm']."',
				   multi_venue_state = '".$row['multi_venue_state']."',
				   venue_county_multi = '".$row['venue_county_multi']."',
				   multi_venue_city = '".$row['multi_venue_city']."',
				   multi_venue = '".$row['multi_venue']."',
				   unique_id = '".$_SESSION['unique_id']."',
				   post_date = '".time()."'";
				   
		$rs=$this->query($sql);	
		}}

// ========================== Multiple Events ===============================



// ========================== Duplicate Events ===============================

function getDuplEventDtls($event_id)
{
	$sql = "SELECT GE.*,C.*,S.*,V.* FROM ".$this->prefix()."general_events GE LEFT join ".$this->prefix()."venue V ON (GE.event_venue = V.venue_id) LEFT join ".$this->prefix()."state S on (S.id = GE.event_venue_state) LEFT join ".$this->prefix()."city C on (C.id = GE.event_venue_city) LEFT join ".$this->prefix()."county Co on (Co.id = GE.event_venue_county) WHERE GE.event_id = '".$event_id."'";
	$this->query($sql);
}

function AddTempTickets($event_id)
{
	
	$qry=mysql_query("select * from  ".$this->prefix()."final_tickets where event_id='".$event_id."'");
	while($row = mysql_fetch_array($qry)){
	
	$sql="INSERT INTO ".$this->prefix()."temporary_tickets 
			  		   SET ticket_name_en = '".$row['ticket_name_en']."',
					   ticket_name_sp = '".$row['ticket_name_sp']."',
					   description_en ='".$row['description_en']."',
					   description_sp = '".$row['description_sp']."',
					   price_mx = '".$row['price_mx']."',
					   price_us = '".$row['price_us']."',
					   ticket_num = '".$row['ticket_num']."',
					   from_ticket = '".$row['from_ticket']."',
					   to_ticket = '".$row['to_ticket']."',	
					   eairly_dis_percen = '".$row['eairly_dis_percen']."', 
					   eairly_days ='".$row['eairly_days']."',
					   group_dis_per = '".$row['group_dis_per']."',
					   group_dis_days = '".$row['group_dis_days']."',
					   members_only = '".$row['members_only']."',	
					   unique_id = '".$_SESSION['unique_id']."',
					   post_date = '".time()."'";
			   
	$rs=$this->query($sql);	
	}
}
function sub_event_by_id($event_id)
{
  $sql="select * from  ".$this->prefix()."type_by_sub_event where sub_event_id='".$event_id."'";
  $rs=$this->query($sql);	
}
function eventTypeBYEventId($event_id)
{
  $sql="select * from  ".$this->prefix()."event_types where event_id='".$event_id."'";
  $rs=$this->query($sql);	
}
function getEventTypeMster()
{
  $sql="select * from  ".$this->prefix()."master_event_types ";
  $rs=$this->query($sql);	
}
function getPerformerTypeMster()
{
  $sql="select * from  ".$this->prefix()."master_performer_types ";
  $rs=$this->query($sql);	
}

// ========================== Venue ===============================

function addEventType($event_types,$last_event_id)
{
   if(count($event_types)>0)
   {
	   for($a=0;$a<count($event_types);$a++)
	   {
			$sql="INSERT INTO ".$this->prefix()."venue_types SET venue_id = '".$last_event_id."',
																 event_master_type_id = '".$event_types[$a]."'";
			$rs=$this->query($sql);	
	   }
   }
}

function addCategoryByVenue($finalArray,$last_event_id)
{
   if(count($finalArray)>0)
   {
	   for($a=0;$a<count($finalArray);$a++)
	   {
			$sql="INSERT INTO ".$this->prefix()."category_by_venue SET venue_id = '".$last_event_id."',
																	   category_id = '".$finalArray[$a]."'";
			$rs=$this->query($sql);	
	   }
   }
}

function addVenue($venue_name_sp,$venue_short_add_sp,$venue_name,$venue_short_add_en,$venue_state,$venue_county,$venue_city,$venue_zip,$venue_address,$venue_contact_name,$venue_head_manager,$venue_phone,$venue_fax,$venue_cell,$venue_email,$venue_url,$venue_capacity,$venue_map,$venue_media_gallery,$venue_authorize_manager,$allowed_commments,$allowed_share,$show_FB_like,$venue_description,$venue_description_sp,$file_name,$private_privacy,$public_privacy,$tags,$publish_date,$venue_unique_id,$venue_stat,$venue_us_tell,$venue_nextel,$venue_fb_page,$venue_twitter_account,$standard_rate,$lat,$long)
{
	$sql="INSERT INTO ".$this->prefix()."venue 
				SET admin_id = '".$_SESSION['ses_user_id']."',
				   venue_name_sp = '".$venue_name_sp."',
				   venue_short_add_sp ='".$venue_short_add_sp."',
				   venue_name = '".$venue_name."',
				   venue_short_add_en = '".$venue_short_add_en."',
				   venue_state = '".$venue_state."',
				   venue_county = '".$venue_county."',
				   venue_city = '".$venue_city."',
				   venue_zip = '".$venue_zip."',
				   venue_address = '".$venue_address."',
				   venue_contact_name = '".$venue_contact_name."',
				   venue_head_manager = '".$venue_head_manager."',
				   venue_phone = '".$venue_phone."',
				   venue_fax = '".$venue_fax."',
				   venue_cell = '".$venue_cell."',
				   venue_email = '".$venue_email."',
				   venue_url = '".$venue_url."',
				   venue_capacity = '".$venue_capacity."',
				   venue_map = '".$venue_map."',
				   venue_media_gallery = '".$venue_media_gallery."',
				   venue_authorize_manager = '".$venue_authorize_manager."',
				   allowed_commments = '".$allowed_commments."',
				   allowed_share = '".$allowed_share."',
				   show_FB_like = '".$show_FB_like."',
				   venue_description = '".$venue_description."',
				   venue_description_sp = '".$venue_description_sp."',
				   venue_image = '".$file_name."',
				   private_privacy = '".$private_privacy."',
				   public_privacy = '".$public_privacy."',
				   tags = '".$tags."',
				   publish_date = '".$publish_date."',
				   venue_stat = '".$venue_stat."',
				   venue_us_tell = '".$venue_us_tell."',
				   venue_nextel = '".$venue_nextel."',
				   venue_fb_page = '".$venue_fb_page."',
				   venue_twitter_account = '".$venue_twitter_account."',
				   unique_id = '".$venue_unique_id."',
				   standard_rate = '".$standard_rate."',
				   
				   venue_lat = '".$lat."',
				   venue_long = '".$long."',
				   
				   post_date = '".time()."'";
  $rs=$this->query($sql);	
  return $last_event_id=mysql_insert_id();
}

function editVenue($venue_name_sp,$venue_short_add_sp,$venue_name,$venue_short_add_en,$venue_state,$venue_county,$venue_city,$venue_zip,$venue_address,$venue_contact_name,$venue_head_manager,$venue_phone,$venue_fax,$venue_cell,$venue_email,$venue_url,$venue_capacity,$venue_map,$venue_media_gallery,$venue_authorize_manager,$allowed_commments,$allowed_share,$show_FB_like,$venue_description,$venue_description_sp,$file_name,$private_privacy,$public_privacy,$tags,$publish_date,$venue_stat,$venue_id,$venue_us_tell,$venue_nextel,$venue_fb_page,$venue_twitter_account,$standard_rate,$lat,$long)
{
	$sql="UPDATE ".$this->prefix()."venue 
				SET venue_name_sp = '".$venue_name_sp."',
				   venue_short_add_sp ='".$venue_short_add_sp."',
				   venue_name = '".$venue_name."',
				   venue_short_add_en = '".$venue_short_add_en."',
				   venue_state = '".$venue_state."',
				   venue_county = '".$venue_county."',
				   venue_city = '".$venue_city."',
				   venue_zip = '".$venue_zip."',
				   venue_address = '".$venue_address."',
				   venue_contact_name = '".$venue_contact_name."',
				   venue_head_manager = '".$venue_head_manager."',
				   venue_phone = '".$venue_phone."',
				   venue_fax = '".$venue_fax."',
				   venue_cell = '".$venue_cell."',
				   venue_email = '".$venue_email."',
				   venue_url = '".$venue_url."',
				   venue_capacity = '".$venue_capacity."',
				   venue_map = '".$venue_map."',
				   venue_media_gallery = '".$venue_media_gallery."',
				   venue_authorize_manager = '".$venue_authorize_manager."',
				   allowed_commments = '".$allowed_commments."',
				   allowed_share = '".$allowed_share."',
				   show_FB_like = '".$show_FB_like."',
				   venue_description = '".$venue_description."',
				   venue_description_sp = '".$venue_description_sp."',
				   venue_image = '".$file_name."',
				   private_privacy = '".$private_privacy."',
				   public_privacy = '".$public_privacy."',
				   tags = '".$tags."',
				   venue_stat = '".$venue_stat."',
				   venue_us_tell = '".$venue_us_tell."',
				   venue_nextel = '".$venue_nextel."',
				   venue_fb_page = '".$venue_fb_page."',
				   venue_twitter_account = '".$venue_twitter_account."',
				   standard_rate = '".$standard_rate."',
				   
				  venue_lat = '".$lat."',
				  venue_long = '".$long."',
				   
				   publish_date = '".$publish_date."'
			WHERE venue_id  = '".$venue_id."'";
	//echo $sql;
	//exit;
			
  $rs=$this->query($sql);	
  //return $last_event_id=mysql_insert_id();
}

function allVenueList($limit,$venue_state = false,$venue_county = false,$venue_city = false, $userType = -1)			
{
	$whereClause = '';
	if($venue_state)
		$whereClause = " AND venue_state = '".$venue_state."'";	
	
	if($venue_county)
		$whereClause .= " AND venue_county = '".$venue_county."'";	
	
	if($venue_city)
		$whereClause .= " AND  venue_city = '".$venue_city."'";
		
    if ($userType != -1) {
        if($userType != 2 && $userType != 3) {
            $whereClause .= " AND  A.admin_id = '".$_SESSION['ses_user_id']."'";	
        }
    } else {
        if($_SESSION['ses_user_id']!=1) {
            $whereClause .= " AND  A.admin_id = '".$_SESSION['ses_user_id']."'";	
        }
    }
	
	
	
	$sql="SELECT A.*,C.city_name,S.county_name,St.state_name FROM kcp_venue AS A LEFT JOIN kcp_city AS C ON(A.venue_city = C.id) LEFT JOIN kcp_county S ON(S.id = A.venue_county) LEFT JOIN kcp_state St ON(St.id = A.venue_state)  WHERE  1=1 $whereClause ORDER BY St.state_name,S.county_name,C.city_name ASC $limit";
	
	/*SELECT A.*,C.city_name,S.county_name,St.state_name FROM ".$this->prefix()."venue AS A, ".$this->prefix()."city AS C, ".$this->prefix()."county S,".$this->prefix()."state St  WHERE A.venue_city = C.id AND S.id = A.venue_county AND St.id = A.venue_state $whereClause ORDER BY St.state_name,S.county_name,C.city_name ASC $limit";*/
	
	//echo $sql;
	$this->query($sql);
}

function allVenueListCount($venue_state = false,$venue_county = false,$venue_city = false, $userType = -1)			
{
	$whereClause = '';
	if($venue_state)
		$whereClause = " AND venue_state = '".$venue_state."'";	
	
	if($venue_county)
		$whereClause .= " AND venue_county = '".$venue_county."'";	
	
	if($venue_city)
		$whereClause .= " AND  venue_city = '".$venue_city."'";
		
    
    if ($userType != -1) {
        if($userType != 2 && $userType != 3) {
            $whereClause .= " AND  A.admin_id = '".$_SESSION['ses_user_id']."'";	
        }
    } else {
        if($_SESSION['ses_user_id']!=1) {
            $whereClause .= " AND  A.admin_id = '".$_SESSION['ses_user_id']."'";	
        }
    }
	
	
	$sql="SELECT A.*,C.city_name,S.county_name,St.state_name FROM kcp_venue AS A LEFT JOIN kcp_city AS C ON(A.venue_city = C.id) LEFT JOIN kcp_county S ON(S.id = A.venue_county) LEFT JOIN kcp_state St ON(St.id = A.venue_state)  WHERE  1=1 $whereClause ORDER BY St.state_name,S.county_name,C.city_name ASC";
	$this->query($sql);
}	

function changeVenueStatus($id,$status)
{
	if($status == 'Y')
	{
	  $new_status = 'N';
	}
	else
	{
	  $new_status = 'Y';
	}

	$sql = "UPDATE ".$this->prefix()."venue SET venue_active='".$new_status."' WHERE venue_id = '".$id."'";
	//echo $sql;exit;
	return $this->query($sql);
}

function getVenueDetails($venue_id)
{
	$sql="SELECT A.*,C.city_name,S.county_name,St.state_name FROM kcp_venue AS A LEFT JOIN kcp_city AS C ON(A.venue_city = C.id) LEFT JOIN kcp_county S ON(S.id = A.venue_county) LEFT JOIN kcp_state St ON(St.id = A.venue_state)  WHERE A.venue_id = $venue_id ";
	
	//$sql="SELECT A.*,B.city_name,C.county_name,D.state_name FROM ".$this->prefix()."venue AS A, ".$this->prefix()."city AS B, ".$this->prefix()."county C,".$this->prefix()."state D   WHERE A.venue_city = B.id AND C.id = A.venue_county AND D.id = A.venue_state AND A.venue_id = $venue_id ";
	//echo $sql;
	$this->query($sql);
}
function eventTypeBYVenueId($venue_id )
{
  $sql="select * from  ".$this->prefix()."venue_types where venue_id ='".$venue_id ."'";
  $rs=$this->query($sql);	
}
function categorylistByVenue($venue_id)
{
	$sql="SELECT * FROM ".$this->prefix()."category_by_venue WHERE venue_id = '".$venue_id."'";
	$this->query($sql);
}

function delCatByVenueId($venue_id)
{
	$sql="DELETE FROM ".$this->prefix()."category_by_venue WHERE venue_id = '".$venue_id."'";
	return $this->query($sql);
}

function delvenueTypeByVenueId($venue_id)
{
	$sql="DELETE FROM ".$this->prefix()."venue_types WHERE venue_id = '".$venue_id."'";
	return $this->query($sql);
}
function delete_venue($venue_id)
{
	$sql="DELETE FROM ".$this->prefix()."venue WHERE venue_id = '".$venue_id."'";
	return $this->query($sql);
}

function checkSavedVenue($uid)
{
  $sql="select * from  ".$this->prefix()."venue where unique_id='".$uid."'";
  //echo $sql;
  $rs=$this->query($sql);	
}
function get_venue($venue_id)
{
  $sql="select * from  ".$this->prefix()."venue where venue_id ='".$venue_id ."'";
  //echo $sql;
  $rs=$this->query($sql);	
}

function addStandardRatesVenue($venue_unique_id,$rate_name_en,$rate_name_sp,$description_en,$description_sp,$price_mx,$price_us,$venue_id=false)
{
			$sql="INSERT INTO ".$this->prefix()."venue_rates 
					SET   venue_unique_id = '".$venue_unique_id."',
						  rate_name_en = '".$rate_name_en."',
						  rate_name_sp = '".$rate_name_sp."',
						  description_en = '".$description_en."',
						  description_sp = '".$description_sp."',
						  price_mx = '".$price_mx."',
						  price_us = '".$price_us."',
						  venue_id = '".$venue_id."'";
						 // echo $sql;
			$rs=$this->query($sql);	
}

function editStandardRatesVenue($rate_name_en,$rate_name_sp,$description_en,$description_sp,$price_mx,$price_us,$exit_rate_id)
{
			$sql="UPDATE ".$this->prefix()."venue_rates 
								  SET rate_name_en = '".$rate_name_en."',
								  rate_name_sp = '".$rate_name_sp."',
								  description_en = '".$description_en."',
								  description_sp = '".$description_sp."',
								  price_mx = '".$price_mx."',
								  price_us = '".$price_us."'
				WHERE venue_rates_id = '".$exit_rate_id."'";
			$rs=$this->query($sql);	
}

function getVenueStandardRate($venue_unique_id)
{
	$sql="SELECT * FROM ".$this->prefix()."venue_rates WHERE venue_unique_id = '".$venue_unique_id."'";
	return $this->query($sql);
}

function getStanRateByVenueId($venue_rates_id)
{
	$sql="SELECT * FROM ".$this->prefix()."venue_rates WHERE venue_rates_id  = '".$venue_rates_id ."'";
	return $this->query($sql);
}

function getStandRtByVenId($venue_id)
{
	$sql="SELECT * FROM ".$this->prefix()."venue_rates WHERE venue_id  = '".$venue_id ."'";
	return $this->query($sql);
}

function del_standard_rates_venue($venue_rates_id)
{
	$sql="DELETE FROM ".$this->prefix()."venue_rates WHERE venue_rates_id = '".$venue_rates_id."'";
	return $this->query($sql);
}

function delvenuerate($venue_id)
{
	$sql="DELETE FROM ".$this->prefix()."venue_rates WHERE venue_id = '".$venue_id."'";
	return $this->query($sql);
}

function updateVenStanRt($venue_unique_id,$venue_id)
{
	$sql = "UPDATE ".$this->prefix()."venue_rates SET venue_id='".$venue_id."' WHERE venue_unique_id = '".$venue_unique_id."'";
	//echo $sql;exit;
	return $this->query($sql);
}
function checkVenue($venue_unique_id)
{
  $sql="select * from  ".$this->prefix()."venue where unique_id ='".$venue_unique_id ."'";
  $rs=$this->query($sql);	
}

// --------------------------------------------- Duplicate Venue ---------------------------

function insrtDuplicateVenue($venue_id)
{
	$sql = "insert into ".$this->prefix()."venue (venue_name, venue_name_sp, venue_country,venue_address,venue_short_add_sp, venue_short_add_en,venue_city,venue_state,venue_county,venue_zip,admin_id,venue_active,venue_contact_name,venue_head_manager,venue_authorize_manager,venue_description,venue_description_sp,venue_capacity,venue_url,venue_phone,venue_cell,venue_fax,venue_email,venue_map,venue_media_gallery,venue_image,allowed_commments,allowed_share,show_FB_like,public_privacy,private_privacy,tags,venue_us_tell,venue_nextel,venue_fb_page,venue_twitter_account,standard_rate) 
			select venue_name, venue_name_sp, venue_country,venue_address,venue_short_add_sp, venue_short_add_en,venue_city,venue_state,venue_county,venue_zip,admin_id,venue_active,venue_contact_name,venue_head_manager,venue_authorize_manager,venue_description,venue_description_sp,venue_capacity,venue_url,venue_phone,venue_cell,venue_fax,venue_email,venue_map,venue_media_gallery,venue_image,allowed_commments,allowed_share,show_FB_like,public_privacy,private_privacy,tags,venue_us_tell,venue_nextel,venue_fb_page,venue_twitter_account,standard_rate
			from ".$this->prefix()."venue
			where venue_id = ".$venue_id;
	$this->query($sql);
	return mysql_insert_id();
}
function editvenuedate($venue_id,$venue_unique_id)
{
	$sql = "UPDATE ".$this->prefix()."venue SET post_date='".$venue_unique_id."',unique_id = '".$venue_unique_id."',venue_stat = 1 WHERE venue_id = '".$venue_id."'";
	$this->query($sql);
}





// ========================== Venue ===============================



function get_event_id($unique_id)
{
	$sql="SELECT * FROM ".$this->prefix()."general_events WHERE unique_id = '".$unique_id."'";
	return $this->query($sql);
}

function get_sub_event_id($unique_id)
{
	$sql="SELECT * FROM ".$this->prefix()."general_subevents WHERE unique_id = '".$unique_id."'";
	return $this->query($sql);
}


function addSubEvent($event_name_sp,$event_name_en,$short_desc_sp,$short_desc_en,$event_start_date_time,$event_start_ampm,$event_end_date_time,$event_end_ampm,$venue_state,$venue_county,$venue_city,$venue,$page_content_en,$page_content_sp,$event_tag,$file_name,$identical_function,$recurring,$sub_events,$Paypal,$Bank,$Oxxo,$Mobile,$Offline,$publish_date,$event_time,$event_time_period,$r_month,$r_month_day,$mon,$tue,$wed,$thu,$fri,$sat,$sun,$r_span_start,$r_span_end,$event_start,$event_end,$all_day,$event_lasts,$attendees,$invitation_only,$password_protect_check,$pass_protected,$radio_access,$pay_ticket_fee,$promo_charge,$paper_less_mob_ticket,$print,$will_call,$event_id,$status,$unique_id,$privacy,$event_id)
{
	//echo "hii".$event_name_en; exit;
	$sql="INSERT INTO ".$this->prefix()."general_subevents SET 
															admin_id = '".$_SESSION['ses_user_id']."',
															parent_id = '".$event_id."',
															
															event_name_sp = '".$event_name_sp."',
															event_name_en = '".$event_name_en."',
															
															event_short_desc_en = '".$short_desc_en."',
															event_short_desc_sp = '".$short_desc_sp."',
															
															event_start_date_time ='".$event_start_date_time."',
															event_start_ampm = '".$event_start_ampm."',
															event_end_date_time = '".$event_end_date_time."',
															event_end_ampm = '".$event_end_ampm."',
															event_venue_state = '".$venue_state."',
															event_venue_county = '".$venue_county."',
															event_venue_city = '".$venue_city."',
															event_venue = '".$venue."',
														    event_details_en = '".$page_content_en."',
														    event_details_sp = '".$page_content_sp."',
															event_tag = '".$event_tag."',
															event_photo = '".$file_name."',
														    identical_function = '".$identical_function."',
														    recurring = '".$recurring."',
															sub_events = '".$sub_events."',
															
															Paypal = '".$Paypal."',
															Bank_deposite = '".$Bank."',
															Oxxo_Payment = '".$Oxxo."',
															Mobile_Payment = '".$Mobile."',
															Offline_Payment = '".$Offline."',
															
															publish_date = '".$publish_date."',
															
															event_time = '".$event_time."',
															event_time_period = '".$event_time_period."',
															r_month = '".$r_month."',
															r_month_day = '".$r_month_day."',
															mon = '".$mon."',
															tue = '".$tue."',
															wed = '".$wed."',
															thu = '".$thu."',
															fri = '".$fri."',
															sat = '".$sat."',
															sun = '".$sun."',
															r_span_start = '".$r_span_start."',
															r_span_end = '".$r_span_end."',
															event_start = '".$event_start."',
															event_end = '".$event_end."',
															all_day = '".$all_day."',
															event_lasts = '".$event_lasts."',
															
															attendees_share = '".$attendees."',
															attendees_invitation = '".$invitation_only."',
															password_protect = '".$password_protect_check."',
															password_protect_text = '".$pass_protected."',
															
															all_access = '".$radio_access."',
															include_promotion = '".$promo_charge."',
															include_payment = '".$pay_ticket_fee."',
															
															paper_less_mob_ticket = '".$paper_less_mob_ticket."',
															print = '".$print."',
															will_call = '".$will_call."',
															unique_id = '".$unique_id."',
															status = '".$status."',
															
															set_privacy = '".$privacy."',
															
															post_date = '".time()."'";
	$rs=$this->query($sql);	
	return $last_event_id=mysql_insert_id();
}


function editSavedSubEvent($ses_user_id,$event_name_sp,$event_name_en,$short_desc_sp,$short_desc_en,$event_start_date_time,$event_start_ampm,$event_end_date_time,$event_end_ampm,$venue_state,$venue_county,$venue_city,$venue,$page_content_en,$page_content_sp,$event_tag,$file_name,$identical_function,$recurring,$sub_events,$Paypal,$Bank,$Oxxo,$Mobile,$Offline,$publish_date,$event_time,$event_time_period,$r_month,$r_month_day,$mon,$tue,$wed,$thu,$fri,$sat,$sun,$r_span_start,$r_span_end,$event_start,$event_end,$all_day,$event_lasts,$attendees,$invitation_only,$password_protect_check,$pass_protected,$radio_access,$pay_ticket_fee,$promo_charge,$paper_less_mob_ticket,$print,$will_call,$status,$unique_id,$privacy,$event_id)
{
	$sql="UPDATE ".$this->prefix()."general_subevents SET event_name_sp = '".$event_name_sp."',
													   event_name_en = '".$event_name_en."',
													   
													   event_short_desc_en = '".$short_desc_en."',
													   event_short_desc_sp = '".$short_desc_sp."',
													   
													   admin_id ='".$ses_user_id."',
													   event_start_date_time ='".$event_start_date_time."',
													   event_start_ampm = '".$event_start_ampm."',
													   event_end_date_time = '".$event_end_date_time."',
													   event_end_ampm = '".$event_end_ampm."',
													   event_venue_state = '".$venue_state."',
													   event_venue_county = '".$venue_county."',
													   event_venue_city = '".$venue_city."',
													   event_venue = '".$venue."',
													   event_details_en = '".$page_content_en."',
													   event_details_sp = '".$page_content_sp."',
													   event_tag = '".$event_tag."',
													   event_photo = '".$file_name."',
													   identical_function = '".$identical_function."',
													   recurring = '".$recurring."',
													   sub_events = '".$sub_events."',
													   
													    Paypal = '".$Paypal."',
														Bank_deposite = '".$Bank."',
														Oxxo_Payment = '".$Oxxo."',
														Mobile_Payment = '".$Mobile."',
														Offline_Payment = '".$Offline."',
														
														publish_date = '".$publish_date."',
														
														event_time = '".$event_time."',
														event_time_period = '".$event_time_period."',
														r_month = '".$r_month."',
														r_month_day = '".$r_month_day."',
														mon = '".$mon."',
														tue = '".$tue."',
														wed = '".$wed."',
														thu = '".$thu."',
														fri = '".$fri."',
														sat = '".$sat."',
														sun = '".$sun."',
														r_span_start = '".$r_span_start."',
														r_span_end = '".$r_span_end."',
														event_start = '".$event_start."',
														event_end = '".$event_end."',
														all_day = '".$all_day."',
														event_lasts = '".$event_lasts."',
														
														attendees_share = '".$attendees."',
														attendees_invitation = '".$invitation_only."',
														password_protect = '".$password_protect_check."',
														password_protect_text = '".$pass_protected."',
														
														all_access = '".$radio_access."',
														include_promotion = '".$promo_charge."',
														include_payment = '".$pay_ticket_fee."',
														
														paper_less_mob_ticket = '".$paper_less_mob_ticket."',
														print = '".$print."',
														will_call = '".$will_call."',
														set_privacy = '".$privacy."',
														status = '".$status."'
															
													   WHERE unique_id = '".$unique_id."'";
	$rs=$this->query($sql);	
}


function get_sub_event($id)
{
	$sql="SELECT * FROM ".$this->prefix()."general_subevents WHERE event_id = '".$id."'";
	return $this->query($sql);
}

function get_sub_event_ticket($id)
{
	$sql="SELECT * FROM ".$this->prefix()."final_tickets WHERE ticket_id = '".$id."'";
	return $this->query($sql);
}




//function editSavedEventEdit($ses_user_id,$event_name_sp,$event_name_en,$short_desc_sp,$short_desc_en,$event_start_date_time,$event_start_ampm,$event_end_date_time,$event_end_ampm,$venue_state,$venue_county,$venue_city,$venue,$page_content_en,$page_content_sp,$event_tag,$file_name,$identical_function,$recurring,$sub_events,$Paypal,$Bank,$Oxxo,$Mobile,$Offline,$publish_date,$event_time,$event_time_period,$r_month,$r_month_day,$mon,$tue,$wed,$thu,$fri,$sat,$sun,$r_span_start,$r_span_end,$event_start,$event_end,$all_day,$event_lasts,$attendees,$invitation_only,$password_protect_check,$pass_protected,$radio_access,$pay_ticket_fee,$promo_charge,$paper_less_mob_ticket,$print,$will_call,$status,$privacy,$eve_id) // if  require then use event_photo = '".$file_name."',.//

function editSavedEventEdit($ses_user_id,$event_name_sp,$event_name_en,$short_desc_sp,$short_desc_en,$event_start_date_time,$event_start_ampm,$event_end_date_time,$event_end_ampm,$venue_state,$venue_county,$venue_city,$venue,$page_content_en,$page_content_sp,$event_tag,$identical_function,$recurring,$sub_events,$Paypal,$Bank,$Oxxo,$Mobile,$Offline,$publish_date,$event_time,$event_time_period,$r_month,$r_month_day,$mon,$tue,$wed,$thu,$fri,$sat,$sun,$r_span_start,$r_span_end,$event_start,$event_end,$all_day,$event_lasts,$attendees,$invitation_only,$password_protect_check,$pass_protected,$radio_access,$pay_ticket_fee,$promo_charge,$paper_less_mob_ticket,$print,$will_call,$status,$privacy,$eve_id)
{
	$sql="UPDATE ".$this->prefix()."general_events SET event_name_sp = '".$event_name_sp."',
													   event_name_en = '".$event_name_en."',
													   event_short_desc_en = '".$short_desc_en."',
													   event_short_desc_sp = '".$short_desc_sp."',
													   admin_id ='".$ses_user_id."',
													   event_start_date_time ='".$event_start_date_time."',
													   event_start_ampm = '".$event_start_ampm."',
													   event_end_date_time = '".$event_end_date_time."',
													   event_end_ampm = '".$event_end_ampm."',
													   event_venue_state = '".$venue_state."',
													   event_venue_county = '".$venue_county."',
													   event_venue_city = '".$venue_city."',
													   event_venue = '".$venue."',
													   event_details_en = '".$page_content_en."',
													   event_details_sp = '".$page_content_sp."',
													   event_tag = '".$event_tag."',
													   
													   identical_function = '".$identical_function."',
													   recurring = '".$recurring."',
													   sub_events = '".$sub_events."',
													    Paypal = '".$Paypal."',
														Bank_deposite = '".$Bank."',
														Oxxo_Payment = '".$Oxxo."',
														Mobile_Payment = '".$Mobile."',
														Offline_Payment = '".$Offline."',
														publish_date = '".$publish_date."',
														event_time = '".$event_time."',
														event_time_period = '".$event_time_period."',
														r_month = '".$r_month."',
														r_month_day = '".$r_month_day."',
														mon = '".$mon."',
														tue = '".$tue."',
														wed = '".$wed."',
														thu = '".$thu."',
														fri = '".$fri."',
														sat = '".$sat."',
														sun = '".$sun."',
														r_span_start = '".$r_span_start."',
														r_span_end = '".$r_span_end."',
														event_start = '".$event_start."',
														event_end = '".$event_end."',
														all_day = '".$all_day."',
														event_lasts = '".$event_lasts."',
														attendees_share = '".$attendees."',
														attendees_invitation = '".$invitation_only."',
														password_protect = '".$password_protect_check."',
														password_protect_text = '".$pass_protected."',
														all_access = '".$radio_access."',
														include_promotion = '".$promo_charge."',
														include_payment = '".$pay_ticket_fee."',
														paper_less_mob_ticket = '".$paper_less_mob_ticket."',
														print = '".$print."',
														will_call = '".$will_call."',
														status = '".$status."',
														set_privacy = '".$privacy."'
													    WHERE event_id = '".$eve_id."'"; //echo $sql;exit;
	//echo $sql;
	$rs=$this->query($sql);	
}

function checkSavedEventEdit($event_id)
{
  $sql="select * from  ".$this->prefix()."general_events where event_id='".$event_id."'";
  //echo $sql;
  $rs=$this->query($sql);	
}

function get_final_MultiEvent($event_id)
{
 $sql = "SELECT TM.*,C.city_name city_name_multi,S.state_name state_name_multi,V.venue_name venue_name_multi FROM ".$this->prefix()."final_multi_event TM LEFT join ".$this->prefix()."venue V ON (TM.multi_venue = V.venue_id ) LEFT join ".$this->prefix()."state S on (S.id = TM.multi_venue_state) LEFT join ".$this->prefix()."city C on (C.id = TM.multi_venue_city) WHERE TM.event_id = '".$event_id."'"; 
 $this->query($sql);
}



function Add_auto_save_eventtype($event_types,$last_event_id)
{
   if(count($event_types)>0)
   {
	   for($a=0;$a<count($event_types);$a++)
	   {
			$sql="INSERT INTO ".$this->prefix()."event_types SET 
												event_id = '".$last_event_id."',
												event_master_type_id = '".$event_types[$a]."'";
												//echo $sql;
			$rs=$this->query($sql);	
	   }
   }
}

function del_event_type($event_id)
{
	$sql="DELETE FROM ".$this->prefix()."event_types WHERE event_id  = '".$event_id."'";
	return $this->query($sql);
}



function getSubeventTicketById($ticket_id)
{
	$sql = "SELECT * FROM ".$this->prefix()."sub_event_tickets  WHERE ticket_id = '".$ticket_id."'";
	$this->query($sql);
}

function del_sub_event_cat($sub_event_id)
{
	$sql="DELETE FROM ".$this->prefix()."category_by_sub_event WHERE sub_event_id = '".$sub_event_id."'";
	return $this->query($sql);
}
function chkExistsubcat($last_event_id,$category_id)
{
	$sql="SELECT * FROM ".$this->prefix()."category_by_event WHERE category_id = '".$category_id."' AND event_id = '".$last_event_id."' ";
	//echo $sql;
	$query = $this->query($sql);
	return mysql_num_rows($query);
}
function chkExistsubcategory($last_event_id,$category_id)
{
	$sql="SELECT * FROM ".$this->prefix()."category_by_sub_event WHERE category_id = '".$category_id."' AND sub_event_id = '".$last_event_id."' ";
	//echo $sql;
	$query = $this->query($sql);
	return mysql_num_rows($query);
}

function addSavedSubCat($category_id,$sub_event_id,$event_id)
{
			$sql="INSERT INTO ".$this->prefix()."category_by_sub_event SET event_id = '".$event_id."',
																	   sub_event_id = '".$sub_event_id."',
																	   category_id = '".$category_id."'";
			$rs=$this->query($sql);	
}

function del_sub_event_type($sub_event_id)
{
	$sql="DELETE FROM ".$this->prefix()."type_by_sub_event WHERE sub_event_id  = '".$sub_event_id."'";
	return $this->query($sql);
}

function Add_auto_sub_save_eventtype($event_types,$sub_event_id,$event_id)
{
   if(count($event_types)>0)
   {
	   for($a=0;$a<count($event_types);$a++)
	   {
			$sql="INSERT INTO ".$this->prefix()."type_by_sub_event SET 
												event_id = '".$event_id."',
												sub_event_id = '".$sub_event_id."',
												event_master_type_id = '".$event_types[$a]."'";
												//echo $sql;
			$rs=$this->query($sql);	
	   }
   }
}

function del_sub_tickets($ticket_id)
{
	$sql="DELETE FROM ".$this->prefix()."sub_event_tickets  WHERE ticket_id = '".$ticket_id."' "; 
	$this->query($sql);
}


/////////////////////////DELETE EVENTS AND CORRESPONDINGS/////////////////////////////

function delete_event($event_id)
{
	$sql="DELETE FROM ".$this->prefix()."general_events WHERE event_id = '".$event_id."'";
	
	return $this->query($sql);
}

function delete_event_type($event_id)
{
	$sql="DELETE FROM ".$this->prefix()."event_types WHERE event_id = '".$event_id."'";
	
	return $this->query($sql);
}

function delete_event_category($event_id)
{
	$sql="DELETE FROM ".$this->prefix()."category_by_event WHERE event_id = '".$event_id."'";
	
	return $this->query($sql);
}

function delete_event_ticket($event_id)
{
	$sql="DELETE FROM ".$this->prefix()."final_multi_event WHERE event_id = '".$event_id."'";
	
	return $this->query($sql);
}	

function delete_sub_event($event_id)
{
	$sql="DELETE FROM ".$this->prefix()."general_subevents WHERE parent_id = '".$event_id."'";
	
	return $this->query($sql);
}

function delete_sub_event_type($event_id)
{
	$sql="DELETE FROM ".$this->prefix()."type_by_sub_event WHERE event_id = '".$event_id."'";
	
	return $this->query($sql);
}

function delete_sub_event_category($event_id)
{
	$sql="DELETE FROM ".$this->prefix()."category_by_sub_event WHERE event_id = '".$event_id."'";
	
	return $this->query($sql);
}

function delete_sub_event_ticket($event_id)
{
	$sql="DELETE FROM ".$this->prefix()."sub_event_tickets WHERE parent_id = '".$event_id."'";
	
	return $this->query($sql);
}	

function getCityById($city_id)
{
	$sql="SELECT * FROM ".$this->prefix()."city WHERE id = '".$city_id."'";
	return $this->query($sql);
}



// ========================== Performer  ===============================

function checkSavedPerformer($uid)
{
  $sql="select * from  ".$this->prefix()."performer where unique_id='".$uid."'";
 // echo $sql."<br>";
  $rs=$this->query($sql);	
}

function get_performer_pid($performer_id)
{
  $sql="select * from  ".$this->prefix()."performer where performer_id='".$performer_id."'";
 // echo $sql."<br>";
  $rs=$this->query($sql);	
}

function del_per_by_pid($performer_id)
{
	$sql="DELETE FROM ".$this->prefix()."performer WHERE performer_id = '".$performer_id."'";
	return $this->query($sql);
}

function getCatPerformer($performer_id)
{
	$sql="SELECT * FROM ".$this->prefix()."category_by_performer WHERE performer_id = '".$performer_id."' ";
	//echo $sql;
	$query = $this->query($sql);
	return mysql_num_rows($query);
}

function chkExistSavedcatPerformer($performer_id,$category_id)
{
	$sql="SELECT * FROM ".$this->prefix()."category_by_performer WHERE category_id = '".$category_id."' AND performer_id = '".$performer_id."' ";
	//echo $sql;
	$query = $this->query($sql);
	return mysql_num_rows($query);
}

function addSavedCatByPerfrm($category_id,$performer_id)
{
			$sql="INSERT INTO ".$this->prefix()."category_by_performer SET performer_id = '".$performer_id."',
																	   category_id = '".$category_id."'";
			$rs=$this->query($sql);	
}
function addperformertype($performer_master_type_id,$performer_id)
{
			$sql="INSERT INTO ".$this->prefix()."performer_types SET performer_id = '".$performer_id."',performer_master_type_id = '".$performer_master_type_id."'";
			$rs=$this->query($sql);	
}


function addSavedPerformer($admin_id,$performer_name_sp,$performer_name_en,$performer_short_add_sp,$performer_short_add_en,$performer_state,$performer_county,$performer_county,$performer_city,$performer_zip,$performer_address,$performer_contact_name,$performer_phone,$performer_fax,$performer_cell,$performer_email,$performer_url,$avail_performanace,$manager_name,$manager_phone,$manager_fax,$manager_cell,$manager_email,$manager_url,$performer_description_sp,$performer_description_en,$privacy,$st_rate,$activate_status,$file_name,$performer_tags,$unique_id,$publish_date = false)
{
	$sql="INSERT INTO ".$this->prefix()."performer 
				SET admin_id = '".$_SESSION['ses_user_id']."',
				   performer_name_sp = '".$performer_name_sp."',
				   performer_name_en ='".$performer_name_en."',
				   performer_short_add_sp = '".$performer_short_add_sp."',
				   performer_short_add_en = '".$performer_short_add_en."',
				   performer_state = '".$performer_state."',
				   performer_county = '".$performer_county."',
				   performer_city = '".$performer_city."',
				   performer_zip = '".$performer_zip."',
				   performer_address = '".$performer_address."',
				   performer_contact_name = '".$performer_contact_name."',
				   performer_phone  = '".$performer_phone ."',
				   performer_fax = '".$performer_fax."',
				   performer_cell = '".$performer_cell."',
				   performer_email = '".$performer_email."',
				   performer_url = '".$performer_url."',
				   avail_performanace = '".$avail_performanace."',
				   manager_name = '".$manager_name."',
				   manager_phone = '".$manager_phone."',
				   manager_fax = '".$manager_fax."',
				   manager_cell = '".$manager_cell."',
				   manager_email = '".$manager_email."',
				   manager_url = '".$manager_url."',
				   performer_description_sp = '".$performer_description_sp."',
				   performer_description_en = '".$performer_description_en."',
				   privacy = '".$privacy."',
				   st_rate = '".$st_rate."',
				   activate_status = '".$activate_status."',
				   performer_photo = '".$file_name."',
				   performer_tags = '".$performer_tags."',
				   publish_date  = '".$publish_date."',
				   unique_id = '".$unique_id."',
				   post_date = '".time()."'";
				  // echo $sql;exit;
  $rs=$this->query($sql);	
  return $last_event_id=mysql_insert_id();
}

function delPerCat($performer_id)
{
	$sql="DELETE FROM ".$this->prefix()."category_by_performer WHERE performer_id = '".$performer_id."'";
	return $this->query($sql);
}

function delPertypes($performer_id)
{
	$sql="DELETE FROM ".$this->prefix()."performer_types WHERE performer_id = '".$performer_id."'";
	return $this->query($sql);
}

function getPertypes($performer_id)
{
	$sql="SELECT * FROM ".$this->prefix()."performer_types WHERE performer_id = '".$performer_id."'";
	return $this->query($sql);
}


function editSavedPerformer($performer_name_sp,$performer_name_en,$performer_short_add_sp,$performer_short_add_en,$performer_state,$performer_county,$performer_county,$performer_city,$performer_zip,$performer_address,$performer_contact_name,$performer_phone,$performer_fax,$performer_cell,$performer_email,$performer_url,$avail_performanace,$manager_name,$manager_phone,$manager_fax,$manager_cell,$manager_email,$manager_url,$performer_description_sp,$performer_description_en,$privacy,$st_rate,$activate_status,$file_name,$performer_tags,$unique_id,$performer_id,$publish_date = false)
{
	$sql="UPDATE ".$this->prefix()."performer 
				SET 
				   performer_name_sp = '".$performer_name_sp."',
				   performer_name_en ='".$performer_name_en."',
				   performer_short_add_sp = '".$performer_short_add_sp."',
				   performer_short_add_en = '".$performer_short_add_en."',
				   performer_state = '".$performer_state."',
				   performer_county = '".$performer_county."',
				   performer_city = '".$performer_city."',
				   performer_zip = '".$performer_zip."',
				   performer_address = '".$performer_address."',
				   performer_phone  = '".$performer_phone ."',
				   performer_contact_name = '".$performer_contact_name."',
				   performer_fax = '".$performer_fax."',
				   performer_cell = '".$performer_cell."',
				   performer_email = '".$performer_email."',
				   performer_url = '".$performer_url."',
				   avail_performanace = '".$avail_performanace."',
				   manager_name = '".$manager_name."',
				   manager_phone = '".$manager_phone."',
				   manager_fax = '".$manager_fax."',
				   manager_cell = '".$manager_cell."',
				   manager_email = '".$manager_email."',
				   manager_url = '".$manager_url."',
				   performer_description_sp = '".$performer_description_sp."',
				   performer_description_en = '".$performer_description_en."',
				   privacy = '".$privacy."',
				   st_rate = '".$st_rate."',
				   activate_status = '".$activate_status."',
				   publish_date = '".$publish_date."',
				   performer_photo = '".$file_name."',
				   performer_tags = '".$performer_tags."'
			WHERE performer_id = '".$performer_id."'";
				//echo $sql;exit;
  $rs=$this->query($sql);	
  
}

function addStandardRates($unique_id,$rate_name_en,$rate_name_sp,$description_en,$description_sp,$price_mx,$price_us)
{
			$sql="INSERT INTO ".$this->prefix()."performer_rates SET unique_id = '".$unique_id."',
																	  rate_name_en = '".$rate_name_en."',
																	  rate_name_sp = '".$rate_name_sp."',
																	  description_en = '".$description_en."',
																	  description_sp = '".$description_sp."',
																	  price_mx = '".$price_mx."',
																	  price_us = '".$price_us."'";
			$rs=$this->query($sql);	
}
function addStandardRatesEDIT($unique_id,$rate_name_en,$rate_name_sp,$description_en,$description_sp,$price_mx,$price_us,$performer_id)
{
			$sql="INSERT INTO ".$this->prefix()."performer_rates SET unique_id = '".$unique_id."',
																	  rate_name_en = '".$rate_name_en."',
																	  rate_name_sp = '".$rate_name_sp."',
																	  description_en = '".$description_en."',
																	  description_sp = '".$description_sp."',
																	  price_mx = '".$price_mx."',
																	  performer_id = '".$performer_id."',
																	  price_us = '".$price_us."'";
			$rs=$this->query($sql);	
}


function editStandardRates($rate_name_en,$rate_name_sp,$description_en,$description_sp,$price_mx,$price_us,$exit_rate_id)
{
			$sql="UPDATE ".$this->prefix()."performer_rates 
								  SET 
								  rate_name_en = '".$rate_name_en."',
								  rate_name_sp = '".$rate_name_sp."',
								  description_en = '".$description_en."',
								  description_sp = '".$description_sp."',
								  price_mx = '".$price_mx."',
								  price_us = '".$price_us."'
				WHERE performer_rates_id = '".$exit_rate_id."'";
			$rs=$this->query($sql);	
}

function getStandardRate($performer_id)
{
	$sql="SELECT * FROM ".$this->prefix()."performer_rates WHERE performer_id = '".$performer_id."'";
	return $this->query($sql);
}

function getStandardRateById($performer_rates_id)
{
	$sql="SELECT * FROM ".$this->prefix()."performer_rates WHERE performer_rates_id  = '".$performer_rates_id ."'";
	return $this->query($sql);
}

function del_standard_rates($performer_rates_id)
{
	$sql="DELETE FROM ".$this->prefix()."performer_rates WHERE performer_rates_id = '".$performer_rates_id."'";
	return $this->query($sql);
}

function del_standard_rates_by_pid($performer_id)
{
	$sql="DELETE FROM ".$this->prefix()."performer_rates WHERE performer_id = '".$performer_id."'";
	return $this->query($sql);
}

function updatePerStanRt($performer_unique_id,$performer_id)
{
	$sql = "UPDATE ".$this->prefix()."performer_rates SET performer_id='".$performer_id."' WHERE unique_id = '".$performer_unique_id."'";
	//echo $sql;exit;
	return $this->query($sql);
}

function getPerRateBySess($uid)
{
  $sql="select * from  ".$this->prefix()."performer_rates where unique_id='".$uid."'";
  $rs=$this->query($sql);	
}

function allPerformerList($limit,$performer_state = false,$performer_country = false,$performer_city = false)			
{
	$whereClause = '';
	if($performer_state)
		$whereClause = " AND performer_state = '".$performer_state."'";	
	
	if($performer_country)
		$whereClause .= " AND performer_county = '".$performer_country."'";	
	
	if($performer_city)
		$whereClause .= " AND  performer_city = '".$performer_city."'";	
		
	if($_SESSION['ses_user_id']!=1)
		$whereClause .= " AND  B.admin_id = '".$_SESSION['ses_user_id']."'";	
	//echo $whereClause;exit;
	
	$sql="SELECT A.*,B.city_name,C.county_name,D.state_name FROM ".$this->prefix()."performer AS A, ".$this->prefix()."city AS B, ".$this->prefix()."county AS C, ".$this->prefix()."state D WHERE A.performer_city = B.id AND A.performer_county = C.id AND A.performer_state = D.id $whereClause ORDER BY C.county_name,B.city_name,A.performer_name_en ASC $limit";
	
	//echo $sql; 
	$this->query($sql);
}

function allPerformerListCount($performer_state = false,$performer_country = false,$performer_city = false)			
{
	$whereClause = '';
	if($performer_state)
		$whereClause = " AND performer_state = '".$performer_state."'";	
	
	if($performer_country)
		$whereClause .= " AND performer_county = '".$performer_country."'";	
	
	if($performer_city)
		$whereClause .= " AND  performer_city = '".$performer_city."'";	
		
	if($_SESSION['ses_user_id']!=1)
		$whereClause .= " AND  B.admin_id = '".$_SESSION['ses_user_id']."'";	
	
	$sql="SELECT A.*,B.city_name,C.county_name,D.state_name FROM ".$this->prefix()."performer AS A, ".$this->prefix()."city AS B, ".$this->prefix()."county AS C, ".$this->prefix()."state D  WHERE A.performer_city = B.id AND A.performer_county = C.id AND A.performer_state = D.id $whereClause ORDER BY C.county_name,B.city_name,A.performer_name_en ASC ";
	
	//echo $sql; 
	$this->query($sql);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function ExportallEventList($limit = false,$venue_state = false,$venue_county = false,$venue_city = false,$venue = false,$show_pastevent = false)			
{
	$whereClause = '';
	if($venue_state)
		$whereClause = " AND A.venue_state = '".$venue_state."'";	
	
	if($venue_county)
		$whereClause .= " AND A.venue_county = '".$venue_county."'";	
	
	if($venue_city)
		$whereClause .= " AND  A.venue_city = '".$venue_city."'";	
	
	if($venue)
		$whereClause .= " AND  event_venue = '".$venue."'";	
		
	if($show_pastevent==0)	
		$whereClause .= " AND ( event_start_date_time >= now() OR ( r_span_end >= now() AND recurring = 1) )";	
		
	if($_SESSION['ses_user_id']!=1)
		$whereClause .= " AND  B.admin_id = '".$_SESSION['ses_user_id']."'";	
	
	$sql="SELECT A.venue_name,C.city_name,S.county_name,B.* FROM ".$this->prefix()."venue AS A, ".$this->prefix()."general_events AS B, ".$this->prefix()."city AS C, ".$this->prefix()."county S  WHERE A.venue_id = B.event_venue AND C.id = A.venue_city AND S.id = A.venue_county $whereClause ORDER BY S.county_name,C.city_name,A.venue_name,B.event_start_date_time ASC ";
	
	//$sql = "SELECT A.venue_name,C.city_name,S.county_name,B.* FROM kcp_general_events AS B  LEFT JOIN kcp_venue AS A ON(A.venue_id = B.event_venue) LEFT JOIN kcp_city AS C  ON(C.id = B.event_venue_city) LEFT JOIN kcp_county S ON(S.id = B.event_venue_county) WHERE 1 = 1 $whereClause ORDER BY S.county_name,C.city_name,A.venue_name,B.event_start_date_time ASC $limit";
	
	//echo $sql; 
	$this->query($sql);
}

function delPage($page_id)
{
	$sql="DELETE FROM ".$this->prefix()."page WHERE page_id = '".$page_id."'";
	return $this->query($sql);
}

function getPageDetails($page_id )
{
  $sql="select * from  ".$this->prefix()."page where page_id ='".$page_id ."'";
  $rs=$this->query($sql);	
}
 
                     
function add_gallery($admin_id,$media_url,$media_format)
	{
		$sql="INSERT INTO ".$this->prefix()."media set admin_id='".$admin_id."',media_url='".$media_url."',media_format='".$media_format."',set_privacy='1'";
		//echo $sql;
		$rs=$this->query($sql);
		return mysql_insert_id();
	}
function add_gallery_withEvent($media_id,$event_id)
	{
		$sql="INSERT INTO ".$this->prefix()."event_gallery set media_id='".$media_id."',event_id='".$event_id."'";
		//echo $sql;
		$rs=$this->query($sql);
		//return mysql_insert_id();
	}
	function add_media_language($media_id,$language,$media_name,$caption,$alternate_text,$description)
	{
		$sql="INSERT INTO ".$this->prefix()."media_language set media_id='".$media_id."',language_id='".$language."',media_name='".$media_name."',caption='".$caption."',alternative_text='".$alternate_text."',description='".$description."'";
		//echo $sql;
		$rs=$this->query($sql);
		return mysql_insert_id();
	}
	
function update_media_gallery($set_privacy,$media_id)
{
    $sql="update ".$this->prefix()."media set set_privacy='".$set_privacy."' WHERE  media_id=".$media_id;
    //echo $sql;
    $rs=$this->query($sql);
    
}

function event_details_byID($event_id){

	$sql='SELECT V.*,E.event_id as e_id, E.`event_name_en`, E.`event_name_sp`, E.`event_start_date_time`, E.`event_start_ampm`, E.`event_end_date_time`, E.`event_end_ampm`, E.`admin_id` as event_creator, E.`event_short_desc_en`, E.`event_short_desc_sp`, S.state_name as st_name,C.city_name as city FROM '.$this->prefix().'general_events E Left join '.$this->prefix().'venue V ON (E.event_venue = V.venue_id ) Left join '.$this->prefix().'state S on (S.id = E.event_venue_state)  Left join '.$this->prefix().'city C on (C.id = E.event_venue_city) WHERE E.event_id="'.$event_id.'" AND E.event_status="Y" ';
	//echo $sql;
	return $this->query($sql);	
}

function allGalleryByID($event_id,$limit){

	$sql="SELECT *,M.media_id as m_id FROM ".$this->prefix()."media M Left join ".$this->prefix()."media_language ML ON (M.media_id = ML.media_id ) Left join ".$this->prefix()."event_gallery EG on (EG.media_id = M.media_id)  WHERE EG.event_id=".$event_id."  AND ML.language_id='es_MX' Order By M.media_id DESC $limit";
	//echo $sql;
	return $this->query($sql);
}

function allGalleryByIDNoLimit($event_id) {

    $sql = "SELECT *, M.media_id as m_id FROM " . $this->prefix() . "media M Left join " . $this->prefix() . "media_language ML ON (M.media_id = ML.media_id ) Left join " . $this->prefix() . "event_gallery EG on (EG.media_id = M.media_id)  WHERE EG.event_id=" . $event_id . "  AND ML.language_id='es_MX' Order By M.media_id DESC";
    //echo $sql;
    return $this->query($sql);
}

function allGalleryByID_count($event_id){

	$sql="SELECT * FROM ".$this->prefix()."media M Left join ".$this->prefix()."media_language ML ON (M.media_id = ML.media_id ) Left join ".$this->prefix()."event_gallery EG on (EG.media_id = M.media_id)  WHERE EG.event_id=".$event_id."  AND ML.language_id='es_MX' Order By M.media_id DESC";
	//echo $sql;
	return $this->query($sql);
}

function allMediaByID($media_id){

	$sql="SELECT * FROM ".$this->prefix()."media  WHERE media_id=".$media_id;
	//echo $sql;
	return $this->query($sql);
}

function allGalleryNotInEvent($event_id,$admin_id){

  $sql="SELECT *,km.media_id as m_id FROM ".$this->prefix()."media as km, ".$this->prefix()."media_language as kml  WHERE km.media_id=kml.media_id and km.media_id NOT IN (SELECT media_id FROM ".$this->prefix()."event_gallery WHERE event_id ='".$event_id."') and (km.set_privacy='0' OR (km.admin_id='".$admin_id."' AND km.set_privacy='1'))  and kml.language_id='es_MX'  order by km.media_id DESC";
	//echo $sql;
	return $this->query($sql);
}

function allGalleryNotInEventPagination($event_id, $admin_id, $limit, $offset){

  $sql="SELECT *,km.media_id as m_id FROM ".$this->prefix()."media as km, ".$this->prefix()."media_language as kml  WHERE km.media_id=kml.media_id and km.media_id NOT IN (SELECT media_id FROM ".$this->prefix()."event_gallery WHERE event_id ='".$event_id."') and (km.set_privacy='0' OR (km.admin_id='".$admin_id."' AND km.set_privacy='1'))  and kml.language_id='es_MX' GROUP BY km.media_id order by km.media_id DESC LIMIT $limit OFFSET $offset";
	//echo $sql;
	return $this->query($sql);
}

function deleteMedia($media_id){

  $sql="DELETE ".$this->prefix()."media ,".$this->prefix()."media_language,".$this->prefix()."event_gallery FROM ". $this->prefix()."media INNER JOIN " .$this->prefix()."media_language INNER JOIN ".$this->prefix()."event_gallery  WHERE ".$this->prefix()."media.media_id=".$this->prefix()."media_language.media_id AND ".$this->prefix()."media_language.media_id=".$this->prefix()."event_gallery.media_id AND ".$this->prefix()."media.media_id=".$media_id;
//	DELETE t1, t2 FROM t1 INNER JOIN t2 INNER JOIN t3
//WHERE t1.id=t2.id AND t2.id=t3.id;
	//echo $sql;
	return $this->query($sql);
	
}
function deleteMediaEvent($media_id,$event_id){

	$sql="DELETE  FROM ".$this->prefix()."event_gallery  WHERE media_id=".$media_id." AND event_id=".$event_id;
	//echo $sql;
	return $this->query($sql);
}
function allMediaAdmin($media_id,$admin_id){

	$sql="SELECT * FROM ".$this->prefix()."event_gallery  WHERE media_id=".$media_id." AND admin_id=".$admin_id;
	//echo $sql;
	return $this->query($sql);
}
function getlangMediabyId($media_id){

	$sql="SELECT * FROM ".$this->prefix()."media_language  WHERE media_id=".$media_id;
	//echo $sql;
	return $this->query($sql);
}

function update_media_details($media_id,$language_id,$set_privacy,$media_name,$caption,$alternate_text,$description)
{
    $sql="update ".$this->prefix()."media as m,".$this->prefix()."media_language as ml set m.set_privacy='".$set_privacy."',ml.media_name='".$media_name."',
    ml.caption='".$caption."',ml.alternative_text='".$alternate_text."',ml.description='".$description."' WHERE  m.media_id='".$media_id."' AND
    ml.language_id='".$language_id."' AND m.media_id=ml.media_id";
   //echo $sql;
    $rs=$this->query($sql);
    
}

function allGalleryByMediaID_ES($media_id){

	$sql="SELECT *,M.media_id as m_id FROM ".$this->prefix()."media M Left join ".$this->prefix()."media_language ML ON (M.media_id = ML.media_id ) Left join ".$this->prefix()."event_gallery EG on (EG.media_id = M.media_id)  WHERE EG.media_id=".$media_id."  AND ML.language_id='es_MX'";
	//echo $sql;
	return $this->query($sql);
}

function allGalleryByMediaID_EN($media_id){

	$sql="SELECT *,M.media_id as m_id FROM ".$this->prefix()."media M Left join ".$this->prefix()."media_language ML ON (M.media_id = ML.media_id ) Left join ".$this->prefix()."event_gallery EG on (EG.media_id = M.media_id)  WHERE EG.media_id=".$media_id."  AND ML.language_id='en_US'";
	//echo $sql;
	return $this->query($sql);
}

function delete_lang($media_id){

	$sql="DELETE  FROM ".$this->prefix()."media_language  WHERE media_id=".$media_id;
	//echo $sql;
	return $this->query($sql);
}
function delete_media($media_id){

	$sql="DELETE  FROM ".$this->prefix()."media  WHERE media_id=".$media_id;
	//echo $sql;
	return $this->query($sql);
}
function delete_media_event($media_id){

	$sql="DELETE  FROM ".$this->prefix()."event_gallery  WHERE media_id=".$media_id;
	//echo $sql;
	return $this->query($sql);
}


function update_feature_image($event_id)
{
    $sql="update ".$this->prefix()."event_gallery set feature_image='0' WHERE  event_id=".$event_id;
    //echo $sql;
    $rs=$this->query($sql);
    
}

function set_feature_image($media_id,$event_id)
{
    $sql="update ".$this->prefix()."event_gallery set feature_image='1' WHERE  media_id=".$media_id." AND event_id=".$event_id;
   //echo $sql;
    $rs=$this->query($sql);
    
}

function has_feature_image($event_id)
{
    $sql="SELECT * FROM ".$this->prefix()."event_gallery WHERE event_id=".$event_id."  AND feature_image = 1";
	//echo $sql;
	return $this->query($sql);
    
}

function getFeatureImage($media_id,$event_id){

	$sql="SELECT *,M.media_id as m_id FROM ".$this->prefix()."media M  Left join ".$this->prefix()."event_gallery EG on (EG.media_id = M.media_id)  WHERE EG.media_id=".$media_id."  AND EG.event_id=".$event_id." AND EG.feature_image='1'";
	//echo $sql;
	return $this->query($sql);
}

function update_event_photo($feature_image,$event_id)
{
    $sql="update ".$this->prefix()."general_events set event_photo='".$feature_image."' WHERE event_id=".$event_id;
    //echo $sql;
    $rs=$this->query($sql);
    
}

/*-------------------------------Promotion  Section--------------------------------------------*/
function add_social($social_url,$social_type,$social_name,$social_lang)
	{
		$sql="INSERT INTO ".$this->prefix()."social set social_url='".$social_url."',social_type='".$social_type."',social_name='".$social_name."',social_lang='".$social_lang."'";
		//echo $sql;
		$rs=$this->query($sql);
		return mysql_insert_id();
	}
function add_social_by_admin($social_id,$admin_id)
	{
	      $sql="INSERT INTO ".$this->prefix()."social_by_admin set social_id='".$social_id."',admin_id='".$admin_id."'";
	    // echo $sql;
	      $rs=$this->query($sql);
	      //return mysql_insert_id();
	}

function add_social_on_event($social_id,$e_id)
	{
	      $sql="INSERT INTO ".$this->prefix()."event_social set social_id='".$social_id."',event_id='".$e_id."'";
	     //echo $sql;
	      $rs=$this->query($sql);
	      //return mysql_insert_id();
	}


function add_social_withEvent($social_id,$event_id)
	{
		$sql="INSERT INTO ".$this->prefix()."event_social set social_id='".$social_id."',event_id='".$event_id."'";
		//echo $sql;
		$rs=$this->query($sql);
		//return mysql_insert_id();
	}
function add_social_withAdmin($social_id,$admin_id)
{
	$sql="INSERT INTO ".$this->prefix()."social_by_admin set social_id='".$social_id."',admin_id='".$admin_id."'";
	//echo $sql;
	$rs=$this->query($sql);
	//return mysql_insert_id();
}

function isUrl($social_url){

	$sql="SELECT * FROM ".$this->prefix()."social  WHERE social_url='".$social_url."'";
	//echo $sql;
	return $this->query($sql);
}

function isEventForSocial($social_id,$event_id){

	$sql="SELECT * FROM ".$this->prefix()."event_social  WHERE event_id='".$event_id."' AND social_id='".$social_id."'";
	//echo $sql;
	return $this->query($sql);
}

function getAllSocial($limit){

	$sql="SELECT S.*,S.social_id as sc_id,group_concat(ES.event_id) as eventid,SA.admin_id FROM ".$this->prefix()."social S
	Left join ".$this->prefix()."event_social ES ON (S.social_id = ES.social_id ) 
	Left join ".$this->prefix()."social_by_admin SA ON (SA.social_id = S.social_id) 
	group by S.social_id Order By S.social_id DESC $limit";
	//echo $sql;
	return $this->query($sql);
}

function getAllSocialURL(){

	$sql="SELECT S.* FROM ".$this->prefix()."social S";
	//echo $sql;
	return $this->query($sql);
}

function allPromotionByID($social_id,$admin_id){

	$sql="SELECT *,S.social_id as sc_id FROM ".$this->prefix()."social S Left join ".$this->prefix()."event_social ES ON (S.social_id = ES.social_id ) Left join ".$this->prefix()."social_by_admin SA on (SA.social_id = S.social_id)  WHERE S.social_id=".$social_id." AND SA.admin_id=".$admin_id." Order By S.social_id DESC";
	//echo $sql;
	return $this->query($sql);
}

function update_social($sc_id,$social_url,$social_name,$social_type,$social_lang)
{
    $sql="update ".$this->prefix()."social  set social_url='".$social_url."',social_type='".$social_type."',social_name='".$social_name."',social_lang='".$social_lang."' WHERE social_id=".$sc_id;
   //echo $sql;
    $rs=$this->query($sql);
    
}

function getPromotionAdmiById($admin_id,$social_id){

	$sql="SELECT * FROM ".$this->prefix()."social_by_admin  WHERE admin_id='".$admin_id."' AND social_id='".$social_id."'";
	//echo $sql;
	return $this->query($sql);
}

function deleteEventSocial($event_id){

	$sql="DELETE  FROM ".$this->prefix()."event_social  WHERE event_id=".$event_id;
	//echo $sql;
	return $this->query($sql);
}

function add_event_promo_instruction($e_id,$dpost1,$dpost2,$dpost3,$dpost4,$dpost5)
{
	$sql="INSERT INTO ".$this->prefix()."event_promo_instruction set event_id='".$e_id."',dpost1='".$dpost1."',dpost2='".$dpost2."',dpost3='".$dpost3."',dpost4='".$dpost4."',dpost5='".$dpost5."'";
	//echo $sql;
	$rs=$this->query($sql);
	//return mysql_insert_id();
}
function deletePromoInstruction($event_id){

	$sql="DELETE  FROM ".$this->prefix()."event_promo_instruction  WHERE event_id=".$event_id;
	//echo $sql;
	return $this->query($sql);
}
function getPromotionInstructionById($event_id){

	$sql="SELECT * FROM ".$this->prefix()."event_promo_instruction  WHERE event_id='".$event_id."'";
	//echo $sql;
	return $this->query($sql);
}


function add_promo_schdule($social_id,$event_id,$date_time)
	{
  $sql="INSERT INTO ".$this->prefix()."event_promo_schedule set social_id='".$social_id."',event_id='".$event_id."',date_time='".$date_time."',published='0'";
		//echo $sql;
		$rs=$this->query($sql);
		//return mysql_insert_id();
	}

function allSocialByEvent($event_id){

	$sql="SELECT * FROM ".$this->prefix()."event_social  WHERE event_id='".$event_id."'";
	
	//echo $sql;
	return $this->query($sql);
}
function allPromoScheduleByEvent($event_id){

	$sql="SELECT * FROM ".$this->prefix()."event_promo_schedule  WHERE event_id='".$event_id."'";
	
	echo $sql;
	return $this->query($sql);
}
function deletePromoSchedule($event_id){

	$sql="DELETE  FROM ".$this->prefix()."event_promo_schedule  WHERE event_id=".$event_id;
	//echo $sql;
	return $this->query($sql);
}
function delete_promo($social_id,$event_id){
	
	$sql_sc="DELETE  FROM ".$this->prefix()."social  WHERE social_id=".$social_id;
	$sql_sc_admin="DELETE  FROM ".$this->prefix()."social_by_admin  WHERE social_id=".$social_id;
	$sql_sc_event="DELETE  FROM ".$this->prefix()."event_social  WHERE social_id=".$social_id;
	$sql_sc_time="DELETE  FROM ".$this->prefix()."event_promo_instruction  WHERE event_id=".$event_id."";
	$sql_sc_schedule="DELETE  FROM ".$this->prefix()."event_promo_schedule  WHERE social_id=".$social_id;
	//echo $sql_sc;
	//echo $sql_sc_admin;
	//echo $sql_sc_event;
	//echo $sql_sc_time;
	//echo $sql_sc_schedule;
	$this->query($sql_sc);
	$this->query($sql_sc_admin);
	$this->query($sql_sc_event);
	$this->query($sql_sc_time);
	return $this->query($sql_sc_schedule);
      
}
/*-------------------------------Promotion  Section End--------------------------------------------*/

/*------------------------------ADVERTISE  Section START----------------------------------*/

 function insert_ad($ad_size,$ad_position, $from_date,$duration,$ad_client_id)
	{
		$sql="INSERT INTO ".$this->prefix()."ad set ad_size='".$ad_size."',position_id='".$ad_position."',From_date='".$from_date."', duration='".$duration."',client_id='".$ad_client_id."'";
		echo $sql;
		$rs=$this->query($sql);
		return mysql_insert_id();
	}

function insert_ad_contain_es($result_last_ad_id, $ad_title_es,$ad_contain_es,$ad_text_es,$lenguage_id_es,$ad_url_es,$image_name_es,$call_to_action_es)
	{
		$sql="INSERT INTO ".$this->prefix()."ad_content set ad_id='".$result_last_ad_id."',ad_image_name='".$image_name_es."',ad_alternate_text='".$ad_contain_es."', language_id='".$lenguage_id_es."',ad_title='".$ad_title_es."',ad_text='".$ad_text_es."',link_url='".$ad_url_es."',call_to_action='".$call_to_action_es."' ";
		echo $sql;
		$rs=$this->query($sql);
		return mysql_insert_id();
	}

	
function insert_ad_contain_en($result_last_ad_id, $ad_title_en,$ad_contain_en,$ad_text_en,$lenguage_id_en,$ad_url_en,$image_name_en,$call_to_action_en)
	{
		$sql="INSERT INTO ".$this->prefix()."ad_content set ad_id='".$result_last_ad_id."',ad_image_name='".$image_name_en."',ad_alternate_text='".$ad_contain_en."', language_id='".$lenguage_id_en."',ad_title='".$ad_title_en."',ad_text='".$ad_text_en."',link_url='".$ad_url_en."',call_to_action='".$call_to_action_en."'";
		echo $sql;
		$rs=$this->query($sql);
		return mysql_insert_id();
	}
	
function getAdbyPosSizeEdit($ad_size,$ad_position,$ad_start_date,$end_date,$ad_id){
  
	$sql="SELECT * FROM ".$this->prefix()."ad  WHERE '".$ad_start_date."' >= From_date  and '".$ad_start_date."' <= duration  and position_id='".$ad_position."' AND ad_size='".$ad_size."' AND ad_id!=".$ad_id;
	//echo $sql;
	return $this->query($sql);
}

function getAdbyPosSize($ad_size,$ad_position,$ad_start_date,$end_date){
  
	$sql="SELECT * FROM ".$this->prefix()."ad  WHERE '".$ad_start_date."' >= From_date  and '".$ad_start_date."' <= duration  and position_id='".$ad_position."' AND ad_size='".$ad_size."'";
	//echo $sql;
	return $this->query($sql);
}


function chek_dateduretion($postion_id ,$size)
		{
			 $sql = "SELECT * FROM ".$this->prefix()."ad WHERE position_id=".$postion_id." AND  ad_size ='".$size."'";
			$this->query($sql);
			if($this->num_rows() > 0)
			{
				return  $this->query($sql);
			}else{
			return 0;
			}
		}
		
function delete_ad($ad_id){

	$sql="DELETE  FROM ".$this->prefix()."ad  WHERE ad_id=".$ad_id;
	//echo $sql;
	return $this->query($sql);
}

function delete_client($client_id){

	$sql="DELETE  FROM ".$this->prefix()."ad_clients  WHERE client_id=".$client_id;
	//echo $sql;
	return $this->query($sql);
}

//function getAllAds($limit){
//  
//	$sql="SELECT * FROM ".$this->prefix()."ad as ka join ".$this->prefix()."ad_content as kac ON (ka.ad_id=kac.ad_id) WHERE kac.language_id='es' Order By ka.ad_id DESC $limit";
//	//echo $sql;
//	return $this->query($sql);
//}

function getAllAds($limit,$short_by,$add_then_by){
//  echo $add_then_by;
    
      if($short_by!=""){
          
         $order_by = 'Order By ka.'.$short_by.' DESC';
          
          
      }else if($short_by=="" && $add_then_by!= "" ){
          
           $order_by = 'Order By ka.'.$add_then_by.' DESC';
          
          
      }else if($short_by!="" && $add_then_by!= "")
      {

          $order_by = 'Order By ka.'.$short_by.' , ka.'.$add_then_by.' DESC'; 

      }else{
          
          $order_by = 'Order By ka.ad_id DESC'; 
            
      }
    $sql="SELECT * FROM ".$this->prefix()."ad as ka join ".$this->prefix()."ad_content as kac ON (ka.ad_id=kac.ad_id) WHERE kac.language_id='es' $order_by  $limit";
	
	//echo $sql;
	return $this->query($sql);
}

function getAllAdsCount(){
  
	$sql="SELECT * FROM ".$this->prefix()."ad as ka join ".$this->prefix()."ad_content as kac ON (ka.ad_id=kac.ad_id) WHERE kac.language_id='es' Order By ka.ad_id DESC";
	echo $sql;
	return $this->query($sql);
}

function deleteAd($ad_id){

	$sql="DELETE  FROM ".$this->prefix()."ad  WHERE ad_id=".$ad_id;
	$sql1="DELETE  FROM ".$this->prefix()."ad_content  WHERE ad_id=".$ad_id;
	//echo $sql;
	//echo $sql1;
	$this->query($sql);
        return $this->query($sql1);
}

function es_data_by_id($ad_id){
	  $sql="SELECT * FROM ".$this->prefix()."ad as ka join ".$this->prefix()."ad_content as kac ON (ka.ad_id=kac.ad_id) WHERE kac.language_id='es' And
	  ka.ad_id = $ad_id ";
return $this->query($sql);
}

function en_data_by_id( $ad_id){
	$sql="SELECT * FROM ".$this->prefix()."ad as ka join ".$this->prefix()."ad_content as kac ON (ka.ad_id=kac.ad_id) WHERE kac.language_id='en' And
	ka.ad_id = $ad_id ";
return $this->query($sql);
}

function featch_client_data_by_id($client_id)
{    
    $sql = "SELECT * FROM " . $this->prefix() . "ad_clients WHERE client_id = '". $client_id ."'";
    
    return $this->query($sql);        
}

function featch_ad_data_by_es_id($ad_id )
{
    
    $sql="SELECT * FROM ".$this->prefix()."ad as ka join ".$this->prefix()."ad_content as kac ON (ka.ad_id=kac.ad_id) WHERE  kac.language_id='es' And
	ka.ad_id = '".$ad_id ."'";
    
return $this->query($sql);
    
    
}


function featch_ad_data_by_en_id($ad_id )
{
    
    $sql="SELECT * FROM ".$this->prefix()."ad as ka join ".$this->prefix()."ad_content as kac ON (ka.ad_id=kac.ad_id) WHERE  kac.language_id='en' And
	ka.ad_id = '".$ad_id ."'";
    
return $this->query($sql);
    
    
}


function update_ad_contain_es($edit_ad_id, $ad_title_es,$ad_contain_es,$ad_text_es,$lenguage_id_es,$ad_url_es,$image_name_es,$call_to_action_en)
	{
		$sql="UPDATE ".$this->prefix()."ad_content set ad_image_name='".$image_name_es."',ad_alternate_text='".$ad_contain_es."',ad_title='".$ad_title_es."',ad_text='".$ad_text_es."',link_url='".$ad_url_es."' ,call_to_action='".$call_to_action_en."'  where ad_id='".$edit_ad_id."' AND  language_id = 'es'";
		//echo $sql;	
                $rs=$this->query($sql);
		//return mysql_insert_id();
	}

	
function update_ad_contain_en($edit_ad_id,$ad_title_en,$ad_contain_en,$ad_text_en,$lenguage_id_en,$ad_url_en,$image_name_en,$call_to_action_en)
	{
		$sql="UPDATE ".$this->prefix()."ad_content set ad_image_name='".$image_name_en."',ad_alternate_text='".$ad_contain_en."',ad_title='".$ad_title_en."',ad_text='".$ad_text_en."',link_url='".$ad_url_en."' ,call_to_action='".$call_to_action_en."'  where ad_id='".$edit_ad_id."' AND  language_id = 'en'";
		//echo $sql;
		$rs=$this->query($sql);
		//return mysql_insert_id();
	}
	function update_ad($edit_ad_id,$ad_size,$ad_position, $from_date,$duration,$client_id)
	{
		$sql="update ".$this->prefix()."ad set ad_size='".$ad_size."',position_id='".$ad_position."',From_date='".$from_date."', duration='".$duration."' ,client_id='".$client_id."' Where ad_id='".$edit_ad_id."'";
        
		//echo $sql;
		$rs=$this->query($sql);
		//return mysql_insert_id();
	}
        
	
      
      
/*----------------------------ADVERTISE  Section End-----------------------------------------------------*/

/*------------------------------ CLIENTS SECTION START--------------------------------*/

function insert_ad_clients($business_name,$cont_f_name,$cont_l_name,$address,$city,$zip,$email,$tel,$cell)
	{
		$sql="INSERT INTO ".$this->prefix()."ad_clients set business_name='".$business_name."',Contact_first_name='".$cont_f_name."',Contact_last_name='".$cont_l_name."', address='".$address."',city='".$city."',zip='".$zip."',email='".$email."',tel='".$tel."',cell='".$cell."'";
		//echo $sql;
		$rs=$this->query($sql);
		return mysql_insert_id();
	}

function getAllClient()
  {
  
  $sql="SELECT * FROM ".$this->prefix()."ad_clients";	  
  return $this->query($sql);
  
  }
  
function getAllSponsors()
{  
    $sql="SELECT * FROM " . $this->prefix() . "sponsors";	  
    return $this->query($sql);
}
/*------------------------------- CLIENTS SECTION END--------------------------------*/


/*---------------------------REPORT ---------------------------------------*/

function eventReport($admin_id,$event_id){
	  
	  if($admin_id>1)
	    {
	      $condition=" and kt.user_id='".$admin_id."'";
	    }
	  else
	    {
	      $condition=" ";
	    }
	$sql="SELECT kt.*,kt.ticket as ticket_quantity,kc.ticket_fee_us,kc.ticket_fee_mx,kc.ticket_fee_included,kc.promo_fee_us,kc.promo_fee_mx,
	      kc.promo_fee_included,kc.us_price,kc.mx_price, ka.fname,ka.lname,ka.email,kft.ticket_name_en,kft.ticket_name_sp FROM ".$this->prefix().
	      "transaction as kt LEFT join ".$this->prefix()."cart kc ON (kt.cart_id = kc.cart_id) LEFT join
	      ".$this->prefix()."final_tickets kft ON (kt.ticket_id = kft.ticket_id ) LEFT join ".$this->prefix()."admin ka
	      ON (kt.user_id = ka.admin_id ) LEFT join ".$this->prefix()."general_events ke ON (kt.event_id = ke.event_id ) where kt.event_id='".$event_id."'
	      $condition Order By kt.id DESC";
	  //echo $sql;
	
	return $this->query($sql);
}

//function upcomingEventReport($admin_id){
//
//	 if($admin_id>1)
//	    {
//	      $condition=" and kt.user_id='".$admin_id."'";
//	    }
//	  else
//	    {
//	      $condition=" ";
//	    }
//	$sql="SELECT kt.*,kt.ticket as ticket_quantity,kc.ticket_fee_us,kc.ticket_fee_mx,kc.ticket_fee_included,kc.promo_fee_us,kc.promo_fee_mx,
//	      kc.promo_fee_included,kc.us_price,kc.mx_price, ka.fname,ka.lname,ka.email,kft.ticket_name_en,kft.ticket_name_sp FROM ".$this->prefix().
//	      "transaction as kt LEFT join ".$this->prefix()."cart kc ON (kt.cart_id = kc.cart_id) LEFT join
//	      ".$this->prefix()."final_tickets kft ON (kt.id = kft.ticket_id ) LEFT join ".$this->prefix()."admin ka
//	      ON (kt.user_id = ka.admin_id ) LEFT join ".$this->prefix()."general_events ke ON (kt.event_id = ke.event_id ) where ke.event_end_date_time > NOW()
//	      $condition Order By kt.id DESC";
//	      
//	//echo $sql;
//	
//	return $this->query($sql);
//}

function get_EXcel($trns_id){

	
	$sql="SELECT kt.*,kt.ticket as ticket_quantity,kc.ticket_fee_us,kc.ticket_fee_mx,kc.ticket_fee_included,kc.promo_fee_us,kc.promo_fee_mx,
	      kc.promo_fee_included,kc.us_price,kc.mx_price,ka.fname,ka.lname,ka.email,kft.ticket_name_en,kft.ticket_name_sp,ke.event_name_en,
	      ke.event_name_sp FROM ".$this->prefix()."transaction as kt Inner join ".$this->prefix()."cart kc ON (kt.cart_id = kc.cart_id)
	      Inner join ".$this->prefix()."admin ka ON (kt.user_id = ka.admin_id ) Inner join ".$this->prefix()."general_events ke
	      ON (kt.event_id = ke.event_id )  LEFT join ".$this->prefix()."final_tickets kft ON (kt.ticket_id = kft.ticket_id )
	      where kt.id IN ($trns_id) Order By kt.id DESC";
	      
	//echo $sql;
	
	return $this->query($sql);
}

/*------------------------------REPORT END--------------------------------*/


/*------------------------------FOR META TABLE START--------------------------------------------*/

function insert_add_meta($page_id,$page_name,$meta_title,$meta_tag,$meta_description)
    {
    $sql="INSERT INTO ".$this->prefix()."meta_table set page_id='".$page_id."',page_name='".$page_name."',language_id='en',meta_title='".$meta_title."',meta_tag='".$meta_tag."',meta_description='".$meta_description."'";
    //echo $sql;
    $rs=$this->query($sql);
    return mysql_insert_id();
    }
function insert_add_meta_es($page_id,$page_name,$meta_title_es,$meta_tag_es,$meta_description_es)
    {
    $sql="INSERT INTO ".$this->prefix()."meta_table set page_id='".$page_id."',page_name='".$page_name."',language_id='es',meta_title='".$meta_title_es."',meta_tag='".$meta_tag_es."',meta_description='".$meta_description_es."'";
    //echo $sql;
    $rs=$this->query($sql);
    return mysql_insert_id();
    }
        
function getAllmeta()
    {
      $sql="SELECT * FROM ".$this->prefix()."meta_table WHERE language_id='es'";	  
      return $this->query($sql);  
    }     
        
function getAllmetaById_en($page_id)
    {
      $sql="SELECT * FROM ".$this->prefix()."meta_table WHERE language_id='en' and page_id='".$page_id."'";	  
      return $this->query($sql);  
    }
function getAllmetaById_es($page_id)
    {
      $sql="SELECT * FROM ".$this->prefix()."meta_table WHERE language_id='es' and page_id='".$page_id."'";	  
      return $this->query($sql);  
    }    

//function featch_meta_data_by_id($edit_meta_id)
//      {
//	$sql="SELECT * FROM ".$this->prefix()."meta_table where id='".$edit_meta_id."'";	  
//	return $this->query($sql);  
//      }

function edit_add_meta($page_name,$meta_title,$meta_tag,$meta_description,$edit_meta_id)
  {
    $sql="update ".$this->prefix()."meta_table set page_name='".$page_name."',meta_title='".$meta_title."',meta_tag='".$meta_tag."',meta_description='".$meta_description."' Where page_id='".$edit_meta_id."'  and language_id='en'";
    
    $rs=$this->query($sql);
    
  }

function edit_add_meta_es($page_name,$meta_title,$meta_tag,$meta_description,$edit_meta_id)
  {
    $sql="update ".$this->prefix()."meta_table set page_name='".$page_name."',meta_title='".$meta_title."',meta_tag='".$meta_tag."',meta_description='".$meta_description."' Where page_id='".$edit_meta_id."' and language_id='es'";
    //echo $sql;
    $rs=$this->query($sql);
    
}  
   
   
  function  deletemeta($id)
    {
    $sql="DELETE  FROM ".$this->prefix()."meta_table  WHERE id=".$id;
    return $this->query($sql);
    }

  function transaction_event($condition)
  {
    $sql="SELECT  *,kt.event_id as e_id FROM ".$this->prefix()."transaction as kt Left Join ".$this->prefix()."general_events as E On(kt.event_id=E.event_id) Left join ".$this->prefix()."venue V ON (E.event_venue = V.venue_id ) Left join ".$this->prefix()."state S on (S.id = E.event_venue_state)  Left join ".$this->prefix()."city C on (C.id = E.event_venue_city) $condition  Group by kt.event_id Order By kt.id DESC";
   // echo $sql;
    return $this->query($sql);
  }

function getLastMetaId()
  {
    $sql="SELECT max(id) as max_id FROM ".$this->prefix()."meta_table";
   // echo $sql;
    return $this->query($sql);
  }

/*------------------------------FOR META TABLE START--------------------------------------------*/

function getCartId($unique_id)
  {
    $sql="SELECT  group_concat(cart_id SEPARATOR ':') as cartid FROM ".$this->prefix()."cart where unique_id='".$unique_id."'";
   // echo $sql;
    return $this->query($sql);
  }
  
  function genarateSiteMap(){

	$sql="SELECT kge.event_id AS e_id, kge.event_name_en AS main_event_en, kge.event_name_sp AS main_event_sp, kgs.event_name_en AS sub_event_en, kgs.event_name_sp AS sub_event_sp FROM ".$this->prefix()."general_events kge  Left join ".$this->prefix()."general_subevents kgs on (kge.event_id=kgs.parent_id) WHERE kge.status ='publish' ORDER BY kge.event_id desc";
	//echo $sql;
	return $this->query($sql);
  }
  
  function get_all_blog($order = null){

    if ($order == null) {
        $order = 'desc';
    }
	$sql="SELECT * FROM ".$this->prefix()."page where path='blog' order by page_id ". $order;
	//echo $sql;
	return $this->query($sql);
  }
  
  function get_all_venue(){

	$sql="SELECT * FROM ".$this->prefix()."venue  order by venue_id desc";
	//echo $sql;
	return $this->query($sql);
  }
  
  function getScheduleEvent($date = null) {
        if ($date != null) {
            $sql = "SELECT * FROM " . $this->prefix() . "event_promo_schedule where DATE(date_time) = '$date'";
        } else {
            $sql = "SELECT * FROM " . $this->prefix() . "event_promo_schedule where DATE(date_time) = CURDATE();";
        }
        return $this->query($sql);
  }
  
    function getScheduleSocialShare($date = null) {

        $sql = "SELECT * FROM " . $this->prefix() . "social_share as S LEFT JOIN " . $this->prefix() . "social_schedule as So on (S.id = So.share_id) where DATE(dpost1) = '$date' "
                . "or DATE(dpost2) = '$date' "
                . "or DATE(dpost3) = '$date' "
                . "or DATE(dpost4) = '$date' "
                . "or DATE(dpost5) = '$date' ";
        
        return $this->query($sql);
  }
  
  function getPhotoByEventId($event_id)
{
	$sql = "SELECT * FROM `".$this->prefix()."general_events` WHERE event_id='" . $event_id . "'";
	//echo $sql."<br />";
	return $this->query($sql);	
}
  
    function saveEventSpotlight($event_id, $spotlight) {
        $sql="UPDATE ".$this->prefix()."general_events SET spotlight=".$spotlight." WHERE event_id=" . $event_id;
    
        $rs=$this->query($sql);
    }
    
    function saveAdsSpotlight($ad_id, $spotlight) {
        $sql="UPDATE ".$this->prefix()."ad SET spotlight=".$spotlight." WHERE ad_id=" . $ad_id;
    
        $rs=$this->query($sql);
    }
    
    function updateScheduleEventToPublished($promoScheduleId) {
        $sql="UPDATE ".$this->prefix()."event_promo_schedule SET published=1 WHERE id=" . $promoScheduleId;
    
        $rs=$this->query($sql);
    }
    
    function editSocialShare($id,$message,$dpost1,$dpost2,$dpost3,$dpost4,$dpost5)
    {
        $sql = "update ".$this->prefix()."social_share set ";
        if (!empty($message)) {
            $sql .= " message='$message'";
        }
         if (!empty($dpost1)) {
            $sql .= ", dpost1='$dpost1'";
        } else {
            $sql .= ", dpost1=null";
        }
        
        if (!empty($dpost2)) {
            $sql .= ", dpost2='$dpost2'";
        } else {
            $sql .= ", dpost2=null";
        }
        
        if (!empty($dpost3)) {
            $sql .= ", dpost3='$dpost3'";
        } else {
            $sql .= ", dpost3=null";
        }
        
        if (!empty($dpost4)) {
            $sql .= ", dpost4='$dpost4'";
        } else {
            $sql .= ", dpost4=null";
        }
        
        if (!empty($dpost5)) {
            $sql .= ", dpost5='$dpost5'";
        } else {
            $sql .= ", dpost5=null";
        }
        
        $sql .= " WHERE id=" . $id;

        $this->query($sql);
        return true;
    }
    
    function createSocialShare($message,$dpost1,$dpost2,$dpost3,$dpost4,$dpost5)
    {
        $sql="INSERT INTO ".$this->prefix()."social_share set ";
        if (!empty($message)) {
            $sql .= " message='$message'";
        }
        
        if (!empty($dpost1)) {
            $sql .= ", dpost1='$dpost1'";
        } else {
            $sql .= ", dpost1=null";
        }
        
        if (!empty($dpost2)) {
            $sql .= ", dpost2='$dpost2'";
        } else {
            $sql .= ", dpost2=null";
        }
        
        if (!empty($dpost3)) {
            $sql .= ", dpost3='$dpost3'";
        } else {
            $sql .= ", dpost3=null";
        }
        
        if (!empty($dpost4)) {
            $sql .= ", dpost4='$dpost4'";
        } else {
            $sql .= ", dpost4=null";
        }
        
        if (!empty($dpost5)) {
            $sql .= ", dpost5='$dpost5'";
        } else {
            $sql .= ", dpost5=null";
        }
        
        $rs = $this->query($sql);

        return mysql_insert_id();
    }
    
    function editSocialSchedule($shareId,$socialId)
    {
        $sql = "update ".$this->prefix()."social_schedule set social_id='$socialId' WHERE share_id=" . $shareId;

        $this->query($sql);
        return true;
    }
    
    function createSocialSchedule($shareId,$socialId)
    {
        $sql = "insert into ".$this->prefix()."social_schedule set social_id='$socialId', share_id=" . $shareId;

        $this->query($sql);
        return true;
    }
    
    function deleteSocialShareById($id_social_share_delete)
    {       
        $sql="DELETE FROM ".$this->prefix()."social_share WHERE id = '".$id_social_share_delete."'";
	return $this->query($sql);        
    }
    
    function deleteSocialScheduleByShareId($id_social_share_delete)
    {
        $sql="DELETE FROM ".$this->prefix()."social_schedule WHERE share_id = '".$id_social_share_delete."'";
	return $this->query($sql); 
    }
    
    function saveAdsValues($ad_id, $field_name, $value) {
        $sql="UPDATE ".$this->prefix()."ad SET $field_name='".$value."' WHERE ad_id=" . $ad_id;
    
        $rs=$this->query($sql);
    }
};


//SELECT * FROM `kcp_page` where path='blog' order by page_id desc
//SELECT kge.event_id AS e_id, kge.event_name_en AS main_event_en, kge.event_name_sp AS main_event_sp, kgs.event_name_en AS sub_event_en, kgs.event_name_sp AS sub_event_sp
//FROM  `kcp_general_events` AS kge
//LEFT JOIN  `kcp_general_subevents` kgs ON ( kge.event_id = kgs.parent_id ) 
//WHERE kge.status =  'publish'
//ORDER BY kge.event_id


?>