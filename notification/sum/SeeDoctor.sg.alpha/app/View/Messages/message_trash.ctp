<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<section class="emai-registration">
    <div class="topheading-box">
       <div class="container">
             <h2>Message</h2>
       </div>
    </div>
      <div class="container">
      	<div class="msg_container">
            <div class="msg_nav">
                <!--<ul>
                  <li class="inbox"><a href="<?php echo BASE_URL;?>messages/MessageInbox" >Inbox</a></li>
                   <li class="compose"><a href="<?php echo BASE_URL;?>messages/conversation/9/Clinicmanager">Compose</a></li>
                   <li class="outbox_active"><a href="<?php echo BASE_URL;?>messages/MessageOutbox" >Outbox</a></li>
                   <li class="draft"><a href="<?php echo BASE_URL;?>messages/MessageTrash">Trash</a></li>
                </ul>-->
		<?php echo $this->element("frontend/message_tab");?>
                <!--<div class="all_msg">
                    <select class="custom-select" style="width:220px;">
                        <option value="">All Messages (5)</option>
                        <option value="">test 1</option>
                        <option value="">test 2</option>
                        <option value="">test 3</option>
                    </select>
                </div>-->
            </div>
            <div class="msg_table">
             <table width="100%" border="0" cellspacing="0" cellpadding="0" class="msg_info">
                 
                 
                <?php  //print_r($all_trash); ?>
                 
              <?php if(!empty($all_trash)) 
                  {
       
                    foreach($all_trash as $all_trash)
                    {
       
                 ?>   
                 
                <tr class="msg_info_cont">
                    <td class="col-1">
                        
                           <div class="pic">
                        
                          <?php
                                        if((isset($all_trash['ToUser']['profile_image']) &&  $all_trash['ToUser']['profile_image']!='') && (file_exists("frontend/uploads/profile_image/".$all_trash['ToUser']['profile_image'])))
                                        {
                                        ?>
                                                <img  width="51" height="50" src="<?php echo $this->webroot; ?>frontend/uploads/profile_image/thumbimage/<?php echo $all_trash['ToUser']['profile_image']; ?>" style="margin-bottom: 10px;">
                                        <?php
                                        }
                                        else
                                        {
                                                ?>
                                                <img src="<?php echo $this->webroot; ?>frontend/images/no-avatar.jpg" width="51" height="50" style="margin-bottom: 10px;">
                                                <?php
                                        }
									
                                        
                                    ?>
                        
                        </div>
                        
                        
                        <div class="picdetails">
                            
                                
                                <?php if($all_trash['Message']['totype']=='superadmin'){
                                   // echo ($to_users['admin'][$all_trash['Message']['toid']]['Admin']['admin_email']); ?>                        
                                    <?php //echo ucfirst('superadmin');
                                    
                                        echo ucfirst($all_trash['ToUser']['username']); 
                           
                                }else{
                                    
                                     $all_types=array('User','Clinic Manager');
                                     
                                     echo ucfirst($all_trash['ToUser']['username']); 
                                     
                                 
              
                           
                           //echo ($to_users['user'][$all_trash['Message']['toid']]['User']['email']); ?>
                     
                               <?php echo ucfirst($all_types[$to_users['user'][$all_trash['Message']['toid']]['User']['user_type']-1]); }?>
                            
                                   <br/>
                            
                          <?php $ret_str=($this->Functions->calculate_time_gap(date('Y-m-d H:i:s'),$all_trash['Messagecontent']['datesent']));if($ret_str['Y']!=0||$ret_str['M']!=0){echo "Nearly ";} if($ret_str['Y']!=0){ echo $ret_str['Y'].' Year'; if($ret_str['Y']>1){echo 's';}}if($ret_str['M']!=0){ echo ' '.$ret_str['M'].' Month'; if($ret_str['M']>1){echo 's';}}if($ret_str['d']!=0){ echo ' '.$ret_str['d'].' Day'; if($ret_str['d']>1){echo 's ';}}if($ret_str['h']!=0){ echo ' '.$ret_str['h'].' Hour'; if($ret_str['h']>1){echo 's ';}}if($ret_str['m']!=0){ echo ' '.$ret_str['m'].' Minute'; if($ret_str['m']>1){echo 's ';}}if($ret_str['Y']!=0||$ret_str['M']!=0||$ret_str['d']!=0||$ret_str['h']!=0||$ret_str['m']!=0){echo ' ago ';}else{echo 'Just Now';}?>
                                   
                        </div>
                    </td>
                    
                    
                    <td class="col-2"><!--<a href="<!?php echo BASE_URL;?>messages/conversation/<?php echo $all_trash['Message']['toid'];?>/<?php echo $all_trash['Message']['totype'];?>/<?php echo $all_trash['Message']['id'];?>/thismsg">--><?php echo $all_trash['Messagecontent']['subject'] ?><!--</a>--></td>
                    <td class="col-3">
                        <?php echo $this->Html->image('../frontend/images/icon36.jpg',array('alt'=>'')); ?> 
                         
                        
                        <div><?php echo  date('d.m.Y',strtotime($all_trash['Messagecontent']['datesent']))?></div></td>
                    <td class="col-4"> <?php echo $this->Html->image('../frontend/images/icon37.jpg',array('alt'=>'')); ?> <div><?php echo  date('h.i  A',strtotime($all_trash['Messagecontent']['datesent']))?></div></td>
                    <td class="col-5">
			<!--<span>
			    <a href="<!?php echo BASE_URL;?>messages/conversation/<!?php echo $all_trash['Message']['toid'];?>/<!?php echo $all_trash['Message']['totype'];?>/<!?php echo $all_trash['Message']['id'];?>"><!?php echo $this->Html->image('../frontend/images/icon35.jpg',array('alt'=>'')); ?></a>
			</span>--> 
                            
			<!--<span><a href="#">
                                <?php //echo $this->Html->image('../frontend/images/icon34.jpg',array('alt'=>'')); ?>     
                            </a></span>-->
<span>
<a onclick="return confirm('Are you sure to delete this Message?')"href="<?php echo BASE_URL.'messages/is_trash_deleted/id:'.$all_trash['Message']['id'] ?>" class="initialism rescheduleapp_open">
                                    
                                   <?php echo $this->Html->image('../frontend/images/icon33.jpg',array('alt'=>'')); ?>  
                                    
                                </a></span>
                    </td>
                </tr>
                
                
                  <?php } }
		  else
		  {
		    echo "<h3 style='padding-left:30px;'>No message in inbox</h3>";
		  }
		  ?>
                
               
            </table>
           </div>
           <!--<div class="msg_table_pagination">
           
                
                  <?php
                                                                
                                                                
