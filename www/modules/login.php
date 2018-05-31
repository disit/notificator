<?php
/* Alarm Management System.
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

?>
<div id="loginIntroContainer">
   <div id="loginIntroMsg">
      Welcome to DISIT Notification management system
   </div>
   <div id="loginCarouselRow">
      <div id="loginCarousel" class="carousel slide" data-ride="carousel">
         <div class="carousel-inner" role="listbox">
           <div id="loginCarousel1" class="item active">
           </div>
           <div id="loginCarousel2" class="item">
           </div>
           <div id="loginCarousel3" class="item">
           </div>
         </div>
      </div>
   </div>
   <div id="loginCarouselTextRow">
      <div id="loginCarouselText" class="carousel slide" data-ride="carousel">
         <div class="carousel-inner" role="listbox">
           <div class="item active">
              Get alarm notifications everywhere you are
           </div>
           <div class="item">
              Save and pick contacts from address book
           </div>
           <div class="item">
              Compose messages, save and pick them from message book
           </div>
         </div>
      </div>
   </div>
</div>

<div id="loginFormContainer" class="container-fluid centerWithFlex">
   <div id="loginFormRow" class="row">
      <div id="loginTitle" class="col-sm-12">Login</div>
      <div id="loginSubtitle" class="col-sm-12">Please fill the following form with your credentials</div>
      <form id="loginForm">
         <div class="row">
            <div id="loginFormUsrLbl" class="col-sm-4 col-sm-offset-1 centerWithFlex">Username</div>
            <div class="col-sm-4">
               <input type="text" id="loginUsr" name="loginUsr" />
            </div>
         </div>
         <div class="row">
            <div id="loginFormPwdLbl" class="col-sm-4 col-sm-offset-1 centerWithFlex">Password</div>
            <div class="col-sm-4">
               <input type="password" id="loginPwd" name="loginPwd" />
            </div>
         </div>
         <div class="row" id="loginBtnRow">
            <button type="button" id="rstLoginFormBtn" class="btn btn-secondary btn-sm">Reset</button>
            <button type="button" id="loginBtn" class="btn btn-primary btn-sm">Login</button>
         </div>
         <div class="row" id="loginMsgRow">
            <div id="loginMsg" class="col-sm-6 col-sm-offset-3 centerWithFlex"></div>
            <div id="loginLoadingIcon" class="col-sm-6 col-sm-offset-3 centerWithFlex">
               <i class="fa fa-spinner fa-spin" style="font-size:24px"></i>
            </div>   
         </div>
      </form>
   </div>
</div>

<script src="js/main.js"></script>

<script>
   $(document).ready(function () 
   {
      setCurrentPage("login");
      
      $("#loginCarouselRow .carousel-inner > .item").height($("#loginCarouselRow").height());
      $("#loginCarouselRow .carousel-inner > .item").width($("#loginIntroContainer").width());
      
      $("#loginIntroMsg").css("margin-top", ($('#loginCarousel').height()/2) - $("#loginIntroMsg").height());
      $("#loginIntroMsg").css("margin-bottom", 0);
      
      $("#appHeader").css("opacity", 1.0);
      $("#headerSpan").css("opacity", 1.0);
      setTimeout(function(){
         $("#footer").css("opacity", 1.0);
          setTimeout(function(){
            $("#mainContainer").css("opacity", 1.0);
         }, 300);
      }, 300);
      
      $('#loginCarousel').carousel({
         interval: 3200,
         pause: null
      });
      
      $('#loginCarouselText').carousel({
         interval: 3200,
         pause: null
      });
      
      
      $(window).off("resize", loginWindowResize);  
      $(window).resize(loginWindowResize);
      
      $("#rstLoginFormBtn").click(resetLoginForm);
      $("#loginBtn").click(login);
   });
</script>   

