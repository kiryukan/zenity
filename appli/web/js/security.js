/**
 * Created by all on 19/04/17.
 */
$(".password").attr(
    'pattern',
    PASSWORD_PATTERN
);
function login(userNameID,passwordID) {
    let password = SHA512($('#'+passwordID).val());
    let userName = $('#'+userNameID).val();
    let target = document.getElementById('spinner-wrapper');
    let spinner = new Spinner().spin(target);
    $.ajax({
        dataType: "json",
        url: WEB_SERVICE_URL + '/login',
        data: {login: userName, password: password},
        type: 'POST',
        success: function (result) {
            localStorage.setItem("token", result['token']);
            Cookies.set('logged', 'true',{ expires: 2 });
            spinner.stop();
            document.location.replace('/reporting');
        },
        error: function () {
            alert("echec de l'authentification");
            spinner.stop();
        }
    });
}
function register(){
    if ($('#password').val() == ""){
        alert ("provide a password")
        return
    }
    let password = SHA512($('#password').val())
    let passwordBis = SHA512($('#password-confirm').val())
    if(password != passwordBis){
        alert("password don't match")
        return 
    }
    let email = $('#email').val()
    let emailBis = $('#email-confirm').val()
    if(email != emailBis){
        alert("email don't match")
        return
    }
    let userName = $("#username").val()
    let companyName = $("#company-name").val()
    $.ajax({
        dataType: "json",
        url: WEB_SERVICE_URL + '/register',
        data: {
            login: userName,
            password: password,
            email:email,
            companyName:companyName
        },
        type: 'GET',
        success: function (result) {
            localStorage.setItem("token", result['token']);
            Cookies.set('logged', 'true',{ expires: 2 });
            document.location.replace('/installation');
        },
        error: function () {
            alert("Account creation failed");
        }
    });
}
function switchToRegister(){
    let registerArea = $("#register-area")
    registerArea.toggle()
    if(registerArea.is(":visible")){
        $("#login-button")
            .html("Back to Login")
            .prop('onclick', null)
            .off("click")
            .on("click",()=>switchToRegister())
        $("#login-header").html("Create account")
        $("#register-button")
            .prop('onclick', null)
            .off("click")
            .on("click",()=>register())
    }else{
        $("#login-button")
            .html("Login")
            .prop('onclick', null)
            .off("click")
            .on("click",()=>login())
        $("#login-header").html("LOGIN")

        $("#register-button")
            .html("Create account")
            .prop('onclick', null)
            .off("click")
            .on("click",()=>switchToRegister())
        
    }
}
function logout() {
    $.ajax({
        dataType: "json",
        url: WEB_SERVICE_URL+'/logout',
        type:'POST',
        data:{token:localStorage.getItem('token')},
        success: function (result) {
            localStorage.setItem("token","");
            document.location.replace('/');
            Cookies.set('logged', 'false');
            logged();
        },
        error: function () {
            console.log('warning, token persist on the server');
            localStorage.setItem("token","");
            Cookies.set('logged','false');
            logged();
        }
    });
}
function logged(){
    if(localStorage.getItem('token') != ''){
        $.ajax({
            dataType: "json",
            url: WEB_SERVICE_URL,
            type:'POST',
            data:{token:localStorage.getItem('token')},
            success: function (result) {
              if (window.location.pathname == '/')document.location.replace('/reporting');
            },
            error: function () {
              if (window.location.pathname != '/')document.location.replace('/');

            }
        });
    }else{
      if (window.location.pathname != '/')document.location.replace('/');
    }
}
