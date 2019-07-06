
<?php
        session_start();
        //session initialization
        if(!$_SESSION['admin']){
           header("location: index.php");
        }
        else{
            if(isset($_POST['logout'])){
                header("location:admin_logout.php");
            }
            //link with the db connector
            require_once("connect.php");
            $output="";
                if(isset($_POST['upload'])){
                        $name=$_POST['name'];
                        $docname=$_FILES['document']['name'];
                        $upload_doc=$docname;
                        $docTemp=$_FILES['document']['tmp_name'];
                        $docType=$_FILES['document']['type'];
                        $docSize=$_FILES['document']['size'];
                        //remove white spaces from the $upload_doc filename                        
                        $upload_doc=preg_replace("#[^a-z0-9.]#i","",$upload_doc);

                        if($docSize>10000000){
                                $output="<div class='alert alert-danger' role='alert'>The file is too big</div>";
                                $page='admin.php';
                                $time="3";
                                header("Refresh:$time;url=$page");
                        }
                        else{
                                if(!$docTemp){
                                        $output="<div class='alert alert-danger' role='alert'>You have not selected a file. Please try again</div>";
                                        $page='admin.php';
                                        $time="3";
                                        header("Refresh:$time;url=$page");
                                 }
                                else{
                                    $upload=$db->prepare("INSERT INTO files(docname,filename)VALUES (?,?)" );
                                    $upload->bindParam(1,$name);
                                    $upload->bindParam(2,$upload_doc);
                                    $upload->execute();
                                    if($upload){
                                            move_uploaded_file($docTemp,"original/$upload_doc");
                                            $output="<div class='alert alert-success' role='alert'>You have successfully uploaded the document</div>";

                                            $page='admin.php';
                                            $time="3";
                                            header("Refresh:$time;url=$page");
                                     }
                                    else{
                                            $output="<div class='alert alert-danger' role='alert'>Unable to upload the file at this time. Try again</div>";
                                            $page='admin.php';
                                            $time="3";
                                            header("Refresh:$time;url=$page");
                                    }
                                }
                        }
                }
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <title>Home</title>
            <link href="bootstrapcss/bootstrap.css" rel="stylesheet"/>
            <link rel="stylesheet" href="admin.css">
            <script src="js/jquery-1.11.3.min.js"></script>
            <script src="js/bootstrap.min.js"></script>
        </head>
        <body>
                <header>
                    <form action="" method="post"><h1 class="sand">Sandbox <button type="submit" name="logout" class="logout">Logout</button></h1></form>
                </header>

                <div id="carrasco">
                    <div id="dekim">
                            <div class="panel panel-default">
                                          <div class="panel-heading"><font color="brown">Upload File</font></div>
                                    <div class="panel-body" style="min-height:50px;">
                                          <form action="" enctype="multipart/form-data" method="POST">
                                                <div class="form-group">
                                                    <label for="name">Name</label>
                                                    <input type="text" class="form-control" required="required" name="name" id="name" placeholder="Document name">
                                                 </div>
                                                 <div class="form-group">
                                                    <label for="exampleInputFile">File input</label>
                                                    <input type="file" name="document" id="exampleInputFile">
                                                 </div>
                                                 <button type="submit" name="upload"class="btn btn-primary btn-sm">Upload</button>
                                          </form>
                                          <br>
                                          <?php echo $output;?>
                                    </div>
                            </div>

                            <div class="panel panel-default">
                                    <div class="panel-heading"><font color="brown">Available Files</font></div>
                                        <div class="panel-body" style="min-height:300px">
                                            <table class="table table-striped" >

                                                    <?php
                                                            $select=$db->prepare("SELECT * FROM files ORDER BY file_id DESC");
                                                            $select->execute();

                                                            //loop throught the rows.... .legth in java
                                                            while($results=$select->fetch()){
                                                                    $doc=$results['docname'];
                                                                    $file_id=$results['file_id'];
                                                                    echo'<tr><td><a href="admin.php?document='.$file_id.'"><font color="blue">'.$doc.'</a></font></tr>';
                                                            }
                                                    ?>
                                            </table>
                                        </div>
                            </div>
                    </div>		  
                    <?php
                            $name2='';
                            if(isset($_GET['document'])){
                            //fetches the id from the URL ju ni GET. hiyo ninii initwa document    
                            $id=$_GET['document'];
                            $fetch=$db->prepare("SELECT * FROM files WHERE file_id=?");
                            $fetch->bindParam(1,$id);
                            $fetch->execute();
                            $res=$fetch->fetch();	
                            $name2=$res['docname'];
                            $filename=$res['filename'];
                            echo'<script>
                            $(document).ready(function(){


                           //loads content of the filename to the content text area
                            $("#content").load("original/'.$filename.'");
                            });

                            </script>';
                            }
                    ?>	
                    <div id="page-wrapper" class="clearfix">
                            <h1>Text Editor</h1>
                            <br>
                            <form action="#" method="POST" id="file-form">
                                    <div class="field">
                                      <input type="text" name="filename" id="filename" value="<?php echo $name2;?>" placeholder="Filename (e.g. test1.txt)">
                                    </div>
                                    <div class="field">
                                      <textarea name="content" id="content" placeholder="Type your content here..."></textarea>
                                    </div>
                                    <div class="field">
                                      <button type="submit" class="saving">Save File</button>
                                      <div id="messages"></div>
                                    </div>
                            </form>
                            <div id="files">
                              <h2>File Browser</h2>
                              <ul id="file-list"></ul>
                            </div>

                    </div>

                    <script src="applanding.js"></script>
                </div>
        </body>
        </html>
        <?php

        }
?>