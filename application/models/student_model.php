<?php
class student_model extends CI_Model {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('huiui_helper');
    }

//
    function select_states(){
        $sql="select state_id,state_name from states where project_required";
        $result = $this->db->query($sql,array());
        return $result;
    }
    function select_district_by_state_id($state_id){
        $sql="select district_id,district_name,state_id from districts where state_id=? and project_required=1";
        $result = $this->db->query($sql,array($state_id));
        return $result;
    }
    function select_religion(){
        $sql="select religion_id,religion_name from religion where inforce=1 and display=1";
        $result = $this->db->query($sql,array());
        return $result;
    }
    function select_cast($religion_id){
        if($religion_id!=2)
            $religion_id=1;
        $sql="select * from cast where inforce=1 and religion_id=?";
        $result = $this->db->query($sql,array($religion_id));
        return $result;
    }
    function select_bank(){
        $sql="select bank_id,bank_name from bank where project_required=1 and inforce=1 order by priority, bank_name";
        $result = $this->db->query($sql,array());
        return $result;
    }
    function select_bank_branches_by_bank_id($bank_id){
        $sql="select branch_id
                ,bank_id
                ,branch_address
                ,city
                ,branch_name
                ,ifsc
                ,micr
                ,branch_code from branch where bank_id=? order by priority, branch_name";
        $result = $this->db->query($sql,array($bank_id));
        return $result;
    }
    function select_motherTongue(){
        $sql="select  tongue_id, tongue_name FROM mother_tongue where inforce=1 order by priority";
        $result = $this->db->query($sql,array());
        return $result;
    }
    function select_blood_group(){
        $sql="SELECT  blood_id, blood_group_name FROM blood_group where inforce=1 order by prority";
        $result = $this->db->query($sql,array());
        return $result;
    }
}//final

?>