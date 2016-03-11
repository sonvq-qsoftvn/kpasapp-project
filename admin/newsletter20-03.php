<?php
// Newsletter generator - English version
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=newsletter_en.doc");

include('../include/admin_inc.php');
$objevent=new merchant_admin;

$cur = date("Y-m-d H:i:s",time());
$upto = date("Y-m-d H:i:s",strtotime('+30 days'));
$objevent->get_event($cur,$upto);


echo "<html>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=Windows-1252\">";
echo "<body>";

if($objevent->num_rows()>0){
    while($objevent->next_record()){
        echo "<h2>".date("l, F j",strtotime($objevent->f('event_start_date_time'))).":</h2>";   
        echo "<h3>"
	        .date("g:i A",strtotime($objevent->f('event_start_date_time'))).
        	": ".$objevent->f('city_name').
        	", ".$objevent->f('venue_name_en')."<br>".  // Venue name doesn't print out. Why? Do we need to include extra files?
        	"<a href='".$obj_base_path->base_path()."/event/".$objevent->f('event_id')."'>".$objevent->f('event_name_en')."</a>
        </h3>";
        echo "".$objevent->f('event_short_desc_en')."<br>";
        echo "".$objevent->f('event_details_en')."";
    }
}

echo "</body>";
echo "</html>";
?>