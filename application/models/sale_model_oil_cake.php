<?php
class sale_model_oil_cake extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }

   function select_inforce_products(){
       $sql="select * from product
        inner join product_group ON product_group.group_id = product.group_id
        where product.inforce=1";
       $result = $this->db->query($sql,array());
       return $result;
   }

    function insert_new_sale($saleMaster,$sale_details_list){
        $return_array=array();
        $financial_year=get_financial_year();
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();
            //insert into maxtable
            $sql="insert into maxtable (subject_name, current_value, financial_year,prefix)
            	values('sale mustard oil',1,?,'SMO')
				on duplicate key UPDATE id=last_insert_id(id), current_value=current_value+1";
            $result = $this->db->query($sql, array($financial_year));
            if($result==FALSE){
                throw new Exception('Increasing Maxtable for sale_master');
            }

            //getting from maxtable
            $sql="select * from maxtable where id=last_insert_id()";
            $result = $this->db->query($sql);
            if($result==FALSE){
                throw new Exception('error getting maxtable');
            }
            $sale_master_id=$result->row()->prefix.'-'.leading_zeroes($result->row()->current_value,4).'-'.$financial_year;
            $memo_number=$result->row()->current_value;
            $memo_number=(int)$memo_number;
            $return_array['sale_master_id']=$sale_master_id;


            //        adding New Bill Master
            $sql="insert into sale_master (
                   sale_master_id
                  ,memo_number
                  ,customer_id
                  ,employee_id
                  ,sale_date
                  ,roundedOff
                  ,grand_total
                ) VALUES (?,?,?,?,?,?,?)";
            $result=$this->db->query($sql,array(
                $sale_master_id
                ,$memo_number
            ,$saleMaster->customer_id
            ,$this->session->userdata('person_id')
            ,to_sql_date($saleMaster->sale_date)
            ,$saleMaster->roundedOff
            ,$saleMaster->grand_total

            ));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }
            // adding bill_master completed
            //adding bill details
            $sql="insert into sale_details (
               sale_details_id
              ,sale_master_id
              ,product_id
              ,quantity
              ,tin_to_quintal
              ,rate
              ,sgst_rate
              ,cgst_rate
              ,igst_rate
              ,sgst
              ,cgst
              ,igst
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

            foreach($sale_details_list as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        $sale_master_id . '-' . ($index+1)
                    , $sale_master_id
                    ,$row->product_id
                    ,$row->quantity
                    ,$row->tin_to_quintal
                    ,$row->rate
                    ,$row->sgst_rate
                    ,$row->cgst_rate
                    ,$row->igst_rate
                    ,$row->sgst
                    ,$row->cgst
                    ,$row->igst
                    )
                );

            }
            if($result==FALSE){
                throw new Exception('error adding bill_details');
            }
            //INSERT INTO STOCK_TRANSACTION TABLE//
            $sql="insert into stock_transaction (
                   stock_transaction_id
                  ,sale_master_id
                  ,product_id
                  ,inward
                  ,outward
                  ,memo_number
                  ,transaction_type_id
                ) VALUES (?,?,?,0,?,?,1)";

            foreach($sale_details_list as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        'ST'.'-'.$sale_master_id . '-' . ($index+1)
                    , $sale_master_id
                    ,$row->product_id
                    ,$row->quantity
                    ,$memo_number
                    )
                );

            }
            if($result==FALSE){
                throw new Exception('error adding bill_details');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['sale_master_id']=$sale_master_id;
            $return_array['memo_number']=$memo_number;
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
            $return_array['error']=create_log($err->code,$this->db->last_query(),'sale_model','insert_opening',"log_file.csv");
            // $return_array['error']=mysql_error;
            $return_array['success']=0;
            $return_array['message']=$err->message;
            $this->db->query("ROLLBACK");
        }
        return (object)$return_array;
    }//end of function

    function select_all_sale(){
        $sql="select 
            max(sale_details.sale_master_id) as sale_master_id
            ,person.person_name
            ,person.mobile_no
            , max(sale_details.product_id) as product_id
            ,max(sale_details.quantity) as quantity
            ,max(sale_details.rate) as rate
            , max(sale_details.sgst_rate) as sgst_rate
            , max(sale_details.cgst_rate) as cgst_rate
            , max(sale_details.igst_rate) as igst_rate
            , max(sale_details.sgst) as sgat
            , max(sale_details.cgst) as cgst
            , max(sale_details.igst) as igst
            , max(sale_master.memo_number) as memo_number
            , max(sale_master.customer_id) as customer_id
            , max(sale_master.employee_id) as employee_id
            , DATE_FORMAT(sale_date, \"%d/%m/%Y\") as sale_date
            ,date_format(sale_master.sale_date,'%M') as sale_month
            , max(sale_master.roundedOff) as roundedOff
            , max(sale_master.grand_total) as grand_total
            from sale_details 
            inner join sale_master ON sale_master.sale_master_id = sale_details.sale_master_id
            inner join person on sale_master.customer_id = person.person_id
            group by sale_details.sale_master_id order by sale_master.record_time desc";
        $result = $this->db->query($sql,array());
        return $result;
    }


    function select_sale_details_by_sale_master_id($sale_master_id){
        $sql="select sale_details_id
            , sale_master_id
            , product_id
            , quantity
            , rate, sgst
            , cgst
            , igst
            ,(quantity*rate) as amount from (select * from sale_details) as table1 where sale_master_id=?";
        $result=$this->db->query($sql,array($sale_master_id));
        if($result==null){
            return null;
        }else{
            return $result;
        }
    }


    function update_sale_master_details($saleMaster,$saleDetailsList){
        $return_array=array();
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();
            // adding New Bill Master

            $sql="update sale_master set customer_id=?
                  ,employee_id=?
                  ,sale_date=?
                  , roundedOff=?
                  , grand_total=? where sale_master_id=?";
            $result=$this->db->query($sql,array(
                $saleMaster->customer_id
            ,$this->session->userdata('person_id')
            ,to_sql_date($saleMaster->sale_date)
            ,$saleMaster->roundedOff
            ,$saleMaster->grand_total
            ,$saleMaster->sale_master_id
            ));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }
            // adding bill_master completed
            //adding bill details

            $sql="delete from sale_details where sale_master_id=?";
            $result=$this->db->query($sql,array($saleMaster->sale_master_id));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }else{
                $return_array['previous details']='deleted';
            }


            $sql="insert into sale_details (
                   sale_details_id
                  ,sale_master_id
                  ,product_id
                  ,quantity
                  ,rate
                  ,sgst_rate
                  ,cgst_rate
                  ,igst_rate
                  ,sgst
                  ,cgst
                  ,igst
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?)";

            foreach($saleDetailsList as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        $saleMaster->sale_master_id . '-' . ($index+1)
                    , $saleMaster->sale_master_id
                    ,$row->product_id
                    ,$row->quantity
                    ,$row->rate
                    ,$row->sgst_rate
                    ,$row->cgst_rate
                    ,$row->igst_rate
                    ,$row->sgst
                    ,$row->cgst
                    ,$row->igst
                    )
                );

            }
            if($result==FALSE){
                throw new Exception('error adding bill_details');
            }
            //STOCK TRANSACTION//
            $sql="delete from stock_transaction where sale_master_id=?";
            $result=$this->db->query($sql,array($saleMaster->sale_master_id));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }else {
                $return_array['previous stock'] = 'deleted';
            }

            $sql="insert into stock_transaction (
                   stock_transaction_id
                  ,sale_master_id
                  ,product_id
                  ,inward
                  ,outward
                  ,transaction_type_id
                ) VALUES (?,?,?,0,?,1)";

            foreach($saleDetailsList as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        'ST'.'-'.$saleMaster->sale_master_id . '-' . ($index+1)
                    , $saleMaster->sale_master_id
                    ,$row->product_id
                    ,$row->quantity
                    )
                );

            }
            if($result==FALSE){
                throw new Exception('error adding bill_details');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['message']='Successfully updated';
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


    function select_mustard_oil_bill_details_by_sale_master_id($sale_master_id){
        $sql="select *
                ,round((quantity * rate),2) as gross_value
                ,round((sgst + cgst + igst),2) as total_tax
                ,round((quantity * rate) + (sgst + cgst + igst),2) as total_amount
                from (select 
                sale_details.sale_master_id
                , sale_master.memo_number
                , sale_master.customer_id
                ,customer.person_name as customer
                ,employee.person_name as employee
                ,customer.gst_number
                ,customer.address1
                ,states.state_gst_code
                ,states.state_name
                , sale_details.product_id
                , hsn_table.hsn_code
                , sale_details.quantity
                , sale_details.tin_to_quintal
                , sale_details.rate
                , sale_details.sgst_rate * 100 as sgst_rate
                , sale_details.cgst_rate *100 as cgst_rate
                , sale_details.igst_rate *100 as igst_rate
                , sale_details.sgst
                , sale_details.cgst
                , sale_details.igst
                , sale_master.roundedOff
                , sale_master.grand_total
                , sale_master.sale_date
                from sale_details
                inner join sale_master ON sale_master.sale_master_id = sale_details.sale_master_id
                inner join product ON product.product_id = sale_details.product_id
                inner join hsn_table ON hsn_table.hsn_serial_no = product.hsn_serial_no 
                inner join person as customer on sale_master.customer_id = customer.person_id
                inner join person as employee on employee.person_id=sale_master.employee_id
                inner join states on states.state_id=customer.state_id
                where sale_details.product_id=18 and sale_master.sale_master_id=?) as table1";
        $result = $this->db->query($sql,array($sale_master_id));
        if($result==null){
            $return_array['success']='0';
        }else{
            return $result;
        }
    }

}//final

?>