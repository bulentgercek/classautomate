<!-- salon bilgileri degistirme -->
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
		$('#updateProgramForm #name').val('{$programValues["name"]}');
		/**
		 * form ismini aktarabilmek icin gizli degisken yarat
		 */
        $('#updateProgramForm').append('<input type="hidden" name="tc:updateProgram" value="updateProgramForm|post" />');
        /**
         * uzerinde degisiklik yapilmasi icin acilan kisinin kod numarasi 
         * hidden form nesnesine ataniyor
         */
        $('#updateProgramForm').prepend('<input type="hidden" name="code" value="{$programValues["code"]}" />');
        /**
         * submit kontrol degiskeni
         */
        var submitControlValue = [0];
        /**
         * program kayit formu kontrolleri
         */
        $('#updateProgramForm #submit').click(function(e) {
            $('#updateProgramForm #name').validator('validate');
            
            if (submitControlValue == '1') {
                $('#updateProgramForm').submit();
            }
        });
 
        $('#updateProgramForm #name').validator({
            format: 'alphanumeric',
            invalidEmpty: true,
            correct: function() {
                $.messages.correctMessage('updateProgramForm','vres_name');
                submitControlValue[0] = 1;
            },
            error: function() {
                $.messages.notEmptyMessage('updateProgramForm','vres_name');
            }
        });
        /**
         * isim alaninin boyutunu, maksimum harf sayisini belirle
         * ve alanin otomatik genislemesini sagla
         */
        $('#program #name').width('150px').attr("maxlength","30").autoGrowInput({
            comfortZone: 10, minWidth: 150
        });
	});
</script>
<div id="programUpdateMain">
	<div id="back">
		<p style="margin-right: 10px;">{$app_program_update_submitToProgramText}</p>
		<a id="submitBackButton" class="button editLeft" href="main.php?tab=app_program" tabindex="{$tabStart++}">{$app_program_update_submitToProgram}</a>
	</div>
	<div id="form">
		<form method="post" name="updateProgramForm" id="updateProgramForm">
			<div id="header" class="header brown bold">{$app_program_update_title}</div>

			<div id="formLine">
                <label for="name" class="requested">{$app_program_update_name}</label>
   				<input name="name" type="text" id="name" tabindex="{$tabStart++}" class="gTextInput" />
   				<div id="space"><span id="vres_name" class="requestedWarning"></span></div>
            </div>

			<div id="submitArea" class="buttons">
				<a id="submit" class="button editRight" href="javascript:" tabindex="{$tabStart++}">{$app_program_update_submitRecordLabel}</a>
			</div>
			
			<div id="clear"></div>
			
		</form>
	</div>
</div>
<!-- /ogrenci ekleme -->