<!-- salon duzenle -->
<script type="text/javascript" src="{$themePath}js/{$main_listControl_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_tableControl_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryAutoGrowTextArea_js}"></script>
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
		$('#updateSaloonForm #name').val('{$saloonValues["name"]}');
		$('#updateSaloonForm #address').val('{$saloonValues["address"]}');
		/**
		 * form ismini aktarabilmek icin gizli degisken yarat
		 */
        $('#updateSaloonForm').append('<input type="hidden" name="tc:updateSaloon" value="updateSaloonForm|post" />');
        /**
         * uzerinde degisiklik yapilmasi icin acilan kisinin kod numarasi 
         * hidden form nesnesine ataniyor
         */
        $('#updateSaloonForm').prepend('<input type="hidden" name="code" value="{$saloonValues["code"]}" />');
        /**
         * submit kontrol degiskeni
         */
        var submitControlValue = [0];
        /**
         * saloon kayit formu kontrolleri
         */
        $('#updateSaloonForm #submit').click(function(e) {
            $('#updateSaloonForm #name').validator('validate');
            
            if (submitControlValue == '1') {
                $('#updateSaloonForm').submit();
            }
        });
                
        $('#updateSaloonForm #name').validator({
            format: 'alphanumeric',
            invalidEmpty: true,
            correct: function() {
                $.messages.correctMessage('updateSaloonForm','vres_name');
                submitControlValue[0] = 1;
            },
            error: function() {
                $.messages.notEmptyMessage('updateSaloonForm','vres_name');
            }
        });
        /**
         * salon adres alaninin otomatik genislemesini sagla
         */
        $("#saloon #address").autoGrow();
        /**
         * isim alaninin boyutunu, maksimum harf sayisini belirle
         * ve alanin otomatik genislemesini sagla
         */
        $('#saloon #name').width('150px').attr("maxlength","30").autoGrowInput({
            comfortZone: 10, minWidth: 150
        });
	});
</script>
<div id="saloonUpdateMain">
	<div id="back">
		<p style="margin-right: 10px;">{$app_saloon_update_submitToSaloonText}</p>
		<a id="submitBackButton" class="button editLeft" href="main.php?tab=app_saloon" tabindex="{$tabStart++}">{$app_saloon_update_submitToSaloon}</a>
	</div>
	<div id="form">
		<form method="post" name="updateSaloonForm" id="updateSaloonForm">
			<div id="header" class="header brown bold">{$app_saloon_update_title}</div>

			<div id="formLine">
                <label for="name" class="requested">{$app_saloon_update_name}</label>
   				<input name="name" type="text" id="name" tabindex="{$tabStart++}" class="gTextInput" />
   				<div id="space"><span id="vres_name" class="requestedWarning"></span></div>
            </div>
 
            <div id="formLine">
                <label for="address">{$app_saloon_update_address}</label>
                <textarea name="address" type="text" id="address" tabindex="{$tabStart++}" class="gTextInput" /></textarea>
                <div id="space"></div>
            </div>

			<div id="submitArea" class="buttons">
				<a id="submit" class="button editRight" href="javascript:" tabindex="{$tabStart++}">{$app_saloon_update_submitRecordLabel}</a>
			</div>
			
			<div id="clear"></div>
			
		</form>
	</div>
</div>
<!-- /salon duzenle -->