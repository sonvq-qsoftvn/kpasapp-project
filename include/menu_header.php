<?php

$objblog=new user;
$objblog->max_blog('blog');
$objblog->next_record();
//echo "b_id=".$objblog->f('blog_id');
/*-----------FOR URL CHANGE OF BOLG-------------*/
$old_pattern = array("/[^a-zA-Z0-9]/", "/_+/", "/_$/");
$new_pattern = array("_", "-", "");
//echo "he= ".strtolower(preg_replace($old_pattern, $new_pattern , $objblog->f('page_name')));

?>
<script>
function checklogged(num)
{
	//alert("ss<?php echo $_SESSION['ses_admin_id'];?>");
	<?php if($_SESSION['ses_admin_id']==""){ ?>
			$('html, body').animate({scrollTop: parseInt($("#text_email").offset().top) - 130}, 2000);
			$('#email_cell').focus();
			<?php if($_SESSION['langSessId']=='eng'){?>
				$('#showerr').html("<p style='font-size:13px; color:red; text-align:left;font-weight:bold;'>You must sign in to access this page</p>");
			<?php } else {?>
				$('#showerr').html("<p style='font-size:13px; color:red; text-align:left;font-weight:bold;'>Debes estar registrado para acceder a esta pÃƒÂ¡gina</p>");
			<?php } ?>
				$('#email_cell').css("border","1px solid red");
				$('#showloginIcon').show();
				//setTimeout('$("#showloginIcon").hide()',5000);
	<?php } else{
		$obj_head = new user;
		$obj_head->getAdminById($_SESSION['ses_admin_id']);
		$obj_head->next_record();
		if($obj_head->f('account_type')==0){
			$url = $obj_base_path->base_path()."/userprofile";
		}
		else{
			$url = $obj_base_path->base_path()."/professional_userprofile";
		}
	?>
	if(num==1)
		window.location = "<?php echo $obj_base_path->base_path(); ?>/savedevents";
	if(num==2)
		window.location = "<?php echo $url;?>";
		
	<?php } ?>
}



function checklogged_2(num)
{
	//alert("ss<?php echo $_SESSION['ses_admin_id'];?>");
	<?php if($_SESSION['ses_admin_id']==""){ ?>
            window.location = "<?php echo $obj_base_path->base_path()?>/admin/";
            return false;			
	<?php
	}
	else{
		//$obj_head = new user;
		//$obj_head->getAdminById($_SESSION['ses_admin_id']);
		//$obj_head->next_record();
		//if($obj_head->f('account_type')==0){
	?>
		//var r = confirm("Please upgrade to professional profile.");
		//if(r === true)
		//{
		//		window.location = "<?php echo $obj_base_path->base_path()?>/professional_userprofile";
		//		return false;
		//		
		//}
		//else
		//{
		//		<?php		
		//		$_SESSION['usernm'] = $obj_head->f('username');
		//		$_SESSION['ses_user_id'] = $obj_head->f('admin_id');
		//		$_SESSION['ses_organization_id'] = $obj_head->f('organization_id');
		//		$_SESSION['ses_admin_seller_type'] = $obj_head->f('seller_type');
		//		?>
		//		window.location = "<?php echo $obj_base_path->base_path()?>/admin/events";
		//		return false;
		//}
		
	<?php
		//}
		//else
		//{
	?>
		window.location = "<?php echo $obj_base_path->base_path()?>/admin/events";
		return false;
	<?php
		//}
	}
	?>

}

/*For Bookings*/
function checklogged_3(num)
{
	//alert("ss<?php echo $_SESSION['ses_admin_id'];?>");
	<?php if($_SESSION['ses_admin_id']==""){ ?>
			$('html, body').animate({scrollTop: parseInt($("#text_email").offset().top) - 130}, 2000);
			$('#email_cell').focus();
			<?php if($_SESSION['langSessId']=='eng'){?>
				$('#showerr').html("<p style='font-size:13px; color:red; text-align:left;font-weight:bold;'>You must sign in to access this page</p>");
			<?php } else {?>
				$('#showerr').html("<p style='font-size:13px; color:red; text-align:left;font-weight:bold;'>Debes estar registrado para acceder a esta pÃƒÂ¡gina</p>");
			<?php } ?>
				$('#email_cell').css("border","1px solid red");
				$('#showloginIcon').show();
				//setTimeout('$("#showloginIcon").hide()',5000);
	<?php } else{
		$obj_head = new user;
		$obj_head->getAdminById($_SESSION['ses_admin_id']);
		$obj_head->next_record();
		if($obj_head->f('account_type')==0){
			$url = $obj_base_path->base_path()."/userprofile";
		}
		else{
			$url = $obj_base_path->base_path()."/professional_userprofile";
		}
	?>
	if(num==1)
		window.location = "<?php echo $obj_base_path->base_path(); ?>/bookings";
	if(num==2)
		window.location = "<?php echo $url;?>";
		
	<?php } ?>
}


