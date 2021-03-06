<script type="text/javascript">
window.fbAsyncInit = function() {
	FB.init({
	appId      : '{FBOOK_APPID}',
	status     : true, 
	cookie     : true, 
	xfbml      : true  
	});
};
(function(d){
	var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement('script'); js.id = id; js.async = true;
	js.src = "//connect.facebook.net/en_US/all.js";
	ref.parentNode.insertBefore(js, ref);
}(document));

function FBLogin(){
	FB.login(function(response){
		if(response.authResponse){
			window.location.href = "edit_data.php?fbconnect=fblogin";
		}
	}, {scope: 'email,user_likes'});
}

</script>
<!-- INCLUDE user_menu_header.tpl -->

<form name="details" action="" method="post">
<input type="hidden" name="csrftoken" value="{_CSRFTOKEN}">
<input type="hidden" name="FB_ids" value="{FBOOK_ID}">
<table width="100%" border="0" cellpadding="4" align="center">
	<tr>
		<td align="right" width="30%">{L_002}</td>
		<td><i>{NAME}</i></td>
	</tr>
	<tr>
		<td align="right" valign="top">{L_003}</td>
		<td valign="top"><i>{NICK}</i></td>
	</tr>
	<tr>
		<th colspan="2" valign="top" align="center">{L_617}</td>
	</tr>
	<tr>
		<td valign="top" class="errfont" style="text-align:right !important;">{L_004}</td>
		<td align="left"><input type="password" name="TPL_password" size=20 maxlength="20"> {L_050}</td>
	</tr>
	<tr>
		<td valign="top" class="errfont" style="text-align:right !important;">{L_005}</td>
		<td align="left">
			<input type="password" name="TPL_repeat_password" size=20 maxlength=20 />
		</td>
	</tr>
	<tr>
		<td valign="top" align="right">{L_006}</td>
		<td>
			<input type="email" name="TPL_email" size=50 maxlength=50 value="{EMAIL}">
		</td>
	</tr>
	<tr>
		<td valign="top" align="right">{L_252}</td>
		<td>
			{DATEFORMAT} <input type="text" name="TPL_year" size="4" maxlength="4" value="{YEAR}">
		</td>
	</tr>
	<tr>
		<td valign="top" align="right">{L_009}</td>
		<td>
			<input type="text" name="TPL_address" size=40 maxlength=255 value="{ADDRESS}">
		</td>
	</tr>
	<tr>
		<td valign="top" align="right">{L_010}</td>
		<td>
			<input type="text" name="TPL_city" size=25 maxlength=25 value="{CITY}">
		</td>
	</tr>
	<tr>
		<td valign="top" align="right">{L_011}</td>
		<td>
			<input type="text" name="TPL_prov" size=10 maxlength=10 value="{PROV}">
		</td>
	</tr>
	<tr>
		<td valign="top" align="right">{L_014}</td>
		<td>
			<select name="TPL_country">
				{COUNTRYLIST}
			</select>
		</td>
	</tr>
	<tr>
		<td valign="top" align="right">{L_012}</td>
		<td>
			<input type="text" name="TPL_zip" size=8 value="{ZIP}">
		</td>
	</tr>
	<tr>
		<td valign="top" align="right">{L_013}</td>
		<td>
			<input type="text" name="TPL_phone" size=40 maxlength=40 value="{PHONE}">
		</td>
	</tr>
	<tr>
		<td valign="top" align="right">{L_346}</td>
		<td>
			{TIMEZONE}
		</td>
	</tr>
	<tr>
		<td valign="top" align="right">{L_352}</td>
		<td>
			<input type="radio" name="TPL_emailtype" value="html" {EMAILTYPE1} />
			{L_902}
			<input type="radio" name="TPL_emailtype" value="text" {EMAILTYPE2} />
			{L_915}
		</td>
	</tr>
	<tr>
<!-- IF B_NEWLETTER -->
		<td align="right" height="2">{L_603}<td>
			<input type="radio" name="TPL_nletter" value="1" {NLETTER1} />
			{L_030}
			<input type="radio" name="TPL_nletter" value="2" {NLETTER2} />
			{L_029}<br><span class="smallspan"><i>{L_609}</i></span>
		</td>
	</tr>
<!-- ENDIF -->
<!-- IF B_FBOOK_LOGIN -->
<div>{FBOOK_EMAIL}</div>
<!-- ENDIF -->
</table>

<div class="padding">
	<h2>{L_719}</h2>
</div>

<table width="100%" border="0" cellpadding="4" align="center">
<!-- IF B_PAYPAL -->
	<tr>
		<td align="right" width="30%">{L_720}</td>
		<td>
			<input type="text" name="TPL_pp_email" size=40 value="{PP_EMAIL}">
		</td>
	</tr>
<!-- ENDIF -->
<!-- IF B_AUTHNET -->
	<tr>
		<td align="right" width="30%">{L_773}</td>
		<td>
			<input type="text" name="TPL_authnet_id" size=40 value="{AN_ID}">
		</td>
	</tr>
	<tr>
		<td align="right" width="30%">{L_774}</td>
		<td>
			<input type="text" name="TPL_authnet_pass" size=40 value="{AN_PASS}">
		</td>
	</tr>
<!-- ENDIF -->
<!-- IF B_WORLDPAY -->
	<tr>
		<td align="right" width="30%">{L_824}</td>
		<td>
			<input type="text" name="TPL_worldpay_id" size=40 value="{WP_ID}">
		</td>
	</tr>
<!-- ENDIF -->
<!-- IF B_TOOCHECKOUT -->
	<tr>
		<td align="right" width="30%">{L_826}</td>
		<td>
			<input type="text" name="TPL_toocheckout_id" size=40 value="{TC_ID}">
		</td>
	</tr>
<!-- ENDIF -->
<!-- IF B_MONEYBOOKERS -->
	<tr>
		<td align="right" width="30%">{L_825}</td>
		<td>
			<input type="text" name="TPL_moneybookers_email" size=40 value="{MB_EMAIL}">
		</td>
	</tr>
<!-- ENDIF -->
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan=2 align="center">
			<input type="submit" name="Input" value="{L_530}" class="button">
			<input type="reset" name="Input" class="button">
		</td>
	</tr>
</table>
<input type="hidden" name="action" value="update">
</form>

<!-- INCLUDE user_menu_footer.tpl -->
