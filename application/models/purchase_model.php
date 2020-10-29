<?php
class Purchase_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }
    function insert_purchase_master($purchase){
        $return_array=array();
        $financial_year=get_financial_year();
        try{
            $this->db->trans_start();
            //insert into maxtable
            $sql="insert into maxtable (subject_name, current_value, financial_year,prefix)
            	values('Purchase',1,?,'Pur')
				on duplicate key UPDATE id=last_insert_id(id), current_value=current_value+1";
            $result = $this->db->query($sql, array($financial_year));
            if($result==FALSE){
                throw new Exception('Increasing Maxtable for Purchase_master_id');
            }

            //getting from maxtable
            $sql="select * from maxtable where id=last_insert_id()";
            $result = $this->db->query($sql);
            if($result==FALSE){
                throw new Exception('error getting maxtable');
            }
            $purchase_id=$result->row()->prefix.'-'.leading_zeroes($result->row()->current_value,3).'-'.$financial_year;
            $return_array['purchase_id']=$purchase_id;
                $sql="insert into purchase_master (
               purchase_master_id
              ,vendor_id
              ,employee_id
              ,invoice_no
              ,purchase_date
            ) VALUES (?,?,?,?,?)";
            $result=$this->db->query($sql,array(
                $purchase_id
                ,
            ));
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

    }

    function insert_new_purchase($purchaseMaster,$purchaseDetailsList){
        $return_array=array();
        $financial_year=get_financial_year();
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();
            //insert into maxtable
            $sql="insert into maxtable (subject_name, current_value, financial_year,prefix)
            	values('purchase',1,?,'PUR')
				on duplicate key UPDATE id=last_insert_id(id), current_value=current_value+1";
            $result = $this->db->query($sql, array($financial_year));
            if($result==FALSE){
                throw new Exception('Increasing Maxtable for bill_master');
            }

            //getting from maxtable
            $sql="select * from maxtable where id=last_insert_id()";
            $result = $this->db->query($sql);
            if($result==FALSE){
                throw new Exception('error getting maxtable');
            }
            $purchase_master_id=$result->row()->prefix.'-'.leading_zeroes($result->row()->current_value,4).'-'.$financial_year;
            $return_array['purchase_master_id']=$purchase_master_id;


            //        adding New Bill Master
            $sql="insert into purchase_master (
                   purchase_master_id
                  ,vendor_id
                  ,employee_id
                  ,invoice_no
                  ,purchase_date
                  ,eway_bill_no
                  ,eway_bill_date
                  ,vehicle_fare
                  ,truck_no
                  ,bilty_no
                  ,licence_no
                  ,transport_name
                  ,transport_mobile
                  ,roundedOff
                  ,grand_total
                  ,valid_from
                  ,valid_to
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $result=$this->db->query($sql,array(
                $purchase_master_id
            ,$purchaseMaster->vendor_id
            ,$this->session->userdata('person_id')
            ,$purchaseMaster->invoice_no
            ,($purchaseMaster->purchase_date)
            ,$purchaseMaster->eway_bill_no
            ,$purchaseMaster->eway_bill_date
            ,$purchaseMaster->vehicle_fare
            ,$purchaseMaster->truck_no
            ,$purchaseMaster->bilty_no
            ,$purchaseMaster->licence_no
            ,$purchaseMaster->transport_name
            ,$purchaseMaster->transport_mobile
            ,$purchaseMaster->roundedOff
            ,$purchaseMaster->grand_total
            ,$purchaseMaster->valid_from
            ,$purchaseMaster->valid_to
            ));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }
            // adding bill_master completed
            //adding bill details
            $sql="insert into purchase_details (
               purchase_datails_id
              ,purchase_master_id
              ,product_id
              ,unit_id
              ,quantity
              ,rate
              ,sgst_rate
              ,cgst_rate
              ,igst_rate
              ,discount
            ) VALUES (?,?,?,?,?,?,?,?,?,?)";

            foreach($purchaseDetailsList as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        $purchase_master_id . '-' . ($index+1)
                    , $purchase_master_id
                    ,$row->product_id
                    ,$row->unit_id
                    ,$row->quantity
                    ,$row->rate
                    ,$row->sgst_rate
                    ,$row->cgst_rate
                    ,$row->igst_rate
                    ,$row->discount
                    )
                );

            }
            if($result==FALSE){
                throw new Exception('error adding bill_details');
            }
            //INSERT INTO STOCK_TRANSACTION TABLE//
            $sql="insert into stock_transaction (
                   stock_transaction_id
                  ,purchase_master_id
                  ,product_id
                  ,inward
                  ,outward
                  ,transaction_date
                  ,memo_number
                  ,transaction_type_id
                ) VALUES (?,?,?,?,0,?,?,2)";

            foreach($purchaseDetailsList as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        'ST'.'-'.$purchase_master_id . '-' . ($index+1)
                    , $purchase_master_id
                    ,$row->product_id
                    ,$row->quantity
                    ,$purchaseMaster->purchase_date
                    ,''
                    )
                );

            }
            if($result==FALSE){
                throw new Exception('error adding bill_details');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['purchase_master_id']=$purchase_master_id;
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
    function update_purchase_master_details($purchaseMaster,$purchaseDetailsList){
        $return_array=array();
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();
            // adding New Bill Master

            $sql="update purchase_master set
                vendor_id=?
                , employee_id=?
                , invoice_no=?
                , purchase_date=?
                , eway_bill_no=?
                , eway_bill_date=?
                , vehicle_fare=?
                , truck_no=?
                , bilty_no=?
                , licence_no=?
                , transport_name=?
                , transport_mobile=?
                , roundedOff=?
                , grand_total=?
                , valid_from=?
                , valid_to=? where purchase_master_id=?";
            $result=$this->db->query($sql,array(
                $purchaseMaster->vendor_id
            ,$this->session->userdata('person_id')
            ,$purchaseMaster->invoice_no
            ,to_sql_date($purchaseMaster->purchase_date)
            ,$purchaseMaster->eway_bill_no
            ,to_sql_date($purchaseMaster->eway_bill_date)
            ,$purchaseMaster->vehicle_fare
            ,$purchaseMaster->truck_no
            ,$purchaseMaster->bilty_no
            ,$purchaseMaster->licence_no
            ,$purchaseMaster->transport_name
            ,$purchaseMaster->transport_mobile
            ,$purchaseMaster->roundedOff
            ,$purchaseMaster->grand_total
            ,to_sql_date($purchaseMaster->valid_from)
            ,to_sql_date($purchaseMaster->valid_to)
            ,$purchaseMaster->purchase_master_id
            ));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }
            // adding bill_master completed
            //adding bill details

            $sql="delete from purchase_details where purchase_master_id=?";
            $result=$this->db->query($sql,array($purchaseMaster->purchase_master_id));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }else{
                $return_array['previous details']='deleted';
            }


            $sql="insert into purchase_details (
               purchase_datails_id
              ,purchase_master_id
              ,product_id
              ,unit_id
              ,quantity
              ,rate
              ,sgst_rate
              ,cgst_rate
              ,igst_rate
              ,discount
            ) VALUES (?,?,?,?,?,?,?,?,?,?)";

            foreach($purchaseDetailsList as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        $purchaseMaster->purchase_master_id . '-' . ($index+1)
                    , $purchaseMaster->purchase_master_id
                    ,$row->product_id
                    ,$row->unit_id
                    ,$row->quantity
                    ,$row->rate
                    ,$row->sgst_rate
                    ,$row->cgst_rate
                    ,$row->igst_rate
                    ,$row->discount
                    )
                );

            }
            if($result==FALSE){
                throw new Exception('error adding bill_details');
            }
            //STOCK TRANSACTION//
            $sql="delete from stock_transaction where purchase_master_id=?";
            $result=$this->db->query($sql,array($purchaseMaster->purchase_master_id));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }else {
                $return_array['previous stock'] = 'deleted';
            }

            $sql="insert into stock_transaction (
                   stock_transaction_id
                  ,purchase_master_id
                  ,product_id
                  ,unit_id
                  ,inward
                  ,outward
                  ,memo_number
                  ,transaction_type_id
                ) VALUES (?,?,?,?,?,0,?,2)";

            foreach($purchaseDetailsList as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        'ST'.'-'.$purchaseMaster->purchase_master_id . '-' . ($index+1)
                    ,$purchaseMaster->purchase_master_id
                    ,$row->product_id
                    ,$row->unit_id
                    ,$row->quantity
                    ,''
                    )
                );

            }
            if($result==FALSE){
                throw new Exception('error adding bill_details');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['purchase_master_id']=$purchaseMaster->purchase_master_id;
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
    function select_all_purchase(){
        $sql="select
        purchase_master_id,vendor_id, person.person_name as vendor_name,company_name, person.mobile_no ,record_time,purchase_date
        ,purchase_month
        ,max(sgst) as sgst
        ,max(cgst) as cgst
        ,max(igst) as igst
        ,sum(amount+sgst+cgst+igst) as total_purchase_amount
        from (select
        purchase_master.purchase_master_id
        ,purchase_master.vendor_id
        ,purchase_master.record_time
        , DATE_FORMAT(purchase_master.purchase_date, '%d/%m/%Y') as purchase_date
        ,date_format(purchase_master.purchase_date,'%M') as purchase_month
        ,sum(purchase_details.quantity * purchase_details.rate) as amount
        ,sum(purchase_details.quantity * purchase_details.rate * purchase_details.sgst_rate) as sgst
        ,sum(purchase_details.quantity * purchase_details.rate * purchase_details.cgst_rate) as cgst
        ,sum(purchase_details.quantity * purchase_details.rate * purchase_details.igst_rate) as igst
        from purchase_details
        inner join purchase_master ON purchase_master.purchase_master_id = purchase_details.purchase_master_id
        group by purchase_master.purchase_master_id,purchase_master.vendor_id
        ,purchase_master.record_time,purchase_master.purchase_date) as table1
        inner join person on person.person_id=table1.vendor_id 
        group by purchase_master_id,table1.vendor_id,person.person_name,person.company_name,person.mobile_no
        ,table1.record_time,table1.purchase_date,table1.purchase_month order by table1.purchase_master_id desc limit 30";
        $result=$this->db->query($sql,array());
        if($result==null){
            return null;
        }else{
            return $result;
        }
    }

    function select_purchase_by_date($start_date,$end_date){
        $sql="select
        purchase_master_id,vendor_id, person.person_name as vendor_name,company_name, person.mobile_no ,record_time,purchase_date
        ,purchase_month
        ,max(sgst) as sgst
        ,max(cgst) as cgst
        ,max(igst) as igst
        ,sum(amount+sgst+cgst+igst) as total_purchase_amount
        from (select
        purchase_master.purchase_master_id
        ,purchase_master.vendor_id
        ,purchase_master.record_time
        , DATE_FORMAT(purchase_master.purchase_date, '%d/%m/%Y') as purchase_date
        ,date_format(purchase_master.purchase_date,'%M') as purchase_month
        ,sum(purchase_details.quantity * purchase_details.rate) as amount
        ,sum(purchase_details.quantity * purchase_details.rate * purchase_details.sgst_rate) as sgst
        ,sum(purchase_details.quantity * purchase_details.rate * purchase_details.cgst_rate) as cgst
        ,sum(purchase_details.quantity * purchase_details.rate * purchase_details.igst_rate) as igst
        from purchase_details
        inner join purchase_master ON purchase_master.purchase_master_id = purchase_details.purchase_master_id
        where purchase_master.purchase_date between ? and ?
        group by purchase_master.purchase_master_id,purchase_master.vendor_id
        ,purchase_master.record_time,purchase_master.purchase_date) as table1
        inner join person on person.person_id=table1.vendor_id 
        group by purchase_master_id,table1.vendor_id,person.person_name,person.company_name,person.mobile_no
        ,table1.record_time,table1.purchase_date,table1.purchase_month order by table1.purchase_master_id desc";
        $result=$this->db->query($sql,array($start_date,$end_date));
        if($result==null){
            return null;
        }else{
            return $result;
        }
    }







    function select_purchase_details_by_purchase_master_id($purchaseMasterId){
        $sql="select 
            purchase_datails_id
            , purchase_master_id
            , product_id
            , quantity
            , rate
            , sgst_rate
            , cgst_rate
            , igst_rate
            , discount
            ,(rate * quantity) as amount
            ,(rate * quantity * sgst_rate) as sgst
            ,(rate * quantity * cgst_rate) as cgst
            ,(rate * quantity * igst_rate) as igst
            from purchase_details  where purchase_master_id=?";
        $result=$this->db->query($sql,array($purchaseMasterId));
        if($result==null){
            return null;
        }else{
            return $result;
        }
    }
    function select_purchase_master_by_purchase_master_id($purchaseMasterId){
        $sql="select purchase_master_id
            , vendor_id
            , invoice_no
            , purchase_date as purchase_date
            , eway_bill_no
            , eway_bill_date as eway_bill_date
            , vehicle_fare
            , truck_no, bilty_no
            , licence_no
            , transport_name
            , transport_mobile
            , roundedOff
            , grand_total
            , valid_from as valid_from
            , valid_to as valid_to
            from purchase_master where purchase_master_id=?";
        $result=$this->db->query($sql,array($purchaseMasterId));
        if($result==null){
            return null;
        }else{
            return $result->row();
        }
    }


}//final

?>