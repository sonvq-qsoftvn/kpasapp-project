<?php 
class merchant_admin extends DB_Sql 
{

	function api_show()
	{
		$sql = "select * from ".$this->prefix()."setting " ;
		$this->query($sql);
	}
	
	//==============================  Manage page ======================
		
		function list_page($limit)			
			{
				$sql="SELECT * FROM ".$this->prefix()."page  order by page_id desc $limit";
				$this->query($sql);
			}
		function showpage_by_id($page_id)
			{
				$sql = "SELECT * FROM ".$this->prefix()."page  where page_id=".$page_id;
	 			$this->query($sql);
			}
		function edit_page($page_name,$title_sp,$page_content,$page_content_sp,$page_id,$page_link,$social,$path,$file_name,$publish)
			{
				$sql = "update ".$this->prefix()."page set page_name='".$page_name."',title_sp='".$title_sp."',page_content='".$page_content."',page_content_sp='".$page_content_sp."',page_link='".$page_link."',path='".$path."',social='".$social."',photo='".$file_name."',publish='".$publish."' where page_id=".$page_id;
				/*echo $sql;
				exit;*/
	 			$this->query($sql);
			}
		function add_page($page_name,$title_sp,$page_content,$page_content_sp,$page_link,$social,$path,$file_name,$publish)
			{
				$sql = "INSERT INTO ".$this->prefix()."page set page_name='".$page_name."',title_sp='".$title_sp."',page_content='".$page_content."',page_content_sp='".$page_content_sp."',page_link='".$page_link."',path='".$path."',social='".$social."',photo='".$file_name."',create_time='".time()."',publish='".$publish."'";
				//exit;
	 			$this->query($sql);
			}	
		function deletepage($page_id)
			{
				$sql="delete from ".$this->prefix()."page where page_id=".$page_id;
				/*echo $sql;
				exit;*/
				$this->query($sql);	
			}	
		function list_page_all()			
			{
				$sql="select * from ".$this->prefix()."page  order by page_id desc ";
				$this->query($sql);
			}								
// ================================== End ==================================

function categorylist()
{
	$sql="SELECT * FROM ".$this->prefix()."event_category WHERE parent_category = '0'";
	return $this->query($sql);
}
function getAllCat($limit)
{
	$sql="SELECT * FROM ".$this->prefix()."event_category WHERE parent_category = '0' $limit ";
	return $this->query($sql);
}
function getAllCatnum()
{
	$sql="SELECT * FROM ".$this->prefix()."event_category WHERE parent_category = '0'";
	return $this->query($sql);
}

function getAllSubCat($limit)
{
	$sql="SELECT * FROM ".$this->prefix()."event_category WHERE parent_category!= '0' $limit ";
	return $this->query($sql);
}

function getAllSubCatnum()
{
	$sql="SELECT * FROM ".$this->prefix()."event_category WHERE parent_category != '0' ";
	return $this->query($sql);
}

function addEventCategory($parent_id,$category_name,$category_name_sp,$category_rank)
{
	$sql = "INSERT INTO ".$this->prefix()."event_category SET category_status = 'Y', parent_category = '".$parent_id."',
															  category_name = '".$category_name."',
															  category_name_sp = '".$category_name_sp."',
															  category_ranking ='".$category_rank."' ";
	$this->query($sql);
}

function editEventCategory($parent_id,$category_name,$category_name_sp,$category_id,$category_rank)
{
	$sql = "UPDATE ".$this->prefix()."event_category SET parent_category = '".$parent_id."',
														 category_name = '".$category_name."',
														 category_name_sp = '".$category_name_sp."',
														 category_ranking ='".$category_rank."'
														 
														 WHERE category_id = '".$category_id."'";
	$this->query($sql);
}	

function delsubcat($scid)
{
	$sql="DELETE FROM ".$this->prefix()."event_category WHERE parent_category != 0 AND category_id = ".$scid;
	$this->query($sql);
}

function delcat($cid)
{
	$sql="DELETE FROM ".$this->prefix()."event_category WHERE parent_category = 0 AND category_id =".$cid;
	$this->query($sql);
	
	$sql="DELETE FROM ".$this->prefix()."event_category WHERE parent_category = ".$cid;
	$this->query($sql);
}


function getParentCatName($category_id)			
{
	$sql="SELECT A.category_name as cat1,A.category_id parentcat_id,B.category_id as subcat_id, B.category_name as cat2 FROM kcp_event_category A INNER JOIN kcp_event_category B ON(A.category_id=B.parent_category) WHERE B.category_id = $category_id";
	$this->query($sql);
}

function getCategoryById($id)
{
	$sql = "SELECT * FROM ".$this->prefix()."event_category  WHERE category_id = ".$id;
	$this->query($sql);
}

