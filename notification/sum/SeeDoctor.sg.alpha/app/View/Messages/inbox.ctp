<div class="row-fluid">
            <div class="span12">
                <ul class="breadcrumb">
                    <li>
                        <a href="dashboard">Home</a> <span class="divider">/</span>
                    </li>
                    <li class='active disabled'>
                        <a href="#" >Inbox</a> 
                    </li>
                </ul>
            </div>
        </div>

<marquee direction='left' scrollamount='2' behavior='alternate'>Click on the column name to sort by that column.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Click on the subject to view the message.</marquee>
<div class="row-fluid">

                <div class="span12">
                    <section class="utopia-widget">
                        <div class="utopia-widget-title">
                            <?php
                            echo $this->Html->image('../admin/img/icons/paragraph_justify.png',array('class'=>'utopia-widget-icon'));
                            ?>
                            <span>Inbox</span>
                        </div>

                        <div class="utopia-widget-content">
                                    
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th style="width: 10%;">Sl No.</th>
                                    <th style="width: 10%;"><?php echo $this->Paginator->sort('Messagecontent.fromuname','From');?></th>
                                    <th style="width: 15%;"><?php echo $this->Paginator->sort('Messagecontent.fromtype','User Type');?></th>
                                    
                                    <th style="width: 35%;"><?php echo $this->Paginator->sort('Messagecontent.subject','Subject');?></th>
                                    <th style="width: 15%;"><?php echo $this->Paginator->sort('Messagecontent.datesent','Date & Time');?></th>
                                    <th style="width: 5%;">Reply</th>
                                    <th style="width: 5%;">Forward</th>
                                    <th style="width: 5%;">Delete</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    
                                    //picking the paginator parameters
                                    $paginator_params=($this->paginator->params());
                                    
                                    //starting point of serial no. for current page
                                    $sl_start=($paginator_params['page']*$paginator_params['current'])-($paginator_params['current']-1);
                                    
                                    //listing all the pages in tabular form
                                    foreach($all_messages_inbox as $k=>$individual)
                                    {
                                    ?>
                                        <tr>
                                            <td><?php echo $sl_start+$k;?></td>
                                            <td><?php echo $individual['Messagecontent']['fromuname'];?></td>
                                            <td><?php echo $individual['Messagecontent']['fromtype'];?></td>
                                            <td><a href='<?php echo BASE_URL;?>administrator/viewmessage?messageid=<?php echo $individual['Message']['id'];?>'><?php echo $individual['Messagecontent']['subject'];?></a></td>
                                            <td><?php echo $individual['Messagecontent']['datesent'];?></td>
                                            
                                            <td>
                                                
                                                <a href="<?php echo BASE_URL;?>administrator/reply?replytoid=<?php echo $individual['Message']['id'];?>"class="delete">
                                                            <?php echo $this->Html->image('../admin/img/icons/mail.png',array('alt'=>'Reply','title'=>'Reply'));?>
                                                </a>
                                            </td>
                                            <td>
                                                
                                                <a href="<?php echo BASE_URL;?>administrator/forwardmessage?messageid=<?php echo $individual['Messagecontent']['id'];?>"class="delete">
                                                            <?php echo $this->Html->image('../admin/img/icons/arrow_up2.png',array('alt'=>'Forward','title'=>'Forward'));?>
                                                </a>
                                            </td>
                                            
                                            <td>
                                                
                                                <a href="javascript:do_trash('<?php echo BASE_URL;?>administrator/sendtotrash?messageid=<?php echo $individual['Message']['id'];?>');" class="delete">
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
