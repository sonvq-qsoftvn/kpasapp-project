<div class="row-fluid">
            <div class="span12">
                <ul class="breadcrumb">
                    <li>
                        <a href="dashboard">Home</a> <span class="divider">/</span>
                    </li>
                    <li class='active disabled'>
                        <a href="#" >Updates From The Heart</a> 
                    </li>
                </ul>
            </div>
        </div>

<!-- Error Message section Starts-->

<?php
if($msg=='editinvalid')
{

?>
            <div class="alert alert-error">
                        <a class="close" data-dismiss="alert" href="#">×</a>
                        <h4 class="alert-heading">Invalid Update From The Heart Id</h4>
                        Sorry Update Not Updated !!!
            </div>
<?php
}
else if($msg=='editnoupdate')
{
?>
            <div class="alert alert-error">
                        <a class="close" data-dismiss="alert" href="#">×</a>
                        <h4 class="alert-heading">No Update From The Heart With Supplied Id</h4>
                        Sorry Update Not Updated !!!
            </div>
<?php            
}
else if($msg=='editfailure')
{
?>
            <div class="alert alert-error">
                        <a class="close" data-dismiss="alert" href="#">×</a>
                        <h4 class="alert-heading">Internal Error Occured</h4>
                        Sorry Update Not Updated !!!
            </div>
<?php            
}
else if($msg=='editsuccess')
{
?>
            <div class="alert alert-info">
                        <a class="close" data-dismiss="alert" href="#">×</a>
                        <strong>Success!</strong>
                        Update Successfully Updated!!!
            </div>
<?php            
}
else if($msg=='deletefailure')
{
?>
            <div class="alert alert-error">
                        <a class="close" data-dismiss="alert" href="#">×</a>
                        <h4 class="alert-heading">Failure!</h4>
                        Sorry Update Not Deleted !!!
            </div>
<?php             
}
else if($msg=='deletesuccess')
{
?>
            <div class="alert alert-info">
                        <a class="close" data-dismiss="alert" href="#">×</a>
                        <strong>Success!</strong>
                        Update Successfully deleted!!!
            </div>
<?php            
}
else if($msg=='addfailure')
{
?>
            <div class="alert alert-error">
                        <a class="close" data-dismiss="alert" href="#">×</a>
                        <h4 class="alert-heading">Failure!</h4>
                        Sorry Update Not Added !!!
            </div>
<?php             
}
else if($msg=='addsuccess')
{
?>
            <div class="alert alert-info">
                        <a class="close" data-dismiss="alert" href="#">×</a>
                        <strong>Success!</strong>
                        Update Successfully Added!!!
            </div>
<?php            
}
?>
<!-- Error Message section Ends-->
<marquee direction='left' scrollamount='2' behavior='alternate'>Click on the column name to sort by that column</marquee>
<div class="row-fluid">

                <div class="span12">
                    <section class="utopia-widget">
                        <div class="utopia-widget-title">
                            <?php
                            echo $this->Html->image('../admin/img/icons/paragraph_justify.png',array('class'=>'utopia-widget-icon'));
                            ?>
                            <span>Updates List</span>
                        </div>

                        <div class="utopia-widget-content">
                                    <div style="width: 100%;margin-right:9%;margin-bottom:1%;text-align:right;">
                                                <a href='<?php echo BASE_URL;?>administrator/addupdate'>
                                                            <?php
                                                                        echo $this->Html->image('../admin/img/icons/add.png',array('alt'=>'add','title'=>'add'));
                                                            ?>
                                                            &nbsp;&nbsp;Add Update
                                               </a>
                                    </div>
 
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th style="width: 10%;">Sl No.</th>
                                    
                                    
                                    <th style="width: 40%;"><?php echo $this->Paginator->sort('text','Text');?></th>
                                    <th style="width: 15%;"><?php echo $this->Paginator->sort('date_added','Date Added');?></th>
                                    <th style="width: 15%;"><?php echo $this->Paginator->sort('date_last_modified','Date Last Modified');?></th>
                                    
                                   
                                    <th style="width: 20%;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    
                                    //picking the paginator parameters
                                    $paginator_params=($this->paginator->params());
                                    
                                    //starting point of serial no. for current page
                                    $sl_start=($paginator_params['page']*$paginator_params['current'])-($paginator_params['current']-1);
                                    
                                    //listing all the pages in tabular form
                                    foreach($all_updates as $k=>$individual)
                                    {
                                    ?>
                                        <tr>
                                            <td><?php echo $sl_start+$k;?></td>
                                            
                                            <td><?php echo $individual['Update']['text'];?></td>
                                            <td><?php echo $individual['Update']['date_added'];?></td>
                                            <td><?php echo $individual['Update']['date_last_modified'];?></td>
                                            
                                            
                                            
                                            <td>
                                                <a href="<?php echo BASE_URL;?>administrator/editupdate?updateid=<?php echo $individual['Update']['id'];?>" class="edit">
                                                            <?php echo $this->Html->image('../admin/img/icons/pencil.png',array('alt'=>'Edit','title'=>'Edit'));?>
                                                </a>
                                                <a href="javascript:do_confirm('<?php echo BASE_URL;?>administrator/deleteupdate?updateid=<?php echo $individual['Update']['id'];?>');" class="delete">
                                                            <?php echo $this->Html->image('../admin/img/icons/trash_can.png',array('alt'=>'Delete','title'=>'Delete'));?>
                                                </a>
                                            </td>
                                
                                        </tr>
                                    <?php
                                    }
                                    
                                    ?>
                                </tbody>

                            </table>
                            <div class="row-fluid">
                                <div class="span6"><div class="dataTables_info" id="DataTables_Table_0_info"><?php echo $this->Paginator->counter(array(
    'format' => 'Page {:page} of {:pages}, showing {:current} records out of
             {:count} total'
));?></div></div>

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
                        </div>
                    </section>
                </div>
</div>