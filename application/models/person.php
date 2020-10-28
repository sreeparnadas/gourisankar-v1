<?php
class Person extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }
    function select_person(){
        $sql = "select person_id as cust_id,person_name, mobile_no,sex from person";
        $result = $this->db->query($sql);
            return $result;
    }

    function select_areas(){
        $sql="select distinct area  from person where LENGTH(area)>0 order by area";
        $result = $this->db->query($sql,array());
        return $result;
    }

    function select_cities(){
        $sql="select distinct city  from person where LENGTH(city)>0 order by city";
        $result = $this->db->query($sql,array());
        return $result;
    }

    function select_post_office(){
        $sql="select distinct post_office  from person where LENGTH(post_office)>0 order by post_office";
        $result = $this->db->query($sql,array());
        return $result;
    }

    function select_address_one(){
        $sql="select distinct address1  from person where LENGTH(address1)>0 order by address1";
        $result = $this->db->query($sql,array());
        return $result;
    }


    function select_states(){
        $sql="select state_id,state_name from states where state_id in(select distinct state_id from districts) order by state_name";
        $result = $this->db->query($sql,array());
        return $result;
    }
    function select_districts($state_id){
        $sql="select district_id,district_name from districts where state_id=?";
        $result = $this->db->query($sql,array($state_id));
        return $result;
    }
   
    function get_person_by_authentication($login_data){
        $sql="select
        person.person_id, person.person_name, person.person_cat_id,person.user_id,1 as is_logged_in
        from person
        inner join person_category on person.person_cat_id = person_category.person_cat_id
        where person.user_id=? and person.user_password=?";
        $result = $this->db->query($sql,array($login_data->userId,$login_data->userPassword));
        if($result->num_rows()>0){
            return $result->row();
        }else{
            $row['person_id']='0';
            $row['person_name']='not found';
            $row['person_cat_id']='0';
            $row['user_id']='0';
            $row['is_logged_in']=0;
            return (object)$row;
        }
    }
    function insert_new_vendor($vendor){
        $return_array=array();
        $financial_year=get_financial_year();
        try{
            //$this->db->query("START TRANSACTION");
            $this->db->trans_start();
            //insert into maxtable
            $sql="insert into maxtable (subject_name, current_value, financial_year,prefix)
            	values('customer',1,?,'C')
				on duplicate key UPDATE id=last_insert_id(id), current_value=current_value+1";
            $result = $this->db->query($sql, array($financial_year));
            if($result==FALSE){
                throw new Exception('Increasing Maxtable for Customer_id');
            }

            //getting from maxtable
            $sql="select * from maxtable where id=last_insert_id()";
            $result = $this->db->query($sql);
            if($result==FALSE){
                throw new Exception('error getting maxtable');
            }
            $customer_id=$result->row()->prefix.'-'.leading_zeroes($result->row()->current_value,3).'-'.$financial_year;
            $return_array['person_id']=$customer_id;
            $sql = "insert into person (
               person_id
              ,person_cat_id
              ,person_name
              ,mailing_name
              ,mobile_no
              ,phone_no
              ,email
              ,aadhar_no
              ,pan_no
              ,address1
              ,city
              ,district_id
              ,post_office
              ,pin
              ,gst_number
              ,inforce
              ,state_id
            ) VALUES (?,5,?,?,?,?,?,?,?,?,?,?,?,?,?,1,?)";
            $result = $this->db->query($sql,array(
                $customer_id
            ,$vendor->person_name
            ,$vendor->mailing_name
            ,$vendor->mobile_no
            ,$vendor->phone_no
            ,$vendor->email
            ,$vendor->aadhar_no
            ,$vendor->pan_no
            ,$vendor->address1
            ,$vendor->city
            ,$vendor->district_id
            ,$vendor->post_office
            ,$vendor->pin
            ,$vendor->gst_number
            ,$vendor->state_id
            ));
            $return_array['dberror']=$this->db->error();
            if($result==FALSE){
                throw new Exception('error adding purchase master');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['message']='Successfully recorded';
        }catch(mysqli_sql_exception $e){
            //$err=(object) $this->db->error();

            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'purchase_model','insert_opening',"log_file.csv");
            $return_array['success']=0;
            $return_array['message']='test';
            $this->db->query("ROLLBACK");
        }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'purchase_model','insert_opening',"log_file.csv");
            // $return_array['error']=mysql_error;
            $return_array['success']=0;
            $return_array['message']=$err->message;
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;


    }//end of insert_vendor

}//final

?>