<?php 
include('include/user_inc.php');
$event_id = $_REQUEST['event_id'];
$sub_id = $_REQUEST['sub_id'];
$multi_id = $_REQUEST['multi_id'];

//echo "eid= ".$event_id."sid= ".$sub_id."mid= ".$multi_id;
//print_r($_REQUEST);
//echo $multi_id; exit;
unset($_SESSION['unique']);
//create object
$objEvent=new user;
$objmulti_event=new user;
$objmul_date=new user;
$obj_venue=new user;
$obj_ticket=new user;
$obj_ticket_fee=new user;
$obj_ticket_img=new user;
$objsub_event=new user;
$obj_venue_sub=new user;

$obj_sub_ticket=new user;
$obj_sub_ticket_img=new user;

$obj_chk=new user;
$obj_cur_eve_dt=new user;

$obj_cart=new user;
$obj_count=new user;

$obj_expire=new user;
$obj_samefunc=new user;

$objfeatureimage=new user;

if($sub_id != ''){
$obj_chk->check_access($_REQUEST['sub_id']);
$obj_chk->next_record();
$access = $obj_chk->f('all_access');
}

// Event Details
$objEvent->getOrgEvent($event_id);
$objEvent->next_record();
//print_r($objEvent); 
$allData[1]['multi_id'] = 0;
$allData[1]['city'] = $objEvent->f('city_name');
$allData[1]['venue_name'] = $objEvent->f('venue_name');
$allData[1]['venue_name_sp'] = $objEvent->f('venue_name_sp');
$allData[1]['event_start_date_time'] = $objEvent->f('event_start_date_time');
$allData[1]['event_end_date_time'] = $objEvent->f('event_end_date_time');
$ticket_fee_included = $allData[1]['include_payment'] = $objEvent->f('include_payment');
$promotion_fee_included = $allData[1]['include_promotion'] = $objEvent->f('include_promotion');

// Check for Multi Function Event
$objmulti_event->multi_event($event_id);
$objsub_event->sub_event($event_id);

$l=2;
if($objmulti_event->num_rows()){
	while($objmulti_event->next_record()){ 

		$allData[$l]['multi_id'] = $objmulti_event->f('multi_id');
		$allData[$l]['city'] = $objmulti_event->f('city_name');
		$allData[$l]['venue_name'] = $objmulti_event->f('venue_name');
		$allData[$l]['venue_name_sp'] = $objmulti_event->f('venue_name_sp');
		$allData[$l]['event_start_date_time'] = $objmulti_event->f('multi_start_time');
		$allData[$l]['event_end_date_time'] = $objmulti_event->f('multi_end_time');
		$allData[$l]['include_payment'] = $objEvent->f('include_payment');
                $allData[$l]['include_promotion'] = $objEvent->f('include_promotion');
		$l++;
	}
}

$arr = array();

if($objsub_event->num_rows()){
	while($objsub_event->next_record()){ 

		list($s1,$s2) = explode(" ",$objsub_event->f('event_start_date_time'));
		if(!in_array($s1, $arr)){
		$arr[] = $s1;
		$allDataS[$l]['event_id'] = $objsub_event->f('event_id');
		$allDataS[$l]['parent_id'] = $objsub_event->f('parent_id');
		$allDataS[$l]['event_name_en'] = $objsub_event->f('event_name_en');
		$allDataS[$l]['event_name_sp'] = $objsub_event->f('event_name_sp');
		//$allData[$l]['city'] = $objsub_event->f('event_venue_city');
		//$allData[$l]['venue_name'] = $objsub_event->f('venue_name');
		//$allData[$l]['venue_name_sp'] = $objsub_event->f('venue_name_sp');
		$allDataS[$l]['event_start_date_time'] = $objsub_event->f('event_start_date_time');
		$allDataS[$l]['event_end_date_time'] = $objsub_event->f('event_end_date_time');
		$allData[$l]['include_payment'] = $objEvent->f('include_payment');
                $allData[$l]['include_promotion'] = $objEvent->f('include_promotion');
		$l++;
		}
	}
}

//print_r($allData);




// Venue Details
$obj_venue->venue_details_eventId($event_id);
$obj_venue->next_record();


if($sub_id != ''){
$obj_venue_sub->venue_details_subId($sub_id);
$obj_venue_sub->next_record();
}

// Event Date

if($sub_id == ''){
list($event_date,$event_time) = explode(" ",$objEvent->f('event_start_date_time'));
list($event_date_end,$event_time_end) = explode(" ",$objEvent->f('event_end_date_time'));
}
else
{
list($event_date,$event_time) = explode(" ",$obj_chk->f('event_start_date_time'));
list($event_date_end,$event_time_end) = explode(" ",$obj_chk->f('event_end_date_time'));

}
// Get tickets by Event ID
$obj_ticket->getTicketById($event_id); 
$obj_ticket_fee->getTicketById($event_id); 

$obj_ticket_img->getTicketById($event_id); 





// ********************************* imp  Change the url  *********************



//echo "e_name= ".$objEvent->f('event_name_sp');

$new_event_name_en = preg_replace('#[^a-zA-Z0-9]#', ' ', trim($objEvent->f('event_name_en')));
$new_event_name_sp = preg_replace('#[^a-zA-Z0-9]#', ' ', trim($objEvent->f('event_name_sp')));

$new_event_name_en = preg_replace('/\s{2,}/',' ', $new_event_name_en);
$new_event_name_sp = preg_replace('/\s{2,}/',' ', $new_event_name_sp);

$new_event_name_en = str_replace(' ', '-',strtolower($new_event_name_en));
$new_event_name_sp = str_replace(' ', '-',strtolower($new_event_name_sp));
//echo  "n_sp_e= ".$new_event_name_sp;

$url_path_cur = explode("/",$_SERVER['REQUEST_URI']);
$last_id = $url_path_cur[count($url_path_cur)-1];
$second_last_id = $url_path_cur[count($url_path_cur)-2];

if(is_numeric($last_id) && is_numeric($second_last_id)) 
{
	//echo "<br> yesy".$_SESSION['langSessId'];
	header("location: ".$obj_base_path->base_path()."/eventPage/".$event_id."/multi/".$multi_id."/lang/".$_SESSION['langSessId']);
	exit;
}

if($_SESSION['langSessId']=='eng')
	$_SESSION['set_lang'] = 'en';
else
	$_SESSION['set_lang'] = 'es';



if($_REQUEST['lang']=="")
{
	
	if($_SESSION['langSessId']=='eng')
	{
		header("location: ".$obj_base_path->base_path()."/event/".$event_id."/en/".$new_event_name_en);
		exit;
	}
	else{
		header("location: ".$obj_base_path->base_path()."/evento/".$event_id."/es/".$new_event_name_sp);
		exit;
	}
	
}
else if($_REQUEST['lang']!="" && $_REQUEST['lang']!=$_SESSION['set_lang'])
{
	if($_SESSION['langSessId']=='eng')
	{
		$_SESSION['set_lang'] = 'en';
		header("location: ".$obj_base_path->base_path()."/event/".$event_id."/en/".$new_event_name_en);
		exit;
	}
	else{ 
		$_SESSION['set_lang'] = 'es';
		header("location: ".$obj_base_path->base_path()."/evento/".$event_id."/es/".$new_event_name_sp);
		exit;
	}
	
	
}

// ********************************* imp  Change the url  *********************





//print_r($_SESSION);
//					********************************* imp *********************

/*if($_SESSION['ses_admin_id'] != ''){
	$obj_count->getCartCount($_SESSION['ses_admin_id']);
	if($obj_count->num_rows()>0){
		header("location: ".$obj_base_path->base_path()."/payment");
	}
}*/     
            
//					********************************* imp *********************
//echo "LANG= ".$_REQUEST['lang'];

/*-------------------------------------------FOR FEES START--------------------------------------------*/


$objfee = new user;
$objfee->getsetting();
$objfee->next_record();
$obj_ticket_fee->next_record();
			      
				
			
/*-------------------------------------------FOR FEES END--------------------------------------------*/			       

$_SESSION['session_id'] = md5(time()); 

$_SESSION['cid'] = '';

if(isset($_POST['action']) && $_POST['action'] == 'cart')	
{
	//echo "success "; 
	/*echo $_POST['frm_event_id']."cvb ";
	echo $_POST['frm_multi_id']."cvbc ";
	echo $_POST['frm_count']."xxx ";*/
	$unique = time();
	for($i=1;$i<$_POST['frm_count'];$i++){
	//echo $_POST['frm_mx_price'.$i];
	//echo $_POST['frm_ticket'.$i];exit;	
	
	
/*-------------------------------------------FOR FEES START--------------------------------------------*/

	// For MX ===================
	if($objEvent->f('include_payment') == 1){
		$add_ed_fee_inc_payment_mx = round($objfee->f('ticket_min_mx') + (($objfee->f('ticket_percent_incl')/100)*$_POST['frm_mx_price'.$i]),2);
	}
	else  
	{
	  $add_ed_fee_inc_payment_mx = round($objfee->f('ticket_min_mx') + (($objfee->f('ticket_percent_nincl')/100)*$_POST['frm_mx_price'.$i]),2);
	}
	
	if($objEvent->f('include_promotion') == 1){
		$add_ed_fee_inc_promotion_mx = round($objfee->f('promo_fee_min_mx') + (($objfee->f('promo_percent_incl')/100)*$_POST['frm_mx_price'.$i]),2);
	}
	else
	{
	 $add_ed_fee_inc_promotion_mx = round($objfee->f('promo_fee_min_mx') + (($objfee->f('promo_percent_nincl')/100)*$_POST['frm_mx_price'.$i]),2);
	}
	
	// End MX ===================
	 
	
	
	// For Us ===================    
	if($objEvent->f('include_payment') == 1){
	       $add_ed_fee_inc_payment_us = round($objfee->f('ticket_min_us') + (($objfee->f('ticket_percent_incl')/100)*$_POST['frm_us_price'.$i]),2);
	}
	else
	{
		$add_ed_fee_inc_payment_us = round($objfee->f('ticket_min_us') + (($objfee->f('ticket_percent_nincl')/100)*$_POST['frm_us_price'.$i]),2);
	}
	
	if($objEvent->f('include_promotion') == 1){
	        $add_ed_fee_inc_promotion_us = round( $objfee->f('promo_fee_min_us') + (($objfee->f('promo_percent_incl')/100)*$_POST['frm_us_price'.$i]),2);
	}
	else
	{
          $add_ed_fee_inc_promotion_us =  round($objfee->f('promo_fee_min_us') + (($objfee->f('promo_percent_nincl')/100)*$_POST['frm_us_price'.$i]),2);
	}
	
	// End MX ===================
	
	
		
/*-------------------------------------------FOR FEES END--------------------------------------------*/			       
	
		
		
	/*echo $_POST['frm_ticket'.$i]." ";
	echo $_POST['frm_mx_price'.$i]." ";
	echo $_POST['frm_us_price'.$i]."<br />";*/
		if($_POST['frm_ticket'.$i] != ''){
			
			/*for payment currency start*/
	                 if($_POST['frm_mx_price'.$i]!=0.00 && $_POST['frm_us_price'.$i]==0.00)
			 {
				$payment_cur="MXP";
			 }
			  else if($_POST['frm_us_price'.$i]!=0.00 && $_POST['frm_mx_price'.$i]==0.00)
			 {
				$payment_cur="USD";
			 }
			 else
			 {
				$payment_cur="";
			 }
	
	               /*for payment currency end*/
	
	
			$cid[] = $obj_cart->add_to_cart($_POST['frm_event_id'],$_POST['frm_multi_id'],$_POST['frm_ticket'.$i],$_POST['frm_mx_price'.$i],$_POST['frm_us_price'.$i],$unique,$_POST['frm_us_tid'.$i],$_POST['frm_payment'],$_POST['frm_date'],$_POST['frm_end_date'],$_POST['frm_start'],$_POST['frm_end'],$_POST['general'],$_POST['multi'],$_POST['sub'],$add_ed_fee_inc_payment_us,$add_ed_fee_inc_payment_mx,$ticket_fee_included,$add_ed_fee_inc_promotion_us,$add_ed_fee_inc_promotion_mx,$promotion_fee_included,$payment_cur);
			//$ticket_fee_included $promotion_fee_included
		       //$ticket_fee_us,$ticket_fee_mx,$ticket_fee_included,$promo_fee_us,$promo_fee_mx,$promo_fee_included
		}
	}
	
	$_SESSION['unique'] = $unique;
	if($_SESSION['ses_admin_id'] == ''){
		$_SESSION['cid'] = $cid;
	}
	/*if($_SESSION['ses_admin_id'] == ''){
		$_SESSION['cid'] = $cid;
		$_SESSION['unique'] = $unique;
		$_SESSION['err'] = "Login to continue..";
		header("location: ".$obj_base_path->base_path()."/event/".$_POST['frm_event_id']."");
		exit;
	}
	else
	{*/
		//$_SESSION['cid'] = '';
		header("location: ".$obj_base_path->base_path()."/payment/".$_POST['frm_event_id']."/attempt/0");
	//}
	/*print_r($cid);
	exit;*/
}

