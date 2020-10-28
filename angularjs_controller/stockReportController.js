app.controller("stockReportController", function ($scope,$http,$filter,$rootScope,$timeout,CommonCode) {
    $scope.msg = "This is Stock Report controller";
    
    $scope.tab = 1;

    $scope.setTab = function(newTab){
        $scope.tab = newTab;
    };


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



    $scope.mseed_date_wise_report=function(start_date,end_date){
        var start_date=$scope.changeDateFormat(start_date);
        var end_date=$scope.changeDateFormat(end_date);
        var request = $http({
            method: "post",
            url: site_url+"/StockReport/get_mustard_seed_report_by_date",
            data: {
                start_date: start_date
                ,end_date: end_date
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.mustardseedStockReportDaywise=response.data.records;
            var dataLn = $scope.mustardseedStockReportDaywise.length;
            var myEl = angular.element( document.querySelector( '#mseed-stock-report-div'));
            if(dataLn > 15){
                myEl.addClass('add-scroll');
            }else{
                myEl.removeClass('add-scroll');
            }
        });
    };


    $scope.mustard_oil_date_wise_report=function(start_date,end_date){
        var start_date=$scope.changeDateFormat(start_date);
        var end_date=$scope.changeDateFormat(end_date);
        var request = $http({
            method: "post",
            url: site_url+"/StockReport/get_mustard_oil_report_by_date",
            data: {
                start_date: start_date
                ,end_date: end_date
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.mustardOilStockReportDaywise=response.data.records;
            var dataLn = $scope.mustardOilStockReportDaywise.length;
            var myEl = angular.element( document.querySelector( '#moil-stock-report-div'));
            if(dataLn > 15){
                myEl.addClass('add-scroll');
            }else{
                myEl.removeClass('add-scroll');
            }
        });
    };




    $scope.oil_cake_date_wise_report=function(start_date,end_date){
        var start_date=$scope.changeDateFormat(start_date);
        var end_date=$scope.changeDateFormat(end_date);
        var request = $http({
            method: "post",
            url: site_url+"/StockReport/get_oil_cake_report_by_date",
            data: {
                start_date: start_date
                ,end_date: end_date
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.oilCakeStockReportDaywise=response.data.records;
            var dataLn = $scope.oilCakeStockReportDaywise.length;
            var myEl = angular.element( document.querySelector( '#oil-cake-stock-report-div'));
            if(dataLn > 15){
                myEl.addClass('add-scroll');
            }else{
                myEl.removeClass('add-scroll');
            }
        });
    };



});

