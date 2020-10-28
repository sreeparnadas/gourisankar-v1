<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sale extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('person');
        $this -> load -> model('sale_model');
        $this -> load -> model('sale_model_oil_cake');
        $this -> load -> model('customer_model');
        //$this -> is_logged_in();
    }
    function is_logged_in() {
		$is_logged_in = $this -> session -> userdata('is_logged_in');
		if (!isset($is_logged_in) || $is_logged_in != 1) {
			echo 'you have no permission to use developer area'. '<a href="">Login</a>';
			die();
		}
	}
    function get_products(){
        $result=$this->sale_model->select_inforce_products()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array);
    }


    public function angular_view_sale(){
        ?>
        <style type="text/css">
            .td-input{
                padding: 2px;
                margin-left: 0px;
                margin-right: 0px;
                text-align: right;
            }
            #sale-table th, #sale-table tr td{
                border: 0;
                padding: 0px;
            }
            #sale-table tfoot{
                border-top: 1px solid black;
            }
            .form-control{
                padding: 0 !important;
            }
            .btn{
                padding-top: 0px !important;
                padding-bottom: 0px !important;
                padding-left: 3px !important;
                padding-right: 3px !important;
            }
            .highlightOne {
                background: orange;
                font-size: 125%;
                margin: 5px;
                padding: 5px;
            }

        </style>
     <div class="d-flex col-12" ng-include="'application/views/header.html'"></div>

     
        <div class="d-flex col-12">
            <div class="col-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==1 && selectedTab" href="#" role="tab" ng-click="setTab(1)"><i class="fas fa-user-graduate"></i></i>Sale Mustard Oil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==2 && selectedTab" href="#" role="tab" ng-click="setTab(2)"><i class="fa fa-envelope"></i>Sale List </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==3 && selectedTab" href="#" role="tab"><i class="fa fa-heart"></i>Show Bill</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==4 && selectedTab" href="#" role="tab" ng-click="setTab(4)"><span class="glyphicon glyphicon-file"></span>Sale Oil Cake</a>
                    </li>
                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="row my-tab-1">
                            <form name="saleForm" class="form-horizontal" id="saleForm">
                                <div class="card" id="sale-master-card">
                                    <div class="d-flex card-header pt-0 pb-0 bg-gray-2">
                                        <label class="col-2 mr-auto">Memo number</label>
                                        <div class="col-2  mr-auto">
                                            <input type="text" class="textinput textInput form-control capitalizeWord" ng-model="oilMaster.memo_number" disabled/>
                                        </div>
                                        <div class="col-2 mr-auto" ng-show="showBillNo"><a title="Show Bill" href="#" ng-click="showSaleMustardOilBill(oilMaster)"><i class="fas fa-print"></i></a></div>
                                        <div ng-show="!tab1BillMasterShow" class="p-2"><a ng-click="tab1BillMasterShow=!tab1BillMasterShow" class="align-self-end" href="#" ><i class="fa fa-plus-square"></i></a></div>
                                        <div ng-show="tab1BillMasterShow" class="p-2"><a ng-click="tab1BillMasterShow=!tab1BillMasterShow" href="#" ><i class="fa fa-minus-square"></i></a></div>
                                    </div>

                                    <div class="card-body justify-content-center pt-0 pb-0 bg-gray-4" ng-show="tab1BillMasterShow">
                                        <div class="row d-flex col-12 bg-gray-4">
                                            <div class="col-4 bg-gray-4">
                                            <input type="text" class="textinput textInput form-control" placeholder="Search Customer" ng-change="searchCustomerByKey();setGstFactor()" ng-model="oilMaster.customerSearchKey"/>
                                                <div class="d-flex col-12 mt-1 pl-0" ng-show="showBillNo"></div>

                                            </div>
                                            <div class="col-4 bg-gray-2">
                                                <div class="d-flex col-12">
                                                    <div class="col-4">Customer</div>
                                               
                                                    <div class="6">
                                                        <select ng-disabled="saleDetailsDataList.length"
                                                                class=""
                                                                data-ng-model="oilMaster.customer"
                                                                data-ng-options="customer as customer.customer_name for customer in customerListByKey" ng-change="setGstFactor();loadAllProducts()">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                            <span ng-show="oilMaster.customer.mailing_name.length>0">
                                                {{oilMaster.customer.mailing_name}}, {{oilMaster.customer.address1}}
                                            </span>
                                                    <div ng-show="oilMaster.customer.gst_number.length>0">GST: {{oilMaster.customer.gst_number}}</div>
                                                </div>
                                            </div>
                                            <div class="col-4 bg-gray-4">
                                                <div class="d-flex col-12 mt-1 pl-0">
                                                    <label  class="col-5">Date<span class="text-danger">*</span></label>
                                                    <div class="col-6">
                                                        <md-datepicker required mg-init="oilMaster.sale_date=new Date()" format="dd/mm/yyyy" ng-model="oilMaster.sale_date" required ng-change="onDateChanged();oilMaster.sale_date=changeDateFormat(oilMaster.sale_date)"  md-placeholder="Enter date"></md-datepicker>
