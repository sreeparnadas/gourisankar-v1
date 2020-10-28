<?php
class Product_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }

   function select_inforce_products(){
       $sql="select product.product_id, hsn_table.hsn_code, product.product_name, product.default_unit_id,in_quintal ,hsn_table.gst_rate, units.unit_name from product
inner join hsn_table on product.hsn_serial_no=hsn_table.hsn_serial_no
inner join units on product.default_unit_id = units.unit_id where product.inforce=1";
       $result = $this->db->query($sql,array());
       return $result;
   }

    function select_purchaseable_products(){
        $sql="select product.product_id, hsn_table.hsn_code, product.product_name, product.default_unit_id, hsn_table.gst_rate, units.unit_name from product
inner join hsn_table on product.hsn_serial_no=hsn_table.hsn_serial_no
inner join units on product.default_unit_id = units.unit_id where product.inforce=1 and product.is_purchaseable=1";
        $result = $this->db->query($sql,array());
        return $result;
    }

    function select_all_units(){
        $sql="select * from units order by unit_name";
        $result = $this->db->query($sql,array());
        return $result;
    }

    function select_all_hsn_codes(){
        $sql="select * from hsn_table where inforce=1";
        $result = $this->db->query($sql,array());
        return $result;
    }


    function insert_new_product($hsnSerialNo,$productName,$openingBalance,$openingDate,$unitId){
        $return_array=array();
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();

//            adding New Bill Master

            //adding product//
            $sql="insert into product (
                   product_id
                  ,hsn_serial_no
                  ,product_name
                  ,default_unit_id
                  ,opening_balance
                  ,opening_date
                ) VALUES (NULL,?,?,?,?,?)";

            $result=$this->db->query($sql,array($hsnSerialNo,$productName,$unitId,$openingBalance,$openingDate));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
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
    }//end of function

    function select_all_products(){
        $sql="select 
                product.product_id
                , product.product_name
                , product.default_unit_id
                , product.opening_balance
                , date_format(opening_date,'%m/%d/%Y') as opening_date
                ,hsn_table.hsn_serial_no
                , hsn_table.hsn_code
                , hsn_table.gst_rate
                , units.unit_id
                , units.unit_name
                from product
                inner join hsn_table on product.hsn_serial_no=hsn_table.hsn_serial_no
                inner join units on product.default_unit_id=units.unit_id where product.inforce=1";
        $result = $this->db->query($sql,array());
        return $result;
    }

    function update_product($pr){
        $return_array=array();
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();

//            adding New Bill Master

            // adding bill_master completed
            //adding bill details
            $sql="update product set hsn_serial_no=?, product_name=?, default_unit_id=?, opening_balance=?, opening_date=? where product_id=?";

            $result=$this->db->query($sql,array(
                $pr->hsn_serial_no
                ,$pr->product_name
                ,$pr->unit_id
                ,$pr->opening_balance
                ,$pr->opening_date
                ,$pr->product_id
            ));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }


            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['message']='Update successful';
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
    }//end of function



}//final

?>