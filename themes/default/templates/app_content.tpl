<!-- sinif listesi -->
<script type="text/javascript" src="{$themePath}js/{$main_tableControl_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryTableSorter_js}"></script>
<script type="text/javascript">
	/**
	 * json metinleri
	 *
	 * @param strings
	 */
	var searchResults = '{$app_content_searchResults}';

</script>
<script type="text/javascript">
	/**
	 * ilk calistirilacaklar
	 */
	$(function() {
        {literal}
            $('#listTable').tablesorter( {sortList: [[0,0]]} );
        {/literal}

        $( "#emptyDialog" ).dialog({
            title:"Onay",
            autoOpen:false,
            resizable: false,
            height:140,
            modal: true,
            buttons: {
                "{$app_content_emptyClassroom}": function() {
		        	$('#emptyClassroomForm').append('<input type="hidden" name="tc:emptyClassroom" value="emptyClassroomForm|direct" />');
					$('#emptyClassroomForm').submit();
                },
                "{$main_cancel}": function() {
                    $(this).dialog("close");
                    }
                }
        }); 
            
        $("#freezeDialog").dialog({
            title:"Onay",
            autoOpen:false,
            resizable: false,
            height:200,
            modal: true,
            buttons: {
                "{$app_content_freezeClassroom}": function() {
		        	$('#freezeClassroomForm').append('<input type="hidden" name="tc:freezeClassroom" value="freezeClassroomForm|direct" />');
					$('#freezeClassroomForm').submit();
                },
                "{$main_cancel}": function() {
                    $(this).dialog("close");
                    }
                }
        });
        
        $( "#unFreezeDialog" ).dialog({
            title:"Onay",
            autoOpen:false,
            resizable: false,
            height:140,
            modal: true,
            buttons: {
                "{$app_content_unFreezeClassroom}": function() {
                	$('#unFreezeClassroomForm').append('<input type="hidden" name="tc:unFreezeClassroom" value="unFreezeClassroomForm|direct" />');
		        	$('#unFreezeClassroomForm').append('<input type="hidden" name="holidayClassroomCode" value="{$classroomInfo.holidayClassroomCode}" />');
					$('#unFreezeClassroomForm').submit();
                },
                "{$main_cancel}": function() {
                    $(this).dialog("close");
                    }
                }
        });  
                    
        /**
         * updateNotesForm gonderim kontrolleri ve islemleri
         */
        $('#updateNotesForm #submit').click(function(e) {
        	$('#updateNotesForm').append('<input type="hidden" name="tc:updateNotes" value="updateNotesForm|direct" />');
        	value = $.cleanEspaceChars($('#notes').val());
        	$('#notes').val(value);
			$('#updateNotesForm').submit();
        });
        
        /**
         * emptyClassroomForm gonderim kontrolleri ve islemleri
         */
        $('#emptyClassroomForm #submit').click(function(e) {
			$("#emptyDialog").dialog("open");
        });

        /**
         * freezeClassroomForm gonderim kontrolleri ve islemleri
         */
        $('#freezeClassroomForm #submit').click(function(e) {
			$("#freezeDialog").dialog("open");
        });

        /**
         * unFreezeClassroomForm gonderim kontrolleri ve islemleri
         */
        $('#unFreezeClassroomForm #submit').click(function(e) {
			$("#unFreezeDialog").dialog("open");
        });
                        
        /**
         * tablo uzerinde hangi satira gelindiyse
         * o satirin islem dugmeleri gorunurluk kazaniyor
         * aksi takdirde de gorunurluk degerleri azaliyor
         */
        $('#listTable tbody tr').hover(
            function() {
                indexValue = $(this).index();
                $('#listTable tbody tr:eq('+indexValue+') > td:last [id^="processEdit"]').fadeTo("fast", 1);
            },
            function() {
                $('#listTable tbody tr:eq('+indexValue+') > td:last [id^="processEdit"]').fadeTo("fast", .5);
        });
				
        /**
         * Odeme bilgisi goruntuleme pop-up
         * 
         * @author Laura Montgomery <http://creativeindividual.co.uk/2011/02/create-a-pop-up-div-in-jquery/>
         */
        var moveLeft = 20;
        var moveDown = 10;
        
        $('[id^="trigger"]').hover(function(e) {
            popUpName = $(this).attr('id').split('_');
            $('div#pop-up_' + popUpName[1]).show();
              //.css('top', e.pageY + moveDown)
              //.css('left', e.pageX + moveLeft)
              //.appendTo('body');
            }, function() {
            $('div#pop-up_' + popUpName[1]).hide();
        });
        
        $('[id^="trigger"]').mousemove(function(e) {
            popUpName = $(this).attr('id').split('_');
            $('div#pop-up_' + popUpName[1]).css('top', e.pageY + moveDown).css('left', e.pageX + moveLeft);
        });
				
        /**
         * baslangic islemleri 
         */
        $('#notes').val("{$classroomInfo.notes}");
 
	});

