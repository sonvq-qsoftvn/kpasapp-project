<div class="row-fluid">
            <div class="span12">
                <ul class="breadcrumb">
                    <li>
                        <a href="<?php echo BASE_URL;?>administrator/dashboard">Home</a> <span class="divider">|</span>
                    </li>
                    <li class='active disabled'>
                        <a href="#" >Compose Message</a> 
                    </li>
                    
                </ul>
            </div>
        </div>




<section id="utopia-wizard-form" class="utopia-widget utopia-form-box">
    <div class="utopia-widget-title">
        <?php
            echo $this->Html->image('../admin/img/icons2/paragraph_justify.png',array('class'=>'utopia-widget-icon'));
        ?>
        
        <span>Compose Message</span>
    </div>

    <div class="row-fluid">
        <div class="utopia-widget-content">
            <div class="span12 utopia-form-freeSpace">
                <div class="sample-form">
                    <?php echo $this->Form->create('Message',$settings=array('class'=>'form-horizontal','id'=>'validation','name'=>'validation')); ?>
                        
                        <fieldset>
                                   
                            <div class="control-group">
                                <label class="control-label" for="select_type">Select Type*:</label>

                                <div class="controls">
                                    
                                    <?php
                                        
                                        $options_arr['none']='Please Select';
                                        $options_arr['all']='All';
                                        $options_arr['all_manager']='All Manager';
                                        $options_arr['all_user']='All user';
                                        $options_arr['one_user']='Individual user';
                                        $options_arr['one_manager']='Individual Manager';
                                        
                                         
                                        echo $this->Form->input('type',array('type'=>'select','label' => FALSE, 'div' => FALSE, 'id' => 'select_type', 'options'=>$options_arr,'style'=>'width:35%;','data-placeholder'=>'Select Type','class'=>'chzn-select-deselect','onchange'=>'message_ajax(this.value,"'.BASE_URL.'administrator/ajaxselectuser")'));
                                    ?> 
                                    
                                </div>
                            </div>
                            
                            <!-------------Ajax Div For select user--------------->
                                    <div  id='sub'>
                                    <input type="hidden" name="recever" id="recever" value="none">
                                    </div>
                        <!-------------End Ajax Div- -------------->     
                                   
                                
                                
                                    
                                    
                            
                            
                          <div id="form_updated_id">
                                  
                               <input type="hidden" name="form_id_update" id="form_id_update" value="0">      
                        </div>  
                             
                            <div class="control-group">
                                <label class="control-label" for="subject">Subject:</label>
                                    <?php if(isset($message_content) && $message_content['Messagecontent']['subject']!='')
                                    {
                                        $subject= $message_content['Messagecontent']['subject'];       
                                    }else{
                                       $subject="";         
                                    }
                                    ?>
                                
                                <div class="controls">
                                    <?php
                                    echo $this->Form->input('subject',array('label' => FALSE, 'div' => FALSE, 'type' => 'text','value'=>$subject, 'class' => 'input-fluid', 'id' =>'subject')); 
                                    ?>
                                    
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="message">Message*:</label>

                                <div class="controls">
                                    <?php if(isset($message_content) && $message_content['Messagecontent']['message']!='')
                                    {
                                        $msg= $message_content['Messagecontent']['message'];       
                                    }else{
                                       $msg="";         
                                    }
                                    ?>
                                    
                                    <?php echo $this->Form->input('messagebody',array('type' => 'textarea','id' => 'messagebody','label' => FALSE,'cols'=>20,'rows'=>10,'value'=>$msg,'div' => FALSE,'class' =>'input-fluid ck_editor')); ?>
                                    
                                </div>
                            </div>
                              
                            
                            <div class="utopia-from-action">
                                <button class="btn btn-primary span5" type="button" onclick='do_validate();'>Send</button>
                               <button class="btn span5" onclick="window.location.href='<?php echo BASE_URL;?>administrator'" type="button">Cancel</button>
                            </div>
                        </fieldset>
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
          
            //form validation
            
            function do_validate()
            {
                        
                        status=1;
                      var select_type=document.getElementById('select_type').value;
                      
                      var subject=document.getElementById('subject').value;
                       
                       
                       
                      var messagebody=CKEDITOR.instances.messagebody.getData();
                       
                       //alert(messagebody);
                         
                        if (select_type=='')
                        {
                                    alert('Please select sender type');
                                    document.getElementById('select_type').focus();
                                    status=0;
                        }
                        else if (subject=='')
                        {
                                    alert('Please give a subjet');
                                    document.getElementById('subject').focus();
                                    status=0;
                        }
                        else if (messagebody=='')
                        {
                                    alert('Please input message Content');
                                    document.getElementById('messagebody').focus();
                                    status=0;
                        }
                        
                        if (status==1)
                        {
                                    document.validation.submit();           
                        }
            }
</script>


<script>

 function message_ajax(val,url)
            {
            
           
                        
            var xmlhttp;
            if (window.XMLHttpRequest)
              {// code for IE7+, Firefox, Chrome, Opera, Safari
              xmlhttp=new XMLHttpRequest();
              }
            else
              {// code for IE6, IE5
              xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
              }
            xmlhttp.onreadystatechange=function()
              {
                        
              if (xmlhttp.readyState==4)
                {
                document.getElementById("sub").innerHTML=xmlhttp.responseText;
                }
              }
            xmlhttp.open("GET",url+'?sendtype='+val,true);
            xmlhttp.send();
            }            
            
            
</script>




<script>
//document.getElementById("subject").addEventListener("keyup", save_auto);
//setInterval(document.getElementById("subject").addEventListener("keyup", save_auto),500000);
setInterval(function () {save_auto()}, 5000);

function save_auto() {
           
       if(document.getElementById("subject").value!='')
       {     
            var subject = document.getElementById("subject").value;
       }else{
         var subject ="";   
       }
       
       if(document.getElementById("select_type").value)
       {     
            var select_type = document.getElementById("select_type").value;
       }else{
         var select_type ="";   
       }
       
       if (document.getElementById("recever").value!="") {
            var recever_id=document.getElementById("recever").value;
       }else{
            var recever_id=""
       }
       
          
      var form_id_val=document.getElementById("form_id_update").value;
      var messagebody=CKEDITOR.instances.messagebody.getData();
                      
            
       
        
       if (window.XMLHttpRequest)
              {// code for IE7+, Firefox, Chrome, Opera, Safari
              xmlhttp=new XMLHttpRequest();
              }
            else
              {// code for IE6, IE5
              xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
              }
            xmlhttp.onreadystatechange=function()
              {
                        
              if (xmlhttp.readyState==4)
                {
                document.getElementById("form_updated_id").innerHTML=xmlhttp.responseText;
                }
              }
              
              
            xmlhttp.open("GET","<?php echo BASE_URL.'administrator/ajaxselectuser'; ?>"+"?autosave=autosave"+"&subject="+subject+"&select_type="+select_type+"&form_id="+form_id_val+"&messagebody="+messagebody+"&recever_id="+recever_id,true);
            xmlhttp.send();
           
       
}
</script>



