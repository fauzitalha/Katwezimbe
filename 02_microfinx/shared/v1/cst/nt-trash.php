<?php
# ... Important Data
include("conf/session-checker.php");

# ... Customer-Data
include("cstmr-mgt.php");

?>
<!DOCTYPE html>
<html>
  <head>
    <?php
    # ... Device Settings and Global CSS
    LoadDeviceSettings(); 
    LoadDefaultCSSConfigurations("Drafts", $APP_SMALL_LOGO); 

    # ... Javascript
    LoadPriorityJS();
    OnLoadExecutions();
    StartTimeoutCountdown();
    ExecuteProcessStatistics();
    ?>
  </head>

  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">

            <div class="navbar nav_title" style="border: 0;">
              <a href="main-dashboard" class="site_title"> <span><?php echo $APP_NAME; ?></span></a>
            </div>

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <?php SideNavBar($CUST_ID); ?>
            </div>
            <!-- /sidebar menu -->


          </div>
        </div>

        <!-- top navigation -->
        <?php TopNavBar($firstname); ?>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="col-md-12 col-sm-12 col-xs-12">

            <!-- System Message Area -->
            <div align="center" style="width: 100%;"><?php if( isset($_SESSION['ALERT_MSG']) ){ echo $_SESSION['ALERT_MSG']; } ?></div>


            <div class="x_panel">
              <div class="x_title">
                <h2>Trash</h2>
                <div class="clearfix"></div>
              </div>

              <!--<div class="x_content" style="overflow-y: auto; height: 490px;"> -->        
              <div class="x_content">     
                <?php
                $RECIPIENT_ID = $_SESSION['CST_USR_ID'];
                $trash_list = array();
                $trash_list = FetchTrash($RECIPIENT_ID);
                $NFTCN_LIST = "";

                for ($i=0; $i < sizeof($trash_list); $i++) { 
                  $trash = array();
                  $trash = $trash_list[$i];
                  $NTFCN_ID = "'".$trash['NTFCN_ID']."'";
                  if ($NFTCN_LIST=="") {
                    $NFTCN_LIST = $NTFCN_ID;
                  } else {
                    $NFTCN_LIST = $NFTCN_LIST.",".$NTFCN_ID;
                  }
                }

                ?>    
                <table id="datatable" class="table">
                  <thead>
                    <tr><th width="1"></th><th>Trash List</th></tr>
                  </thead>
                  <tbody>
                    <?php
                    $notif_list = array();
                    $notif_list = FetchNftcnsByScope($NFTCN_LIST);

                    for ($i=0; $i < sizeof($notif_list); $i++) { 
                      
                      $notif_msg = array();
                      $notif_msg = $notif_list[$i];
                      $M_RECORD_ID = $notif_msg['RECORD_ID'];
                      $M_NTFCN_ID = $notif_msg['NTFCN_ID'];
                      $M_SENDER_ID = $notif_msg['SENDER_ID'];
                      $M_HAS_ATTCHMT_FLG = $notif_msg['HAS_ATTCHMT_FLG'];
                      $M_RECALL_FLG = $notif_msg['RECALL_FLG'];
                      $M_SEND_DATE = $notif_msg['SEND_DATE'];
                      $M_NTFCN_SUBJECT = $notif_msg['NTFCN_SUBJECT'];
                      $M_NTFCN_MSG = $notif_msg['NTFCN_MSG'];
                      $M_NTFCN_THREAD_ID = $notif_msg['NTFCN_THREAD_ID'];
                      $M_NTFCN_MSG_STATUS = $notif_msg['NTFCN_MSG_STATUS'];

                      # ... Get Sender Id
                      $address = FetchAddressFromAddressBookById($M_SENDER_ID);
                      $SENDER_NAME = $address['ADDRESS_ENTITY_NAME'];

                      
                      # ... Determine of Message has attachment
                      $ATT_DISP = "";
                      if ($M_HAS_ATTCHMT_FLG=="NN") {
                        $ATT_DISP = "";
                      } else if ($M_HAS_ATTCHMT_FLG=="YY") {
                        $ATT_DISP = "<i class='fa fa-paperclip pull-right'></i>";
                      }

                      # ... Get Thread Count
                      $thread_count = FetchThreadCount($M_NTFCN_THREAD_ID, $M_NTFCN_ID);
                      $thd_cnt_disp = "";
                      if ($thread_count>1) {
                        $thd_cnt_disp = "<span class='badge bg-blue'>".$thread_count."</span>";
                      } else {
                        $thd_cnt_disp = "";
                      }

                      # ... Process Time Display
                      $time_stamp = strtotime($M_SEND_DATE);
                      $disp_time = "";

                      $SEND_D = date("Y-m-d", $time_stamp);
                      $TODAY = date("Y-m-d", time());
                      $DATE = date("Y-m-d H:i:s", time());
                      if ($SEND_D==$TODAY) {
                        $disp_time = date("H:i", $time_stamp);
                      } else {
                        $disp_time = date("Y-m-d", $time_stamp);
                      }
            
            

                      $data_transfer = $M_NTFCN_ID;
                      ?>
                      <tr>
                        <td></td>
                        <td>
                          <a href="nt-trash-msg-details?k=<?php echo $data_transfer; ?>">
                            <strong style="font-size: 14px;"><?php echo $SENDER_NAME; ?></strong><br>
                            <?php echo $ATT_DISP; ?>

                            <strong style="font-size: 12px;"><?php echo $M_NTFCN_SUBJECT; ?></strong>
                            <span class="pull-right"><?php echo $disp_time; ?></span>
                            <br>
                            
                            <p style="font-size: 12px;"><?php echo $M_NTFCN_MSG; ?></p>
                          </a>
                          
                        </td>
                        
                      </tr>
                      <?php
                    }


                    ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>          
        </div>
        <!-- /page content -->

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            <?php echo $COPY_RIGHT_STMT; ?> 
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>



    <?php LoadDefaultJavaScriptConfigurations(); ?>
  
  </body>
</html>
