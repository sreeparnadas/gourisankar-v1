<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('student_model');
        //$this -> is_logged_in();
    }
    function is_logged_in() {
		$is_logged_in = $this -> session -> userdata('is_logged_in');
		if (!isset($is_logged_in) || $is_logged_in != 1) {
			echo 'you have no permission to use developer area'. '<a href="">Login</a>';
			die();
		}
	}


    public function angular_view_student(){
        ?>
        <div class="d-flex">
            <div class="p-2 my-flex-item col-12">
                <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
                    <!-- Brand -->
                    <a class="navbar-brand" href="#">Logo</a>

                    <!-- Links -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Link 1</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#!staffArea">Back <i class="fas fa-home"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>

        </div>
        <div class="d-flex col-12">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="vendor-div">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link " data-toggle="tab" href="#" role="tab" ng-click="setTab(1)"><i class="fas fa-user-graduate"></i></i> New Student</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click="setTab(2)"><i class="fa fa-heart"></i> Product List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click="setTab(3)"><i class="fa fa-envelope"></i>About Product</a>
                    </li>
                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="my-tab-1">
                            <form name="student_form">
                                <div class="row col-12">
                                <div class="col-6 left-div">
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Student Code <span class="text-danger">*</span></label>
                                        <input class="col-6" type="text" ng-model="student.student_code" placeholder="Student Code" required>
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Student Name <span class="text-danger">*</span></label>
                                        <input class="col-6" type="text" placeholder="Student Name" ng-model="student.student_name" required>
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Date of Birth <span class="text-danger">*</span></label>
                                        <select class=""
                                                ng-change="dobChange()"
                                                ng-model="student.dobDay"
                                                ng-options="k*1+1 as v for (k,v) in huiDays"
                                            ></select>
                                        <select class=""
                                                ng-change="dobChange()"
                                                ng-model="student.dobMonth"
                                                ng-options="k*1+1 as v for (k,v) in huiMonths"
                                        ></select>
                                        <select class=""
                                                ng-change="dobChange()"
                                                ng-model="student.dobYear"
                                                ng-options="v as v for (k,v) in dobYears">
                                         ></select>
                                        <input ng-show="true"  type="text" class="col-2" ng-model="student.dob">

                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Date of Admission</label>
                                        <select class=""
                                                ng-change="dobChange()"
                                                ng-model="student.doaDay"
                                                ng-options="k*1+1 as v for (k,v) in huiDays"
                                        ></select>
                                        <select class=""
                                                ng-change="dobChange()"
                                                ng-model="student.doaMonth"
                                                ng-options="k*1+1 as v for (k,v) in huiMonths"
                                        ></select>
                                        <select class=""
                                                ng-change="dobChange()"
                                                ng-model="student.doaYear"
                                                ng-options="v as v for (k,v) in dobYears">
                                            ></select>
                                        <input ng-show="true"  type="text" class="col-2" ng-model="student.doa">

                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Sex <span class="text-danger">*</span></label>
                                        <select class="col-3"
                                                ng-model="student.sex"
                                                ng-options="v as v for (k,v) in sexes"
                                        ></select>
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Father's Name</label>
                                        <input type="text" class="col-6" ng-model="student.father_name">
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Mother's Name</label>
                                        <input type="text" class="col-6" ng-model="student.mother_name">
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Contact No 1 <span class="text-danger">*</span></label>
                                        <input type="text" class="col-6" ng-model="student.contact_1">
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Contact No 2</label>
                                        <input type="text" class="col-6" ng-model="student.contact_2">
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Aadhar No</label>
                                        <input type="text" class="col-6" ng-model="student.aadhar_number">
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Religion</label>
                                        <select class="col-3"
                                                ng-change="getCast(student.religion)"
                                                ng-model="student.religion"
                                                ng-options="religion as religion.religion_name for religion in allReligion"
                                        ></select>
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Cast</label>
                                        <select class="col-3"
                                                ng-model="student.cast"
                                                ng-options="cast as cast.cast_name for cast in allCast"
                                        ></select>
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Mother Tongue</label>
                                        <select class="col-3"
                                                ng-model="student.mothertongue"
                                                ng-options="motherTongue as motherTongue.tongue_name for motherTongue in allMotherTongue">
                                            ></select>
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Email</label>
                                        <input type="text" name="myField"  maxlength="5" class="col-6" ng-model="student.email_id">
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Pan No</label>
                                        <input type="text" class="col-3" name="pan" ng-model="student.pan_number" ng-pattern="/^[a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}$/">
                                        <div class="col-5" ng-messages="student_form.pan.$error" role="alert">
                                            <div class="text-danger" ng-message-exp="['pattern']">Check format AAAAA9999A</div>
                                        </div>
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Blood Group</label>
                                        <select class="col-2"
                                                ng-model="student.bloodgroup"
                                                ng-options="bloodGroup as bloodGroup.blood_group_name for bloodGroup in allBloodGroup">
                                          ></select>
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Is Any Sister in School?</label>
                                        <select class="col-2"
                                                ng-model="student.sibling"
                                                ng-options="k*1 as v for (k,v) in options"
                                        ></select>
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Is Mentally Disable?</label>
                                        <select class="col-2"
                                                ng-model="student.is_mental_disable"
                                                ng-options="k*1 as v for (k,v) in options"
                                        ></select>
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Is Physically Challenged?</label>
                                        <select class="col-2"
                                                ng-model="student.is_physical_disable"
                                                ng-options="k*1 as v for (k,v) in options"
                                        ></select>
                                    </div>
                                    <div class="row col-12"></div>
                                </div>
                                <div class="col-6 right-div">
                                    <div class="row col-12 mt-2">
                                        <label class="col-4">Address <span class="text-danger">*</span></label>
                                        <input class="col-6" type="text" name="address" ng-model="student.address" required>
                                        <div class="col-2" ng-messages="student_form.address.$error" role="alert">
                                            <div ng-message-exp="['required']">This is required</div>
                                        </div>
                                    </div>

                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Area</label>
                                        <input class="col-6" type="text" ng-model="student.area">
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">City</label>
                                        <input class="col-6" type="text" ng-model="student.city">
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">PIN</label>
                                        <input class="col-4" type="text" ng-model="student.pin">
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">District</label>
                                        <select class="col-6"
                                                ng-model="student.district"
                                                ng-options="district as district.district_name for district in allDistricts"
                                         ></select>
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">State</label>
                                        <select class="col-6"
                                                ng-model="student.state"
                                                ng-options="state as state.state_name for state in allStates"
                                            ></select>
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Bank</label>
                                        <select class="col-6"
                                                ng-change="getBranches(student.bank)"
                                                ng-model="student.bank"
                                                ng-options="bank as bank.bank_name for bank in allBanks"
                                        ></select>
                                    </div>
                                    <div class="row col-12 mt-1">
                                        <label class="col-4">Branch</label>
                                        <select class="col-4"
                                                ng-model="student.branch"
                                                ng-options="branch as branch.branch_name for branch in allBranches"
                                        ></select>
                                        <div class="col-4 text-success">IFSC: {{student.branch.ifsc}}</div>
                                    </div>
                                    <div class="row col-12">
                                        <div class="row col-6 mt-1">
                                            <input type="submit" value="Save" ng-click="saveStudent(student)" ng-disabled="student_form.$invalid">{{student_form.$invalid}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <div class="row col-12">

                                </div>
                            </form>
                        </div> <!--//End of my tab1//-->
                    </div>
                    <div ng-show="isSet(2)">
                        <div id="my-tab-2">
                            This is tab 2
                        </div>
                    </div>
                    <div ng-show="isSet(3)">
                        <div id="my-tab-3">
                            <pre>student={{student | json}}</pre>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function get_states(){
        $result=$this->student_model->select_states()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array);
    }

    function get_districts_by_state(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        //echo $post_data[];
        $result=$this->student_model->select_district_by_state_id($post_data['state_id'])->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
    function get_religion(){
        $result=$this->student_model->select_religion()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
    function get_cast(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->student_model->select_cast($post_data['religion_id'])->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
    function get_bank(){
        //$post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->student_model->select_bank()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
    function get_bank_branches(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->student_model->select_bank_branches_by_bank_id($post_data['bank_id'])->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
    function get_motherTongue(){
        //$post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->student_model->select_motherTongue()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
    function get_blood_group(){
        //$post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->student_model->select_blood_group()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
}
?>