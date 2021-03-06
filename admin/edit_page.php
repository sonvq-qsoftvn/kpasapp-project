<?php

session_start();
// Edit Page
// -------- include file -------------
//include('../include/admin_inc.php');
include("../class/db_mysql.inc");
include("../class/admin_class.php");
include("../class/event_class.php");
include("../class/merchant_admin_class.php");
include("../class/duplicate_event_class.php");
include("../class/pagination.class.php");
include('../class/pagination_search.class.php');
include('../class/user_class.php');
//include("../class/class.phpmailer.php");
//include("../class/class.smtp.php");
$obj_base_path = new DB_Sql;
$obj=new user;
include("../include/session_admin.inc.php");
include_once '../ckeditor/ckeditor.php';
include_once '../ckfinder/ckfinder.php';

//creation of objects
$objlist=new merchant_admin;
$objedit = new merchant_admin;
$obj = new merchant_admin;
	
$page_id=$_REQUEST['page_id'];

if(isset($_REQUEST['submit1'])) {
    // post value
    $social=$_POST['social'];
    $path=$_POST['path'];
    $page_name=addslashes($_POST['title']);
    $title_sp=addslashes($_POST['title_sp']);
    $page_content=addslashes($_POST['page_content']);
    $page_content_sp=addslashes($_POST['page_content_sp']);
    $page_link=$_POST['page_link'];
    $file_name = $_POST['event_photo'];
    $publish=$_POST['publish'];
			
    if($page_id) {
        // -- Edit Content --		
        $objedit->edit_page($page_name,$title_sp,$page_content,$page_content_sp,$page_id,$page_link,$social,$path,$file_name,$publish);
    }
		  
?>
    <script language="javascript">
        window.location="<?php echo $obj_base_path->base_path(); ?>/admin/list_page.php?err=2";
    </script>

<?php
}
// -- SELECT CONTENT BY CONTENT ID --
$obj->showpage_by_id($page_id);
$obj->next_record();
$actual_link = "http://www.phppowerhousedemo.com/webroot/team5/kpasapp/blog/".$obj->f('page_link')."";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta property="og:locale" content="en_US" />
<meta property="og:type" content="article" />
<meta property="og:title" content="<?php echo $obj->f('page_name')?>" />
<meta property='og:site_name' content='Kpasapp' />
<meta name="title" content="<?php echo $obj->f('page_name')?>" />
<meta property="og:url" content="<?php echo $actual_link;?>" />
<meta property="og:description" content="<?php //echo $obj->f('page_content');?>" />
<meta property="og:image" content="<?php echo $obj_base_path->base_path(); ?>/images/kpassa_logo_fb.png">
<title>Kcpasa - Edit Page</title>
<link href="<?php echo $obj_base_path->base_path(); ?>/css/base.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/header.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/style_event.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/jquery1-ui-1.8.14.custom.css" rel="stylesheet" type="text/css" />

<!-- Ajax File Upload -->
<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/js/ajaxupload.3.5.js" ></script>
<!-- Ajax File Upload -->
<script src="<?php echo $obj_base_path->base_path(); ?>/css/SpryAssets/SpryTabbedPanels.js" type="text/javascript"></script>
<link href="<?php echo $obj_base_path->base_path(); ?>/css/SpryAssets/SpryTabbedPanels.css" rel="stylesheet" type="text/css"/>

<script src="<?php echo $obj_base_path->base_path(); ?>/css/SpryAssets2/SpryTabbedPanels.js" type="text/javascript"></script>
<link href="<?php echo $obj_base_path->base_path(); ?>/css/SpryAssets2/SpryTabbedPanels.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/js/custom-form-elements.js"></script>
<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/js/jquery-ui.min.js"></script>
<script src="<?php echo $obj_base_path->base_path(); ?>/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>


<!-- jQuery lightBox plugin -->
<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/include/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<?php echo $obj_base_path->base_path(); ?>/include/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $obj_base_path->base_path(); ?>/include/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<!--jquery tooltips -->
<script src="<?php echo $obj_base_path->base_path(); ?>/js/jquery.tipsy.js" type="text/javascript"></script>
<!--jquery tooltips -->

<script type="text/javascript">

function check()
{
    if(document.frm.title.value=="")
    {
        alert("Please enter Page Title");
        document.frm.title.focus();
        return false;
    }

    if(CKEDITOR.instances['page_content'].getData()=="")
    {
        alert("Please Enter page content");
        CKEDITOR.instances['page_content'].focus();
        return false;
    }

    if(document.frm.page_link.value=="")
    {
        alert("Please enter Page Title");
        document.frm.page_link.focus();
        return false;
    }

    return true;
}

