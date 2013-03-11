<!-- grouping ekleme -->
<script type="text/javascript" src="{$themePath}js/{$main_listControl_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_tableControl_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryAutoGrowInput_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryTableSorter_js}"></script>
<script type="text/javascript">
		/**
		 * json metinleri
		 *
		 * @param strings
		 */
		var groupingCount = {$groupingCount};

</script>
<script type="text/javascript">
		/**
		 * ilk calistirilacaklar
		 */
		$(function() {
				$('#addGroupingForm').append('<input type="hidden" name="tc:addGrouping" value="addGroupingForm|post" />');
				/**
				 * submit kontrol degiskeni
				 */
				var submitControlValue = [0];
				/**
				 * grouping kayit formu kontrolleri
				 */
				$('#addGroupingForm #submit').click(function(e) {
						$('#addGroupingForm #name').validator('validate');

						if (submitControlValue == '1') {
								$('#addGroupingForm').submit();
						}
				});

				$('#addGroupingForm #name').validator({
						format: 'alphanumeric',
						invalidEmpty: true,
						correct: function() {
								$.messages.correctMessage('addGroupingForm', 'vres_name');
								submitControlValue[0] = 1;
						},
						error: function() {
								$.messages.notEmptyMessage('addGroupingForm', 'vres_name');
						}
				});
				/**
				 * grouping liste tablosunu tablesorter ile bagla
				 */
				if (groupingCount > 0) {
						{literal}
						$('#listTable').tablesorter({sortList: [[0, 0]]});
						{/literal}
				}
				/**
				 * grouping silme form islemleri
				 */
				$('#deleteGroupingForm').append('<input type="hidden" name="tc:deleteGrouping" value="deleteGroupingForm|post" />');
				/**
				 * uzerinde degisiklik yapilmasi icin acilan groupingin kod numarasi 
				 * hidden form nesnesine ataniyor
				 */
				$('#deleteGroupingForm').prepend('<input type="hidden" id="code" name="code" value="">');
				/**
				 * tablo uzerinde hangi satira gelindiyse
				 * o satirin islem dugmeleri gorunurluk kazaniyor
				 * aksi takdirde de gorunurluk degerleri azaliyor
				 */
				$('#listTable tbody tr').hover(
								function() {
										indexValue = $(this).index();
										$('#listTable tbody tr:eq(' + indexValue + ') > td:last [id^="processEdit"]').fadeTo("fast", 1);
										$('#listTable tbody tr:eq(' + indexValue + ') > td:last [id^="processDelete"]').fadeTo("fast", 1);
								},
								function() {
										$('#listTable tbody tr:eq(' + indexValue + ') > td:last [id^="processEdit"]').fadeTo("fast", .5);
										$('#listTable tbody tr:eq(' + indexValue + ') > td:last [id^="processDelete"]').fadeTo("fast", .5);
								});
				/**
				 * grouping kayit formu kontrolleri
				 * diyalog ekrani ile onay
				 */
				$('[id^="processDelete"]').click(function() {
						$('#deleteGroupingForm #code').val($(this).attr('name').split('_')[1]);
						$("#dialog").dialog({
								title: "Onay",
								resizable: false,
								height: 140,
								modal: true,
								buttons: {
										"{$main_delete}": function() {
												$('#deleteGroupingForm').submit();
										},
										"{$main_cancel}": function() {
												$(this).dialog("close");
										}
								}
						});
				});
				/**
				 * isim alaninin boyutunu, maksimum harf sayisini belirle
				 * ve alanin otomatik genislemesini sagla
				 */
				$('#groupingMain #name').width('150px').attr("maxlength", "30").autoGrowInput({
						comfortZone: 10, minWidth: 150
				});
		});
</script>
<div id="groupingMain">
		<div id="form">
				<form method="post" name="addGroupingForm" id="addGroupingForm">
						<div id="header" class="header brown bold">{$app_grouping_addNewGroupingTitle}</div>

						<div id="formLine">
                <label for="name" class="requested">{$app_grouping_name}</label>
								<input name="name" type="text" id="name" tabindex="{$tabStart++}" class="gTextInput" />
								<div id="space"><span id="vres_name" class="requestedWarning"></span></div>
            </div>

						<div id="submitArea" class="buttons">
								<a id="submit" class="button add" href="javascript:" tabindex="{$tabStart++}">{$app_grouping_submitRecordLabel}</a>
						</div>

						<div id="clear"></div>

				</form>
		</div>
		<div id="list">
				<div id="header" class="lowHeader brown bold">{$app_grouping_groupingsListTitle}</div>
				<form method="post" name="deleteGroupingForm" id="deleteGroupingForm">
						<table id="listTable" class="tablesorter">
								<thead>
										<tr>
												<th scope="col">{$app_grouping_groupingsListGroupingName}</th>
												<th scope="col">{$app_grouping_groupingsListUsage}</th>
												<th scope="col">{$app_grouping_groupingsListProcess}</th>
										</tr>
								</thead>
								<tfoot>
										<tr>
												<td colspan="3"><em><span id="warningTextArea">{$app_grouping_groupingsListCount} : {$groupingListArray|@count}</span></em></td>
										</tr>
								</tfoot>
								<tbody>
										{foreach from=$groupingListArray key=myid item=item}
												<tr>
														<td>{$item.name}</td>
														<td>{$item.status} {$app_grouping_groupingsListUsageObject}</td>
														<td class="columnProcess" style="width:30px">
																{if $item.process == 0}
																		<div class="buttons"><a id="processEdit_{$item.code}" class="button editLeft alphaQuarter" href="main.php?tab=app_grouping_update&amp;code={$item.code}">&nbsp;</a></div>
																{elseif $item.process == 1}
																		<div class="buttons"><a id="processEdit_{$item.code}" class="button editLeft alphaQuarter" href="main.php?tab=app_grouping_update&amp;code={$item.code}">&nbsp;</a></div>
																		<div class="buttons"><a name="processDelete_{$item.code}" id="processDelete_{$item.code}" class="button delete alphaQuarter" href="javascript:">&nbsp;</a></div>
																		<div style="display:none" id="dialog">{$app_grouping_sureToDelete}</div>
																{/if}                                        
														</td>
												</tr>
										{/foreach}
                </tbody>
						</table>
				</form>
		</div>	
</div>
<!-- /grouping ekleme -->