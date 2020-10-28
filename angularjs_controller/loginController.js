app.controller("loginCtrl", function ($scope,$http,$filter,md5,$window) {
    $scope.msg = " This is Log in controller";
    $scope.loginData={};
    $scope.login=function (loginData) {
        var psw=md5.createHash($scope.loginData.user_password || '');
        var request = $http({
            method: "post",
            url: site_url+"/base/validate_credential",
            data: {
                    userId: loginData.user_id
                ,userPassword: psw
            }
            ,headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(function(response){
            $scope.loginDatabaseResponse=response.data;
            $(".modal-backdrop").hide();
            if($scope.loginDatabaseResponse.person_cat_id==3){
                $window.location.href = base_url+'#!/staffArea';
            }else{
                alert("Check user Id or Password");
            }
        });
    };

    $scope.searchItem=function(){
      alert("Work in progress")
    };

});

