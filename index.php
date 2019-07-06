<?php
//error_reporting(0);
$output='';
require_once("connect.php");
if(isset($_POST['login'])){
        $name=$_POST['user'];
        $password=md5($_POST['password']);

        $check=$db->prepare("SELECT * FROM registeredusers WHERE username=? AND password=?");
        $check->bindParam(1,$name);
        $check->bindParam(2,$password);
        $check->execute();
        //count the number of rows
        $count=$check->rowCount();

        $check2=$db->prepare("SELECT * FROM systemuser WHERE username=? AND password=?");
        $check2->bindParam(1,$name);
        $check2->bindParam(2,$password);
        $check2->execute();
        $count2=$check2->rowCount();

        if($count>0){
                session_start();  
                $_SESSION['client']=$name;
                header("location: landing.php");
         }
        elseif($count2>0){
                session_start();  
                $_SESSION['admin']=$name;
                header("location: admin.php");
         }
        else{
                $output="<div class='alert alert-danger' role='alert'>Wrong credentials</div>";
                $page='index.php';
                $time="3";
                header("Refresh:$time;url=$page");
         }
}

//register.php begins here
// Check for form submission
if (isset($_POST['firstname'],$_POST['surname'],$_POST['user'],$_POST['password'])) {
    
        $erors = array();                      // set an empty array that will contains the errors
        $regexp_mail = '/^([a-zA-Z0-9]+[a-zA-Z0-9._%-]*@([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,4})$/';         // an e-mail address pattern

        // remove tags and whitespace from the beginning and end of form data
        $_POST = array_map("strip_tags", $_POST);
        $_POST = array_map("trim", $_POST);
        $username=$_POST['user']; //email variable for checking if it exist from db

        // check if all form fields are filled in correctly
        // (email address and the minimum number of characters in "user" and "password")
        //The preg_match function is used to perform a regular expression match
        if (!preg_match($regexp_mail, $_POST['user'])) $erors[] = 'Invalid e-mail address';
        
        //minimum number of characters in the password field
        if (strlen($_POST['password'])<5) $erors[] = 'Password must contain minimum 5 characters';

        
        //check if email already exist in db
        $check=$db->prepare("SELECT * FROM registeredusers WHERE username=?");
        $check->bindparam(1,$username);
        $check->execute();
        $count=$check->rowCount();
        //if count is > than 0 then the email exist
        if($count>0){
                $output="<div class='alert alert-danger' role='alert'>Email address already exist</div>";
                $page='index.php';
                $time="3";
                header("Refresh:$time;url=$page");
        }
        //end of email checker
        
        else{
                // if no errors ($error array empty)
                if(count($erors)<1) {
                        // connect to the "authentic" database
                        $conn = new mysqli('localhost', 'root', '', 'authentic');

                        // check connection
                        //this method returns the error code for the last connect call
                        if (mysqli_connect_errno()) {
                            exit('Connect failed: '. mysqli_connect_error());
                        }

                        // store the values in an Array, escaping special characters for use in the SQL statement
                        //real_escape_string_escapes special characters in a string for use in anSQL statement
                        //it takes into account the current charset of the connection
                        $adds['firstname'] = $conn->real_escape_string($_POST['firstname']);
                        $adds['surname'] = $conn->real_escape_string($_POST['surname']);
                        $adds['user'] = $conn->real_escape_string($_POST['user']);
                        $adds['password'] = $conn->real_escape_string($_POST['password']);
                        //$adds['password2'] = $conn->real_escape_string($_POST['password2']);
                        //because I only need to store one value of a password. the password2 field is for javascript validation
                        
                        //Encrypted password
                        $pass=  md5($adds['password']);
                        
                        // sql query for INSERT INTO users
                        $sql = "INSERT INTO `registeredusers` (`firstname`, `surname`,`username`, `password`) VALUES ('". $adds['firstname']. "','". $adds['surname']. "','". $adds['user']. "', '". $pass. "')"; 

                        // Performs the $sql query on the server to insert the values
                        if ($conn->query($sql) === TRUE) {
                                $output= "<div class='alert alert-success' role='alert'>New user successfully registered</div>";
                                $page='index.php';
                                $time="3";
                                header("Refresh:$time;url=$page");
                        }
                        else {
                                $output= "<div class='alert alert-danger' role='alert'>". $conn->error ."</div>";
                                $page='index.php';
                                $time="3";
                                header("Refresh:$time;url=$page");
                        }

                        $conn->close();
                }
                else {
                        // else, if errors, it adds them in string format and print it
                        $output= "<div class='alert alert-danger' role='alert'>". implode('<br />', $erors)."</div>";
                        $page='index.php';
                        $time="3";
                        header("Refresh:$time;url=$page");
                }
        }
}