</script>
<?php 
if($_SESSION['ses_page_name']=="userprofile.php" || $_SESSION['ses_page_name']=="personal_preference.php" || $_SESSION['ses_page_name']=="confirm_email.php" || $_SESSION['ses_page_name']=="professional_userprofile.php" || $_SESSION['ses_page_name']=="professional_preference.php" || $_SESSION['ses_page_name']=="professional_payment.php"){
	$class_active = 'class="active"';
}
?>
<div id="back_header_box">
  <div class="back_header">
    <div class="back_header_top" >
        <div class="logo"><a href="<?php echo $obj_base_path->base_path(); ?>"><img alt="<?php echo LOGO_TITLE; ?>" src="<?php echo $obj_base_path->base_path(); ?>/images/KPasapp_logo.png" border="0" /></a></div>
        <div class="back_right_part">
            <div class="righttxt"><?=LOGO_TITLE;?></div>
        </div>
    </div>
    <div class="back_navigation_bar">
    <div class="back_navigation">
        <ul>
            <li><a href="<?php echo $obj_base_path->base_path(); ?>" <?php if($_SESSION['ses_page_name']=="index.php" || $_SESSION['ses_page_name']=="event.php") { ?>class="active" <?php } ?>><span class="nav_arrow1"><img alt="Kpasapp question mark" src="<?php echo $obj_base_path->base_path(); ?>/images/ques_mark.png" border="0" align="absmiddle" class="question_mark"/><img alt="Kpasapp question mark black" class="question_mark_black" src="<?php echo $obj_base_path->base_path(); ?>/images/ques_mark_black.png" border="0" align="absmiddle"/></span><strong>KPasapp?</strong><!--<span class="nav_arrow"><img src="<?php echo $obj_base_path->base_path(); ?>/images/event_navarrow.png" border="0" /></span>--></a></li>
            <li><div class="back_nav_devider">&nbsp;</div></li>
          
	    <li><a href="javascript:void(0);" onclick="checklogged_3(1)" <?php if($_SESSION['ses_page_name']=="bookings.php"){ ?>class="active" <?php } ?>><?=TAB_MY_BOOKINGS?></a></li>
            <li><div class="back_nav_devider">&nbsp;</div></li>
            
           <?php /*?> <li><a href="<?php if($_SESSION['ses_admin_id']!=""){ echo $obj_base_path->base_path().'/savedevents';} else echo '#'; ?>"><?=TAB_MY_SAVED_EVENTS?></a></li><?php */?>
            <li><a href="javascript:void(0);" onclick="checklogged(1)" <?php if($_SESSION['ses_page_name']=="savedevents.php"){ ?>class="active" <?php } ?>><?=TAB_MY_SAVED_EVENTS?></a></li>
            
            <li><div class="back_nav_devider">&nbsp;</div></li>
            <li><a href="javascript:void(0);" onclick="checklogged(2)"  <?php  echo $class_active; ?>><?=TAB_MY_KPASSAPP;?></a></li>
            <li><div class="back_nav_devider">&nbsp;</div></li>
	    
            <li><a href="<?php if($_SESSION['langSessId']=='eng') { echo $obj_base_path->base_path(); ?>/en/blog/<?php echo $objblog->f('page_id');?>/<?php echo strtolower(preg_replace($old_pattern, $new_pattern , $objblog->f('page_name')));} else {echo $obj_base_path->base_path(); ?>/es/blog/<?php echo $objblog->f('page_id');?>/<?php echo strtolower(preg_replace($old_pattern, $new_pattern , $objblog->f('title_sp')));}?> " <?php if($_SESSION['ses_page_name']=="blog.php") {?> class="active" <?php } ?>><?=TAB_MY_APPS?></a></li>
	    
            <li><div class="back_nav_devider">&nbsp;</div></li>
            <li><a class="align-left-mobile" href="javascript:void(0);" onclick="checklogged_2(2)" style="text-align: center; vertical-align: top; line-height: 20px;"><?=TAB_ANNOUNCE_EVENTS?></a></li>
            
            <!-- additional menu -->
            <li class="hidden-desktop"><a href="<?php  if($_SESSION['langSessId']=='eng') { echo $obj_base_path->base_path(); ?>/en/about-kpasapp/ <?php } else {echo $obj_base_path->base_path(); ?>/es/acerca-de-kpasapp/ <?php }?> "><?=ABOUT_KCPASA?></a></li>                                   
            <li class="hidden-desktop"><a href="<?php  if($_SESSION['langSessId']=='eng') { echo $obj_base_path->base_path(); ?>/en/about-baja-sur/ <?php } else {echo $obj_base_path->base_path(); ?>/es/acerca-de-baja-california-sur/ <?php }?> "><?=ABOUT_BAJASUR?></a></li>
            <li class="hidden-desktop"><a href="<?php  if($_SESSION['langSessId']=='eng') { echo $obj_base_path->base_path(); ?>/en/news/ <?php } else {echo $obj_base_path->base_path(); ?>/es/news/ <?php }?> "><?=WHATS_UP?></a></li>
            <li class="hidden-desktop"><a href="<?php  if($_SESSION['langSessId']=='eng') { echo $obj_base_path->base_path(); ?>/en/resources/ <?php } else {echo $obj_base_path->base_path(); ?>/es/resources/ <?php }?> "><?=RESOURCES?></a></li>
        </ul>
    </div>    
    </div>
 </div>
</div>