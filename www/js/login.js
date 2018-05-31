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

function loginWindowResize()
{
   $("#loginCarouselRow .carousel-inner > .item").height($("#loginCarouselRow").height());
   $("#loginCarouselRow .carousel-inner > .item").width($("#loginIntroContainer").width());

   $("#loginIntroMsg").css("margin-top", ($('#loginCarousel').height()/2) - $("#loginIntroMsg").height());
   $("#loginIntroMsg").css("margin-bottom", 0);
}

function resetLoginForm()
{
   $("#loginForm").trigger("reset");
   $("#loginMsg").html("");
}

function login()
{
   $("#loginBtnRow").hide();
   $("#loginMsg").html("Checking credentials, please wait");
   $("#loginLoadingIcon").show();
   $("#loginMsgRow").show();
   
    $.ajax({
      url: "restInterface.php",
      data: {
         apiUsr: "alarmManager",
         apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
         operation: "localLogin",
         usr: $("#loginUsr").val(),
         pwd: $("#loginPwd").val()
      },
      type: "POST",
      async: true,
      dataType: 'json',
      success: function (data) 
      { 
         switch(data.result)
         {
            case "Ok":
               $("#loginLoadingIcon").hide();
               $("#loginMsgRow").hide();
               $("#loginBtnRow").show();
               $("#mainContainer").css("opacity", 0.0);
               $("#headerSpan").css("opacity", 0.0);
                
               setTimeout(function(){
                  showLoggedUserModule();
                  $("#loggedUsrHidden").html(data.loginUsr);
                  $("#loginAppHidden").html(data.loginApp);
                  showMainMenu();
                  showEventsManagerModule("loginPage");
                  //$(window).trigger(event);
               }, 500);
               break;
               
            case "missingParams":
               $("#loginLoadingIcon").hide();
               $("#loginBtnRow").show();
               $("#loginMsg").html("Username and/or password missing");
               break;
            
            case "ldapConnKo":
               $("#loginLoadingIcon").hide();
               $("#loginBtnRow").show();
               $("#loginMsg").html("Error in LDAP service connection");
               break;
               
            case "Unauthorized":
               $("#loginLoadingIcon").hide();
               $("#loginBtnRow").show();
               $("#loginMsg").html("Unauthorized to login");
               break;
         }
      },
      error: function (data)
      {
         console.log("Error");
         console.log(JSON.stringify(data));
         $("#loginLoadingIcon").hide();
         $("#loginBtnRow").show();
         $("#loginMsg").html("Error in login API call");
      }
   });
}


