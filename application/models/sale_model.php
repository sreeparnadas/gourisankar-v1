<?php
class sale_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }


    function select_number_of_oiltin_for_sale($oil_tin_id){
        $sql="select no_of_oil_tin_for_sale(?) as total";
        $result=$this->db->query($sql,array($oil_tin_id));
        if($result==null){
            return null;
        }else{
            return $result->row();
        }
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
                  ,bill_type
                ) VALUES (?,?,?,?,?,?,?,1)";
            $result=$this->db->query($sql,array(
                $sale_master_id
                ,$memo_number
            ,$saleMaster->customer_id
            ,$this->session->userdata('person_id')
            ,($saleMaster->sale_date)
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
              ,no_of_container
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
                    ,$row->tin_to_quintal
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
            //INSERT INTO STOCK_TRANSACTION TABLE//
            $sql="insert into stock_transaction (
                   stock_transaction_id
                  ,sale_master_id
                  ,product_id
                  ,inward
                  ,outward
                  ,memo_number
                  ,transaction_date
                  ,transaction_type_id
                ) VALUES (?,?,?,0,?,?,?,1)";

            foreach($sale_details_list as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        'ST'.'-'.$sale_master_id . '-' . ($index+1)
                    , $sale_master_id
                    ,$row->product_id
                    ,$row->quantity
                    ,$memo_number
                    ,$saleMaster->sale_date
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
        ,max(person.person_name) as person_name
        ,max(person.mobile_no) as mobile_no
        ,max(sale_details.product_id) as product_id
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
        , DATE_FORMAT(max(sale_date), '%d/%m/%Y') as display_sale_date
        , max(sale_date) as sale_date
        ,date_format(max(sale_master.sale_date),'%M') as sale_month
        , max(sale_master.roundedOff) as roundedOff
        , max(sale_master.grand_total) as grand_total
        ,max(sale_master.bill_type) as bill_type
        ,max(product_name) as bill_type_name
        from sale_details 
        inner join sale_master ON sale_master.sale_master_id = sale_details.sale_master_id
        inner join person on sale_master.customer_id = person.person_id
        inner join product on sale_details.product_id=product.product_id
        group by sale_details.sale_master_id,sale_details.product_id,sale_master.record_time order by sale_master.record_time desc";
        $result = $this->db->query($sql,array());
        return $result;
    }


    function select_sale_details_by_sale_master_id($sale_master_id){
        $sql="select sale_details_id
            , sale_master_id
            , product_id
            , quantity
            , rate
            ,no_of_container
            , sgst
            , cgst
            , igst
            ,no_of_container * rate as amount 
             from (select * from sale_details where sale_master_id=?) as table1";
        $result=$this->db->query($sql,array($sale_master_id));
        if($result==null){
            return null;
        }else{
            return $result;
        }
    }

    function select_sale_mont_wise(){
        $sql="select sale_month,month_val,sum(grand_total)/1000 as month_wise_sale  from(
                select DATE_FORMAT(sale_date, \"%M\") as sale_month, month(sale_date) as month_val,grand_total from sale_master
                ) as table1 group by sale_month,month_val order by month_val";
        $result=$this->db->query($sql,array());
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
            ,$saleMaster->sale_date
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
                  ,no_of_container
                  ,rate
                  ,sgst_rate
                  ,cgst_rate
                  ,igst_rate
                  ,sgst
                  ,cgst
                  ,igst
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

            foreach($saleDetailsList as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        $saleMaster->sale_master_id . '-' . ($index+1)
                    , $saleMaster->sale_master_id
                    ,$row->product_id
                    ,$row->tin_to_quintal
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
                  ,transaction_date
                  ,memo_number
                  ,transaction_type_id
                ) VALUES (?,?,?,0,?,?,?,1)";

            foreach($saleDetailsList as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        'ST'.'-'.$saleMaster->sale_master_id . '-' . ($index+1)
                    , $saleMaster->sale_master_id
                    ,$row->product_id
                    ,$row->quantity
                    ,$saleMaster->sale_date
                    ,$saleMaster->memo_number
                    )
                );

            }
            if($result==FALSE){
                throw new Exception('error adding bill_details');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['sale_master_id']= $saleMaster->sale_master_id;
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


    function select_mustard_oil_bill_details_by_sale_master_id($sale_master_id,$product_id){
        $sql="select *
        ,if(product_id=3,round((no_of_container * rate),2),round((quantity * rate),2)) as gross_value
        ,round((sgst + cgst + igst),2) as total_tax
        ,if(product_id=3,round((no_of_container * rate) + (sgst + cgst + igst),2),round((quantity * rate) + (sgst + cgst + igst),2)) as total_amount
        from (select 
        sale_details.sale_master_id
        , sale_master.memo_number
        , sale_master.customer_id
        ,customer.person_name as customer
        ,employee.person_name as employee
        ,customer.gst_number
        ,customer.address
        ,states.state_gst_code
        ,states.state_name
        , sale_details.product_id
        , hsn_table.hsn_code
        , sale_details.quantity
        , sale_details.no_of_container
        , sale_details.rate
        , sale_details.sgst_rate * 100 as sgst_rate
        , sale_details.cgst_rate *100 as cgst_rate
        , sale_details.igst_rate *100 as igst_rate
        , sale_details.sgst
        , sale_details.cgst
        , sale_details.igst
        , sale_master.roundedOff
        , sale_master.grand_total
        , DATE_FORMAT(sale_master.sale_date, '%d/%m/%Y') as sale_date
        from sale_details
        inner join sale_master ON sale_master.sale_master_id = sale_details.sale_master_id
        inner join product ON product.product_id = sale_details.product_id
        inner join hsn_table ON hsn_table.hsn_serial_no = product.hsn_serial_no 
        inner join person as customer on sale_master.customer_id = customer.person_id
        inner join person as employee on employee.person_id=sale_master.employee_id
        inner join states on states.state_id=customer.state_id
        where sale_details.product_id= ? and sale_master.sale_master_id=?) as table1";
        $result = $this->db->query($sql,array($product_id,$sale_master_id));
        if($result==null){
            $return_array['success']='0';
        }else{
            return $result;
        }
    }



    function insert_new_oil_cake_sale($saleMaster,$sale_details_list){
        
        $return_array=array();
        $financial_year=get_financial_year();
        try{
            $this->db->query("START TRANSACTION");
            $this->db->trans_start();
            //insert into maxtable
            $sql="insert into maxtable (subject_name, current_value, financial_year,prefix)
            	values('sale oil cake',1,?,'SLOC')
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
                  ,bill_type
                ) VALUES (?,?,?,?,?,?,?,2)";
            $result=$this->db->query($sql,array(
                $sale_master_id
            ,$memo_number
            ,$saleMaster->customer_id
            ,$this->session->userdata('person_id')
            ,($saleMaster->sale_date)
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
              ,no_of_container
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
                    ,$row->packet_to_quintal
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
            //INSERT INTO STOCK_TRANSACTION TABLE//
            $sql="insert into stock_transaction (
                   stock_transaction_id
                  ,sale_master_id
                  ,product_id
                  ,inward
                  ,outward
                  ,memo_number
                  ,transaction_date
                  ,transaction_type_id
                ) VALUES (?,?,?,0,?,?,?,1)";

            foreach($sale_details_list as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        'ST'.'-'.$sale_master_id . '-' . ($index+1)
                    , $sale_master_id
                    ,$row->product_id
                    ,$row->quantity
                    ,$memo_number
                    ,($saleMaster->sale_date)
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

    function update_oil_cake($oil_cake_master,$oil_cake_details_list){
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
                $oil_cake_master->customer_id
            ,$this->session->userdata('person_id')
            ,($oil_cake_master->sale_date)
            ,$oil_cake_master->roundedOff
            ,$oil_cake_master->grand_total
            ,$oil_cake_master->sale_master_id
            ));
            $return_array['dberror']=$this->db->error();

            if($result==FALSE){
                throw new Exception('error adding sale master');
            }
            // adding bill_master completed
            //adding bill details

            $sql="delete from sale_details where sale_master_id=?";
            $result=$this->db->query($sql,array($oil_cake_master->sale_master_id));
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
                  ,no_of_container
                  ,rate
                  ,sgst_rate
                  ,cgst_rate
                  ,igst_rate
                  ,sgst
                  ,cgst
                  ,igst
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

            foreach($oil_cake_details_list as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        $oil_cake_master->sale_master_id . '-' . ($index+1)
                    , $oil_cake_master->sale_master_id
                    ,$row->product_id
                    ,$row->packet_to_quintal
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
            $result=$this->db->query($sql,array($oil_cake_master->sale_master_id));
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
                  ,transaction_date
                  ,memo_number
                  ,transaction_type_id
                ) VALUES (?,?,?,0,?,?,?,1)";

            foreach($oil_cake_details_list as $index=>$value){
                $row=(object)$value;
                $result = $this->db->query($sql, array(
                        'ST'.'-'.$oil_cake_master->sale_master_id . '-' . ($index+1)
                    , $oil_cake_master->sale_master_id
                    ,$row->product_id
                    ,$row->quantity
                    ,($oil_cake_master->sale_date)
                    ,$oil_cake_master->memo_number
                    )
                );

            }
            if($result==FALSE){
                throw new Exception('error adding bill_details');
            }
            $this->db->trans_complete();
            $return_array['success']=1;
            $return_array['sale_master_id']= $oil_cake_master->sale_master_id;
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



    function select_excel_data_for_mustard_oil($start_date,$end_date){
        $sql="select sale_master_id
        ,max(sale_date) as sale_date
        ,max(memo_number) as memo_number
        ,max(buyer_name) as buyer_name,
        max(gst_number) as gst_number
        ,sum(no_of_container) as tin
        ,sum(quantity) as qty_in_quintal
        ,max(rate) as rate
        ,sum(gross_value) as gross_value
        ,sum(sgst_rate) as sgst_rate,sum(sgst) as sgst,sum(cgst_rate) as cgst_rate,sum(cgst) as cgst
                ,sum(igst_rate) as igst_rate,sum(igst) as igst,
                sum(sgst)+sum(cgst)+sum(igst) as total_tax,
                sum(gross_value)+sum(sgst)+sum(cgst)+sum(igst) as total_amount
         from (SELECT sale_master.sale_master_id,sale_date,memo_number,customer_id,person.person_name as buyer_name,person.gst_number,employee_id,record_time,sale_details.product_id,product_name,
        quantity,no_of_container,rate,(rate*no_of_container) as gross_value,sgst_rate,cgst_rate,igst_rate,sgst,cgst,igst FROM sale_master
        inner join sale_details on sale_master.sale_master_id=sale_details.sale_master_id
        inner join product on sale_details.product_id=product.product_id
        inner join person on person.person_id = sale_master.customer_id
        where sale_details.product_id=3 and sale_date between ? and ?) table1
        group by sale_master_id WITH ROLLUP;";
        $result=$this->db->query($sql,array($start_date,$end_date));
        if($result==null){
            return null;
        }else{
            return $result;
        }
    }

    function select_excel_data_for_oil_cake($start_date,$end_date){
        $sql="select sale_master_id
        ,max(sale_date) as sale_date
        ,max(memo_number) as memo_number
        ,max(buyer_name) as buyer_name,
        max(gst_number) as gst_number
        ,sum(no_of_container) as packet
        ,sum(quantity) as qty_in_quintal
        ,max(rate) as rate
        ,sum(gross_value) as gross_value
        ,sum(sgst_rate) as sgst_rate,sum(sgst) as sgst,sum(cgst_rate) as cgst_rate,sum(cgst) as cgst
                ,sum(igst_rate) as igst_rate,sum(igst) as igst,
                sum(sgst)+sum(cgst)+sum(igst) as total_tax,
                sum(gross_value)+sum(sgst)+sum(cgst)+sum(igst) as total_amount
         from (SELECT sale_master.sale_master_id,sale_date,memo_number,customer_id,person.person_name as buyer_name,person.gst_number,employee_id,record_time,sale_details.product_id,product_name,
        quantity,no_of_container,rate,(rate*no_of_container) as gross_value,sgst_rate,cgst_rate,igst_rate,sgst,cgst,igst FROM sale_master
        inner join sale_details on sale_master.sale_master_id=sale_details.sale_master_id
        inner join product on sale_details.product_id=product.product_id
        inner join person on person.person_id = sale_master.customer_id
        where sale_details.product_id=2 and sale_date between ? and ?) table1
        group by sale_master_id WITH ROLLUP;";
        $result=$this->db->query($sql,array($start_date,$end_date));
        if($result==null){
            return null;
        }else{
            return $result;
        }
    }

}//final

?>