/*For event gallery start*/
/*for the query of gallery and social share Start*/
if($_SESSION['langSessId']=='eng')
{
$lang_id="en_US";
}
else
{
$lang_id="es_MX";
}
/*for the query of gallery and social share end*/

$obj_event_media=new user;
$obj_event_media->all_gallery_for_event($event_id,$lang_id);
//$obj_event_media->next_record();

        function videoType($video_url) {
		if (strpos($video_url, 'youtube') > 0) {
		    return 'youtube';
		} elseif (strpos($video_url, 'vimeo') > 0) {
		    return 'vimeo';
		} elseif (strpos($video_url, 'dailymotion') > 0) {
		    return 'dailymotion';
		} else {
		    return 'image';
		}
	}

	
//================================  for meta==================== 

$objevent_category=new user;     
$objevent_category->getCategorByEvent($event_id);
$objevent_category->next_record();

//================================  for meta====================   
   /*=====================EVENT DESCRIPTION==============================*/
   
	 if($_REQUEST['lang']=='en')
		{													
			$kp_event_title = $objEvent->f('event_name_en');
			$kp_event_desc = date("l, j F, Y, g:i a",strtotime($objEvent->f('event_start_date_time'))).", ".$obj_venue->f('venue_name').", ".$obj_venue->f('city').", ".$obj_venue->f('st_name').", ".$objEvent->f('event_short_desc_en');
	
		}
	else
		{
			$kp_event_title = $objEvent->f('event_name_sp');
			
			$kp_event_desc = setlocale(LC_TIME, 'es_ES');  strftime("%a",strtotime($event_date))." ".strftime("%e",strtotime($event_date))." de ".strftime("%b",strtotime($event_date)).", ".strftime("%Y",strtotime($event_date)).", ".$obj_venue->f('venue_name_sp').", ".$obj_venue->f('city').", ".$obj_venue->f('st_name').", ".$objEvent->f('event_short_desc_sp');
	
		}
																					
   /*====================EVENT DESCRIPTION END=============================*/
        
/*For event gallery end*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 
    
<title><?php if($_SESSION['langSessId']=='eng') { echo htmlentities(stripslashes($objEvent->f('event_name_en'))); } else { echo htmlentities(stripslashes($objEvent->f('event_name_sp')));}?></title>

<meta name="title" content="<?php if($_SESSION['langSessId']=='eng') { echo htmlentities(stripslashes($objEvent->f('event_name_en'))); } else { echo htmlentities(stripslashes($objEvent->f('event_name_sp')));}?>">
<meta name="keywords" content="<?php  if($_SESSION['langSessId']=='eng') { echo htmlentities(stripslashes($objevent_category->f('category_name'))); } else { echo htmlentities(stripslashes($objevent_category->f('category_name_sp')));}?>">
<meta name="description" content="<?php  if($_SESSION['langSessId']=='eng') { echo htmlentities(stripslashes($objEvent->f('event_short_desc_en'))); } else { echo htmlentities(stripslashes($objEvent->f('event_short_desc_sp')));}?>">
 
<!--<title><?php //if($_SESSION['langSessId']=='eng') { echo htmlentities(stripslashes($objEvent->f('event_name_en'))); } else { echo htmlentities(stripslashes($objEvent->f('event_name_sp')));}?></title>-->
<link href="<?php echo $obj_base_path->base_path(); ?>/css/base.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/style99.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/header-frontend.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/pagination.css" rel="stylesheet" type="text/css" />

<meta http-equiv="Content-Type" content="text/html;charset=utf-8"   /> <!---"charset=iso-8859-1" for English, Spanish, French, German, etc.-->


<!--<title><?php //if($_SESSION['langSessId']=='eng') { echo htmlentities(stripslashes($objEvent->f('event_name_en'))); } else { echo $objEvent->f('event_name_sp');}?></title>-->

<meta http-equiv="Content-Type" content="text/html;charset=utf-8"   /> <!---"charset=iso-8859-1" for English, Spanish, French, German, etc.-->

<meta property='fb:app_id' content='1411675195718012' />
<!--<meta property="og:locale" content="<?php //if($_SESSION['langSessId']=='eng'){echo "en_US";}else{echo "es_ES";}?>" />-->
<meta property="og:locale" content="<?php if($_REQUEST['lang']=='en'){echo "en_US";}else {echo "es_ES";}?>" />
<meta property="og:type" content="website" />

<!--1411675195718012--->
<?php $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";  //echo "actual_link= ".$actual_link;?>

<meta property='og:site_name' content='Kpasapp' />

<meta property="og:title" content="<?php  echo $kp_event_title;?>" />

<meta name="title" content="<?php  echo $kp_event_title;?>" />

<meta property="og:url" content="<?php echo $actual_link; ?>" />


<meta property="og:description" content="<?php echo $kp_event_desc;?>" />

<meta name="description" content="<?php echo $kp_event_desc;?>" />


<?php if($objEvent->f('event_photo')==''){?>
<meta property="og:image" content="<?php echo $obj_base_path->base_path(); ?>/images/kpassa_logo_fb.png">

<?php
}
else
{
?>
<meta property="og:image" content="<?php echo $obj_base_path->base_path(); ?>/files/event/large/<?php echo $objEvent->f('event_photo');?>"/>
<?php
}
?> 

<!---------FOR  TWITTER SHARE IMAGE-------------->
<!--?php if($objEvent->f('event_photo')==''){
//echo $tweet_image = $obj_base_path->base_path();"/images/kpassa_logo_fb.png
//
//else
//{
//?>
<!--<meta property="og:image" content="<!--?php echo $obj_base_path->base_path(); ?>/files/event/large/<!--?php echo $objEvent->f('event_photo');?>"/>-->
<!--?php
//}
//?> 
<!----------------------------->
<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/js/jquery.js"></script>
<?php include("include/analyticstracking.php")?> <!-----for google analytics--------->


<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=AIzaSyCaEfiGqBVrb7GgQKoYeCkb7CNMcQGfT-s" type="text/javascript"></script>
<!-- jQuery lightBox plugin -->
<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/include/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/include/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $obj_base_path->base_path(); ?>/include/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script src="<?php echo $obj_base_path->base_path(); ?>/js/slides.min.jquery.js"></script>

<!-------------bx slider start------------------->

<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/js/jquery.bxSlider.js"></script>

<script type="text/javascript">
$(document).ready(function(){
	  
	var slider1=$('#slider1').bxSlider({
	controls: false,
	displaySlideQty:3,
	moveSlideQty:1,
	pager:false,
	auto:true,
	mode:'horizontal'
	});
	
	$('#go-next1').click(function(){
	slider1.goToPreviousSlide();
	return false;
	});
	
	$('#go-prev1').click(function(){
	slider1.goToNextSlide();
	return false;
	});
  });

</script>

<style>
/*.minheightdiv {
	height: auto;
	min-height: 540px;
}
#calendar {
	width: 100%;
	margin: 0 auto;
	height:400px;
	overflow:scroll;
	overflow-x:hidden;
}
#calendar1 {
	width: 100%;
	margin: 0 auto;
	height:400px;
	overflow:scroll;
	overflow-x:hidden;
}*/

.trns{
 background-image: url("http://kpasapp.com/images/vid_trns.png");
 z-index: 9999;
 
}
/*style="z-index: 9999; background-image: <?php echo $obj_base_path->base_path(); ?>"/";*/
.bx-wrapper {
	width: 580px!important;
	position: absolute;
	padding: 0;
	margin: 0;
	top: 0;
	left: 50px;
}
.bx-window {
	width: 580px!important;
}
.pager {
	width: 190px!important;
	/*border: 1px solid #fc00ff;*/
	padding: 0!important;
	margin: 0!important;
}
#slider1 {
	height: 140px!important;
	margin: 2px 0!important;
}
#slider1 li {
	width: 193px !important;
	float: left!important;
	overflow: hidden!important;
	padding: 0!important;
	margin: 0!important;
	display: inline-block!important;
	
}
#slider1 li img {
	width: 170px !important;
	display: block!important;
	margin: 4px;
}
</style>
<!---------bx slider end----------------------->
<script type="text/javascript">