//                                                               if($this->Paginator->hasPrev())
//								{
//								    // Shows the next and previous links
//                                                                echo $this->Paginator->prev(' ', $options=array('tag'=>false,'class'=>'prev')
//                                                                   );    
//								}
//                                                                
//                                                                // Shows the page numbers
//                                                                echo $this->Paginator->numbers($options=array('class'=>'page','currentClass'=>'page_active','separator'=>''));
//                                                                if($this->Paginator->hasNext())
//								{
//								    // Shows the next and previous links
//                                                                echo $this->Paginator->next(' ', $options=array('tag'=>false,'class'=>'next')
//                                                                   );    
//								}
                                                                
                    
                                                            ?>
                
                
                 
        </div>-->
	<div class="span6">
                                    <div class="dataTables_paginate paging_bootstrap pagination">
                                                <ul>
                                                            <?php
                                                                
                                                                
                                                                // Shows the next and previous links
                                                                echo $this->Paginator->prev(
                                                                  ' ←',
                                                                  $options=array('tag'=>'li','class'=>'prev','disabledTag'=>'a'),
                                                                  null,
                                                                  array('tag'=>'li','disabledTag'=>'a','class'=>'prev disabled')
                                                                );
                                                                
                                                                // Shows the page numbers
                                                                echo $this->Paginator->numbers($options=array('tag'=>'li','separator'=>'','currentTag'=>'a','currentClass'=>'active'));
                                                                
                                                                // Shows the next and previous links
                                                                echo $this->Paginator->next(
                                                                  '→ ',
                                                                  $options=array('tag'=>'li','class'=>'next','disabledTag'=>'a'),
                                                                  null,
                                                                  array('tag'=>'li','disabledTag'=>'a','class'=>'next disabled')
                                                                );
                    
                                                            ?>
                                                </ul>
                                    </div>
                                </div>
          
      </div> 
          
    </section>