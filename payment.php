<style>
.checkbtn {
  width: auto;
  height: 24px;
  background: #134f5c; 
  border: 0;
  outline: none;
  font: bold 16px/24px Arial, Helvetica, sans-serif;
  color: #fff;
  text-align: center;
  padding: 0 12px;
  margin: 0;
  display: inline-block;
  overflow: hidden;
  text-decoration: none;
  cursor: pointer;
}
.checkbtn:hover {  
  color: #fff;
  text-decoration: none;
}
</style>	

<?php
include('include/user_inc.php');

$_SESSION['total'] = 0;
$e_id = $_REQUEST['event_id'];
//unset($_SESSION['event_id']);
$_SESSION['event_id'] = $e_id;
//echo "seid= ".$_SESSION['event_id'] = $e_id;

//echo $_SESSION['ses_admin_id'];

//$sub_id = $_REQUEST['sub_id'];
//echo $event_id; exit;
// =================================== Google Plus =====================================
########## Google Settings.. Client ID, Client Secret #############

require './facebook-php/src/Facebook/autoload.php';

$facebook = new \Facebook\Facebook(array(
            'app_id' => '445192265673724',
            'app_secret' => '41f5bccae260641bce323da48eb35776',
			'default_graph_version' => 'v2.5',
            ));
$helper = $facebook->getRedirectLoginHelper();

$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl('https://www.kpasapp.com/fb_callback.php', $permissions);

/*$google_client_id 		= '256208379976-qn6714nedvs4ci49mlfm1o988q6dhqld.apps.googleusercontent.com';
$google_client_secret 	= 'OmTKyOc5XDUNqs9_taw_GP9l';
$google_redirect_url 	= 'http://kpasapp.com/google.php';
$google_developer_key 	= 'AIzaSyCaEfiGqBVrb7GgQKoYeCkb7CNMcQGfT-s';*/

$google_client_id 	= '199568594992-5ppg13iba5cnp7ga6l0nnrfjjkvnlaa1.apps.googleusercontent.com';
$google_client_secret 	= 'mDck0ws-RLAuOXXhEpcoQtgB';
$google_redirect_url 	= 'https://www.kpasapp.com/google.php';
$google_developer_key 	= 'AIzaSyDavBYRIR_y12c5EfKqqY40KLUaKwBujTo';

$objCommon = new Common();
$objLocation=new user;

//include google api files
require_once 'src/Google_Client.php';
require_once 'src/contrib/Google_Oauth2Service.php';

$gClient = new Google_Client();
$gClient->setApplicationName('Login to saaraan.com');
$gClient->setClientId($google_client_id);
$gClient->setClientSecret($google_client_secret);
$gClient->setRedirectUri($google_redirect_url);
$gClient->setDeveloperKey($google_developer_key);

$google_oauthV2 = new Google_Oauth2Service($gClient);

//If user wish to log out, we just unset Session variable
if (isset($_REQUEST['reset'])) 
{
  unset($_SESSION['token']);
  $gClient->revokeToken();
  header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
}

if (isset($_GET['attempt_id'])) {
	$_SESSION['attempt_id'] = $_GET['attempt_id'];
}

//Redirect user to google authentication page for code, if code is empty.
//Code is required to aquire Access Token from google
//Once we have access token, assign token to session variable
//and we can redirect user back to page and login.
if (isset($_GET['code'])) 
{ 
	$gClient->authenticate($_GET['code']);
	$_SESSION['token'] = $gClient->getAccessToken();
	header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
	return;
}


if (isset($_SESSION['token'])) 
{ 
		$gClient->setAccessToken($_SESSION['token']);
}


if ($gClient->getAccessToken()) 
{
	  //Get user details if user is logged in
	  $user 				= $google_oauthV2->userinfo->get();
	  $user_id 				= $user['id'];
	  $user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
	  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
	  $profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
	  $profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
	  $personMarkup 		= "$email<div><img src='$profile_image_url?sz=50'></div>";
	  $_SESSION['token'] 	= $gClient->getAccessToken();
}
else 
{
	//get google login url
	$authUrl = $gClient->createAuthUrl();
}

//echo $authUrl;
if(isset($authUrl)) //user is not logged in, show login button
{
	//header("Location:".$obj_base_path->base_path()."/payment.php");
	echo '<a class="login" href="'.$authUrl.'"></a>';
}
else{
	//header("Location:".$obj_base_path->base_path()."/index.php?reset=1");
	//header("location:".$obj_base_path->base_path()."/payment/".$e_id);
	//exit;
}

// =================================== Google Plus =====================================

//create object
$objEvent=new user;
$objmulti_event=new user;
$objmul_date=new user;
$obj_venue=new user;
$obj_ticket=new user;
$obj_ticket_img=new user;
$objsub_event=new user;
$obj_venue_sub=new user;

$obj_sub_ticket=new user;
$obj_sub_ticket_img=new user;

$obj_chk=new user;
$obj_cur_eve_dt=new user;

$obj_cart=new user;
$obj_cart_details=new user;
$obj_event_date_frm_cart=new user;
$obj_remove=new user;
$obj_count=new user;
$obj_total=new user;
$obj=new user;
$obj_country=new user;
$obj_venuestate=new user;
$faq=new user;
$edit_admin=new user;



if( isset($_POST['hid_sign']) )
{
	//print_r($_POST);exit;
	$loginid=$_POST["email_cell"];
	$pass=$_POST["pass_signin"];
	$remember=$_POST["remember_me"];
	$faq = new User;

	 $faq->login($loginid,$pass) ;

	if ($faq->num_rows() > 0 ) 
	{	
		unset($_SESSION['err']);
		$faq->next_record();						
		$_SESSION['admin_email'] = $faq->f('email');
		$_SESSION['name'] = $faq->f('fname')." ". $faq->f('lname');
		$_SESSION['ses_admin_id'] = $faq->f('admin_id');
		$_SESSION['login_mode'] = 'site';

		if(isset($remember)){
		    setcookie('first_name1','amit',time()+60*60*24*365, '/');
				    $_SESSION['login_mode1'] = 'site111';
    
		} else {
		    setcookie('first_name1','',time()-3600, '/');
				    $_SESSION['login_mode1'] = '';
		}

	?>
		<script language="javascript">
		  $(document).ready(function(){
			  <?php
			  if($faq->f('language')=="Spanish")
				  $set_lang = "spn";
			  else
				  $set_lang = "eng";
			  ?>
			  $('#languageId').val('<?php echo $set_lang;?>');
			  $('#frmlanguage').submit();
			  
		  })
		</script>
    	
    <?php	
		//redirect
		//header("Location:".$obj_base_path->base_path()."/index");
		if($_SESSION['cid'] != ''){
			foreach($_SESSION['cid'] as $data){
			  $obj_cart->update_cart($data,$_SESSION['ses_admin_id'],$_SESSION['unique']);
			}
		}

	}
	else
	{
		unset($_SESSION['err']);
		if($_SESSION['langSessId']=='eng')
		$_SESSION['err'] = "Invalid login. Please try again.";
		else
		$_SESSION['err'] = "login inv&aacute;lido. Por favor, inténtelo de nuevo.";
		header("Location:".$obj_base_path->base_path()."/payment/".$_GET['event_id']."/attempt/".$_GET['attempt_id']);
		exit;
	}
}
//echo $_SESSION['err'];

