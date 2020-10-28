<?php
class vendor_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }

//
   
 
    function select_religion(){
        $sql="select religion_id,religion_name from religion where inforce=1 and display=1";
        $result = $this->db->query($sql,array());
        return $result;
    }
 


    function insert_new_vendor($vendor){
        $return_array=array();
        $financial_year=get_financial_year();
        try{
            //$this->db->query("START TRANSACTION");
            $this->db->trans_start();
            //insert into maxtable
            $sql="insert into maxtable (subject_name, current_value, financial_year,prefix)
            	values('person',1,?,'V')
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
            $vendor_id=$result->row()->prefix.'-'.leading_zeroes($result->row()->current_value,3).'-'.$financial_year;
            $return_array['person_id']=$vendor_id;
            $sql = "insert into person (
                person_id
                ,person_cat_id
                ,company_name
                ,person_name
                ,mobile_no
                ,email
                ,address
                ,city
                ,district_id
                ,post_office
                ,pin
                ,aadhar_no
                ,pan_no
                ,gst_number
                ,state_id
                ,bank_name
                ,branch
                ,ifsc_code
                ,micr_code
                ,account_number
              ) VALUES (?,5,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);";
            $result = $this->db->query($sql,array(
                $vendor_id
                ,$vendor->company_name
            ,$vendor->vendor_name
            ,$vendor->mobile_no
            ,$vendor->email
            ,$vendor->address
            ,$vendor->city
            ,$vendor->district_id
            ,$vendor->post_office
             ,$vendor->pin
            ,$vendor->aadhar_no
            ,$vendor->pan_no
            ,$vendor->gst_number
            ,$vendor->state_id
            ,$vendor->bank_name
            ,$vendor->branch
            ,$vendor->ifsc_code
            ,$vendor->micr_code
            ,$vendor->account_number
            
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

    function select_vendors(){
            $sql="select person_id, company_name, person_name as vendor_name,mobile_no,email
            ,bank_name,branch,micr_code,ifsc_code,account_number
            , pan_no,aadhar_no, address, city
            , post_office, pin
            , gst_number, person.state_id
            , person.district_id
            , states.state_name
            from person
            inner join states on person.state_id = states.state_id
            left join districts on person.district_id = districts.district_id
            where person_cat_id=5 order by person.person_name";
        $result = $this->db->query($sql,array());
        return $result;
    }


    function update_vendor_by_vendor_id($vendor){
        $return_array=array();
        try{
            $this->db->trans_start();
            //update Customer
            $sql="update person set
                  company_name=?
                , person_name=?
                , mobile_no=?
                , email=?
                , aadhar_no=?
                , pan_no=?
                , address=?
                , city=?
                , district_id=?
                , post_office=?
                , pin=?
                , gst_number=?
                , state_id=?,bank_name=?,branch=?,micr_code=?,ifsc_code=?,account_number=? where person_id=?";
            $result=$this->db->query($sql,array(
            $vendor->company_name
            ,$vendor->vendor_name
            ,$vendor->mobile_no
            ,$vendor->email
            ,$vendor->aadhar_no
            ,$vendor->pan_no
            ,$vendor->address
            ,$vendor->city
            ,$vendor->district_id
            ,$vendor->post_office
            ,$vendor->pin
            ,$vendor->gst_number
            ,$vendor->state_id
            ,$vendor->bank_name
            ,$vendor->branch
            ,$vendor->micr_code
            ,$vendor->ifsc_code
            ,$vendor->account_number
            ,$vendor->person_id
            ));
            if($result==FALSE){
                throw new mysqli_sql_exception('error updating vendor');
            }
            // adding customer completed
            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['message']='Successfully Updated';
        }catch(mysqli_sql_exception $e){
            //$err=(object) $this->db->error();
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'person model','update_person',"log_file.csv");
            $return_array['success']=0;
            $return_array['message']='test';
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;


    }//update_new_vendor_by_vendor_id
 
}//final

?>