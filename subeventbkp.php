<?php 
include('include/user_inc.php');
$event_id = $_REQUEST['event_id'];
$sub_id = $_REQUEST['sub_id'];
$multi_id = $_REQUEST['multi_id'];
//print_r($_REQUEST);
//echo $multi_id; exit;

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
$obj_count=new user;

$obj_expire=new user;
$obj_samefunc=new user;


if($sub_id != ''){
$obj_chk->check_access($_REQUEST['sub_id']);
$obj_chk->next_record();
$access = $obj_chk->f('all_access');
}

// Event Details
$objEvent->getOrgEvent($event_id);
$objEvent->next_record();

$allData[1]['multi_id'] = 0;
$allData[1]['city'] = $objEvent->f('city_name');
$allData[1]['venue_name'] = $objEvent->f('venue_name');
$allData[1]['venue_name_sp'] = $objEvent->f('venue_name_sp');
$allData[1]['event_start_date_time'] = $objEvent->f('event_start_date_time');
$allData[1]['event_end_date_time'] = $objEvent->f('event_end_date_time');

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

$obj_ticket_img->getTicketById($event_id); 

//print_r($_SESSION);
//					********************************* imp *********************

/*if($_SESSION['ses_admin_id'] != ''){
	$obj_count->getCartCount($_SESSION['ses_admin_id']);
	if($obj_count->num_rows()>0){
		header("location: ".$obj_base_path->base_path()."/payment");
	}
}*/     
            
//					********************************* imp *********************

$_SESSION['cid'] = '';

