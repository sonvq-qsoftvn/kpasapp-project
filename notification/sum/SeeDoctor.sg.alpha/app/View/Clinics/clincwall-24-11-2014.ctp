<?php
	/* 
	 * To change this license header, choose License Headers in Project Properties.
	 * To change this template file, choose Tools | Templates
	 * and open the template in the editor.
	 */
	//echo "<pre>";
	//echo "luid= ".$this->Session->read('reid_user_uid');
	//print_r($wall_post);
	//echo "c=".count($wall_post);
	//for($i=0;$i<count($wall_post);$i++)
	//{
	//	echo $i;
	//	echo ucwords($wall_post[$i]['Wallpost']['alias_fname']);
	//}
	//
	//echo $wall_post[0]['Wallpost']['id'];
	//exit;
	
	//echo "cl= ".$client_all_detail[0]['Clinic']['clinicmanagersid'];
	//echo "uid= ".$this->Session->read('reid_user_uid');
	//pr($comments);exit;

?>


<?php if($this->Session->flash()) { ?>	
	<script>
	$(document).ready(function(){$('html, body').animate({scrollTop: $(".pro_comment_text").offset().top}, 2000);});
	$(document).ready(function(){$('html, body').animate({scrollTop: $(".page_top_success").offset().top}, 2000);});
	</script>
<?php } ?>
	

	<script type="text/javascript">
		function unlike(id)
		{
			$.ajax({
				url:'<?php echo BASE_URL ?>clinics/ajx_unlike',
				type:'post',
				data: 'id='+id,
				complete:function(data1){
					window.location.reload(true);
				}
			});
		}
		
		function like(id)
		{
			$.ajax({
				url:'<?php echo BASE_URL ?>clinics/ajx_like',
				type:'post',
				data: 'client_id='+id,
				complete:function(data1){
                         window.location.reload(true);
                    }
			});
		}
                
		function hide_img(id)
		{
			$('#div_'+id).show();
		}
        
		function show_img(id)
		{
             $('#div_'+id).hide();     
		}    
	</script>

<script>
function comment_del(CommentId,ClinicId)
{
		
	$.ajax({
	    url : '<?php echo BASE_URL;?>clinics/delete_comment',
	    type: "POST",
	    data : {comment_id:CommentId,clinic_id:ClinicId},
	    success: function(data)
	    {
		//alert(data);
		$('#comment_'+CommentId).fadeOut(1500)
		$('#comment_action_'+CommentId).fadeOut(1500)
		//$('#comment_msg_'+CommentId).html(data)
		setTimeout(function() { $('#comment_msg_'+CommentId).fadeOut(2000); }) 
			
	    },
	    error: function (jqXHR, textStatus, errorThrown)
	    {
	 
	    }
	});
}
</script>
<script>
function comment_edit(CommentId,Post_id)
	{
		
		$("#comment_id_"+Post_id).val(CommentId);
		$("#comment_text_"+Post_id).val($("#pcomment_"+CommentId).html());
	}

</script>

