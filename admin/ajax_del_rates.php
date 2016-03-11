<?php
session_start();
//ajax event price level
include('../include/admin_inc.php');

$obj_add = new admin;
$obj_temp_tickets = new admin;
$obj_edit_tic = new admin;
$obj_final_tickets1 = new admin;
$obj_performer_del = new admin;
$obj_performer = new admin;

//$_SESSION['unique_id'] = '1374580180';
$performer_rates_id = $_POST['performer_rates_id'];

// delete rates
$obj_performer_del->del_standard_rates($performer_rates_id);
$obj_performer_del->next_record();

// Get performer details
$obj_performer->checkSavedPerformer($_SESSION['performer_unique_id']);
$obj_performer->next_record();



echo ' <div style=" max-height:95px; over flow:auto;">';
 //Fetch records from temp table
$obj_temp_tickets->getStandardRate($obj_performer->f('performer_id'));
if($obj_temp_tickets->num_rows()){
	while($obj_temp_tickets->next_record()){
?>
<div class="event_ticketbox" style="border-bottom:1px dashed #CCC;">
    <ul>
        <li><?php echo $obj_temp_tickets->f('rate_name_en');?></li>
        <li><?php echo $obj_temp_tickets->f('rate_name_sp');?></li>
        <li style="width: 90px;"><?php echo number_format($obj_temp_tickets->f('price_mx'),2);?> MXP </li>
        <li style="width: 90px;"><?php echo number_format($obj_temp_tickets->f('price_us'),2);?> USD </li>
        <li style="margin-right: 0;">
            <span class="tic_edit">
                <span style="cursor:pointer;" onclick="edit_rates(<?php echo $obj_temp_tickets->f('performer_rates_id') ?>)"><img src="<?php echo $obj_base_path->base_path(); ?>/images/edit.gif" alt="" width="20" height="16" border="0" align="absmiddle"/> Edit </span>
                <span style="cursor:pointer;" onclick="delStandardrates(<?php echo $obj_temp_tickets->f('performer_rates_id') ?>)"><img src="<?php echo $obj_base_path->base_path(); ?>/images/cross.gif" alt="" width="20" height="16" border="0" align="absmiddle"/> Delete </span>
            </span>
        </li>
    </ul>
</div>
    
 <?php
	}
}
echo '</div>';

 ?>