if(isset($_POST['action']) && $_POST['action'] == 'save')
{
//print_r($_POST);
//echo $_POST["country_id"]; exit;


$obj_adduser = new user;
$obj_sendmail = new user;



$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email_cell'];
$phone = $_POST['phone'];
$country_id = $_POST['country_id'];
$country_code = $_POST['country_code'];
$rem_password = '';
$password = '';

//$rem_password = $_POST['password'];
//$password = md5($_POST['password']);
$account_type = $_POST['account_type'];

if (isset($_SESSION['langSessId']) && ($_SESSION['langSessId'] == 'eng')) {
    $language = 'English';
} elseif (isset($_SESSION['langSessId']) && ($_SESSION['langSessId'] == 'spn')) {
    $language = 'Spanish';
} else {
    $language = 'English';
}    

$mobile_code = $_POST['mobile_code'];


//if($language == 'English'){
//$_SESSION['langSessId'] = 'eng';
//}
//elseif($language == 'Spanish')
//{
//$_SESSION['langSessId'] = 'spn';
//}

//echo $_SESSION['langSessId']; exit;

$user_id = $obj_adduser->register_user_on_payment($fname,$lname,$email,$phone,$country_id,$country_code,$password,$account_type,$language,$mobile_code);

//if($_SESSION['langSessId']=='eng')
if($_POST['password'] != ''){

if($language=='English') 
{
$subject='Your activation key for your kpasapp.com account!';

$body='<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#f4f4f4">
<tbody><tr>
	<td valign="top" align="center">

		<table width="100%" cellspacing="0" cellpadding="0">                  
			 
			<tbody><tr>
				<td style="background-color:#1a1c35;border-top:1px solid #57658e;border-bottom:1px solid #262f47">
					<center>
						<a target="_blank" style="color:#4c5a81;color:#4c5a81;color:#4c5a81" href="'.$obj_base_path->base_path().'">
						<img border="0" align="middle" alt="KPasapp" title="KPasapp" src="'.$obj_base_path->base_path().'/images/KPasapp_logo.png"></a>
					</center>
										</td>
			</tr>
						</tbody></table>

		<table width="550" cellspacing="0" cellpadding="20" bgcolor="#FFFFFF">
			<tbody><tr>
			  <td valign="top" bgcolor="#FFFFFF" style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms;border-left:1px solid #e0e0e0;border-right:1px solid #e0e0e0;border-bottom:1px solid #e0e0e0">
					<p style="margin-top:0px"></p>
					<div style="font-size:20px;font-weight:bold;color:#f3164f;font-family:arial;line-height:100%;padding:20px 0px">Dear '.$fname.'</div>

				
			    <p style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms">Thank you for joining kpasapp.com!<br><br>
					  For your records, your login is: '.$email.' or ('.$country_code.') '.$phone.'

                  <p style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms"><a target="_blank" href="'.$obj_base_path->base_path().'/activate_user/'.$user_id.'" style="font-weight:bold;">Click here to Activate your login.</a>
                  <p style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms"><strong id="docs-internal-guid-41d2b438-2620-af16-b214-944f15f673bf">If you are unable to open the hyperlink above, copy and paste the following URL into your internet browser (if the link is split into two lines, be sure to copy both lines): "'.$obj_base_path->base_path().'/activate_user/'.$user_id.'"</strong>                                    
                  <p style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms"> Once your login has been activated, you can begin using your KPasapp account and setup your profile to fully take advantage of the numerous features of KPasapp.com, your passport to all the events of Baja California Sur.                    
                <p style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms">                                        With a warm welcome.<br>
                <p style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms">The KPasapp Team<br>
                    Do not reply to this email.<br>
              Email us at info@kpasapp.com if you require additional assistance.</td>
			</tr>
			<tr>
			<td valign="top" style="background-color:#e9e9e9;border-top:22px solid #f4f4f4;padding:5px 10px">
						<div style="font-size:10px;color:#666666;line-height:100%;font-family:verdana">
							<div style="float:right">
	<a target="_blank" href="http://twitter.com/tickethype"><img width="43" border="0" height="43" src="'.$obj_base_path->base_path().'/images/twitter_icon.png"></a>
	<a target="_blank" href="http://www.facebook.com/tickethype"><img width="43" border="0" height="43" src="'.$obj_base_path->base_path().'/images/facebook_icon.png"></a>
	<a target="_blank" href="#"><img width="43" border="0" src="'.$obj_base_path->base_path().'/images/youtube_icon.png"></a>
	</div><div>Copyright &copy; 2011 <span style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms">Kpasapp</span>, Inc. All rights reserved.</div></div>
					</td>
				</tr>
						</tbody></table>
	</td>
</tr>
</tbody></table>';
}
else
{
$subject='Su clave de activación para su cuenta kpasapp.com!';
$body='<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#f4f4f4">
<tbody><tr>
	<td valign="top" align="center">
		<table width="100%" cellspacing="0" cellpadding="0">                  
		  <tbody><tr>
			  <td style="background-color:#1a1c35;border-top:1px solid #57658e;border-bottom:1px solid #262f47">
			    <center>
				    <a target="_blank" style="color:#4c5a81;color:#4c5a81;color:#4c5a81" href="'.$obj_base_path->base_path().'">
				    <img border="0" align="middle" alt="KPasapp" title="KPasapp" src="'.$obj_base_path->base_path().'/images/KPasapp_logo.png"></a>
			    </center>
			  </td>
		      </tr>
		</tbody></table>
		<table width="550" cellspacing="0" cellpadding="20" bgcolor="#FFFFFF">
			<tbody><tr>
			  <td valign="top" bgcolor="#FFFFFF" style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms;border-left:1px solid #e0e0e0;border-right:1px solid #e0e0e0;border-bottom:1px solid #e0e0e0">
					<p style="margin-top:0px"></p>
					<div style="font-size:20px;font-weight:bold;color:#f3164f;font-family:arial;line-height:100%;padding:20px 0px"><strong id="docs-internal-guid-2a53dcf4-cf71-79c7-e78d-b719f4163692">Estimado </strong> '.$fname.'</div>

				
			    <p dir="ltr"><strong id="docs-internal-guid-2a53dcf4-cf71-4930-5290-27dd9f591481">Gracias por unirse a kpasapp.com !</strong><br><br>
				  <strong id="docs-internal-guid-2a53dcf4-cf72-a832-6bd1-e3d64f59b2cc">Para sus archivos, su login es:</strong> '.$email.' or ('.$country_code.') '.$phone.' </p>
				  <p dir="ltr"><a target="_blank" href="'.$obj_base_path->base_path().'/activate_user/'.$user_id.'" style="font-weight:bold;">Haga clic aqu&#237; para activar su login.</a>			      </p>
				  <p dir="ltr"><strong id="docs-internal-guid-41d2b438-260b-4578-7486-b278d6d5827a">Si no son capaces de abrir el hiperv&#237;nculo anterior, copie y pegue el siguiente URL en su navegador de internet (si el enlace se divide en dos l&#237;neas, asegúrese de copiar ambas l&#237;neas):  "'.$obj_base_path->base_path().'/activate_user/'.$user_id.'"</strong></p>
				  <p dir="ltr">Una vez que su login se ha activado, puede comenzar utilizando su cuenta KPasapp y configurar su perfil para aprovechar al m&aacute;ximo las numerosas caracter&#237;sticas de KPasapp.com, su pasaporte para todos los eventos de Baja California Sur.</p>
				  <p dir="ltr">Con una c&aacute;lida bienvenida.<br>
					  <br>
					  
					  
					  El Equipo de KPasapp<br>
                      No responda a este correo electr&oacute;nico.<br>
                      Email nosotros en info@kpasapp.com si necesita ayuda adicional.<br>
		      </p></td>
			</tr>
			<tr>
			<td valign="top" style="background-color:#e9e9e9;border-top:22px solid #f4f4f4;padding:10px 10px">
						<div style="font-size:10px;color:#666666;line-height:100%;font-family:verdana">
							<div style="float:right">
	<a target="_blank" href="http://twitter.com/tickethype"><img width="43" border="0" height="43" src="'.$obj_base_path->base_path().'/images/twitter_icon.png"></a>
	<a target="_blank" href="http://www.facebook.com/tickethype"><img width="43" border="0" height="43" src="'.$obj_base_path->base_path().'/images/facebook_icon.png"></a>
	<a target="_blank" href="#"><img width="43" border="0" src="'.$obj_base_path->base_path().'/images/youtube_icon.png"></a>
	</div><div>Copyright &copy; 2011 <span style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms">Kpasapp</span>, Inc. All rights reserved.</div></div>
					</td>
				</tr>
	</tbody></table>
	</td>
</tr>
</tbody></table>';
}


//echo $body;exit;
//send email
if($user_id>0)
$obj_sendmail->merchant_login_mail($subject,$email,$user_id,$body);	
	


//if($_SESSION['langSessId']=='eng') 
if($_SESSION['langSessId'] == 'eng') 
{
	if($phone==""){
		$_SESSION['user_msg'] = "<span style='font:normal 16px/20px Arial,Helvetica,sans-serif; font-style:italic;'>An activation link has been sent to ".$email.".<br>Please check your email and click on the link to activate your KPasapp account.</span><br>
	<span style='font:normal 12px/20px Arial,Helvetica,sans-serif;'>If you do not receive the Activation email within 5 minutes please check your Spam or Junk folders to ensure safe delivery. If you think that there has been a problem with delivery of the email please contact us at info@kpasapp.com.</span>";
	}
	else{
		$_SESSION['user_msg'] = "<span style='font:normal 16px/20px Arial,Helvetica,sans-serif; font-style:italic;'>An activation link has been sent to ".$email." (or ".$phone.").<br>Please check your email (or SMS) and click on the link to activate your KPasapp account.</span><br>
	<span style='font:normal 12px/20px Arial,Helvetica,sans-serif;'>If you do not receive the Activation email within 5 minutes please check your Spam or Junk folders to ensure safe delivery. If you think that there has been a problem with delivery of the email please contact us at info@kpasapp.com.</span>";
	}
}
else{
	if($phone==""){
		$_SESSION['user_msg'] = "<span style='font:normal 16px/20px Arial,Helvetica,sans-serif; font-style:italic;'>Un enlace de activaci&oacute;n ha sido enviado a  ".$email." .<br>Por favor, consulta su correo electr&oacute;nico y haga clic en el enlace para activar su cuenta KPasapp.</span><br>
	<span style='font:normal 12px/20px Arial,Helvetica,sans-serif;'>Si no recibe el e-mail de activaci&oacute;n en 5 minutos por favor revise su carpeta de spam o basura para asegurar entrega segura. Si usted piensa que ha habido un problema con el env&#237;o del correo electr&oacute;nico, por favor cont&aacute;ctenos a info@kpasapp.com.</span>";	}
	else{
		$_SESSION['user_msg'] = "<span style='font:normal 16px/20px Arial,Helvetica,sans-serif; font-style:italic;'>Un enlace de activaci&oacute;n ha sido enviado a  ".$email." (or ".$phone.").<br>Por favor, consulta su correo electr&oacute;nico (SMS) y haga clic en el enlace para activar su cuenta KPasapp.</span><br>
	<span style='font:normal 12px/20px Arial,Helvetica,sans-serif;'>Si no recibe el e-mail de activaci&oacute;n en 5 minutos por favor revise su carpeta de spam o basura para asegurar entrega segura. Si usted piensa que ha habido un problema con el env&#237;o del correo electr&oacute;nico, por favor cont&aacute;ctenos a info@kpasapp.com.</span>";
	}
}

}
  
  // Added By Amit ===================
  $_SESSION['ses_admin_id'] = $user_id;
  $_SESSION['name'] = $fname." ". $lname;
  if($_SESSION['cid'] != '')
  {
      foreach($_SESSION['cid'] as $data){
	$obj_cart->update_cart($data,$_SESSION['ses_admin_id'],$_SESSION['unique']);
      }
  }
  
 
?>   
    <script language="javascript">
      window.location = '<?php echo $obj_base_path->base_path()?>/payment/<?php echo $_GET['event_id']?>/attempt/<?php echo $_GET['attempt_id']?>';
    </script>
 <?php  
  
  //header("Location:".$obj_base_path->base_path()."/payment/".$_GET['event_id']);
  exit;
  // Added By Amit ===================


}



if(isset($_POST['pay_edit']) && $_POST['pay_edit'] == 1)
{	
	
	$fname=$_POST["fname"];
	$lname=$_POST["lname"];
	$email=$_POST["email"];
	$phone=$_POST["phone"];
	$mobile_code=$_POST["mobile_code"];
	$country_id=$_POST["country_id"];
	$pay_eid=$_POST["pay_eid"];
	
	$faq->checkEmailexists($email,$_SESSION['ses_admin_id']);
	{		    
        $edit_admin->edit_admin_details_new_on_payment($fname,$lname,$email,$phone,$country_id,$_SESSION['ses_admin_id'],$mobile_code);
	}
	
}

//echo '<br><br><br>'.$_SESSION['ses_admin_id'].','.$_SESSION['unique'].','.$_SESSION['event_id'].'<br><br><br>';
#$obj_cart_details->getCartDetails($_SESSION['ses_admin_id'],$_SESSION['unique'],$_SESSION['event_id']);
//echo "admin_id= ".$_SESSION['ses_admin_id'];
$obj_cart_details->getAllCartDetails($_SESSION['ses_admin_id'],$_SESSION['unique'],$_SESSION['event_id']);
$obj_event_date_frm_cart->getAllCartDetails($_SESSION['ses_admin_id'],$_SESSION['unique'],$_SESSION['event_id']); // for event date  and  time..
					
if($obj_cart_details->num_rows() == 0){ 
	//echo "hi ".$_SESSION['event_id']; exit;
	header("location:".$obj_base_path->base_path()."/event/".$_SESSION['event_id']); 
	exit;
}

  $obj_ticket->getTicket($_SESSION['ses_admin_id'],$_SESSION['unique'],$_SESSION['event_id']);
  $obj_ticket->next_record();
  if($obj_ticket->f('mx_price') != '' && $obj_ticket->f('us_price') == 0){					
    $_SESSION['pay'] = 'mx';
  }
  elseif($obj_ticket->f('us_price') != '' && $obj_ticket->f('mx_price') == 0){
    $_SESSION['pay'] = 'us';
  }
  elseif($obj_ticket->f('us_price') != '' && $obj_ticket->f('mx_price') != 0){
    $_SESSION['pay'] = 'us';
  }
  
  if($_GET['act'] != ''){
	  unset($_SESSION['pay']);
	  $_SESSION['pay'] = $_GET['act'];
  }

/*if($_POST['type'] != '' && $_POST['type'] == 'checkout'){
	//echo "<pre>";
	//print_r($_POST); exit;
	if($_POST['payment_type'] == 'standard'){
		
		?>
		<script>
		  $("#contact").action('<?php echo $obj_base_path->base_path();?>/standard/pay.php');
		  $("#contact").submit();
		</script>
		<?php
		//header("location: ".$obj_base_path->base_path()."/standard/pay.php");
	}
	if($_POST['payment_type'] == 'pro'){
		
		?>
		<script>
		  $("#contact").action('<?php echo $obj_base_path->base_path();?>/pro/pay.php');
		  $("#contact").submit();
		</script>
		<?php
		//header("location: ".$obj_base_path->base_path()."/pro/pay.php");
	}
}*/


	$obj_count->getCartCount($_SESSION['ses_admin_id'],$_SESSION['unique']);
	if($obj_count->num_rows()==0){
		header("location: ".$obj_base_path->base_path()."/index");
	}

//print_r($_SESSION);
if($_GET['action'] == 'del' && $_GET['tid'] !=''){
	//echo "hihih".$e_id; exit;
	$obj_remove->remove($_GET['tid']);
	$obj_remove->next_record();
	header("location: ".$obj_base_path->base_path()."/payment/".$_SESSION['event_id']."/attempt/".$_GET['attempt_id']);
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

<title>Payment</title>
    <meta charset="utf-8">
        <meta name="title" content="Payment">
        <meta name="keywords" content="Payment">
        <meta name="description" content="Payment">
<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/js/jquery.js"></script>
<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>

<link href="<?php echo $obj_base_path->base_path(); ?>/css/base.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/style99.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/header-frontend.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/pagination.css" rel="stylesheet" type="text/css" />
<!--<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=AIzaSyCaEfiGqBVrb7GgQKoYeCkb7CNMcQGfT-s" type="text/javascript"></script>
--><!-- jQuery lightBox plugin -->
<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/include/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/include/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $obj_base_path->base_path(); ?>/include/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script src="<?php echo $obj_base_path->base_path(); ?>/js/slides.min.jquery.js"></script>

<?php if(!empty($_SESSION['ses_admin_id'])) : ?>
<script type="text/javascript">
$(document).ready(function(){	
	
	
	setTimeout(function(){
		console.log($("html").offset().top);
		//$(window).scrollTop( $("#submit-container-wrap").offset().top );	
		
		$('html, body').animate({scrollTop: parseInt($("#submit-container-wrap").offset().top) - 100}, 2000);
	});	
})
</script>
<?php endif; ?>

<script type="text/javascript">

$(document).ready(function() {
		
	$('#loading').hide();
        //options( 1 - ON , 0 - OFF)
        var auto_slide = 0;
        var hover_pause = 1;
        var key_slide = 1;
        
        //speed of auto slide(
        var auto_slide_seconds = 500;
        /* IMPORTANT: i know the variable is called ...seconds but it's 
        in milliseconds ( multiplied with 1000) '*/
        
        /*move he last list item before the first item. The purpose of this is 
        if the user clicks to slide left he will be able to see the last item.*/
		
        //$('#carousel_ul li:first').before($('#carousel_ul li:last')); 
        
        //check if auto sliding is enabled
        if(auto_slide == 1){
            /*set the interval (loop) to call function slide with option 'right' 
            and set the interval time to the variable we declared previously */
            var timer = setInterval('slide("right")', auto_slide_seconds); 
            
            /*and change the value of our hidden field that hold info about
            the interval, setting it to the number of milliseconds we declared previously*/
            $('#hidden_auto_slide_seconds').val(auto_slide_seconds);
        }
  
        //check if hover pause is enabled
       /* if(hover_pause == 1){
            //when hovered over the list 
            $('#carousel_ul').hover(function(){
                //stop the interval
                clearInterval(timer)
            },function(){
                //and when mouseout start it again
                timer = setInterval('slide("right")', auto_slide_seconds); 
            });
  
        }*/
  
        //check if key sliding is enabled
        if(key_slide == 1){
            
            //binding keypress function
            $(document).bind('keypress', function(e) {
                //keyCode for left arrow is 37 and for right it's 39 '
                if(e.keyCode==37){
                        //initialize the slide to left function
                        slide('left');
                }else if(e.keyCode==39){
                        //initialize the slide to right function
                        slide('right');
                }
            });

        }
        
        
  });

//FUNCTIONS BELLOW

//slide function  
function slide(where){
    
            //get the item width
            var item_width = $('#carousel_ul li').outerWidth() + 500;
            
            /* using a if statement and the where variable check 
            we will check where the user wants to slide (left or right)*/
            if(where == 'left'){
                //...calculating the new left indent of the unordered list (ul) for left sliding
                var left_indent = parseInt($('#carousel_ul').css('left')) + item_width;
            }else{
                //...calculating the new left indent of the unordered list (ul) for right sliding
                var left_indent = parseInt($('#carousel_ul').css('left')) - item_width;
            
            }
            
            
            //make the sliding effect using jQuery's animate function... '
            $('#carousel_ul:not(:animated)').animate({'left' : left_indent},0,function(){    
                
                /* when the animation finishes use the if statement again, and make an ilussion
                of infinity by changing place of last or first item*/
                if(where == 'left'){
                    //...and if it slided to left we put the last item before the first item
                    $('#carousel_ul li:first').before($('#carousel_ul li:last'));
                }else{
                    //...and if it slided to right we put the first item after the last item
                    $('#carousel_ul li:last').after($('#carousel_ul li:first')); 
                }
                
                //...and then just get back the default left indent
                $('#carousel_ul').css({'left' : '0px'});
            });
            
            
            
             
           
}
  
  
function tkt_num(count,cart_id,event_id,payment,promotion){
  var attempt=<?php echo $_GET['attempt_id'] ?> ;
  //alert("attempt"+attempt);
	sendData = {"count":$("#ticket_num"+count).val(),"cart_id":cart_id,"event_id":event_id,"payment":payment,"promotion":promotion,"attempt":attempt};
	//alert($("#amount").val());
	$("#loader").show();
	 $.ajax({ 
	   url: "<?php echo $obj_base_path->base_path(); ?>/ajax_folder/ajax_ticket_num.php",
	   cache: false,
	   type: "POST",
	   
	   data: sendData,   
	   success: function(data){
               console.log(data);
	    //alert(data);
	    $("#loader").hide();
	    $("#checkout_frm").html(data);
	    //alert($("#event_id_amit").val());
	    $("#amount").val($("#total_amnt_ajax").val())
	    $("#ticket_num").val($("#total_count").val())

	   //alert();
	  // window.location.href = "http://kpasapp.com/payment/"+event_id;
	   }
	 });
}

function checkemail()
{
  var emailcell=$("#email_cell").val();
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(emailcell);
}

function check_user(){
    if (!checkemail()) {
      $("#email_cell").val('');
      $("#email_cell").focus();
      
      <?php if($_SESSION['langSessId']=='eng') {?>
      alert('Please enter a valid email.');
      <?php }elseif($_SESSION['langSessId']=='spn'){?>
      alert('Por favor, introduzca un email válido.');
      <?php }?>
      
      return false;
    }
    //else
    {
	$('#loading').show();
	sendData = {"email":$("#email_cell").val()};
	//alert($("#email_cell").val());
	 $.ajax({ 
	   url: "<?php echo $obj_base_path->base_path(); ?>/ajax_check_user.php",
	   cache: false,
	   type: "POST",
	   data: sendData,   
	   success: function(data){
	   	$('#loading').hide();
		$("#new").html(data);
	   }
	 });
    }
  
	
}

function validate_new(){
  $("#err_log_pass").html('');
  var password=$("#pass_signin").val();
  var err = 0;
  if (password=='') { $("#err_log_pass").html('Please enter password.'); err = 1; }
  if(err == 0) return true; else return false;
}

function validate(){
  
  console.log("vao day");
  $("#err_fname").html('');
  $("#err_lname").html('');
  $("#err_country").html('');
  
  var fname=$("#fname").val();
  var lname=$("#lname").val();
  var country=$("#country_id").val();
  
  var err = 0;
  if (fname=='' || fname==null) { $("#err_fname").html('Please input First Name.'); err = 1; }
  if (lname=='' || lname==null) { $("#err_lname").html('Please input Last Name.'); err = 1; }
  if (country=='' || country==null) { $("#err_country").html('Please select a country.'); err = 1; }

  if(err == 0) return true; else return false;
}
</script>

<script type="text/javascript">
function checkLoggedin(){
	<?php
		if($_SESSION['ses_admin_id']==""){
	?>
		$('html, body').animate({scrollTop: parseInt($("#text_email").offset().top) - 100}, 2000);
		$('#email_cell').focus();
	<?php
		} else{
	?>
		 $.ajax({ 
		   url: "<?php echo $obj_base_path->base_path(); ?>/checkSavedEvent.php",
		   cache: false,
		   type: "POST",
		   data: "event_id=<?php echo $event_id;?>",   
		   success: function(data){
			  // alert(data);
			   if(data==1){
				$('#alrdy_svd_evnt1').trigger('click');
			   }
			   else
			   {
		  	 	window.location = "<?php echo $obj_base_path->base_path(); ?>/add_saved_events.php?event_id=<?php echo $event_id;?>";
			   }
		   }
		 });
		
	<?php }?>
	
}

</script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#alrdy_svd_evnt1").fancybox({ 
			'hideOnOverlayClick':false,
			'hideOnContentClick':false,
			'onComplete': function(){
				setTimeout( function() {$.fancybox.close(); },2000); // 3000 = 3 secs
			  }
		});
	});
	
