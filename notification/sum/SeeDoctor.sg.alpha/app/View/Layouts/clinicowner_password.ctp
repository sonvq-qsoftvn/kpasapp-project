<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <title><?php echo $title_for_layout;?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $meta_description_content;?>">
    <meta name="author" content="Unified Infotech Pvt. Ltd.">

    <!-- styles -->
	<?php
	echo $this->Html->css(array('../admin/css/utopia-white','../admin/css/utopia-responsive','../admin/css/validationEngine.jquery'));
	?>
    

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
	<?php
	echo $this->Html->script('../clinicowner/js/hml5');
	?>
   
    <![endif]-->
	<?php
	echo $this->Html->script('../admin/js/jquery.min');
	echo $this->Html->script('../admin/js/jquery.cookie');
	?>
   
    <script type="text/javascript">
       
        $(document).ready(function() {
            $(".theme-changer a").live('click', function() {
                $('link[href*="utopia-white.css"]').attr("href",$(this).attr('rel'));
                $('link[href*="utopia-dark.css"]').attr("href",$(this).attr('rel'));
                $('link[href*="utopia-wooden.css"]').attr("href",$(this).attr('rel'));
                $.cookie("css",$(this).attr('rel'), {expires: 365, path: '/'});
                $('.user-info').removeClass('user-active');
                $('.user-dropbox').hide();
            });
        });
    </script>

</head>

<body>

<div class="container-fluid">

    <div class="row-fluid">
        <div class="span12">
            <div class="utopia-login-message">
                <h1>Welcome to SeeDoctor.sg Forgot Password</h1>
                <p>Fill the form to reset your password</p>
            </div>
        </div>
    </div>
<h2 style='color:tomato;font-weight:bolder;margin-left:51%;'><?php echo $msg;?></h2>
    <div class="row-fluid">

        <div class="span12">

            <div class="row-fluid">

                <div class="span6">
		    
                    <div class="utopia-login-info">
			
					<?php
					echo $this->Html->image('../admin/img/login.png',array('alt'=>'image'));
					?>
                       
                    </div>

                </div>

                <?php
				echo $content_for_layout ;
				?>

            </div>

        </div>
    </div>
</div> <!-- end of container -->

<!-- javascript placed at the end of the document so the pages load faster -->
<?php
echo $this->Html->script('../admin/js/utopia');
echo $this->Html->script('../admin/js/jquery.validationEngine');
echo $this->Html->script('../admin/js/jquery.validationEngine-en');
?>

<script type="text/javascript">
    jQuery(function(){
        jQuery(".utopia").validationEngine('attach', {promptPosition : "topLeft", scroll: false});
    })
</script>
</body>


</html>