 // ==================== state ===================================
	function statelist($limit)
	{
		$sql="SELECT * FROM ".$this->prefix()."state order by id $limit";
		return $this->query($sql);
	}
	function statelist_num()
	{
		$sql="SELECT * FROM ".$this->prefix()."state order by id ";
		return $this->query($sql);
	}
	function deleteState($id)
	{
		$sql="DELETE FROM ".$this->prefix()."state WHERE id = '".$id."'";
		$this->query($sql);

		$sql1="DELETE FROM ".$this->prefix()."county WHERE state_id = '".$id."'";
		$this->query($sql1);
	}
	function getState($id)
	{
		$sql="SELECT * FROM ".$this->prefix()."state WHERE id = $id ";
		return $this->query($sql);
	}
	
	function EditState($country_id,$state_name_sp,$state_name,$id)
	{
		$sql = "UPDATE ".$this->prefix()."state SET country_id = '".$country_id."',
													 state_name_sp = '".$state_name_sp."',
													 state_name = '".$state_name."'
													 
													 WHERE id = '".$id."'";
		$this->query($sql);
	}	
	
function addState($country_id,$state_name_sp,$state_name)
{
	$sql = "INSERT INTO ".$this->prefix()."state SET country_id = '".$country_id."',
													  state_name_sp = '".$state_name_sp."',
													  state_name = '".$state_name."' ";
	$this->query($sql);
}
	

 // ==================== state ===================================


//==================================== Country list ============================================
	function countries_list(){
	
		$sql="SELECT * FROM ".$this->prefix()."countries  ";
		return $this->query($sql);
	}
//==================================== End ============================================


//==================================== City list ============================================
	function cityDtlslist($limit){
	
		$sql="SELECT *,A.id cty FROM ".$this->prefix()."city A LEFT JOIN ".$this->prefix()."county B ON(A.county_id = B.id) LEFT JOIN ".$this->prefix()."state C ON(B.state_id = C.id) order by A.id $limit";
		$this->query($sql);
	}
	function cityDtlslist_num($limit){
	
		$sql="SELECT * FROM ".$this->prefix()."city A LEFT JOIN ".$this->prefix()."county B ON(A.county_id = B.id) LEFT JOIN ".$this->prefix()."state C ON(B.state_id = C.id) order by A.id ";
		$this->query($sql);
	}
	function geteachCityDtls($city_id){
	
		$sql="SELECT *, C.id stateid,B.id countyid FROM ".$this->prefix()."city A LEFT JOIN ".$this->prefix()."county B ON(A.county_id = B.id) LEFT JOIN ".$this->prefix()."state C ON(B.state_id = C.id) Where A.id = $city_id";
		$this->query($sql);
	}
	function getStateByCountry($country_id){
	
		$sql="SELECT * FROM ".$this->prefix()."state WHERE country_id = $country_id ";
		//echo $sql;
		return $this->query($sql);
	}
	function obj_county($stateid){
	
		$sql="SELECT * FROM ".$this->prefix()."county WHERE state_id = $stateid ";
		return $this->query($sql);
	}
	function updateCity($county_id,$city_name,$city_name_sp,$city_id)
	{
		$sql = "UPDATE ".$this->prefix()."city SET county_id = '".$county_id."', city_name = '".$city_name."', city_name_sp = '".$city_name_sp."' where id=".$city_id;
		$this->query($sql);
	}
	function addCity($county_id,$city_name,$city_name_sp)
	{
		$sql = "INSERT INTO ".$this->prefix()."city SET county_id = '".$county_id."',city_name = '".$city_name."',city_name_sp = '".$city_name_sp."'";
		$this->query($sql);
	}
	function deleteCity($id)
	{
		$sql="DELETE FROM ".$this->prefix()."city WHERE id = '".$id."'";
		$this->query($sql);
	}

//==================================== End ============================================


//==================================== Perosnal & professional User ============================================

