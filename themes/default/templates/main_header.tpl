<!-- main_header -->
<script type="text/javascript">
		/**
		 * ilk calistirilacaklar
		 */
		$(function() {
				/**
				 * session'a alınan secim sınıf listesine gönderiliyor
				 */
				$('#code option[value="{$classroomSelection}"]').attr('selected', 'selected');
		});
</script>
<table id="mainHeader" summary="Company Header">
		<tr class="top">
				<td><a href="main.php?tab=app_welcome" tabindex="{$tabStart++}" class="black header noDecor">{$main_header_fullSchoolName}</a><span class="black"> | {$main_header_programTitle} {$main_header_version}</span> {$main_header_mainDate['full']} | {$main_header_mainDate['dayOfWeek']}</td>
		</tr>
</table>
<table id="mainGAccessTable">
		<tr>
				<td>
						<form method="get" name="classListForm" id="classListForm">
								<input name="tab" type="hidden" value="app_content" />
								<label for="code" class="brown bold size14">{$main_header_classListLabel}</label>
								<select id="code" name="code" tabindex="{$tabStart++}" class="gSelect">
										<!-- sinif listesi -->
										{foreach from=$classList key=k item=v}
												<option value="{$k}">{$v}</option>
										{/foreach}
										<!-- /sinif listesi -->
								{if count($classList)>0}<option value="splitter" disabled="disabled">- - - - - - - - - - - - - - - - -</option>{/if}
								<option value="sbyRoom">{$main_header_sbyRoom}</option>
						</select>
						<!-- siralama -->
						<input name="orderby" type="hidden" value="name ASC" />
						<!-- /siralama -->
						</span>
				</form>
		</td>
		<td>
				<div id="listDiv">
						<a id="classListFormSubmit" class="button" tabindex="{$tabStart++}" href="#">{$main_header_classListSubmitLabel}</a>
				</div>
		</td>
		<td>
				<form method="get" name="searchForm" id="searchForm">
						<input name="tab" type="hidden" value="app_person_search" />
						<label for="search" class="brown bold size14">{$main_header_searchLabel}</label>
						<input id="search" name="search" type="text" tabindex="{$tabStart++}" size="24" class="gTextInput" />
						<span class="brownText14"></span>
				</form>
		</td>
		<td>
				<div id="searchDiv">
						<a id="searchFormSubmit" class="button" tabindex="{$tabStart++}" href="#">{$main_header_searchSubmitLabel}</a>
				</div>
		</td>
		<td>
				<div class="buttons">
						<a href="main.php?tab=app_main" tabindex="{$tabStart++}" class="button"><b>{$main_header_appsMainLink}</b></a>
				</div>
		</td>
</tr>
</table>
<!-- /main_header -->