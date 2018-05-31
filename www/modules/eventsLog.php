<?php
/* Notifications Management System.
   Copyright (C) 2017 DISIT Lab http://www.disit.org - University of Florence

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */
   session_start();
?>

<div class="row" id="eventsLogContainer">
   <div class="col-sm-12">
      <div class="col-sm-4 <?php if($_SESSION['usrRole'] != "ToolAdmin"){echo 'col-sm-offset-1';} ?> centerWithFlex">
         Text filter
      </div>
      <div class="col-sm-2 centerWithFlex">
         Start date
      </div>
      <div class="col-sm-2 centerWithFlex">
         End date
      </div>
      <?php
         if($_SESSION['usrRole'] == "ToolAdmin")
         {
            echo '<div class="col-sm-2 centerWithFlex">';
               echo 'Application';
            echo '</div>';
         }
      ?>
   </div>
   <div class="col-sm-12" id="filterEventsLogTableRow">
      <div class="col-sm-4 <?php if($_SESSION['usrRole'] != "ToolAdmin"){echo 'col-sm-offset-1';} ?> centerWithFlex">
         <input data-toggle="tooltip" data-container="body" title="Text filter will output records containing search string in all fields but event time and application" id="filterEventsLogTableText" type="text" class="form-control" />
      </div>
      <div class="col-sm-2 centerWithFlex">
         <div class="form-group">
            <div class="input-group date" id="filterEventsLogTableStart">
               <input data-toggle="tooltip" data-container="body" title="Use this filter to search for events occurred after a certain moment" type="text" class="form-control" />
               <span class="input-group-addon">
                  <span class="glyphicon glyphicon-calendar"></span>
               </span>
            </div>
         </div>
      </div>
      <div class="col-sm-2 centerWithFlex">
         <div class="form-group">
            <div class="input-group date" id="filterEventsLogTableEnd">
               <input data-toggle="tooltip" data-container="body" title="Use this filter to search for events occurred before a certain moment" type="text" class="form-control" />
               <span class="input-group-addon">
                  <span class="glyphicon glyphicon-calendar"></span>
               </span>
            </div>
         </div>
      </div>
      <?php
         if($_SESSION['usrRole'] == "ToolAdmin")
         {
            echo '<div class="col-sm-2 centerWithFlex">';
               echo '<select data-toggle="tooltip" data-container="body" title="Select events generated from a specific application or from all" id="eventsLogAppSelect" class="form-control"></select>';
            echo '</div>';
         }
      ?>
      <div class="col-sm-2 centerWithFlex">
         <button data-toggle="tooltip" data-container="body" title="Apply search criteria" id="filterEventsLogTableBtn" type="button" class="btn btn-primary">Filter</button>
         <button data-toggle="tooltip" data-container="body" title="Restore default events log table (events of today)" id="restoreEventsLogTableBtn" type="button" class="btn btn-primary">Restore</button>
      </div>
   </div>
   
   <div id="eventsLogTableContainer" class="col-sm-10 col-sm-offset-1">
      <div id="mainLoading" class="col-sm-10 col-sm-offset-1">
         <div class="col-sm-12 centerWithFlex loadingMsg">Loading events, please wait</div>
         <div class="col-sm-12 centerWithFlex loadingSpin"><i class="fa fa-circle-o-notch fa-spin"></i></div>
      </div>
      <table id="eventsLogTable" class="mainContainerTable">
          <thead>
              <tr>
              </tr>
          </thead>
          <tbody>
          </tbody>
      </table>
   </div>
   
   <input type="hidden" id="filterActive" />
   
   <div id="eventsLogMsg" class="col-sm-10 col-sm-offset-1 centerWithFlex"></div>
   
   <div id="newEventsPopup">
      <div id="newEventsPopupHeader" class="centerWithFlex">
         New events occurred
      </div>
      <div id="newEventsPopupBody">
         <div id="newEventsPopupBodyHeader">
            <div class="newEventsPopupBodyHeaderCell centerWithFlex">
               Generator
            </div>
            <div class="newEventsPopupBodyHeaderCell centerWithFlex">
               Event
            </div>
            <div class="newEventsPopupBodyHeaderCell centerWithFlex">
               Time
            </div>
         </div>
         <div id="newEventsPopupBodyContent">
            
         </div>
      </div>
   </div>
</div>

<script>
   $(document).ready(function () 
   {
      var newEventsPopupTop = $(window).height() + $("#newEventsPopup").outerHeight();
      //var newEventsPopupLeft = $(window).width() - $("#newEventsPopup").outerWidth();
      var newEventsPopupLeft = 0;
      $("#newEventsPopup").css("top", newEventsPopupTop + "px");
      $("#newEventsPopup").css("left", newEventsPopupLeft + "px");
      
      $("#filterActive").val("false");
      
      var prevPage = getCurrentPage();
      setCurrentPage("eventsLog");
      
      $('[data-toggle="tooltip"]').tooltip(); 
      
      //Refresh
      if(prevPage === "eventsLog")
      {
         $("#appHeader").css("opacity", 1.0);
            setTimeout(function(){
               $("#footer").css("opacity", 1.0);
                setTimeout(function(){
                  $("#mainContainer").css("opacity", 1.0);
               }, 500);
         }, 500);
      }
      else
      {
         $("#headerSpan").css("opacity", 1.0); 
         $("#mainContainer").css("opacity", 1.0);
      }
      
      setMainMenuLinks();
      $("#eventsLogBtn").off("click");
      
      $("#mainMenu div.mainMenuItemContainer").removeClass("active");
      $("#eventsLogBtn").parent().addClass("active");
      
      var usrRole = "<?php echo $_SESSION['usrRole']; ?>";
      var appName = "<?php echo $_SESSION['loginApp']; ?>";
      var ldapAppName = "<?php echo $_SESSION['loginAppLdap']; ?>";
      
      setEventsLogGlobals(usrRole, appName, ldapAppName);
      
      $('#filterEventsLogTableStart').datetimepicker({
         format: 'YYYY-MM-DD HH:mm:ss'
      });
      
      $('#filterEventsLogTableEnd').datetimepicker({
         format: 'YYYY-MM-DD HH:mm:ss'
      });
      
      $("#filterEventsLogTableBtn").off("click");
      $("#filterEventsLogTableBtn").click(function(){
         //Marchiamo il filtro come attivo qui e lo marchiamo come non attivo quando è terminata l'esecuzione di loadEventsLogTable derivante dalla pressione di restore
         $("#filterActive").val("true");
         filterEventsLogTable(usrRole);
      });
      
      $("#restoreEventsLogTableBtn").off("click");
      $("#restoreEventsLogTableBtn").click(function(){
         restoreEventsLogTable(usrRole);
      });
      
      if(usrRole === "ToolAdmin")
      {
         loadEventsLogTable(getDateForDatabase("midnightToday"), getDateForDatabase("now"), "All", "All", usrRole, " ");
      }
      else
      {
         loadEventsLogTable(getDateForDatabase("midnightToday"), getDateForDatabase("now"), appName, ldapAppName, usrRole, " ");
      }
      
   });//Fine document ready
</script>   