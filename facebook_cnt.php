<?php
/***************************************************************************
 *   copyright				: (C) 2008 - 2013 WeBid
 *   site					: http://www.webidsupport.com/
 ***************************************************************************/

/***************************************************************************
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version. Although none of the code may be
 *   sold. If you have been sold this script, get a refund.
 ***************************************************************************/

///////////////////////////////////////////////////////////////////////////////////////
//// Connect with facebook Mod ////////// E ///// I left nots and labeled most ////////
///////////////////////////////////////// N ///// of the codes to tell you what ///////
///////////////////////////////////////// J ///// they are for and what they do. //////
//// There is 4 different parts ///////// O ///// You set your app id and app /////////
//// to this mod on this page /////////// Y ///// secret in your admin area and ///////
///////////////////////////////////////////////// you can also turn the connect ///////
//// part 1: log in to facebook ///////////////// with facebook on or off. ////////////
//// and gets the info and is /////////// T ///////////////////////////////////////////
//// stored in webid sql //////////////// H ///////////////////////////////////////////
///////////////////////////////////////// E ///// Made by uBidzz.com //////////////////
//// Part 2 unlink facebook from webid ////////////////////////////////////////////////
//// The unlink facebook deletes the //////////////////////////////////////////////////
//// facebook info that is stored in //// F ///
//// webid sql. This is only used if //// A ///
//// the user clicks on the unlink ////// C ///
//// facebook button. /////////////////// E ///
///////////////////////////////////////// B ///
//// part 3: checks the facebook ids //// O ///
//// in the sql to see if the user ////// O ///
//// is a website member. /////////////// K ///
///////////////////////////////////////////////
///////////////////////////////////////////////
////// part 4: Post auctions to ///////// M ///
////// facebook twitter google ////////// O ///
////// with the item price, ///////////// D ///
////// description, image /////////////////////
////// and title. /////////////////////////////
///////////////////////////////////////////////


