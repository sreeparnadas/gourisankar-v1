app.controller("studentCtrl", function ($scope,$http,$filter,$timeout,dateFilter,$rootScope) {
    $scope.msg = "This is student controller";
    //Tab area
    $scope.tab = 1;
    $scope.setTab = function(newTab){
        $scope.tab = newTab;
    };
    $scope.isSet = function(tabNum){
        return $scope.tab === tabNum;
    };
    //End of tab area
    $scope.student={"address":"","pin":"","area":"","city":"","district":"","cast":"","father_name":"","mother_name":"","email_id":"","contact_1":"","contact_2":"","aadhar_number":"","pan_number":""};

    $scope.dobYears=[];
    //************
    $scope.huiMonths=["Jan","Feb","Mar","April","May","June","July","Aug","Sept","Oct","Nov","Dec"];
    $scope.huiDays=[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31];
    $scope.sexes=["Male","Female"];
    $scope.allStates=[];


   // alert(angular.isDate(new Date('2015-09-28')));


    $scope.getStates=function(){
        var request = $http({
            method: "post",
            url: site_url+"student/get_states",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.allStates=response.data.records;
            $scope.student.state=$scope.allStates[0];
        });
    };//end of function
    $scope.getStates();

    var x=new Date().getFullYear()-9;
    var yearBaseIndex=x;
    for(var i=1;i<21;i++){
       $scope.dobYears.push(x--);
    }
    $scope.student.dobMonth=1;
    $scope.student.doaMonth=1;
    $scope.student.dobDay=1;
    $scope.student.doaDay=1;
    $scope.student.dobYear=yearBaseIndex;
    $scope.student.doaYear=yearBaseIndex;
    $scope.student.dob=yearBaseIndex+"-"+$scope.student.dobMonth+"-"+$scope.student.dobDay;
    $scope.student.doa=yearBaseIndex+"-"+$scope.student.doaMonth+"-"+$scope.student.doaDay;
    $scope.student.sex="Female";
    $scope.dobChange=function(){
        $scope.student.dob=$scope.student.dobYear+"-"+($scope.student.dobMonth)+"-"+($scope.student.dobDay);
    }
        //************
    $scope.allDistricts=[];
    $scope.getDistrictsByStates=function(){
        var request = $http({
            method: "post",
            url: site_url+"student/get_districts_by_state",
            data: {state_id: 19}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.allDistricts=response.data.records;
            $scope.student.district=$scope.allDistricts[0];
        });
    };//end of function

    $scope.getDistrictsByStates();
    $scope.getReligion=function(){
        var request = $http({
            method: "post",
            url: site_url+"student/get_religion",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.allReligion=response.data.records;
            $scope.student.religion=$scope.allReligion[0];
            $scope.getCast($scope.student.religion);
        });
    };//end of function
    $scope.getReligion();
    $scope.getCast=function(religion){
        var request = $http({
            method: "post",
            url: site_url+"student/get_cast",
            data: {religion_id: religion.religion_id}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.allCast=response.data.records;
            $scope.student.cast=$scope.allCast[0];
        });
    };//end of function

    $scope.getBank=function(){
        var request = $http({
            method: "post",
            url: site_url+"student/get_bank",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.allBanks=response.data.records;
            $scope.student.bank=$scope.allBanks[0];
            $scope.getBranches($scope.student.bank);
        });
    };//end of function
    $scope.getBank();

    $scope.getBranches=function(bank){
        var request = $http({
            method: "post",
            url: site_url+"student/get_bank_branches",
            data: {bank_id: bank.bank_id}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.allBranches=response.data.records;
            $scope.student.branch=$scope.allBranches[0];
        });
    };//end of function
    $scope.getMotherTongue=function(){
        var request = $http({
            method: "post",
            url: site_url+"student/get_motherTongue",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.allMotherTongue=response.data.records;
            $scope.student.mothertongue=$scope.allMotherTongue[0];

        });
    };//end of function
    $scope.getMotherTongue();
    $scope.getBloodGroup=function(){
        var request = $http({
            method: "post",
            url: site_url+"student/get_blood_group",
            data: {}
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.allBloodGroup=response.data.records;
            $scope.student.bloodgroup=$scope.allBloodGroup[0];

        });
    };//end of function
    $scope.getBloodGroup();
    $scope.student.sibling=0;
    $scope.student.is_mental_disable=0;
    $scope.student.is_physical_disable=0;
    $scope.options=["NO","YES"];
    $scope.saveStudent=function(student){
        var tempStudent={};
        tempStudent.student_code=student.student_code;
        tempStudent.student_name=student.student_name;
        tempStudent.address=student.address;
        tempStudent.pin=student.pin;
        tempStudent.area=student.area;
        tempStudent.city=student.city;
        tempStudent.dob=student.dob;
        tempStudent.doa=student.doa;
        tempStudent.cast=student.cast.cast_id;
        tempStudent.religion=student.religion.religion_id;
        tempStudent.father_name=student.father_name;
        tempStudent.mother_name=student.mother_name;
        tempStudent.mother_tongue=student.mothertongue.tongue_id;
        tempStudent.email_id=student.email_id;
        tempStudent.contact_1=student.contact_1;
        tempStudent.contact_2=student.contact_2;
        tempStudent.bank_branch_id=student.branch.branch_id;
        tempStudent.aadhar_number=student.aadhar_number;
        tempStudent.pan_number=student.pan_number;
        tempStudent.blood_group=student.bloodgroup.blood_id;
        tempStudent.sibling=student.sibling;
        tempStudent.is_mental_disable=student.is_mental_disable;
        tempStudent.is_physical_disable=student.is_physical_disable;
        tempStudent.district=student.district.district_id;

        tempStudent.bank_branch_id=student.branch.branch_id;

    }
});

