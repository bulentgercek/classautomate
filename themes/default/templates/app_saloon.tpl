<!-- saloon ekleme -->
<script type="text/javascript" src="{$themePath}js/{$main_listControl_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_tableControl_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryAlert_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryAutoGrowTextArea_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryAutoGrowInput_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryTableSorter_js}"></script>
<script type="text/javascript">
    /**
     * json metinleri
     *
     * @param strings
     */
    var saloonCount = {$saloonCount};

</script>
<script type="text/javascript">
	/**
	 * ilk calistirilacaklar
	 */
	$(function() {
        /**
	     * yeni salon ekleme form islemleri
	     */
	    $('#addSaloonForm').append('<input type="hidden" name="tc:addSaloon" value="addSaloonForm|post" />');
	    /**
	     * submit kontrol degiskeni
	     */
	    var submitControlValue = [0];
	    /**
	     * saloon kayit formu kontrolleri
	     */
	    $('#addSaloonForm #submit').click(function(e) {
	       
	        $('#addSaloonForm #name').validator('validate');
	           
	        if (submitControlValue == '1') {
	            $('#addSaloonForm').submit();
	        }
	    });
	            
	    $('#addSaloonForm #name').validator({
	        format: 'alphanumeric',
	        invalidEmpty: true,
	        minLength:1,
	        correct: function() { 
	            $.messages.correctMessage('addSaloonForm','vres_name');
	            submitControlValue[0] = 1;
	        },
	        error: function() {
	            $.messages.notEmptyMessage('addSaloonForm','vres_name');
	        }
	    });
        /**
         * salon liste tablosunu tablesorter ile bagla
         */
        if (saloonCount != "") {
	        {literal}
	            $("#listTable").tablesorter( {sortList: [[0,0]]} );
	        {/literal}
        }
       /**
        * salon silme form islemleri
        */
        $('#deleteSaloonForm').append('<input type="hidden" name="tc:deleteSaloon" value="deleteSaloonForm|post" />');
       /**
        * uzerinde degisiklik yapilmasi icin acilan kisinin kod numarasi 
        * hidden form nesnesine ataniyor
        */
        $('#deleteSaloonForm').prepend('<input type="hidden" id="code" name="code" value="">');
        /**
         * tablo uzerinde hangi satira gelindiyse
         * o satirin islem dugmeleri gorunurluk kazaniyor
         * aksi takdirde de gorunurluk degerleri azaliyor
         */
        $('#listTable tbody tr').hover(
            function() {
                indexValue = $(this).index();
                $('#listTable tbody tr:eq('+indexValue+') > td:last [id^="processEdit"]').fadeTo("fast", 1);
                $('#listTable tbody tr:eq('+indexValue+') > td:last [id^="processDelete"]').fadeTo("fast", 1);
            },
            function() {
                $('#listTable tbody tr:eq('+indexValue+') > td:last [id^="processEdit"]').fadeTo("fast", .5);
                $('#listTable tbody tr:eq('+indexValue+') > td:last [id^="processDelete"]').fadeTo("fast", .5);
        });
       /**
        * saloon kayit formu kontrolleri
        * diyalog ekrani ile onay
        */       
        $('[id^="processDelete"]').click(function() {
            $('#deleteSaloonForm #code').val( $(this).attr('name').split('_')[1] );
            $( "#dialog" ).dialog({
                title:"Onay",
                resizable: false,
                height:140,
                modal: true,
                buttons: {
                    "{$app_saloon_delete}": function() {
                        $('#deleteSaloonForm').submit();
                    },
                    "{$app_saloon_cancel}": function() {
                        $( this ).dialog( "close" );
                        }
                    }
            });       
        });
        /**
         * salon adres alaninin otomatik genislemesini sagla
         */
        $("#saloonMain #address").autoGrow();
        /**
         * isim alaninin boyutunu, maksimum harf sayisini belirle
         * ve alanin otomatik genislemesini sagla
         */
        $('#saloonMain #name').width('150px').attr("maxlength","30").autoGrowInput({
            comfortZone: 10, minWidth: 150
        });
	});

</script>
<div id="saloonMain">
	<div id="form">
		<form method="post" name="addSaloonForm" id="addSaloonForm">
			<div id="header" class="header brown bold">{$app_saloon_addNewSaloonTitle}</div>

			<div id="formLine">
                <label for="name" class="requested">{$app_saloon_name}</label>
   				<input name="name" type="text" id="name" tabindex="{$tabStart++}" class="gTextInput" />
   				<div id="space"><span id="vres_name" class="requestedWarning"></span></div>
            </div>
 
            <div id="formLine">
                <label for="address">{$app_saloon_address}</label>
                <textarea name="address" type="text" id="address" tabindex="{$tabStart++}" class="gTextInput" /></textarea>
                <div id="space"></div>
            </div>

			<div id="submitArea" class="buttons">
				<a id="submit" class="button add" href="javascript:" tabindex="{$tabStart++}">{$app_saloon_submitRecordLabel}</a>
			</div>
			
			<div id="clear"></div>
			
		</form>
	</div>
	<div id="list">
		<div id="header" class="header brown bold">{$app_saloon_saloonsListTitle}</div>
		<form method="post" name="deleteSaloonForm" id="deleteSaloonForm">
			<table id="listTable" class="tablesorter">
				<thead>
					<tr>
						<th scope="col">{$app_saloon_saloonsListSaloonName}</th>
						<th scope="col">{$app_saloon_saloonsListSaloonAddress}</th>
						<th scope="col">{$app_saloon_saloonsListUsage}</th>
						<th scope="col">{$app_saloon_saloonsListProcess}</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="4"><em><span id="warningTextArea">{$app_saloon_saloonsListCount} : {$saloonListArray|@count}</span></em></td>
					</tr>
				</tfoot>
				<tbody>
					{foreach from=$saloonListArray key=myid item=item}
					<tr>
						<td>{$item.name}</td>
						<td style="max-width: 150px">{$item.address}</td>
						<td>{$item.status} {$app_saloon_saloonsListUsageObject}</td>
						<td class="columnProcess" style="width:30px">
							{if $item.process == 0}
							<div class="buttons"><a id="processEdit_{$item.code}" class="button editLeft alphaQuarter" href="main.php?tab=app_saloon_update&amp;code={$item.code}">&nbsp;</a></div>
							{elseif $item.process == 1}
							<div class="buttons"><a id="processEdit_{$item.code}" class="button editLeft alphaQuarter" href="main.php?tab=app_saloon_update&amp;code={$item.code}">&nbsp;</a></div>
							<div class="buttons"><a name="processDelete_{$item.code}" id="processDelete_{$item.code}" class="button delete alphaQuarter" href="javascript:">&nbsp;</a></div>
                            <div style="display:none" id="dialog">{$app_saloon_sureToDelete}</div>
							{/if}                                        
						</td>
					</tr>
					{/foreach}
                </tbody>
			</table>
		</form>
	</div>
</div>
<!-- /saloon ekleme -->