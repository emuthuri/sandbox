<?php
    session_start();

    if(!$_SESSION['client']){

       header("location: index.php");
    }
    else{
        require_once("connect.php");
?>
            <!DOCTYPE html>
            <html lang="en">
                    <head>
                        <!-- jQuery -->
                        <script src="js/jquery-1.11.3.min.js"></script>

                        <!-- Bootstrap Core JavaScript -->
                        <script src="js/bootstrap.min.js"></script>

                        <meta charset="utf-8">
                        <meta http-equiv="X-UA-Compatible" content="IE=edge">
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

                        <title>Sandbox</title>

                        <!-- Bootstrap Core CSS -->
                        <link href="css/bootstrap.min.css" rel="stylesheet">

                        <!-- Custom CSS: overrides any Bootstrap styles and/or apply own styles -->
                        <link href="css/custom.css" rel="stylesheet">
                        
                        <link rel="stylesheet" href="css/stylelanding.css">


                    </head>
                    <body>
                        <!-- Navigation -->
                        <nav id="siteNav" class="navbar navbar-default navbar-fixed-top" role="navigation">
                            <div class="container">
                                <!-- Logo and responsive toggle -->
                                <div class="navbar-header">

                                    <a class="navbar-brand" href="#">
                                            <span class="glyphicon glyphicon-fire"></span> 
                                            SANDBOX
                                    </a>
                                </div>

                                <!-- Navbar links -->
                                <div class="collapse navbar-collapse" id="navbar">
                                    <ul class="nav navbar-nav navbar-right">
                                        <li class="active">
                                            <a href="#">Home</a>
                                        </li>
                                        <li>
                                            <a href="#page-wrapper">New File</a>
                                        </li>
                                        <li>
                                            <a href="#content-3">Browse Files</a>
                                        </li>
                                        <li>
                                                <a href="client_logout.php">Logout</a>
                                        </li>
                                    </ul>

                                </div><!-- /.navbar-collapse -->
                            </div><!-- /.container -->
                        </nav>

                        <!-- Header -->
                        <header>
                            <div class="header-content">
                                <div class="header-content-inner">
                                    <h1>Welcome to Sandbox</h1>
                                    <p>Sandbox is a web application that enables users to interact locally with files from the server.</p>
                                    You can browse through text files that are already uploaded,
                                    edit uploaded text files and even save them locally in your pc!	
                                    With Sandbox, saved files are abstracted from any other interaction other than through this webapp!
                                    Therefore, private content is kept private and neither a system administrator nor you, the user, should be worried about irresponsible replication of your files!</p>
                                  <a href="#page-wrapper" class="btn btn-primary btn-lg">Create a new Text File</a>
                                </div>
                            </div>
                        </header>

                        <!-- Content 1 -->
                        <section class="content">
                                <?php
                                    $name2='';
                                    if(isset($_GET['document'])){
                                            $id=$_GET['document'];
                                            $fetch=$db->prepare("SELECT * FROM files WHERE file_id=?");
                                            $fetch->bindParam(1,$id);
                                            $fetch->execute();
                                            $res=$fetch->fetch();	
                                            $name2=$res['docname'];
                                            $filename=$res['filename'];
                                            echo'<script>
                                                        $(document).ready(function(){
                                                            $("#content").load("original/'.$filename.'");
                                                        });
                                                 </script>';
                                    }
                                ?>

                                <div class="container">
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
                                              <button type="submit">Save File</button>
                                              <div id="messages"></div>
                                            </div>
                                        </form>
                                        <div id="files">
                                          <h2>File Browser</h2>
                                          <ul id="file-list"></ul>
                                        </div>
                                    </div>
                                </div>

                        </section>

                        <!-- Content 2 -->
                        <section class="content content-2">
                            <div class="container" id="content-3">
                                <div class="row">
                                    <div class="col-md-3"></div>
                                    <div class="col-md-6" style="border:1px solid grey;">
                                <div class="panel-default">
                                    
                                    <div class="panel-body">
                                        <div clas="panel panel-default">
                                            <div class="panel-heading"><center><font color="#fff" size="5">Template Files</font></center></div>
                                            <div class="panel-body">
                                        <table class="table table-striped" style="background: #eee;">
                                                <?php
                                                    $select=$db->prepare("SELECT * FROM files ORDER BY file_id DESC");
                                                    $select->execute();

                                                    //loop throught the rows .legth in java
                                                    while($results=$select->fetch()){
                                                            $doc=$results['docname'];
                                                            $file_id=$results['file_id'];
                                                            echo'<tr><td><center><a href="landing.php?document='.$file_id.'"><font color="blue">'.$doc.'</a></font></center></td></tr>';
                                                    }
                                                ?>
                                        </table>
                                                </div>
                                        </div>
                                   </div>
                                </div>
                                    </div>
                                     <div class="col-md-3">
                                        </div>
                                    </div>
                            </div>
                        </section>

                        <!-- Footer -->
                        <footer class="page-footer">
                           
                            <!-- Copyright etc -->
                            <div class="small-print">
                                    <div class="container">
                                            <p>Copyright &copy; COM/039/13	 2016</p>
                                    </div>
                            </div>
                        </footer>

                        <!-- Plugin JavaScript -->
                        <script src="js/jquery.easing.min.js"></script>

                        <!-- Custom Javascript -->
                        <script src="js/custom.js"></script>
                        
                        <!-- Text Editor JavaScript -->
                        <script src="applanding.js"></script>


                    </body>

            </html>
    <?php
    }
    ?>