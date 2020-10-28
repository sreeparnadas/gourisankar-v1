app.controller("purchaseCtrl", function ($scope,$http,$filter,$rootScope,$timeout) {
    $scope.msg = "This is Purchase controller";
    $scope.tab = 1;
    $scope.submitStatus=false;
    $scope.updateStatus=false;
    $scope.isUpdateable=false;
    $scope.showBillNo=false;
    $scope.hideVendorDetails=false;
    $scope.sort = {
        active: '',
        descending: undefined
    };
    $scope.purchaseUpdated=false;
    $scope.findObjectByKey = function(array, key, value) {
        for (var i = 0; i < array.length; i++) {
            if (array[i][key] === value) {
                return array[i];
            }
        }
        return null;
    };
    $scope.changeSorting = function(column) {

        var sort = $scope.sort;

        if (sort.active == column) {
            sort.descending = !sort.descending;
        }
        else {
            sort.active = column;
            sort.descending = false;
        }
    };



    $scope.data = {
        switch1: true,
        switch2: false,
        switch3: false,
        switch4: true,
        switch5: true,
        switch6: false
    };

    this.startDate = new Date();
    $scope.getIcon = function(column) {

        var sort = $scope.sort;

        if (sort.active == column) {
            return sort.descending
                ? 'glyphicon-chevron-up'
                : 'glyphicon-chevron-down';
        }

        return 'glyphicon-star';
    };



    $scope.isDuplicate=false;
    $scope.btnSubmitDisable=false;
    $scope.defaultPurchaseDetails={
        discount: 0,
        sgst: 0,
        cgst: 0,
        igst: 0,
        sgst_rate: 0,
        cgst_rate: 0,
        igst_rate: 0,
        rate:0,
        discount:0,
        quantity:0,
        amount:0
    };

    $scope.defaultPurchaseMaster={
        cgstFactor: 0,
        sgstFactor: 0,
        igstFactor: 0,
        eway_bill_no: "",
        invoice_no: "",
        vehicle_fare: "",
        truck_no: "",
        bilty_no: "",
        transport_name: "",
        transport_mobile: "",
        licence_no: "",
        purchase_date_sql: "",
        eway_bill_date: "",
        eway_bill_date_sql: "",
        valid_from: "",
        valid_from_sql: "",
        valid_to: "",
        valid_to_sql: ""
    };
    $scope.changeDateFormat=function(userDate){
        return moment(userDate).format('YYYY-MM-DD');
    };
    $scope.purchaseMaster=angular.copy($scope.defaultPurchaseMaster);

    $scope.purchaseDetails=angular.copy($scope.defaultPurchaseDetails);
    $scope.setGstFactor=function(){
        if($scope.purchaseMaster.vendor.state_id!=19){
            $scope.purchaseMaster.cgstFactor=0;
            $scope.purchaseMaster.sgstFactor=0;
            $scope.purchaseMaster.igstFactor=1;
        }else{
            $scope.purchaseMaster.cgstFactor=0.5;
            $scope.purchaseMaster.sgstFactor=0.5;
            $scope.purchaseMaster.igstFactor=0;
        }

        var gstRate=$scope.purchaseDetails.product.gst_rate;

        $scope.purchaseDetails.sgst_rate=(gstRate*$scope.purchaseMaster.sgstFactor)/100;
        $scope.purchaseDetails.cgst_rate=(gstRate*$scope.purchaseMaster.cgstFactor)/100;
        $scope.purchaseDetails.igst_rate=(gstRate*$scope.purchaseMaster.igstFactor)/100;

        $scope.purchaseDetails.sgst=($scope.purchaseDetails.amount*$scope.purchaseDetails.sgst_rate);
        $scope.purchaseDetails.cgst=($scope.purchaseDetails.amount*$scope.purchaseDetails.cgst_rate);
        $scope.purchaseDetails.igst=($scope.purchaseDetails.amount*$scope.purchaseDetails.igst_rate);

    };


    $scope.gstRateChangeOfProduct=function(){
        var gstRate=$scope.purchaseDetails.product.gst_rate;
        $scope.purchaseDetails.sgst_rate=(gstRate*$scope.purchaseMaster.sgstFactor)/100;
        $scope.purchaseDetails.cgst_rate=(gstRate*$scope.purchaseMaster.cgstFactor)/100;
        $scope.purchaseDetails.igst_rate=(gstRate*$scope.purchaseMaster.igstFactor)/100;
    };


    $scope.setTab = function(newTab){
        $scope.tab = newTab;
    };

    $scope.isSet = function(tabNum){
        return $scope.tab === tabNum;
        if(newTab==1){
            $scope.isUpdateable=false;
        }
    };



      // $scope.purcaseData.purchase_date = new Date();
    $scope.totalPurchaseAmount=0;
    $scope.vendorList={};
    $scope.loadAllVendors=function(){
        var request = $http({
            method: "post",
            url: site_url+"/Vendor/get_vendors",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.vendorList=response.data.records;
            $scope.purchaseMaster.vendor=$scope.vendorList[0];
            $scope.setGstFactor();
        });
    };//end of loadVendors
    $scope.loadAllVendors();

    $scope.prductList={};
    $scope.loadAllProducts=function(){
        var request = $http({
            method: "post",
            url: site_url+"/product/get_purchaseable_products",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.prductList=response.data.records;
            var productIndex=$scope.prductList.findIndex(x=>x.product_id==1);
            $scope.purchaseDetails.product=$scope.prductList[productIndex];
            $scope.setGstFactor();
            $scope.setGst();

        });
    };//end of loadVendors
    $scope.loadAllProducts();

    $scope.removeRow = function(index){
        $scope.purchaseForm.$setDirty();
        // remove the row specified in index
        $scope.purchaseDetailsDataList.splice( index, 1);
        // if no rows left in the array create a blank array
        if ($scope.purchaseDetailsDataList.length() === 0){
            $scope.purchaseDetailsDataList = [];
        }
    };

    $scope.setAmount=function(){
        var discount=$scope.getDiscount();
        var amt=($scope.purchaseDetails.quantity)*($scope.purchaseDetails.rate);
         $scope.purchaseDetails.amount=$rootScope.roundNumber((amt-discount),2);
    };

    $scope.currentIndex=-1;

    $scope.purchaseDetailsDataList=[];
    $scope.addPurchaseDetailsData=function(purchase){
        $scope.isDuplicate=false;
        var total=0;
        total=purchase.amount+purchase.sgst+purchase.cgst+purchase.igst;
        purchase.total=total;
        var test=0;
        angular.forEach($scope.purchaseDetailsDataList, function(value, key) {

            if(angular.equals(value,purchase))
                test++;

        });
        
        if(test==0){
            var test=angular.copy(purchase);

            $scope.purchaseDetailsDataList.unshift(test);
        }else{
            $scope.isDuplicate=true;

        }

    };
    $scope.getDiscount=function(){
        var purchaseValue=$rootScope.roundNumber(($scope.purchaseDetails.quantity)*($scope.purchaseDetails.rate),2);
        var disc_rate=$scope.purchaseDetails.discount;
        var amt_disc=$rootScope.roundNumber(purchaseValue*(disc_rate/100),2);
        return amt_disc;
    };
    $scope.setGst=function(){
        var purchaseValue=$rootScope.roundNumber(($scope.purchaseDetails.quantity)*($scope.purchaseDetails.rate),2);
            $scope.purchaseDetails.sgst=$rootScope.roundNumber((purchaseValue*$scope.purchaseDetails.sgst_rate),2);
            $scope.purchaseDetails.cgst=$rootScope.roundNumber((purchaseValue*$scope.purchaseDetails.cgst_rate),2);
            $scope.purchaseDetails.igst=$rootScope.roundNumber((purchaseValue*$scope.purchaseDetails.igst_rate),2);
    };


    $scope.purchaseDetailToSave=[];
    $scope.$watchCollection("purchaseDetailsDataList",function (newValue, oldValue) {
        if(newValue!=oldValue){
            $scope.purchaseTableFooter=alasql('SELECT sum(sgst)as totalSgst,sum(cgst) as totalCgst,sum(igst) as totalIgst,sum(amount) as totalPurchaseAmount  from ? ',[newValue]);
             var totalPurchase=$scope.purchaseTableFooter[0].totalPurchaseAmount;
            var roundDecimal=$rootScope.roundNumber(totalPurchase-parseInt(totalPurchase),2);
            if(roundDecimal==0){
                $scope.purchaseMaster.roundedOff=0;
            }else if(roundDecimal>0.49) {
                    $scope.purchaseMaster.roundedOff = $rootScope.roundNumber(1-roundDecimal,2);
            }else{
                    $scope.purchaseMaster.roundedOff = $rootScope.roundNumber(0-roundDecimal,2);
            }
            $scope.purchaseMaster.grand_total=totalPurchase + $scope.purchaseMaster.roundedOff;//get bill_amount using roundoff

            $scope.purchaseDetailToSave=alasql('SELECT ' +
                'quantity' +
                ',product->product_id as product_id'+
                ',product->default_unit_id as unit_id'+
                ',rate' +
                ',discount' +
                ',sgst_rate' +
                ',cgst_rate' +
                ',igst_rate' +
                '  from ? ',[newValue]);



        }
    });



    $scope.savePurchaseDetails=function(purchaseMaster,purchaseDetailToSave){
        var pm={};
        var pdl=[];
        pm.vendor_id=purchaseMaster.vendor.person_id;
        pm.invoice_no=purchaseMaster.invoice_no;
        pm.purchase_date=purchaseMaster.purchase_date;
        pm.eway_bill_no=purchaseMaster.eway_bill_no;
        pm.eway_bill_date=purchaseMaster.eway_bill_date;
        pm.vehicle_fare=purchaseMaster.vehicle_fare;
        pm.truck_no=purchaseMaster.truck_no;
        pm.bilty_no=purchaseMaster.bilty_no;
        pm.transport_name=purchaseMaster.transport_name;
        pm.transport_mobile=purchaseMaster.transport_mobile;
        pm.licence_no=purchaseMaster.licence_no;
        pm.valid_from=purchaseMaster.valid_from;
        pm.valid_to=purchaseMaster.valid_to;
        pm.roundedOff=purchaseMaster.roundedOff;
        pm.grand_total=purchaseMaster.grand_total;
        pdl=angular.copy(purchaseDetailToSave);
        var request = $http({
            method: "post",
            url: site_url+"/purchase/save_new_purchase",
            data: {
                purchase_master: pm,
                purchase_details_list: pdl
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.reportArray=response.data.records;
            if($scope.reportArray.success==1){
                $scope.isUpdateable=true;
                $scope.showBillNo=true;
                $scope.submitStatus = true;
                $timeout(function() {
                    $scope.submitStatus = false;
                }, 5000);
                //$scope.btnSubmitDisable=true;
                $scope.purchaseMaster.purchase_master_id=$scope.reportArray.purchase_master_id;

                $scope.purchasedProductGstValue=alasql('SELECT sum(sgst)as sgst,sum(cgst) as cgst,sum(igst) as igst  from ? ',[$scope.purchaseDetailsDataList]);

                var tempPurchaseDetail={};
                tempPurchaseDetail.purchase_master_id=$scope.purchaseMaster.purchase_master_id;
                tempPurchaseDetail.vendor_id=$scope.purchaseMaster.vendor.person_id;
                tempPurchaseDetail.vendor_name=$scope.purchaseMaster.vendor.vendor_name;
                tempPurchaseDetail.company_name=$scope.purchaseMaster.vendor.company_name;
                tempPurchaseDetail.mobile_no=$scope.purchaseMaster.vendor.mobile_no;
                tempPurchaseDetail.purchase_date=$filter('date')($scope.purchaseMaster.purchase_date, "dd/MM/yyyy");
                tempPurchaseDetail.purchase_month=$filter('date')($scope.purchaseMaster.purchase_date, "MMMM");
                tempPurchaseDetail.sgst=$scope.purchasedProductGstValue[0].sgst;
                tempPurchaseDetail.cgst=$scope.purchasedProductGstValue[0].cgst;
                tempPurchaseDetail.igst=$scope.purchasedProductGstValue[0].igst;
                tempPurchaseDetail.total_purchase_amount=$scope.purchaseMaster.grand_total;
               // console.log(tempPurchaseDetail);
                $scope.allPurchaseList.unshift(tempPurchaseDetail);
            }
        });

        
    };

    $scope.dd = new Date().getDate();
    $scope.mm = new Date().getMonth()+1;
    $scope.yy = new Date().getFullYear();
    $scope.day= ($scope.dd<10)? '0'+$scope.dd : $scope.dd;
    $scope.month= ($scope.mm<10)? '0'+$scope.mm : $scope.mm;
    // $scope.purchaseMaster.purchase_date=($scope.day+"/"+$scope.month+"/"+$scope.yy);


    $scope.printDivAngularjs = function (divName) {
        var printContents = document.getElementById(divName).innerHTML;
        var originalContents = document.body.innerHTML;

        if (navigator.userAgent.toLowerCase().indexOf('chrome') > -1) {
            var popupWin = window.open('', '_blank', 'width=600,height=600,scrollbars=no,menubar=no,toolbar=no,location=no,status=no,titlebar=no');
            popupWin.window.focus();
            popupWin.document.write('<!DOCTYPE html><html><head>' +
                '<link rel="stylesheet" type="text/css" href="style.css" />' +
                '</head><body onload="window.print()"><div class="reward-body">' + printContents + '</div></html>');
            popupWin.onbeforeunload = function (event) {
                popupWin.close();
                return '.\n';
            };
            popupWin.onabort = function (event) {
                popupWin.document.close();
                popupWin.close();
            }
        } else {
            var popupWin = window.open('', '_blank', 'width=800,height=600');
            popupWin.document.open();
            popupWin.document.write('<html><head><link rel="stylesheet" type="text/css" href="style.css" /></head><body onload="window.print()">' + printContents + '</html>');
            popupWin.document.close();
        }
        popupWin.document.close();

        return true;

    };

    $scope.loadAllPurchase=function(){
        var request = $http({
            method: "post",
            url: site_url+"/purchase/get_all_purchase",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.allPurchaseList=response.data.records;
        });
    };
    //loading sale bills
    $scope.loadAllPurchase();
//working
    $scope.options = [{ name: "a", id: 1 }, { name: "b", id: 2 }, { name: "c", id: 3 }];
    $scope.selectedOption = $scope.options[1];


    $scope.getPurchaseFromTableForUpdate=function(purchase){
        $scope.purchaseDetailsDataList=[];
        $scope.purchaseUpdated=false;
        var purchaseMasterId=purchase.purchase_master_id;
        $scope.isUpdateable=true;
        //$scope.selectState(purchaseMaster.vendor.state_id);
        var request = $http({
            method: "post",
            url: site_url+"/purchase/get_purchase_details_by_purchase_master_id",
            data: {
                purchase_master_id: purchaseMasterId
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.purchaseForm.$setPristine();
            var purchaseDetails=response.data.records;
            //console.log(purchaseDetails);
            $scope.tempList=angular.copy(purchaseDetails);
            angular.forEach($scope.tempList, function(value, key) {
                var product=alasql('select * from ? where product_id=?',[$scope.prductList,value.product_id])[0];
                value.product=product;
                $scope.purchaseDetailsDataList.push(value);

            });
            //$scope.productDataListTemp=alasql('select *,prductList from ?',[$scope.purchaseDetailsDataList]);
            $scope.tab=1;
        });
        var request = $http({
            method: "post",
            url: site_url+"/purchase/get_purchase_master_by_purchase_master_id",
            data: {
                purchase_master_id: purchaseMasterId
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.purchaseForm.$setPristine();
            var purchaseMaster=response.data.records;
           //console.log(purchaseMaster);
            $scope.purchaseMaster =angular.copy(purchaseMaster);
            //$scope.purchaseMaster[0].vendor=alasql('SELECT *  from ? where person_id = ?',[$scope.vendorList,$scope.purchaseMaster[0].vendor_id]);
            var customer=$scope.findObjectByKey($scope.vendorList,'person_id',purchase.vendor_id);
            $scope.purchaseMaster.vendor=$scope.vendorList[$scope.vendorList.indexOf(customer)];
            $scope.setGstFactor();
            $scope.tab=1;
        });
    };

    $scope.updatePurchaseDetails=function(purchaseMaster,purchaseDetailToSave){
        var pm={};
        var pdl=[];
        pm.purchase_master_id=purchaseMaster.purchase_master_id;
        pm.vendor_id=purchaseMaster.vendor.person_id;
        pm.invoice_no=purchaseMaster.invoice_no;
        pm.purchase_date=purchaseMaster.purchase_date;
        pm.eway_bill_no=purchaseMaster.eway_bill_no;
        pm.eway_bill_date=purchaseMaster.eway_bill_date;
        pm.vehicle_fare=purchaseMaster.vehicle_fare;
        pm.truck_no=purchaseMaster.truck_no;
        pm.bilty_no=purchaseMaster.bilty_no;
        pm.transport_name=purchaseMaster.transport_name;
        pm.transport_mobile=purchaseMaster.transport_mobile;
        pm.licence_no=purchaseMaster.licence_no;
        pm.valid_from=purchaseMaster.valid_from;
        pm.valid_to=purchaseMaster.valid_to;
        pm.roundedOff=purchaseMaster.roundedOff;
        pm.grand_total=purchaseMaster.grand_total;
        pdl=angular.copy(purchaseDetailToSave);
        var request = $http({
            method: "post",
            url: site_url+"/purchase/update_saved_purchase",
            data: {
                purchase_master: pm,
                purchase_details_list: pdl
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.reportArray=response.data.records;
            $scope.purchaseUpdated=true;
            if($scope.reportArray.success==1){
                $scope.isUpdateable=true;
                $scope.updateStatus = true;
                $timeout(function() {
                    $scope.updateStatus = false;
                }, 5000);
                //$scope.btnSubmitDisable=true;
                $scope.purchaseMaster.purchase_master_id=$scope.reportArray.purchase_master_id;
                var tempPurchaseDetail={};
                tempPurchaseDetail.purchase_master_id=$scope.purchaseMaster.purchase_master_id;

                tempPurchaseDetail.vendor_name=$scope.purchaseMaster.vendor.person_name;
                tempPurchaseDetail.company_name=$scope.purchaseMaster.vendor.company_name;
                tempPurchaseDetail.mobile_no=$scope.purchaseMaster.vendor.mobile_no;
                tempPurchaseDetail.purchase_date=$scope.purchaseMaster.purchase_date;
                tempPurchaseDetail.total_purchase_amount=$scope.purchaseMaster.grand_total;
                $scope.allPurchaseList.unshift(tempPurchaseDetail);
            }
        });
    };
    $scope.resetPurchaseDetails=function () {
        $scope.purchaseMaster=angular.copy($scope.defaultPurchaseMaster);
        $scope.purchaseDetails=angular.copy($scope.defaultPurchaseDetails);
        $scope.purchaseDetailsDataList=[];
        $scope.isUpdateable=false;
    };

    $scope.hideVendorDetailsDiv=function(flag){
        $scope.hideVendorDetails=flag;
    };

     $scope.dateChanged = function() {
        // console.log(this);

     }




});

