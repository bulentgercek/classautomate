<!-- grouping bilgileri degistirme -->
<script type="text/javascript" src="{$themePath}js/{$main_listControl_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_tableControl_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryAutoGrowInput_js}"></script>
<script type="text/javascript">
		/**
		 * json metinleri
		 *
		 * @param strings
		 */

</script>
<script type="text/javascript">
		/**
		 * ilk calistirilacaklar
		 */
		$(function() {
				/**
				 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
				 * update bilgilerine gore formun ici dolduruluyor
				 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
				 */
				$('#updateGroupingForm #name').val('{$groupingValues["name"]}');
				/**
				 * form ismini aktarabilmek icin gizli degisken yarat
				 */
				$('#updateGroupingForm').append('<input type="hidden" name="tc:updateGrouping" value="updateGroupingForm|post" />');
				/**
				 * uzerinde degisiklik yapilmasi icin acilan kisinin kod numarasi 
				 * hidden form nesnesine ataniyor
				 */
				$('#updateGroupingForm').prepend('<input type="hidden" name="code" value="{$groupingValues["code"]}" />');
				/**
				 * submit kontrol degiskeni
				 */
				var submitControlValue = [0];
				/**
				 * grouping kayit formu kontrolleri
				 */
				$('#updateGroupingForm #submit').click(function(e) {
						$('#updateGroupingForm #name').validator('validate');

						if (submitControlValue == '1') {
								$('#updateGroupingForm').submit();
						}
				});

				$('#updateGroupingForm #name').validator({
						format: 'alphanumeric',
						invalidEmpty: true,
						correct: function() {
								$.messages.correctMessage('updateGroupingForm', 'vres_name');
								submitControlValue[0] = 1;
						},
						error: function() {
								$.messages.notEmptyMessage('updateGroupingForm', 'vres_name');
						}
				});
				/**
				 * isim alaninin boyutunu, maksimum harf sayisini belirle
				 * ve alanin otomatik genislemesini sagla
				 */
				$('#grouping #name').width('150px').attr("maxlength", "30").autoGrowInput({
						comfortZone: 10, minWidth: 150
				});
		});
</script>
<div id="groupingUpdateMain">
		<div id="back">
				<p style="margin-right: 10px;">{$app_grouping_update_submitToGroupingText}</p>
				<a id="submitBackButton" class="button editLeft" href="main.php?tab=app_grouping" tabindex="{$tabStart++}">{$app_grouping_update_submitToGrouping}</a>
		</div>
		<div id="form">
				<form method="post" name="updateGroupingForm" id="updateGroupingForm">
						<div id="header" class="header brown bold">{$app_grouping_update_title}</div>

						<div id="formLine">
								<label for="name" class="requested">{$app_grouping_update_name}</label>
								<input name="name" type="text" id="name" tabindex="{$tabStart++}" class="gTextInput" />
								<div id="space"><span id="vres_name" class="requestedWarning"></span></div>
						</div>

						<div id="submitArea" class="buttons">
								<a id="submit" class="button editRight" href="javascript:" tabindex="{$tabStart++}">{$app_grouping_update_submitRecordLabel}</a>
						</div>

						<div id="clear"></div>

				</form>
		</div>
</div>
<!-- /ogrenci ekleme -->