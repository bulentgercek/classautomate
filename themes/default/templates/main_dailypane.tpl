<!-- gunluk isler paneli -->

<table width="100%">
		<tr>
				<td style="border: 0px solid rgb(136, 136, 136);" width="168" align="left" valign="top">
						<table width="100%" border="0" cellspacing="5" cellpadding="5">
								<tr>
										<td height="29">
												<div align="center" class="red bold">
														{$main_dailypane_daily_class_header}
												</div>
										</td>
								</tr>
								<tr>
										<td>
												<div align="center">
														{foreach from=$panelClassroomList key=k item=item}
																<a href="{if $item.lectureStatus eq 'on'}{$item.url}{else}main.php?tab=app_rollcall&classroom=all&dayTime=&date=now{/if}" class="grayBlue noDecor">
																		{$item.name}<br>({$item.startTime}-{$item.endTime})
																</a><br><br>
														{/foreach}
												</div>
										</td>
								</tr>
						</table>
				</td>
		</tr>
</table>
<br><br>

<!-- /gunluk isler paneli -->
