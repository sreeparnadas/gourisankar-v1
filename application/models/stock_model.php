<?php
class Stock_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }

    function insert_new_seed_to_oil_stock($master_date,$stock_details){

        $return_array=array();
        $financial_year=get_financial_year();
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();
            //insert into maxtable
            $sql="insert into maxtable (subject_name, current_value, financial_year,prefix)
            	values('seed_oil_convert',1,?,'SOC')
				on duplicate key UPDATE id=last_insert_id(id), current_value=current_value+1";
            $result = $this->db->query($sql, array($financial_year));
            if($result==FALSE){
                throw new Exception('Increasing Maxtable for stock_production_master');
            }

            //getting from maxtable
            $sql="select * from maxtable where id=last_insert_id()";
            $result = $this->db->query($sql);
            if($result==FALSE){
                throw new Exception('error getting stock_production_master');
            }
            $stock_production_master_id=$result->row()->prefix.'-'.leading_zeroes($result->row()->current_value,4).'-'.$financial_year;
            $return_array['stock_production_master_id']=$stock_production_master_id;


            //        adding New Bill Master
            $sql="insert into stock_production_master (
                  stock_production_master_id
                  ,employee_id
                  ,production_date
                ) VALUES (?,?,?)";
            $result=$this->db->query($sql,array(
                $stock_production_master_id
            ,$this->session->userdata('person_id')
            ,$master_date
            ));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding stock_production_master');
            }
            // adding stock_production_master completed
            //adding stock_production_details
            $sql="insert into stock_production_details (
                   stock_production_details_id
                  ,stock_production_master_id
                  ,product_id
                  ,inward
                  ,outward
                ) VALUES (?,?,?,?,?)";

            foreach($stock_details as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        $stock_production_master_id . '-' . ($index+1)
                    , $stock_production_master_id
                    ,$row->product_id
                    ,$row->inward
                    ,$row->outward
                    )
                );

            }
            if($result==FALSE){
                throw new Exception('error adding stock_production_details');
            }
            //INSERT INTO STOCK_TRANSACTION TABLE//
            $sql="insert into stock_transaction (
                   stock_transaction_id
                  ,stock_production_master_id
                  ,product_id
                  ,inward
                  ,outward
                  ,transaction_date
                  ,memo_number
                  ,transaction_type_id
                ) VALUES (?,?,?,?,?,?,?,5)";

            foreach($stock_details as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        'ST'.'-'.$stock_production_master_id . '-' . ($index+1)
                    , $stock_production_master_id
                    ,$row->product_id
                    ,$row->inward
                    ,$row->outward
                    ,$master_date
                    ,''
                    )
                );

            }
            if($result==FALSE){
                throw new Exception('error adding bill_details');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['stock_production_master_id']=$stock_production_master_id;
            $return_array['message']='Production and Stock transaction successfully added';
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

    function insert_oil_into_blank_tin($master_date,$stock_details){
        $return_array=array();
        $financial_year=get_financial_year();
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();
            //insert into maxtable
            $sql="insert into maxtable (subject_name, current_value, financial_year,prefix)
            	values('seed_oil_convert',1,?,'SOC')
				on duplicate key UPDATE id=last_insert_id(id), current_value=current_value+1";
            $result = $this->db->query($sql, array($financial_year));
            if($result==FALSE){
                throw new Exception('Increasing Maxtable for stock_production_master');
            }

            //getting from maxtable
            $sql="select * from maxtable where id=last_insert_id()";
            $result = $this->db->query($sql);
            if($result==FALSE){
                throw new Exception('error getting stock_production_master');
            }
            $stock_production_master_id=$result->row()->prefix.'-'.leading_zeroes($result->row()->current_value,4).'-'.$financial_year;
            $return_array['stock_production_master_id']=$stock_production_master_id;


            //        adding New Bill Master
            $sql="insert into stock_production_master (
                  stock_production_master_id
                  ,employee_id
                  ,production_date
                ) VALUES (?,?,?)";
            $result=$this->db->query($sql,array(
                $stock_production_master_id
            ,$this->session->userdata('person_id')
            ,$master_date
            ));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding stock_production_master');
            }
            // adding stock_production_master completed
            //adding stock_production_details
            $sql="insert into stock_production_details (
                   stock_production_details_id
                  ,stock_production_master_id
                  ,product_id
                  ,inward
                  ,outward
                ) VALUES (?,?,?,?,?)";

            foreach($stock_details as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        $stock_production_master_id . '-' . ($index+1)
                    , $stock_production_master_id
                    ,$row->product_id
                    ,$row->inward
                    ,$row->outward
                    )
                );

            }
            if($result==FALSE){
                throw new Exception('error adding stock_production_details');
            }
            //INSERT INTO STOCK_TRANSACTION TABLE//
            $sql="insert into stock_transaction (
                   stock_transaction_id
                  ,stock_production_master_id
                  ,product_id
                  ,inward
                  ,outward
                  ,transaction_date
                  ,memo_number
                  ,transaction_type_id
                ) VALUES (?,?,?,?,?,?,?,5)";

            foreach($stock_details as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        'ST'.'-'.$stock_production_master_id . '-' . ($index+1)
                    , $stock_production_master_id
                    ,$row->product_id
                    ,$row->inward
                    ,$row->outward
                    ,$master_date
                    ,''
                    )
                );

            }
            if($result==FALSE){
                throw new Exception('error adding bill_details');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['stock_production_master_id']=$stock_production_master_id;
            $return_array['message']='Production and Stock transaction successfully added';
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

    function select_all_stock(){
        $sql="select 
        product.product_name
        ,sum( stock_transaction.inward) as total_inward
        ,sum( stock_transaction.outward) as total_outward
        ,sum( stock_transaction.inward)-sum( stock_transaction.outward) as total_stock
        ,units.unit_name
        from stock_transaction
        inner join product on stock_transaction.product_id = product.product_id
        inner join units on product.default_unit_id = units.unit_id group by product.product_name,units.unit_name";
        $result=$this->db->query($sql,array());
        if($result==null){
            return null;
        }else{
            return $result;
        }
    }


   


    function select_last_moil_closing_stock(){

        $return_array=array();
        
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();
           
            //getting from production table
            $sql="select  max(production_date) as production_date from stock_production_master
            inner join (select * from stock_production_details where product_id =3)stock_production_details 
            on stock_production_master.stock_production_master_id=stock_production_details.stock_production_master_id";
           
            $result = $this->db->query($sql);
            if($result==FALSE){
                throw new Exception('error getting stock_production_master');
            }
            $productionDate = $result->row()->production_date;

            $dt = $productionDate;
            //getting from sale table
            $sql="SELECT max(sale_date) as sale_date FROM `sale_master`
            inner join (select * from sale_details where product_id=3) sale_details
             on sale_master.sale_master_id=sale_details.sale_master_id";
           
            $result = $this->db->query($sql);
            if($result==FALSE){
                throw new Exception('error getting stock_production_master');
            }
            $saleDate = $result->row()->sale_date;
            
            
            if($saleDate > $dt){
                $dt = $saleDate;
            }
            

            $sql="call get_mustard_oil_daily_stock_report(?,?)";
            $result=$this->db->query($sql,array($dt,$dt));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error getting moil report');
            }        
           
            $this->db->trans_complete();
            $return_array['moil']=$result->row();
           
        }catch(mysqli_sql_exception $e){
            //$err=(object) $this->db->error();

            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'stock_model','insert_opening',"log_file.csv");
            $this->db->query("ROLLBACK");
        }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'purchase_model','insert_opening',"log_file.csv");
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }//end of function





    function select_last_mseed_closing_stock(){

        $return_array=array();
        
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();
           
            //getting from production table
            $sql="SELECT max(purchase_date) as purchase_date FROM `purchase_master`
            inner join (select * from purchase_details where product_id=1) purchase_details 
            on purchase_master.purchase_master_id = purchase_details.purchase_master_id";
           
            $result = $this->db->query($sql);
            if($result==FALSE){
                throw new Exception('error getting stock_production_master');
            }
            $purchaseDate = $result->row()->purchase_date;

            $dt = $purchaseDate;
            //getting from production table
            $sql="select  max(production_date) as production_date from stock_production_master
            inner join (select * from stock_production_details where product_id =1)stock_production_details 
            on stock_production_master.stock_production_master_id=stock_production_details.stock_production_master_id";
           
            $result = $this->db->query($sql);
            if($result==FALSE){
                throw new Exception('error getting stock_production_master');
            }
            $productionDate = $result->row()->production_date;
            
            
            if($productionDate > $dt){
                $dt = $productionDate;
            }
            

            $sql="call get_mustard_seed_daily_stock_report(?,?)";
            $result=$this->db->query($sql,array($dt,$dt));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error getting moil report');
            }        
           
            $this->db->trans_complete();
            $return_array['mseed']=$result->row();
           
        }catch(mysqli_sql_exception $e){
            //$err=(object) $this->db->error();

            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'stock_model','insert_opening',"log_file.csv");
            $this->db->query("ROLLBACK");
        }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'purchase_model','insert_opening',"log_file.csv");
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }//end of function






    function select_last_oil_cake_closing_stock(){

        $return_array=array();
        
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();
           
            //getting from production table
            $sql="select  max(production_date) as production_date from stock_production_master
            inner join (select * from stock_production_details where product_id =2)stock_production_details 
            on stock_production_master.stock_production_master_id=stock_production_details.stock_production_master_id";
           
            $result = $this->db->query($sql);
            if($result==FALSE){
                throw new Exception('error getting stock_production_master');
            }
            $productionDate = $result->row()->production_date;

            $dt = $productionDate;
            //getting from sale table
            $sql="SELECT max(sale_date) as sale_date FROM `sale_master`
            inner join (select * from sale_details where product_id=2) sale_details
             on sale_master.sale_master_id = sale_details.sale_master_id";
           
            $result = $this->db->query($sql);
            if($result==FALSE){
                throw new Exception('error getting stock_production_master');
            }
            $saleDate = $result->row()->sale_date;
            
            
            if($saleDate > $dt){
                $dt = $saleDate;
            }
            

            $sql="call get_oil_cake_daily_stock_report(?,?)";
            $result=$this->db->query($sql,array($dt,$dt));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error getting moil report');
            }        
           
            $this->db->trans_complete();
            $return_array['oilCake']=$result->row();
           
        }catch(mysqli_sql_exception $e){
            //$err=(object) $this->db->error();

            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'stock_model','select_last_oil_cake_closing_stock',"log_file.csv");
            $this->db->query("ROLLBACK");
        }catch(Exception $e){
            $err=(object) $this->db->error();
            $return_array['error']=create_log($err->code,$this->db->last_query(),'stock_model','select_last_oil_cake_closing_stock',"log_file.csv");
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }//end of function

}//final

?>