app.controller("vendorCtrl", function ($scope,$http,uploadService,$timeout) {
    $scope.msg = "This is vendor controller";
    $scope.successMsg=false;
    $scope.alertmessage="";


    $scope.sort = {
        active: '',
        descending: undefined
    };

    $scope.isUpdateable=false;
    $scope.defaultVendor={
        "company_name": "",
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
        "state_id": "19",
        "bank_name": "",
        "branch": "",
        "ifsc_code": "",
        "micr_code": "",
        "account_number": "",
    };
    $scope.vendor=angular.copy($scope.defaultVendor);
    var request = $http({
        method: "post",
        url: site_url+"/vendor/get_states",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        $scope.states=response.data.records;
        $scope.selectStates($scope.defaultVendor.state_id);
    });

    var request = $http({
        method: "post",
        url: site_url+"/vendor/get_vendors",
        data: {}
        ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    }).then(function(response){
        $scope.vendorList=response.data.records;
    });


    $scope.tab = 1;

    $scope.setTab = function(newTab){
        $scope.vendor = angular.copy($scope.defaultVendor);
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
            url: site_url+"/vendor/get_districts",
            data: {
                stateID: stateID
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.districts=response.data.records;
            
        });
    };

    $scope.updateableVendorIndex=-1;

    $scope.updateVendorFromTable = function(vendor) {
        $scope.vendor = angular.copy(vendor);
        var index=$scope.vendorList.indexOf(vendor);
        $scope.updateableVendorIndex=index;
        $scope.isUpdateable=true;
        $scope.tab=1;
        $scope.selectStates($scope.vendor.state_id);

        $scope.vendorForm.$setPristine();
    };
    $scope.updateVendorByVendorId = function(vendor) {
        $scope.master = angular.copy(vendor);
        var request = $http({
            method: "post",
            url: site_url+"/vendor/update_vendor_by_vendor_id",
            data: {
                vendor: $scope.master
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
                $scope.vendorList[$scope.updateableVendorIndex]=$scope.vendor;
                $scope.vendorForm.$setPristine();
            }

        });

    };
    $scope.saveVendor = function(vendor) {
        $scope.master = angular.copy(vendor);
        var request = $http({
            method: "post",
            url: site_url+"/vendor/insert_vendor",
            data: {
                vendor: $scope.master
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.vendorRecord=response.data.records;
            if($scope.vendorRecord.success==1){
                $scope.vendor=angular.copy($scope.defaultVendor);
                $scope.vendor_form.$setPristine();
              
                $scope.successMsg=true;
                $scope.alertmessage="Vendor added successfully!";
                $timeout(function() {
                    $scope.successMsg=false;
                }, 5000);
               
                $scope.isUpdateable=true;
                $scope.master.person_id=$scope.vendorRecord.person_id;
                $scope.vendorList.unshift($scope.master);
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

    $scope.copyVendorName = function () {
        $scope.vendor.mailing_name = $scope.vendor.person_name;
    }

    $scope.newVendor = function () {
        $scope.msg = "New Vendor Creation";
        var request=$.ajax({
            type:'get',
            url: site_url+"/vendor/new_vendor_form",
            data:  {},
            success: function(data, textStatus, xhr) {
                $('#main-working-div').html(data)
            }

        });// end of ajax

    };
    $scope.resetVendor=function(){
        $scope.vendor=angular.copy($scope.defaultVendor);
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

