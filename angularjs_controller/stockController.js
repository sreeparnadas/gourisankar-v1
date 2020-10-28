app.controller("stockCtrl", function ($scope,$http,$filter,$rootScope,$timeout) {
    $scope.msg = "This is Stock Production controller";
    $scope.seedToOilConversion = false;
    $scope.blankToOilTinConversion = false;
    $scope.successCss = {
        "background-color" : "green"
    }


    $scope.tab = 1;
    $scope.sort = {
        active: '',
        descending: undefined
    };
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
    $scope.getIcon = function(column) {

        var sort = $scope.sort;

        if (sort.active == column) {
            return sort.descending
                ? 'glyphicon-chevron-up'
                : 'glyphicon-chevron-down';
        }

        return 'glyphicon-star';
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

    $scope.changeDateFormat=function(userDate){
        return moment(userDate).format('YYYY-MM-DD');
    };

    $scope.seedToOil={};
    $scope.vendorList={};
    $scope.loadAllVendors=function(){
        var request = $http({
            method: "post",
            url: site_url+"/vendor/get_vendors",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.vendorList=response.data.records;
            $scope.purchaseMaster.vendor=$scope.vendorList[0];
        });
    };//end of loadVendors
    $scope.loadAllVendors();

    $scope.prductList={};
    $scope.loadAllProducts=function(){
        var request = $http({
            method: "post",
            url: site_url+"/product/get_inforce_products",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.prductList=response.data.records;
            $scope.mustardSeed=alasql('select * from ? where product_id =1',[$scope.prductList])[0];
            $scope.mustardOil=alasql('select * from ? where product_id =3',[$scope.prductList])[0];
            $scope.oilCake=alasql('select * from ? where product_id =2',[$scope.prductList])[0];
            $scope.blankTin=alasql('select * from ? where product_id =4',[$scope.prductList])[0];
            $scope.oilTin=alasql('select * from ? where product_id =6',[$scope.prductList])[0];
            $scope.seedToOil.mustardSeed=angular.copy($scope.mustardSeed);
            $scope.seedToOil.mustardOil=angular.copy($scope.mustardOil);
            $scope.seedToOil.oilCake=angular.copy($scope.oilCake);
            $scope.oilToTin.mustardOil=angular.copy($scope.mustardOil);
            $scope.oilToTin.blankTin=angular.copy($scope.blankTin);
            $scope.oilToTin.oilTin=angular.copy($scope.oilTin);

        });
    };//end of loadVendors
    $scope.loadAllProducts();

    
    $scope.loadAllStocks=function(){
        $scope.allStockList = [];
        var request = $http({
            method: "post",
            url: site_url+"/stock/moil_closing_balance",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            var moil =response.data.moil;
            var tempObj = {};
            tempObj.closing_balance= moil.opening_balance + moil.moil_manufactured_inward - moil.product_sold;
            tempObj.closing_date = moil.report_date;
            tempObj.product = 'Mustard Oil';

            $scope.allStockList.push(tempObj);
        });



        var request = $http({
            method: "post",
            url: site_url+"/stock/mseed_closing_balance",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            var mseed = response.data.mseed;
            var tempObj = {};
            tempObj.closing_balance=mseed.opening_balance + mseed.mustard_seed_purchased - mseed.mustard_seed_used;
            
            tempObj.closing_balance = $rootScope.roundNumber(tempObj.closing_balance,2);
            tempObj.closing_date = mseed.report_date;
            tempObj.product = 'Mustard Seed';
            $scope.allStockList.push(tempObj);
        });

        var request = $http({
            method: "post",
            url: site_url+"/stock/oil_cake_closing_balance",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            var oilCake = response.data.oilCake;
            var tempObj = {};
            tempObj.closing_balance = oilCake.opening_balance + oilCake.oil_cake_manufactured_inward - oilCake.product_sold;
            tempObj.closing_date = oilCake.report_date;
            tempObj.product = 'Oil Cake';
            $scope.allStockList.push(tempObj);
            
            
        });
    };
    $scope.loadAllStocks();

    $scope.getTotalOilTinInQuintal=function(){
        var oilTinInQuintal= $scope.oilToTin.oilTin.in_quintal;
        $scope.oilToTin.mustardOil.mustard_oil_quantity = $scope.oilToTin.blankTin.blank_tin_quantity *  oilTinInQuintal;
        $scope.oilToTin.mustardOil.mustard_oil_quantity= $rootScope.roundNumber($scope.oilToTin.mustardOil.mustard_oil_quantity,2);
    };


 


    $scope.removeRow = function(index){
        $scope.purchaseForm.$setDirty();
        // remove the row specified in index
        $scope.purchaseDetailsDataList.splice( index, 1);
        // if no rows left in the array create a blank array
        if ($scope.purchaseDetailsDataList.length() === 0){
            $scope.purchaseDetailsDataList = [];
        }
    };


    $scope.currentIndex=-1;

    $scope.dd = new Date().getDate();
    $scope.mm = new Date().getMonth()+1;
    $scope.yy = new Date().getFullYear();
    $scope.day= ($scope.dd<10)? '0'+$scope.dd : $scope.dd;
    $scope.month= ($scope.mm<10)? '0'+$scope.mm : $scope.mm;
    //$scope.seedToOil.record_date=($scope.day+"/"+$scope.month+"/"+$scope.yy);

    $scope.saveSeedToOil=function (seedToOil) {
        var master_date=seedToOil.record_date;
        master_date=$scope.changeDateFormat(master_date);
        var stockdtls={};
        var stockdtls = [

            {product_id:$scope.seedToOil.mustardSeed.product_id,inward:0, outward:$scope.seedToOil.mustardSeed.mustard_seed_quantity},

            {product_id:$scope.seedToOil.mustardOil.product_id,inward:$scope.seedToOil.mustardOil.mustard_oil_quantity, outward:0},

            {product_id:$scope.seedToOil.oilCake.product_id,inward:$scope.seedToOil.oilCake.oil_cake_quantity, outward:0}

        ];
        var request = $http({
            method: "post",
            url: site_url+"/stock/save_new_seed_to_oil",
            data: {
                master_date: master_date
                ,stock_details: stockdtls
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.reportArray=response.data.records;
            if($scope.reportArray.success==1){
                $scope.seedToOilConversion = true;
                $timeout(function() {
                    $scope.seedToOilConversion = false;
                }, 5000);
            }
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
                tempPurchaseDetail.mobile_no=$scope.purchaseMaster.vendor.mobile_no;
                tempPurchaseDetail.purchase_date=$scope.purchaseMaster.purchase_date;
                tempPurchaseDetail.total_purchase_amount=$scope.purchaseMaster.grand_total;
                $scope.allPurchaseList.unshift(tempPurchaseDetail);
            }
        });
    };
    $scope.resetSeedOilStockDetails=function () {
        $scope.seedToOil.mustardSeed.mustard_seed_quantity="";
        $scope.seedToOil.mustardOil.mustard_oil_quantity="";
        $scope.seedToOil.oilCake.oil_cake_quantity="";
    };
    $scope.oilToTin={};
    //$scope.oilToTin.record_date=($scope.day+"/"+$scope.month+"/"+$scope.yy);

    $scope.saveOilToBlankTin=function (oilToTin) {
        var master_date=oilToTin.record_date;
        master_date=$scope.changeDateFormat(master_date);
        var stockdtls={};
        var stockdtls = [

            {product_id:$scope.oilToTin.mustardOil.product_id,inward:0, outward:$scope.oilToTin.mustardOil.mustard_oil_quantity},

            {product_id:$scope.oilToTin.blankTin.product_id,inward:0, outward:$scope.oilToTin.blankTin.blank_tin_quantity},

            {product_id:$scope.oilToTin.oilTin.product_id,inward:$scope.oilToTin.oilTin.oil_tin_quantity, outward:0}

        ];
        var request = $http({
            method: "post",
            url: site_url+"/stock/save_new_oil_to_tin_conversion",
            data: {
                master_date: master_date
                ,stock_details: stockdtls
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.reportArray=response.data.records;
            if($scope.reportArray.success==1){
                $scope.blankToOilTinConversion = true;
                $timeout(function() {
                    $scope.blankToOilTinConversion = false;
                }, 5000);
            }
        });
    };
    $scope.resetOilTinStockDetails=function () {
        $scope.oilToTin.mustardOil.mustard_oil_quantity="";
        $scope.oilToTin.blankTin.blank_tin_quantity="";
        $scope.oilToTin.oilTin.oil_tin_quantity="";
    };



});

