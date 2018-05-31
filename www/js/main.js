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

var loggedIn;

function setIndexGlobals(loggedInAtt)
{
   loggedIn = loggedInAtt;
}

function setCurrentPage(page)
{
   $.ajax({
      url: "restInterface.php",
      data: 
      {
         apiUsr: "alarmManager",
         apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
         operation: "setCurrentPage",
         page: page
      },
      type: "POST",
      async: true,
      dataType: 'json',
      success: function (data) 
      {
         return "Ok";
         console.log(getCurrentPage());
      },
      error: function (data)
      {
         console.log("Pagina settata KO: " + page);
         console.log(data);
         return "RequestKo";
      }
   });
}

function getCurrentPage()
{
   var result = null;
   
   $.ajax({
      url: "restInterface.php",
      data: 
      {
         apiUsr: "alarmManager",
         apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
         operation: "getCurrentPage"
      },
      type: "POST",
      async: false,//Non può essere asincrona, serve il risultato prima di andare avanti con gli script di pagina
      dataType: 'json',
      success: function (data) 
      {
         result = data.result;
      },
      error: function (data)
      {
         console.log("Pagina richiesta KO");
         console.log(data);
         result = "RequestKo";
      }
   });
   
   return result;
}

function setMainMenuLinks()
{
   //Events manager
   $("#eventsManagerBtn").off("click");
   $("#eventsManagerBtn").click(function(event){
      loggedIn = true;
      event.preventDefault();
      $("#headerSpan").css("opacity", 0.0);
      $("#mainContainer").css("opacity", 0.0);
      setTimeout(function(){
        $("#headerSpan").html("Events generators management");  
        $("#mainContainer").load("modules/eventsManager.php");
      }, 300);
   });
   
   //E-mail book
   $("#emailBookBtn").off("click");
   $("#emailBookBtn").click(function(event){
      loggedIn = true;
      event.preventDefault();
      $("#headerSpan").css("opacity", 0.0);
      $("#mainContainer").css("opacity", 0.0);
      setTimeout(function(){
        $("#headerSpan").html("Message book");  
        $("#mainContainer").load("modules/emailBook.php");
      }, 300);
   });
   
   //E-Mail addresses book
   $("#emailAddressBookBtn").off("click");
   $("#emailAddressBookBtn").click(function(event){
      loggedIn = true;
      event.preventDefault();
      $("#headerSpan").css("opacity", 0.0);
      $("#mainContainer").css("opacity", 0.0);
      setTimeout(function(){
        $("#headerSpan").html("Address book");
        $("#mainContainer").load("modules/emailAddressBook.php");
      }, 300);
   });
   
   //Events log
   $("#eventsLogBtn").off("click");
   $("#eventsLogBtn").click(function(event){
      loggedIn = true;
      event.preventDefault();
      $("#headerSpan").css("opacity", 0.0);
      $("#mainContainer").css("opacity", 0.0);
      setTimeout(function(){
        $("#headerSpan").html("Events log");
        $("#mainContainer").load("modules/eventsLog.php");
      }, 300);
   });
   
   //API testing
   $("#applicationsBtn").off("click");
   $("#applicationsBtn").click(function(event){
      loggedIn = true;
      event.preventDefault();
   });
}

function logout()
{
   var result = false;
   $.ajax({
      url: "restInterface.php",
      data: 
      {
         apiUsr: "alarmManager",
         apiPwd: "d0c26091b8c8d4c42c02085ff33545c1",
         operation: "logout"
      },
      type: "POST",
      async: false,
      dataType: 'json',
      success: function (data) 
      {
         if(data.result === "Ok")
         {
            result = true;
         }
         else
         {
            result = false;
         }
         loggedIn = false;
         console.log("Local logout OK");
         console.log(data);
      },
      error: function (data)
      {
         console.log("Local logout KO");
         console.log(JSON.stringify(data));
         loggedIn = true;
         result = false;
      }
   });
   
   return result;
}

function showLoginModule()
{
   $("#mainContainer").load("modules/login.php");
   $("#headerSpan").html("Notification management system");
}

function showLoggedUserModule()
{
   $("#footer div.footerBoxContainer").show();
   $("#footerNotifContainer").show();
   var width = $(window).width() - 720 - 200;
   $("#footerBoxLastContainer").css("width", width);
   
   $("#logoutBtn").click(function()
   {
      var result = logout();
      if(result)
      {
        $("#headerSpan").css("opacity", 0.0);
        $("#mainContainer").css("opacity", 0.0);
        setTimeout(function(){
          hideMainMenu();
          hideLoggedUserModule();
          showLoginModule();
          $("#headerSpan").html("Notification management system");
        }, 300);
      }
   });
}

function hideLoggedUserModule()
{
   $("#footer div.footerBoxContainer").hide();
   $("#footerNotifContainer").hide();
   var footerBoxLastContainerWidth = $(window).width();
   $("#footerBoxLastContainer").css("width", footerBoxLastContainerWidth);
}

function showMainMenu()
{
   var paddingLeft = parseInt(($(window).width() - 900)/2);
   $("#mainMenu").css("padding-left", paddingLeft);
   $("#mainMenu").show();
   $("#mainContainer").height($(window).height() - $("#appHeader").height() - $("#mainMenu").height() - $("#footer").height());
   setMainMenuLinks();
}

function hideMainMenu()
{
   $("#mainMenu").hide();
   $("#mainContainer").height($(window).height() - $("#appHeader").height() - $("#footer").height());
}

function showEventsManagerModule(callerPage)
{
   $("#mainContainer").load("modules/eventsManager.php");
   $("#headerSpan").html("Events generators management");
   $("#headerSpan").css("opacity", 1.0);
   loggedIn = true;
   if(callerPage === "loginPage")
   {
      
   }
}

function showGeneratorAlerts(/*Per ora non usato*/callerPage, generatorId)
{
   $("#mainContainer").load("modules/eventsManager.php?generatorId=" + generatorId);
   $("#headerSpan").html("Events generators management");
   $("#headerSpan").css("opacity", 1.0);
   loggedIn = true;
}

function showEmailBookModule()
{
   $("#mainContainer").load("modules/emailBook.php");
   $("#headerSpan").html("Message book");
   $("#headerSpan").css("opacity", 1.0);
   loggedIn = true;
}

function showEmailAddressBookModule()
{
   $("#mainContainer").load("modules/emailAddressBook.php");
   $("#headerSpan").html("Address book");
   $("#headerSpan").css("opacity", 1.0);
   loggedIn = true;
}

function showEventsLogModule()
{
   $("#mainContainer").load("modules/eventsLog.php");
   $("#headerSpan").html("Events log");
   $("#headerSpan").css("opacity", 1.0);
   loggedIn = true;
}

function getDateForPicker(type)
{
   var now = new Date();
     
   var day = now.getDate();
   if(day < 9)
   {
      day = "0" + day;
   }

   var month = now.getMonth() + 1;
   if(month < 9)
   {
      month = "0" + month;
   }

   var hour = now.getHours();
   if(hour < 9)
   {
      hour = "0" + hour;
   }

   var min = now.getMinutes();
   if(min < 9)
   {
      min = "0" + min;
   }
   
   var year = now.getFullYear();
   
   if(type === "oneYear")
   {
      year = year + 1;
   }
   
   return day + "/" + month + "/" + year + " " + hour + ":" + min;
}

