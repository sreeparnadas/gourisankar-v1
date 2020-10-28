<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SaleReport extends CI_Controller {

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
            .table-responsive{
                overflow-y:auto
            }

        </style>
     <div class="d-flex col-12" ng-include="'application/views/header.html'"></div>

     
        <div class="d-flex col-12">
            <div class="col-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==1 && selectedTab" href="#" role="tab" ng-click="setTab(1)"><i class="fas fa-user-graduate"></i></i>Moil sale</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==2 && selectedTab" href="#" role="tab" ng-click="setTab(2)"><i class="fa fa-envelope"></i>OilCake sale</a>
                    </li>
                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <div ng-show="isSet(1)">
                        <div id="my-tab-1">
                            <div class="card">
                                <div class="card-header">
                                <div class="d-flex pt-0 pb-0 bg-gray-2">
                                        <div class="d-flex">
                                            <div class="col"><input type="date" class="form-control" ng-model="start_date"></div>
                                            <div class="col ml-1 mr-1">TO</div>
                                            <div class="col"><input type="date" class="form-control" ng-model="end_date"></div>
                                            <input type="button" class="form-control" value="Show" ng-click="getMoilDataToSendInExcel(start_date,end_date)">
                                            <button type="button" class="btn btn-primary" ng-click="saveToExcel('test2.xls',moilExceldata)">Send to Excel</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table cellpadding="0" cellspacing="0" class="table table-bordered table-responsive">
                                        <tr>
                                            <th class="p-0">SL></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('memo_number')">Date<i class="glyphicon" ng-class="getIcon('memo_number')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('memo_number')">Memo No<i class="glyphicon" ng-class="getIcon('memo_number')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('memo_number')">Buyers' name<i class="glyphicon" ng-class="getIcon('memo_number')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('memo_number')">GST no<i class="glyphicon" ng-class="getIcon('memo_number')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('memo_number')">Tin<i class="glyphicon" ng-class="getIcon('memo_number')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('memo_number')">Qty(ql)<i class="glyphicon" ng-class="getIcon('memo_number')"></i></th>

                                            <th class="p-0 pl-1" ng-click="changeSorting('person_name')">rate<i class="glyphicon" ng-class="getIcon('person_name')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('mobile_no')">GrossValue<i class="glyphicon" ng-class="getIcon('mobile_no')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('grand_total')">sgst ratet(%)<i class="glyphicon" ng-class="getIcon('grand_total')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('sale_date')">sgst<i class="glyphicon" ng-class="getIcon('sale_date')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('sale_month')">cgst ratet(%)</th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('bill_type_name')">cgst</th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('sale_month')">igst ratet(%)</th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('bill_type_name')">igst</th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('bill_type_name')">Total tax</th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('bill_type_name')">Total amount</th>

                                        </tr>
                                        <tbody ng-repeat="sale in moilExceldata | filter : searchItem  | orderBy:sort.active:sort.descending">
                                            <tr ng-class-even="'banana'" ng-class-odd="'bee'">
                                                <td class="p-0  pl-1">{{ $index+1}}</td>
                                                <td class="p-0  pl-1">{{sale.sale_date}}</td>
                                                <td class="p-0  pl-1">{{sale.memo_number}}</td>
                                                <td class="p-0  pl-1">{{sale.buyer_name}}</td>
                                                <td class="p-0  pl-1">{{sale.gst_number}}</td>
                                                <td class="p-0  pl-1">{{sale.tin}}</td>
                                                <td class="p-0  pl-1">{{sale.qty_in_quintal}}</td>

                                                <td class="p-0  pl-1">{{sale.rate}}</td>
                                                <td class="p-0  pl-1">{{sale.gross_value}}</td>
                                                <td class="text-right p-0  pl-1">{{sale.sgst_rate | number:2}}</td>
                                                <td class="text-right p-0  pl-1">{{sale.sgst}}</td>
                                                <td class="p-0  pl-1 style="padding-left: 20px;">{{sale.cgst_rate}}</td>
                                                <td class="p-0  pl-1" style="padding-left: 20px;">{{sale.cgst}}</td>
                                                <td class="p-0  pl-1 style="padding-left: 20px;">{{sale.igst_rate}}</td>
                                                <td class="p-0  pl-1" style="padding-left: 20px;">{{sale.igst}}</td>
                                                <td class="p-0  pl-1 style="padding-left: 20px;">{{sale.total_tax}}</td>
                                                <td class="p-0  pl-1" style="padding-left: 20px;">{{sale.total_amount}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer">
                                <p ng-show="noRecordsMoil" class="text-center font-weight-bold"> No records found</p>
                                   <!-- <pre>allSaleList = {{allSaleList | json}}</pre> -->
                                </div>
                            </div>
                        </div>
                    </div>


                    <div ng-show="isSet(2)">
                        <div id="my-tab-2">
                            <div class="card">
                                <div class="card-header">
                                <div class="d-flex pt-0 pb-0 bg-gray-2">
                                        <div class="d-flex">
                                            <div class="col"><input type="date" class="form-control" ng-model="start_date"></div>
                                            <div class="col ml-1 mr-1">TO</div>
                                            <div class="col"><input type="date" class="form-control" ng-model="end_date"></div>
                                            <input type="button" class="form-control" value="Show" ng-click="getOilCakeDataToSendInExcel(start_date,end_date)">
                                            <button type="button" class="btn btn-primary" ng-click="saveToExcel('test2.xls',oilCakeExceldata)">Send to Excel</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table cellpadding="0" cellspacing="0" class="table table-bordered table-responsive">
                                        <tr>
                                            <th class="p-0">SL></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('memo_number')">Date<i class="glyphicon" ng-class="getIcon('memo_number')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('memo_number')">Memo No<i class="glyphicon" ng-class="getIcon('memo_number')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('memo_number')">Buyers' name<i class="glyphicon" ng-class="getIcon('memo_number')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('memo_number')">GST no<i class="glyphicon" ng-class="getIcon('memo_number')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('memo_number')">Tin<i class="glyphicon" ng-class="getIcon('memo_number')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('memo_number')">Qty(ql)<i class="glyphicon" ng-class="getIcon('memo_number')"></i></th>

                                            <th class="p-0 pl-1" ng-click="changeSorting('person_name')">rate<i class="glyphicon" ng-class="getIcon('person_name')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('mobile_no')">GrossValue<i class="glyphicon" ng-class="getIcon('mobile_no')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('grand_total')">sgst ratet(%)<i class="glyphicon" ng-class="getIcon('grand_total')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('sale_date')">sgst<i class="glyphicon" ng-class="getIcon('sale_date')"></i></th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('sale_month')">cgst ratet(%)</th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('bill_type_name')">cgst</th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('sale_month')">igst ratet(%)</th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('bill_type_name')">igst</th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('bill_type_name')">Total tax</th>
                                            <th class="p-0 pl-1" ng-click="changeSorting('bill_type_name')">Total amount</th>

                                        </tr>
                                        <tbody ng-repeat="sale in oilCakeExceldata | filter : searchItem  | orderBy:sort.active:sort.descending">
                                            <tr ng-class-even="'banana'" ng-class-odd="'bee'">
                                                <td class="p-0  pl-1">{{ $index+1}}</td>
                                                <td class="p-0  pl-1">{{sale.sale_date}}</td>
                                                <td class="p-0  pl-1">{{sale.memo_number}}</td>
                                                <td class="p-0  pl-1">{{sale.buyer_name}}</td>
                                                <td class="p-0  pl-1">{{sale.gst_number}}</td>
                                                <td class="p-0  pl-1">{{sale.packet}}</td>
                                                <td class="p-0  pl-1">{{sale.qty_in_quintal}}</td>

                                                <td class="p-0  pl-1">{{sale.rate}}</td>
                                                <td class="p-0  pl-1">{{sale.gross_value}}</td>
                                                <td class="text-right p-0  pl-1">{{sale.sgst_rate | number:2}}</td>
                                                <td class="text-right p-0  pl-1">{{sale.sgst}}</td>
                                                <td class="p-0  pl-1 style="padding-left: 20px;">{{sale.cgst_rate}}</td>
                                                <td class="p-0  pl-1" style="padding-left: 20px;">{{sale.cgst}}</td>
                                                <td class="p-0  pl-1 style="padding-left: 20px;">{{sale.igst_rate}}</td>
                                                <td class="p-0  pl-1" style="padding-left: 20px;">{{sale.igst}}</td>
                                                <td class="p-0  pl-1 style="padding-left: 20px;">{{sale.total_tax}}</td>
                                                <td class="p-0  pl-1" style="padding-left: 20px;">{{sale.total_amount}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer">
                                <p ng-show="noRecords" class="text-center font-weight-bold"> No records found</p>
                                   <!-- <pre>allSaleList = {{allSaleList | json}}</pre> -->
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        <?php
    }

    function get_excel_data_for_mustard_oil(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->sale_model->select_excel_data_for_mustard_oil($post_data['start_date'],$post_data['end_date'])->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }

    function get_excel_data_for_oil_cake(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->sale_model->select_excel_data_for_oil_cake($post_data['start_date'],$post_data['end_date'])->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);
    }

}
?>