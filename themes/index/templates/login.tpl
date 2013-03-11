
<!--
 * classautomate / login table
 *
 --> 
<script language="JavaScript">
	<!-- formu gonder ve hangi link'e basildigini ogren -->
    function checkButton(id) {
		document.getElementById("login_formButton").value = id;
        submitLoginForm();
    }
	<!-- return tusuna basildi mi? -->
	function checkSubmit(e) {
		if(e && e.keyCode == 13) {
			document.getElementById("login_formButton").value = 'submit';
			submitLoginForm();
		}
	}
	<!-- login formunu gonder -->
	function submitLoginForm() {
		document.forms.login_form.submit();
	}
</script>

<form name="login_form" method="post">
	<table id="login-table" summary="Login Table">
	
		<thead>
			<tr>
				<th scope="col">{$login_header}</th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td>
					<div id="login_ask" style="display:{$login_ask}" onKeyPress="return checkSubmit(event)">
						<table id="askArea">
							<tr>
							  <td class="username">{$login_username} </td>
							  <td class="usernameInput"><input name="login_username" type="text" id="login_username" tabindex="1" class="gTextInput" value="{$login_formUsername}" /></td>
							</tr>
							<tr>
							  <td class="password">{$login_password} </td>
							  <td class="passwordInput"><input name="login_password" type="password" id="login_password" tabindex="2" class="gTextInput" on/></td>
							</tr>
						</table>
                     <input type="hidden" value="" id="login_formButton" name="login_formButton">
					</div>
				</td>
			</tr>
			<tr>
				<td class="infoArea">{$login_info}</td>
			</tr>
			<!--
			 * classautomate / timeZone
			 * Put user local time difference into the submit form
			 *
			 --> 
			<script type="text/javascript">
			tzo = - new Date().getTimezoneOffset()/60;
			document.write('<input type="hidden" value="'+tzo+'" name="timeZoneOffset">');
		</script>
			<tr>
				 <td>
					<div id="login_askSubmit" style="display:{if $login_ask eq "inline"}inline{else}none{/if}">
						<a href="#" name="submit" class="button" onclick="checkButton(this.name);return(FALSE);">{$login_submitButton}</a>
					</div>
					<div id="login_askEnter" style="display:{if $login_ask eq "none"}inline{else}none{/if}">
						<a href="#" name="enter" class="button" onclick="checkButton(this.name);return(FALSE);">{$login_enterButton}</a>
						<a href="#" name="logout" class="button" onclick="checkButton(this.name);return(FALSE);">{$login_logoutButton}</a>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
</form>
