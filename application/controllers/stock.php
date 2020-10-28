<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('purchase_model');
        $this -> load -> model('stock_model');
        //$this -> is_logged_in();
    }

    public function angular_view_stock_production(){
        ?>
        <style type="text/css">
            .td-input{
                padding: 2px;
                margin-left: 0px;
                margin-right: 0px;
                text-align: right;
            }
            #purchase-table th, #purchase-table tr td{
                border: 0;
            }

        </style>
        <div class="d-flex col-12" ng-include="'application/views/header.html'"></div>
        
        <div class="d-flex col-12">
            <div class="col-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link " data-toggle="tab" href="#" role="tab" ng-click="setTab(1)"><i class="fas fa-user-graduate"></i></i>Seed to Oil</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click="setTab(2)"><i class="fa fa-heart"></i>Oil to Tin</a>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#" role="tab" ng-click="setTab(3);loadAllStocks()"><i class="fa fa-envelope"></i>All Stocks</a>
                    </li>
                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="row my-tab-1">
                            <form name="seedToOilForm" class="form-horizontal" id="seedToOilForm">
                                <div class="row d-flex col-12" ng-style="seedToOilConversion && successCss">
                                    <div class="col-6 bg-gray-2 mt-1 mb-1">
                                        <div class="d-flex col-12 mt-1">
                                            <label  class="col-3">Date <span class="text-danger">*</span></label>
                                            <div class="col-4">
                                                <md-datepicker mg-init="seedToOil.record_date=new Date()" format="mm/dd/yyyy" ng-model="seedToOil.record_date" required  md-placeholder="Enter date"></md-datepicker>
                                                <!--<input type="text" class="form-control" ng-model="seedToOil.record_date"  required/>-->
                                            </div>
                                        </div>
                                        <div class="d-flex col-12 mt-1">
                                            <label  class="col-3">Mustard Seed <span class="text-danger">*</span></label>
                                            <div class="col-4">
                                                <input type="text" class="form-control text-right" ng-model="seedToOil.mustardSeed.mustard_seed_quantity" required="" />
                                            </div>
                                            <div class="col-2 text-light bg-dark">{{mustardSeed.unit_name}}</div>
                                        </div>

                                    </div>
                                    <div class="col-6 bg-gray-3 mt-1 mb-1">
                                        <div class="d-flex col-12 mt-1">
                                            <label  class="col-3">Oil <span class="text-danger">*</span></label>
                                            <div class="col-4">
                                                <input type="text" class="form-control text-right" ng-model="seedToOil.mustardOil.mustard_oil_quantity" required/>
                                            </div>
                                            <div class="col-2 text-light bg-dark">{{mustardOil.unit_name}}</div>
                                        </div>
                                        <div class="d-flex col-12 mt-1">
                                            <label  class="col-3">Oil Cake <span class="text-danger">*</span></label>
                                            <div class="col-4">
                                                <input type="text" class="form-control text-right" ng-model="seedToOil.oilCake.oil_cake_quantity" required/>
                                            </div>
                                            <div class="col-2 text-light bg-dark">{{oilCake.unit_name}}</div>
                                        </div>

                                        <div class="d-flex col-12 mt-1">
                                            <label  class="col-3">Wastage</label>
                                            <div class="col-4">
                                                <input type="text" class="form-control text-right" ng-model="seedToOil.mustardSeed.wastage"/>
                                            </div>
                                            <div class="col-2 text-light bg-dark">{{mustardSeed.unit_name}}</div>
                                        </div>

                                    </div>
                                </div>

                                <div class="row d-flex col-12">
                                    <div class="col-4"></div>
                                    <div class="col-4">
                                        <span ng-show="seedToOilConversion">Record successfully added</span>
                                        <span ng-show="updateStatus">Update successful</span>
                                    </div>
                                    <div class="col-4">
                                        <input type="button" class="btn btn-outline-primary float-right" id="save-purchase" ng-click="saveSeedToOil(seedToOil)" ng-disabled="seedToOilForm.$invalid" value="Save" ng-show="!isUpdateable"/>
                                        <input type="button" class="btn btn-outline-primary float-right" id="reset-purchase" ng-click="resetSeedOilStockDetails()" value="Reset" ng-disabled="purchaseForm.$pristine"/>
                                        <input type="button" class="btn btn-outline-primary float-right" id="update-purchase" ng-click="updatePurchaseDetails(purchaseMaster,purchaseDetailToSave)" value="Update" ng-show="isUpdateable" ng-disabled="purchaseForm.$pristine"/>
                                    </div>
                                </div>
                            </form>
                            <div class="row d-flex col-12" ng-show="false">
                                <div class="col-5">
                                    <pre>mustardSeed={{mustardSeed | json}}</pre>
                                    <pre>prductList={{prductList | json}}</pre>
                                    <pre>reportArray={{reportArray | json}}</pre>
                                </div>
                            </div>

                        </div> <!--//End of my tab1//-->
                    </div>
                    <div ng-show="isSet(2)">
                        <div id="my-tab-2">
                            <form name="oilToTinForm" class="form-horizontal" id="oilToTinForm">
                                <div class="row d-flex col-12" ng-style="blankToOilTinConversion && successCss">
                                    <div class="col-6 bg-gray-2 mt-1 mb-1">
                                        <div class="d-flex col-12 mt-1">
                                            <label  class="col-3">Date <span class="text-danger">*</span></label>
                                            <div class="col-4">
                                                <md-datepicker mg-init="oilToTin.record_date=new Date()" format="mm/dd/yyyy" ng-model="oilToTin.record_date" required  md-placeholder="Enter date"></md-datepicker>
                                                <!--<input type="text" class="form-control" ng-model="oilToTin.record_date" ng-change="oilToTin.record_date=changeDateFormat(oilToTin.record_date)" required/>-->
                                            </div>
                                        </div>


                                        <div class="d-flex col-12 mt-1">
                                            <label  class="col-3">Blank Tin(s) <span class="text-danger">*</span></label>
                                            <div class="col-4">
                                                <input type="text" class="form-control text-right" 
                                                ng-keyup="getTotalOilTinInQuintal();oilToTin.oilTin.oil_tin_quantity=oilToTin.blankTin.blank_tin_quantity"
                                                 ng-model="oilToTin.blankTin.blank_tin_quantity" required="" />
                                            </div>
                                            <div class="col-2 text-light bg-dark">{{blankTin.unit_name}}</div>
                                        </div>


                                        <div class="d-flex col-12 mt-1">
                                            <label  class="col-3">Oil <span class="text-danger">*</span></label>
                                            <div class="col-4">
                                                <input type="text" class="form-control text-right" ng-model="oilToTin.mustardOil.mustard_oil_quantity" required="" />
                                            </div>
                                            <div class="col-2 text-light bg-dark">{{mustardOil.unit_name}}</div>
                                        </div>
                                       

                                    </div>
                                    <div class="col-6 bg-gray-3 mt-1 mb-1">
                                        <div class="d-flex col-12 mt-1">
                                            <label  class="col-3">Oil Tin(s) </label>
                                            <div class="col-4">
                                                <input readOnly type="text" class="form-control text-right" ng-model="oilToTin.oilTin.oil_tin_quantity"/>
                                            </div>
                                            <div class="col-2 text-light bg-dark">{{oilTin.unit_name}}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row d-flex col-12">
                                    <div class="col-4"></div>
                                    <div class="col-4">
                                        <span ng-show="blankToOilTinConversion">Record successfully added</span>
                                        <span ng-show="updateStatus">Update successful</span>
                                    </div>
                                    <div class="col-4">
                                        <input type="button" class="btn btn-outline-primary float-right" id="save-purchase" ng-click="saveOilToBlankTin(oilToTin)" ng-disabled="oilToTinForm.$invalid" value="Save" ng-show="!isUpdateable"/>
                                        <input type="button" class="btn btn-outline-primary float-right" id="reset-purchase" ng-click="resetOilTinStockDetails()" value="Reset" ng-disabled="oilToTinForm.$pristine"/>
                                        <input type="button" class="btn btn-outline-primary float-right" id="update-purchase" ng-click="updatePurchaseDetails(purchaseMaster,purchaseDetailToSave)" value="Update" ng-show="isUpdateable" ng-disabled="oilToTinForm.$pristine"/>
                                    </div>
                                </div>
                            </form>
                            <div class="row d-flex col-12" ng-show="false">
                                <div class="col-5">
                                    <pre>oilToTin={{oilToTin | json}}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div ng-show="isSet(3)">
                        <div id="my-tab-3">
                            <div class="row d-flex col-12">
                                <p><input type="text" ng-model="searchItem"><span class="glyphicon glyphicon-search"></span> Search </p>
                                <table cellpadding="0" cellspacing="0" class="table table-bordered">
                                    <tr>
                                        <th>SL></th>
                                        <th ng-click="changeSorting('product')">Product Name<i class="glyphicon" ng-class="getIcon('vendor_name')"></i></th>
                                        <th ng-click="changeSorting('closing_date')">Closing Date<i class="glyphicon" ng-class="getIcon('total_purchase_amount')"></i></th>
                                        <th ng-click="changeSorting('purchase_date')">closing_balance balance<i class="glyphicon" ng-class="getIcon('purchase_date')"></i></th>
                                        
                                        
                                    </tr>
                                    <tbody ng-repeat="stock in allStockList">
                                    <tr ng-class-even="'banana'" ng-class-odd="'bee'">
                                        <td>{{ $index+1}}</td>
                                        <td>{{stock.product}}</td>
                                        <td class="text-right">{{stock.closing_date}}</td>
                                        <td style="padding-left: 20px;">{{stock.closing_balance | number:2}}</td>
                                       
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row d-flex col-12" ng-show="false">
                                <div class="col-5">
                                    <pre>allStockList={{allStockList | json}}</pre>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    function save_new_seed_to_oil(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $stock_details=(object)$post_data['stock_details'];
        $result=$this->stock_model->insert_new_seed_to_oil_stock($post_data['master_date'],$stock_details);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }
    function save_new_oil_to_tin_conversion(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $stock_details=(object)$post_data['stock_details'];
        $result=$this->stock_model->insert_oil_into_blank_tin($post_data['master_date'],$stock_details);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }



    function moil_closing_balance(){
        $result=$this->stock_model->select_last_moil_closing_stock();
        echo json_encode($result,JSON_NUMERIC_CHECK);

    }

    function mseed_closing_balance(){
        $result=$this->stock_model->select_last_mseed_closing_stock();
        echo json_encode($result,JSON_NUMERIC_CHECK);

    }

    
    function oil_cake_closing_balance(){
        $result=$this->stock_model->select_last_oil_cake_closing_stock();
        echo json_encode($result,JSON_NUMERIC_CHECK);

    }
}
?>