function setHover1(val,setDateTime,multi_id,event_id)
{
	//alert(multi_id);
	//alert(event_id);
	$("#frm_multi_id").val(multi_id);
	$("#frm_event_id").val(event_id);
	
	$('.abc').css({"color":"#FFFFFF","font-size":"12px","font-weight":"normal"});

	$('#tbl1'+val).css({"color":"red","font-size":"15px","font-weight":"bold"});
	$('.timetxt').html(setDateTime);
}

function addtocart(num,mx,us)
{
	//alert(val)
	$("#frm_ticket"+num).val($("#ticket_num"+num).val());
	$("#frm_mx_price"+num).val(mx);
	$("#frm_us_price"+num).val(us);
}

function save(){
	$("#frm").submit();
}

function contactfrm(){
	$("#contact").submit();
}
	

function pay_type(type){
	var err = 0;
	if(document.contact.fname.value.search(/\S/) == -1){
		alert("Please enter your First name.");
		document.contact.fname.focus();
		err = 1;
	}
	else if(document.contact.lname.value.search(/\S/) == -1){
		alert("Please enter your Last name.");
		document.contact.lname.focus();
		err = 1;
	}
	else if(document.contact.email.value.search(/\S/) == -1){
		alert("Please enter your Email.");
		document.contact.email.focus();
		err = 1;
	}
	else if(document.contact.country_id.value.search(/\S/) == -1){
		alert("Please select your Country.");
		document.contact.country_id.focus();
		err = 1;
	}	
	
	else if(document.contact.email.value != '')
	{
		var email = document.getElementById('email');
		var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if (!filter.test(email.value)) {
		alert('Please provide a valid email address');
		document.contact.email.focus();
		err = 1;
		}
	}	
	
	if(err == 0){
		if(type == 'standard'){
			//alert('<?php echo $obj_base_path->base_path()?>/standard/pay.php');
			//document.frm.action = '<?php echo $obj_base_path->base_path()?>/standard/pay.php';
			//$("#frm").submit();
			document.contact.payment_type.value = 'standard';
			
		}
		else if(type == 'pro'){
			//alert('<?php echo $obj_base_path->base_path()?>/standard/pay.php');
			//document.frm.action = '<?php echo $obj_base_path->base_path()?>/pro/pay.php';
			//$("#frm").submit();
			document.contact.payment_type.value = 'pro';
		}
		else if(type == 'tns'){
			//alert('<?php echo $obj_base_path->base_path()?>/standard/pay.php');
			//document.frm.action = '<?php echo $obj_base_path->base_path()?>/pro/pay.php';
			//$("#frm").submit();
			document.contact.payment_type.value = 'tns';
		}
		
	}
	//alert(<?php echo $_SESSION['ses_admin_id'];?>);
	//$("#u_id").val('<?php echo $_SESSION['ses_admin_id'];?>');
}


