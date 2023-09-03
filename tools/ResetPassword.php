<?php
#Error Reporting
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

#Login Details
$Username = ''; //admin
$Password = ''; //admin

$LoginSuccessful = false;
// Check username and password:
if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
    if ($Username == $_SERVER['PHP_AUTH_USER'] && $Password == $_SERVER['PHP_AUTH_PW']) {
        $LoginSuccessful = true;
    }
}
if (empty($Username) || empty($Password)){
   $LoginSuccessful = true;
}
// Login passed successful?
if (!$LoginSuccessful) {
    header('WWW-Authenticate: Basic realm="Vtiger Admin Area"');
    header('HTTP/1.0 401 Unauthorized');
    die("Login failed!\n");
} else {
    
    
    // Turn on debugging level 
    $Vtiger_Utils_Log = true;
    include_once('vtlib/Vtiger/Menu.php');
    include_once('vtlib/Vtiger/Module.php');

    function encrypt_password($username, $user_password, $crypt_type = '')
    {
        $salt = substr($username, 0, 2);
        if ($crypt_type == '') {
            $crypt_type = 'MD5';
        }
        if ($crypt_type == 'MD5') {
            $salt = '$1$' . $salt . '$';
        } elseif ($crypt_type == 'BLOWFISH') {
            $salt = '$2$' . $salt . '$';
        } elseif ($crypt_type == 'PHP5.3MD5') {
            $salt = '$1$' . str_pad($salt, 9, '0');
        }
        $encrypted_password = crypt($user_password, $salt);
        return $encrypted_password;
    }    
    
    $adb = PearDatabase::getInstance();
    
    if (isset($_POST['pwd2']) && isset($_POST['pwd1']) && isset($_POST['username']) && $_POST['pwd2'] == $_POST['pwd1'] && !empty($_POST['pwd1'])) {
        $error  = false;
        $status = "sucess";
        $sql    = 'SELECT user_name, crypt_type FROM vtiger_users WHERE status = "Active" and id = "' . $_POST['username'] . '" limit 1';
        $result = $adb->query($sql);
        if ($adb->num_rows($result) > 0) {
            while ($row = $adb->fetchByAssoc($result)) {
                $crypt_type = $row['crypt_type'];
                $user_name  = $row['user_name'];
            }
            $userid            = $_POST['username'];
            $encryptedPassword = encrypt_password($user_name, $_POST['pwd1'], $crypt_type);
            $query             = "UPDATE vtiger_users SET user_password=?, confirm_password=? where id=?";
            $adb->pquery($query, array(
                $encryptedPassword,
                $encryptedPassword,
                $userid
            ));
            if ($adb->hasFailedTransaction()) {
                if ($dieOnError) {
                    $error  = "error setting new password: [" . $adb->database->ErrorNo() . "] " . $adb->database->ErrorMsg();
                    $status = "error";
                }
            }
            if (isset($_POST['recreate']) && $_POST['recreate'] == '1') {
                require_once('modules/Users/CreateUserPrivilegeFile.php');
                createUserPrivilegesfile($userid);
                createUserSharingPrivilegesfile($userid);
                require_once($root_directory . 'user_privileges/user_privileges_' . $userid . '.php');
                require_once($root_directory . 'user_privileges/sharing_privileges_' . $userid . '.php');
            }
        } else {
            $error  = "Invalid User Selected!";
            $status = "error";
        }
        header("Location: " . $_SERVER['PHP_SELF'] . ($status == "error" ? "?status=error&msg=" . $error : "?status=success"));
        exit;
    }
    

    
    
    $sql       = 'SELECT id, user_name, first_name, last_name FROM vtiger_users WHERE status = "Active"';
    $result    = $adb->query($sql);
    $listusers = '<select id="selectbasic" name="username" class="form-control">';
    while ($row = $adb->fetchByAssoc($result)) {
        $listusers .= '<option value="' . $row['id'] . '">' . $row['first_name'] . ' ' . $row['last_name'] . ' - (' . $row['user_name'] . ')</option>';
        
    }
    $listusers .= '</select>';
    
echo '<!doctype html>
<html lang="en"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Change User\'s Password</title>
<link rel="stylesheet" href="libraries/bootstrap/css/bootstrap.css" type="text/css" media="all" />
<script type="text/javascript" src="libraries/jquery/jquery.min.js"></script>    
<style>
html {
    font-size: 10pt;
}
body {
    background-color: rgba(250,250,250,0.5)
}
input, select, .uneditable-input {
    margin-left: 20px;
    height: 28px;
    font-family: tahoma;
}    
input[type="checkbox"] {
    margin-left: 20px;
}
.loginform {
    width: 510px;
    margin: 60px auto;
    padding: 25px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.2), 
                inset 0px 1px 0px 0px rgba(250, 250, 250, 0.5);
    border: 1px solid rgba(0, 0, 0, 0.3);
}
.center {
    text-align:center;
}
.cf:before,
.cf:after {
    content: ""; 
    display: table;
}

