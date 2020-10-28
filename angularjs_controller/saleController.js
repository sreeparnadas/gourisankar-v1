app.controller("saleCtrl", function ($scope,$http,$filter,$rootScope,$timeout,$filter,CommonCode) {
    $scope.msg = "This is Sale controller";
    $scope.tab = 1;
    $scope.saleSubmitStatus=false;
    $scope.updateStatus=false;
    $scope.updateOilCakeStatus=false;
    $scope.isUpdateOilCakeStatus=false;
    $scope.isUpdateableOil=false;
    $scope.isUpdateableOilCake = false;
    $scope.isUpdateableOilCake=false;
    $scope.showBillNo=false;
    $scope.isSaveOilCake=false;
    $scope.showBillNoFromTableList=false;
    $scope.setTab = function(newTab){
        $scope.tab = newTab;
        if($scope.tab==2 && $scope.allSaleList==undefined){
            $scope.loadAllSale();       
        }
    };





    $scope.alerter = CommonCode;
    //$scope.alerter.show("Hello World");


    $scope.tab3DeveloperAriaShowHide=true;
    $scope.tab1BillMasterShow=true;
    $scope.oilCakeTab1BillMasterShow=true;
    $scope.tab1DeveloperAriaShowHide=true;
    $scope.oilCakeDeveloperAriaShowHide=true;

    $scope.barcodeOilBill = {
        format: 'CODE128',
        lineColor: '#000000',
        width: 2,
        height: 25,
        displayValue: false,
        fontOptions: '',
        font: 'monospace',
        textAlign: 'center',
        textPosition: 'bottom',
        textMargin: 2,
        fontSize: 20,
        background: '#ffffff',
        margin: 0,
        marginTop: undefined,
        marginBottom: undefined,
        marginLeft: undefined,
        marginRight: undefined,
        valid: function (valid) {
        }
    }
    $scope.barcodeTest=12323232;
    $scope.isSet = function(tabNum){
        return $scope.tab === tabNum;
        if(newTab==1){
            $scope.isUpdateableOil=false;
        }
    };

    $scope.sort = {
        active: '',
        descending: undefined
    };

    $scope.changeDateFormat=function(userDate){
        return moment(userDate).format('YYYY-MM-DD');
    };

    $scope.selectedTab = {
        "color" : "white",
        "background-color" : "coral",
        "font-size" : "15px",
        "padding" : "5px"
    };

    $scope.saleUpdated=false;
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



    $scope.showNotification=false;
    $scope.btnSubmitDisable=false;
    $scope.defaultSaleDetails={};
    $scope.oilCakeMaster={};

    $scope.setGstFactor=function(){
        if($scope.oilMaster!=undefined){
            if($scope.oilMaster.customer!=undefined && $scope.oilMaster.customer.state_id!=19){
                $scope.oilMaster.cgstFactor=0;
                $scope.oilMaster.sgstFactor=0;
                $scope.oilMaster.igstFactor=1;
            }else{
                $scope.oilMaster.cgstFactor=0.5;
                $scope.oilMaster.sgstFactor=0.5;
                $scope.oilMaster.igstFactor=0;
            }
        }
        //For sale oil cake
        if($scope.oilCakeMaster!=undefined){
            if($scope.oilCakeMaster.customer!=undefined && $scope.oilCakeMaster.customer.state_id!=19){
                $scope.oilCakeMaster.cgstFactor=0;
                $scope.oilCakeMaster.sgstFactor=0;
                $scope.oilCakeMaster.igstFactor=1;
            }else{
                $scope.oilCakeMaster.cgstFactor=0.5;
                $scope.oilCakeMaster.sgstFactor=0.5;
                $scope.oilCakeMaster.igstFactor=0;
            }
        }

    };

    
    
    $scope.customerList={};
    $scope.loadAllCustomers=function(){
        var request = $http({
            method: "post",
            url: site_url+"/Customer/get_customers",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.customerList=response.data.records;
            $scope.customerListByKey=alasql("select * from ?",[$scope.customerList]);
            $scope.oilMaster.customer=$scope.customerListByKey[0];
            $scope.oilCakeMaster.customer=$scope.customerList[0];
            $scope.setGstFactor();
        });
    };//end of loadcustomers
    $scope.loadAllCustomers();


    $scope.searchCustomerByKey=function () {
            $scope.customerListByKey=alasql("select * from ? where customer_name like '"+$scope.oilMaster.customerSearchKey+"%'",[$scope.customerList]);
            $scope.oilMaster.customer=$scope.customerListByKey[0];
    };


    $scope.defaultOilMaster={
        product_id: 3,
        cgstFactor: 0,
        sgstFactor: 0,
        igstFactor: 0,
        memo_number: "",
        sale_date: ""
    };
    $scope.defaultOilCakeMaster={
        product_id: 2,
        cgstFactor: 0,
        sgstFactor: 0,
        igstFactor: 0,
        memo_number: "",
        sale_date: ""
    };

    $scope.oilCakeMaster=angular.copy($scope.defaultOilCakeMaster);
    $scope.oilCakeDetails=angular.copy($scope.defaultSaleDetails);
   
    $scope.oilMaster=angular.copy($scope.defaultOilMaster);
    $scope.oilDetails=angular.copy($scope.defaultSaleDetails);
  


      // $scope.purcaseData.sale_date = new Date();
    $scope.totalsaleAmount=0;

    $scope.getNoOfOilTinForSale=function(itemId){
        var request = $http({
            method: "post",
            url: site_url+"/Sale/get_oil_tin",
            data: {
                oil_tin_id : itemId
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.counOilTin=response.data;
            
        });
    };

    

    $scope.productList={};
    $scope.loadAllProducts=function(){
        var request = $http({
            method: "post",
            url: site_url+"/product/get_inforce_products",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.productList=response.data.records;
            $scope.oilDetails.mustardOil=alasql('select * from ? where product_id =3',[$scope.productList])[0];
            $scope.getNoOfOilTinForSale($scope.oilDetails.mustardOil.product_id);
           	$scope.setGstFactor();
            $scope.oilDetails.mustardOil.discount=0;

            $scope.oilDetails.mustardOil.sgst_rate=($scope.oilDetails.mustardOil.gst_rate*$scope.oilMaster.sgstFactor)/100;
        	$scope.oilDetails.mustardOil.cgst_rate=($scope.oilDetails.mustardOil.gst_rate*$scope.oilMaster.cgstFactor)/100;
        	$scope.oilDetails.mustardOil.igst_rate=($scope.oilDetails.mustardOil.gst_rate*$scope.oilMaster.igstFactor)/100;

            $scope.oilCakeDetails.oilCake=alasql('select * from ? where product_id =2',[$scope.productList])[0];
            $scope.oilCakeDetails.oilCake.discount=0;

            $scope.oilCakeDetails.oilCake.sgst_rate=($scope.oilCakeDetails.oilCake.gst_rate*$scope.oilCakeMaster.sgstFactor)/100;
            $scope.oilCakeDetails.oilCake.cgst_rate=($scope.oilCakeDetails.oilCake.gst_rate*$scope.oilCakeMaster.cgstFactor)/100;
            $scope.oilCakeDetails.oilCake.igst_rate=($scope.oilCakeDetails.oilCake.gst_rate*$scope.oilCakeMaster.igstFactor)/100;

            // alert($scope.oilCakeDetails.oilCake.sgst_rate);


        });
    };//end of loadcustomers
    $scope.loadAllProducts();

    $scope.setMustardOilGst=function(){
        var amt=$scope.setAmount();
        $scope.oilDetails.mustardOil.sgst=$rootScope.roundNumber((amt*$scope.oilDetails.mustardOil.sgst_rate),2);
        $scope.oilDetails.mustardOil.cgst=$rootScope.roundNumber((amt*$scope.oilDetails.mustardOil.cgst_rate),2);
        $scope.oilDetails.mustardOil.igst=$rootScope.roundNumber((amt*$scope.oilDetails.mustardOil.igst_rate),2);
    };

    $scope.setOilCakeGst=function(){
        var amt=$scope.setAmountForOilCake();
        $scope.oilCakeDetails.oilCake.sgst=$rootScope.roundNumber((amt*$scope.oilCakeDetails.oilCake.sgst_rate),2);
        $scope.oilCakeDetails.oilCake.cgst=$rootScope.roundNumber((amt*$scope.oilCakeDetails.oilCake.cgst_rate),2);
        $scope.oilCakeDetails.oilCake.igst=$rootScope.roundNumber((amt*$scope.oilCakeDetails.oilCake.igst_rate),2);

    };



    
    $scope.tinToQuintalConversion=function(){
        if($scope.oilDetails.mustardOil!=undefined){
            var noOfTins= $scope.oilDetails.mustardOil.quantity;
        }else{
            var noOfTins= 0;
        }
		var TinToKg=noOfTins*15;
		$scope.oilDetails.tin_to_quintal=$rootScope.roundNumber((TinToKg * 0.01),2);
		return $scope.oilDetails.tin_to_quintal;
	};

    $scope.packetToQuintalConversion=function(){
        if($scope.oilCakeDetails.oilCake!=undefined){
            var noOfPackets=$scope.oilCakeDetails.oilCake.quantity;
        }else{
            var noOfPackets= 0;
        }
        var packetToKg=noOfPackets*50;
        $scope.oilCakeDetails.packet_to_quintal=$rootScope.roundNumber((packetToKg * 0.01),2);
        return $scope.oilCakeDetails.packet_to_quintal;
    };


    $scope.removeRow = function(index){
        $scope.saleForm.$setDirty();
        // remove the row specified in index
        $scope.saleDetailsDataList.splice( index, 1);
        // if no rows left in the array create a blank array
        if ($scope.saleDetailsDataList.length() === 0){
            $scope.saleDetailsDataList = [];
        }
    };

    //remove row for oil cake
    $scope.removeRow2 = function(index){
        $scope.saleFormOilCake.$setDirty();
        $scope.oilCakeDataList.splice( index, 1);
        // if no rows left in the array create a blank array
        if ($scope.oilCakeDataList.length() === 0){
            $scope.oilCakeDataList = [];
        }
    };



    $scope.getDiscount=function(){
        var saleValue=$rootScope.roundNumber(($scope.oilDetails.mustardOil.quantity)*($scope.oilDetails.mustardOil.rate),2);
        var disc_rate=$scope.oilDetails.mustardOil.discount;
        var amt_disc=$rootScope.roundNumber(saleValue*(disc_rate/100),2);
        return amt_disc;
    };

    $scope.setAmount=function(){
        var discount=$scope.getDiscount();
        var amt=($scope.oilDetails.mustardOil.quantity)*($scope.oilDetails.mustardOil.rate);
         $scope.oilDetails.mustardOil.amount=$rootScope.roundNumber((amt-discount),2);
         return $scope.oilDetails.mustardOil.amount;
    };

    $scope.setAmountForOilCake=function(){
        var amt=($scope.oilCakeDetails.packet_to_quintal)*($scope.oilCakeDetails.oilCake.rate);
        $scope.oilCakeDetails.oilCake.amount=$rootScope.roundNumber(amt,2);
        return ($scope.oilCakeDetails.oilCake.amount);
    };

    $scope.currentIndex=-1;

    $scope.saleDetailsDataList=[];
    $scope.addOilDetailsData=function(sale){
        $scope.showNotification=false;
        var test=0;
        angular.forEach($scope.saleDetailsDataList, function(value, key) {

            if(angular.equals(value,sale))
                test++;

        });
        if(test==0){
            var test=angular.copy(sale);
            var total=0;
            $scope.saleDetailsDataList.unshift(test);
        }else{
            $scope.showNotification=true;

        }

    };

    //add oil cake for sale
    $scope.oilCakeDataList=[];
    $scope.addOilCakeDetailsData=function(sale){
        $scope.showNotification2=false;
        var test=0;
        angular.forEach($scope.oilCakeDataList, function(value, key) {

            if(angular.equals(value,sale))
                test++;

        });
        if(test==0){
            var test=angular.copy(sale);
            var total=0;
            $scope.oilCakeDataList.unshift(test);
        }else{
            $scope.showNotification2=true;

        }

    };




    $scope.oilDetailToSave=[];
    $scope.$watchCollection("saleDetailsDataList",function (newValue, oldValue) {
        $scope.setGstFactor();
        if($scope.oilDetails.mustardOil !=undefined){
            $scope.setMustardOilGst();
        }
        if(newValue!=oldValue){
            $scope.oilFooter=alasql('SELECT sum(mustardOil->sgst)as totalSgst' +
                ',sum(mustardOil->cgst) as totalCgst' +
                ',sum(mustardOil->igst) as totalIgst' +
                ',sum(mustardOil->amount)+sum(mustardOil->sgst)+sum(mustardOil->cgst)+sum(mustardOil->igst) as totalsaleAmount  from ? ',[newValue]);
             var totalsale=$scope.oilFooter[0].totalsaleAmount;
            var roundDecimal=$rootScope.roundNumber(totalsale-parseInt(totalsale),2);
            if(roundDecimal==0){
                $scope.oilMaster.roundedOff=0;
            }else if(roundDecimal>0.49) {
                    $scope.oilMaster.roundedOff = $rootScope.roundNumber(1-roundDecimal,2);
            }else{
                    $scope.oilMaster.roundedOff = $rootScope.roundNumber(0-roundDecimal,2);
            }
            $scope.oilMaster.grand_total=totalsale + $scope.oilMaster.roundedOff;//get bill_amount using roundoff

            $scope.oilDetailToSave=alasql('SELECT mustardOil->product_id as product_id' +
                ',mustardOil->quantity as quantity' +
                ',mustardOil->rate as rate' +
                ',mustardOil->sgst_rate as sgst_rate' +
                ',mustardOil->cgst_rate as cgst_rate' +
                ',mustardOil->igst_rate as igst_rate' +
                ',mustardOil->sgst as sgst' +
                ',mustardOil->cgst as cgst' +
                ',mustardOil->igst as igst' +
                ',tin_to_quintal' +
                ' from ? ',[newValue]);
           


        }
    });

    $scope.oilCakeDetailToSave=[];
    $scope.$watchCollection("oilCakeDataList",function (newValue, oldValue) {
        // $scope.setGstFactor();
        if($scope.oilCakeDetails.oilCake!=undefined){
            $scope.setOilCakeGst();
        }
        if(newValue!=oldValue){
            // $scope.oilCakeFooter=alasql('SELECT (oilCake->rate) as val from ? ',[newValue]);

            $scope.oilCakeFooter=alasql('SELECT ' +
                'sum(oilCake->sgst)as totalSgst' +
                ',sum(oilCake->cgst) as totalCgst' +
                ',sum(oilCake->igst) as totalIgst' +
                ',sum(oilCake->rate * quintal) as totalValue' +
                ',sum(oilCake->amount)+sum(oilCake->sgst)+sum(oilCake->cgst)+sum(oilCake->igst) as totalsaleAmount  from ? ',[newValue]);
            var totalsale=$scope.oilCakeFooter[0].totalsaleAmount;
            var roundDecimal=$rootScope.roundNumber(totalsale-parseInt(totalsale),2);
            if(roundDecimal==0){
                $scope.oilCakeMaster.roundedOff=0;
            }else if(roundDecimal>0.49) {
                $scope.oilCakeMaster.roundedOff = $rootScope.roundNumber(1-roundDecimal,2);
            }else{
                $scope.oilCakeMaster.roundedOff = $rootScope.roundNumber(0-roundDecimal,2);
            }
            $scope.oilCakeMaster.grand_total=totalsale + $scope.oilCakeMaster.roundedOff;//get bill_amount using roundoff

            $scope.oilCakeDetailToSave=alasql('SELECT oilCake->product_id as product_id' +
                ',oilCake->quantity as quantity' +
                ',oilCake->rate as rate' +
                ',oilCake->sgst_rate as sgst_rate' +
                ',oilCake->cgst_rate as cgst_rate' +
                ',oilCake->igst_rate as igst_rate' +
                ',oilCake->sgst as sgst' +
                ',oilCake->cgst as cgst' +
                ',oilCake->igst as igst' +
                ',packet_to_quintal' +
                ' from ? ',[newValue]);
            // console.log($scope.oilDetailToSave);


        }
    });



    $scope.saveSaleDetails=function(oilMaster,oilDetailToSave){
        var sm={};
        var sdl=[];
        sm.customer_id=oilMaster.customer.person_id;
        sm.sale_date=$scope.changeDateFormat(oilMaster.sale_date);
        sm.roundedOff=oilMaster.roundedOff;
        sm.grand_total=oilMaster.grand_total;
        sdl=angular.copy(oilDetailToSave);
        var request = $http({
            method: "post",
            url: site_url+"/sale/save_new_sale",
            data: {
                sale_master: sm,
                sale_details_list: sdl
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.saleReportArray=response.data.records;
            if($scope.saleReportArray.success==1){
                $scope.updateableSaleIndex=0;
                $scope.isUpdateableOil=true;
                $scope.showBillNo=true;
                $scope.showoilCakeBillNo=true;
                $scope.saleSubmitStatus = true;
                $timeout(function() {
                    $scope.saleSubmitStatus = false;
                }, 5000);

                $scope.oilMaster.memo_number=$scope.saleReportArray.memo_number;
                $scope.oilMaster.sale_master_id=$scope.saleReportArray.sale_master_id;

                var tempSaleDetail={};
                tempSaleDetail.sale_master_id=$scope.oilMaster.sale_master_id;
                tempSaleDetail.memo_number=$scope.oilMaster.memo_number;
                tempSaleDetail.customer_id=$scope.oilMaster.customer.person_id;
                tempSaleDetail.person_name=$scope.oilMaster.customer.customer_name;
                tempSaleDetail.mobile_no=$scope.oilMaster.customer.mobile_no;
                tempSaleDetail.grand_total=$scope.oilMaster.grand_total;
                tempSaleDetail.bill_type_name='Mustered Oil';
                tempSaleDetail.bill_type=1;
                tempSaleDetail.product_id=3;

               var dd =$scope.changeDateFormat($scope.oilMaster.sale_date);
                tempSaleDetail.display_sale_date= $filter('date')(dd, "dd/MM/yyyy");
                tempSaleDetail.sale_date= $scope.changeDateFormat($scope.oilMaster.sale_date);

                tempSaleDetail.sale_month='this';
                $scope.allSaleList.unshift(tempSaleDetail);
            }
        });
    };
    $scope.currentDate=new Date();
    $scope.dd = new Date().getDate();
    $scope.mm = new Date().getMonth()+1;
    $scope.yy = new Date().getFullYear();
    $scope.day= ($scope.dd<10)? '0'+$scope.dd : $scope.dd;
    $scope.month= ($scope.mm<10)? '0'+$scope.mm : $scope.mm;
    // $scope.oilMaster.sale_date=($scope.day+"/"+$scope.month+"/"+$scope.yy);
    // $scope.oilCakeMaster.sale_date=($scope.day+"/"+$scope.month+"/"+$scope.yy);

    $scope.oilMaster.sale_date=$scope.currentDate;
    $scope.oilCakeMaster.sale_date=$scope.currentDate;

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

    $scope.loadAllSale=function(){
        var request = $http({
            method: "post",
            url: site_url+"/sale/get_all_sale",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.allSaleList=response.data.records;
        });
    };
    //loading sale bills
    
//working
    $scope.options = [{ name: "a", id: 1 }, { name: "b", id: 2 }, { name: "c", id: 3 }];
    $scope.selectedOption = $scope.options[1];




    //new
    $scope.prepareOilBillData=function(sale){
        var index = $scope.allSaleList.findIndex(x => x.sale_master_id === sale.sale_master_id);
        var saleMasterId = sale.sale_master_id;
        $scope.tab=3;
        $scope.showMustardOilBillByBillId(saleMasterId,sale.product_id);
    };

    $scope.getSaleFromTableForUpdate=function(sale){
        // console.log(sale);
        $scope.updateableSaleIndex = index;
        var index = $scope.allSaleList.findIndex(x => x.sale_master_id === sale.sale_master_id);
        var saleMasterId = sale.sale_master_id;
        var custIndex = $scope.customerListByKey.findIndex(x => x.person_id === sale.customer_id);
        
        //checking bill type
        if(sale.bill_type==1) {
            $scope.saleDetailsDataList = [];
            $scope.tab = 1;
            $scope.saleForm.$setPristine();
            $scope.isUpdateableOil = true;
            $scope.oilMaster.sale_date = sale.sale_date;
            $scope.oilMaster.memo_number = sale.memo_number;
            $scope.oilMaster.sale_master_id = sale.sale_master_id;
            $scope.oilMaster.customer = $scope.customerListByKey[custIndex];
            $scope.setGstFactor();

            var request = $http({
                method: "post",
                url: site_url + "/sale/get_sale_details_for_edit_sale",
                data: {
                    sale_master_id: saleMasterId
                }
                , headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function (response) {
                var tempList = response.data.records;
                $scope.showBillNo = true;
                // $scope.tab = 1;
                $scope.loadAllProducts();
                var mustardOil = angular.copy($scope.oilDetails.mustardOil);
                var saleList = {};
                angular.forEach(tempList, function (value, key) {
                    mustardOil.quantity = value.no_of_container;
                    mustardOil.rate = value.rate;
                    mustardOil.amount = value.amount;
                    mustardOil.sgst = value.sgst;
                    mustardOil.cgst = value.cgst;
                    mustardOil.igst = value.igst;
                    saleList.mustardOil = angular.copy(mustardOil);
                    saleList.tin_to_quintal = value.quantity;

                    value.saleList = angular.copy(saleList);
                    $scope.saleDetailsDataList.push(value.saleList);
                });
            });
        }else{
            //when oil cake is selected
            $scope.tab = 4;
            $scope.oilCakeDataList=[];
            $scope.saleFormOilCake.$setPristine();
            $scope.isUpdateableOilCake = true;
            $scope.oilCakeMaster.sale_date = sale.sale_date;
            $scope.oilCakeMaster.memo_number = sale.memo_number;
            $scope.oilCakeMaster.sale_master_id = sale.sale_master_id;
            $scope.oilCakeMaster.customer = $scope.customerList[custIndex];
            $scope.setGstFactor();

            var request = $http({
                method: "post",
                url: site_url + "/sale/get_sale_details_for_edit_sale",
                data: {
                    sale_master_id: saleMasterId
                }
                , headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function (response) {
                var tempList = response.data.records;
                $scope.showOilCakeBillNo = true;
                // $scope.tab = 1;
                $scope.loadAllProducts();
                var oilCake = angular.copy($scope.oilCakeDetails.oilCake);
                var saleList = {};
                var packet_to_quintal="";
                angular.forEach(tempList, function (value, key) {
                    oilCake.quantity = value.quantity;
                    oilCake.rate = value.rate;
                    oilCake.amount = value.amount;
                    oilCake.sgst = value.sgst;
                    oilCake.cgst = value.cgst;
                    oilCake.igst = value.igst;
                    saleList.oilCake = angular.copy(oilCake);
                    saleList.oilCake = angular.copy(oilCake);

                    value.saleList = angular.copy(saleList);
                    value.saleList.packet_to_quintal=value.packet_to_quintal;
                    $scope.oilCakeDataList.push(value.saleList);
                });
            });

        }
    };

    $scope.updateSaleDetails=function(oilMaster,oilDetailToSave){        
        var sm={};
        var sdl=[];
        sm.customer_id=oilMaster.customer.person_id;
        sm.sale_master_id=oilMaster.sale_master_id;
        sm.memo_number=oilMaster.memo_number;

        sm.sale_date= $scope.changeDateFormat(oilMaster.sale_date);
        sm.roundedOff=oilMaster.roundedOff;
        sm.grand_total=oilMaster.grand_total;
        sdl=angular.copy(oilDetailToSave);
        var request = $http({
            method: "post",
            url: site_url+"/sale/update_saved_sale",
            data: {
                sale_master: sm,
                sale_details_list: sdl
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.saleReportArray=response.data.records;
            $scope.saleUpdated=true;
            if($scope.saleReportArray.success==1){
                $scope.isUpdateableOil=true;
                $scope.updateStatus = true;
                $timeout(function() {
                    $scope.updateStatus = false;
                }, 5000);
                //$scope.btnSubmitDisable=true;
                $scope.oilMaster.sale_master_id=$scope.saleReportArray.sale_master_id;
                
                // var tempsaleDetail={};
                // tempsaleDetail.sale_master_id=$scope.oilMaster.sale_master_id;
                // tempsaleDetail.customer_name=$scope.oilMaster.customer.person_name;
                // tempsaleDetail.mobile_no=$scope.oilMaster.customer.mobile_no;
                // tempsaleDetail.sale_date=$scope.oilMaster.sale_date;
                // tempsaleDetail.total_sale_amount=$scope.oilMaster.grand_total;
                // $scope.allSaleList.unshift(tempsaleDetail);
            }
        });
    };
    $scope.resetSaleDetails=function () {
        $scope.isUpdateableOil=false;
        $scope.oilMaster.memo_number="";
        $scope.oilMaster.roundedOff="";
        $scope.oilMaster.grand_total="";
        $scope.oilMaster.customer=$scope.customerList[0];
        $scope.loadAllProducts();
        $scope.saleDetailsDataList=[];
        $scope.showBillNo=false;
        $scope.saleSubmitStatus=false;
    };

    $scope.resetSaleOilCake=function () {
        $scope.isUpdateableOilCake=false;
        $scope.oilCakeMaster.memo_number="";
        $scope.oilCakeMaster.roundedOff="";
        $scope.oilCakeMaster.grand_total="";
        $scope.oilCakeMaster.customer=$scope.customerList[0];
        $scope.loadAllProducts();
        $scope.oilCakeDataList = [];
        $scope.oilCakeDetails=angular.copy($scope.defaultSaleDetails);
        $scope.showBillNo=false;
        $scope.saleSubmitStatus=false;
    };


    $scope.showMustardOilBillByBillId=function (saleMasterId,productId) {
        var request = $http({
            method: "post",
            url: site_url+"/sale/get_mustard_oil_bill_details_by_id",
            data: {
                sale_master_id: saleMasterId
                ,product_id: productId
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.mustardOilBillDetails=response.data.records;
        });

    };

    $scope.$watch("oilMaster", function(newValue, oldValue){
        if(newValue != oldValue){
            $scope.showMustardOilBillByBillId(newValue.sale_master_id,newValue.product_id);
        }
    });

    $scope.$watch("oilCakeMaster", function(newValue, oldValue){
        if(newValue != oldValue){
            $scope.showMustardOilBillByBillId(newValue.sale_master_id,newValue.product_id);
        }
    });


    $scope.showSaleMustardOilBill=function (oilMaster) {
        $scope.tab=3;
        $scope.showMustardOilBillByBillId(oilMaster.sale_master_id,oilMaster.product_id);

    }


    $scope.$watch("mustardOilBillDetails", function(newValue, oldValue){

        if(newValue != oldValue){
            var result=alasql('SELECT sum(gross_value) as totalGrossValue,sum(total_tax) as grandTotalTax,sum(total_amount) as grandTotalAmount  from ? ',[newValue]);
            $scope.mOilShowTableFooter=result[0];
            $scope.mOilShowTableFooter.finalBillTotal=$scope.mOilShowTableFooter.grandTotalAmount+$scope.oilMaster.roundedOff;
            var tempGstTable=alasql('SELECT hsn_code,gst_rate,max(cgst) as cgst_rate, max(sgst) as sgst_rate, max(igst) as igst_rate,sum(sgst_value) as sum_of_sgst,sum(cgst_value) as sum_of_cgst,sum(igst_value)as sum_of_igst,sum(taxable_amount) as sum_of_taxable_amount from ? group by hsn_code,gst_rate',[newValue]);
            $scope.gstTable=tempGstTable;
            var temp=alasql('SELECT SUM(sum_of_sgst) AS total_sgst,SUM(sum_of_cgst) AS total_cgst,SUM(sum_of_igst) AS total_igst from ?',[tempGstTable]);
      
            $scope.gstTableFooter=temp[0];
          
        }
    });

    $scope.saveOilCakeDetails=function(oilCakeMaster,oilCakeDetailToSave){
        var sm={};
        var sdl=[];
        sm.customer_id=oilCakeMaster.customer.person_id;
        sm.sale_date=oilCakeMaster.sale_date;
        sm.roundedOff=oilCakeMaster.roundedOff;
        sm.grand_total=oilCakeMaster.grand_total;
        sdl=angular.copy(oilCakeDetailToSave);
        var request = $http({
            method: "post",
            url: site_url+"/sale/save_new_oil_cake_sale",
            data: {
                sale_master: sm,
                sale_details_list: sdl
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.oilCakeReportArray=response.data.records;
            if($scope.oilCakeReportArray.success==1){
                $scope.updateableOilCakeIndex=0;
                $scope.isUpdateableOilCake=true;
                $scope.showOilCakeBillNo=true;
                // $scope.showBillNo=true;
                $scope.isSaveOilCake=true;
                $timeout(function() {
                    $scope.isSaveOilCake=false;
                }, 5000);

                $scope.oilCakeMaster.memo_number=$scope.oilCakeReportArray.memo_number;
                $scope.oilCakeMaster.sale_master_id=$scope.oilCakeReportArray.sale_master_id;

                var tempSaleDetail={};
                tempSaleDetail.sale_master_id=$scope.oilCakeMaster.sale_master_id;
                tempSaleDetail.memo_number=$scope.oilCakeMaster.memo_number;
                tempSaleDetail.customer_id=$scope.oilCakeMaster.customer.person_id;
                tempSaleDetail.person_name=$scope.oilCakeMaster.customer.customer_name;
                tempSaleDetail.mobile_no=$scope.oilCakeMaster.customer.mobile_no;
                tempSaleDetail.grand_total=$scope.oilCakeMaster.grand_total;
                tempSaleDetail.bill_type=2;
                tempSaleDetail.product_id=2;
                tempSaleDetail.bill_type_name='Oil Cake';


                var dd =$scope.changeDateFormat($scope.oilCakeMaster.sale_date);
                tempSaleDetail.display_sale_date= $filter('date')(dd, "dd/MM/yyyy");
                tempSaleDetail.sale_date=$scope.oilCakeMaster.sale_date;
                tempSaleDetail.sale_date=$scope.changeDateFormat($scope.oilCakeMaster.sale_date);

                tempSaleDetail.sale_month='this';
                $scope.allSaleList.unshift(tempSaleDetail);
            }
        });
    };


    $scope.updateOilCakeDetails=function(oilCakeMaster,oilCakeDetailToSave){
        var sm={};
        var sdl=[];
        sm.customer_id=oilCakeMaster.customer.person_id;
        sm.sale_master_id=oilCakeMaster.sale_master_id;
        sm.memo_number=oilCakeMaster.memo_number;
        sm.sale_date=oilCakeMaster.sale_date;
        sm.roundedOff=oilCakeMaster.roundedOff;
        sm.grand_total=oilCakeMaster.grand_total;
        sdl=angular.copy(oilCakeDetailToSave);
        var request = $http({
            method: "post",
            url: site_url+"/sale/update_oil_cake_details",
            data: {
                oilCakeMaster: sm,
                oilCakeDetailsList: sdl
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.oilCakeReportArray=response.data.records;
            if($scope.oilCakeReportArray.success==1){
                $scope.isUpdateableOilCake=true;
                $scope.updateOilCakeStatus=true;
                $timeout(function() {
                    $scope.updateOilCakeStatus = false;
                }, 5000);
                //$scope.btnSubmitDisable=true;
                $scope.oilCakeMaster.sale_master_id=$scope.oilCakeReportArray.sale_master_id;
                // var tempsaleDetail={};
                // tempsaleDetail.sale_master_id=$scope.oilMaster.sale_master_id;
                // tempsaleDetail.customer_name=$scope.oilMaster.customer.person_name;
                // tempsaleDetail.mobile_no=$scope.oilMaster.customer.mobile_no;
                // tempsaleDetail.sale_date=$scope.oilMaster.sale_date;
                // tempsaleDetail.total_sale_amount=$scope.oilMaster.grand_total;
                // $scope.allSaleList.unshift(tempsaleDetail);
            }
        });
    };

    $scope.noRecordsMoil=true;
    $scope.noRecordsOilcake=true;
    $scope.excelDataForMustardOil=[];
    $scope.getMoilDataToSendInExcel=function(startDate,endDate){
        startDate= $scope.changeDateFormat(startDate);
        endDate= $scope.changeDateFormat(endDate);

        var request = $http({
            method: "post",
            url: site_url+"/SaleReport/get_excel_data_for_mustard_oil",
            data: {
                start_date:startDate,
                end_date:endDate
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.moilExceldata=response.data.records;
            if($scope.moilExceldata.length==0){
                $scope.noRecordsMoil=true;
            }else{
                $scope.noRecordsMoil=false;
            }
        });
    };

    $scope.oilCakeExceldata=[];
    $scope.getOilCakeDataToSendInExcel=function(startDate,endDate){
        startDate= $scope.changeDateFormat(startDate);
        endDate= $scope.changeDateFormat(endDate);

        var request = $http({
            method: "post",
            url: site_url+"/SaleReport/get_excel_data_for_oil_cake",
            data: {
                start_date:startDate,
                end_date:endDate
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.oilCakeExceldata=response.data.records;
            if($scope.moilExceldata.length==0){
                $scope.noRecordsOilcake=true;
            }else{
                $scope.noRecordsOilcake=false;
            }
        });
    };
    


    //save to excel
        $scope.saveToExcel=function (fileName,data) {
            // alert($scope.customerList);
        //$scope.testVendorList=alasql('select SUM(CONVERT(number,state_id)) as tot from ?',[$scope.vendorList]);
        //alasql('SELECT * INTO CSV("Myfile.csv",{headers:true}) FROM ?', [$scope.vendorList]);
        //alasql('SELECT * INTO XLSX("Myfile.xlsx",{headers:true}) FROM ?', [$scope.vendorList]);
        var mystyle = {
            headers:true,
            column: {style:{Font:{Bold:"1"}}},
            autoExt:false,
            cells: {1:{1:{
                        style: {Font:{Color:"#00FFFF"}}
                    }}},
             rows: {3:{style:{Font:{Color:"#FF0077"}}}},
             cells: {1:{1:{
                         style: {Font:{Color:"#00FFFF"}}
                     }}}
        };

        //alasql('SELECT * INTO XLSXML(?,?) FROM ?',[fileName,mystyle,data]);

        alasql.promise('SELECT * INTO XLSXML(?,?) FROM ?',[fileName,mystyle,data])
                .then(function(data){
                    //alert(data);
                }).catch(function(err){
              //  console.log('Error:', err);
        });




    };
        $scope.saveToCSV=function (fileName,data) {
        //$scope.testVendorList=alasql('select SUM(CONVERT(number,state_id)) as tot from ?',[$scope.vendorList]);
        //alasql('SELECT * INTO CSV("Myfile.csv",{headers:true}) FROM ?', [$scope.vendorList]);
        //alasql('SELECT * INTO XLSX("Myfile.xlsx",{headers:true}) FROM ?', [$scope.vendorList]);
        var mystyle = {
            headers:true,
            column: {style:{Font:{Bold:"1"}}}
        };

        alasql('SELECT * INTO CSV(?,?) FROM ? order by person_name',[fileName,mystyle,data]);
    };
    //end of save to excel




    //chart data
    http://jtblin.github.io/angular-chart.js/#reactive
    $scope.doughnutLabels = ["Download Sales", "In-Store Sales", "Mail-Order Sales"];
    $scope.doughnutData = [300, 800, 100];


    $scope.labels = ["Download Sales", "In-Store Sales", "Mail-Order Sales", "Tele Sales", "Corporate Sales"];
    $scope.data = [300, 500, 100, 40, 120];
    $scope.type = 'polarArea';

    $scope.toggle = function () {
        $scope.type = $scope.type === 'polarArea' ?
            'pie' : 'polarArea';
    };


    //bar chart data






    //end of chart data


    $scope.getSaleByMonth=function(){
        var request = $http({
            method: "post",
            url: site_url+"/sale/get_sale_by_month",
            data: {

            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.testData=response.data;

            $scope.chartLabels=$scope.testData.map(function(el) {
                return el.sale_month;
            });
          
            $scope.chartData=$scope.testData.map(function(el) {
                return el.month_wise_sale;
            });
          
        });
    };
    // $scope.getSaleByMonth();
    $scope.barChartSeries = ['Series A'];


});

