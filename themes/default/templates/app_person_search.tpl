<!-- kişi arama sonucu listele -->
<script type="text/javascript" src="{$themePath}js/{$main_tableControl_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryTableSorter_js}"></script>
<script type="text/javascript">
	/**
	 * json metinleri
	 *
	 * @param strings
	 */
	var searchResults = '{$app_person_search_searchResults}';
	var studentLang = '{$app_person_search_positionStudent}';
	var instructorLang = '{$app_person_search_positionInstructor}';
	var asistantLang = '{$app_person_search_positionAsistant}';
	var secretaryLang = '{$app_person_search_positionSecretary}';
	var cleanerLang = '{$app_person_search_positionCleaner}';
    var peopleCount = {$peopleCount};

</script>
<script type="text/javascript">
	/**
	 * ilk calistirilacaklar
	 */
	$(function() {
		if (peopleCount > 0) {
	        {literal}
	            $('#listTable').tablesorter( {sortList: [[0,0]]} );
	        {/literal}
		}
       /**
        * kisi silme form islemleri
        */
        $('#deletePersonForm').append('<input type="hidden" name="tc:deletePerson" value="deletePersonForm|post" />');
       /**
        * uzerinde degisiklik yapilmasi icin acilan kisinin kod numarasi 
        * hidden form nesnesine ataniyor
        */
        $('#deletePersonForm').prepend('<input type="hidden" id="code" name="code" value="">');
        /**
         * kisinin pozisyonunu gizli degiskene aktar 
         */
        $('#deletePersonForm').append('<input type="hidden" id="position" name="position" value="" />');
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
        * kisi arama sonucu formu kontrolleri
        * diyalog ekrani ile onay
        */       
        $('[id^="processDelete"]').click(function() {
            $('#deletePersonForm #code').val( $(this).attr('name').split('_')[1] );
            $('#deletePersonForm #position').val( $(this).attr('name').split('_')[2] );

            $( "#dialog" ).dialog({
                title:"Onay",
                resizable: false,
                height:140,
                modal: true,
                buttons: {
                    "{$app_person_search_delete}": function() {
                        $('#deletePersonForm').submit();
                    },
                    "{$app_person_search_cancel}": function() {
                        $( this ).dialog( "close" );
                        }
                    }
            });
  
        });
	});
</script>
<div id="personSearch">
    <div id="list">
        <div id="header" class="header brown bold">{$app_person_search_title} - '{$search}' {$app_person_search_searchResults}</div>
		<form method="post" name="deletePersonForm" id="deletePersonForm">
			<table id="listTable" class="tablesorter">
				<thead>
					<tr>
						<th scope="col">{$app_person_search_nameSurname}</th>
						<th scope="col">{$app_person_search_position}</th>
						<th scope="col">{$app_person_search_class}</th>
						<th scope="col">{$app_person_search_listProcess}</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="4"><em><span id="warningTextArea">'{$search}'  {$app_person_search_searchResults} {count($personSearchResultArray)}</span></em></td>
					</tr>
				</tfoot>
				<tbody>
                    {foreach from=$personSearchResultArray key=myid item=item}
                    <tr>
                    	<td>{$item.name} {$item.surname}</td>
                    	<td>
                    	    {if $item.position == "student"}{$app_person_search_positionStudent}
                    	    {elseif $item.position == "instructor"}{$app_person_search_positionInstructor}
                    	    {elseif $item.position == "asistant"}{$app_person_search_positionAsistant}
                    	    {elseif $item.position == "secretary"}{$app_person_search_positionSecretary}
                    	    {elseif $item.position == "cleaner"}{$app_person_search_positionCleaner}
                    	    {/if}
                    	</td>
                        <td>{$item.classroom}</td>
                        <td style="width:30px">
                            {if $item.process == 0}
                            <div class="buttons"><a id="processEdit_{$item.code}" class="button editLeft alphaQuarter" href="main.php?tab=app_person_update&code={$item.code}&position={$item.position}">&nbsp;</a></div>
                            {elseif $item.process == 1}
                            <div class="buttons"><a id="processEdit_{$item.code}" class="button editLeft alphaQuarter" href="main.php?tab=app_person_update&code={$item.code}&position={$item.position}">&nbsp;</a></div>
                            <div class="buttons"><a name="processDelete_{$item.code}_{$item.position}" id="processDelete_{$item.code}_{$item.position}" class="button delete alphaQuarter" href="javascript:">&nbsp;</a></div>
                            <div style="display:none" id="dialog">{$app_person_search_sureToDelete}</div>
                            {/if}                                        
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
			</table>
			<br>
		    <span class="gray italic"><em></em></span>
		</form>
        </div>
    </div>
</div>
<!-- /kişi arama sonucu listele -->