.cf:after {
    clear: both;
}
.cf {
    *zoom: 1;
}


</style>
<script type="text/javascript">
  function checkForm(form) {
      $("#formerror").hide();
      $("formsuccess").hide();
    if(form.username.value == "") {
      //alert("Error: Username cannot be blank!");
      $("#formerror").show();
      $("#formerror").text("Error: Username cannot be blank!");
      form.username.focus();
      return false;
    }
    if(form.pwd1.value != "" && form.pwd1.value == form.pwd2.value) {
      if(form.pwd1.value.length < 6) {
        //alert("Error: Password must contain at least six characters!");
        $("#formerror").show();
        $("#formerror").text("Error: Password must contain at least six characters!");
        form.pwd1.focus();
        return false;
      }
    } else {
      //alert("Error: Please check that you\'ve entered and confirmed your password!");
      $("#formerror").show();
      $("#formerror").text("Error: Please check that you\'ve entered and confirmed your password!");
      form.pwd1.focus();
      return false;
    }
    return true;
  }
//<![CDATA[
$(window).load(function(){
$(".chb").change(function() {
    var checked = $(this).is(":checked");
    $(".chb").prop("checked",false);
    if(checked) {
        $(this).prop("checked",true);
    }
});
});//]]> 
  
  
</script>    
</head>

<body>
        <div class="container">

      <div class="starter-template">
<div class="loginform cf">
<h1 class="center">Change User\'s Password</h1>

<form class="form-horizontal" name="form" method="post" action="'.$_SERVER['PHP_SELF'].'" onsubmit="return checkForm(this);">
<fieldset>

<!-- Form Name -->
<legend class="center" >Change User\'s Password</legend>';
    if (isset($_GET['status']) && $_GET['status'] == 'success') {
        echo '<div id="formsuccess" class="alert alert-success">User Password Changed Successfully</div>';
    }
    if (isset($_GET['status']) && $_GET['status'] == 'error' && isset($_GET['msg'])) {
        $style = "";
        $msg   = $_GET['msg'];
    } else {
        $style = 'style="display:none;"';
        $msg   = "";
    }
echo '<div id="formerror" class="alert alert-danger" '.$style.'>'.$msg.'</div>
<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="selectbasic">CRM User:</label>
  <div class="col-md-4">
'.$listusers.'
 </div>
</div><br />

<!-- Password input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="pwd1">Password:</label>
  <div class="col-md-5">
    <input id="pwd1" name="pwd1" type="password" placeholder="New Password" class="form-control input-md" required="">
    
  </div>
</div><br />

<!-- Password input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="pwd2">Confirm Password:</label>
  <div class="col-md-5">
    <input id="pwd2" name="pwd2" type="password" placeholder="Confirm Password" class="form-control input-md" required="">
    
  </div>
</div><br />

<!-- Multiple Checkboxes (inline) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="checkboxes">Privilege:</label>
  <div class="col-md-4">
    <label class="checkbox-inline" for="checkboxes-0">
      <input type="checkbox" name="recreate" id="checkboxes-0" value="1"  class="chb" checked>
      Recreate User Privilege Files
    </label>
    <label class="checkbox-inline" for="checkboxes-1">
      <input type="checkbox" name="recreate" id="checkboxes-1" value="0"  class="chb">
      Ignore User Privilege Files
    </label>
  </div>
</div><br />

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="singlebutton"></label>
  <div class="col-md-4">
    <button id="singlebutton" name="singlebutton" class="btn btn-danger">Submit</button>
  </div>
</div>

</fieldset>
</form>
    
    </div>
        
      </div>

    </div>    
</body>
</html>';
}
?>