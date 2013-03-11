<!-- program ekleme -->
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
    var programCount = {$programCount};
    
</script>
<script type="text/javascript">
	/**
	 * ilk calistirilacaklar
	 */
	$(function() {
		$('#addProgramForm').append('<input type="hidden" name="tc:addProgram" value="addProgramForm|post" />');
		/**
		 * submit kontrol degiskeni
		 */
		var submitControlValue = [0];
		/**
		 * program kayit formu kontrolleri
		 */
		$('#addProgramForm #submit').click(function(e) {
			$('#addProgramForm #name').validator('validate');
	
			if (submitControlValue == '1') {
				$('#addProgramForm').submit();
			}
		});
				
		$('#addProgramForm #name').validator({
			format: 'alphanumeric',
			invalidEmpty: true,
			correct: function() {
				$.messages.correctMessage('addProgramForm','vres_name');
				submitControlValue[0] = 1;
			},
			error: function() {
				$.messages.notEmptyMessage('addProgramForm','vres_name');
			}
		});
        /**
         * program liste tablosunu tablesorter ile bagla
         */
        if (programCount > 0) {
	        {literal}
	            $('#listTable').tablesorter( {sortList: [[0,0]]} );
	        {/literal}
        }
       /**
        * program silme form islemleri
        */
        $('#deleteProgramForm').append('<input type="hidden" name="tc:deleteProgram" value="deleteProgramForm|post" />');
       /**
        * uzerinde degisiklik yapilmasi icin acilan programin kod numarasi 
        * hidden form nesnesine ataniyor
        */
        $('#deleteProgramForm').prepend('<input type="hidden" id="code" name="code" value="">');
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
        * program kayit formu kontrolleri
        * diyalog ekrani ile onay
        */       
        $('[id^="processDelete"]').click(function() {
            $('#deleteProgramForm #code').val( $(this).attr('name').split('_')[1] );
            $( "#dialog" ).dialog({
                title:"Onay",
                resizable: false,
                height:140,
                modal: true,
                buttons: {
                    "{$app_program_delete}": function() {
                        $('#deleteProgramForm').submit();
                    },
                    "{$app_program_cancel}": function() {
                        $( this ).dialog( "close" );
                        }
                    }
            });       
        });
        /**
         * isim alaninin boyutunu, maksimum harf sayisini belirle
         * ve alanin otomatik genislemesini sagla
         */
        $('#programMain #name').width('150px').attr("maxlength","30").autoGrowInput({
            comfortZone: 10, minWidth: 150
        });
	});
</script>
<div id="programMain">
	<div id="form">
		<form method="post" name="addProgramForm" id="addProgramForm">
			<div id="header" class="header brown bold">{$app_program_addNewProgramTitle}</div>

			<div id="formLine">
                <label for="name" class="requested">{$app_program_name}</label>
   				<input name="name" type="text" id="name" tabindex="{$tabStart++}" class="gTextInput" />
   				<div id="space"><span id="vres_name" class="requestedWarning"></span></div>
            </div>

			<div id="submitArea" class="buttons">
				<a id="submit" class="button add" href="javascript:" tabindex="{$tabStart++}">{$app_program_submitRecordLabel}</a>
			</div>
			
			<div id="clear"></div>
			
		</form>
	</div>
	<div id="list">
		<div id="header" class="lowHeader brown bold">{$app_program_programsListTitle}</div>
		<form method="post" name="deleteProgramForm" id="deleteProgramForm">
			<table id="listTable" class="tablesorter">
				<thead>
					<tr>
						<th scope="col">{$app_program_programsListProgramName}</th>
						<th scope="col">{$app_program_programsListUsage}</th>
						<th scope="col">{$app_program_programsListProcess}</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="3"><em><span id="warningTextArea">{$app_program_programsListCount} : {$programListArray|@count}</span></em></td>
					</tr>
				</tfoot>
				<tbody>
					{foreach from=$programListArray key=myid item=item}
					<tr>
						<td>{$item.name}</td>
						<td>{$item.status} {$app_program_programsListUsageObject}</td>
						<td class="columnProcess" style="width:30px">
							{if $item.process == 0}
							<div class="buttons"><a id="processEdit_{$item.code}" class="button editLeft alphaQuarter" href="main.php?tab=app_program_update&amp;code={$item.code}">&nbsp;</a></div>
							{elseif $item.process == 1}
							<div class="buttons"><a id="processEdit_{$item.code}" class="button editLeft alphaQuarter" href="main.php?tab=app_program_update&amp;code={$item.code}">&nbsp;</a></div>
							<div class="buttons"><a name="processDelete_{$item.code}" id="processDelete_{$item.code}" class="button delete alphaQuarter" href="javascript:">&nbsp;</a></div>
                            <div style="display:none" id="dialog">{$app_program_sureToDelete}</div>
							{/if}                                        
						</td>
					</tr>
					{/foreach}
                </tbody>
			</table>
		</form>
	</div>	
</div>
<!-- /program ekleme -->