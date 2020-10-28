<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('person');
        $this -> load -> model('product_model');
//        $this -> is_logged_in();
    }
    function is_logged_in() {
		$is_logged_in = $this -> session -> userdata('is_logged_in');
		if (!isset($is_logged_in) || $is_logged_in != 1) {
			echo 'you have no permission to use developer area'. '<a href="">Login</a>';
			die();
		}
	}

    function get_inforce_products(){
        $result=$this->product_model->select_inforce_products()->result_array();
        $test=array();
        $k[]=array('id'=>100,'name'=>'xyz');
        $k[]=array('id'=>101,'name'=>'pqr');
//        foreach($result as $row){
//
//            $row['units']=$this->db->query('select * from unit_to_product inner join units on unit_to_product.unit_id = units.unit_id where product_id=?',array($row['product_id']))->result_array();
//            array_push($test,$row);
//        }
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }

    function get_purchaseable_products(){
        $result=$this->product_model->select_purchaseable_products()->result_array();
        $test=array();
        $k[]=array('id'=>100,'name'=>'xyz');
        $k[]=array('id'=>101,'name'=>'pqr');
//        foreach($result as $row){
//
//            $row['units']=$this->db->query('select * from unit_to_product inner join units on unit_to_product.unit_id = units.unit_id where product_id=?',array($row['product_id']))->result_array();
//            array_push($test,$row);
//        }
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
    public function angular_view_product(){
        ?>
                <style type="text/css">
                    .navbar-fixed-top {
                        border: none;
                        background: #ac2925;

                        margin-top: -20px;
                    }
                    .navbar-fixed-top a{
                        color: #a6e1ec;
                    }
                    #vendor-div{
                        margin-top: 0px;
                    }
                    h1{
                        color: blue;
                    }
                    #mySidenav a[ng-click]{
                        cursor: pointer;
                        position: absolute;
                        left: -20px;
                        transition: 0.3s;
                        padding: 15px;
                        width: 140px;
                        text-decoration: none;
                        font-size: 15px;
                        color: white;
                        border-radius: 0 5px 5px 0;
                        background-color: #ac2925;
                    }

                    #mySidenav a[ng-click]:hover {
                        left: 0;
                    }

                    #mySidenav a:hover {
                        left: 0;
                    }
                    #new-vendor {
                        top: 20px;
                        background-color: #4CAF50;
                    }

                    #update-vendor {
                        top: 78px;
                        background-color: #2196F3;
                    }

                    #show-vendor{
                        top: 136px;
                        background-color: #f44336;
                    }
                    #main-working-div h1{
                        color: darkblue;
                    }
                    #vendor-form input{
                        border-radius: 5px;
                    }
                    #vendorForm{
                        margin-top: 10px;
                     }
                     /*input.ng-invalid {*/
                        /*background-color: pink;*/
                    /*}*/
    </style>
    <div class="container-fluid">
    <div class="d-flex col-12" ng-include="'application/views/header.html'"></div>
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="vendor-div">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link " data-toggle="tab" href="#" role="tab" ng-click="setTab(1)"><i class="fa fa-user" ></i> New Product</a>
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
                            <form name="productForm" class="form-horizontal" id="product-form">
                                <div class="row d-flex col-12" ng-style="">
                                    <div class="col-6 bg-gray-2 mt-1 mb-1">
                                        <div class="d-flex col-12 mt-1">
                                            <label  class="col-3">Product name &nbsp;<span class="text-danger">*</span></label>
                                            <div class="col-4">
                                                <input type="text" class="form-control text-right" ng-model="product.product_name" ng-change="product.product_name= (product.product_name | capitalize)" required/>
                                            </div>
                                        </div>
                                        <div class="d-flex col-12 mt-1">
                                            <label  class="col-3">HSN code &nbsp;<span class="text-danger">*</span></label>
                                            <div class="col-4">
                                                <select required
                                                        ng-model="product.hsn_code" ng-change=""
                                                        ng-options="hsn as hsn.hsn_code for hsn in hsnCodesList">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex col-12 mt-1">
                                            <label  class="col-3">GST rate (%) </label>
                                            <div class="col-4">
                                                <span ng-bind="product.hsn_code.gst_rate"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6 bg-gray-3 mt-1 mb-1">
                                        <div class="d-flex col-12 mt-1">
                                            <label  class="col-4">Unit &nbsp;<span class="text-danger">*</span></label>
                                            <div class="col-4">
                                                <select required
                                                        ng-model="product.product_unit"
                                                        ng-options="u as u.unit_name for u in unitsList">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="d-flex col-12 mt-1">
                                            <label  class="col-4">Opening balance &nbsp;<span class="text-danger">*</span></label>
                                            <div class="col-4">
                                                <input type="text" class="form-control text-right" gold-decimal-places ng-model="product.opening_balance" required/>
                                            </div>
                                        </div>

                                        <div class="d-flex col-12 mt-1">
                                            <label  class="col-4">Opening Date &nbsp;<span class="text-danger">*</span></label>
                                            <div class="col-4">
                                                
                                                <md-datepicker mg-init="product.opening_date=new Date()" format="dd/mm/yyyy" ng-model="product.opening_date" required ng-change="product.opening_date=changeDateFormat(product.opening_date)"  md-placeholder="Enter date"></md-datepicker>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="row d-flex col-12">
                                    <div class="col-4"></div>
                                    <div class="col-4">
                                        <span ng-show="productSubmit" class="text-success">Record successfully added</span>
                                        <span ng-show="updateStatus" class="text-success">Update successful</span>
                                    </div>
                                    <div class="col-4">
                                        <input type="button" class="btn btn-outline-primary float-right" id="save-product" ng-click="saveProduct(product)" ng-disabled="((productForm.$invalid) && (!productSubmit))" value="Save" ng-show="!isUpdateable"/>
                                        <input type="button" class="btn btn-outline-primary float-right" id="reset-purchase" ng-click="resetProduct()" value="Reset" ng-disabled=""/>
                                        <input type="button" class="btn btn-outline-primary float-right" id="update-product" ng-click="updateProductByProductId(product)" value="Update" ng-show="isUpdateable" ng-disabled="productForm.$pristine"/>
                                    </div>
                                </div>
                            </form>
                            <div class="row d-flex col-12" ng-show="false">
                                <div class="col-5">
                                    <pre>product = {{product | json}}</pre>
                                    <pre>hsnCodesList = {{hsnCodesList | json}}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/.Panel 1-->
                    <!--Panel 2-->
                    <div ng-show="isSet(2)">
                        <style type="text/css">
                            .bee{
                                background-color: #d9edf7;
                            }
                            .banana{
                                background-color: #c4e3f3;
                            }
                            #vendor-table-div table th{
                                background-color: #1b6d85;
                                color: #a6e1ec;
                                cursor: pointer;
                            }
                            a[ng-click]{
                                cursor: pointer;
                            }

                        </style>
                        <p><input type="text" ng-model="searchItem"><span class="glyphicon glyphicon-search"></span> Search </p>
                        <div id="vendor-table-div">
                            <table cellpadding="0" cellspacing="0" class="table table-bordered">
                                <tr>
                                    <th>SL></th>
                                    <th ng-click="changeSorting('person_id')">Product<i class="glyphicon" ng-class="getIcon('person_id')"></i></th>
                                    <th ng-click="changeSorting('person_name')">Unit<i class="glyphicon" ng-class="getIcon('person_name')"></i></th>
                                    <th ng-click="changeSorting('mobile_no')">HSN Code<i class="glyphicon" ng-class="getIcon('mobile_no')"></i></th>
                                    <th ng-click="changeSorting('address')">Gst Rate<i class="glyphicon" ng-class="getIcon('address1')"></i></th>
                                    <th ng-click="changeSorting('gst_number')">Opening Balance<i class="glyphicon" ng-class="getIcon('gst_number')"></i></th>
                                    <th ng-click="changeSorting('opening_date')">Opening Date<i class="glyphicon" ng-class="getIcon('opening_date')"></i></th>
                                    <th>Edit</th>
                                </tr>
                                <tbody ng-repeat="product in allProductList  | filter : searchItem  | orderBy:sort.active:sort.descending">
                                <tr ng-class-even="'banana'" ng-class-odd="'bee'">
                                    <td>{{ $index+1}}</td>
                                    <td>{{product.product_name}}</td>
                                    <td>{{product.unit_name}}</td>
                                    <td>{{product.hsn_code}}</td>
                                    <td>{{product.gst_rate}}</td>
                                    <td>{{product.opening_balance}}</td>
                                    <td>{{product.opening_date}}</td>
                                    <td ng-click="getProductForUpdateFromTable(product)"><a href="#"><i class="fa fa-angle-double-right"></i></a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                    <!--/.Panel 2-->
                    <!--Panel 3-->
                    <div ng-show="isSet(3)">
                        This is our help area
                    </div>
                    <!--/.Panel 3-->
                </div>
            </div>

        </div>
    </div>

        <?php
    }

    function get_all_units(){
        $result=$this->product_model->select_all_units()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }
    function get_all_hsn_codes(){
        $result=$this->product_model->select_all_hsn_codes()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }


    function save_new_product(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->product_model->insert_new_product($post_data['hsnSerialNo'],$post_data['productName'],$post_data['openingBalance'],$post_data['openingDate'],$post_data['unitId']);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }

    function get_all_product(){
        $result=$this->product_model->select_all_products()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }

    function update_product_by_product_id(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->product_model->update_product((object)$post_data['pr']);
        $report_array['records']=$result;
        echo json_encode($report_array);
    }




}
?>