if(isset($_POST['action']) && $_POST['action'] == 'cart')	
{
	//echo "success "; 
	/*echo $_POST['frm_event_id']."cvb ";
	echo $_POST['frm_multi_id']."cvbc ";
	echo $_POST['frm_count']."xxx ";*/
	$unique = time();
	for($i=1;$i<$_POST['frm_count'];$i++){
	/*echo $_POST['frm_ticket'.$i]." ";
	echo $_POST['frm_mx_price'.$i]." ";
	echo $_POST['frm_us_price'.$i]."<br />";*/
		if($_POST['frm_ticket'.$i] != ''){
			$cid[] = $obj_cart->add_to_cart($_POST['frm_event_id'],$_POST['frm_multi_id'],$_POST['frm_ticket'.$i],$_POST['frm_mx_price'.$i],$_POST['frm_us_price'.$i],$unique,$_POST['frm_us_tid'.$i],$_POST['frm_payment'],$_POST['frm_date'],$_POST['frm_end_date'],$_POST['frm_start'],$_POST['frm_end']);
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
		header("location: ".$obj_base_path->base_path()."/payment/".$_POST['frm_event_id']);
	//}
	/*print_r($cid);
	exit;*/
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#">
<head>
	
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta property="og:locale" content="en_US" />
<meta property="og:locale:alternate" content="es_ES" />
<meta property="og:type" content="website" />
<meta property='fb:app_id' content='1411675195718012' />

<?php $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";?>
<meta property="og:title" content="<?php if($_SESSION['langSessId']=='eng') { echo stripslashes($objEvent->f('event_name_en')); } else { echo stripslashes($objEvent->f('event_name_sp'));}?>" />
<meta property='og:site_name' content='Kpasapp' />

<meta name="title" content="<?php if($_SESSION['langSessId']=='eng') { echo stripslashes($objEvent->f('event_name_en')); } else { echo stripslashes($objEvent->f('event_name_sp'));}?>" />

<meta property="og:url" content="<?php echo $actual_link; ?>" />
<meta property="og:description" content="<?php if($_SESSION['langSessId']=='eng') { echo $objEvent->f('event_short_desc_en'); } else { echo $objEvent->f('event_short_desc_sp');}?>" />
<?php if($objEvent->f('event_photo')==''){?>
<meta property="og:image" content="<?php echo $obj_base_path->base_path(); ?>/images/kpassa_logo_fb.png">
<?php
}
else
{
?>
<meta property="og:image" content="<?php echo $obj_base_path->base_path(); ?>/files/event/medium/<?php echo $objEvent->f('event_photo');?>">
<?php
}
?>
<title>Event</title>
<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/js/jquery.js"></script>
</head>
<link href="<?php echo $obj_base_path->base_path(); ?>/css/base.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/style99.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/header-frontend.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/pagination.css" rel="stylesheet" type="text/css" />
<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=AIzaSyCaEfiGqBVrb7GgQKoYeCkb7CNMcQGfT-s" type="text/javascript"></script>
<!-- jQuery lightBox plugin -->
<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/include/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/include/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $obj_base_path->base_path(); ?>/include/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script src="<?php echo $obj_base_path->base_path(); ?>/js/slides.min.jquery.js"></script>

<body>

<div class="left_panel bg">
            
            <div style="text-align:center;"><?php if($_SESSION['err'] != ''){ echo $_SESSION['err']; unset($_SESSION['err']);}?></div>
            <div class="clear"></div>
            
            	<div class="cheese_box">
               	<div class="heading1"><?php 
				if($sub_id == ''){
				if($_SESSION['langSessId']=='eng') { echo substr(stripslashes($objEvent->f('event_name_en')),0,60); } else { echo substr(stripslashes($objEvent->f('event_name_sp')),0,60);}
				}
				else
				{
				if($_SESSION['langSessId']=='eng') { echo substr(stripslashes($obj_chk->f('event_name_en')),0,60); } else { echo substr(stripslashes($obj_chk->f('event_name_sp')),0,60);}
				}
				?>
<!--                	<span style="float: right; margin: 0 auto; padding: 0; width: 160px;">
                    <img onclick="checkLoggedin()" src='<?php echo $obj_base_path->base_path(); ?>/images/save_btn.gif' width="160" height="36" border="0" style="cursor:pointer"/>
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
							  	<a href="<?php echo $obj_base_path->base_path(); ?>/events/<?php echo $objmul_date->f('parent_id');?>/sub_id/<?php echo $objmul_date->f('event_id');?>" class="link" data-fancybox-type="iframe">
								<?php if($_SESSION['langSessId']=='eng') echo $objmul_date->f('event_name_en'); else echo $objmul_date->f('event_name_sp');?> <br/><?php echo date('g:i A',strtotime($time))." - ".date('g:i A',strtotime($end_time)); ?>
								</a>
								<script type="text/javascript">
									$(document).ready(function() {
										$(".link").fancybox({ 
										type: 'iframe',
										'width': 1100,
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
			<div class="heading_top"><h1>Seleccione su función</h1></div>
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
                                      <th><?php echo date("D",strtotime($dd))." ".date("M",strtotime($dd))." ".date("d",strtotime($dd));?></th>
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
                                            <p style="cursor:pointer;" class="abc" id="tbl1<?php echo $kk;?>" onclick="setHover1(<?php echo $kk;?>,'<?php echo date("D",strtotime($multi_event_date_new))." ".date("M",strtotime($multi_event_date_new))." ".date("d",strtotime($multi_event_date_new)).", ".date("Y",strtotime($multi_event_date_new))." - ".date('g:i A',strtotime($multi_event_time_new))." to ".date('g:i A',strtotime($multi_event_time_end_new));?>','<?php echo $row['multi_id'];?>','<?php echo $event_id;?>','<?php echo date("d-m-Y",strtotime($multi_event_date_new));?>','<?php echo date("d-m-Y",strtotime($multi_event_end_new));?>','<?php echo date('g:i A',strtotime($multi_event_time_new));?>','<?php echo date('g:i A',strtotime($multi_event_time_end_new));?>')">
												<?php echo date('g:i A',strtotime($multi_event_time_new)); ?>
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
						if($objmulti_event->num_rows()){
							if($event_date<date("Y-m-d"))
							{
								$obj_cur_eve_dt->getCurrMultiEve($event_id);
								$obj_cur_eve_dt->next_record();
								
								if( $obj_cur_eve_dt->f('multi_start_time')){
						
					 ?>                      
                       <div class="timetxt">
                        <?php echo date("D",strtotime($obj_cur_eve_dt->f('multi_start_time')))." ".date("M",strtotime($obj_cur_eve_dt->f('multi_start_time')))." ".date("d",strtotime($obj_cur_eve_dt->f('multi_start_time'))).", ".date("Y",strtotime($obj_cur_eve_dt->f('multi_start_time')));?> - <?php echo date('g:i A',strtotime($obj_cur_eve_dt->f('multi_start_time'))); ?> to <?php echo date('g:i A',strtotime($obj_cur_eve_dt->f('multi_end_time'))); ?>
                       </div>

                       <?php
								}
							}
							else{
						?>
                       <div class="timetxt">
                        <?php echo date("D",strtotime($event_date))." ".date("M",strtotime($event_date))." ".date("d",strtotime($event_date)).", ".date("Y",strtotime($event_date));?> - <?php echo date('g:i A',strtotime($event_time)); ?> to <?php echo date('g:i A',strtotime($event_time_end)); ?>
                       </div>
                        <?php
							}
						}
						
						else{
                       ?>
                       <div class="timetxt">
                        <?php echo date("D",strtotime($event_date))." ".date("M",strtotime($event_date))." ".date("d",strtotime($event_date)).", ".date("Y",strtotime($event_date));?> - <?php echo date('g:i A',strtotime($event_time));
						
						if($event_date != $event_date_end) 
						{ 
							echo " <br /> To ";
							echo date("D",strtotime($event_date_end))." ".date("M",strtotime($event_date_end))." ".date("d",strtotime($event_date_end)).", ".date("Y",strtotime($event_date_end))." - ";?> <?php echo date('g:i A',strtotime($event_time_end)); 
						} 
						else 
						{ 
						?>
                         	to <?php echo date('g:i A',strtotime($event_time_end)); ?>
                            <?php } ?>
                       </div>
                       <?php
						}
					   ?>
<!--                       <div class="reviews_box">
                        <div class="left_option"><?=REVIEWS?> (899)<div class="reviews"><a href="#"><img src="<?php echo $obj_base_path->base_path(); ?>/images/ster_review.png" border="0" /></a></div></div>
                        <div class="right_option"><div class="dropdown1"><select name=""><option>4.6 / 5</option></select></div></div>
                       </div>
-->                        </div>
                        <div class="clear"></div>
                        <?php 
                            if($sub_id != ''){
                                    if($_SESSION['langSessId']=='eng') echo $obj_chk->f('event_short_desc_en'); else echo $obj_chk->f('event_short_desc_sp');
                            }
                            else{
                                    if($_SESSION['langSessId']=='eng') echo $objEvent->f('event_short_desc_en'); else echo $objEvent->f('event_short_desc_sp');
                            }
                        ?>
                        <div class="clear"></div>
                        <?php //if($_SESSION['langSessId']=='eng') echo $obj_chk->f('event_details_en'); else echo $obj_chk->f('event_details_sp');?>
                        <div class="clear"></div>
                        <?php 
			    if($sub_id == ''){
    			    ?>
			    <div style="margin:10px 0 20px; font-weight:bold;">
                        	<p style="margin: 0px 0px; padding: 0px 4px;"><?php echo $obj_venue->f('venue_name');?></p>
                        	<p style="margin: 0px 0px; padding: 0px 4px; font-weight:normal;"><?php echo $obj_venue->f('venue_address');?>
                                <br />
                         	<?php echo $obj_venue->f('city').', '.$obj_venue->f('st_name');?></p>
                            </div>
						
						<?php
						}
						else
						{
						
						       if($obj_venue->f('venue_name')!=$obj_venue_sub->f('venue_name')){?>
 							    <div style="margin:10px 0 20px; font-weight:bold;">
								<p style="margin: 0px 0px; padding: 0px 4px;"><?php echo $obj_venue_sub->f('venue_name');?></p>
								<p style="margin: 0px 0px; padding: 0px 4px; font-weight:normal;"><?php echo $obj_venue_sub->f('venue_address');?>
                                                                <br />
								<?php echo $obj_venue_sub->f('city').', '.$obj_venue_sub->f('st_name');?></p>
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
          $Lat = $xml->result->geometry->location->lat;
          $Lon = $xml->result->geometry->location->lng;
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

  return "<?php echo str_replace(",",", <br />",urldecode($Address));?>";
}

function initialize() {
  var mapOptions = {
    zoom: 15,
    center: chicago
  };

  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

  var coordInfoWindow = new google.maps.InfoWindow();
  coordInfoWindow.setContent(createInfoWindowContent());
  coordInfoWindow.setPosition(chicago);
  coordInfoWindow.open(map);

  google.maps.event.addListener(map, 'zoom_changed', function() {
    coordInfoWindow.setContent(createInfoWindowContent());
    coordInfoWindow.open(map);
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
									
							</script>
-->                          <!--<div id="map" style="width:323px; height:325px; font-family: arial; font-size: 12px; color: #313E61; text-align: center; background-color:#FFFFFF;"></div>-->    
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
			
                        <input type="hidden" name="tick" id="tick" value="" />
                        <input type="hidden" name="tot" id="tot" value="" />
			<input type="hidden" name="fun" id="fun" value="" />
			<input type="hidden" name="is_multi" id="is_multi" value="<?php echo $objEvent->f('identical_function');?>" />
						<?php 
						if($_REQUEST['sub_id'] == ''){?>
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
                                    
                                    <div class="dropdown25"><select name="ticket_num" id="ticket_num<?php echo $count;?>" onchange="addtocart(<?php echo $count;?>,<?php echo $obj_ticket->f('price_mx');?>,<?php echo $obj_ticket->f('price_us');?>,<?php echo $obj_ticket->f('ticket_id');?>,<?php echo $event_id;?>);">
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
											echo '<tr><td width="10%">Reservación para este evento ha vencido</td></tr>';
										}
									}
								}

							  ?>
                                  
                          </table>
                          </div>
                          <?php  
						  	if($obj_ticket->num_rows()){?>
                              <div class="select_box2">
                             <?php if($_SESSION['langSessId']=='eng') {?>
                                <div><a href="javascript:void(0);" onclick="save();"><img src="<?php echo $obj_base_path->base_path(); ?>/images/reserv_btn.gif" /></a></div>
                             <?php } else {?>   
                                <div><a href="javascript:void(0);" onclick="save();"><img src="<?php echo $obj_base_path->base_path(); ?>/images/spainreser_btn.gif" /></a></div>
                             <?php } ?>  
                                <!--<div class="icon_link">
                                    <ul>
                                        <li><input type="radio" name="same" value="standard" onclick="save('standard');" /><a href="javascript:void(0);"><img src="<?php echo $obj_base_path->base_path(); ?>/images/icon1.gif" border="0" /></a></li>
                                        <li><input type="radio" name="same" value="pro" onclick="save('pro');" /><a href="javascript:void(0);"><img src="<?php echo $obj_base_path->base_path(); ?>/images/icon2.gif" border="0" /></a></li>
                                        <li><input type="radio" name="same" value="transfer" onclick="save('transfer');" /><a href="javascript:void(0);"><img src="<?php echo $obj_base_path->base_path(); ?>/images/icon4.gif" border="0" /></a></li>
                                        <li><input type="radio" name="same" value="oxxo" onclick="save('oxxo');" /><a href="javascript:void(0);"><img src="<?php echo $obj_base_path->base_path(); ?>/images/icon5.gif" border="0" /></a></li>
                                    </ul>
                                </div>-->
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
                              <tr>
                                <!--<td style="padding: 0;"><div class="like_textbox bg1"><input name="" type="text" value="104" /></div></td>
                                <td><div class="like_textbox bg2"><input name="" type="text" value="75" /></div></td>
                                <td><div class="like_textbox bg3"><input name="" type="text" value="4" /></div></td>
                                <td><div class="like_textbox bg4"><input name="" type="text" value="2" /></div></td>
                                <td><div class="like_textbox bg5"><input name="" type="text" value="2054" /></div></td>
                              </tr>-->
                              <tr>
                                <!--<td style="padding: 0;"><a href="#"><img src="<?php echo $obj_base_path->base_path(); ?>/images/like_img1.gif" border="0" /></a></td>
                                <td><a href="#"><img src="<?php echo $obj_base_path->base_path(); ?>/images/like_img2.gif" border="0" /></a></td>
                                <td><a href="#"><img src="<?php echo $obj_base_path->base_path(); ?>/images/like_img3.gif" border="0" /></a></td>
                                <td><a href="#"><img src="<?php echo $obj_base_path->base_path(); ?>/images/like_img4.gif" border="0" /></a></td>
                                <td><a href="#"><img src="<?php echo $obj_base_path->base_path(); ?>/images/like_img5.gif" border="0" /></a></td>-->
				<!-- AddThis Button BEGIN -->
				
				
				<!--<div class="addthis_toolbox addthis_counter_style" style="left:50px;top:50px;">
				<a class="addthis_button_facebook_like" fb:like:layout="box_count"></a>
				
				<a class="addthis_button_tweet" tw:count="vertical"></a>
				<a class="addthis_button_google_plusone" g:plusone:size="tall"></a>
				<a class="addthis_counter"></a>
				</div>-->
				
<div style="margin: 4px;float:left;padding: 5px;">

<?php $url="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=149448255219243";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="fb-share-button" data-href="<?php echo $url;?>" data-type="box_count"></div>

<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo $url;?>" data-via="your_screen_name" data-lang="en" data-related="anywhereTheJavascriptAPI" data-count="vertical">Tweet</a>

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
                              </tr>
                          </table>	
						</div>
						<div class="clear"></div>		
                        <div class="offer_box" style="float: left; margin: 0; width:100%;">                       
                       	 <div class="preview_imgbox" style="float: left; width: 100%;">
                         <div class="imgbox" style="width:100%; height: auto;">
                            <ul>
                           <!-- <li><img src="<?php echo $obj_base_path->base_path(); ?>/images/preview_img1.gif" border="0" /></li>-->
                         	  
                            <?php if($objEvent->f('event_photo')){  

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
                            <?php } 
								
							  ?>
                            </ul>
                         </div>
                        </div>
					  </div>				
					  <div class="clear"></div>		
                      </div>
                    </div>
                    <div class="clear"></div>
                    <div class="show_box"> 
					                    
                      <div class="leftbox">
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
            </div>

            
</body>
</html>