<!--                                                        <input type="date" name="saleDate" ng-init="oilMaster.sale_date=currentDate" ng-change="oilMaster.sale_date=changeDateFormat(oilMaster.sale_date)" max="3000-12-31" min="2018-01-01" ng-model="oilMaster.sale_date"  class="form-control">-->
                                                        <span>{{oilMaster.sale_date | date:shortDate}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
<!--                                end of first card-->

                                <div class="card" id="sale-details-card">
                                    <div class="d-flex card-header pt-0 pb-0 bg-gray-2">
                                        <div class="row d-flex col-12 bg-gray-2">
                                            <label  class="col-2">Product  <b>{{' (stock :' + counOilTin.total + ')'}}</b></label>
                                            <label  class="col-2">Quantity&nbsp;<kbd>(Tin)</kbd></label>
                                            <label  class="col-2">Rate</label>
                                            <!--                                    <label  class="col-1">Dis Rt(%)</label>-->
                                            <label class="col-1">Amount</label>
                                            <label class="col-1" ng-show="oilMaster.sgstFactor">SGST</label>
                                            <label class="col-1" ng-show="oilMaster.cgstFactor">CGST</label>
                                            <label class="col-1" ng-show="oilMaster.igstFactor">IGST</label>
                                        </div>
                                    </div>
                                    <div class="card-body justify-content-center pt-0 pb-0 bg-gray-4">
                                        <div class="row d-flex col-12 bg-gray-4">
                                            <div class="col-2">
                                                <span id="product-name" name="product-name"  ng-bind="oilDetails.mustardOil.product_name"></span>
                                            </div>
                                            <div class="col-2">
                                                <input type="number" min="0" class="td-input form-control col-5" id="sale-quantity" name="saleQuantity" ng-change="setAmount()" ng-model="oilDetails.mustardOil.quantity" ng-change="setMustardOilGst()" required>
                                                <span ng-bind="tinToQuintalConversion() + '&nbsp; Qtl'" ng-show="oilDetails.mustardOil.quantity"></span>
                                            </div>
                                            <div class="col-2">
                                                <input  type="number" min="0" class="td-input form-control col-5" id="sale-rate" name="saleRate" ng-change="setAmount();setMustardOilGst()" ng-model="oilDetails.mustardOil.rate" required>
                                            </div>
                                            <div class="hidden">
                                                <input class="td-input" type="hidden" id="sale-discount" ng-keyup="setAmount();setMustardOilGst()" name="saleDiscount" ng-init="0.00" step="0.01" ng-model="oilDetails.mustardOil.discount">
                                            </div>
                                            <div class="col-1">
                                                <span id="sale-amount" name="saleAmount"  ng-bind="(oilDetails.mustardOil.amount | number:2)"></span>
                                            </div>
                                            <div class="col-1" ng-show="oilMaster.sgstFactor" >
                                                <span id="sale-sgst" name="saleSgst"  ng-bind="(oilDetails.mustardOil.sgst | number:2)"></span>
                                            </div>
                                            <div class="col-1" ng-show="oilMaster.cgstFactor">
                                                <span   id="sale-cgst" name="saleCgst"  ng-bind="(oilDetails.mustardOil.cgst | number:2)"></span>
                                            </div>
                                            <div class="col-1" ng-show="oilMaster.igstFactor">
                                                <span   id="sale-igst" name="saleIgst" ng-show="oilMaster.igstFactor"  ng-bind="(oilDetails.mustardOil.igst | number:2)"></span>
                                            </div>
                                            <div class="col-1">
                                                <button ng-click="addOilDetailsData(oilDetails)" ng-disabled="saleForm.$invalid"><i class="fa fa-plus-square" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
<!--                                end of sale details card-->

                                <div class="card" id="sale-details-table-card">
                                    <div class="d-flex card-header pt-0 pb-0 bg-gray-2">

                                    </div>
                                    <div class="card-body justify-content-center pt-0 pb-0 bg-gray-4">
                                        <div class="row d-flex col-12 bg-gray-4 table-responsive mt-2">
                                            <table class="table" id="sale-table">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">SL</th>
                                                        <th class="text-right">Quantity</th>
                                                        <th class="text-right">Rate(<i class="fas fa-rupee-sign "></i>)</th>
                                                        <th class="text-right">Value</th>
                                                        <th class="text-right" ng-show="oilMaster.sgstFactor">SGST</th>
                                                        <th class="text-right" ng-show="oilMaster.cgstFactor">CGST</th>
                                                        <th class="text-right" ng-show="oilMaster.igstFactor">IGST</th>
                                                        <th class="text-right">Total Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody ng-repeat="p in saleDetailsDataList">
                                                <tr>
                                                    <td class="text-center" >{{$index+1}}</td>
                                                    <td class="text-right">{{p.mustardOil.quantity}}</td>
                                                    <td class="text-right">{{p.mustardOil.rate | number:2}}</td>
                                                    <td class="text-right">{{p.mustardOil.amount | number:2}}</td>
                                                    <td class="text-right" ng-show="oilMaster.sgstFactor">{{p.mustardOil.sgst | number:2}}</td>
                                                    <td class="text-right" ng-show="oilMaster.cgstFactor">{{p.mustardOil.cgst | number:2}}</td>
                                                    <td class="text-right" ng-show="oilMaster.igstFactor">{{p.mustardOil.igst | number:2}}</td>
                                                    <td class="text-right">
                                                        {{(p.mustardOil.amount+p.mustardOil.sgst+p.mustardOil.cgst+p.mustardOil.igst) | number:2}}
                                                    </td>
                                                    <td> <a href="#" data-ng-click="removeRow($index)">&nbsp;<i class="fa fa-trash" aria-hidden="true"></i> </a></td>
                                                </tr>
                                                </tbody>
                                                <tfoot ng-show="saleDetailsDataList.length" class="bg-gray-3">
                                                    <tr>
                                                        <td>Total:</td>
                                                        <td colspan="4" class="text-right" ng-show="oilMaster.sgstFactor">{{oilFooter[0].totalSgst | number:2}}</td>
                                                        <td class="text-right" ng-show="oilMaster.cgstFactor">{{oilFooter[0].totalCgst | number:2}}</td>
                                                        <td class="text-right" colspan="4" ng-show="oilMaster.igstFactor">{{oilFooter[0].totalIgst | number:2}}</td>
                                                        <td class="text-right">{{oilFooter[0].totalsaleAmount | number:2}}</td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="6" class="text-right" ng-show="oilMaster.sgstFactor">Rounded off</td>
                                                        <td colspan="5" class="text-right" ng-show="oilMaster.igstFactor">Rounded off</td>
                                                        <td class="text-right">{{oilMaster.roundedOff | number:2}}</td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="6" class="text-right" ng-show="oilMaster.sgstFactor">Grand Total</td>
                                                        <td colspan="5" class="text-right" ng-show="oilMaster.igstFactor">Grand Total</td>
                                                        <td class="text-right">{{oilMaster.grand_total | number:2}}</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div class="row d-flex col-12">
                                            <div class="col-4"></div>
                                            <div class="col-4">
                                                <span ng-show="saleSubmitStatus" class="text-success">Record successfully added</span>
                                                <span ng-show="updateStatus"  class="text-success">Update successful</span>
                                            </div>
                                            <div class="col-4">
                                                <md-button class="md-raised" id="save-sale" ng-click="saveSaleDetails(oilMaster,oilDetailToSave)" ng-disabled="saleDetailsDataList.length<1"  ng-show="!isUpdateableOil">Save</md-button>
                                                <md-button class="md-raised" id="reset-sale" ng-click="resetSaleDetails()" value="Reset" ng-disabled="">Reset</md-button>
                                                <md-button class="md-raised" id="update-sale" ng-click="updateSaleDetails(oilMaster,oilDetailToSave)"  ng-show="isUpdateableOil" ng-disabled="saleForm.$touched">Update</md-button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                            <!--developer div-->
                            <div class="d-flex" ng-show="false">
                            
                                <div class="col-4"><pre>oilMaster={{oilMaster | json}}</pre></div>
                                <div class="col-4"><pre>oilDetailToSave={{oilDetailToSave | json}}</pre></div>
                                <div class="col-4"><pre>saleDetailsDataList={{saleDetailsDataList | json}}</pre></div>


                            </div>
                            
                           
                           

<!--                            <pre>allSaleList={{allSaleList | json}}</pre>-->
<!--                            <div class="card" ng-show="true">-->
<!--                                <div class="card-header p-0 mb-0">-->
<!--                                    <div class="d-flex">-->
<!--                                        <div class="mr-auto p-2">-->
<!--                                            Developer Area-->
<!--                                        </div>-->
<!--                                        <div ng-show="tab1DeveloperAriaShowHide" class="p-2"><a ng-click="tab1DeveloperAriaShowHide=!tab1DeveloperAriaShowHide" class="align-self-end" href="#" ><i class="fa fa-plus-square"></i></a></div>-->
<!--                                        <div ng-show="!tab1DeveloperAriaShowHide" class="p-2"><a ng-click="tab1DeveloperAriaShowHide=!tab1DeveloperAriaShowHide" href="#" ><i class="fa fa-minus-square"></i></a></div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                                <div class="card-body" ng-show="!tab1DeveloperAriaShowHide">-->
<!--                                    <div class="d-flex">-->
<!--                                        <div class="col">-->
<!--                                            werwerwer<qrcode data="This is bill"></qrcode>-->
<!--                                           <canvas id="bar" class="chart chart-bar"-->
<!--                                                         chart-data="chartData" chart-labels="chartLabels" chart-series="barChartSeries"-->
<!--                                            </canvas>-->
<!--                                            <pre>saleDetailsDataList={{saleDetailsDataList | json}}</pre>-->
<!--                                        </div>-->
<!--                                        <div class="col">-->
<!--                                            http://jtblin.github.io/angular-chart.js/-->
<!--                                            <canvas id="doughnut" class="chart chart-doughnut"-->
<!--                                                    chart-data="chartData" chart-labels="chartLabels">-->
<!--                                            </canvas>-->
<!--                                        </div>-->
<!--                                        <div class="col">-->
<!--                                            <canvas id="base" class="chart-base" chart-type="type"-->
<!--                                                    chart-data="chartData" chart-labels="chartLabels" >-->
<!--                                            </canvas>-->
<!--                                            <button type="button" ng-click="toggle()" class="btn btn-default">Toggle</button>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->

                        </div> <!--//End of my tab1//-->
                    </div>

                    <div ng-show="isSet(2)">
                        <div id="my-tab-2">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex">
                                        <div class="col-4"><input type="text" ng-model="searchItem"><span class="glyphicon glyphicon-search"></span> Search </div>
                                        <div class="col-6 p-0 m-0"><button type="button" class="btn btn-primary" ng-click="saveToExcel('test2.xls',moilExceldata)">Send to Excel</button></div>

                                    </div>
                                </div>
                                <div class="card-body">
                                <div class="d-flex justify-content-center" ng-if="!allSaleList"><img src="img/spinner.gif" width="100px" height="100px" alt=""></div>
                                <!-- <span class="text-center"><img src="img/spinner.gif" width="100px" height="100px" alt=""></span> -->
                                    <table cellpadding="0" cellspacing="0" class="table table-bordered" ng-if="allSaleList.length">
                                        <tr>
                                            <th class="p-0">SL></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('memo_number')">Memo No<i class="glyphicon" ng-class="getIcon('memo_number')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('person_name')">Customer<i class="glyphicon" ng-class="getIcon('person_name')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('mobile_no')">Contact<i class="glyphicon" ng-class="getIcon('mobile_no')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('grand_total')">Amount<i class="glyphicon" ng-class="getIcon('grand_total')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('sale_date')">Date<i class="glyphicon" ng-class="getIcon('sale_date')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('sale_month')">Month</th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('bill_type_name')">Type</th>
                                            <th class="p-0 pl-1">Action</th>
                                        </tr>
                                        <tbody ng-repeat="sale in allSaleList | filter : searchItem  | orderBy:sort.active:sort.descending">
                                            <tr ng-class-even="'banana'" ng-class-odd="'bee'">
                                                <td class="p-0  pl-1">{{ $index+1}}</td>
                                                <td class="p-0  pl-1">{{sale.memo_number}}</td>
                                                <td class="p-0  pl-1">{{sale.person_name}}</td>
                                                <td class="p-0  pl-1">{{sale.mobile_no || 'empty'}}</td>
                                                <td class="text-right p-0  pl-1">{{sale.grand_total | number:2}}</td>
                                                <td class="text-right p-0  pl-1">{{sale.display_sale_date}}</td>
                                                <td class="p-0  pl-1 style="padding-left: 20px;">{{sale.sale_month}}</td>
                                                <td class="p-0  pl-1" style="padding-left: 20px;">{{sale.bill_type_name}}</td>
                                                <td class="p-0  pl-1 style="padding-left: 20px;" ng-click="getSaleFromTableForUpdate(sale)"><a href="#">Edit</a></td>
                                                <td class="p-0  pl-1 style="padding-left: 20px;" ng-click="prepareOilBillData(sale)"><a title="Show Bill" href="#"><i class="fas fa-print"></i></a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer">
<!--                                    <pre>allSaleList = {{allSaleList | json}}</pre>-->
                                </div>
                            </div>
                        </div>
                    </div>


                    <div ng-show="isSet(3)">
                        <style type="text/css">
                            #show-bill-details-table tfoot{
                                border: 1px solid black !important;
                            }
                            #bill-header-div{
                                border-bottom: 1px solid black;
                            }

                            #show-bill-details-table > tfoot > tr{
                                border-right: 1px solid black; !important;
                            }
                            #show-bill-details-table{
                                border: 1px solid black !important;
                            }
                            #show-bill-details-table tr{
                                line-height: 2px;
                            }
                            #show-bill-details-table tr th{
                                border: 1px solid black !important;
                            }
                            #show-bill-details-table tr td{
                                border: 1px solid black !important;
                            }
                            #print-bank-details ul{
                                list-style: none;
                            }

                        </style>

                        <!-- bill part -->

                        <div id="show-bill-div">
                            <div id="bill-header-div">
                                <div class="card ">
                                    <div class="card-header p-0 mb-0">
                                        <div class="d-flex">
                                            <div class="col-3"></div>
                                            <div class="col-6">
                                                <h1 class="text-center">M/S. GOURI SANKAR OIL MILL</h1>
                                                <h5 class="text-center">Thanaroad, Gandhimore, Bethuadahari, Nadia, Pin - 741126</h5>
                                            </div>
                                            <div class="col-3">
                                                <h5 class="text-left">Mob : 9800403003</h5>
                                                <h5 class="text-left ">GSTIN: 19AAFFG4167M1ZX</h5>
                                                <h5 class="text-left ">State Code: WB 19</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="d-flex mb-1" style="border-bottom: 3px solid #8c8b8b;">
                                            <div class="col-4 font-weight-bold">
                                                <div>Buyer's name & Address</div>
                                                <div ng-bind="mustardOilBillDetails[0].customer"></div>
                                                <div ng-bind="mustardOilBillDetails[0].address"></div>
                                                <div>GSTIN: {{mustardOilBillDetails[0].gst_number}}</div>
                                                <span ng-bind="mustardOilBillDetails[0].state_gst_code +'&nbsp;('+mustardOilBillDetails[0].state_name+')'">19</span>
                                            </div>
                                            <div class="d-flex flex-column col-4">
                                                <h3 class="text-center font-weight-bold">TAX INVOICE</h3>
                                                <h5 class="text-center">Cash/ Credit</h5>
                                            </div>
                                            <div class="d-flex flex-column col-3">
                                                <div>
                                                    Invoice : {{mustardOilBillDetails[0].memo_number}}<angular-barcode ng-model="mustardOilBillDetails[0].memo_number" bc-options="barcodeOilBill" bc-class="barcode" bc-type="img"></angular-barcode>
                                                </div>
                                                <div class="text-left mt-0 font-weight-bold">Date: {{mustardOilBillDetails[0].sale_date}}</div>
                                                <div class="text-left mt-0 font-weight-bold font-weight-bold">Memo no: {{mustardOilBillDetails[0].memo_number}}</div>
                                            </div>
                                        </div>
                                        <!--Bill table below-->

                                        <div>
                                            <table class="table" id="show-bill-details-table">
                                                <thead>
                                                <tr border="1">
                                                    <th class="text-center text-truncate">HSN</th>
                                                    <th class="text-center text-truncate">Product</th>
                                                    <th class="text-center text-truncate">Quantity({{mustardOilBillDetails[0].product_id==3 ? 'Tin' : 'Pac'}})</th>
                                                    <th class="text-center text-truncate">Rate</th>
                                                    <th class="text-center text-truncate">Gross Value</th>
                                                    <th class="text-center text-truncate" colspan="2" ng-show="mustardOilBillDetails[0].state_gst_code==19">SGST</th>
                                                    <th class="text-center text-truncate" colspan="2" ng-show="mustardOilBillDetails[0].state_gst_code==19">CGST</th>
                                                    <th class="text-center text-truncate" colspan="2" ng-show="mustardOilBillDetails[0].state_gst_code!=19">IGST</th>
                                                    <th class="text-center text-truncate">Total Tax</th>
                                                    <th class="text-center pl-0 pr-4 text-truncate">Total Amount</th>
                                                </tr>
                                                <tr border="1">
                                                    <th class="text-center"></th>
                                                    <th class="text-center"></th>
                                                    <th class="text-center"></th>
                                                    <th class="text-center"></th>
                                                    <th class="text-center">(A)</th>
                                                    <th class="text-center" ng-show="mustardOilBillDetails[0].state_gst_code==19">Rate</th>
                                                    <th class="text-center" ng-show="mustardOilBillDetails[0].state_gst_code==19">Amount</th>
                                                    <th class="text-center" ng-show="mustardOilBillDetails[0].state_gst_code==19">Rate</th>
                                                    <th class="text-center" ng-show="mustardOilBillDetails[0].state_gst_code==19">Amount</th>
                                                    <th class="text-center" ng-show="mustardOilBillDetails[0].state_gst_code!=19">Rate</th>
                                                    <th class="text-center" ng-show="mustardOilBillDetails[0].state_gst_code!=19">Amount</th>
                                                    <th class="text-center">(B)</th>
                                                    <th class="text-center">(A+B)</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr ng-repeat="x in mustardOilBillDetails">
                                                    <td class="text-right">{{x.hsn_code}}</td>
                                                    <td class="text-center">{{x.product_id==3 ? 'M.Oil' : 'O.Cake'}}</td>
                                                    <td class="text-right">{{x.no_of_container + ' (' + x.quantity + ' qntl)'}}</td>
                                                    <td class="text-right">{{x.rate | number:2}}</td>
                                                    <td class="text-right">{{x.gross_value | number:2}}</td>
                                                    <td class="text-right" ng-show="mustardOilBillDetails[0].state_gst_code==19">{{x.sgst_rate}}(%)</td>
                                                    <td class="text-right" ng-show="mustardOilBillDetails[0].state_gst_code==19">{{x.sgst | number:2}}</td>
                                                    <td class="text-right" ng-show="mustardOilBillDetails[0].state_gst_code==19">{{x.cgst_rate}}(%)</td>
                                                    <td class="text-right" ng-show="mustardOilBillDetails[0].state_gst_code==19">{{x.cgst | number:2}}</td>
                                                    <td class="text-right" ng-show="mustardOilBillDetails[0].state_gst_code!=19">{{x.igst_rate}}(%)</td>
                                                    <td class="text-right" ng-show="mustardOilBillDetails[0].state_gst_code!=19">{{x.igst | number:2}}</td>
                                                    <td class="text-right">{{x.total_tax | number:2}}</td>
                                                    <td class="text-right">{{x.total_amount | number:2}}</td>
                                                </tr>
                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <td colspan="4">Total</td>
                                                    <td class="text-right">{{mOilShowTableFooter.totalGrossValue | number:2}}</td>
                                                    <td class="text-right"  colspan="5" ng-show="mustardOilBillDetails[0].state_gst_code==19">{{mOilShowTableFooter.grandTotalTax | number:2}}</td>
                                                    <td class="text-right"  colspan="3" ng-show="mustardOilBillDetails[0].state_gst_code!=19">{{mOilShowTableFooter.grandTotalTax | number:2}}</td>
                                                    <td class="text-right">{{mOilShowTableFooter.grandTotalAmount | number:2}}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="10" class="text-right" ng-show="mustardOilBillDetails[0].state_gst_code==19">Rounded Off</td>
                                                    <td  class="text-right" colspan="8" ng-show="mustardOilBillDetails[0].state_gst_code!=19">Rounded Off</td>
                                                    <td class="text-right">{{mustardOilBillDetails[0].roundedOff | number:2}}</td>
                                                </tr>
                                                <tr>
                                                    <td  class="text-right" colspan="10" ng-show="mustardOilBillDetails[0].state_gst_code==19">Bill amount</td>
                                                    <td  class="text-right" colspan="8" ng-show="mustardOilBillDetails[0].state_gst_code!=19">Bill amount</td>
                                                    <td class="text-right">{{mOilShowTableFooter.grandTotalAmount + oilMaster.roundedOff | number:2}}</td>
                                                </tr>
                                                </tfoot>
                                            </table>
                                            Inword: Rupees {{mOilShowTableFooter.grandTotalAmount | words}} only
                                        </div>
                                        <!--End of Bill Part-->
                                        <div class="d-flex">
                                            <div class="col" id="print-bank-details">
                                                <ul>
                                                    <li>Bank: SBI</li>
                                                    <li>A/c No: 11276910399 (Bethuadahari)</li>
                                                    <li>IFSC Code: SBIN0006985</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="d-flex mt-3 p-5">
                                                <div class="col-4 text-center" style="border-top: 1px dashed black"><b>Customer Signature</b></div>
                                                <div class="col-4"></div>
                                                <div class="col-4 text-center" style="border-top: 1px dashed black"><b>For M/S Gourisankar Oil Mill</b></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- end of bill part -->



                        <!-- bill icon -->
                        <div class="card" ng-show="true">
                                <div class="card-header p-0 mb-0">
                                    <div class="d-flex">
                                        <div class="mr-auto p-2">
                                            <a href="#" ng-click="huiPrintDiv('show-bill-div','my_printing_style.css',2)" class="btn btn-info btn-lg no-print">
                                                <i class="fas fa-print"></i> Print
                                            </a>
                                        </div>
                                        <div ng-show="tab3DeveloperAriaShowHide" class="p-2"><a ng-click="tab3DeveloperAriaShowHide=!tab3DeveloperAriaShowHide" class="align-self-end" href="#" ><i class="fa fa-plus-square"></i></a></div>
                                        <div ng-show="!tab3DeveloperAriaShowHide" class="p-2"><a ng-click="tab3DeveloperAriaShowHide=!tab3DeveloperAriaShowHide" href="#" ><i class="fa fa-minus-square"></i></a></div>
                                    </div>
                                </div>
                                <div class="card-body" ng-show="!tab3DeveloperAriaShowHide">
                                    <div class="d-flex">
                                        <div class="col">
                                           <!-- <pre>mustardOilBillDetails={{mustardOilBillDetails | json}}</pre> -->
                                        </div>
                                        <div class="col">

                                        </div>
                                        <div class="col">

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- end of bill icon -->
                    </div>
                            <!-- end of tab3 -->
                    <div ng-show="isSet(4)">
                        <div id="row my-tab-4">
                            <form name="saleFormOilCake" class="form-horizontal" id="saleForm">
                                <div class="card" id="oil-cake-master-card">
                                    <div class="d-flex card-header pt-0 pb-0 bg-gray-2">
                                        <label class="col-2 mr-auto">Memo number</label>
                                        <div class="col-2  mr-auto">
                                            <input type="text" class="textinput textInput form-control capitalizeWord" ng-model="oilCakeMaster.memo_number" readonly/>
                                        </div>
                                        <div class="col-2 mr-auto" ng-show="showOilCakeBillNo"><a title="Show Bill" href="#" ng-click="showSaleMustardOilBill(oilCakeMaster)"><i class="fas fa-print"></i></a></div>
                                        <div class="col-2 mr-auto" ng-show=""><a title="Show Bill" href="#" ng-click="showSaleMustardOilBill(oilCakeMaster)"><i class="fas fa-print"></i></a></div>
                                        <div ng-show="!oilCakeTab1BillMasterShow" class="p-2"><a ng-click="oilCakeTab1BillMasterShow=!oilCakeTab1BillMasterShow" class="align-self-end" href="#" ><i class="fa fa-plus-square"></i></a></div>
                                        <div ng-show="oilCakeTab1BillMasterShow" class="p-2"><a ng-click="oilCakeTab1BillMasterShow=!oilCakeTab1BillMasterShow" href="#" ><i class="fa fa-minus-square"></i></a></div>
                                    </div>
                                    <div class="card-body justify-content-center pt-0 pb-0 bg-gray-4" ng-show="oilCakeTab1BillMasterShow">
                                        <div class="row d-flex col-12">
                                            <div class="col-4 bg-gray-2 mt-1 mb-1">
                                                <div class="d-flex col-12">
                                                    <div class="col-6">Customer
                                                    
                                                    </div>
                                                    <div class="6">
                                                        <select ng-disabled="oilCakeDataList.length"
                                                                class="form-control"
                                                                data-ng-model="oilCakeMaster.customer"
                                                                data-ng-options="customer as customer.customer_name for customer in customerList" ng-change="loadAllProducts()">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                            <span ng-show="oilCakeMaster.customer.mailing_name.length>0">
                                                {{oilCakeMaster.customer.mailing_name}}, {{oilCakeMaster.customer.address1}}
                                            </span>
                                                    <div ng-show="oilCakeMaster.customer.gst_number.length>0">GST: {{oilCakeMaster.customer.gst_number}}</div>
                                                </div>
                                            </div>
                                            <div class="col-4 bg-gray-4 mt-1 mb-1">
                                                <div class="d-flex col-12 mt-1 pl-0">
                                                    <label  class="col-2">Date<span class="text-danger">*</span></label>
                                                    <div class="col-5">
                                                        <md-datepicker mg-init="oilCakeMaster.sale_date=new Date()" format="dd/mm/yyyy" ng-model="oilCakeMaster.sale_date" required ng-change="onDateChanged();oilCakeMaster.sale_date=changeDateFormat(oilCakeMaster.sale_date)"  md-placeholder="Enter date"></md-datepicker>
                                                        <!--                                                <input type="date" name="saleDate" ng-init="oilCakeMaster.sale_date=currentDate" ng-change="oilCakeMaster.sale_date=currentDate(oilCakeMaster.sale_date)" max="3000-12-31" min="2018-01-01" ng-model="oilCakeMaster.sale_date"  class="form-control">-->
                                                        <span>{{oilCakeMaster.sale_date | date:shortDate}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end of first card-->

                                <div class="card" id="oil-cake-sale-details-card">
                                    <div class="d-flex card-header pt-0 pb-0 bg-gray-2">
                                        <div class="row d-flex col-12 bg-gray-5">
                                            <label class="col-2">Product</label>
                                            <label class="col-2">Quantity&nbsp;<kbd>(Pac)</kbd></label>
                                            <label class="col-2">Rate</label>
                                            <!--                                    <label  class="col-1">Dis Rt(%)</label>-->
                                            <label class="col-1">Value</label>
                                            <label class="col-1" ng-show="oilCakeMaster.sgstFactor">SGST</label>
                                            <label class="col-1" ng-show="oilCakeMaster.cgstFactor">CGST</label>
                                            <label class="col-1" ng-show="oilCakeMaster.igstFactor">IGST</label>
                                        </div>
                                    </div>

                                    <div class="card-body justify-content-center pt-0 pb-0 bg-gray-4">
                                        <div class="row d-flex col-12 bg-gray-5">
                                            <div class="col-2">
                                                <span id="product-name" name="product-name"  ng-bind="oilCakeDetails.oilCake.product_name"></span>
                                            </div>
                                            <div class="col-2">
                                                <input type="number" min="0" class="td-input form-control col-5" id="sale-quantity" name="saleQuantity" ng-keyup="setAmountForOilCake()" ng-model="oilCakeDetails.oilCake.quantity" ng-change="setOilCakeGst()" required>
                                                <span ng-bind="packetToQuintalConversion() + '&nbsp; Qtl'" ng-show="oilCakeDetails.oilCake.quantity"></span>
                                            </div>
                                            <div class="col-2">
                                                <input  type="number" min="0" class="td-input form-control col-5" id="sale-rate" name="saleRate" ng-keyup="setAmountForOilCake()" ng-change="setOilCakeGst()" ng-model="oilCakeDetails.oilCake.rate" required>
                                            </div>

                                            <div class="col-1">
                                                <span id="sale-amount"  ng-bind="(oilCakeDetails.oilCake.amount | number:2)"></span>
                                            </div>
                                            <div class="col-1" ng-show="oilCakeMaster.sgstFactor">
                                                <span id="sale-sgst"  ng-bind="(oilCakeDetails.oilCake.sgst | number:2)"></span>
                                            </div>
                                            <div class="col-1" ng-show="oilCakeMaster.cgstFactor">
                                                <span  id="sale-cgst"  ng-bind="(oilCakeDetails.oilCake.cgst | number:2)"></span>
                                            </div>
                                            <div class="col-1">
                                                <span  id="sale-igst" ng-show="oilCakeMaster.igstFactor" ng-bind="(oilCakeDetails.oilCake.igst | number:2)"></span>
                                            </div>
                                            <div class="col-1">
                                                <button ng-click="addOilCakeDetailsData(oilCakeDetails)" ng-disabled="saleFormOilCake.$invalid"><i class="fa fa-plus-square" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end of oil cake sale details card-->
                                <div class="card" id="oil-cake-sale-details-table-card">
                                    <div class="d-flex card-header pt-0 pb-0 bg-gray-2"></div>
                                    <div class="card-body justify-content-center pt-0 pb-0 bg-gray-4">
                                        <div class="row d-flex col-12 bg-gray-4 table-responsive mt-2">
                                            <table class="table" id="sale-table">
                                                <thead>
                                                <tr>
                                                    <th class="text-center">SL</th>
                                                    <th class="text-right">Quantity</th>
                                                    <th class="text-right">Rate(<i class="fas fa-rupee-sign "></i>)</th>
                                                    <th class="text-right">Value</th>
                                                    <th class="text-right" ng-show="oilCakeMaster.sgstFactor">SGST</th>
                                                    <th class="text-right" ng-show="oilCakeMaster.cgstFactor">CGST</th>
                                                    <th class="text-right" ng-show="oilCakeMaster.igstFactor">IGST</th>
                                                    <th class="text-right">Total Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody ng-repeat="p in oilCakeDataList">
                                                <tr>
                                                    <td class="text-center">{{$index+1}}</td>
                                                    <td class="text-right">{{p.oilCake.quantity}}</td>
                                                    <td class="text-right">{{p.oilCake.rate | number}}</td>
                                                    <td class="text-right">{{p.oilCake.amount | number:2}}</td>
                                                    <td class="text-right" ng-show="oilCakeMaster.sgstFactor">{{p.oilCake.sgst | number:2}}</td>
                                                    <td class="text-right" ng-show="oilCakeMaster.cgstFactor">{{p.oilCake.cgst | number:2}}</td>
                                                    <td class="text-right" ng-show="oilCakeMaster.igstFactor">{{p.oilCake.igst | number:2}}</td>
                                                    <td class="text-right">
                                                        {{(p.oilCake.amount+p.oilCake.sgst+p.oilCake.cgst+p.oilCake.igst) | number:2}}
                                                    </td>
                                                    <td> <a href="#" data-ng-click="removeRow2($index)">&nbsp;<i class="fa fa-trash" aria-hidden="true"></i> </a></td>
                                                </tr>
                                                </tbody>
                                                <tfoot ng-show="oilCakeDataList.length" class="bg-gray-3">
                                                <tr>
                                                    <td>Total:</td>
                                                    <td  class="text-right" colspan="3">{{oilCakeFooter[0].totalValue | number:2}}</td>
                                                    <td  class="text-right" ng-show="oilCakeMaster.sgstFactor">{{oilCakeFooter[0].totalSgst | number:2}}</td>
                                                    <td class="text-right" ng-show="oilCakeMaster.cgstFactor">{{oilCakeFooter[0].totalCgst | number:2}}</td>
                                                    <td class="text-right" colspan="4" ng-show="oilCakeMaster.igstFactor">{{oilCakeFooter[0].totalIgst | number:2}}</td>
                                                    <td class="text-right">{{oilCakeFooter[0].totalsaleAmount | number:2}}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="6" class="text-right" ng-show="oilCakeMaster.sgstFactor">Rounded off</td>
                                                    <td colspan="5" class="text-right" ng-show="oilCakeMaster.igstFactor">Rounded off</td>
                                                    <td class="text-right">{{oilCakeMaster.roundedOff | number:2}}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="6" class="text-right" ng-show="oilCakeMaster.sgstFactor">Grand Total</td>
                                                    <td colspan="5" class="text-right" ng-show="oilCakeMaster.igstFactor">Grand Total</td>
                                                    <td class="text-right">{{oilCakeMaster.grand_total | number:2}}</td>
                                                </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div class="row d-flex col-12">
                                            <div class="col-4"></div>
                                            <div class="col-4">
                                                <span ng-show="isSaveOilCake" class="text-success">Record successfully added</span>
                                                <span ng-show="updateOilCakeStatus"  class="text-success">Update successful</span>
                                            </div>
                                            <div class="col-4">
                                                <input type="button" class="btn btn-outline-primary float-right"  ng-click="saveOilCakeDetails(oilCakeMaster,oilCakeDetailToSave)" ng-disabled="!oilCakeDataList.length" ng-show="!isUpdateableOilCake" value="Save" />
                                                <input type="button" class="btn btn-outline-primary float-right" id="reset-sale" ng-click="resetSaleOilCake()" value="Reset" ng-disabled=""/>
                                                <input type="button" class="btn btn-outline-primary float-right" id="update-sale" ng-click="updateOilCakeDetails(oilCakeMaster,oilCakeDetailToSave)" value="Update" ng-show="isUpdateableOilCake" ng-disabled="saleFormOilCake.$touched"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

<!--                                oil-cake-sale-details-table-card-->
                            </form>
                            

                            <div class="card" ng-show="false">
                                <div class="card-header p-0 mb-0">
                                    <div class="d-flex">
                                        <div class="mr-auto p-2">Developer Area</div>
                                        <div ng-show="oilCakeDeveloperAriaShowHide" class="p-2"><a ng-click="oilCakeDeveloperAriaShowHide=!oilCakeDeveloperAriaShowHide" class="align-self-end" href="#" ><i class="fa fa-plus-square"></i></a></div>
                                        <div ng-show="!oilCakeDeveloperAriaShowHide" class="p-2"><a ng-click="oilCakeDeveloperAriaShowHide=!oilCakeDeveloperAriaShowHide" href="#" ><i class="fa fa-minus-square"></i></a></div>
                                    </div>
                                </div>
                                <div class="card-body" ng-show="!oilCakeDeveloperAriaShowHide">
                                    <div class="row d-flex col-12"></div>
                                </div>
                            </div>

                        </div> <!--//End of my tab1//-->
                    </div>

<!--                    Show Mustard Oil Bill-->

                    <div class="d-flex" ng-show="false">
                            
                            <div class="col-4"><pre>oilCakeMaster={{oilCakeMaster | json}}</pre></div>
                            <div class="col-4"><pre>oilCakeDetailToSave={{oilCakeDetailToSave | json}}</pre></div>
                            <div class="col-4"><pre>oilCakeDataList={{oilCakeDataList | json}}</pre></div>


                        </div>










                </div>
            </div>
        </div>

        <?php
    }
    function save_new_sale(){
        $post_data =json_decode(file_get_contents("php://input"), true);
       $result=$this->sale_model->insert_new_sale((object)$post_data['sale_master'],(object)$post_data['sale_details_list']);
        $report_array['records']=$result;
        echo json_encode($report_array);

    }

    function get_all_sale(){
        $result=$this->sale_model->select_all_sale()->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }

    function get_sale_details_for_edit_sale(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->sale_model->select_sale_details_by_sale_master_id($post_data['sale_master_id'])->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }

    function update_saved_sale(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $sale_master=(object)$post_data['sale_master'];
        $sale_details_list=(object)$post_data['sale_details_list'];
        $result=$this->sale_model->update_sale_master_details($sale_master,$sale_details_list);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }

    function get_mustard_oil_bill_details_by_id(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        
        $result=$this->sale_model->select_mustard_oil_bill_details_by_sale_master_id($post_data['sale_master_id'],$post_data['product_id'])->result_array();
        $report_array['records']=$result;
        //echo json_encode($report_array);
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }

    function save_new_oil_cake_sale(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->sale_model->insert_new_oil_cake_sale((object)$post_data['sale_master'],(object)$post_data['sale_details_list']);
        $report_array['records']=$result;
        echo json_encode($report_array);

    }

    function update_oil_cake_details(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $oil_cake_master=(object)$post_data['oilCakeMaster'];
        $oil_cake_details_list=(object)$post_data['oilCakeDetailsList'];
        $result=$this->sale_model->update_oil_cake($oil_cake_master,$oil_cake_details_list);
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }

    function get_sale_by_month(){
        $result=$this->sale_model->select_sale_mont_wise()->result_array();
        echo json_encode($result);

    }

    function get_oil_tin(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->sale_model->select_number_of_oiltin_for_sale($post_data['oil_tin_id']);
        echo json_encode($result,JSON_NUMERIC_CHECK);

    }









}
?>