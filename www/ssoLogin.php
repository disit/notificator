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
   
  $ldapRole = null;
  $ldapOk = false;
  $appUrl = "https://notificator.snap4city.org/notificator";
   
  $oidc = new OpenIDConnectClient(
        'https://www.snap4city.org',
        'php-notificator',
        '...secret...'
    );

    $oidc->setVerifyHost(false);
    $oidc->setVerifyPeer(false);

    $oidc->providerConfigParam(array('authorization_endpoint'=>'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/auth'));
    $oidc->providerConfigParam(array('token_endpoint'=>'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/token'));
    $oidc->providerConfigParam(array('userinfo_endpoint'=>'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/userinfo'));
    $oidc->providerConfigParam(array('jwks_uri'=>'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/certs'));
    $oidc->providerConfigParam(array('issuer'=>'https://www.snap4city.org/auth/realms/master'));
    $oidc->providerConfigParam(array('end_session_endpoint'=>'https://www.snap4city.org/auth/realms/master/protocol/openid-connect/logout'));

    $oidc->addScope(array('openid','username','profile'));
    $oidc->setRedirectURL($appUrl . '/ssoLogin.php');
    try {
      $oidc->authenticate();
    } catch(Exception $ex) {
      header("Location: ssoLogin.php?exception");
    }

    //Appena Piero te lo dice, cambia il campo reperito in "username"
    $username = $oidc->requestUserInfo('username');
    $ldapUsername = "cn=". $username . ",dc=ldap,dc=disit,dc=org";

    $ds = ldap_connect($ldapServer, $ldapPort);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    $bind = ldap_bind($ds);

    if($ds && $bind)
    {
        if(RestController::checkLdapMembership($ds, $ldapUsername, "Dashboard"))
        {
           if(RestController::checkLdapRole($ds, $ldapUsername, "RootAdmin")) {
              $ldapRole = "RootAdmin";
              $ldapOk = true;
           } else if(RestController::checkLdapRole($ds, $ldapUsername, "ToolAdmin")) {
              $ldapRole = "ToolAdmin";
              $ldapOk = true;
           } else if(RestController::checkLdapRole($ds, $ldapUsername, "AreaManager")) {
              $ldapRole = "AreaManager";
              $ldapOk = true;
           } else if(RestController::checkLdapRole($ds, $ldapUsername, "Manager")) {
              $ldapRole = "Manager";
              $ldapOk = true;
           } else {
              $msg="no role";
           }
        } else {
          $msg="no group";
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
      $oidc->signOut($oidc->getAccessToken(), $appUrl . "/ssoLogin.php?msg=".$msg);
    }