</script>

<div id="contentMain">
	{if $classroomInfo.code != 'sbyRoom'}
		<div id="info">
			<div id="header" class="header brown bold">{$classroomInfo.name}</div>
			
		    <div id="formLine">
		        <label class="red bold">{$app_content_instructor}</label>
		        <span id="text">{$classroomInfo.instructor_name} {$classroomInfo.instructor_surname}</span>
		    </div>
		    
		    <div id="formLine">
		        <label class="red bold">{$app_content_program}</label>
		        <span id="text">{$classroomInfo.program_name}</span>
		    </div>
		    
		    <div id="formLine">
		        <label class="red bold">{$app_content_saloon}</label>
		        <span id="text">{$classroomInfo.saloon_name}</span>
		    </div>
		    
		    <div id="formLine">
		        <label class="red bold">{$app_content_lessonCount}</label>
		        <span id="text">{$classroomInfo.lectureCount}</span>
		    </div>
		    
		    <div id="formLine">
		        <label class="red bold">{$app_content_holidayLectureCount}</label>
		        <span id="text">{$classroomInfo.holidayLectureCount}</span>
		    </div>
		    <div id="formLine">
		        <label class="red bold">{$app_content_nextLectureDateTime}</label>
		        <span id="text">{$classroomInfo.nextLectureDateTime}</span>
		    </div>
				<div id="formLine">
		        <label class="red bold">{$app_content_instructorNextPaymentDateTime}</label>
		        <span id="text">{$classroomInfo.instructorNextPaymentDateTime}</span>
		    </div>
				<div id="formLine">
		        <label class="red bold">{$app_content_instructorPaymentInCase}</label>
		        <span id="text">{$classroomInfo.instructorPaymentInCase} {$currency}</span>
		    </div>
		    <div id="clear"></div>
	    </div>
	    <div id="list">
	        
	        <div id="header" class="header brown bold">{$app_content_title}</div>
			<form method="post" name="contentForm" id="contentForm">
				<table id="listTable" class="tablesorter">
					<thead>
						<tr>
							<th scope="col">{$app_content_nameSurname}</th>
							<th scope="col">{$app_content_firstLectureDateTime}</th>
							<th scope="col">{$app_content_remainingDebt}</th>
							<th scope="col">{$app_content_nextPaymentDateTime}</th>
							<th scope="col">{$app_content_listProcess}</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="5"><em><span id="warningTextArea">'{$search}'  {$app_content_searchResults} {count($studentList)}</span></em></td>
						</tr>
					</tfoot>
					<tbody>
	                    {foreach from=$studentList key=myid item=item}
	                    <tr>
												<td>{$item.name} {$item.surname}</td>
												<td>{$item.firstLectureDateTime}</td>
												<td>
														<a href="#" id="trigger_{$item.code}" class="trigger noDecor">{$item.remainingDebt}  {$currency}{if $item.debtInfo} ({$main_{$item.debtInfo}}){/if}</a>
														<div id="pop-up_{$item.code}" class="pop-up">
																<span class="red header">Ödeme Bilgisi</span><br>
																<span class="black">{$item.payment} {$currency} ({$main_{$item.paymentPeriod}})</span>
														</div>
												</td>
	                      <td style="width:150px">{$item.nextPaymentDateTime}</td>
	                      <td style="width:30px">
	                            <div class="buttons"><a id="processEdit_{$item.code}" class="button editLeft alphaQuarter" href="main.php?tab=app_person_update&code={$item.code}&position=student">&nbsp;</a></div>                                      
	                      </td>
	                    </tr>
	                    {/foreach}
	                </tbody>
				</table>
			</form>
			
			{if $studentList|@count > 0}
			<form method="post" name="emptyClassroomForm" id="emptyClassroomForm">
				<div id="submitArea" class="buttons">
					<a id="submit" class="button flag" href="javascript:" tabindex="{$tabStart++}">{$app_content_emptyClassroom}</a>
					<div style="display:none" id="emptyDialog">{$app_content_sureToEmpty}</div>
				</div>
			</form>
			{/if}
			
			{if $classroomInfo.holidayClassroomCode == 0}
			<form method="post" name="freezeClassroomForm" id="freezeClassroomForm">
				<div id="submitArea" class="buttons">
					<a id="submit" class="button down" href="javascript:" tabindex="{$tabStart++}">{$app_content_freezeClassroom}</a>
					<div style="display:none" id="freezeDialog">{$app_content_sureToFreeze}</div>
				</div>
			</form>
			{/if}
			
			{if $classroomInfo.holidayClassroomCode > 0}
			<form method="post" name="unFreezeClassroomForm" id="unFreezeClassroomForm">
				<div id="submitArea" class="buttons">
					<a id="submit" class="button up" href="javascript:" tabindex="{$tabStart++}">{$app_content_unFreezeClassroom}</a>
					<div style="display:none" id="unFreezeDialog">{$app_content_sureToUnFreeze}</div>
				</div>
			</form>
			{/if}
			<div id="clear"></div>
			
	    </div>
	    <div id="note">
	    	<form method="post" name="updateNotesForm" id="updateNotesForm">
		    	<div id="header" class="header brown bold">{$app_content_classNote}</div>
		
			    <div style="display: block;">
		    		<div id="noteAreaContainer"><textarea rows="3" name="notes" id="notes"/></textarea></div>
				</div>
				
				<div id="submitArea" class="buttons">
					<a id="submit" class="button add" href="javascript:" tabindex="{$tabStart++}">{$app_content_submitNote}</a>
				</div>
				
				<div id="clear"></div>
			</form>
		</div>
	{else}
	    <div id="sbyRoomlist">
	        
	        <div id="header" class="header brown bold">{$app_content_title_sbyRoom}</div>
			<form method="post" name="contentForm" id="contentForm">
				<table id="listTable" class="tablesorter">
					<thead>
						<tr>
							<th scope="col">{$app_content_nameSurname}</th>
							<th scope="col">{$app_content_remainingDebt}</th>
							<th scope="col">{$app_content_nextPaymentDateTime}</th>
							<th scope="col">{$app_content_listProcess}</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="4"><em><span id="warningTextArea">'{$search}'  {$app_content_searchResults} {count($studentList)}</span></em></td>
						</tr>
					</tfoot>
					<tbody>
	                    {foreach from=$studentList key=myid item=item}
	                    <tr>
	                    	<td>{$item.name} {$item.surname}</td>
	                        <td>{$item.remainingDebt}  {$currency}{if $item.debtInfo} ({$item.debtInfo}){/if}</td>
	                        <td style="width:150px">{$item.nextPaymentDateTime}</td>
	                        <td style="width:30px">
	                            <div class="buttons"><a id="processEdit_{$item.code}" class="button editLeft alphaQuarter" href="main.php?tab=app_person_update&code={$item.code}&position=student">&nbsp;</a></div>                                      
	                        </td>
	                    </tr>
	                    {/foreach}
	                </tbody>
				</table>
			</form>
						
			<div id="clear"></div>
			
	    </div>
    {/if}
	
</div>
<!-- /sinif listesi -->