$(document).ready(function() {
        //alert(<?php echo $multi_id;?>);
	$('#tbl1<?php echo $multi_id;?>').trigger("click");
	
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
			   //alert(data);
			   if(data==1){
				$('#alrdy_svd_evnt1').trigger('click');
			   }
			   else
			   {
		  	 	window.location = "<?php echo $obj_base_path->base_path(); ?>/add_saved_events.php?event_id=<?php echo $event_id;?>&start=<?php echo $objEvent->f('event_start_date_time');?>&end=<?php echo $objEvent->f('event_end_date_time');?>";
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
	
function setHover1(val,setDateTime,multi_id,event_id,dd,ed,st,et)
{ 
	//alert(multi_id);
	//alert(event_id);
	//alert(setDateTime);
	//alert(dd);
	//alert(ed);
	//alert(st);
	//alert(et);
	
	$("#select_fuction").hide();
	
	$("#fun").val(1);
	$("#frm_multi_id").val(multi_id);
	$("#frm_event_id").val(event_id);
	
	$("#frm_date").val(dd);
	$("#frm_end_date").val(ed);
	$("#frm_start").val(st);
	$("#frm_end").val(et);
	
	$('.abc').css({"color":"#FFFFFF","font-size":"12px","font-weight":"normal"});

	$('#tbl1'+val).css({"color":"red","font-size":"15px","font-weight":"bold"});
	$('.timetxt').html(setDateTime);
}

function addtocart(num,mx,us,tid,event_id,general,multi,sub)
{
	//alert(val)
	$("#frm_event_id").val(event_id);
	$("#frm_ticket"+num).val($("#ticket_num"+num).val());
	$("#frm_mx_price"+num).val(mx);
	$("#frm_us_price"+num).val(us);
	$("#frm_us_tid"+num).val(tid);
	$("#tick").val($("#ticket_num"+num).val());
	$("#general").val(general);
	$("#multi").val(multi);
	$("#sub").val(sub);
	var a = $("#tick").val();
	var b = $("#tot").val();
	b=b+a;
	$("#tot").val(b);
}


function addtocart_new(num,mx,us,tid,event_id,dd,ed,st,et,general,multi,sub)
{
	//alert(dd);
	//alert(num)
	$("#frm_event_id").val(event_id);
	$("#frm_ticket"+num).val($("#ticket_num"+num).val());
	$("#frm_mx_price"+num).val(mx);
	$("#frm_us_price"+num).val(us);
	$("#frm_us_tid"+num).val(tid);
	$("#tick").val($("#ticket_num"+num).val());
	$("#frm_date").val(dd);
	$("#frm_end_date").val(ed);
	$("#frm_start").val(st);
	$("#frm_end").val(et);
	$("#general").val(general);
	$("#multi").val(multi);
	$("#sub").val(sub);
	var a = $("#tick").val();
	var b = $("#tot").val();
	b=b+a;
	$("#tot").val(b);
	//$("#frm").submit();
}


function save(type){
	$("#frm_payment").val(type);
	//alert(type);
	//var aa = $("#frm_event_id").val();
	var aa = $("#frm_multi_id").val();
	var bb = $("#tick").val();
	var cc = $("#fun").val();
	//alert($("#is_multi").val());
	//alert(aa);
	//alert(bb);
	if($("#is_multi").val() == 1){
		if(cc == ''){
			//alert("Select an event date and time!");
			$("#select_fuction").show();
		}
		
		else if(bb == '' || bb == 0){
			//alert("Select a ticket!");
			$("#select_ticket").show();
		}
		else
		{
			$("#frm").submit();
		}
	}
	else
	{
		if(bb == ''){
			//alert("Select a ticket!");
			$("#select_ticket").show();
		}
		else
		{
			$("#frm").submit();
		}
	}
	
}
	
	
</script>
<script>
	function scroll_to() {
		$('html, body').animate({
		    scrollTop: $("#scrollto").offset().top
		}, 2000);
	}
</script>
</head>




<body>
<div style="display:none;">
    <div style="width:400px;height:auto; background:#FFF; padding:10px; font-size:19px;" id="alrdy_svd_evnt">
       You already saved this event.
    </div>
</div>
<a href="#alrdy_svd_evnt" id="alrdy_svd_evnt1"></a>
	
	
<?php include("include/secondary_header.php");?>
<?php include("include/menu_header.php");?>
<div id="maindiv">	
	<div class="clear"></div>
	<div class="body_bg">    	
    	<div class="clear"></div>
    	<div class="container">
        	<div class="left_panel bg">
            
            <div style="text-align:center;"><?php if($_SESSION['err'] != ''){ echo $_SESSION['err']; unset($_SESSION['err']);}?></div>
            <div class="clear"></div>
            
            	<div class="cheese_box">
               	<div class="heading1"><?php 
				if($sub_id == ''){
				if($_SESSION['langSessId']=='eng') { echo htmlentities(substr(stripslashes($objEvent->f('event_name_en')),0,60)); } else { echo htmlentities(substr(stripslashes($objEvent->f('event_name_sp')),0,60));}
				}
				else
				{
				if($_SESSION['langSessId']=='eng') { echo htmlentities(substr(stripslashes($obj_chk->f('event_name_en')),0,60)); } else { echo htmlentities(substr(stripslashes($obj_chk->f('event_name_sp')),0,60));}
				}
				?>
<!--                	<span style="float: right; margin: 0 auto; padding: 0; width: 160px;">
                    <img onclick="checkLoggedin()" src='<?php //echo $obj_base_path->base_path(); ?>/images/save_btn.gif' width="160" height="36" border="0" style="cursor:pointer"/>
                    </span>-->
					
					<div class="savebtn_<?php echo $_SESSION['langSessId'];?>"><a href="javascript:void()" onclick="checkLoggedin();"><?php echo SAVE_THIS_EVENT;?></a></div>
                 </div> 
				 <div class="clear"></div>
                 
				 
			<?php
				 //echo $objEvent->f('sub_events');
			if($sub_id == ''){
				 if($objEvent->f('sub_events') == 1){
				 //echo $objsub_event->num_rows();
					if($objsub_event->num_rows()){
						//echo "Hiii"; exit;
				 ?>
                 		
                 <div class="multi_functionbg">
                    <div class="multi_box">
                    <?php
						$i=0;
						//echo "<pre>";
						//print_r($arr);
						//print_r($allDataS);
						//echo "</pre>";
						
			function sortFunction( $a, $b ) {
				return strtotime($a["event_start_date_time"]) - strtotime($b["event_start_date_time"]);
			}
			usort($allDataS, "sortFunction");
						
                        foreach($allDataS as $eachData){
							//print_r($eachData);echo "<br/><br/>";
							/*echo "<pre>";
							print_r($eachData);
							echo "</pre>";*/
                        	
							// Multi Event Date
                        	
							list($multi_event_date,$multi_event_time) = explode(" ",$eachData['event_start_date_time']);
							list($multi_event_end_date,$multi_event_end_time) = explode(" ",$eachData['event_end_date_time']);
							
							$row[$i]['parent_id'] = $eachData['parent_id'];
							$row[$i]['multi_event_date'] = $multi_event_date;
							$row[$i]['city'] = $eachData['city'];
							$row[$i]['venue_name'] =$eachData['venue_name'];
							$row[$i]['multi_start_time'] =$multi_event_time;
							
							if($row[$i]['multi_event_date'] !=$row[$i-1]['multi_event_date']  && $row[$i]['multi_event_date']!=0){
								$date2 = date('Y-m-d',strtotime($row[$i]['multi_event_date']. "+1 day"));
								$objmul_date->sub_event_datewise($row[$i]['parent_id'],$row[$i]['multi_event_date'],$date2);
					?>
						<div class="multi_function">					 
                           <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tbl<?php echo $i;?>" class="multi_function1" onclick="setHover('<?php echo $i;?>')">                      
                            <tr>
                              <th><?php echo date("D",strtotime($multi_event_date))." ".date("M",strtotime($multi_event_date))." ".date("d",strtotime($multi_event_date));?></th>
                            </tr>
                     <?php 
					 //echo $objmul_date->num_rows();
					 while($objmul_date->next_record()){
					 		list($date,$time) = explode(" ",$objmul_date->f('event_start_date_time'));
							list($date,$end_time) = explode(" ",$objmul_date->f('event_end_date_time'));
					 ?>       
							<tr>
                            <td style="height: auto;">
							  	<a href="<?php echo $obj_base_path->base_path(); ?>/subevent/<?php echo $objmul_date->f('parent_id');?>/sub_id/<?php echo $objmul_date->f('event_id');?>" class="link" data-fancybox-type="iframe">
								<?php if($_SESSION['langSessId']=='eng') echo $objmul_date->f('event_name_en'); else echo $objmul_date->f('event_name_sp');?> <br/><?php echo date('g:i A',strtotime($time))." - ".date('g:i A',strtotime($end_time)); ?>
								</a>
								<script type="text/javascript">
									$(document).ready(function() {
										$(".link").fancybox({ 
										type: 'iframe',
										'width': 720,
										'height': 900,
										'transitionIn'		: 'elastic',
										'transitionOut'		: 'elastic',
										autoSize: true
										});
									});
								  </script>
                              </td>
                            </tr>
						<?php } ?>
                          </table>
						</div>
                      <?php
							}
							$i++;
						}
					  	?>
						</div>
				 	 </div>
                 <?php
					}
				}
			}
			?>
				 
				 
				 
				<div class="clear"></div>                
                <div class="map_ticket">
                  <div class="leftpart" style="width: 338px;">					
			<div class="clear"></div>
			<?php if($_SESSION['langSessId']=='eng'){?>
			<div class="select_fuction" id="select_fuction" style="display:none;"></div>
			<?php
			}
			else
			{
			?>
                      <div class="select_fuction2" id="select_fuction" style="display:none;"></div>
                      <?php
			}
			?>
                      <?php if($_SESSION['langSessId']=='eng'){?>
                      <div class="select_ticket" id="select_ticket" style="display:none;"></div>
			<?php
			}
			else
			{
			?>
			<div class="select_ticket2" id="select_ticket" style="display:none;"></div>
			<?php
			}
			?>
                        <div class="time_reviews_box"> 						                     
				<div class="heading_tabbox">
					<?php //echo $objEvent->f('identical_function');
					if($objEvent->f('identical_function') == 1){
						if($_SESSION['langSessId']=='eng'){
					?>
							<div class="heading_top"><h1>Select your function</h1></div>
			<?php
			}
					else
					{
					?>
			<div class="heading_top"><h1>Seleccione su funci&#243n</h1></div> <!--ó=&#243;---->
					<?php
						} 
					}
					?>
				</div> 
	<?php
				if($objmulti_event->num_rows()){
						/*echo "<pre>";
						print_r($allData);
						echo "</pre>";*/
				?>
                        
                        <div id='carousel_container'>
                       <!-- <div id='left_scroll'>
                       	<a href='javascript:slide("left");'><img src='<?php echo $obj_base_path->base_path(); ?>/images/left_arrow5.png' width="28" height="27" border="0" /></a>
                        </div>-->
                        <div id='carousel_inner'>						
                            <ul id='carousel_ul'>
                             <?php
								$i=0;
								//$kk = 0;
								$cntr = 0;
								
			/**/function sortFunction( $a, $b ) {
				return strtotime($a["event_start_date_time"]) - strtotime($b["event_start_date_time"]);
			}
			usort($allData, "sortFunction");

			$arr = array();
			foreach($allData as $date){
				list($multi_event_date,$multi_event_time) = explode(" ",$date['event_start_date_time']);
				if(strtotime($date['event_start_date_time']) >= time()){
					if(!in_array($multi_event_date, $arr)){
					$arr[] = $multi_event_date;
					}
				}
			}
			
			//print_r($arr); exit;
			
								
								foreach($arr as $dd){

									$date2 = date('Y-m-d',strtotime($dd. "+1 day"));
									$objmul_date->multi_event_datewise($event_id,$dd,$date2);
									$obj_samefunc->multi_event_datewise($event_id,$dd,$date2);
							?>
								<!--class="multi_function1"-->
                                <li><table width="100%" border="0" cellspacing="0" cellpadding="0" class="heading_left">
								
                                    <tr>
                                      <th><?php
				      if($_SESSION['langSessId']=='eng')
				        {
					echo date("D",strtotime($dd))." ".date("M",strtotime($dd))." ".date("d",strtotime($dd));
					}
					else
					 {
						setlocale(LC_TIME, 'es_ES');
						echo strftime("%a",strtotime($dd))." ".strftime("%e",strtotime($dd))." de ".strftime("%b",strtotime($dd)).", ".strftime("%Y",strtotime($dd));
						
					 }
					?></th>
                                    </tr>
                                    <tr>
                                      <td>
                                      		<?php
						$objmul_date->next_record();
						//while($objmul_date->next_record()){
						//print_r($allData); exit;
						foreach($allData as $row){
							$multi = $row['multi_id'];
							$kk = $row['multi_id'];
							list($multi_event_date_new,$multi_event_time_new) = explode(" ",$row['event_start_date_time']);
							list($multi_event_end_new,$multi_event_time_end_new) = explode(" ",$row['event_end_date_time']);
							if($dd == $multi_event_date_new){
						?>
                                            <p style="cursor:pointer;" class="abc" id="tbl1<?php echo $kk;?>" onclick="setHover1(<?php  echo $kk;?>,'<?php
					    if($_SESSION['langSessId']=='eng') { echo date("D",strtotime($multi_event_date_new))." ".date("M",strtotime($multi_event_date_new))." ".date("d",strtotime($multi_event_date_new)).", ".date("Y",strtotime($multi_event_date_new))." - ".date('g:i A',strtotime($multi_event_time_new))." to ".date('g:i A',strtotime($multi_event_time_end_new)); }
					    else {
						setlocale(LC_TIME, 'es_ES');
					       echo strftime("%a",strtotime($multi_event_date_new))." ".strftime("%e",strtotime($multi_event_date_new))." de ".strftime("%b",strtotime($multi_event_date_new)).", ".strftime("%Y",strtotime($multi_event_date_new))." - ".strftime('%l:%M%p',strtotime($multi_event_time_new))." to ".strftime('%l:%M%p',strtotime($multi_event_time_end_new));
												
						}?>','<?php echo $row['multi_id'];?>','<?php echo $event_id;?>','<?php echo date("d-m-Y",strtotime($multi_event_date_new));?>','<?php echo date("d-m-Y",strtotime($multi_event_end_new));?>','<?php echo date('g:i A',strtotime($multi_event_time_new));?>','<?php echo date('g:i A',strtotime($multi_event_time_end_new));?>')">
						<?php if($_SESSION['langSessId']=='eng') { echo  date('g:i A',strtotime($multi_event_time_new)); }
						else { setlocale(LC_TIME, 'es_ES');  echo  strftime('%l:%M%p',strtotime($multi_event_time_new));}?>
						
                                            </p>
                                <?php
							}
							//$kk++;
						}
								?>
                                	</td>
                                    </tr>
                                </table></li>
                                 <?php
					$cntr++;
					}
				?>
                            </ul>
                        </div>
                        
                        
                        
                        <?php
						if($cntr>3){
						?>
                        <div id='right_scroll'>
                        <a href='javascript:slide("right");'><img src='<?php echo $obj_base_path->base_path(); ?>/images/right_arrow5 .png' width="28" height="27" border="0"/></a>
                        </div>
                        <input type='hidden' id='hidden_auto_slide_seconds' value=0 />
                        <?php } ?>
                      </div>
                      <div class="clear" style="height:20px;"></div>
                      <?php
						}
						//echo $event_date."/".$event_date_end;
						//if($_SESSION['langSessId']=='spn'){ setlocale(LC_ALL, 'es_ES'); }
						if($objmulti_event->num_rows()){
							if($event_date<date("Y-m-d"))
							{
								$obj_cur_eve_dt->getCurrMultiEve($event_id);
								$obj_cur_eve_dt->next_record();
								
								if( $obj_cur_eve_dt->f('multi_start_time')){
						
					 ?>                      
                       <?php if($_SESSION['langSessId']=='eng'){?>
		       <div class="timetxt">
                        <?php echo date("D",strtotime($obj_cur_eve_dt->f('multi_start_time')))." ".date("M",strtotime($obj_cur_eve_dt->f('multi_start_time')))." ".date("d",strtotime($obj_cur_eve_dt->f('multi_start_time'))).", ".date("Y",strtotime($obj_cur_eve_dt->f('multi_start_time')));?> - <?php echo date('g:i A',strtotime($obj_cur_eve_dt->f('multi_start_time'))); ?> to <?php echo date('g:i A',strtotime($obj_cur_eve_dt->f('multi_end_time'))); ?>
                       </div>
		       <?php }
		       else{
			setlocale(LC_TIME, 'es_ES');
			?>
		       <div class="timetxt">
                        <?php echo strftime("%a",strtotime($obj_cur_eve_dt->f('multi_start_time')))." ".strftime("%e",strtotime($obj_cur_eve_dt->f('multi_start_time')))." de ".strftime("%b",strtotime($obj_cur_eve_dt->f('multi_start_time'))).", ".strftime("%Y",strtotime($obj_cur_eve_dt->f('multi_start_time')));?> - <?php echo strftime('%l:%M%p',strtotime($obj_cur_eve_dt->f('multi_start_time'))); ?> to <?php echo strftime('%l:%M%p',strtotime($obj_cur_eve_dt->f('multi_end_time'))); ?>
                       </div>
			<?php }?>
                       <?php
								}
							}
							else{
						?>
			<?php if($_SESSION['langSessId']=='eng'){?>
                       <div class="timetxt">
                        <?php echo date("D",strtotime($event_date))." ".date("M",strtotime($event_date))." ".date("d",strtotime($event_date)).", ".date("Y",strtotime($event_date));?> - <?php echo date('g:i A',strtotime($event_time)); ?> to <?php echo date('g:i A',strtotime($event_time_end)); ?>
                       </div>
		       <?php }
		       else{ setlocale(LC_TIME, 'es_ES');
		       ?>
		       <div class="timetxt">
                        <?php echo  strftime("%a",strtotime($event_date))." ".strftime("%e",strtotime($event_date))." de ".strftime("%b",strtotime($event_date)).", ".strftime("%Y",strtotime($event_date));?> - <?php echo strftime('%l:%M%p',strtotime($event_time)); ?> to <?php echo strftime('%l:%M%p',strtotime($event_time_end)); ?>
                       </div>
		       <?php }?>
                        <?php
							}
						}
						
						else{
                       ?>
		       <?php if($_SESSION['langSessId']=='eng'){?>
                       <div class="timetxt">
                        <?php echo date("D",strtotime($event_date))." ".date("M",strtotime($event_date))." ".date("d",strtotime($event_date)).", ".date("Y",strtotime($event_date));?> - <?php echo date('g:i A',strtotime($event_time));
						
						if($event_date != $event_date_end) 
						{ 
							echo " <br /> To ";
							echo  date("D",strtotime($event_date_end))." ".date("M",strtotime($event_date_end))." ".date("d",strtotime($event_date_end)).", ".date("Y",strtotime($event_date_end))." - ";?> <?php echo date('g:i A',strtotime($event_time_end)); 
						} 
						else 
						{ 
						?>
                         	to <?php echo date('g:i A',strtotime($event_time_end)); ?>
                            <?php } ?>
                       </div>
		       <?php }
		       else{ setlocale(LC_TIME, 'es_ES');
		       ?>
		       <div class="timetxt">
                        <?php echo  strftime("%a",strtotime($event_date))." ".strftime("%e",strtotime($event_date))." de ".strftime("%b",strtotime($event_date)).", ".strftime("%Y",strtotime($event_date));?> - <?php echo strftime('%l:%M%p',strtotime($event_time));
						
						if($event_date != $event_date_end) 
						{ 
							echo " <br /> To ";
							echo  strftime("%a",strtotime($event_date_end))." ".strftime("%e",strtotime($event_date_end))." de ".strftime("%b",strtotime($event_date_end)).", ".strftime("%Y",strtotime($event_date_end))." - ";?> <?php echo strftime('%l:%M%p',strtotime($event_time_end)); 
						} 
						else 
						{ 
						?>
                         	to <?php echo strftime('%l:%M%p',strtotime($event_time_end)); ?>
                            <?php } ?>
                       </div>
		       <?php }?>
                       <?php
						}
					   ?>
<!--                       <div class="reviews_box">
                        <div class="left_option"><!?=REVIEWS?> (899)<div class="reviews"><a href="#"><img src="<?php// echo $obj_base_path->base_path(); ?>/images/ster_review.png" border="0" /></a></div></div>
                        <div class="right_option"><div class="dropdown1"><select name=""><option>4.6 / 5</option></select></div></div>
                       </div>
-->                        </div>
                        <div class="clear"></div>
                        <?php 
                            if($sub_id != ''){
                                    if($_SESSION['langSessId']=='eng') echo htmlentities($obj_chk->f('event_short_desc_en')); else echo htmlentities($obj_chk->f('event_short_desc_sp'));
                            }
                            else{
                                    if($_SESSION['langSessId']=='eng') echo htmlentities($objEvent->f('event_short_desc_en')); else echo  htmlentities($objEvent->f('event_short_desc_sp'));
                            }
                        ?>
			
			
			<div class="btn2_sudip"><a onclick="scroll_to();"><?php if($_SESSION['langSessId']=='eng'){?> More <?php }else{?> Mas <?php }?></a></div>
			
                        <div class="clear"></div>
                        <?php //if($_SESSION['langSessId']=='eng') echo $obj_chk->f('event_details_en'); else echo $obj_chk->f('event_details_sp');?>
                        <div class="clear"></div>
                        <?php 
			    if($sub_id == ''){
    			    ?>
			    <div style="margin:10px 0 20px; font-weight:bold;">
                        	<p style="margin: 0px 0px; padding: 0px 4px;"><?php echo htmlentities($obj_venue->f('venue_name'));?></p>
                        	<p style="margin: 0px 0px; padding: 0px 4px; font-weight:normal;"><?php echo htmlentities($obj_venue->f('venue_address'));?>
                                <br />
                         	<?php echo htmlentities($obj_venue->f('city')).', '.htmlentities($obj_venue->f('st_name'));?></p>
                            </div>
						
			<?php
			}
			else
			{
						
				if($obj_venue->f('venue_name')!=$obj_venue_sub->f('venue_name')){?>
				<div style="margin:10px 0 20px; font-weight:bold;">
				    <p style="margin: 0px 0px; padding: 0px 4px;"><?php echo htmlentities($obj_venue_sub->f('venue_name'));?></p>
				    <p style="margin: 0px 0px; padding: 0px 4px; font-weight:normal;"><?php echo $obj_venue_sub->f('venue_address');?>
				    <br />
				    <?php echo htmlentities($obj_venue_sub->f('city')).', '.htmlentities($obj_venue_sub->f('st_name'));?></p>
				</div>
				<?php
				}
			}
			?>
						
		<?php if($obj_venue->f('venue_name')!=$obj_venue_sub->f('venue_name')){?>
		<?php $add = $obj_venue->f('venue_name').", ".$obj_venue->f('venue_address').", ".$obj_venue->f('city').", ".$obj_venue->f('st_name'); ?>
						
			<div class="clear"></div>			
                        <div class="map_box" style="height:339px;">
			
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"></script>			
<script>
      
    <?php
      $Address = urlencode($add);
      $request_url = "http://maps.googleapis.com/maps/api/geocode/xml?address=".$Address."&sensor=true";
      $xml = simplexml_load_file($request_url) or die("url not loading");
      $status = $xml->status;
      if ($status=="OK") {
          //$Lat = $xml->result->geometry->location->lat;
         // $Lon = $xml->result->geometry->location->lng;
	 $Lat = $obj_venue->f('venue_lat');
	 $Lon = $obj_venue->f('venue_long');
          $LatLng = $Lat.",".$Lon;
      }
    ?>
 
      
var map;
var TILE_SIZE = 256;
var chicago = new google.maps.LatLng(<?php echo $LatLng;?>);

function bound(value, opt_min, opt_max) {
  if (opt_min != null) value = Math.max(value, opt_min);
  if (opt_max != null) value = Math.min(value, opt_max);
  return value;
}

function degreesToRadians(deg) {
  return deg * (Math.PI / 180);
}

function radiansToDegrees(rad) {
  return rad / (Math.PI / 180);
}

/** @constructor */
function MercatorProjection() {
  this.pixelOrigin_ = new google.maps.Point(TILE_SIZE / 2,
      TILE_SIZE / 2);
  this.pixelsPerLonDegree_ = TILE_SIZE / 360;
  this.pixelsPerLonRadian_ = TILE_SIZE / (2 * Math.PI);
}

MercatorProjection.prototype.fromLatLngToPoint = function(latLng,
    opt_point) {
  var me = this;
  var point = opt_point || new google.maps.Point(0, 0);
  var origin = me.pixelOrigin_;

  point.x = origin.x + latLng.lng() * me.pixelsPerLonDegree_;

  // Truncating to 0.9999 effectively limits latitude to 89.189. This is
  // about a third of a tile past the edge of the world tile.
  var siny = bound(Math.sin(degreesToRadians(latLng.lat())), -0.9999,
      0.9999);
  point.y = origin.y + 0.5 * Math.log((1 + siny) / (1 - siny)) *
      -me.pixelsPerLonRadian_;
  return point;
};

MercatorProjection.prototype.fromPointToLatLng = function(point) {
  var me = this;
  var origin = me.pixelOrigin_;
  var lng = (point.x - origin.x) / me.pixelsPerLonDegree_;
  var latRadians = (point.y - origin.y) / -me.pixelsPerLonRadian_;
  var lat = radiansToDegrees(2 * Math.atan(Math.exp(latRadians)) -
      Math.PI / 2);
  return new google.maps.LatLng(lat, lng);
};

function createInfoWindowContent() {
  var numTiles = 1 << map.getZoom();
  var projection = new MercatorProjection();
  var worldCoordinate = projection.fromLatLngToPoint(chicago);
  var pixelCoordinate = new google.maps.Point(
      worldCoordinate.x * numTiles,
      worldCoordinate.y * numTiles);
  var tileCoordinate = new google.maps.Point(
      Math.floor(pixelCoordinate.x / TILE_SIZE),
      Math.floor(pixelCoordinate.y / TILE_SIZE));

}

function initialize() {
  var mapOptions = {
    zoom: 15,
    center: chicago
  };

  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

  //var coordInfoWindow = new google.maps.InfoWindow();
  //coordInfoWindow.setContent(createInfoWindowContent());
  //coordInfoWindow.setPosition(chicago);
  //coordInfoWindow.open(map);

  //google.maps.event.addListener(map, 'zoom_changed', function() {
  //  coordInfoWindow.setContent(createInfoWindowContent());
  //  coordInfoWindow.open(map);
  //});
  
  var marker = new google.maps.Marker({
      position: chicago,
      map: map,
      title: ''
  });
  
}

google.maps.event.addDomListener(window, 'load', initialize);

</script>

<div id="map-canvas" style="height: 100%; width: 100%;"></div>

			
<!-- removed from here -->				
			<iframe class="alignleft" scrolling="no" frameborder="0" style="border:0px;margin-right:5px;padding:0px;" src="<?php //echo $obj_base_path->base_path().'/google_map.php?add='.$add;?>" width="100%" height="100%"></iframe>
                            <script type="text/javascript">
								 	var map = null;
									var geocoder = null;
								
									function initialize(add) {
										if (GBrowserIsCompatible()) {
											map = new GMap2(document.getElementById("map"));
											map.setCenter(new GLatLng(37.4419, -122.1419), 15);
											geocoder = new GClientGeocoder();
											var addressof=add;
											showAddress(addressof);
										}
									}
								
									function showAddress(address) {
									  if (geocoder) {
											geocoder.getLatLng(
												address,
												function(point) {
													if (!point) {
														alert(address + " not found");
													} else {
														map.setCenter(point, 15);
														var marker = new GMarker(point);
														map.addOverlay(marker);
														marker.openInfoWindowHtml(address + '<br /><div align="left" width="100%" style="margin:5px 0px 0px 10px;"><a style="color:#6a6a6a;" href="http://maps.google.com/maps?f=d&hl=en&geocode=&saddr=&daddr=' + address + '&ie=UTF8" target="_blank">Get directions</a></div>');
													}
												}
											);
										}
									}
									
							</script>                         <!--<div id="map" style="width:323px; height:325px; font-family: arial; font-size: 12px; color: #313E61; text-align: center; background-color:#FFFFFF;"></div>-->    
                        </div>
						
						<?php } ?>
						
                        <div class="clear"></div>
                      </div>
                      <div class="rightpart" style="width: 344px;">
                        
			<form name="frm" id="frm" method="post">
                        <input type="hidden" name="action" value="cart" />
                        <input type="hidden" name="frm_event_id" id="frm_event_id" value="" />
                        <input type="hidden" name="frm_multi_id" id="frm_multi_id" value="" />
                        <input type="hidden" name="frm_payment" id="frm_payment" value="" />
			
			<input type="hidden" name="frm_date" id="frm_date" value="" />
			<input type="hidden" name="frm_end_date" id="frm_end_date" value="" />
			<input type="hidden" name="frm_start" id="frm_start" value="" />
			<input type="hidden" name="frm_end" id="frm_end" value="" />
			
			<input type="hidden" name="general" id="general" value="" />
			<input type="hidden" name="multi" id="multi" value="" />
			<input type="hidden" name="sub" id="sub" value="" />
			
                        <input type="hidden" name="tick" id="tick" value="" />
                        <input type="hidden" name="tot" id="tot" value="" />
			<input type="hidden" name="fun" id="fun" value="" />
			<input type="hidden" name="is_multi" id="is_multi" value="<?php echo $objEvent->f('identical_function');?>" />
			<?php 
			if($_REQUEST['sub_id'] == ''){?>
                          
			<?php if($objEvent->f('sub_events') == 1){?>
										
			<div class="select_box1">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="select_table1">
			   <tr>
			     <td colspan="4"><div class="heading"><?php if($obj_ticket->num_rows()) { echo SELECT_TICKETS; } else { echo NO_TICKETS_AVAILABLE; }?></div></td>
			   </tr>
			   <?php 
			   $obj_expire->getTicketByExp($event_id); 
				if($obj_expire->num_rows()){  
				      
				      if($obj_ticket->num_rows()){
					      $count=1;
				 while($obj_ticket->next_record()){
			   ?>
			   <tr>
			     <td width="10%">
			     <?php 
							     
				$tt = time();
				if($tt<=$obj_ticket->f('to_ticket')){
					if($obj_ticket->f('ticket_num') > 0){
				?>
				 
				 <div class="dropdown25"><select name="ticket_num" id="ticket_num<?php echo $count;?>" onchange="addtocart_new(<?php echo $count;?>,<?php echo $obj_ticket->f('price_mx');?>,<?php echo $obj_ticket->f('price_us');?>,<?php echo $obj_ticket->f('ticket_id');?>,<?php echo $event_id;?>,'<?php echo date("d-m-Y",strtotime($event_date));?>','<?php echo date("d-m-Y",strtotime($event_date_end));?>','<?php echo date('g:i A',strtotime($event_time));?>','<?php echo date('g:i A',strtotime($event_time_end));?>',0,0,1);">
				 <?php for($i=0;$i<=$obj_ticket->f('ticket_num');$i++) {?>
				     <option value="<?php echo $i;?>"><?php echo $i;?></option>
				 <?php } ?>
				 </select></div>
				 
				 <input type="hidden" name="frm_ticket<?php echo $count;?>" class="tick" id="frm_ticket<?php echo $count;?>" value="" />
				 <input type="hidden" name="frm_mx_price<?php echo $count;?>" id="frm_mx_price<?php echo $count;?>" value="" />
				 <input type="hidden" name="frm_us_price<?php echo $count;?>" id="frm_us_price<?php echo $count;?>" value="" />
				 <input type="hidden" name="frm_us_tid<?php echo $count;?>" id="frm_us_tid<?php echo $count;?>" value="" />
				 
				 <?php
				     }
				else
				{
					echo "Sold Out ";
				}
				}
				else
				{
					echo "Not available ";
				}
				?>
				 
			     </td>
			     <td width="66%" style="padding-left: 6px;"><?php if($_SESSION['langSessId']=='eng') { echo $obj_ticket->f('ticket_name_en'); } else { echo $obj_ticket->f('ticket_name_sp');}?></td>
			     <td width="3%" style="padding-right: 4px;"><a href="#showticketdes<?php echo $obj_ticket->f('ticket_id'); ?>" id="ticket_des<?php echo $obj_ticket->f('ticket_id'); ?>">
				 <img src="<?php echo $obj_base_path->base_path(); ?>/images/select_table_img1.png" border="0" /></a></td>
			     <td width="21%">
				 <div class="amount_btn">
				 <?php  
				     if($obj_ticket->f('price_us')!="0.00" && $obj_ticket->f('price_us')!="" && $obj_ticket->f('price_mx')!="0.00" && $obj_ticket->f('price_mx')!=""){
				   ?>										
				   <span class="block_box">US $<?php echo number_format($obj_ticket->f('price_us'),2); ?> </span><span>MXP <?php echo number_format($obj_ticket->f('price_mx'),2); ?></span>
				   <?php 
				     } else if($obj_ticket->f('price_us')!="0.00" && $obj_ticket->f('price_us')!=""){
				   ?>
				 <span class="block_box">US $<?php echo number_format($obj_ticket->f('price_us'),2); ?> </span><?php } else if($obj_ticket->f('price_mx')!="0.00" && $obj_ticket->f('price_mx')!="") { ?><span style="width:">MXP <?php echo number_format($obj_ticket->f('price_mx'),2); ?></span><?php } ?>
				 
			     <?php /*?><?php if($_SESSION['langSessId']=='eng') { ?>$<?php echo $obj_ticket->f('price_us'); } else { echo $obj_ticket->f('price_mx');}?><?php */?></div></td>
			   </tr>
			   <script type="text/javascript">
			     $(document).ready(function() {
				 $("#ticket_des<?php echo $obj_ticket->f('ticket_id'); ?>").fancybox({ 
				 'hideOnOverlayClick':false,
				 'hideOnContentClick':false
				 });
			     });
			   </script>
			   <div style="display:none;">
			     <div style="width:500px; height:auto; background:#FFF; padding:10px;" id="showticketdes<?php echo $obj_ticket->f('ticket_id'); ?>">
				 <?php if($_SESSION['langSessId']=='eng') { echo $obj_ticket->f('description_en'); } else { echo $obj_ticket->f('description_sp');}?>
			     </div>
			   </div>
			   <?php
				 $count++;
				}
				?>
				<input type="hidden" name="frm_count" id="frm_count" value="<?php echo $count;?>" />
				<?php
			     }
			     else{
			   ?>
			   <?php /*?> <tr>
			     <td colspan="4" style="margin:0 0 0 10px;"><?=NO_TICKETS_AVAILABLE?></td>
			   </tr><?php */?>
			   <?php } 
					}
					else
					{
						if($obj_ticket->num_rows()) { 
							if($_SESSION['langSessId']=='eng') {
								echo '<tr><td width="10%">Reservation for this event has expired</td></tr>';
							}
							elseif($_SESSION['langSessId']=='spn') {
								echo '<tr><td width="10%">Reservaci&#243;n para este evento ha vencido</td></tr>';
							}
						}
					}
	
				  ?>
			       
		       </table>
		       </div>

			<?php
			}
			else			
			{
			?>
			  <div class="select_box1">
					
                           <table width="100%" border="0" cellspacing="0" cellpadding="0" class="select_table1">
                              <tr>
                                <td colspan="4"><div class="heading"><?php if($obj_ticket->num_rows()) { echo SELECT_TICKETS; } else { echo NO_TICKETS_AVAILABLE; }?></div></td>
                              </tr>
                              <?php 
                              $obj_expire->getTicketByExp($event_id); 
				if($obj_expire->num_rows()){  
				      
				      if($obj_ticket->num_rows()){
					      $count=1;
                                    while($obj_ticket->next_record()){
                              ?>
                              <tr>
                                <td width="10%">
                                <?php 
								
				$tt = time();
				if($tt<=$obj_ticket->f('to_ticket')){
					if($obj_ticket->f('ticket_num') > 0){
				?>
                                    
                                    <div class="dropdown25"><select name="ticket_num" id="ticket_num<?php echo $count;?>"
				    <?php if($objEvent->f('identical_function')==1){?>
				    onchange="addtocart(<?php echo $count;?>,<?php echo $obj_ticket->f('price_mx');?>,<?php echo $obj_ticket->f('price_us');?>,<?php echo $obj_ticket->f('ticket_id');?>,<?php echo $event_id;?>,0,1,0);"
				    <?php }elseif($objEvent->f('identical_function')==0){?>
				    onchange="addtocart_new(<?php echo $count;?>,<?php echo $obj_ticket->f('price_mx');?>,<?php echo $obj_ticket->f('price_us');?>,<?php echo $obj_ticket->f('ticket_id');?>,<?php echo $event_id;?>,'<?php echo date("d-m-Y",strtotime($event_date));?>','<?php echo date("d-m-Y",strtotime($event_date_end));?>','<?php echo date('g:i A',strtotime($event_time));?>','<?php echo date('g:i A',strtotime($event_time_end));?>',1,0,0);"
				    <?php } ?>
				    >
                                    <?php for($i=0;$i<=$obj_ticket->f('ticket_num');$i++) {?>
                                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                    <?php } ?>
                                    </select></div>
                                    
				    <input type="hidden" name="frm_ticket<?php echo $count;?>" class="tick" id="frm_ticket<?php echo $count;?>" value="" />
                                    <input type="hidden" name="frm_mx_price<?php echo $count;?>" id="frm_mx_price<?php echo $count;?>" value="" />
                                    <input type="hidden" name="frm_us_price<?php echo $count;?>" id="frm_us_price<?php echo $count;?>" value="" />
                                    <input type="hidden" name="frm_us_tid<?php echo $count;?>" id="frm_us_tid<?php echo $count;?>" value="" />
                                    
                                    <?php
                                    	}
						else
						{
							echo "Sold Out ";
						}
					}
					else
					{
						echo "Not available ";
					}
					?>
                                    
                                </td>
                                <td width="66%" style="padding-left: 6px;"><?php if($_SESSION['langSessId']=='eng') { echo $obj_ticket->f('ticket_name_en'); } else { echo $obj_ticket->f('ticket_name_sp');}?></td>
                                <td width="3%" style="padding-right: 4px;"><a href="#showticketdes<?php echo $obj_ticket->f('ticket_id'); ?>" id="ticket_des<?php echo $obj_ticket->f('ticket_id'); ?>">
                                    <img src="<?php echo $obj_base_path->base_path(); ?>/images/select_table_img1.png" border="0" /></a></td>
                                <td width="21%">
                                    <div class="amount_btn">
                                    <?php  
                                        if($obj_ticket->f('price_us')!="0.00" && $obj_ticket->f('price_us')!="" && $obj_ticket->f('price_mx')!="0.00" && $obj_ticket->f('price_mx')!=""){
                                      ?>										
                                      <span class="block_box">US $<?php echo number_format($obj_ticket->f('price_us'),2); ?> </span><span>MXP <?php echo number_format($obj_ticket->f('price_mx'),2); ?></span>
                                      <?php 
                                        } else if($obj_ticket->f('price_us')!="0.00" && $obj_ticket->f('price_us')!=""){
                                      ?>
                                    <span class="block_box">US $<?php echo number_format($obj_ticket->f('price_us'),2); ?> </span><?php } else if($obj_ticket->f('price_mx')!="0.00" && $obj_ticket->f('price_mx')!="") { ?><span style="width:">MXP <?php echo number_format($obj_ticket->f('price_mx'),2); ?></span><?php } ?>
                                    
                                <?php /*?><?php if($_SESSION['langSessId']=='eng') { ?>$<?php echo $obj_ticket->f('price_us'); } else { echo $obj_ticket->f('price_mx');}?><?php */?></div></td>
                              </tr>
                              <script type="text/javascript">
                                $(document).ready(function() {
                                    $("#ticket_des<?php echo $obj_ticket->f('ticket_id'); ?>").fancybox({ 
                                    'hideOnOverlayClick':false,
                                    'hideOnContentClick':false
                                    });
                                });
                              </script>
                              <div style="display:none;">
                                <div style="width:500px; height:auto; background:#FFF; padding:10px;" id="showticketdes<?php echo $obj_ticket->f('ticket_id'); ?>">
                                    <?php if($_SESSION['langSessId']=='eng') { echo $obj_ticket->f('description_en'); } else { echo $obj_ticket->f('description_sp');}?>
                                </div>
                              </div>
                              <?php
                                    $count++;
				}
				?>
				<input type="hidden" name="frm_count" id="frm_count" value="<?php echo $count;?>" />
				<?php
                                }
                                else{
                              ?>
                              <?php /*?> <tr>
                                <td colspan="4" style="margin:0 0 0 10px;"><?=NO_TICKETS_AVAILABLE?></td>
                              </tr><?php */?>
                              <?php } 
					}
					else
					{
						if($obj_ticket->num_rows()) { 
							if($_SESSION['langSessId']=='eng') {
								echo '<tr><td width="10%">Reservation for this event has expired</td></tr>';
							}
							elseif($_SESSION['langSessId']=='spn') {
								echo '<tr><td width="10%">Reservaci&#243;n para este evento ha vencido</td></tr>';
							}
						}
					}

				  ?>
                                  
                          </table>
                          </div>
			  <?php
				}
			  ?>
			  
                          <?php  
				if($obj_ticket->num_rows()){?>
                              <div class="select_box2">
                             <?php if($_SESSION['langSessId']=='eng') {?>
                                <div><a href="javascript:void(0);" onclick="save();"><img src="<?php echo $obj_base_path->base_path(); ?>/images/reserv_btn.gif" /></a></div>
                             <?php } else {?>   
                                <div><a href="javascript:void(0);" onclick="save();"><img src="<?php echo $obj_base_path->base_path(); ?>/images/spainreser_btn.gif" /></a></div>
                             <?php } ?>  
                             </div>
				<?php 
				      } 
			      } 
			      elseif($_REQUEST['sub_id'] != '' && $access!= 2)
			      {
				      
				      $obj_sub_ticket->subgetTicketById($_REQUEST['sub_id'],$objsub_event->f('parent_id')); 
				      $obj_sub_ticket_img->subgetTicketById($_REQUEST['sub_id'],$objsub_event->f('parent_id')); 
				      
			      ?>
                         <div class="select_box1">
				
                           <table width="100%" border="0" cellspacing="0" cellpadding="0" class="select_table1">
                              <tr>
                                <td colspan="4"><div class="heading"><?php if($obj_sub_ticket->num_rows()) { echo SELECT_TICKETS; } else { echo NO_TICKETS_AVAILABLE; }?></div></td>
                              </tr>
                              <?php 
                                if($obj_sub_ticket->num_rows()){
                                    while($obj_sub_ticket->next_record()){
                              ?>
                              <tr>
                                <td width="10%">
                                    <div class="dropdown25"><select name="">
                                    <?php for($i=0;$i<=$obj_sub_ticket->f('ticket_num');$i++) {?>
                                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                    <?php } ?>
                                    </select></div>
                                </td>
                                <td width="66%" style="padding-left: 6px;"><?php if($_SESSION['langSessId']=='eng') { echo $obj_sub_ticket->f('ticket_name_en'); } else { echo $obj_sub_ticket->f('ticket_name_sp');}?></td>
                                <td width="3%" style="padding-right: 4px;"><a href="#showticketdes<?php echo $obj_sub_ticket->f('ticket_id'); ?>" id="ticket_des<?php echo $obj_sub_ticket->f('ticket_id'); ?>">
                                    <img src="<?php echo $obj_base_path->base_path(); ?>/images/select_table_img1.png" border="0" /></a></td>
                                <td width="21%">
                                    <div class="amount_btn">
                                    <?php  
                                        if($obj_sub_ticket->f('price_us')!="0.00" && $obj_sub_ticket->f('price_us')!="" && $obj_sub_ticket->f('price_mx')!="0.00" && $obj_sub_ticket->f('price_mx')!=""){
                                      ?>										
                                      <span class="block_box">US $<?php echo number_format($obj_sub_ticket->f('price_us'),2); ?> </span><span>MXP <?php echo number_format($obj_sub_ticket->f('price_mx'),2); ?></span>
                                      <?php 
                                        } else if($obj_sub_ticket->f('price_us')!="0.00" && $obj_sub_ticket->f('price_us')!=""){
                                      ?>
                                    <span class="block_box">US $<?php echo number_format($obj_sub_ticket->f('price_us'),2); ?> </span><?php } else if($obj_sub_ticket->f('price_mx')!="0.00" && $obj_sub_ticket->f('price_mx')!="") { ?><span style="width:">MXP <?php echo number_format($obj_sub_ticket->f('price_mx'),2); ?></span><?php } ?>
                                    
                                <?php /*?><?php if($_SESSION['langSessId']=='eng') { ?>$<?php echo $obj_ticket->f('price_us'); } else { echo $obj_ticket->f('price_mx');}?><?php */?></div></td>
                              </tr>
                              <script type="text/javascript">
                                $(document).ready(function() {
                                    $("#ticket_des<?php echo $obj_sub_ticket->f('ticket_id'); ?>").fancybox({ 
                                    'hideOnOverlayClick':false,
                                    'hideOnContentClick':false
                                    });
                                });
                              </script>
                              <div style="display:none;">
                                <div style="width:500px; height:auto; background:#FFF; padding:10px;" id="showticketdes<?php echo $obj_sub_ticket->f('ticket_id'); ?>">
                                    <?php if($_SESSION['langSessId']=='eng') { echo $obj_sub_ticket->f('description_en'); } else { echo $obj_sub_ticket->f('description_sp');}?>
                                </div>
                              </div>
                              <?php
                                    }
                                }
                                else{
                              ?>
                              <?php /*?> <tr>
                                <td colspan="4" style="margin:0 0 0 10px;"><?=NO_TICKETS_AVAILABLE?></td>
                              </tr><?php */?>
                              <?php } ?>
                                  
                          </table>
			</div>	
                         <?php  if($obj_sub_ticket->num_rows()){?><div class="select_box2">
                            <div class="buy_btn27"><a href="#"><?=BUY?></a></div>
                            <div class="icon_link">
                                <ul>
                                    <li><a href="#"><img src="<?php echo $obj_base_path->base_path(); ?>/images/icon1.gif" border="0" /></a></li>
                                    <li><a href="#"><img src="<?php echo $obj_base_path->base_path(); ?>/images/icon2.gif" border="0" /></a></li>
                                    <li><a href="#"><img src="<?php echo $obj_base_path->base_path(); ?>/images/icon3.gif" border="0" /></a></li>
                                    <li><a href="#"><img src="<?php echo $obj_base_path->base_path(); ?>/images/icon4.gif" border="0" /></a></li>
                                    <li><a href="#"><img src="<?php echo $obj_base_path->base_path(); ?>/images/icon5.gif" border="0" /></a></li>
                                </ul>
                            </div>
                         </div><?php } ?>				
						<?php }
						elseif($_REQUEST['sub_id'] != '' && $access == 2)
						{
						?>
						
							<?php /*?><table width="100%" border="0" cellspacing="0" cellpadding="0" class="select_table1">
                              <tr>
                                <td colspan="4"><div class="heading"><?php if($obj_ticket->num_rows()) { echo SELECT_TICKETS; } else { echo NO_TICKETS_AVAILABLE; }?></div></td>
                              </tr>
                              <?php 
                                if($obj_ticket->num_rows()){
                                    while($obj_ticket->next_record()){
                              ?>
                              <tr>
                                <td width="10%">
                                    <div class="dropdown25"><select name="">
                                    <?php for($i=0;$i<=$obj_ticket->f('ticket_num');$i++) {?>
                                        <option value="<?php echo $i;?>"><?php echo $i;?></option>
                                    <?php } ?>
                                    </select></div>
                                </td>
                                <td width="66%" style="padding-left: 6px;"><?php if($_SESSION['langSessId']=='eng') { echo $obj_ticket->f('ticket_name_en'); } else { echo $obj_ticket->f('ticket_name_sp');}?></td>
                                <td width="3%" style="padding-right: 4px;"><a href="#showticketdes<?php echo $obj_ticket->f('ticket_id'); ?>" id="ticket_des<?php echo $obj_ticket->f('ticket_id'); ?>">
                                    <img src="<?php echo $obj_base_path->base_path(); ?>/images/select_table_img1.png" border="0" /></a></td>
                                <td width="21%">
                                    <div class="amount_btn">
                                    <?php  
                                        if($obj_ticket->f('price_us')!="0.00" && $obj_ticket->f('price_us')!="" && $obj_ticket->f('price_mx')!="0.00" && $obj_ticket->f('price_mx')!=""){
                                      ?>										
                                      <span class="block_box">US $<?php echo number_format($obj_ticket->f('price_us'),2); ?> </span><span>MXP <?php echo number_format($obj_ticket->f('price_mx'),2); ?></span>
                                      <?php 
                                        } else if($obj_ticket->f('price_us')!="0.00" && $obj_ticket->f('price_us')!=""){
                                      ?>
                                    <span class="block_box">US $<?php echo number_format($obj_ticket->f('price_us'),2); ?> </span><?php } else if($obj_ticket->f('price_mx')!="0.00" && $obj_ticket->f('price_mx')!="") { ?><span style="width:">MXP <?php echo number_format($obj_ticket->f('price_mx'),2); ?></span><?php } ?>
                                	</div>
                                </td>
                              </tr>
                              <script type="text/javascript">
                                $(document).ready(function() {
                                    $("#ticket_des<?php echo $obj_ticket->f('ticket_id'); ?>").fancybox({ 
                                    'hideOnOverlayClick':false,
                                    'hideOnContentClick':false
                                    });
                                });
                              </script>
                              <div style="display:none;">
                                <div style="width:500px; height:auto; background:#FFF; padding:10px;" id="showticketdes<?php echo $obj_ticket->f('ticket_id'); ?>">
                                    <?php if($_SESSION['langSessId']=='eng') { echo $obj_ticket->f('description_en'); } else { echo $obj_ticket->f('description_sp');}?>
                                </div>
                              </div>
                              <?php
                                    }
                                }
                              ?>
                                  
                          </table><?php */?>
							
						<?php
						}
						?>
                       </form>
                        
						<div class="clear"></div>
						<div class="icon_box2"><p>Invite your friends <a href="#"><img src="<?php echo $obj_base_path->base_path(); ?>/images/icon9.gif" border="0" align="absmiddle"/></a> <a href="#"><img src="<?php echo $obj_base_path->base_path(); ?>/images/icon8.gif" border="0" align="absmiddle"/></a></p></div>
						<div class="clear"></div>
                        <div class="like_box" style=" margin: 0 0 0 0; width: 333px; vertical-align: baseline !important;">
                          <table width="100%" border="0" cellspacing="0" cellpadding="0" class="like_table">
                              <tr><td>
					<div style="margin: 4px;float:left;padding: 5px;">
					
					<?php $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
					//echo "url= ".$url;
					?>
					<?php if($_SESSION['langSessId']=='eng' || $_REQUEST['lang']=='eng')
					 {
						 $lang="en_US";
						 $url2=$url."/lang/eng";
					 }
					 else 
					 {
						$lang="es_ES";
						$url2=$url."/lang/spn";
					 }
					 
					 ?>
					<div id="fb-root"></div>
					<script>(function(d, s, id) {
					  var js, fjs = d.getElementsByTagName(s)[0];
					  if (d.getElementById(id)) return;
					  js = d.createElement(s); js.id = id;
					  js.src = "//connect.facebook.net/<?=$lang?>/all.js#xfbml=1&appId=1411675195718012";
					  fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));</script>
					
					<div class="fb-share-button" data-href="<?php echo $url;?>" data-type="box_count"></div>
					
					
					<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo $url;?>" data-via="Kpasapp" data-lang="<?=$lang?>" data-related="anywhereTheJavascriptAPI" data-text="<?php if($_SESSION['langSessId']=='eng') { echo htmlentities(stripslashes($objEvent->f('event_name_en'))); } else { echo htmlentities(stripslashes($objEvent->f('event_name_sp')));}?><?php if($_SESSION['langSessId']=='eng') { echo date("l, j F, Y, g:i a",strtotime($objEvent->f('event_start_date_time'))).", ".$obj_venue->f('venue_name').", ".$obj_venue->f('city').", ".$obj_venue->f('st_name').", ".$objEvent->f('event_short_desc_en'); } else { setlocale(LC_TIME, 'es_ES'); echo  strftime("%a",strtotime($event_date))." ".strftime("%e",strtotime($event_date))." de ".strftime("%b",strtotime($event_date)).", ".strftime("%Y",strtotime($event_date)).", ".$obj_venue->f('venue_name_sp').", ".$obj_venue->f('city').", ".$obj_venue->f('st_name').", ".$objEvent->f('event_short_desc_sp');}?>" data-count="vertical">Tweet</a>
					
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
					
					<!-- Place this tag where you want the +1 button to render. -->
					<div class="g-plusone" data-size="tall"></div>
					
					<!-- Place this tag after the last +1 button tag. -->
					<script type="text/javascript">
					  (function() {
					    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
					    po.src = 'https://apis.google.com/js/platform.js';
					    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
					  })();
					</script>
					
					
					
					<script type="text/javascript" src="http://www.reddit.com/static/button/button2.js"></script>
					
					<!-- Place this tag where you want the su badge to render -->
					<su:badge layout="5"></su:badge>
					
					<!-- Place this snippet wherever appropriate -->
					<script type="text/javascript">
					  (function() {
					    var li = document.createElement('script'); li.type = 'text/javascript'; li.async = true;
					    li.src = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//platform.stumbleupon.com/1/widgets.js';
					    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(li, s);
					  })();
					</script>
					
					</div>
					</td>
                              </tr>
                          </table>	
			</div>
			<div class="clear"></div>		
                        <div class="offer_box" style="float: left; margin: 0; width:100%;">                       
                       	 <div class="preview_imgbox" style="float: left; width: 100%;">
                         <div class="imgbox" style="width:100%; height: auto;">
                            <ul>
                           <!-- <li><img src="<?php echo $obj_base_path->base_path(); ?>/images/preview_img1.gif" border="0" /></li>-->
                         	  
				<!-----------FOR  FEATURE  IMAGE------------------>
				
				
				<?php  $objfeatureimage->isfeatureImage($event_id);
					$objfeatureimage->next_record();
				       if($objfeatureimage->num_rows())
				       {?>
					<li style="margin: 0; display: block;">
                                <a href="#feature_image" id="feature"><img src="<?php echo $obj_base_path->base_path(); ?>/files/event/medium/<?php echo $objfeatureimage->f('media_url');?>"  border="0"  /></a>
                                </li>
                                 <script type="text/javascript">
					$(document).ready(function() {
						$("#feature").fancybox({ 
						'hideOnOverlayClick':false,
						'hideOnContentClick':false
						});
					});
				  </script>
                                  <div style="display:none;">
                                  	<div style="width:auto;height:auto; background:#FFF; padding:10px;" id="feature_image">
                                    	<img src="<?php echo $obj_base_path->base_path(); ?>/files/event/medium/<?php echo $objfeatureimage->f('media_url');?>"  border="0"  />
                                    </div>
                                  </div>
					
				<?php } // if end
				    else
				        {
					   if($objEvent->f('event_photo')){  

					?>
                            	<li style="margin: 0; display: block;">
                                <a href="#feature_image" id="feature"><img src="<?php echo $obj_base_path->base_path(); ?>/files/event/medium/<?php echo $objEvent->f('event_photo');?>"  border="0"  /></a>
                                </li>
                                 <script type="text/javascript">
					$(document).ready(function() {
						$("#feature").fancybox({ 
						'hideOnOverlayClick':false,
						'hideOnContentClick':false
						});
					});
				  </script>
                                  <div style="display:none;">
                                  	<div style="width:auto;height:auto; background:#FFF; padding:10px;" id="feature_image">
                                    	<img src="<?php echo $obj_base_path->base_path(); ?>/files/event/large/<?php echo $objEvent->f('event_photo');?>"  border="0"  />
                                    </div>
                                  </div>
                                <?php
				  } // inner if	end				
			        
					} //else end
				
				?>
				
				<!--------------FOR FEATURE IMAGE  END------------------------->
				  
                            
                            </ul>
                         </div>
                        </div>
			</div>				
			<div class="clear"></div>		
                      </div>
                    </div>
                    <div class="clear"></div>
                    <div class="show_box"> 
					                    
                      <div class="leftbox" id="scrollto">
                       	<p style="min-height: 0; height: auto;">
						<?php //if($_SESSION['langSessId']=='eng') { echo $objEvent->f('event_details_en'); } else { echo $objEvent->f('event_details_sp');}?>
						<?php 
						if($sub_id == ''){
						if($_SESSION['langSessId']=='eng') { echo stripslashes($objEvent->f('event_details_en')); } else { echo stripslashes($objEvent->f('event_details_sp'));}
						}
						else
						{
						if($_SESSION['langSessId']=='eng') { echo stripslashes($obj_chk->f('event_details_en')); } else { echo stripslashes($obj_chk->f('event_details_sp'));}
						}
						?>
						</p>
                      </div>
                    </div>
                </div>
                <div class="clear"></div>
				
<!--                <div class="view_box">
                	<div class="heading"><?=SECTION1_TEXT;?></div>
                	<div class="hot_events">
                    
                    </div>
                    <div class="clear"></div>d
                </div>
-->                <div class="clear"></div>
        <?php if($obj_event_media->num_rows()){ ?> <!---if images----------------------->
	
                <div class="view_box" style="margin: 0;">
			<div class="eventgallery_box">
                	<!--<div class="heading"><?php //echo SECTION2_TEXT;?></div>-->
			<div class="heading"><?php if($_SESSION['langSessId']=='eng') { echo "Media Gallery" ; } else { echo "Galería multimedia" ;} ?></div>
			
                   <!-- <div class="photo_count"><?php // echo FAN_PHOTOS?> (<?php //echo $num;?>)</div>-->
		    
		    <!--------------------carousel for event gallery---------------------------->	
                	<div class="eventgallery" style="position: relative;">
               <div id="go-prev1" style=" margin: 0; top: 53px; left: 0; position: absolute;"><img src="<?php echo $obj_base_path->base_path(); ?>/images/arrow_left.png" /></div> 
               <div id="go-next1" style=" margin: 0; top: 53px; right: 0; position: absolute;"><img src="<?php echo $obj_base_path->base_path(); ?>/images/arrow_right.png" /></div> 
                <ul id="slider1" style="padding-left:2px; padding: 0; width: 580px; height: 140px; overflow: hidden;">
                <?php
				
					while($obj_event_media->next_record()){
						if($obj_event_media->f('media_url')!=""){
            ?>

		 <?php
		  $media_id=$obj_event_media->f('m_id');
		  $media_name = $obj_event_media->f('media_name');
		 $video_url=$obj_event_media->f('media_url');
		 $var=videoType($video_url);
		//echo "<br/>var=".$var;?>
	       <?php if($obj_event_media->f('media_format')!="video") {?>
	      <!-- <li style="border: 1px solid #CCCCCC; margin: 0 4px; width: 170px;" id="media_click"><a href="<?php //echo $obj_base_path->base_path(); ?>/files/event/gallery/large/<?php // echo $obj_event_media->f('media_url');?>" class="fancybox"><img src="<?php //echo $obj_base_path->base_path(); ?>/files/event/gallery/thumb/<?php //echo $obj_event_media->f('media_url'); ?>" alt="" width="160" height="90" /></a></li>-->
	     <?php if($_SESSION['langSessId']=='eng') { ?> 
	       <li style="border: 1px solid #CCCCCC; margin: 0 4px; width: 170px;" id="media_click"><a href='<?php echo $obj_base_path->base_path(); ?>/en/event/<?php echo htmlentities(stripslashes($objEvent->f('event_name_en')));?>/gallery/<?php echo $media_id;?>/<?php echo $media_name;?>' class="fancybox"><img src="<?php echo $obj_base_path->base_path(); ?>/files/event/thumb/<?php echo $obj_event_media->f('media_url'); ?>" alt="" width="190" height="143" /></a></li>
	       <?php } else {?>
	       <li style="border: 1px solid #CCCCCC; margin: 0 4px; width: 170px;" id="media_click"><a href='<?php echo $obj_base_path->base_path(); ?>/es/evento/<?php echo htmlentities(stripslashes($objEvent->f('event_name_sp')));?>/galeria/<?php echo $media_id;?>/<?php echo $media_name;?>' class="fancybox"><img src="<?php echo $obj_base_path->base_path(); ?>/files/event/thumb/<?php echo $obj_event_media->f('media_url'); ?>" alt="" width="190" height="143" /></a></li> <?php } ?>
	       <?php } // if not video

	       else{?>
	       <?php  if($var=="youtube") { //echo "YOUTUBE";?>
		
	     <!-- <iframe width="150" height="90" src="http://www.youtube.com/watch?v=97VqfrsgyAM"></iframe>-->
	     <li style="border: 1px solid #CCCCCC; margin: 0 4px; width: 170px;"><div class="trns"><a href="<?php echo $obj_base_path->base_path(); ?><?php if($_SESSION['langSessId']=='eng') { echo "/en/event/";}else { echo "/es/event/";}?><?php echo htmlentities(stripslashes($objEvent->f('event_name_en')));?>/gallery/<?php echo $media_id;?>/<?php echo $media_name;?>" class="fancybox"><img src='<?php echo "http://img.youtube.com/vi/".end(explode('=',$obj_event_media->f('media_url')))."/default.jpg"?>'/></a></div></li>
		  <?php }
		  elseif($var=="vimeo") { ?>
		  <li style="border: 1px solid #CCCCCC; margin: 0 4px; width: 170px;"><a href="<?php echo $obj_base_path->base_path(); ?><?php if($_SESSION['langSessId']=='eng') { echo "/en/event/";}else { echo "/es/event/";}?><?php echo htmlentities(stripslashes($objEvent->f('event_name_en')));?>/gallery/<?php echo $media_id;?>/<?php echo $media_name;?>"  class="fancybox"><iframe src="//player.vimeo.com/video/<?php echo  end(explode('/',$obj_event_media->f('media_url')));?>" width="190" height="143" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></a></li>
		  <?php }
		  elseif($var=="dailymotion") {
		    $dm_vid_arr=explode('_',end(explode('/',$obj_event_media->f('media_url'))));
		    $dm_vid = $dm_vid_arr[0];
		    ?>
		 <li style="border: 1px solid #CCCCCC; margin: 0 4px; width: 170px;"><a href="<?php echo $obj_base_path->base_path(); ?><?php if($_SESSION['langSessId']=='eng') { echo "/en/event/";}else { echo "/es/event/";}?><?php echo htmlentities(stripslashes($objEvent->f('event_name_en')));?>/gallery/<?php echo $media_id;?>/<?php echo $media_name;?>"  class="fancybox"><iframe frameborder="0" width="190" height="143" src="//www.dailymotion.com/embed/video/<?php echo  $dm_vid;?>" allowfullscreen></iframe></a></li>
		<?php   } ?>
		 
		 <?php 
		     } //else video
		  } //if media url
		} //while
		
			?>
               
          
               </ul>    
            </div>
			</div>
			
		 <!---------------Fancy  box  start---------------------->

			<script type="text/javascript">
				jQuery(document).ready(function() {
				    //jQuery("a.fancybox").fancybox();
				
				
				$(".fancybox").fancybox({
					 'width': 720,
					 'height': 900,
					 'autoSize': false,
					 'transitionIn'    : 'elastic',
					'transitionOut'    : 'elastic',
					'type'             : 'iframe'
				    });
				    
				 
			});
			
				    
			    </script>
<!----------------Fancy box end--------------------->
	<!--------------------carousel for event gallery---------------------------->		
                    <div class="clear"></div>
<!--                    <div class="time_reviews_box" style="width: 652px; margin: 10px auto; float: none; overflow: hidden;">
                        <div class="reviews_box" style="margin: 5px 0 10px 0;">
                            <div class="left_option"><?=REVIEWS?> (899)<div class="reviews"><a href="#"><img src="<?php echo $obj_base_path->base_path(); ?>/images/ster_review.png" border="0" /></a></div></div>
                            <div class="right_option"><div class="dropdown1"><select name=""><option>4.6 / 5</option></select></div></div>
                        </div>
                        <div class="right_btn"><a href="#"><?=WRITE_REVIEW?></a></div>
                        <div class="clear"></div>        
                       <div class="dropdown3"><select name=""><option><?=CHOOSE_SORT_ORDER?> </option></select></div>         
                  </div>
-->                  <div class="clear"></div>
<!--                  <div class="Tchai_box">
                  	<div class="reviews"><a href="#"><img src="<?php //echo $obj_base_path->base_path(); ?>/images/ster_review.png" border="0" /></a></div>
                    <div class="feature_btn"><a href="#"><?=FEATURED_REVIEW?></a></div>
                    <div class="clear"></div>
                    <p>TEST</p>
                    <div class="share_this">
                    	<ul>
                        	<li style="margin: 0 10px 0 0;"><?=SHARE_REVIEW?>:</li>
                            <li><a href="#"><img src="<?php ///echo $obj_base_path->base_path(); ?>/images/share_icon1.gif" border="0" /></a></li>
                            <li><a href="#"><img src="<?php //echo $obj_base_path->base_path(); ?>/images/share_icon2.gif" border="0" /></a></li>
                        </ul>
                    </div>
                  </div>
--><!--    <div class="Tchai_box">
                  	<div class="reviews"><a href="#"><img src="<?php //echo $obj_base_path->base_path(); ?>/images/ster_review.png" border="0" /></a></div>
                    <div class="headtxt">Tchaikovsky was Great</div>
                  	<p>Hollywood Bowl - Hollywood, CA - Fri, Sep 7 2012</p>
                    <p>Posted 09/18/2012 by <strong>EKHO1</strong> <a href="#">this Fan's Reviews</a></p>
                    <div class="feature_btn"><a href="#">Featured Review</a></div>
                    <div class="clear"></div>
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo,</p>
                    <p><strong>Favorite moment: </strong>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. <br />Aenean commodo ligula eget dolor. </p>
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. <span><a href="#">Yes</a> | <a href="#">No</a> </span> <span style="margin: 0 10px;"><a href="#">(Report as inappropriate)</a></span></p>
                    <div class="share_this">
                    	<ul>
                        	<li style="margin: 0 10px 0 0;">Share this review:</li>
                            <li><a href="#"><img src="<?php //echo $obj_base_path->base_path(); ?>/images/share_icon1.gif" border="0" /></a></li>
                            <li><a href="#"><img src="<?php //echo $obj_base_path->base_path(); ?>/images/share_icon2.gif" border="0" /></a></li>
                        </ul>
                    </div>
                  </div>
                  <div class="page_box2">
               	 	<div class="pagination2">
                    	<ul>
                        	<li><a href="#" class="active">1</a></li>
                            <li><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                            <li><a href="#">4</a></li>
                            <li><a href="#">5</a></li>
                            <li><a href="#">...</a></li>
                            <li><a href="#">300</a></li>
                            <li><a href="#">Next</a></li>
                            <li><a href="#" style="color: #929292;"> >> </a></li>
                        </ul>
                    </div>
                </div>-->
                </div>
		<?php 
				} // if num rows
			?>
            
</div>        	       
			<?php include("include/frontend_rightsidebar.php");?>
			<div class="clear"></div>
			<div class="clear"></div>
			<?php include("include/footer_bottom.php");?>
			<div class="clear"></div>
    	</div>
        <div class="clear"></div>
	</div>
    <div class="clear"></div>
    <?php include("include/frontend_footer.php");?>
</div>

<?php if($obj_venue->f('venue_name')!=$obj_venue_sub->f('venue_name')){?>
<!--<script type="text/javascript">
$(document).ready(function(){
	initialize('<?php echo $obj_venue->f('venue_name'); ?>,<?php echo $obj_venue->f('venue_address'); ?><?php echo $obj_venue->f('city'); ?>,<?php echo $obj_venue->f('st_name'); ?>');
})
</script>-->
<?php } ?>

</body>
</html>
