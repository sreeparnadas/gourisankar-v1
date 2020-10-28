app.controller("productCtrl", function ($scope,$http,$timeout) {
    $scope.msg = "This is Product controller";
    $scope.productSubmit = false;
    $scope.updateStatus = false;
    $scope.isUpdateable=false;
    $scope.tab = 1;
    $scope.changeDateFormat=function(userDate){
        return moment(userDate).format('YYYY-MM-DD');
    };
    
    $scope.setTab = function(newTab){
        $scope.tab = newTab;
    };

    $scope.isSet = function(tabNum){
        return $scope.tab === tabNum;
    };

    var request = $http({
        method: "post",
        url: site_url+"/product/get_all_units",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
         $scope.unitsList=response.data.records;
         $scope.product.product_unit=$scope.unitsList[0];
    });

    var request = $http({
        method: "post",
        url: site_url+"/product/get_all_hsn_codes",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        $scope.hsnCodesList=response.data.records;
    });



    $scope.defaultProduct={
        product_name:''
        ,hsn_code: ''
        ,gst_rate: ''
        ,opening_balance: ''
        ,opening_date: new Date
    };

    $scope.product=angular.copy($scope.defaultProduct);


    $scope.saveProduct=function (product) {
        var hsnSerialNo=product.hsn_code.hsn_serial_no;
        var productName=product.product_name;
        var openingBalance=product.opening_balance;
        var openingDate=$scope.changeDateFormat(product.opening_date);
        var unitId=product.product_unit.unit_id;
        var request = $http({
            method: "post",
            url: site_url+"/product/save_new_product",
            data: {
                hsnSerialNo: hsnSerialNo
                ,productName:productName
                ,openingBalance:openingBalance
                ,openingDate:openingDate
                ,unitId: unitId
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.reportArrayProduct=response.data.records;
            $scope.productSubmit=true;
            $scope.isUpdateable=true;
            $timeout(function() {
                $scope.productSubmit = false;
            }, 5000);
            $scope.productForm.$setPristine();
        });
    };


    $scope.loadAllProduct=function(){
        var request = $http({
            method: "post",
            url: site_url+"/product/get_all_product",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.allProductList=response.data.records;
            var unitIndex=$scope.unitsList.findIndex(record=>record.unit_id===$scope.allProductList.default_unit_id);
            $scope.allProductList.product_unit=$scope.unitsList[unitIndex];
        });
    };
    //loading sale bills
    $scope.loadAllProduct();


    $scope.getProductForUpdateFromTable = function(product) {
        $scope.product = angular.copy(product);
        var index=$scope.allProductList.indexOf(product);
        $scope.updateableProductIndex=index;
        $scope.tab=1;
        $scope.isUpdateable=true;
        var unitIndex=$scope.unitsList.findIndex(x=>x.unit_id===product.default_unit_id);
        $scope.product.product_unit=$scope.unitsList[unitIndex];
        var hsnIndex=$scope.hsnCodesList.findIndex(x=>x.hsn_serial_no===product.hsn_serial_no);
        $scope.product.hsn_code=$scope.hsnCodesList[hsnIndex];
        $scope.productForm.$setPristine();
    };

    $scope.updateProductByProductId=function(product){
        var pr={};
        pr.product_id=product.product_id;
         pr.hsn_serial_no=product.hsn_code.hsn_serial_no;
         pr.product_name=product.product_name;
         pr.opening_balance=product.opening_balance;
         pr.opening_date=product.opening_date;
         pr.unit_id=product.product_unit.unit_id;
        var request = $http({
            method: "post",
            url: site_url+"/product/update_product_by_product_id",
            data: {
               pr: pr
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.updateReportArray=response.data.records;
            if($scope.updateReportArray.success==1){
                $scope.updateStatus = true;
                $scope.isUpdateable=false;
                $timeout(function() {
                    $scope.updateStatus = false;
                }, 5000);
               var tempProduct=angular.copy($scope.product);
               tempProduct.gst_rate=tempProduct.hsn_code.gst_rate;
               tempProduct.hsn_code=tempProduct.hsn_code.hsn_code;
               tempProduct.unit_name=tempProduct.product_unit.unit_name;
                $scope.allProductList[$scope.updateableProductIndex]=tempProduct;
                $scope.productForm.$setPristine();
            }

        });
    };


    $scope.resetProduct=function(){
        $scope.product=angular.copy($scope.defaultProduct);
        $scope.isUpdateable=false;
    };

});

