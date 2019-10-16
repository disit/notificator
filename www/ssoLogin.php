<?php
      /* Dashboard Builder.
   Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

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
   
  include "RestController.php";
  require 'sso/autoload.php';
  use Jumbojett\OpenIDConnectClient; 
   
  $conf = parse_ini_file("./conf/conf.ini");
  $ldapServer = $conf["ldapServer"];
  $ldapPort = $conf["ldapPort"];
  $ldapBaseDN = $conf["ldapBaseDN"];
  
  $ssoCliendId = $conf["ssoCliendId"];
  $ssoClientSecret = $conf["ssoClientSecret"];
  $ssoEndpoint = $conf["ssoEndpoint"];
  $ssoAuthorizationEndpoint = $conf["ssoAuthorizationEndpoint"];
  $ssoTokenEndpoint = $conf["ssoTokenEndpoint"];
  $ssoUserinfoEndpoint = $conf["ssoUserinfoEndpoint"];
  $ssoJwksUri = $conf["ssoJwksUri"];
  $ssoIssuer = $conf["ssoIssuer"];
  $ssoEndSessionEndpoint = $conf["ssoEndSessionEndpoint"];
  $appUrl = $conf["appUrl"];
  
  $ldapRole = null;
  $ldapOk = false;
   
  $oidc = new OpenIDConnectClient(
        $ssoEndpoint,
        $ssoCliendId,
        $ssoClientSecret
    );

    $oidc->setVerifyHost(false);
    $oidc->setVerifyPeer(false);

    $oidc->providerConfigParam(array('authorization_endpoint'=>$ssoAuthorizationEndpoint));
    $oidc->providerConfigParam(array('token_endpoint'=>$ssoTokenEndpoint));
    $oidc->providerConfigParam(array('userinfo_endpoint'=>$ssoUserinfoEndpoint));
    $oidc->providerConfigParam(array('jwks_uri'=>$ssoJwksUri));
    $oidc->providerConfigParam(array('issuer'=>$ssoIssuer));
    $oidc->providerConfigParam(array('end_session_endpoint'=>$ssoEndSessionEndpoint));

    $oidc->addScope(array('openid','username','profile'));
    $oidc->setRedirectURL($appUrl . '/ssoLogin.php');
    try {
      $oidc->authenticate();
    } catch(Exception $ex) {
      header("Location: ssoLogin.php?exception");
   //     echo $ex;
    }

    //Appena Piero te lo dice, cambia il campo reperito in "username"
    $username = $oidc->requestUserInfo('username');
    $ldapUsername = "cn=". $username . ",$ldapBaseDN";

    $ds = ldap_connect($ldapServer, $ldapPort);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    $bind = ldap_bind($ds);

    if($ds && $bind)
    {
        if(RestController::checkLdapMembership($ds, $ldapUsername, "Dashboard", $ldapBaseDN))
        {
           if(RestController::checkLdapRole($ds, $ldapUsername, "RootAdmin", $ldapBaseDN)) {
              $ldapRole = "RootAdmin";
              $ldapOk = true;
           } else if(RestController::checkLdapRole($ds, $ldapUsername, "ToolAdmin", $ldapBaseDN)) {
              $ldapRole = "ToolAdmin";
              $ldapOk = true;
           } else if(RestController::checkLdapRole($ds, $ldapUsername, "AreaManager", $ldapBaseDN)) {
              $ldapRole = "AreaManager";
              $ldapOk = true;
           } else if(RestController::checkLdapRole($ds, $ldapUsername, "Manager", $ldapBaseDN)) {
              $ldapRole = "Manager";
              $ldapOk = true;
           } else {
              $msg="$ldapUsername no role";
           }
        } else {
          $msg="$ldapUsername not in group";
        }
    } else {
      $msg="no bind to LDAP";
    }

    if($ldapOk)
    {
        ini_set('session.gc_maxlifetime', $sessionDuration);
        session_set_cookie_params($sessionDuration);
        $_SESSION['loginType'] = "local";
        $_SESSION['loginApp'] = 'Dashboard Manager'; //$result["usrApp"];
        $_SESSION['loginAppLdap'] = 'Dashboard';//$result["usrAppLdap"];
        $_SESSION['loginUsr'] = $username;
        $_SESSION["usrOrigin"] = 'ldap';
        $_SESSION["usrRole"] = $ldapRole;        
        $_SESSION['refreshToken']=$oidc->getRefreshToken();
        $_SESSION['accessToken']=$oidc->getAccessToken();
        
        $_SESSION["currentPage"] = "eventsManager";
        
        header("location: index.php");
    }
    else
    {
      $_SESSION = array();

      if(ini_get("session.use_cookies")) {
          $params = session_get_cookie_params();
          setcookie(session_name(), '', time() - 42000,
              $params["path"], $params["domain"],
              $params["secure"], $params["httponly"]
          );
      }

      session_destroy();

      //Dev'essere assoluto, visto con Piero
      //$oidc->signOut($oidc->getAccessToken(), $appUrl . "/ssoLogin.php?msg=".$msg);
      echo $msg;
    }
