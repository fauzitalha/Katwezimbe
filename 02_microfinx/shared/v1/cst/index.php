<?php
include("saas/saasrouter.php");

// ... SAAS logic Here
$domain_name = trim($_SERVER['SERVER_NAME']);
ExecuteSAASRouter($domain_name);


include("conf/no-session.php");


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php     
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations($APP_NAME, $APP_SMALL_LOGO); 
    ?>   
    <style>

        * {
            margin: 0;
            padding: 0;
        }
        .imgbox {
            display: grid;
            height: 100%;
        }
        .center-fit {
            width: 100%;
            max-height: 100vh;
            margin: auto;
        }

    </style>
  </head>

  <body>

  	<div style="background: #FFF;">

  		<!-- top navigation -->
  		<div class="row">
  			<div class="top_nav">
		      <div class="nav_menu" style="margin-bottom: 0; padding-bottom: 0">
		          <ul class="nav navbar-nav navbar-right">
		            <li class="list-group-item-success"><a href="cst-acct-actvn">Account Activation</a></li>
		            <li class="list-group-item-danger"><a href="cst-lgin">Sign In</a></li>
		            <li><a href="index"><?php echo $APP_NAME; ?></a></li>
		          </ul>
		      </div>

		    </div>
  		</div>
	   
	    
	    <!-- /top navigation -->

	    <!-- slide show feed -->
      <div class="row" style="background-color: #EEE">
        <div class="imgbox">
          <img class="center-fit" src="files/images/front_page_slider/indexx.jpg">
      </div>
        
      </div>
	  	<!--<div class="row" style="background: #2f4357;">
	  		<div class="clearfix"></div>
    		<div id="myCarousel" class="carousel slide" data-interval="6000" data-ride="carousel">
	        <ol class="carousel-indicators">
	            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
	            <li data-target="#myCarousel" data-slide-to="1"></li>
	            <li data-target="#myCarousel" data-slide-to="2"></li>
	        </ol>   
        	<div class="carousel-inner">
            <div class="active item">
              <img src="files/images/front_page_slider/amber.png" alt="First Slide">
	         		<div class="carousel-caption">
	                  <h3>Simplicity</h3>
	                  <p>Easy to manage & control your account(s).</p>
	                </div>
	            </div>
            	<div class="item">
                <img src="files/images/front_page_slider/grey.png" alt="Second Slide">
                <div class="carousel-caption">
                  <h3>Reliability</h3>
                  <p>Consistently & accurately provide your needs at all times.</p>
                </div>
            	</div>
            	<div class="item">
                <img src="files/images/front_page_slider/purple.png" alt="Third Slide">
                <div class="carousel-caption">
                  <h3>Security</h3>
                  <p>100% safety of your account and data.</p>
                </div>
            </div>
        	</div>

		        <a class="carousel-control left" href="#myCarousel" data-slide="prev">
		            <span class="glyphicon glyphicon-chevron-left"></span>
		        </a>
		        <a class="carousel-control right" href="#myCarousel" data-slide="next">
		            <span class="glyphicon glyphicon-chevron-right"></span>
		        </a>
		    </div>
		    <div class="clearfix"></div>
	  	</div>-->
	    <!-- /slide show feed -->


	    <!-- article feed -->
	    <!--<div class="row" style="padding-left: 15px; padding-right: 15px;">
	    	<br>
	    	<div class="col-md-4 col-sm-4 col-xs-6">
          <div class="x_panel">
            <div class="x_title">
              <h2>Info 01</h2>
              
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
                Add content to the page ...
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-4 col-xs-6">
          <div class="x_panel">
            <div class="x_title">
              <h2>Info 02</h2>
              
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
                Add content to the page ...
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
            </div>
          </div>
        </div>

        <div class="col-md-4 col-sm-4 col-xs-6">
          <div class="x_panel">
            <div class="x_title">
              <h2>Info 03</h2>
              
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
                Add content to the page ...
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
            </div>
          </div>
          <br />
	    	<br />
	    	<br />
        </div>
	    	
	    </div>-->
    	<!-- /article feed -->


    	<!-- Bottom Link -->
	    <div class="row" style="color: #FFF; background: #2f4357; padding-left: 25px; padding-right: 25px;">
	    	<span style="font-family: calibri; font-size: 35px;"><?php echo $APP_NAME; ?></span>
	    	<hr style="margin-top: 3px; margin-bottom: 10px;" />
	    	<div>
	    		<div class="pull-left" style="font-family: calibri; font-size: 14px;"><?php echo $COPY_RIGHT_STMT; ?></div>
	    		<br />
	    		<br />
	    	</div>
	    </div>
    	<!-- /Bottom Link -->



    	<!-- Copy right Statement -->
	    <div>
	    	
	    </div>
    	<!-- /Copy right Statement -->



  	</div>



  </body>

  <?php
  LoadDefaultJavaScriptConfigurations();
  ?>
</html>