<?php
if($my_comment_id!='')
{
	?>
	<script>
	$(document).ready(function(){
$('html, body').animate({scrollTop: $("#comment_<?php echo $my_comment_id; ?>").offset().top}, 2000);
		});
		</script>
	<?php
	
}
?>
<style>
/*.comment_msg
{
	display: none;
}*/
</style>
	<section class="emai-registration">
		<div class="banner">
			<div class="container pro">
				<div class="orchard-surgery">
					<h1><strong> <?php echo (isset($client_all_detail[0]['Clinic']['name']))?$client_all_detail[0]['Clinic']['name']:'Clinic'; ?></strong></h1>
					<p>
						<?php
							echo (isset($Specialitie_category[0]['Speciality']['specialities_name']))?$Specialitie_category[0]['Speciality']['specialities_name']:'Medical specialty';
							if(isset($Specialitie_sub_category) && !empty($Specialitie_sub_category))
							{ 
								foreach($Specialitie_sub_category as $Specialitie_sub_category){
									echo  $sub_cat_special =', '. $Specialitie_sub_category['Speciality']['specialities_name'];
								}
							} 
						?>
					</p>
					<ul>
						<li>
							<?php echo (isset($client_all_detail[0]['Clinic']['address']))?$client_all_detail[0]['Clinic']['address']:'No address' ?>
						</li>
						<li>
							Opening Hours:
							<br>
							<?php
								$all_opening_keys=array();
								if(!empty($oping_time))
								{
									foreach($oping_time as $key=>$val)
									{
							?>
										<br />
							<?php
							echo $key;
							$all_opening_keys[]=$key; ?> :
							<?php
										for($i=0,$iteration=0;$i<count($val);$i++) 
										{
											if($iteration!=0)
												echo ', ';
											$iteration++;
											echo $sub_cat_special =$val[$i]; 
										}
									}
								}
								else
								{
									echo '<strong>Opening time is not found!</strong>';
							  
								}
							?>
							<br/>
							<?php
								$all_keys=implode(', ',$all_opening_keys);
								$all_opening_keys=explode(', ',$all_keys);
							
								for($i=0,$iteration=0; $i<count($days); $i++)
								{
									if(gettype(array_search($days[$i],$all_opening_keys))!='boolean')
									    continue;
									
									if($iteration!=0)
										echo ', ';
										
									$iteration++;
									echo $days[$i];
									
									if(($i+1) == count($days))
										echo (count($days) > 1)?' are closed':' closed';
								}
								
								if($likes_count>0)
									echo ($likes_count>1)?'<br><br>'.$likes_count.' Likes':'<br><br>'.$likes_count.' Like';
								else
									echo '<br><br>No likes yet';
							?>
						</li>
					</ul>
					<div class="orchard-buttons">
						<a class="book_appointment" href="<?php echo BASE_URL.'appointments/book_appointment/clinic:'.$client_all_detail[0]['Clinic']['id'] ?>">Book Appointment</a>
						<?php
							if($loged_user_type==1)
							{   
								if($count_like_user==0)    
								{
						?>
									<button type="button" class="link" onclick="like('<?php if(isset($client_all_detail[0]['Clinic']['id'])) echo $client_all_detail[0]['Clinic']['id'] ?>')">Like</button>
		
						<?php 
								}
								else
								{
									//echo '<a href="javascript:void(0)" onclick="unlike(\''.($Cliniclike_id?$Cliniclike_id:0).'\')">'.$this->Html->image('../frontend/images/thunbdown.ong.png',array('alt'=>'')).'</a>';
						?>
				<button type="button" class="unlink" onclick="unlike('<?php  echo ($Cliniclike_id)?$Cliniclike_id:0; ?>')">Unlike</button>  
				
					
						<?php
								}
							}
						?>
					</div>
				</div>
				<div class="banner_slider banner_slider_wall">
					<ul class="bxslider">
						<li><?php echo $this->Html->image('../frontend/images/mobile_screen.png',array('alt'=>'')); ?>   </li>
						<li><?php echo $this->Html->image('../frontend/images/mobile_screen.png',array('alt'=>'')); ?>   </li>
						<li><?php echo $this->Html->image('../frontend/images/mobile_screen.png',array('alt'=>'')); ?>   </li>
						<li><?php echo $this->Html->image('../frontend/images/mobile_screen.png',array('alt'=>'')); ?>   </li>
					</ul>
				</div>
			</div>
		</div>
		<div class="seedoctor_orchardsurg">
			<div class="container">
				<div class="seedoctor_orchardsurg_title">
					<h2>
						<?php echo (isset($client_all_detail[0]['Clinic']['url']))?'<a href="'.$client_all_detail[0]['Clinic']['url'].'">'.$client_all_detail[0]['Clinic']['url'].'/<span>orchardsurg</span></a>':'No link yet' ?>
					</h2>
			</div>
			<div class="seedoctor_orchardsurg_para">
				<?php
					echo (isset($client_all_detail[0]['Clinic']['logo']))?$this->Html->image('../admin/uploads/thumb/'.$client_all_detail[0]['Clinic']['logo'],array('alt'=>'')):$this->Html->image('../frontend/images/na.jpg',array('alt'=>''));
				?>
				<br />
				<p><?php if(isset($client_all_detail[0]['Clinic']['about'])) echo $client_all_detail[0]['Clinic']['about'] ?></p>
				<br />
				<p class="gray">We are the best clinic in Singapore</p>
			</div>
			<div class="seedoctor_orchardsurg_items">
				<div class="col-sm-4 col-md-4 appointment_module1">appointment<br>module</div>
				<div class="col-sm-4 col-md-4">
					<div class="see-orc">
						<?php
							$todays_day=date('D');
							//echo '<pre.';
							//print_r($oping_time);
							
							foreach($oping_time as $key=>$val)
							{
								if(gettype(strstr($key, $todays_day))!='boolean')   
								{
									$time_array= $val;
									break;
								}
							}
                                                        
                                                      // pr($time_array);
							
							$time_status=0;
							
							if(!empty($time_array))
							{
								foreach($time_array as $time_arrays)
								{
									$temp_time=explode('-',$time_arrays);
									//pr($temp_time);
									$current_time_date= date('H:i');
									
									if($current_time_date<=$temp_time[1] &&  $current_time_date>=$temp_time[0])
									{
										
										$time_status=1;
										break;
									}
								}  
							}
						?>
						<div class="see-orc-left"> <?php echo $this->Html->image('../frontend/images/icon15.jpg',array('alt'=>'')); ?> </div>
						<div class="see-orc-right">
							<?php
								if($time_status==1)
								{
							?>
									<h2>Currently Open</h2>
									<p>We are open now!</p>
						    <?php
								}
								else
								{
							?>
									<h2>Currently Closed</h2>
									<p>We are Closed now!</p>
						    <?php } ?>
						</div>
					</div>
					<?php
						if(isset($client_all_detail[0]['Clinic']['displaywaiting']) && $client_all_detail[0]['Clinic']['displaywaiting']==1)
						{
					?>
							<div class="see-orc">
								<div class="see-orc-left"><?php echo $this->Html->image('../frontend/images/icon16.jpg',array('alt'=>'')); ?> </div>
								<div class="see-orc-right">
									<h2>Waiting Time</h2>
									<p>
										<?php echo ($client_all_detail[0]['Clinic']['displaywaiting'])?$client_all_detail[0]['Clinic']['displaywaiting']:'Between 0 to 15 minutes.'; ?></p>
								</div>
							</div>
				<?php  	} ?>      
					<div class="see-orc">
						<div class="see-orc-left"> <?php echo $this->Html->image('../frontend/images/icon17.jpg',array('alt'=>'')); ?></div>
						<div class="see-orc-right">
							<h2>Insurance Eligibility</h2>
							<p>
								<?php
									if( !empty($current_insurances))
									{ 
										foreach($current_insurances as $current_insuranc){ 
											echo  $sub_cat_special =$current_insuranc['Insurance']['insurances_name'].',' ;
										}
									} 
								?>
							</p>
						</div>
					</div>
				</div>
				<div class="col-sm-4 col-md-4 appointment_module">appointment<br>module</div>
				<div class="col-sm-4 col-md-4">                
					<div class="see-orc">
						<div class="see-orc-left"><?php echo $this->Html->image('../frontend/images/icon18.jpg',array('alt'=>'')); ?></div>
						<div class="see-orc-right">
							<h2>Contact us</h2>
							<?php
								echo ($user_phone_no)?'<p>Phone: +'.$user_phone_no.'</p>':'No information yet';
								echo ($user_phone_no)?'<button type="" name="">Message Me</button>':'';
							?>
					    </div>
					</div>
					<div class="see-orc">
						<div class="see-orc-left"><?php echo $this->Html->image('../frontend/images/icon19.jpg',array('alt'=>'')); ?></div>
						<div class="see-orc-right">
							<h2>Book an Appointment</h2>
							<p>Hassle-free and no extra charges!</p>
						</div>
					</div>
					<div class="see-orc">
						<div class="see-orc-left"><?php echo $this->Html->image('../frontend/images/icon20.jpg',array('alt'=>'')); ?></div>
						<div class="see-orc-right">
							<h2>Company Eligibility</h2>
							<p>
								<?php
									if( !empty($current_insurances))
									{ 
										$i=0;
										foreach($current_eligibi as $current_eligibis)
										{ 
											if($i!=0)
											    echo ',';
											echo  $sub_cat_special =$current_eligibis['Eligibility']['name'];
											$i++;
										}
									} 
								?>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="wall_map">
	<!--      <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3675.1892630768243!2d-43.18861099999999!3d-22.906388999999997!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x997f69432920f9%3A0x2f63f7fa11c4029c!2sParque+Campo+de+Santana!5e0!3m2!1sen!2sin!4v1411983531391" frameborder="0" style="border:0"></iframe>-->
		<iframe  src="https://maps.google.co.in/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=<?php if(isset($client_all_detail[0]['Clinic']['address'])) echo strip_tags($client_all_detail[0]['Clinic']['address']) ?>&amp;aq=0&amp;oq=kol&amp;&amp;sspn=0.63665,1.352692&amp;ie=UTF8&amp;hq=&amp;hnear=<?php echo (isset($client_all_detail[0]['Clinic']['address']))?strip_tags($client_all_detail[0]['Clinic']['address']):'Singapore'; ?>&amp;&amp;spn=0.63658,1.352692&amp;t=m&amp;z=10&amp;output=embed"></iframe>       
	</div>
	<div class="our_doctors">
		<div class="container">
			<div class="our_doctors_title"><h2>Our Doctors</h2></div>
			<div class="our_doctors_para">
				<p>We are experienced surgeons who have practised in multiple institutions locally and overseas. Our main areas of expertise 
				include: colorectal cancers, screening endoscopy, chronic inflammatory disorders and perianal disease.</p>
			</div>
		 
			<div class="our_doctors_items">
				<?php
					$my_id=0;
					foreach($doctor as $doctors)
					{
				?>
						<div class="col-md-3 col-sm-6" my_id="<?php echo ++$my_id;?>">
							<div class="doctor_pic" onmouseover="hide_img(<?php echo $my_id;?>)">           
								<?php
									if(isset($doctors['Doctor']['img']) && $doctors['Doctor']['img']!="")
										echo $this->Html->image('../admin/uploads/'.$doctors['Doctor']['img'],array('width'=>'233px','height'=>'199px','alt'=>'')); 
									else
										echo $this->Html->image('../frontend/images/na.jpg',array('alt'=>''));
								?>
								<div  id="div_<?php echo $my_id;?>" style='display:none;' onmouseout="show_img('<?php echo $my_id;?>')" >
									<h2><?php echo $doctors['Doctor']['qualification'].''.$doctors['Doctor']['qualification'] ; ?></h2>
									<!--<p>Special interests: <?php echo $doctors['Doctor']['qualification'].''.$doctors['Doctor']['qualification'] ; ?></p>-->
								</div>
							</div>
							<h2>Dr <?php echo $doctors['Doctor']['f_name'].''.$doctors['Doctor']['l_name'] ; ?></h2>
							<p><?php echo $doctors['Doctor']['title']; ?></p>
						</div>
			<?php 	}  ?>  
			</div>
		</div>
	</div>
	
	<div class="reviews">
		<div class="container">
			<div class="reviews_icon">
				<?php echo $this->Html->image('../frontend/images/icon21.jpg',array('alt'=>'')); ?>
			</div>
		<!---Edited By Developer Start--->
		<?php if($this->Session->check('reid_user_logged') && $this->Session->check('reid_user_uid') && ($this->Session->read('reid_user_logged')==1) && ($this->Session->read('reid_user_uid')!='') && (($this->Session->read('reid_user_type')==2 && $client_all_detail[0]['Clinic']['clinicmanagersid']==$this->Session->read('reid_user_uid')) || ($this->Session->read('reid_user_type')==1 && $client_all_detail[0]['Clinic']['allowpost']==1)))
		{?>
		<div class="inner-gapbox-1">
			<div class="orchard-surgery new_padding">
				<div class="orchard-buttons">
			<a class="book_appointment" href="<?php echo BASE_URL;?>clinics/addwallpost/<?php echo $clinic_id;?>" >Add Wall Post</a>
				</div>
			<div class="clearfix"></div>
			</div>
			<!----Showing the error/success message ---->
			<div class="clearfix"></div>
			<span style="padding: 4px;"><?php echo $this->Session->flash('update_error');?></span>
		</div>
		<?php } ?>
		<!---Edited By Developer End--->
			<?php for($i=0;$i<count($wall_post);$i++){ ?>
		
			<div class="reviews_main">
			<div class="reviews_section">
					<div class="reviews_title">
						<!--<div class="pro_pic"><?php // echo $this->Html->image('../frontend/images/pic1.jpg',array('alt'=>'')); ?></div>-->
						<div class="pro_info">
							<h2>
		<?php echo ucwords($wall_post[$i]['Wallpost']['alias_fname'])." ".ucwords($wall_post[$i]['Wallpost']['alias_lname']);?>
							</h2>
							<p><?php echo ucwords($wall_post[$i]['Wallpost']['alias_designation']);?></p>
							<span>
<?php $ret_str=($this->Functions->calculate_time_gap(date('Y-m-d H:i:s'),$wall_post[$i]['Wallpost']['post_modify_time']));if($ret_str['Y']!=0||$ret_str['M']!=0){echo "Nearly ";} if($ret_str['Y']!=0){ echo $ret_str['Y'].' Year'; if($ret_str['Y']>1){echo 's';}}if($ret_str['M']!=0){ echo ' '.$ret_str['M'].' Month'; if($ret_str['M']>1){echo 's';}}if($ret_str['d']!=0){ echo ' '.$ret_str['d'].' Day'; if($ret_str['d']>1){echo 's ';}}if($ret_str['h']!=0){ echo ' '.$ret_str['h'].' Hour'; if($ret_str['h']>1){echo 's ';}}if($ret_str['m']!=0){ echo ' '.$ret_str['m'].' Minute'; if($ret_str['m']>1){echo 's ';}}if($ret_str['Y']!=0||$ret_str['M']!=0||$ret_str['d']!=0||$ret_str['h']!=0||$ret_str['m']!=0){echo ' ago ';}else{echo 'Just Now';}?>
							</span>
						</div>
						<?php if(($wall_post[$i]['Wallpost']['user_id']==$this->Session->read('reid_user_uid')) || ($wall_post[$i]['Clinic']['clinicmanagersid']==$this->Session->read('reid_user_uid'))){?>
	<div class="top_btngroup">
		<a href="<?php  echo BASE_URL;?>clinics/editwallpost/<?php  echo $wall_post[$i]['Wallpost']['id'];?>" class="edit_pst" title="Edit Post"></a>
		<a href="<?php  echo BASE_URL;?>clinics/deletewallpost/<?php  echo $wall_post[$i]['Wallpost']['id'];?>/<?php echo $clinic_id?>" class="del_pst"  title="Delete Post"></a>
	</div>
		<?php } ?>
					</div>
					
					<div class="pro_cont">
						<p><?php echo $wall_post[$i]['Wallpost']['post_main_text'];?></p>
					</div>
<?php if(($wall_post[$i]['Wallpost']['attachment_heading'] !='') || ($wall_post[$i]['Wallpost']['attachment_image']!='') || ($wall_post[$i]['Wallpost']['attachment_image']!='')){?>
					<div class="pro_ad_cont">
						<div class="pro_ad_cont_pic">
<?php echo $this->Html->image('../frontend/uploads/wallpost/thumbimage/'. $wall_post[$i]['Wallpost']['attachment_image'],array('alt'=>'')); ?>
						</div>
						<div class="pro_ad_cont_info">
							<h2><?php echo ucfirst($wall_post[$i]['Wallpost']['attachment_heading']);?></h2>
							<p><?php echo ucfirst($wall_post[$i]['Wallpost']['attachment_text']);?></p>
							<span><a href="<?php echo $wall_post[$i]['Wallpost']['attachment_url'];?>"><?php echo $wall_post[$i]['Wallpost']['attachment_url'];?></a></span>
						</div>
					</div>
					<?php } ?>
					<div class="pro_comm_shr">
						<?php echo $this->Html->image('../frontend/images/icon24.jpg',array('alt'=>'')); ?>
						<?php echo count($comments[$wall_post[$i]['Wallpost']['id']])?> Comment  <?php echo $this->Html->image('../frontend/images/icon30.jpg',array('alt'=>'')); ?>
						<!--<span>Share on Facebook</span>-->
					</div>
					<?php foreach($comments[$wall_post[$i]['Wallpost']['id']] as $value){?>
					<div class="pro_comment" id="comment_<?php echo $value['Comment']['id'];?>">
					<!--<div class="pro_comment_pic">
					<?php //echo $this->Html->image('../frontend/images/pic3.jpg',array('alt'=>'')); ?>
					</div>-->
					<div class="pro_comment_text">
<strong><?php echo  $value['u']['username'];?> : </strong><span id="pcomment_<?php echo $value['Comment']['id'];?>"><?php echo $value['Comment']['comment'];?></span>


					
					</div>
		<div id="comment_action_<?php echo $value['Comment']['id'];?>" class="cmnt_action">
		<?php if($this->Session->read('reid_user_uid') == $value['Comment']['user_id']){?>
			 <a onclick="comment_edit('<?php echo $value['Comment']['id'];?>','<?php echo $wall_post[$i]['Wallpost']['id']?>')" class="edt_small"></a>
			<?php }?>
			<?php if(($wall_post[$i]['Wallpost']['user_id']==$this->Session->read('reid_user_uid')) || ($this->Session->read('reid_user_uid') == $value['Comment']['user_id'])){?>
			<a onclick="comment_del('<?php echo $value['Comment']['id'];?>','<?php echo $clinic_id;?>')" class="del_small"></a>
			<?php } ?><!-----only user of that comment and Clinic manager of that Post can delete----->
		</div>
		<!--<div class=comment_msg" id="comment_msg_<?php //echo $value['Comment']['id'];?>"></div>-->
					</div>
					<!--<div class="clear"></div>-->
			
					<?php }?>
					
					
		<div class="pro_new_comment">
		<!--<div class="pro_comment_pic">
		<?php //echo $this->Html->image('../frontend/images/pic5.jpg',array('alt'=>'')); ?>
		</div>-->
		<div class="pro_comment_text">
		<?php echo $this->Form->create('Comment',$settings=array( 'url' => array('controller' => 'clinics', 'action' => 'save_comment'),'class'=>'form-horizontal','id'=>'comment_form','name'=>'comment_form')); ?>
		<textarea rows="5" cols="5" name="comment" placeholder="Write a comment..." id="comment_text_<?php echo $wall_post[$i]['Wallpost']['id'];?>"></textarea>
		
		<input type="hidden" name="comment_id" value="" id="comment_id_<?php echo $wall_post[$i]['Wallpost']['id'];?>">
		<input type="hidden"  name="post_id" value="<?php echo $wall_post[$i]['Wallpost']['id'];?>">
		<input type="hidden" name="clinic_id" value="<?php echo $clinic_id;?>">
		<input type="hidden" name="user_id" value="<?php echo $this->Session->read('reid_user_uid');?>">
		<input type="submit" name="save" value="Save" class="save"/>
		<?php echo $this->Form->end(); ?>
		<!--<a href="#"><?php //echo $this->Html->image('../frontend/images/icon27.jpg',array('alt'=>'')); ?></a>-->
		</div>
		
		</div>
		
			</div>
			</div>
				<?php } ?> <!---For loop Wall post end---->
				
		</div>
	</div>
	<div id="flashMessage" class="error">
		<?php echo $this->Session->flash();?>
		</div>	
	<div class="getin_touch">
	    <div class="container">
	    <div class="getin_touch_title">
			    <h2>Our Services</h2>
			
		 </div>
		 <div class="getin_touch_para">
		    <p>We manage the following:</p>
		    </div>
		 <div class="getin_touch_items">
		    <div class="col-md-6 col-sm-6 col-xs-12">
			    <ul class="getin_touch_left">
				    <li><a href="#">Anorectal Disease</a></li>
				   <li><a href="#">Anal Fistulas</a></li>
				   <li><a href="#">Diverticulosis</a></li>
				   <li><a href="#">Colorectal Cancer</a></li>
				   <li><a href="#">Colonoscopy</a></li>
				   <li><a href="#">FOBT screening</a></li>
				   <li><a href="#">Inflammatory Bowel Disease</a></li>
				   <li><a href="#">Irritable Bowel Syndrome</a></li>
				   <li><a href="#">Robotic Surgery</a></li>
			    </ul>
			</div>
			
			<?php //pr($Specialitie_category); ?>
			
			<div class="col-md-2 col-sm-2 col-xs-12">
			    <ul class="getin_touch_middle">
				    <li><a href="#">
						 
						<?php echo $this->Html->image('../frontend/images/facebook_icon.jpg',array('alt'=>'')); ?>
						
					  </a></li>
				   <li><a href="#">
						   <?php echo $this->Html->image('../frontend/images/twitter_icon.jpg',array('alt'=>'')); ?>
						
						 
					  </a></li>
				   <li><a href="#">
						 <?php echo $this->Html->image('../frontend/images/youtube_icon.jpg',array('alt'=>'')); ?>
						 </a></li>
				   <li><a href="#">
						 <?php echo $this->Html->image('../frontend/images/google_plus_icon.jpg',array('alt'=>'')); ?>
						
						 
					  </a></li>
			    </ul>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-12">
			    <div class="getin_touch_right">
					  
					  <?php echo $this->Html->image('../frontend/images/mobile_img1.jpg',array('alt'=>'')); ?>
				    
			    </div>
			</div>
		 </div>
	  </div>
	</div>
   </section>

        <style>   
        .showme{ 
display: none;
}
.showhim:hover .showme{
display : block;
}

</style>