/////////////////////////////////////////
////// part 1 starts here ///////////////
/////////////////////////////////////////
////// Get user info from facebook //////
/////////////////////////////////////////
/////////////////////////////////////////////
//////// Check the URL to see if the word ///
//////// fbconnect is in the URL          ///
$checklink = isset($_GET["fbconnect"])? $_GET["fbconnect"] : ''; 
/////////////////////////////////////////////
/////////////////////////////////////////////////////
///////// This code only runs if the connect with ///
///////// Facebook button has been clicked        ///
///////// on in the register.php, index.php       ///
///////// or user_menu.php and edit_data.php pages //
/////////////////////////////////////////////////////
switch($checklink) 
{
    case "fblogin": 
    $fbookappid = $system->SETTINGS['facebook_app_id'];
    $fbookappsecret = $system->SETTINGS['facebook_app_secret'];
    require'includes/facebook.php'; 
    $fbappid = "$fbookappid";   
    $fbappsecret = "$fbookappsecret";  
    $facebook   = new Facebook(array( 
          'appId' => $fbappid, 
          'secret' => $fbappsecret, 
          'cookie' => TRUE, 
    ));
    $fbookuser = $facebook->getUser();
    if ($fbookuser) 
    { 
        try 
        { 
            $user_profile = $facebook->api('/me'); 
        } 
        catch (Exception $e) { 
            $ERR = $e->getMessage(); 
            exit(); 
        } 
        //////Get the person facebook info
        $fb_user_fbids = $fbookuser; 
        $fbuser_email = isset($user_profile["email"])?$user_profile["email"] : ''; 
        $fbuser_fnmae = isset($user_profile["name"])?$user_profile["name"] : ''; 
        $fbuser_address = isset($user_profile["location"])? $user_profile["location"] : ''; 
        $fbuser_phone = isset($user_profile["phone"])? $user_profile["phone"] : ''; 
        $fbuser_status = isset($user_profile["status"]) ? $user_profile["status"] : ''; 
        $fbuser_birthday = isset($user_profile["birthday"]) ? $user_profile["birthday"] : ''; 
        $fbuser_image = isset($fb_user_fbids) ? "https://graph.facebook.com/".$fb_user_fbids."/picture?type=large" : ''; 
        $post_time = time();  
        
        /////////This will check to see if the person has a stored FB id in the FB sql table  
        /////////If there is no stored FB id in the FB sql table then it will make a new 
        /////////FB id column in the FB sql table 
        $query = "SELECT * FROM " . $DBPrefix . "fblogin WHERE email ='" . $fbuser_email . "'"; 
        $result = mysql_query($query); 
        $system->check_mysql($result, $query, __LINE__, __FILE__); 
        
        /// This checks to see if there is a 
        /// facebook id already stored in the  
        /// sql and if there is no facebook id
        /// stored in the sql then this will 
        /// run and store there facebook id
        if (mysql_num_rows($result) == 0) 
        { 
            $sql = "INSERT INTO " . $DBPrefix . "fblogin (fb_id, name, email, image, postdate, address, phone, birthday, status) VALUES ('$fb_user_fbids', '$fbuser_fnmae', '$fbuser_email', '$fbuser_image', '$post_time', '" . $fbuser_address["name"] . "', '$fbuser_phone', '$fbuser_birthday', '$fbuser_status')"; 
            $res = mysql_query($sql); 
            $system->check_mysql($res, $sql, __LINE__, __FILE__);

            $fb_users_id['fb_id'] = $fb_user_fbids;
         } else {
        
        $fb_users_id = mysql_fetch_assoc($result);
        }
        
        //// Make new session's that will be used later on  
        $_SESSION['FBOOK_USER_EMAIL'] = $fbuser_email; 
        $_SESSION['FBOOK_USER_NAME'] = $fbuser_fnmae;
        $_SESSION['FBOOK_USER_IDS'] = $fb_users_id['fb_id'];
        $_SESSION['FBOOK_USER_IMAGE'] = $fbuser_image;
        
        
        
        if (isset($fb_users_id['fb_id']))
        {
            //// Checking the users table to see if 
            //// the user has a stored FB id and if it matchs
            //// we need this code so we can get the facebook
            //// login to work on webid
            $query = "SELECT id, fblogin_id
            FROM " . $DBPrefix . "users 
            WHERE fblogin_id = " . $fb_users_id['fb_id'];
            $fbsql = mysql_query($query);
            $system->check_mysql($fbsql, $query, __LINE__, __FILE__);
            $fb_wb = mysql_fetch_assoc($fbsql);
            $match_ids = $fb_wb['fblogin_id'] == $fb_users_id['fb_id'];
        }
        if ($match_ids)
        {    
            //// make the session that will be needed to see
            //// if the user has a stored facebook id in there sql column 
            $_SESSION['FB_WB'] = $fb_wb['id'];
            
            //// Getting redirected back to any pages that had the 
            //// the redirect session made on if there no redirect session
            //// then the person will be redirected back to the index.php
            if (isset($_SESSION['REDIRECT_AFTER_FBLOGIN']))
            {
                $redirect = $_SESSION['REDIRECT_AFTER_FBLOGIN'];
                $URL = $redirect;
            }
            else
            {
                $URL = 'index.php';
            }
        }
        elseif (isset($_SESSION['REDIRECT_AFTER_FBLOGIN']))
        {
                $redirect = $_SESSION['REDIRECT_AFTER_FBLOGIN'];
                $URL = $redirect;
		}
        else
        {
                $URL = 'register.php?';
         
        }             
    }
    break;  
     
///////////////////////////////////
//// Part 2 starts here         ///
//// unlink facebook with webid ///
///////////////////////////////////
    case "unlinked":
    	$query = "SELECT id, fblogin_id
    	FROM " . $DBPrefix . "users 
        WHERE id = " . $user->user_data['id'];
        $fb_sql = mysql_query($query);
        $system->check_mysql($fb_sql, $query, __LINE__, __FILE__);
        $unlink_fb = mysql_fetch_assoc($fb_sql);

    if (isset($unlink_fb['fblogin_id']))
    {
        /// Delete the user facebook column that is stored in the facebook table in webid sql
        $query = "DELETE from " . $DBPrefix . "fblogin WHERE fb_id = " . $unlink_fb['fblogin_id'];
        $system->check_mysql(mysql_query($query), $query, __LINE__, __FILE__);
        
        //// Delete the facebook id from the user column that will be turned to 0
        $query = "UPDATE " . $DBPrefix . "users SET fblogin_id = '0' WHERE id = '" . $user->user_data['id'] . "'";
          $system->check_mysql(mysql_query($query), $query, __LINE__, __FILE__);
          
          /// unset all facebook session's  
        unset($_SESSION['FBOOK_USER_IDS']);
        unset($_SESSION['FB_WB']);
        unset($_SESSION['FBOOK_USER_EMAIL']);
        unset($_SESSION['FBOOK_USER_NAME']);
        unset($_SESSION['FBOOK_USER_IMAGE']);
      }
          $URL = 'edit_data.php';      
    break;
/// Part 2 ends here   ///
///////////////////////////
} 
//////Part 1 ends here  //////
////////////////////////////////
/////////////////////////////////
//////////////////////////////////
//// Part 3 starts here ////////////
//////////////////////////////////////
//// Facebook login code that turns // 
//// in to webid login code. /////////
//////////////////////////////////////
//// checking to see if the user has the FBOOK_USER_IDS session
if(isset($_SESSION['FBOOK_USER_IDS']))
{
    //// checking to see if the user has the FB_WB session
    if (isset($_SESSION['FB_WB']))
    {
         ////turning the session in to a salt
        $checkusersfb = $_SESSION['FBOOK_USER_IDS'];
        
        ////Check the users sql table to see if a user has the same FB id
        ////and if there is a user with the same FB id it will log them in
        $query = "SELECT id, hash, suspended, password FROM " . $DBPrefix . "users WHERE fblogin_id = " . $checkusersfb;
        $sql = mysql_query($query);
        $system->check_mysql($sql, $query, __LINE__, __FILE__);
        
        /// From here down is what turns the facebook 
        /// login in to webid login.
        if (mysql_num_rows($sql) > 0)
        {    
            $_SESSION['csrftoken'] = md5(uniqid(rand(), true));
            $user_data = mysql_fetch_assoc($sql);
            $password = $user_data['password'];
            if ($user_data['suspended'] == 9)
            {
                $_SESSION['signup_id'] = $user_data['id'];
                header('location: pay.php?a=3');
                exit;
            }
            
            /// Here we are checking the user account
            /// to see if the account is suspended
            if ($user_data['suspended'] == 1)
            {
                $ERR = $ERR_618;
            }
            elseif ($user_data['suspended'] == 8)
            {
                $ERR = $ERR_620;
            }
            elseif ($user_data['suspended'] == 10)
            {
                $ERR = $ERR_621;
            }
            else
            {
                /// Here we are making the webid session's to tell
                /// webid that we are loged in
                $_SESSION['WEBID_LOGGED_IN']         = $user_data['id'];
                $_SESSION['WEBID_LOGGED_NUMBER']     = strspn($password, $user_data['hash']);
                $_SESSION['WEBID_LOGGED_PASS']         = $password;
                        
                // Update "last login" fields in users table
                $query = "UPDATE " . $DBPrefix . "users SET lastlogin = '" . gmdate("Y-m-d H:i:s") . "' WHERE id = " . $user_data['id'];
                $system->check_mysql(mysql_query($query), $query, __LINE__, __FILE__);
                
                ////check ip
                $query = "SELECT id FROM " . $DBPrefix . "usersips WHERE USER = " . $user_data['id'] . " AND ip = '" . $_SERVER['REMOTE_ADDR'] . "'";
                $res = mysql_query($query);
                $system->check_mysql($res, $query, __LINE__, __FILE__);
                    if (mysql_num_rows($res) == 0)
                    {
                        $query = "INSERT INTO " . $DBPrefix . "usersips VALUES
                        (NULL, '" . $user_data['id'] . "', '" . $_SERVER['REMOTE_ADDR'] . "', 'after','accept')";
                        $system->check_mysql(mysql_query($query), $query, __LINE__, __FILE__);
                    }

                // delete your old session
                if (isset($_COOKIE['WEBID_ONLINE']))
                {
                    $query = "DELETE from " . $DBPrefix . "online WHERE SESSION = '" . strip_non_an_chars($_COOKIE['WEBID_ONLINE']) . "'";
                    $system->check_mysql(mysql_query($query), $query, __LINE__, __FILE__);
                }
                
                /// Here we are unset unneeded session's
                unset($_SESSION['FBOOK_USER_EMAIL']);
                unset($_SESSION['FBOOK_USER_NAME']);
            }
        }

    }
}

