<?php
class Stock_report_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }



    function select_mseed_report_by_date_to_date($start_date,$end_date){
        $sql="call get_mustard_seed_daily_stock_report(?,?)";
        $result = $this->db->query($sql,array($start_date,$end_date));
        return $result;
    }


    
    function select_moil_report_by_date_to_date($start_date,$end_date){
        $sql="call get_mustard_oil_daily_stock_report(?,?)";
        $result = $this->db->query($sql,array($start_date,$end_date));
        return $result;
    }



    function select_oil_cake_report_by_date_to_date($start_date,$end_date){
        $sql="call get_oil_cake_daily_stock_report (?,?)";
        $result = $this->db->query($sql,array($start_date,$end_date));
        return $result;
    }


}//final

?>