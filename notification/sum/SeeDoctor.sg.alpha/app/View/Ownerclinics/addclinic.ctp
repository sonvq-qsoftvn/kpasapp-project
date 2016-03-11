<div class="row-fluid">
            <div class="span12">
                <ul class="breadcrumb">
                    <li>
                        <a href="<?php echo BASE_URL;?>clinicmanager/dashboard">Home</a> <span class="divider">/</span>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL;?>clinicmanager/clinics">Clinics</a> <span class="divider">/</span>
                    </li>
                    <li class='active disabled'>
                        <a href="#" >Add Clinic</a> 
                    </li>
                </ul>
            </div>
        </div>
<?php
if($msg!=array())
{
?>
<div class="alert alert-error">
                        <a class="close" data-dismiss="alert" href="#">×</a>
                        <h4 class="alert-heading">Failure!</h4>
                        <ul>
                                    
                        <?php
                        foreach($msg as $k=>$v)
                        {
                        ?>
                                                <li><?php echo $v;?></li>
                                    
                        <?php
                                    
                        }
                        ?>
                        </ul>
            </div>
<?php
}
?>

<section id="utopia-wizard-form" class="utopia-widget utopia-form-box">
    <div class="utopia-widget-title">
        <?php
            echo $this->Html->image('../admin/img/icons2/paragraph_justify.png',array('class'=>'utopia-widget-icon'));
        ?>
        
        <span>Add Clinic Form</span>
    </div>

    <div class="row-fluid">
        <div class="utopia-widget-content">
            <div class="span12 utopia-form-freeSpace">
                <div class="sample-form">
                    <?php echo $this->Form->create('Clinic',$settings=array('class'=>'form-horizontal','id'=>'validation','name'=>'validation')); ?>
                    
                        <fieldset>
                        <?php
                                     
                        $owner_u_id= $this->Session->read('reid_clinicowners_uid');
                                    
                        echo $this->Form->input('clinicmanagersid',array('type'=>'hidden','label' => FALSE, 'div' => FALSE, 'id' => 'clinicmanagersid','value'=>$owner_u_id));
                                    
                        ?>
                                  
                                
                            
                            <div class="control-group">
                                <label class="control-label" for="name">Clinic Name*:</label>

                                <div class="controls">
                                    <?php
                                    echo $this->Form->input('name',array('label' => FALSE, 'div' => FALSE, 'type' => 'text', 'class' => 'input-fluid', 'id' => 'name'));
                                    ?>
                                    
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="license">License Number*:</label>

                                <div class="controls">
                                    <?php
                                    echo $this->Form->input('license',array('label' => FALSE, 'div' => FALSE, 'type' => 'text', 'class' => 'input-fluid', 'id' => 'license'));
                                    ?>
                                    
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="handphone">Hand Phone Number*:</label>

                                <div class="controls">
                                    <?php
                                    echo '+65&nbsp;&nbsp;&nbsp;'.$this->Form->input('handphone',array('label' => FALSE, 'div' => FALSE, 'type' => 'text', 'class' => 'input-fluid', 'id' => 'handphone','style'=>'width:77%;'));
                                    ?>
                                    
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="url">Clinic url*:</label>

                                <div class="controls">
                                    <?php
                                    echo $this->Form->input('url',array('label' => FALSE, 'div' => FALSE, 'type' => 'text', 'class' => 'input-fluid', 'id' => 'url'));
                                    ?>
                                  <br/><span>( Please add <strong>http</strong> or <strong>https</strong>  before the clinic url.)</span>  
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label" for="address">Address*:</label>

                                <div class="controls">
                                    
                                    <?php echo $this->Form->input('address',array('type' => 'textarea','id' => 'address','label' => FALSE,'cols'=>20,'rows'=>10,'div' => FALSE,'class' => 'input-fluid ck_editor')); ?>
                                    
                                    
                                </div>
                            </div>
                            
                            <div class="utopia-from-action">
                                <button class="btn btn-primary span5" type="button" onclick='do_validate();'>Save changes</button>
                               <button class="btn span5" type="button" onclick="window.location.href='<?php echo BASE_URL;?>clinicmanager/clinics'">Cancel</button>
                            </div>
                        </fieldset>
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!--script for form validation-->
<script>
            //form validation
            
            function do_validate()
            {
                        
                        status=1;
                        cm=document.getElementById('clinicmanagersid').value;
                         
                        n=document.getElementById('name').value;
                         
                        l=document.getElementById('license').value;
                         
                        hp=document.getElementById('handphone').value;
                        
                        hp='65'+hp;
                        u=document.getElementById('url').value;
                         
                        addr=CKEDITOR.instances.address.getData();
                        
                        var phExp=/^[(]{0,1}[0-9]{3}[)]{0,1}[-\s\.]{0,1}[0-9]{3}[-\s\.]{0,1}[0-9]{4}$/;
                         
                        if (n=='')
                        {
                                    alert('Please enter clinic name');
                                    document.getElementById('name').focus();
                                    status=0;
                        }
                        else if (l=='')
                        {
                                    alert('Please enter clinic license number');
                                    document.getElementById('license').focus();
                                    status=0;
                        }
                        else if (cm=='')
                        {
                                    alert('Please select a clinic manager');
                                    document.getElementById('clinicmanagersid').focus();
                                    status=0;
                        }
                        else if (hp=='')
                        {
                                    alert('Please enter hand phone');
                                    document.getElementById('handphone').focus();
                                    status=0;
                        }
                        else if (!hp.match(phExp))
                        {
                                 alert('Invalid hand phone');
                                 document.getElementById('handphone').focus();
                                 status=0;   
                        }
                        else if (addr=='')
                        {
                                    alert('Please enter address');
                                    document.getElementById('address').focus();
                                    status=0;
                        }
                        else if (u=='')
                        {
                                    alert('Please enter url');
                                    document.getElementById('url').focus();
                                    status=0;
                        }
                        else if (!u.match(/^(ht|f)tps?:\/\/[a-z0-9-\.]+\.[a-z]{2,4}\/?([^\s<>\#%"\,\{\}\\|\\\^\[\]`]+)?$/))
                        {
                                 alert('Invalid clinic url');
                                 document.getElementById('url').focus();
                                 status=0;   
                        }
                        if (status==1)
                        {
                                    document.validation.submit();           
                        }
            }
</script>