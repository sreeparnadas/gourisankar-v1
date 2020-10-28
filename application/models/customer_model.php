<?php
class customer_model extends CI_Model {
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
 

    function insert_new_customer($customer){
        $return_array=array();
        $financial_year=get_financial_year();
        try{
            //$this->db->query("START TRANSACTION");
            $this->db->trans_start();
            //insert into maxtable
            $sql="insert into maxtable (subject_name, current_value, financial_year,prefix)
            	values('person',1,?,'C')
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
              
              ) VALUES (?,4,?,?,?,?,?,?,?,?,?,?,?,?);";
            $result = $this->db->query($sql,array(
                $customer_id
            ,$customer->customer_name
            ,$customer->mobile_no
            ,$customer->email
            ,$customer->address
            ,$customer->city
            ,$customer->district_id
            ,$customer->post_office
             ,$customer->pin
            ,$customer->aadhar_no
            ,$customer->pan_no
            ,$customer->gst_number
            ,$customer->state_id
                       
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
            $return_array['error']=create_log($err->code,$this->db->last_query(),'customer_model','insert_new_customer',"log_file.csv");
            $return_array['success']=0;
            $return_array['message']='test';
            $this->db->query("ROLLBACK");
        }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'customer_model','insert_new_customer',"log_file.csv");
            // $return_array['error']=mysql_error;
            $return_array['success']=0;
            $return_array['message']=$err->message;
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;


    }//end of insert_customer

    function select_customers(){
            $sql="select person_id, person_name as customer_name,mobile_no,email
            , pan_no,aadhar_no, address, city
            , post_office, pin
            , gst_number, person.state_id
            , person.district_id
            , states.state_name
            from person
            inner join states on person.state_id = states.state_id
            left join districts on person.district_id = districts.district_id
            where person_cat_id=4 order by person.person_name";
        $result = $this->db->query($sql,array());
        return $result;
    }


    function update_customer_by_customer_id($customer){
        $return_array=array();
        try{
            $this->db->trans_start();
            //update Customer
            $sql="update person set
                 person_name=?
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
                , state_id=? where person_id=?";
            $result=$this->db->query($sql,array(
           $customer->customer_name
            ,$customer->mobile_no
            ,$customer->email
            ,$customer->aadhar_no
            ,$customer->pan_no
            ,$customer->address
            ,$customer->city
            ,$customer->district_id
            ,$customer->post_office
            ,$customer->pin
            ,$customer->gst_number
            ,$customer->state_id
            ,$customer->person_id
            ));
            if($result==FALSE){
                throw new mysqli_sql_exception('error updating customer');
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


    }//update_new_customer_by_customer_id
 
}//final

?>