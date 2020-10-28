app.controller("customerCtrl", function ($scope,$http,uploadService,$timeout) {
    $scope.msg = "This is customer controller";
    
    $scope.successMsg=false;
    $scope.alertmessage="";


    $scope.sort = {
        active: '',
        descending: undefined
    };

    $scope.isUpdateable=false;
    $scope.defaultCustomer={
        "person_name": "",
        "mobile_no": "",
        "email": "",
        "aadhar_no": "",
        "pan_no": "",
        "address": "",
        "city": "",
        "district_id": "0",
        "post_office": "",
        "pin": "",
        "gst_number": "",
        "state_id": "19"
    };
    $scope.customer=angular.copy($scope.defaultCustomer);
    var request = $http({
        method: "post",
        url: site_url+"/Customer/get_states",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        $scope.states=response.data.records;
        $scope.selectStates($scope.defaultCustomer.state_id);
    });

    var request = $http({
        method: "post",
        url: site_url+"/customer/get_customers",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        $scope.customerList=response.data.records;
    });


    $scope.tab = 1;

    $scope.setTab = function(newTab){
        $scope.customer = angular.copy($scope.defaultCustomer);
        $scope.tab = newTab;
        if(newTab==1)
            $scope.isUpdateable=false;
    };

    $scope.isSet = function(tabNum){
        return $scope.tab === tabNum;
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


    $scope.selectStates=function(stateID){
        
        var request = $http({
            method: "post",
            url: site_url+"/Customer/get_districts",
            data: {
                stateID: stateID
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.districts=response.data.records;
            
        });
    };

    $scope.updateableCustomerIndex=-1;

    $scope.updateCustomerFromTable = function(customer) {
        $scope.customer = angular.copy(customer);
        var index=$scope.customerList.indexOf(customer);
        $scope.updateableCustomerIndex=index;
        $scope.isUpdateable=true;
        $scope.tab=1;
        $scope.selectStates($scope.customer.state_id);

        $scope.customerForm.$setPristine();
    };
    $scope.updateCustomerByCustomerId = function(customer) {
        $scope.master = angular.copy(customer);
        var request = $http({
            method: "post",
            url: site_url+"/Customer/update_customer_by_customer_id",
            data: {
                customer: $scope.master
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.reportArray=response.data.records;
            if($scope.reportArray.success==1){
                $scope.isUpdateable=true;
                $scope.alertmessage="Records updated successfully!";
                $scope.successMsg=true;
                $timeout(function() {
                    $scope.successMsg=false;
                }, 5000);
                $scope.customerList[$scope.updateableCustomerIndex]=$scope.customer;
                $scope.customerForm.$setPristine();
            }

        });

    };
    $scope.saveCustomer = function(customer) {
        $scope.master = angular.copy(customer);
        var request = $http({
            method: "post",
            url: site_url+"/Customer/insert_customer",
            data: {
                customer: $scope.master
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.customerRecord=response.data.records;
            if($scope.customerRecord.success==1){
                $scope.customer=angular.copy($scope.defaultCustomer);
                $scope.customer_form.$setPristine();
              
                $scope.successMsg=true;
                $scope.alertmessage="Customer added successfully!";
                $timeout(function() {
                    $scope.successMsg=false;
                }, 5000);
               
                $scope.isUpdateable=true;
                $scope.master.person_id=$scope.customerRecord.person_id;
                $scope.customerList.unshift($scope.master);
            }
        });


    };

    $scope.reportArray={message:'New Vendor',success:"0"};
    $scope.reMobile = /^(\+\d{1,3}[- ]?)?\d{10}$/;
    $scope.reGST = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
    $scope.reset = function() {
        $scope.vendor = angular.copy($scope.master);
    };

    //$scope.reset();
    $scope.districts=[
        {district_id: "0", district_name: "--Select--"}
    ];


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

 

 
    $scope.resetCustomer=function(){
        $scope.customer=angular.copy($scope.defaultCustomer);
        $scope.isUpdateable=false;
    };

    $scope.$watch('file', function(newfile, oldfile) {
        if(angular.equals(newfile, oldfile) ){
            return;
        }

        uploadService.upload(newfile).then(function(res){
            // DO SOMETHING WITH THE RESULT!
            
        })
    });
});