?>

<!DOCTYPE html>
<html>
        <head>
              <meta charset="UTF-8">
              <title>Sandbox Login</title>

              <link href="bootstrapcss/bootstrap.css" rel="stylesheet"/>
              <link rel="stylesheet" href="css/reset.css">
              <link rel="stylesheet" href="css/style.css">

              <script type="text/javascript">
                              function checkPass()
                              {
                                  //Store the password field objects into variables ...
                                  var password = document.getElementById('password');
                                  var password2 = document.getElementById('password2');

                                  //Set the colors we will be using ...
                                  var goodColor = "#6aff6a";
                                  var badColor = "#ff9d9d";

                                  //Compare the values in the password field 
                                  //and the confirmation field
                                  if(password.value === password2.value){
                                      //The passwords match. 
                                      //Set the color to the good color and inform
                                      //the user that they have entered the correct password 
                                      password2.style.backgroundColor = goodColor;
                                   }
                                  else{
                                      //The passwords do not match.
                                      //Set the color to the bad color and
                                      //notify the user.
                                      password2.style.backgroundColor = badColor;
                                    }
                              }
              </script>
        </head>

        <body>
              <div class="container">
                          <div class="login">
                                  <center><?php echo $output; ?></center>
                                  <h1 class="login-heading">Sandbox Login</h1>
                                  <form method="post" action="">
                                          <input type="text" name="user" placeholder="Username" class="input-txt" />
                                          <input type="password" name="password" placeholder="Password" class="input-txt" />          

                                          <br/><br/>

                                          <div class="login-footer">
                                                  <button type="submit" name="login" class="btn-sign-in">Sign in  </button>
                                          </div>

                                          <br>

                                  </form>
                          </div>

                          <div class="toregister" id="ctr">
                                  <div class="flipper">
                                          <div class="register-button">
                                              <button type="submit" class="create-account">Create account  </button>
                                          </div>
                                          <div class="back">
                                                  <form action="" method="post">
                                                          <h3 class="login-registration">Sandbox Registration</h3>
                                                          <br>
                                                          <input type="text" name="firstname" placeholder="First name" required="required" class="input-txt" />
                                                          <input type="text" name="surname" placeholder="Surname" required="required" class="input-txt" />
                                                          <input type="text" name="user" placeholder="Enter your Email address (Username)" required="required" class="input-txt" />
                                                          <input type="password" name="password" id="password" placeholder="Password" required="required" class="input-txt" />          
                                                          <input type="password" name="password2" id="password2" placeholder="Confirm Password" required="required" class="input-txt" onkeyup="checkPass(); return false;"/>          
                                                          <br><br>
                                                          <div class="register-footer">
                                                                  <input type="submit" class="btn-submit" value="Submit"/>
                                                          </div>
                                                          <br><br><br><br><br><br>
                                                  </form>
                                          </div>
                                  </div>
                          </div>	
              </div>

              <script src="js/jquery-1.11.3.min.js"></script>
              <script src="js/bootstrap.min.js"></script>
        </body>
</html>
