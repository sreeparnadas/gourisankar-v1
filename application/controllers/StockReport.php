<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StockReport extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this -> load -> model('person');
        $this -> load -> model('Stock_report_model');
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


    public function angular_view_report(){
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
            .report-table tr th,.report-table tr td{
                border: 1px solid black !important;
                font-size: 11px;
                line-height: 0px;
            }
            .add-scroll{
                height:500px;
                overflow-y: scroll;
            }
         
            .table-fixed tbody {
                height: 500px;
                overflow-y: scroll;
                background-color:aqua;
            }
         
           

        </style>

        <div class="d-flex col-12" ng-include="'application/views/header.html'"></div>



        <div class="d-flex col-12">
            <div class="col-12">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-justified indigo" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==1 && selectedTab" href="#" role="tab" ng-click="setTab(1)"><i class="fas fa-user-graduate"></i></i>Mseed Stock</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==2 && selectedTab" href="#" role="tab" ng-click="setTab(2)"><i class="fa fa-envelope"></i>MOil Stock </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" ng-style="tab==3 && selectedTab" href="#" role="tab" ng-click="setTab(3)"><i class="fa fa-envelope"></i>Oil Cake Stock </a>
                    </li>

                </ul>
                <!-- Tab panels -->
                <div class="tab-content">
                    <!--Panel 1-->
                    <div ng-show="isSet(1)">
                        <div id="row my-tab-1">
                            <form name="mseedReport" class="form-horizontal" id="saleForm">
                                <div class="" id="sale-master-card">
                                    <div class="d-flex pt-0 pb-0 bg-gray-2">
                                        <div class="d-flex">
                                            <div class="col"><input type="date" class="form-control" ng-model="start_date"></div>
                                            <div class="col ml-1 mr-1">TO</div>
                                            <div class="col"><input type="date" class="form-control" ng-model="end_date"></div>
                                            <div class="col ml-1"><input type="button" class="form-control" value="Submit" ng-click="mseed_date_wise_report(start_date,end_date)"></div>
                                        </div>
                                    </div>

                                    <div class="d-flex" id="mseed-stock-report-div">
                                    <div class="pt-2">
                                       <table class="table table-bordered table-responsive report-table" id="stock-reprt-table">   
                                            <thead>
                                           
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Op.Stock</th>
                                                    <th colspan="6" class="text-center bg-warning">Purchase(If any)</th>
                                                    <th>Total</th>
                                                    <th>Purchased Mseed</th>
                                                    <th>Mseed.for.production</th>
                                                    <th>Produced.Moil</th>
                                                    <th>Produced.Oilcake</th>
                                                    <th>Wastage</th>
                                                    <th>Clos.Stock</th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th>Vendor</th>
                                                    <th>GSTNo</th>
                                                    <th>Total amt</th>
                                                    <th>SGST</th>
                                                    <th>CGST</th>
                                                    <th>IGST</th>
                                                    <th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="">
                                                <tr ng-repeat="x in mustardseedStockReportDaywise">
                                                    <td>{{x.report_date}}</td>
                                                    <td class="text-center">{{x.opening_balance}}</td>
                                                    <td>{{(x.vendor_names || 'empty')}}</td>
                                                    <td></td><td></td><td></td><td></td><td></td>

                                                    <td></td>
                                                    <td class="text-center">{{x.mustard_seed_purchased}}</td>

                                                    <td class="text-center">{{x.mustard_seed_used}}</td>
                                                    <td class="text-center">{{x.produced_moil}}</td>
                                                    <td class="text-center">{{x.produced_oil_cake}}</td>
                                                    <td class="text-center">{{x.wastage}}</td>

                                                    <td class="text-center">{{(x.opening_balance - x.mustard_seed_used + x.mustard_seed_purchased) | number:2}}</td>
                                                </tr>
                                           
                                            </tbody>
                                        </table>
                                    </div>
                                       


                                     
                                    </div>
                                </div>

                            </form>
                            <!-- <pre>mustardseedStockReportDaywise={{mustardseedStockReportDaywise | json}}</pre> -->

                        </div> <!--//End of my tab1//-->

                    </div>		<!--//End of first tab div//-->




                                 <!-- tab2 -->


				<!--Panel 2-->
                    <div ng-show="isSet(2)">
							<div id="row my-tab-2">
								<form name="moilReport" class="form-horizontal" id="moil-report">
									<div class="" >
										<div class="d-flex pt-0 pb-0 bg-gray-2">
											<div class="d-flex">
												<div class="col"><input type="date" class="form-control" ng-model="start_date"></div>
												<div class="col ml-1 mr-1">TO</div>
												<div class="col"><input type="date" class="form-control" ng-model="end_date"></div>
												<div class="col ml-1"><input type="button" class="form-control" value="Submit" ng-click="mustard_oil_date_wise_report(start_date,end_date)"></div>
											</div>
										</div>

										<div class="d-flex" id="moil-stock-report-div">
												<table class="table table-bordered table-responsive report-table" id="stock-reprt-table">   
												<thead>
											
													<tr>
														<th>Date</th>
														<th>Op.Stock</th>
														<th>Manufactured</th>
														<th>Total</th>
														<th>Moil.sold</th>
														<th>Memo.No</th>
														<th>Stock in hand</th>
													</tr>
												
												</thead>
												<tbody class="">
													<tr ng-repeat="x in mustardOilStockReportDaywise">
														<td>{{x.report_date}}</td>
														<td class="text-center">{{x.opening_balance}}</td>
														
														<td class="text-center">{{x.moil_manufactured_inward}}</td>

														<td class="text-center">{{(x.opening_balance + x.moil_manufactured_inward) | number:2}}</td>
														<td class="text-center">{{x.product_sold | number:2}}</td>
														<td class="text-center">{{x.memo_numbers}}</td>
														<td class="text-center">{{(x.opening_balance + x.moil_manufactured_inward - x.product_sold) | number :2}}</td>
													</tr>
											
												</tbody>
											</table>                                    


										
										</div>
								</div>

							</form>
                        <!-- <pre>mustardOilStockReportDaywise={{mustardOilStockReportDaywise | json}}</pre> -->

						</div> <!--//End of my tab2//-->
					</div>		<!--//End of my tab2 last div//-->
			
			
			
			<!--Panel 3-->
			   <div ng-show="isSet(3)">
                        <div id="row my-tab-3">
                            <form name="oilCakeReport" class="form-horizontal" id="oil-cake-report">
                                <div class="" >
                                    <div class="d-flex pt-0 pb-0 bg-gray-2">
                                        <div class="d-flex">
                                            <div class="col"><input type="date" class="form-control" ng-model="start_date"></div>
                                            <div class="col ml-1 mr-1">TO</div>
                                            <div class="col"><input type="date" class="form-control" ng-model="end_date"></div>
                                            <div class="col ml-1"><input type="button" class="form-control" value="Submit" ng-click="oil_cake_date_wise_report(start_date,end_date)"></div>
                                        </div>
                                    </div>

                                    <div class="d-flex" id="oil-cake-stock-report-div">
                                       <table class="table table-bordered table-responsive report-table" id="stock-reprt-table">   
                                            <thead>
                                           
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Op.Stock</th>
                                                    <th>Manufactured</th>
                                                    <th>Total</th>
                                                    <th>oilCake.sold</th>
                                                    <th>Memo.No</th>
                                                    <th>Stock in hand</th>
                                                </tr>
                                               
                                            </thead>
                                            <tbody class="">
                                                <tr ng-repeat="x in oilCakeStockReportDaywise">
                                                    <td>{{x.report_date}}</td>
                                                    <td class="text-center">{{x.opening_balance}}</td>
                                                    
                                                    <td class="text-center">{{x.oil_cake_manufactured_inward}}</td>

                                                    <td class="text-center">{{(x.opening_balance + x.oil_cake_manufactured_inward) | number:2}}</td>
                                                    <td class="text-center">{{x.product_sold | number:2}}</td>
                                                    <td class="text-center">{{x.memo_numbers}}</td>
                                                    <td class="text-center">{{(x.opening_balance + x.oil_cake_manufactured_inward - x.product_sold) | number :2}}</td>
                                                </tr>
                                           
                                            </tbody>
                                        </table>                                    


                                     
                                    </div>
                                </div>

                            </form>
                            <!-- <pre>oilCakeStockReportDaywise={{oilCakeStockReportDaywise | json}}</pre> -->

                        </div> <!--//End of my tab3//-->
                    
                 </div>		 <!--//End of my tab3 last div//-->
        </div>

        <?php
    }



    function get_mustard_seed_report_by_date(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Stock_report_model->select_mseed_report_by_date_to_date($post_data['start_date'],$post_data['end_date'])->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }


    
    function get_mustard_oil_report_by_date(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Stock_report_model->select_moil_report_by_date_to_date($post_data['start_date'],$post_data['end_date'])->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }



    function get_oil_cake_report_by_date(){
        $post_data =json_decode(file_get_contents("php://input"), true);
        $result=$this->Stock_report_model->select_oil_cake_report_by_date_to_date($post_data['start_date'],$post_data['end_date'])->result_array();
        $report_array['records']=$result;
        echo json_encode($report_array,JSON_NUMERIC_CHECK);

    }

    function get_sale_by_month(){
        $result=$this->sale_model->select_sale_mont_wise()->result_array();
        echo json_encode($result);

    }









}
?>