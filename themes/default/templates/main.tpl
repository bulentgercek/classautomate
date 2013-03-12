<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		{$main_loginMeta}

		<title>{$main_header_fullSchoolName} : {$main_title}</title>
</head>
<style type="text/css">
		<!--
		@import url("{$themePath}css/{$main_css}");
		@import url("{$themePath}css/{$main_gplusButton_css}");
		@import url("{$themePath}css/{$main_tab_css}");
		@import url("{$themePath}css/{$main_jqueryValidator_css}");
		@import url("{$themePath}css/{$main_jqueryAlerts_css}");
		@import url("{$themePath}css/{$main_jqueryTableSorterDefault_css}");
		@import url("{$themePath}css/{$main_jqueryChosen_css}");
		@import url("http://code.jquery.com/ui/1.8.18/themes/base/jquery-ui.css");
		-->
</style>

<script type="text/javascript">
		/**
		 * Session Panel Durumu
		 *
		 * @var string
		 */
		var sessionPaneState = '{$paneState}';

		/**
		 * genel json metinleri
		 *
		 * @param strings
		 */
		var notEmpty = '{$main_notEmpty}';
		var notEmail = '{$main_notEmail}';
		var notNumber = '{$main_notNumber}';
		var notPhone = '{$main_notPhone}';
		var notSubmit = '{$main_notSubmit}';
		var thanksText = '{$main_thanks}';

</script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.8.18/jquery-ui.min.js"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryValidator_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryBlockUI_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_general_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_paneMotion_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryURLParser_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryChosen_js}"></script>
<script  type="text/javascript">
		/* Turkish initialisation for the jQuery UI date picker plugin. */
		/* Written by Izzet Emre Erkan (kara@karalamalar.net). */
		$(function($) {
				$.datepicker.regional['tr'] = {
						closeText: 'kapat',
						prevText: '&#x3c;geri',
						nextText: 'ileri&#x3e',
						currentText: 'bugün',
						monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
								'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
						monthNamesShort: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz',
								'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'],
						dayNames: ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'],
						dayNamesShort: ['Pz', 'Pt', 'Sa', 'Ça', 'Pe', 'Cu', 'Ct'],
						dayNamesMin: ['Pz', 'Pt', 'Sa', 'Ça', 'Pe', 'Cu', 'Ct'],
						weekHeader: 'Hf',
						dateFormat: 'dd.mm.yy',
						firstDay: 1,
						isRTL: false,
						showMonthAfterYear: false,
						yearSuffix: ''};
				/**
				 * datePicker icin dil ayari
				 */
				$.datepicker.setDefaults($.datepicker.regional['{$language}']);
		});
		$(function($) {
				$.timepicker.regional['tr'] = {
						hourText: 'Saat',
						minuteText: 'Dakika',
						amPmText: ['ÖÖ', 'ÖS'],
						closeButtonText: 'Tamam',
						nowButtonText: 'Şimdi',
						deselectButtonText: 'Kaldır'
				};
				/**
				 * datePicker icin dil ayari
				 */
				$.timepicker.setDefaults($.timepicker.regional['{$language}']);
		});
</script>
<body>
		{$tabStart = 1}
		<table id="mainTable">
				<tr>
						<td>{include file = $main_header_area}</td>
				</tr>
				<tr>
						<td>
								<table id="mainCenterTopTable">
										<tr>
												<td>
														<table id="mainTabTable">
																<tr>
																		<!-- tablar -->
																		{foreach from=$tabs key=k item=v}
																				{assign var=expK value="&"|explode:$k}
																				{assign var=expPersonAttr value="="|explode:$expK.1}
																				<td>
																						<div id="tabOrange">
																								<ul>
																										<!-- CSS Tabs -->
																										<li 
																												{if $currentTab == "app_person_add" || $currentTab == "app_person_update"}
																														{if $personPosition == $expPersonAttr.1}
																																id="current"
																														{/if}
																												{else}
																														{if $currentTab == $expK.0}
																																id="current"
																														{/if}
																												{/if}														
																												><a 
																														{if $currentTab != $k}
																																href="main.php?tab={$k}"
																														{/if}
																														tabindex="{$tabStart++}"><span>{$v}</span></a></li>
																								</ul>
																						</div>
																				</td>
																		{/foreach}
																		<!-- /tablar -->
																		<!-- active tab -->
																		{if $activeTab != NULL}
																				<td>
																						<div id="tabRed">
																								<ul>
																										<!-- CSS Tabs -->
																										<li id="current"><a tabindex="{$tabStart++}"><span>{$activeTab['value']}</span></a></li>
																								</ul>
																						</div>
																				</td>
																		{/if}
																		<!-- /active tab -->
																</tr>
														</table>
												</td>
												<td class="paneControl black bold">
														<a href="javascript:;" id="paneButton" class="button add">{$main_paneHeader}</a>
												</td>
										</tr>
								</table>
						</td>
				</tr>
				<tr>
						<td>
								<table id="mainCenterAreaTable">
										<tr>
												<td class="page">
														<div id="centerArea">
																<!-- uygulama alani -->
																{include file = {$main_center_area}}
																<!-- /uygulama alani -->
														</div>
												</td>
												<td id="paneDivTd" class="paneDivTd">
														<div id="paneDiv" class="shadow">
																{include file = {$main_dailypane_area}}
														</div>
												</td>
										</tr>
								</table>
						</td>
				</tr>
				<tr>
						<td>
								<table id="mainFooter">
										<tr>
												<td>{include file = {$main_footer_area}}</td>
										</tr>
								</table>
						</td>
				</tr>
		</table>
</body>
</html>