function new_checkout(){
  //alert($("#payment_type").val());
  if ($("#payment_type").val() == 'standard'){
     $('#contact').attr('action', '<?php echo $obj_base_path->base_path();?>/standard/pay.php');
     $("#contact").submit();
  }
  else if ($("#payment_type").val() == 'pro') {
    $('#contact').attr('action', '<?php echo $obj_base_path->base_path();?>/pro/pay.php');
    $("#contact").submit();
  }
  else if ($("#payment_type").val() == 'tns') {
    $('#contact').attr('action', '<?php echo $obj_base_path->base_path();?>/PHP_VPC_3Party_Super_Order.php');
    $("#contact").submit();
  }
}

	
function setCountryCode() 
{
    // $('#div_county_display').html('<select name="county" id="county" class="textbg_grey" style="width:205px; margin-left:5px;"><option value="">County</option></select>');
	// $('#div_city_display').html('<select name="city" id="city" class="textbg_grey" style="width:205px; margin-left:5px;"><option value="">City</option></select>');
	
	sendData = {"country_id":$('#country_id').val()};
	 $.ajax({ 
	   url: "<?php echo $obj_base_path->base_path(); ?>/ajax_folder/ajax_set_country_code.php",
	   cache: false,
	   type: "POST",
	   data: sendData,   
	   success: function(data){
	   $("#country_code").val(data);
	   }
	 });
	 $.ajax({ 
	   url: "<?php echo $obj_base_path->base_path(); ?>/ajax_folder/ajax_state_bycountry.php",
	   cache: false,
	   type: "POST",
	   data: sendData,   
	   success: function(data){
	   $("#div_state_display").html(data);
	   }
	 });
}


