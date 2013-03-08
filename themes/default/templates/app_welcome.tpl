<div id="welcomeMain">
		<div id="header" class="header brown bold">{$app_welcome_title}</div>

		<div id="formLine">
				{$app_welcome_body}
		</div>

		<div id="formLine">
				<div class="brown bold">{$app_welcome_updateTitle} {$logs.no}</div>
				<div>{$app_welcome_updateDateTime} : {$logs['date']} {$logs['time']}</div>

		</div>

		<div id="formLine">
				<div class="brown">{$app_welcome_updateNotes}</div>
				{foreach from=$logs['updates'] key=myid item=item}
						{$myid+1}) {$item}<br>
				{/foreach}
		</div>

		<div id="formLine">
				<div class="brown">{$app_welcome_updateUserNotes}</div>
				{$logs['users']}
		</div>

		<div id="clear"></div>
</div>