//$URL = ''; 
if (isset($URL)) {
header('location: ' . $URL);
exit();
}
/////Part 3 ends here //////////
////////////////////////////////
////////////////////////////////
//// Part 4 starts here ////////
//// Post auctions to //////////
//// facebook twitter google ///
//// with the item price, //////
//// description, image ////////
//// and title. ////////////////
////////////////////////////////
///////////////////////////////////
/////////Auction price in title ///
///////////////////////////////////

//// Here we are getting the auctions prices
//// and making them in to salt
if (!empty($auction_data)) {
$buy_now_price = strip_tags($system->print_money($auction_data['buy_now']));
$bid_price = strip_tags($system->print_money($minimum_bid));

//Checking to see if the auction is a Buy Now only auction
if (isset($auction_data['buy_now']) && ($auction_data['bn_only'] == 'y'));
{
    $fb_price = " - Buy Now " . $buy_now_price;
}
//Checking the auction to see if it is a Standard Auction with a buy now botton
if (isset($min_bid) && ($auction_data['bn_only'] == 'n') && (isset($auction_data['buy_now'])))
{
    $fb_price = " - Current Bid: " . $bid_price . " or Buy Now " . $buy_now_price;
}
//Checking the auction to see if it is a Standard Auction with no buy now botton
if (isset($min_bid) && ($auction_data['bn_only'] == 'n') && ($auction_data['buy_now'] == 0))
{
    $fb_price = " - Current Bid: " . $bid_price;
}

///////////////////////////////
///////// Auction title    ////
///////////////////////////////

///// Checking to see if the auction title is set
///// if the auction title is not found it will
///// display the page title.
if (isset($auction_data['title'])) 
{
    $fb_title = $auction_data['title'];
}
else         
{
    $fb_title = $system->SETTINGS['sitename'] . $page_title;
}

/////////////////////////////////
///////// Auction Description ///
/////////////////////////////////
function shortText($string,$lenght) {
    $string = substr($string,0,$lenght).".....";
    $string_ende = strrchr($string, " ");
    $string = str_replace($string_ende," .....", $string);
    return $string;
}
if (isset($auction_data['description'])) {
            $fbdescW = strip_tags($auction_data['description']);
            $fbdescW = trim($fbdescW);
            $fb_desc = $fbdescW;
            }
            else         
            {
            $fb_desc = stripslashes($system->SETTINGS['descriptiontag']);
}

$fb_desc = shortText($fb_desc,350);

/////////////////////////////////
///////// Auction Image      ////
/////////////////////////////////
$fb_pic01 = $system->SETTINGS['siteurl'];
$fb_pic02 = $system->SETTINGS['thumb_show'];
$fb_pic03 = $uploaded_path . $auction_data['id'] . '/' . $auction_data['pict_url'];

if (empty($auction_data['pict_url'])) 
{
$fb_img = "https://ubidzz.com/themes/ubidzz/logo.gif";
}
if (isset($auction_data['pict_url'])) 
{
$fb_img = $fb_pic01."getthumb.php?w=".$fb_pic02."&fromfile=".$fb_pic03;
}

//////////////////////////////////
///////// Auction URL          ///
//////////////////////////////////
$fb_url = (empty($_SERVER['HTTPS'])) ? 'http://' : 'https://';
$fb_url .= $_SERVER['HTTP_HOST'];
$fb_url .= $_SERVER['REQUEST_URI'];

//////////////////////////////////
/////// social network array's ///
//////////////////////////////////
$template->assign_vars(array(
        'FB_TITLE' => $fb_title,
        'FB_DESC' => $fb_desc,
        'FB_IMG' => $fb_img,
        'FB_URL' => $fb_url,
        'FB_PRICE' => $fb_price,
        ));
}        
/////Part 4 ends here
?>