	function per_user_dtls($limit)			
	{
		$sql="SELECT A.*, B.county_name,C.state_name,D.city_name,E.nicename as country_name FROM ".$this->prefix()."admin A LEFT JOIN ".$this->prefix()."county B ON(A.county=B.id) LEFT JOIN ".$this->prefix()."state C ON(A.province=C.id) LEFT JOIN ".$this->prefix()."city D ON(A.city=D.id) LEFT JOIN ".$this->prefix()."countries E ON(A.country_id=E.id) Where A.account_type = 0 order by fname $limit";
		//echo $sql;
		$this->query($sql);
	}

	function per_user_dtls_num()			
	{
		$sql="SELECT A.*, B.county_name,C.state_name,D.city_name,E.nicename as country_name FROM ".$this->prefix()."admin A LEFT JOIN ".$this->prefix()."county B ON(A.county=B.id) LEFT JOIN ".$this->prefix()."state C ON(A.province=C.id) LEFT JOIN ".$this->prefix()."city D ON(A.city=D.id) LEFT JOIN ".$this->prefix()."countries E ON(A.country_id=E.id) Where A.account_type = 0 order by admin_id ";
		$this->query($sql);
	}

	function pro_user_dtls($limit)			
	{
		$sql="SELECT A.*, B.county_name,C.state_name,D.city_name,E.nicename as country_name FROM ".$this->prefix()."admin A LEFT JOIN ".$this->prefix()."county B ON(A.county=B.id) LEFT JOIN ".$this->prefix()."state C ON(A.province=C.id) LEFT JOIN ".$this->prefix()."city D ON(A.city=D.id) LEFT JOIN ".$this->prefix()."countries E ON(A.country_id=E.id) Where A.account_type = 1 order by fname $limit";
		//echo $sql;
		$this->query($sql);
	}

	function pro_user_dtls_num()			
	{
		$sql="SELECT A.*, B.county_name,C.state_name,D.city_name,E.nicename as country_name FROM ".$this->prefix()."admin A LEFT JOIN ".$this->prefix()."county B ON(A.county=B.id) LEFT JOIN ".$this->prefix()."state C ON(A.province=C.id) LEFT JOIN ".$this->prefix()."city D ON(A.city=D.id) LEFT JOIN ".$this->prefix()."countries E ON(A.country_id=E.id) Where A.account_type = 1 order by admin_id ";
		$this->query($sql);
	}

//==================================== End ============================================

	function get_event($cur,$upto){
	
		$sql="SELECT E.*, C.*, V.venue_id, V.venue_name, V.venue_name_sp FROM ".$this->prefix()."general_events as E join ".$this->prefix()."city as C on E.event_venue_city = C.id join ".$this->prefix()."venue as V on E.event_venue = V.venue_id WHERE E.event_start_date_time >= '".$cur."' and E.event_start_date_time <= '".$upto."' order by E.event_start_date_time asc";
		//echo $sql;
		return $this->query($sql);
	}

//===================================== DELETE USER ===================================//
function deleteUser($id,$email_id)
	{
		$sql="DELETE FROM ".$this->prefix()."admin WHERE admin_id = '".$id."'";
		$this->query($sql);
		//echo $sql;
		$sql_f="DELETE FROM ".$this->prefix()."social_login WHERE email = '".$email_id."'";
		$this->query($sql_f);
		//echo $sql_f;
		$sql_g="DELETE FROM ".$this->prefix()."google_user WHERE google_email = '".$email_id."'";
		$this->query($sql_g);
		//echo $sql_g;
		//exit;
	}

	

};

?>