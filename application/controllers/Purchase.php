<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('student_model');
        $this -> load -> model('purchase_model');
        //$this -> is_logged_in();
    }
    function is_logged_in() {
		$is_logged_in = $this -> session -> userdata('is_logged_in');
		if (!isset($is_logged_in) || $is_logged_in != 1) {
			echo 'you have no permission to use developer area'. '<a href="">Login</a>';
			die();
		}
	}


    public function angular_view_purchase(){
        ?>
        <style type="text/css">
            .td-input-left{
                padding: 0.175rem !important;
                margin-left: 0px;
                margin-right: 0px;
                text-align: right;
            }

            .td-input-right{
                padding: 0.175rem !important;
                margin-left: 0px;
                margin-right: 0px;
                text-align: right;
            }

            #purchase-table th, #purchase-table tr td{
                border: 0;
            }
            #panel-heading{
                margin-left: 0px !important;
                padding-left: 0px !important;
            }
            md-input-container label{
                color: red;
            }

           
        </style>
    <div class="d-flex col-12" ng-include="'application/views/header.html'"></div>


        <div class="d-flex col-12">
            <div class="col-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link " data-toggle="tab" href="#" role="tab" ng-click="setTab(1)"><i class="fas fa-user-graduate"></i></i> New Purchase</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click="setTab(2)"><i class="fa fa-heart"></i> Purchase List</a>
                    </li>
                    <li class="nav-item" ng-show="false">
                        <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click="setTab(3)"><i class="fa fa-envelope"></i>About Product</a>
                    </li>
                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="my-tab-1">
                            <div class="bg-gray-2 border border-secondary">
                                <form name="purchaseForm" class="form-horizontal" id="purchaseForm">
                                    <div class="card">
                                        <div class="d-flex card-header pt-0 pb-0">
                                            <div class="mr-auto">Enter vendor details below</div>
                                            <div class=""><a  href="#" ng-show="hideVendorDetails" ng-click="hideVendorDetailsDiv(false)"><i class="fa fa-plus-square" aria-hidden="true"></i></a></div>
                                            <div class=""><a  href="#" ng-show="!hideVendorDetails" ng-click="hideVendorDetailsDiv(true)"><i class="fa fa-minus-square" aria-hidden="true"></i></a></div>
                                        </div>
                                        <div class="card-body justify-content-center pt-0 pb-0 bg-gray-4" ng-hide="hideVendorDetails">
                                            <div class="row d-flex col-12 bg-gray-4 mt-0">
                                                <div class="col-4 bg-gray-4">
                                                    <div class="d-flex col-12">
                                                        <div class="col-6">Vendor</div>
                                                        <div class="6">
                                                            <select ng-disabled="purchaseDetailsDataList.length"
                                                                    class="form-control td-input-left"
                                                                    data-ng-model="purchaseMaster.vendor"
                                                                    data-ng-options="vendor as vendor.company_name for vendor in vendorList" ng-change="setGstFactor();setGst()">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <sapn ng-show="purchaseMaster.vendor.address.length>0">Address: {{purchaseMaster.vendor.address}}</sapn>
                                                        <span ng-show="purchaseMaster.vendor.gst_number.length>0">GST: {{purchaseMaster.vendor.gst_number}}</span>
                                                    </div>
                                                    <div class="d-flex col-12 mt-1 pl-0">
                                                        <label class="col-4">Invoice</label>
                                                        <div class="col-8">
                                                            <input type="text" class=" td-input-right form-control" ng-model="purchaseMaster.invoice_no" />
                                                        </div>
                                                    </div>
                                                    <div class="d-flex col-12 mt-1 pl-0">
                                                        <label class="col-4">Pr. Date</label>
                                                        <div class="col-8">
                                                            <md-datepicker mg-init="purchaseMaster.purchase_date=new Date()" format="dd/mm/yyyy" ng-model="purchaseMaster.purchase_date" required ng-change="onDateChanged();purchaseMaster.purchase_date=changeDateFormat(purchaseMaster.purchase_date)"  md-placeholder="Enter date"></md-datepicker>
                                                            <span>{{purchaseMaster.purchase_date | date:shortDate}}</span>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="col-4 bg-gray-2">
                                                    <div class="d-flex col-12 mt-1">
                                                    <label  class="col-4">Vehicle Fare</label>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control td-input-right capitalizeWord" ng-model="purchaseMaster.vehicle_fare" />
                                                    </div>
                                                </div>
                                                    <div class="d-flex col-12 mt-1">
                                                    <label  class="col-4">Truck No</label>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control td-input-right capitalizeWord" ng-model="purchaseMaster.truck_no" />
                                                    </div>
                                                </div>
                                                    <div class="d-flex col-12 mt-1">
                                                    <label  class="col-4">Bilty No</label>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control td-input-right capitalizeWord" ng-model="purchaseMaster.bilty_no" />
                                                    </div>
                                                </div>
                                                    <div class="d-flex col-12 mt-1 mb-1">
                                                    <label  class="col-4">Transporter</label>
                                                    <div class="col-8">
                                                        <input type="text" class="td-input-right form-control capitalizeWord" ng-model="purchaseMaster.transport_name"/>
                                                    </div>
                                                </div>
                                                     <div class="d-flex col-12 mt-1 mb-1">
                                                    <label  class="col-4">Trans. Mob</label>
                                                    <div class="col-8">
                                                        <input type="text" class="td-input-right form-control capitalizeWord" ng-model="purchaseMaster.transport_mobile"/>
                                                    </div>
                                                </div>
                                                    <div class="d-flex col-12 mt-1 mb-1">
                                                    <label  class="col-4">Licence No</label>
                                                    <div class="col-8">
                                                        <input type="text" class="td-input-right form-control capitalizeWord" ng-model="purchaseMaster.licence_no"/>
                                                    </div>
                                                </div>
                                                </div>
                                                <div class="col-4 bg-gray-4">
                                                    <div class="d-flex col-12 mt-1">
                                                    <label  class="col-4">E-WayBill No</label>
                                                    <div class="col-8">
                                                        <input type="text" class="td-input-right form-control capitalizeWord" ng-model="purchaseMaster.eway_bill_no" />
                                                    </div>
                                                </div>
                                                    <div class="d-flex col-12 mt-1">
                                                    <label  class="col-4">E-WayBill Date</label>
                                                    <div class="col-8">
                                                        <md-datepicker ng-model="purchaseMaster.eway_bill_date" ng-change="onDateChanged();purchaseMaster.eway_bill_date=changeDateFormat(purchaseMaster.eway_bill_date)"  md-placeholder="Enter date"></md-datepicker>
                                                            <span>{{purchaseMaster.eway_bill_date | date:shortDate}}</span>
                                                    </div>
                                                </div>
                                                    <div class="d-flex col-12 mt-1">
                                                    <label  class="col-4">Valid From</label>
                                                    <div class="col-8">
                                                        <md-datepicker ng-model="purchaseMaster.valid_from" ng-change="onDateChanged();purchaseMaster.valid_from=changeDateFormat(purchaseMaster.valid_from)"  md-placeholder="Enter date"></md-datepicker>
                                                        <span>{{purchaseMaster.valid_from | date:shortDate}}</span>
                                                    </div>
                                                </div>
                                                    <div class="d-flex col-12 mt-1">
                                                    <label  class="col-4">Valid To</label>
                                                    <div class="col-8">
                                                        <md-datepicker ng-model="purchaseMaster.valid_to" ng-change="onDateChanged();purchaseMaster.valid_to=changeDateFormat(purchaseMaster.valid_to)"  md-placeholder="Enter date"></md-datepicker>
                                                        <span>{{purchaseMaster.valid_to | date:shortDate}}</span>
                                                    </div>
                                                </div>
                                                    <div class="d-flex col-12 mt-1 mb-1" ng-show="isUpdateable">
                                                    <label  class="col-4">Purchase Id</label>
                                                    <div class="col-8">
                                                        <input type="text" class="td-input-right form-control capitalizeWord" ng-model="purchaseMaster.purchase_master_id" disabled/>
                                                    </div>
                                                <!--<div class="col-2"><a title="Show Bill" href="#"><i class="far fa-file-alt" style="font-size: 40px"></i></a></div>-->
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card" id="purchase-details-card">
                                        <div class="d-flex card-header pt-0 pb-0 bg-gray-2">
                                            <div class="d-flex flex-row col-12 bg-gray-2" id="purchase-details-div">
                                                <!--for product-->
                                                <div class="col-2 mt-3">
                                                    <md-input-container>
                                                        <label>Product</label>
                                                        <md-select ng-model = "purchaseDetails.product" ng-change="gstRateChangeOfProduct();setGst()">
                                                            <md-optgroup label = "Product List">
                                                                <md-option ng-value = "product"
                                                                           ng-repeat = "product in prductList">
                                                                    {{product.product_name}}</md-option>
                                                            </md-optgroup>
                                                        </md-select>
                                                    </md-input-container>
                                                </div>
                                                <div class="col-2 mt-3">
                                                    <md-input-container class = "md-block">
                                                        <label>Qty({{purchaseDetails.product.unit_name}})</label>
                                                        <input required name = "purchaseQuantity" ng-model = "purchaseDetails.quantity" ng-keyup="setAmount();setGst()">
                                                        <div ng-messages = "purchaseForm.purchaseQuantity.$error">
                                                            <div ng-message = "required">This is required.</div>
                                                        </div>
                                                    </md-input-container>
                                                </div>
                                                <div class="col-2 mt-3">
                                                    <md-input-container class = "md-block">
                                                        <label>Rate Per {{purchaseDetails.unit==null?purchaseDetails.product.unit_name:purchaseDetails.unit.unit_name}}</label>
                                                        <input required name = "purchaseRate" ng-model = "purchaseDetails.rate"  ng-keyup="setAmount();setGst()">
                                                        <div ng-messages = "purchaseForm.purchaseRate.$error">
                                                            <div ng-message = "required">This is required.</div>
                                                        </div>
                                                    </md-input-container>
                                                </div>
                                                <div class="col-2 mt-3">
                                                    <md-input-container class = "md-block">
                                                        <label>Amount</label>
                                                        <input class="text-right" required name = "purchaseAmount" ng-readonly="true" value = "{{purchaseDetails.amount | number:2}}">
                                                    </md-input-container>
                                                </div>
                                                <div class="col-1 mt-3" ng-show="purchaseDetails.sgst_rate>0">
                                                    <md-input-container class = "md-block">
                                                        <label>S({{purchaseDetails.sgst_rate*100}}%)</label>
                                                        <input required name = "purchaseSgst" ng-readonly="true" ng-model = "purchaseDetails.sgst">
                                                        <div ng-messages = "purchaseForm.purchaseSgst.$error">
                                                            <div ng-message = "required">This is required.</div>
                                                        </div>
                                                    </md-input-container>
                                                </div>
                                                <div class="col-1 mt-3" ng-show="purchaseDetails.cgst_rate">
                                                    <md-input-container class = "md-block">
                                                        <label>C({{purchaseDetails.cgst_rate*100}}%)</label>
                                                        <input required name = "purchaseCgst" ng-readonly="true" ng-model = "purchaseDetails.cgst">
                                                        <div ng-messages = "purchaseForm.purchaseCgst.$error">
                                                            <div ng-message = "required">This is required.</div>
                                                        </div>
                                                    </md-input-container>
                                                </div>
                                                <div class="col-1 mt-3" ng-show="purchaseDetails.igst_rate">
                                                    <md-input-container class = "md-block">
                                                        <label>I({{purchaseDetails.igst_rate*100}}%)</label>
                                                        <input required name = "purchaseIgst" ng-readonly="true" ng-model = "purchaseDetails.igst">
                                                        <div ng-messages = "purchaseForm.purchaseIgst.$error">
                                                            <div ng-message = "required">This is required.</div>
                                                        </div>
                                                    </md-input-container>
                                                </div>
                                                <div class="col-1 mt-3">
                                                    <button mat-raised-button ng-click="addPurchaseDetailsData(purchaseDetails)" ng-disabled="purchaseDetails.amount<1">Add</button>
                                                     <span class="text-danger" ng-show="isDuplicate">Duplicate data !!</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body justify-content-center pt-0 pb-0 bg-gray-4">
                                            <div class="row d-flex col-12 bg-gray-4 table-responsive">
                                                <table class="table" id="purchase-table">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center">SL</th>
                                                        <th class="text-center">Product</th>
                                                        <th class="text-center">HSN</th>
                                                        <th class="text-center">Quantity</th>
                                                        <th class="text-center">Rate(<i class="fas fa-rupee-sign "></i>)</th>
                                                        <th class="text-center">Value</th>
                                                        <th class="text-center">amt.disc</th>
                                                        <th class="text-center">SGST</th>
                                                        <th>CGST</th>
                                                        <th>IGST</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody ng-repeat="p in purchaseDetailsDataList">
                                                    <tr>
                                                        <td class="text-center">{{$index+1}}</td>
                                                        <td class="text-center">{{p.product.product_name}}</td>
                                                        <td class="text-center">{{p.product.hsn_code}}</td>
                                                        <td class="text-center">{{p.quantity}}&nbsp;{{p.product.unit_name}}</td>
                                                        <td class="text-right">{{p.rate | number}} Per {{p.product.unit_name}}</td>
                                                        <td class="text-right">{{p.amount}}</td>
                                                        <td class="text-right">{{getDiscount() | number:2}}</td>
                                                        <td class="text-right">{{p.sgst | number:2}}</td>
                                                        <td class="text-right">{{p.cgst | number:2}}</td>
                                                        <td class="text-right">{{p.igst | number:2}}</td>
                                                        <td class="text-right">{{p.total | number:2}}</td>
                                                        <td> <a href="#" data-ng-click="removeRow($index)"><span class="glyphicon glyphicon-remove"></span> Remove </a></td>
                                                    </tr>
                                                    </tbody>
                                                    <tfoot ng-show="purchaseDetailsDataList.length">
                                                    <tr>
                                                        <td>Total:</td>
                                                        <td colspan="7" class="text-right">{{purchaseTableFooter[0].totalSgst | number:2}}</td>
                                                        <td class="text-right">{{purchaseTableFooter[0].totalCgst | number:2}}</td>
                                                        <td class="text-right">{{purchaseTableFooter[0].totalIgst | number:2}}</td>
                                                        <td class="text-right">
                                                        {{(purchaseTableFooter[0].totalPurchaseAmount +
                                                        purchaseTableFooter[0].totalSgst +
                                                        purchaseTableFooter[0].totalCgst +
                                                        purchaseTableFooter[0].totalIgst) | number:2}}
                                                        
                                                        </td>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="row d-flex col-12">
                                        <div class="col-4"></div>
                                        <div class="col-4">
                                            <span class="text-success" ng-show="submitStatus">Record successfully added</span>
                                            <span class="text-success" ng-show="updateStatus">Update successful</span>
                                        </div>
                                        <div class="col-4">
                                            <md-button class="md-raised" ng-click="savePurchaseDetails(purchaseMaster,purchaseDetailToSave)" ng-disabled="purchaseDetailsDataList.length<1" ng-show="!isUpdateable">Save</md-button>
                                            <md-button class="md-raised" ng-click="resetPurchaseDetails()" ng-disabled="purchaseForm.$pristine" >Reset</md-button>
                                            <md-button class="md-raised" ng-click="updatePurchaseDetails(purchaseMaster,purchaseDetailToSave)" ng-show="isUpdateable" ng-disabled="purchaseForm.$pristine">Update</md-button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="row d-flex col-12" ng-show="false">
                                <pre>purchaseMaster={{purchaseMaster | json}}</pre>
                                <pre>purchaseDetailsDataList={{purchaseDetailsDataList | json}}</pre>
                            </div>

                        </div> <!--//End of my tab1//-->
                    </div>
                    <div ng-show="isSet(2)">
                        <div id="my-tab-2">
                            <div class="row ">
                                <div class="d-flex">
                                        <div class="col-2"><input type="text" ng-model="searchItem"><span class="glyphicon glyphicon-search"></span> Search  </div>
                                        <div class="col-1"></div>
                                        <div class="col-3"><input type="date" class="form-control" ng-model="start_date" ng-change="changeDateFormat(start_date)"></div>
                                        <div class="col-3"><input type="date" class="form-control" ng-model="end_date" ng-change="changeDateFormat(end_date)"></div>
                                        <div class="col-3 p-0 m-0"><button type="button" class="btn btn-success" ng-click="getPurchaseByDate(start_date,end_date)">Show</button></div>
                                </div>

                                <table cellpadding="0" cellspacing="0" class="table table-bordered">
                                    <tr>
                                        <th>SL></th>
                                        <th ng-click="changeSorting('vendor_id')">ID<i class="glyphicon" ng-class="getIcon('vendor_id')"></i></th>
                                        <th ng-click="changeSorting('vendor_name')">Company Name<i class="glyphicon" ng-class="getIcon('vendor_name')"></i></th>
                                        <th ng-click="changeSorting('vendor_name')">Vendor Name<i class="glyphicon" ng-class="getIcon('vendor_name')"></i></th>
                                        <th ng-click="changeSorting('total_purchase_amount')">Amount<i class="glyphicon" ng-class="getIcon('total_purchase_amount')"></i></th>
                                        <th ng-click="changeSorting('total_purchase_amount')">sgst<i class="glyphicon" ng-class="getIcon('total_purchase_amount')"></i></th>
                                        <th ng-click="changeSorting('total_purchase_amount')">cgst<i class="glyphicon" ng-class="getIcon('total_purchase_amount')"></i></th>
                                        <th ng-click="changeSorting('total_purchase_amount')">igst<i class="glyphicon" ng-class="getIcon('total_purchase_amount')"></i></th>
                                        <th ng-click="changeSorting('purchase_date')">Purchase Date<i class="glyphicon" ng-class="getIcon('purchase_date')"></i></th>
                                        <th ng-click="changeSorting('purchase_month')">Month</th>
                                        <th>Action</th>
                                    </tr>
                                    <tbody ng-repeat="purchase in allPurchaseList | filter : searchItem  | orderBy:sort.active:sort.descending">
                                    <tr ng-class-even="'banana'" ng-class-odd="'bee'">
                                        <td>{{ $index+1}}</td>
                                        <td>{{purchase.purchase_master_id}}</td>
                                        <td>{{purchase.company_name}}</td>
                                        <td>{{purchase.vendor_name}}</td>
                                        <td class="text-right">{{purchase.total_purchase_amount | number:2}}</td>
                                        <td class="text-right">{{purchase.sgst | number:2}}</td>
                                        <td class="text-right">{{purchase.cgst | number:2}}</td>
                                        <td class="text-right">{{purchase.igst | number:2}}</td>
                                        <td class="text-right">{{purchase.purchase_date}}</td>
                                        <td style="padding-left: 20px;" >{{purchase.purchase_month}}</td>
                                        <td style="padding-left: 20px;" ng-click="getPurchaseFromTableForUpdate(purchase)"><a href="#"><i class="fa fa-angle-double-right"></i></a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <div class="d-flex" ng-show="false">
                        <div class="col">
                            <pre>allPurchaseList={{allPurchaseList | json}}</pre>
                        </div>
                        <div class="col"></div>
                    </div>

                       <!-- <md-content  layout-padding layout-margin>
                            <md-datepicker ng-model="myDate" ng-change="onDateChanged()"
                                           md-placeholder="Enter date"></md-datepicker>
                            Date: {{myDate | date:shortDate}}
                        </md-content>


                        <md-switch ng-model = "data.switch1" aria-label = "Switch 1">
                            Switch 1: {{ data.switch1 }}
                        </md-switch>

                        <md-switch ng-model = "data.switch2" aria-label = "Switch 2"
                                   ng-true-value = "'true'" ng-false-value = "'false'" class = "md-warn">
                            Switch 2 (md-warn): {{ data.switch2 }}
                        </md-switch>

                        <md-switch ng-disabled = "true" aria-label = "Disabled switch"
                                   ng-model = "disabledModel">
                            Switch 3 (Disabled)
                        </md-switch>

                        <md-switch ng-disabled = "true" aria-label = "Disabled active switch"
                                   ng-model = "data.switch4">
                            Switch 4 (Disabled, Active)
                        </md-switch>

                        <md-switch class = "md-primary" md-no-ink aria-label = "Switch No Ink"
                                   ng-model = "data.switch5">
                            Switch 5 (md-primary): No Ink
                        </md-switch>

                        <md-switch ng-model = "data.switch6" aria-label = "Switch 6"
                                   ng-change = "onChange(data.switch6)">
                            Switch 6 : {{ message }}
                        </md-switch>  -->
                        
                    </div>
                    <div ng-show="isSet(3)">

                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    function save_new_purchase(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $purchase_master=(object)$post_data['purchase_master'];
        $purchase_details_list=(object)$post_data['purchase_details_list'];
        $result=$this->purchase_model->insert_new_purchase($purchase_master,$purchase_details_list);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
    function update_saved_purchase(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $purchase_master=(object)$post_data['purchase_master'];
        $purchase_details_list=(object)$post_data['purchase_details_list'];
        $result=$this->purchase_model->update_purchase_master_details($purchase_master,$purchase_details_list);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }
    function get_all_purchase(){
        $result=$this->purchase_model->select_all_purchase()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }
    function get_purchase_by_date(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->purchase_model->select_purchase_by_date($post_data['start_date'],$post_data['end_date'])->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }

    function get_purchase_details_by_purchase_master_id(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->purchase_model->select_purchase_details_by_purchase_master_id($post_data['purchase_master_id'])->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }
    function get_purchase_master_by_purchase_master_id(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->purchase_model->select_purchase_master_by_purchase_master_id($post_data['purchase_master_id']);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }
}
?>