$(function(){
    var btnUpload=$('#me1');
    var mestatus=$('#mestatus1');
    var files=$('#files');
    new AjaxUpload(btnUpload, {
        action: '<?php echo $obj_base_path->base_path(); ?>/admin/uploadPhotoEdit.php',
        name: 'uploadfile',
        onSubmit: function(file, ext){
            if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                mestatus.text('Only JPG, PNG or GIF files are allowed');
                return false;
            }
            mestatus.html('<img src="ajax-loader.gif" height="16" width="16">');
        },
        onComplete: function(file, response){
            //On completion clear the status
            mestatus.text('Photo Uploaded Sucessfully!');
            $('#event_photo').val(response);
            $('#imgshow').html('<img src="<?php echo $obj_base_path->base_path(); ?>/files/event/thumb/'+response+'" alt="" />');
            $('#me1').html('');

            //On completion clear the status
        }
    });		
});


</script>

<link href="<?php echo $obj_base_path->base_path(); ?>/css/base.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/header.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/style.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/style_event.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $obj_base_path->base_path(); ?>/css/jquery1-ui-1.8.14.custom.css" rel="stylesheet" type="text/css" />

<?php include("../include/analyticstracking.php")?>

</head>

<body class="body1">
    <?php include("admin_header.php"); ?>
    <div id="maindiv">
        <div class="clear"></div>
        <div class="body_bg">
            <div class="clear"></div>
            <div class="container">
                <?php include("admin_header_menu.php");?>
                <div class="clear"></div>		
                <!--start body-->
                <div id="body">
                    <div class="body2"> 
                        <div class="clear"></div>
                        <div class="blue_box1">
                            <div class="blue_box10"><p>Page</p></div>
                            <?php include("admin_menu/list_page_menu.php");?>
                       </div> 
                        <div class="clear"></div>
                    </div>	
                </div>
            </div>
            <!---------------------put your div--here-------------------------------------------------- --> 
        
            <div style="font-family:Arial, Helvetica, sans-serif; padding-left:10px; color:#006600;">
                <strong>
                    <?php  if($_SESSION['msg']){ echo $_SESSION['msg']; $_SESSION['msg'] = ''; } ?>
                </strong>
            </div>
            <?php if($msg!=""){ ?>
                <div style="font-family:Arial, Helvetica, sans-serif; padding-left:10px; color:#FF0000;">
                    <strong>
                        <?php echo $msg;?>		
                    </strong>
                </div>	
            <?php } ?>	
		
            <div class="myevent_box">   
                <div class="sct_right">										
                    <fieldset>                
                        <div class="table_wrapper">
                            <div class="table_wrapper_inner">
                                <table width="100%" border=0 align="center" cellpadding="0" cellspacing="0">
                                    <form name="frm" method="post" action="edit_page.php" onSubmit="return check()" enctype="multipart/form-data">
                                        <input type="hidden" name="page_id" value="<?php echo $page_id  ?>" />
                                        <?php
                                            // -- SELECT CONTENT BY CONTENT ID --
                                            $objlist->showpage_by_id($page_id);
                                            if($objlist->num_rows() > 0)
                                            {
                                                $objlist->next_record();
                                        ?>								
                                                <tr>
                                                    <td width="13%" style="font: normal 12px/18px Arial, Helvetica, sans-serif; padding: 0;">Path ::</td>
                                                    <td width="87%"><input type="radio" name="path" id="path" value="blog" <?php if($objlist->f('path')=='blog'){ echo "checked";}?>> blog<br>
                                                        <input type="radio" name="path" id="path" value="about_kp" <?php if($objlist->f('path')=='about_kp'){ echo "checked";}?>> about_kp<br>
                                                        <input type="radio" name="path" id="path" value="about_baja_sur" <?php if($objlist->f('path')=='about_baja_sur'){ echo "checked";}?>> about_baja_sur<br>
                                                        <input type="radio" name="path" id="path" value="news" <?php if($objlist->f('path')=='news'){ echo "checked";}?>> news<br>
                                                        <input type="radio" name="path" id="path" value="resources" <?php if($objlist->f('path')=='resources'){ echo "checked";}?>> resources<br>
                                                        <input type="radio" name="path" id="path" value="terms_condition" <?php if($objlist->f('path')=='terms_condition'){ echo "checked";}?>> terms & condition<br>
                                                        <input type="radio" name="path" id="path" value="contact_us" <?php if($objlist->f('path')=='contact_us'){ echo "checked";}?>> Contact Us<br>
                                                        <input type="radio" name="path" id="path" value="other" <?php if($objlist->f('path')=='other'){ echo "checked";}?>> other<br>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td width="13%" style="font: normal 12px/18px Arial, Helvetica, sans-serif; padding: 0;">Social Share ::</td>
                                                    <td width="87%"><input type="checkbox" name="social" id="social" value="1" <?php if($objlist->f('social')==1){ echo "checked";}?>></td>
                                                </tr>
                                                <tr>
                                                    <td width="13%" style="font: normal 12px/18px Arial, Helvetica, sans-serif; padding: 0;">Page Title <span class="state_en"> EN </span>::</td>
                                                    <td width="87%"><input type="text" name="title" style="height: 24px; margin-top:" class="event_field8" value="<?php echo $objlist->f('page_name')?>"></td>
                                                </tr>
                                                <tr>
                                                    <td width="13%" style="font: normal 12px/18px Arial, Helvetica, sans-serif; padding: 0;">Page Title <span class="state_en"> SP </span>::</td>
                                                    <td width="87%"><input type="text" name="title_sp" style="height: 24px;" class="event_field8" value="<?php echo $objlist->f('title_sp')?>"></td>
                                                </tr>
                                                <tr>
                                                    <td  align="right" height="4"></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td align="left" valign="top" style="font: normal 12px/18px Arial, Helvetica, sans-serif;">Description ::</td>
                                                    <td>
                                                        <div id="TabbedPanels2" class="TabbedPanels2" style="float: left;">
                                                            <ul class="TabbedPanelsTabGroup2">
                                                                <li class="TabbedPanelsTab2" tabindex="1">Espanol</li>  
                                                                <li class="TabbedPanelsTab2" tabindex="0">English</li> 
                                                                <img src="<?php echo $obj_base_path->base_path(); ?>/images/globe1.jpg" width="38" height="38" border="0" style="float:right; margin:0 10px 0 0;"/>	 
                                                            </ul>
                                                            <div class="TabbedPanelsContentGroup2">
                                                                <div class="TabbedPanelsContent2">
                                                                    <?php 
                                                                        //include_once($obj_base_path->base_path()."/ckeditor/ckeditor.php");
                                                                       $CKeditor = new CKeditor();
                                                                       $CKeditor->BasePath = 'ckeditor/';
                                                                       $CKeditor->editor('page_content_sp', stripslashes($objlist->f('page_content_sp')));
                                                                    ?>
                                                                </div> 
                                                                <div class="TabbedPanelsContent2">
                                                                    <?php 
                                                                    //include($obj_base_path->base_path()."/ckeditor/ckeditor.php");
                                                                       $CKeditor = new CKeditor();
                                                                       $CKeditor->BasePath = 'ckeditor/';
                                                                       $CKeditor->editor('page_content', stripslashes($objlist->f('page_content')));
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>			  
                                                <tr>
                                                    <td style="font: normal 12px/18px Arial, Helvetica, sans-serif;">Image ::</td>
                                                    <td valign="top">
                                                        <div class="event_ticket">
                                                            <h1>Set feature image <img src="<?php echo $obj_base_path->base_path(); ?>/images/question_mark.gif" alt="" width="31" height="28" border="0"/></h1>
                                                            <ul style="margin-left: 10px;">
                                                                <li>
                                                                    <a href="#" class="here"> 
                                                                        <div id="me1" class="styleall" style=" cursor:pointer; ">
                                                                            <span style=" cursor:pointer; font-family:Verdana, Geneva, sans-serif; font-size:9px;"><span style=" cursor:pointer;">Upload Files</span></span>
                                                                        </div>
                                                                        <span id="mestatus1"></span>
                                                                        <div class="clear"></div>
                                                                        <span id="imgshow">
                                                                            <?php if($objlist->f('photo')){?>
                                                                                <img src="<?php echo $obj_base_path->base_path(); ?>/files/event/thumb/<?php echo $objlist->f('photo'); ?>" alt="" />
                                                                            <?php } ?>
                                                                        </span>
                                                                        <input type="hidden" name="event_photo" id="event_photo" value="<?php if($objlist->f('photo')){ echo $objlist->f('photo'); }?>" />
                                                                    </a>
                                                                </li>
                                                                <li>|</li>
                                                                <li><a href="#">Media Library</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>		
                                                <tr>
                                                    <td width="13%" style="font: normal 12px/18px Arial, Helvetica, sans-serif; padding: 0;">Publish ::</td>
                                                    <td width="87%"><input type="checkbox" name="publish" id="publish" value="1" <?php if($objlist->f('publish')==1){?> checked="checked"<?php } ?>></td>
                                                </tr>   			  
                                                <tr>
                                                    <td style="font: normal 12px/18px Arial, Helvetica, sans-serif; padding: 8px 0 0 0;"><!--Page Link::--></td>
                                                    <td><input type="hidden" name="page_link" class="event_field8" style="height: 24px;"  value="<?php echo $objlist->f('page_link')  ?>"></td>
                                                </tr>				
                                                <tr>
                                                    <td>&nbsp;</td>
                                                    <td><input type="submit" name="submit1" class="createbtn" value="Save"></td>
                                                </tr>
                                        <?php } ?>
                                    </form>
                                </table>
                            </div>
                        </div>
                    </fieldset>									
                </div>    
                <div class="clear"></div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
    <!------------------------end maindiv----------------------------------------------- -->
    <?php include("admin_footer.php"); ?>

    <script type="text/javascript">
        var TabbedPanels2 = new Spry.Widget.TabbedPanels("TabbedPanels2" , {defaultTab:0});
    </script>
</body>
</html>