function display(){
  $("#display").show();
}

function change(eid,type) {
  
  //alert("hi"+eid+type);
  var attempt=<?php echo $_GET['attempt_id']?>;
  //alert("atp= "+attempt);
  window.location.href='<?php echo $obj_base_path->base_path()?>/payment.php?event_id='+eid+'&act='+type+'&attempt_id='+attempt+'';
}
</script>
<div style="display:none;">
    <div style="width:400px;height:auto; background:#FFF; padding:10px; font-size:19px;" id="alrdy_svd_evnt">
       You already saved this event.
    </div>
</div>
<a href="#alrdy_svd_evnt" id="alrdy_svd_evnt1"></a>
<?php include("include/analyticstracking.php")?> <!-----for google analytics--------->
</head>
<body>
<?php include("include/secondary_header.php");?>
<?php include("include/menu_header.php");?>
<div id="maindiv">	
	<div class="clear"></div>
	<div class="body_bg">    	
    	<div class="clear"></div>
    	<div class="container">
        	<div class="left_panel bg">
            
            <div style="text-align:center;margin-bottom: 10px; color: red;"><?php //if($_SESSION['err'] != ''){ echo $_SESSION['err']; unset($_SESSION['err']); }
	    if($_SESSION['user_msg'] != ''){ echo $_SESSION['user_msg']; unset($_SESSION['user_msg']); }?></div>
            <div class="clear"></div>
            
            	
                <div class="clear"></div>
		  <div class="view_box8">
                	<div class="heading"><?php if($_SESSION['langSessId']=='eng') {?>Checkout<?php }elseif($_SESSION['langSessId']=='spn'){?>finalizar pedido<?php }?></div>
                	<div class="hot_event7">
			<?php
			
			while($obj_cart_details->next_record())
			{
			  $event_id = $obj_cart_details->f('event_id');
			  $ticket_id_arr[]=  $obj_cart_details->f('ticket_id');
			  $multi_id  =  $obj_cart_details->f('multi_id');
			  $cart_id_arr[] = $obj_cart_details->f('cart_id');
			  $ticket_arr[] = $obj_cart_details->f('ticket');
			}
			//echo "<pre>";print_r($ticket_id_arr);
			
			$ticket_id = implode(",",$ticket_id_arr);
			$cart_id = implode(",",$cart_id_arr);
			$ticket = implode(",",$ticket_arr);
			
			
			
			//$obj_cart_details->next_record();
			//$event_id = $obj_cart_details->f('event_id');
			////$payment =  $obj_cart_details->f('payment');
			//$ticket_id =  $obj_cart_details->f('ticket_id');
			//$multi_id  =  $obj_cart_details->f('multi_id');
			//$cart_id = $obj_cart_details->f('cart_id');
			//$ticket = $obj_cart_details->f('ticket');
			
			// Event Details
			$objEvent->getEventDetails($event_id);
			$objEvent->next_record();
					
					
			if($_SESSION['langSessId']=='eng') {
					    $name = $objEvent->f('event_name_en');
					    ?>
			<h1><?php echo htmlentities($objEvent->f('event_name_en'));?></h1> 
                    <?php
                    }
					elseif($_SESSION['langSessId']=='spn')
					{
					$name = $objEvent->f('event_name_sp');
					?>
                    <h1><?php echo htmlentities($objEvent->f('event_name_sp'));?></h1> 
					<?php 
					}
					?>
                    <?php
		                        $obj_event_date_frm_cart->next_record();
					$start = $obj_event_date_frm_cart->f('start');
					//echo "start= ".$start;
					$end = $obj_event_date_frm_cart->f('end');
					//echo "end= ".$end;
					$date = $obj_event_date_frm_cart->f('sdate');
					$edate = $obj_event_date_frm_cart->f('edate');
					?>
					<?php if($_SESSION['langSessId']=='eng') {?>
					<p><?php echo date("l, M d, Y",strtotime($date))." ".$start;?> - <?php if($obj_event_date_frm_cart->f('sdate') != $obj_event_date_frm_cart->f('edate')){ echo date("l, M d, Y",strtotime($edate))." ";} echo $end;?></p>
					<p><?php echo htmlentities($objEvent->f('venue_name'));?></p>
					<p><?php echo htmlentities($objEvent->f('venue_address'));?></p>
					<p><?php echo htmlentities($objEvent->f('city_name'));?>, <?php echo htmlentities($objEvent->f('county_name'));?>, <?php echo htmlentities($objEvent->f('state_name'));?></p>                
                                        <?php }
					   else
					   {?>
					
					<p><?php setlocale(LC_TIME, 'es_ES'); echo 
					strftime("%a",strtotime($date))." ".strftime("%e",strtotime($date))." de ".strftime("%b",strtotime($date)).", ".strftime("%Y",strtotime($date))." ".$start;?> - <?php if($obj_event_date_frm_cart->f('sdate') != $obj_event_date_frm_cart->f('edate')){ echo strftime("%a",strtotime($date))." ".strftime("%e",strtotime($date))." de ".strftime("%b",strtotime($date)).", ".strftime("%Y",strtotime($date))." ";} echo $end;?></p>
					
					
					<p><?php echo htmlentities($objEvent->f('venue_name_sp'));?></p>
					<p><?php echo htmlentities($objEvent->f('venue_address'));?></p>
					<p><?php echo htmlentities($objEvent->f('city_name'));?>, <?php echo htmlentities($objEvent->f('county_name'));?>, <?php echo htmlentities($objEvent->f('state_name'));?></p>
					<?php }?>
					
                                        <?php 
                                        
                                            $objLocation->getStateCountyByEventID($event_id);
                                            $objLocation->next_record();
                                            $text = ($_SESSION['langSessId'] == 'spn') ? 'evento': 'event';  
                                            $languageEvent = ($_SESSION['langSessId'] == 'spn') ? 'es': 'en'; 
                                            $eventURL = $obj_base_path->base_path() . $objCommon->getEventURLByEventID($event_id, $objLocation, $languageEvent, $text, $name);                                           
                                        ?>
					<!--
					setlocale(LC_TIME, 'es_ES'); echo  strftime("%a",strtotime($date))." ".strftime("%e",strtotime($date))." de ".strftime("%b",strtotime($date)).", ".strftime("%Y",strtotime($date))-->
			</div>
			<div class="clear"></div>
		  </div>
		  <div class="view_box8">
                	<div class="heading"><?php if($_SESSION['langSessId']=='eng') {?>Review your order<?php }elseif($_SESSION['langSessId']=='spn'){?>Revise su pedido<?php }?>&nbsp&nbsp&nbsp<span id="loader" style="display:none;"><img src='<?php echo $obj_base_path->base_path(); ?>/images/total-loader.gif'></span></div>
                	<div class="hot_event8">
                    <div><span> <span style="float: left; padding: 0; margin: 0 auto; width: 100%"><strong>
					<?php 
						if($_SESSION['langSessId']=='eng') {
							if($obj_ticket->f('us_price') != 0 && $obj_ticket->f('mx_price') != 0){
								echo "Select payment currency:";
							} else {
								echo "Payment Currency:"; 
							}
						} elseif($_SESSION['langSessId']=='spn'){
							if($obj_ticket->f('us_price') != 0 && $obj_ticket->f('mx_price') != 0){
								echo "Seleccione moneda de pago:";
							}
						} else {
							echo "moneda de pago:";
						}
					?>
                    <p class="price-currency-selection">
						<?php if($obj_ticket->f('mx_price') != 0 && $obj_ticket->f('us_price') == 0) : ?>
							<?php if($_SESSION['langSessId']=='eng') : ?>  
								MX Pesos 
							<?php elseif($_SESSION['langSessId']=='spn'): ?> 
								Pesos MX 
							<?php endif; ?>
						<?php elseif($obj_ticket->f('us_price') != 0 && $obj_ticket->f('mx_price') == 0) : ?>
							<?php if($_SESSION['langSessId']=='eng') : ?>
								US$ 
							<?php elseif($_SESSION['langSessId']=='spn') : ?>
								EE.UU. $
							<?php endif; ?>
						<?php elseif($obj_ticket->f('us_price') != 0 && $obj_ticket->f('mx_price') != 0) : ?>
							<?php if($_SESSION['langSessId']=='eng') : ?>  
								MX Pesos 
							<?php elseif($_SESSION['langSessId']=='spn') : ?> 
								Pesos MX 
							<?php endif; ?>
							<input type="radio" name="cur" onclick="change(<?php echo $_SESSION['event_id'];?>,'mx');" id="" value="" <?php if($_SESSION['pay'] == 'mx'){ echo "checked";}?> />

							<?php if($_SESSION['langSessId']=='eng') : ?>
								US$ 
							<?php elseif($_SESSION['langSessId']=='spn') : ?>
								EE.UU. $
							<?php endif; ?> 
							<input type="radio" name="cur" onclick="change(<?php echo $_SESSION['event_id'];?>,'us');" id="" value="" <?php if($_SESSION['pay'] == 'us'){ echo "checked";}?> />
						<?php endif; ?>
					</p>
 					</strong></span></span></div>
					<div class="clear"></div>
					<div class="hot_event7">
					 <div  id="checkout_frm">
                      <form action="" name="frm" id="frm" method="post" style="overflow: scroll">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="responsive-table event_review full_width">
                        <tr>
                            <th width="5%">&nbsp;</th>
                            <th width="10%"><?php if($_SESSION['langSessId']=='eng') {?>Qy<?php }elseif($_SESSION['langSessId']=='spn'){?>Ca<?php }?></th>
                            <th width="40%"><?php if($_SESSION['langSessId']=='eng') {?>TicketName<?php }elseif($_SESSION['langSessId']=='spn'){?>Boleto<?php }?></th>
                            <th width="15%"><?php if($_SESSION['langSessId']=='eng') {?>Price<?php }elseif($_SESSION['langSessId']=='spn'){?>Precio<?php }?></th>
                            <th width="15%"><?php if($_SESSION['langSessId']=='eng') {?>Fee<?php }elseif($_SESSION['langSessId']=='spn'){?>Cargos<?php }?></th>
                            <th width="15%"><?php if($_SESSION['langSessId']=='eng') {?>Total<?php }elseif($_SESSION['langSessId']=='spn'){?>Total<?php }?></th>                      
                        </tr>
                        
                        <?php 
			  $count = 1;
			  //echo $e_id; exit;
			  $obj_ticket->getTicket($_SESSION['ses_admin_id'],$_SESSION['unique'],$_SESSION['event_id']);
			  while($row = $obj_ticket->next_record()){
			  ?>
                        
                        <tr>
                            <td>  
                                <a class="red-remove-btn" 
                                   href="<?php echo $obj_base_path->base_path()."/payment.php?event_id=".$_SESSION['event_id']."&action=del&attempt_id=".$_GET['attempt_id']."&tid=".$obj_ticket->f('cart_id'); ?>">
                                    X
                                </a>
                            </td>
                            <td>
                                <select name="ticket_num" id="ticket_num<?php echo $count;?>" onChange="tkt_num(<?php echo $count;?>,<?php echo $obj_ticket->f('cart_id');?>,<?php echo $_SESSION['event_id'];?>,<?php echo $objEvent->f('include_payment');?>,<?php echo $objEvent->f('include_promotion');?>);">
                                    <?php for($i=1;$i<=$obj_ticket->f('ticket_num');$i++) {?>
                                        <option value="<?php echo $i;?>" <?php if($i == $obj_ticket->f('ticket')){ echo "selected";}?>><?php echo $i;?></option>
                                    <?php } ?>
                                </select>						  
                            </td>
                          <td>
                          <?php
                          if($_SESSION['langSessId']=='eng') {
				  echo $obj_ticket->f('ticket_name_en');
			    }
			    elseif($_SESSION['langSessId']=='spn')
			    {
			      echo $obj_ticket->f('ticket_name_sp');
			    }
			    ?>
                          </td>
                          <td><?php 
				if($_SESSION['pay'] == 'us'){
				      echo number_format($obj_ticket->f('us_price'),2,'.',',');
				}elseif($_SESSION['pay'] == 'mx'){
				      echo number_format($obj_ticket->f('mx_price'),2,'.',',');
				}
				?></td>
                          <td> 
			    <?php
			      $promo_fee_us = 0;
			      $objfee = new user;
			      $objfee->getsetting();
			      $objfee->next_record();
			     
			     
	// ================================ Check Spanish or English and show the ticket fee  ============================
			      if($_SESSION['pay'] == 'us'){
		
				// Now check whether fees is include or not
				if($objEvent->f('include_payment') == 1){		    
				  $tic_fee = 0;
				}
				else{
				  $tic_fee = $obj_ticket->f('ticket_fee_us');
				}
				
				
				// Now check whether promoton is include or not
				if($objEvent->f('include_promotion') == 1){
				  $promo_fee = 0;
				}
				else{
				  $promo_fee = $obj_ticket->f('promo_fee_us');
				}
				
				$tic_fee_us = $promo_fee +  $tic_fee;
				echo number_format($tic_fee_us,2,'.',',');
			      }
			      else
			      {
				if($objEvent->f('include_payment') == 1){		    
				  $tic_fee_mx = 0;
				}
				else{
				  $tic_fee_mx = $obj_ticket->f('ticket_fee_mx');
				}
				
				// Now check whether promoton is include or not
				if($objEvent->f('include_promotion') == 1){
			    
				  $promo_fee_mx = 0;
				}
				else
				{
				  $promo_fee_mx = $obj_ticket->f('promo_fee_mx');
				}
				
				
				$tic_fee_mx = $promo_fee_mx +  $tic_fee_mx;
				echo number_format($tic_fee_mx,2,'.',',');
			      }
			      
	 // ========================= End of Check Spanish or English and show the ticket fee  =========================
			    
			    
			    
			    ?>
			  </td>
                          <td>
			 
			  <?php
			  
			 // Check for inlcude payment
			  
			if($_SESSION['pay'] == 'us'){
			  
			  
			  // For Include Fee
			  if($objEvent->f('include_payment') == 1){
			    
			    $incl_fee_us = 0;
			    
			  }
			  else{
			    
			    $incl_fee_us = $objfee->f('ticket_min_us') + (($objfee->f('ticket_percent_nincl')/100)*$obj_ticket->f('us_price'));
			    
			  }
			  
			  // For Include Promotion
			  if($objEvent->f('include_promotion') == 1){
			    
			    $incl_promo_us = 0;
			  }
			  else{
			    
			    $incl_promo_us = $objfee->f('promo_fee_min_us') + (($objfee->f('promo_percent_nincl')/100)*$obj_ticket->f('us_price'));
			    
			  }
			  
			  // Calculate Total Fee for 1 ticket...
			  $total_per_ticket_fee_us =  $obj_ticket->f('ticket')*($incl_fee_us + $incl_promo_us + $obj_ticket->f('us_price'));
			  echo number_format($total_per_ticket_fee_us,2,'.',',');			    
			  $_SESSION['total']=$_SESSION['total'] + $total_per_ticket_fee_us; //This is for All Total Amount  
			  
			}
			  
			elseif($_SESSION['pay'] == 'mx'){
			  
			  // For Include Fee
			  if($objEvent->f('include_payment') == 1){
			    
			    $incl_fee_mx = 0;
			    
			  }
			  else{
			    
			    $incl_fee_mx = $objfee->f('ticket_min_mx') + (($objfee->f('ticket_percent_nincl')/100)*$obj_ticket->f('mx_price'));
			    
			  }
			  
			  // For Include Promotion
			  if($objEvent->f('include_promotion') == 1){
			    
			    $incl_promo_mx = 0;
			  }
			  else{
			    
			    $incl_promo_mx = $objfee->f('promo_fee_min_mx') + (($objfee->f('promo_percent_nincl')/100)*$obj_ticket->f('mx_price'));
			    
			  }
			  
			  //echo "<br><br>=======".$obj_ticket->f('mx_price')."========<br><br><br>";
			  
			  // Calculate Total Fee for 1 ticket...
			  $total_per_ticket_fee_mx =  $obj_ticket->f('ticket')*($incl_fee_mx + $incl_promo_mx + $obj_ticket->f('mx_price'));
			  echo number_format($total_per_ticket_fee_mx,2,'.',',');			    
			  $_SESSION['total']=$_SESSION['total'] + $total_per_ticket_fee_mx; //This is for All Total Amount  
			  
			   
			} 
			  
			?>
			  </td>
	                  
                        </tr>
                        
                        <?php
                        	$count++;
			    }
			?>
                        <tr>
                            <td colspan="6">
                                <input type="hidden" name="ticket" value="<?php echo $obj_total->f('Total');?>" />
                                <strong>Total : <?php echo number_format($_SESSION['total'],2,'.',',');?>
                                <?php if($_SESSION['pay'] == 'us'){ if($_SESSION['langSessId']=='eng') {?>  US$ <?php }elseif($_SESSION['langSessId']=='spn'){?>EE.UU. $<?php } }elseif($_SESSION['pay'] == 'mx'){ if($_SESSION['langSessId']=='eng') {?>  MX Pesos <?php }elseif($_SESSION['langSessId']=='spn'){?> Pesos MX <?php } }?>
                                </strong>
                            </td>                            		                            
                        </tr>
                        <tr>
                          <td colspan="6" style="text-align:right;"><!--<input type="submit" value="Checkout" /> <input type="submit" value="Update" />--></td>
                        </tr>
                      </table>
                      	<!--<input type="hidden" name="event_id" value="<?php //echo $event_id;?>" />
                        <input type="hidden" name="amount" value="<?php //echo $obj_total->f('Amt');?>" />
                        <input type="hidden" name="payment_type" id="payment_type" value="" />
                        <input type="hidden" name="ticket_id" value="<?php //echo $ticket_id;?>" />
                        <input type="hidden" name="multi_id" value="<?php //echo $multi_id;?>" />
                        <input type="hidden" name="user_id" value="<?php //echo $_SESSION['ses_admin_id'];?>" />
                        <input type="hidden" name="name" value="<?php //echo $name;?>" />-->
                      </form>
                      </div>                      
		    </div>
                    </div>
			<div class="clear"></div>
		  </div>				
		  <div class="clear"></div>
				
			
              <div class="blue_bg full-width border-box" style="margin: 0 auto;"><?php if($_SESSION['langSessId']=='eng') {?>Buyers Information<?php }elseif($_SESSION['langSessId']=='spn'){?>Informaci&oacute;n del comprador<?php }?></div> 
	      <div class="clear"></div>
	      
		<!--------If User Not Logged In Checking Start-------------------->
		
              <?php if($_SESSION['ses_admin_id'] == ''){
			  //echo $authUrl;
			  ?>
              <form method="post" action="" enctype="multipart/form-data" name="signin" id="signin" autocomplete="on">
              <!--<input type="hidden" name="hid_sign" id="hid_sign" value="1" />-->
                <div class="account_bg full-width" style="box-sizing: border-box; height: 62px; margin: 0; border: 1px solid #555; border-top: 0px; border-bottom: 0px;">	      
                    <span class="field_bgs" style="width: 100%; background: none; border: 0; text-align:center; font: normal 24px/46px Arial, Helvetica, sans-serif; color: #134f5c;">
                        <?php if($_SESSION['langSessId']=='eng') {?>
                            Sign in with
                        <?php }elseif($_SESSION['langSessId']=='spn'){?>
                            Entrar con
                        <?php }?>
                        <a href="<?php echo $loginUrl; ?>">
							<img src='<?php echo $obj_base_path->base_path(); ?>/images/facebook_blue.gif' style="margin-bottom: 15px;" width="40" height="46" border="0"/>
						</a>
                        <a href="<?php echo $authUrl; ?>">
							<img src='<?php echo $obj_base_path->base_path(); ?>/images/4google_blue.gif' width="40" height="46" border="0"/>
						</a>
                        <!--<strong>
                        <?php if($_SESSION['langSessId']=='eng') {?>
                        OR
                        <?php }elseif($_SESSION['langSessId']=='spn'){?>
                        O
                        <?php }?>
                        </strong> -->
                        <!--<input type="text" name="email_cell" id="email_cell" class="textbg_grey" placeholder="<?php if($_SESSION['langSessId']=='eng') {?>Email or Cell#<?php }elseif($_SESSION['langSessId']=='spn'){?>Email o celular #<?php }?>" style="width: 150px;"/>  
                        <input type="password" name="pass_signin" id="pass_signin" class="textbg_grey" placeholder="<?php if($_SESSION['langSessId']=='eng') {?>Password<?php }elseif($_SESSION['langSessId']=='spn'){?>Contraseña<?php }?>" style="width: 150px;"/>
                        <input type="submit" name="Submit" value="<?php if($_SESSION['langSessId']=='eng') {?>Sign in<?php }elseif($_SESSION['langSessId']=='spn'){?>Entrar<?php }?>" class="btn1_sudip"/>-->
                        </span>			    
                </div>
         	 </form>
         	 <?php }?>
		 
		  <!--------If User Not Logged In Checking End-------------------->
		  
		 <div class="clear"></div>
		 <div class="account_box full-width" style="box-sizing: border-box; margin-bottom: 19px; margin-top: 0px;">
		 <div class="account_left full-width">
         
         	<!--form-->
	
            <?php
		if($_SESSION['ses_admin_id'] != ''){
		//echo $_SESSION['ses_admin_id'];
			$obj->getAdminById($_SESSION['ses_admin_id']);
			$obj->next_record();
		}
	    ?>
           <form method="post" action="" enctype="multipart/form-data" name="contact" id="contact" autocomplete = "off">
           <?php if($_SESSION['ses_admin_id'] == ''){?>
			<table width="100%" align="center" border="0" cellpadding="4" cellspacing="4" style="border-collapse:separate;">
				<tr>
					<td width="100%" style=" vertical-align: middle; line-height: 24px;">
						<?php if($_SESSION['langSessId']=='eng'){
							echo "Or let's check your email address ";
						}elseif($_SESSION['langSessId']=='spn'){
							echo "O vamos a comprobar su direcci&oacute;n de correo electr&oacute;nico.";
						} ?>
						<br/>
						<input type="text" name="email_cell" id="email_cell" class="textbg_grey payment-email-input" value="<?php echo $obj->f('email')?>" />
						<a href="javascript:void(0);" onClick="check_user();" class="checkbtn float-left">
							<?php
								if($_SESSION['langSessId']=='eng') {
									echo "Check";
								} elseif($_SESSION['langSessId']=='spn'){
									echo "Compruebe";
								}
							?>
						</a>
					</td>                
              </tr>
	      <tr>
		<td colspan="3"><div style="text-align:center;margin-bottom: 10px; color: red;"><?php if($_SESSION['err'] != ''){ echo $_SESSION['err']; unset($_SESSION['err']); }?></div>
		</td>
	      </tr>
              <tr>
              		<td colspan="3"><div id="loading" style="text-align: center;"><img src="<?php echo $obj_base_path->base_path(); ?>/images/loading.gif" height="100" width="100" /></div></td>
              </tr>
           </table>
           <div id="new"></div>
           <?php 
	    }
	    else
	    {
	  ?>
	  <input type="hidden" name="pay_edit" id="pay_edit" value="1">
	  <input type="hidden" name="pay_eid" id="pay_eid" value="<?php echo $_SESSION['event_id'];?>">
	  <input type="hidden" name="language" id="language" value="<?php echo $obj->f('language')?>" />
           <table width="100%" align="center" border="0" cellpadding="4" cellspacing="4" style="border-collapse:separate;">
				<tr>
					<td width="23%"><?php if($_SESSION['langSessId']=='eng'){echo "First Name";}elseif($_SESSION['langSessId']=='spn'){echo "Nombre";} ?> <span style="color:red;">*</span></td>
                    <td width="77%"><input type="text" name="fname" id="fname" class="textbg_grey required" value="<?php echo $obj->f('fname')?>" style="width: 190px;" <?php if($_SESSION['ses_admin_id'] != ''){echo "readonly";}?>/><br/><span class="err" id="err_name"></span></td>
				</tr>
				<tr>
					<td><?php if($_SESSION['langSessId']=='eng'){echo "Last Name";}elseif($_SESSION['langSessId']=='spn'){ echo "Apellido";} ?><span style="color:red;">*</span></td>
					<td>
						<input type="text" name="lname" id="lname" class="textbg_grey required" value="<?php echo $obj->f('lname');?>" style="width: 190px;" <?php if($_SESSION['ses_admin_id'] != ''){echo "readonly";}?>/> <br/>
						<span class="err" id="err_lname"></span>
					</td>
				</tr>						  
              <tr>
                <td><?php if($_SESSION['langSessId']=='eng'){echo "Primary Email";}elseif($_SESSION['langSessId']=='spn'){ echo "Correo Electr&oacute;nico";} ?><span style="color:red;">*</span></td>
                <td>
                <input type="text" name="email" id="email" class="textbg_grey" style="width: 190px; margin-right: 6px;" value="<?php echo $obj->f('email')?>" <?php if($_SESSION['ses_admin_id'] != ''){echo "readonly";}?>/>
                <input type="hidden" id="email_orig_hid" value="<?php echo $obj->f('email')?>"/>
                </td>
              </tr>
                                          
              <tr>
                <td><?php if($_SESSION['langSessId']=='eng'){echo "Primary Mobile#";}elseif($_SESSION['langSessId']=='spn'){ echo "m&oacute;vil";}?></td>
                <td>
                    <select onChange="display();" name="mobile_code" id="mobile_code" class="textbg_grey" style="width:155px; margin-left:5px;">
                    <?php
                         $obj_cntry = new user;
                        $sel = "selected='selected'";
                         $obj_cntry->countries_list();
                            while($obj_cntry->next_record()){
                    ?>
                        <option value="<?php echo $obj_cntry->f('phonecode');?>" <?php if($_SESSION['langSessId']=="spn" && $obj_cntry->f('id')==138 && $obj->f('mobile_code')==''){ echo $sel; } else if($_SESSION['langSessId']=="eng" && $obj_cntry->f('id')==226 && $obj->f('mobile_code')==''){ echo $sel; } else if($obj->f('mobile_code')==$obj_cntry->f('phonecode')) { echo $sel;}  ?>><?php echo $obj_cntry->f('phonecode')." - ".$obj_cntry->f('nicename');?></option>
                    <?php
                        }
                    ?>    
                    </select>

                  <input onClick="display();" type="text" name="phone" id="phone" class="phone-input-payment textbg_grey" value="<?php echo $obj->f('mobile')?>" style="width: 190px;" />
                                            
                    <div id="sh_alt_phn" style="color:red; margin-left:6px;"></div>
                </td>
              </tr>
              
              
              <tr>
                <td><?php if($_SESSION['langSessId']=='eng'){echo "Country";}elseif($_SESSION['langSessId']=='spn'){ echo "País";}?><span style="color:red;">*</span></td>
                <td>
                    <select onchange="display();" name="country_id" id="country_id" onChange="setCountryCode()" class="textbg_grey" style="width:205px;margin-left:5px;">
                    <?php
                        $value_code = '';
                        $sel = "selected='selected'";
                        if($_SESSION['langSessId']=="spn")
                            $value_code = "value='52'";
                        else
                            $value_code = "value='1'";
                        
                        // check country code for per user
                        if($obj->f('country_code')!="" && $obj->f('country_code')!=0)
                            $value_code = $obj->f('country_code');
                            
                        $obj_country->countries_list();
                        while($obj_country->next_record()){
                    ?>
                        <option value="<?php echo $obj_country->f('id');?>"
			<?php if($_SESSION['langSessId']=="spn" && $obj_country->f('id')==138 && $obj->f('country_id')==0){
			  echo $sel;
			  } else if($_SESSION['langSessId']=="eng" && $obj_country->f('id')==226 && $obj->f('country_id')==0){
			    echo $sel;
			    } else if($obj->f('country_id')==$obj_country->f('id')) {
			      echo $sel;
			      }  ?>><?php echo $obj_country->f('nicename');?></option>
                    <?php
                    }
                    ?>
                    </select>
                    <input type="hidden" name="country_code" id="country_code" value="<?php echo $value_code;?>" />
                </td>
              </tr>  
				<tr>
					<td colspan="2">			  
						<span id="display" style="display: none; float: left; margin-bottom: 10px; margin-top: 15px;">
							<input type="submit" value="<?php if($_SESSION['langSessId']=='eng'){echo "Update";}elseif($_SESSION['langSessId']=='spn'){ echo "Actualizar";}?>" class="btn1_sudip" />
						</span>
					</td>
				</tr>
            </table>
		   <?php
           }
		   ?>
            <!--end-->
         
         </div>		
		 </div>
		 <div class="clear"></div>
		 
		<?php if(!empty($_SESSION['ses_admin_id'])) : ?>
			<?php $style = 'style="visibility: visible"'; ?>
		<?php else : ?>
			<?php $style = 'style="visibility: hidden"'; ?>
		<?php endif; ?>
			
		<div id="submit-container-wrap" class="submit-container" <?php echo $style; ?>>

			<?php if($_SESSION['langSessId']=='eng') {?>
				By clicking the "Submit Order" button, you are agreeing to the <a href="<?php echo $obj_base_path->base_path(); ?>/about/privacy-terms">KPasapp.com Purchase Policy and Privacy Policy</a>. All orders are subject to payment approval and billing address verification. Please contact customer service if you have any questions regarding your order.
			<?php }elseif($_SESSION['langSessId']=='spn'){?>
				Al hacer clic en el bot&oacute;n "Enviar pedido", est&aacute;s aceptando la <a href="<?php echo $obj_base_path->base_path(); ?>/about/privacy-terms">Política de Compra y Pol'&#237';tica de Privacidad KPasapp.com</a>. Todos los pedidos est&aacute;n sujetos a la aprobaci&oacute;n de los pagos y a la verificaci&oacute;n de direcciones de facturaci&oacute;n. Por favor, p&oacute;ngase en contacto con atenci&oacute;n al cliente si tiene alguna pregunta acerca de su pedido.
			<?php }?>
	 
			<?php
				$obj_total_new = new user;
				$obj_total_new->totalTicket($_SESSION['ses_admin_id'],$_SESSION['unique']);
				$obj_total_new->next_record();
			?>
			<input type="hidden" name="event_id" value="<?php echo $event_id;?>" />
			<input type="hidden" name="amount" id="amount" value="<?php echo round($_SESSION['total'],2);?>" />
			<input type="hidden" name="payment_type" id="payment_type" value="" />
			<!--<input type="hidden" name="tns" id="payment_type" value="" />-->
			<input type="hidden" name="ticket_id" value="<?php echo $ticket_id;?>" />
			<input type="hidden" name="multi_id" value="<?php echo $multi_id;?>" />
			<input type="hidden" name="user_id" id="u_id" value="<?php echo $_SESSION['ses_admin_id'];?>" />
			<input type="hidden" name="name" value="<?php echo $name;?>" />
			<input type="hidden" name="ticket"  id="ticket_num" value="<?php echo $ticket;?>" />
			<input type="hidden" name="cart_id" value="<?php echo $cart_id;?>" />
			<input type="hidden" name="currency" value="<?php echo $_SESSION['pay'];?>" />
			<input type="hidden" name="unique_id" value="<?php echo $_SESSION['unique'];?>" />
	
			<?php if($_GET['attempt_id']>0) { ?>
			   <input type="hidden" name="attempt_id" value="<?php echo $_GET['attempt_id'];?>" />
			<?php  } ?>
	
			<input type="hidden" name="type" value="checkout" />
			<div align="center" style="margin-top: 15px; padding-bottom: 30px;">
				 <a href="<?php echo $eventURL; ?>" class="btn1_sudip btn_link">Cancel</a>
				 <input id="submit-order-button" type="button" onclick="<?php if($_SESSION['ses_admin_id'] == ''){ echo 'check_user();'; } ?>pay_type('standard');new_checkout();" value="<?php if($_SESSION['langSessId']=='eng') {?>Submit Order<?php }elseif($_SESSION['langSessId']=='spn'){?>Enviar pedido<?php }?>" class="btn1_sudip" />             
			</div>
		</div>
		 
          </form>                 
          </div>

        <?php include("include/frontend_rightsidebar.php");?>
			
    	</div>
        <div class="clear"></div>
	</div>
    <div class="clear"></div>
    <?php include("include/frontend_footer.php");?>
</div>

<?php if($obj_venue->f('venue_name')!=$obj_venue_sub->f('venue_name')){?>
<script type="text/javascript">
$(document).ready(function(){
	initialize('<?php echo $obj_venue->f('city'); ?>+<?php echo $obj_venue->f('st_name'); ?>+<?php echo $obj_venue->f('venue_zip'); ?>');		
})
</script>
<?php } ?>



</body>
</html>