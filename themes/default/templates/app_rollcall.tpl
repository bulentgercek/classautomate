<!-- yoklama -->
<style type="text/css">
    <!--
        @import url("{$themePath}css/{$main_jqueryTimePicker_css}");
        -->
</style>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryTableSorter_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryTimePicker_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_rollcallControl_js}"></script>
<script type="text/javascript">
	/**
	 * json metinleri
	 *
	 * @param strings
	 */
	var activeClassroomCount = {$activeClassroomCount};
	var notActiveClassroomCount = {$notActiveClassroomCount};
	var classroomCount = {$classroomCount};

</script>
<script type="text/javascript">
	/**
	 * sayfa scriptleri
	 */
	$(function() {
		/**
		 * Genel degiskenler 
		 */
		var classroomValue;
		var dayTimeValue;
		var activeCheckBox;
		/**
         * sinif liste tablosunu tablesorter ile bagla
         */
        if (classroomCount != "") {
	        {literal}
	           $("#listTable").tablesorter( {sortList: [[0,0]]} );
	        {/literal}
        }
        
		$("#date").datepicker({
			showButtonPanel: true,
			dateFormat: 'yy/mm/dd'
		}).attr('readonly', true);
		

		$("#date").change(function() {
			$.defineValues();
			$.setValues();
			$('#rollcallForm').submit();
		});
		
		$("#classroom").change(function() {
			$.defineValues();
			$.setValues();
			$('#rollcallForm').submit();
		});

		/**
		 * sinif aktif mi?
		 */
		$('input[id^="classActive"]').click(function() {
		    activeCheckBox = $(this);
            $("#dialog").dialog({
                closeOnEscape: false,
                open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); },
                title:"Onay",
                resizable: false,
                height:140,
                modal: true,
                buttons: {
                    "{$main_activate}": function() {
                        classroomValue = activeCheckBox.attr('name').split('_')[1];
                        dayTimeValue = $('#classroom_' + classroomValue).val();
                        $('#activateRollcallForm').submit();
                    },
                    "{$main_cancel}": function() {
                        activeCheckBox.prop('checked', false);
                        $(this).dialog("close");
                        }
                    }
            });
		});
		
		/**
		 * yoklama kayit kontroller ve form gonderimi
		 */
		$('[id^="rollCheck"]').live("click", function() {
			if ($(this).val() == "off") {
				$(this).val('on');
			} else {
				$(this).val('off');
			}
		});
		
		$('#addRemoveRollcallForm #submit').click(function(e) {
			$('#addRemoveRollcallForm').submit();
		});
		
		$('#addRemoveRollcallForm').live('submit', function() {
			$('#addRemoveRollcallForm').append('<input type="hidden" name="tc:addRemoveRollcall" value="addRemoveRollcallForm|post"/>');
			$('#addRemoveRollcallForm').append('<input type="hidden" name="classroom" value="'+ $.url().param('classroom') +'"/>');
			$('#addRemoveRollcallForm').append('<input type="hidden" name="dayTime" value="'+ $.url().param('dayTime') +'"/>');
			$('#addRemoveRollcallForm').append('<input type="hidden" name="date" value="'+ $.url().param('date') +'"/>');
		  	return true;
		});
		
        $('#activateRollcallForm').live('submit', function() {
            $('#activateRollcallForm').append('<input type="hidden" name="tc:activateRollcall" value="activateRollcallForm|post"/>');
            $('#activateRollcallForm').append('<input type="hidden" name="classroom" value="' + classroomValue + '" />');
            $('#activateRollcallForm').append('<input type="hidden" name="dayTime" value="' + dayTimeValue + '" />');
            $('#activateRollcallForm').append('<input type="hidden" name="date" value="' + $('#date').val() + '" />');
            return true;
        });
		
		/**
		 * metodlar 
		 */	
		$.defineValues = function() {
			if ($('#classroom').val() == "all") {
				classroomValue = "all";
				dayTimeValue = "";
			} else {
				classroomValue = $('#classroom').val().split('_')[0];
				dayTimeValue = $('#classroom').val().split('_')[1];
			}			
		}
		
		$.setValues = function() {
			$('#rollcallForm').append('<input type="hidden" name="tab" value="app_rollcall" />');
			$('#rollcallForm').append('<input type="hidden" name="classroom" value="' + classroomValue + '" />');
			$('#rollcallForm').append('<input type="hidden" name="dayTime" value="' + dayTimeValue + '" />');
			$('#rollcallForm').append('<input type="hidden" name="date" value="' + $('#date').val() + '" />');
		}
		
		$.setClassroomOption = function() {
			if ($.url().param('classroom') != "all") {
				var selectedClassroomValue = $.url().param('classroom') + '_' + $.url().param('dayTime');
				$('#classroom option[value=' + selectedClassroomValue + ']').attr('selected', 'selected');
			}
		}
		
		/* classroom bilgisini url'den al ve degerlendir */
		$.setClassroomOption();
		
		/* sayfa baslangicinda yoklamasi olan yani value="on" olanlarin checkbox'lari işaretleniyor */
		
		{foreach from=$rollcallList key=k item=item}
		$("input[name=rollCheck_{$item.studentCode}_{$item.rollcallCode}]").prop("checked", {if $item.rollcallCode != 0}true{else}false{/if});
		{/foreach}
		
	});
	
</script>
<div id="rollcallMain">
	<div id="form">
        <div id="header" class="header brown bold">{$app_rollcall_title}</div>
		<form method="get" name="rollcallForm" id="rollcallForm">
            <div id="formLine">
                <label for="date">{$app_rollcall_date}</label>
                <input id="date" type="text" size="1" class="gTextInput" value="{$date}">
                <span id="weekDayText" class="red">{$weekDayText}</span>
            </div>
			<div id="formLine">
				<label for="classroom">{$app_rollcall_classList}</label>
				<select id="classroom" tabindex="{$tabStart++}" class="gSelect">
					<option value="all">Günün Tüm Sınıfları</option>
					{if $activeClassroomCount > 0}
    					{foreach from=$activeClassroomList key=key item=item}
	    					{if $item.lectureStatus neq 'off' && $item.lectureStatus neq ''}
	    						<option value="{$item.code}_{$item.dayTimeCode}">{$item.className}</option>
	    					{/if}
    					{/foreach}
    				{/if}
				</select>
			</div>
		</form>
	</div>

	{if $classroom eq 'all' or $classroomCount eq 0}
	<!-- Baslayan siniflarin belirlendigi tablo -->
	
    	{if $notActiveClassroomCount > 0}
            <div id="list">
    			<form method="post" name="activateRollcallForm" id="activateRollcallForm">
        			<table id="listTableStarting">
        				<thead>
        					<tr>
        						<th scope="col">{$app_rollcall_startingClassName}</th>
        						<th scope="col">{$app_rollcall_startingTime}</th>
        						<th scope="col">{$app_rollcall_startIt}</th>
        					</tr>
        				</thead>
        					<tfoot>
        						<tr>
        							<td colspan="3"><em><span id="warningTextArea">{$app_rollcall_notActiveClassroomCountResult} : {$notActiveClassroomCount}</span></em></td>
        						</tr>
        					</tfoot>
        				<tbody>
        	                {foreach from=$notActiveClassroomList key=key item=item}
        	                	{if $lastClassroomCode != $item.code}
	            	                {if $item.classroomStatus != 'active'}
	            	                <tr>
	            	                	<td>{$item.classTopName}</td>
	            	                	<td>
	            	                		<select id="classroom_{$item.code}" tabindex="{$tabStart++}" class="gSelect">
	            							
	            								{foreach from=$notActiveClassroomList key=sKey item=sItem}
	            								{if $item.code eq $sItem.code}
            										<option value="{$sItem.dayTimeCode}">{$sItem.time} - {$sItem.endTime}</option>
	            								{/if}
	            								{/foreach}
	            							</select>
	            						</td>
	            	                	<td>
	                                        <input id="classActive_{$item.code}" name="classActive_{$item.code}" type="checkbox" value="off">
	                                        <div style="display:none" id="dialog">{$app_rollcall_sureToActivate}</div>
	            	                	</td>
	            	                </tr>
	            	                {/if}
	            	                {$lastClassroomCode = $item.code}
            	                {/if}
        	                {/foreach}
        	            </tbody>
        			</table>
    			</form>
    	    </div>
        {/if}
        
	    <!-- Tum siniflarin listelendigi tablo -->
	    {if $activeClassroomCount > 0}
		<div id="clear"></div>
		
	    <div id="list">
			<table id="listTable" class="tablesorter">
				<thead>
					<tr>
						<th scope="col">{$app_rollcall_className}</th>
						<th scope="col">{$app_rollcall_instructor}</th>
						<th scope="col">{$app_rollcall_program}</th>
						<th scope="col">{$app_rollcall_lectureNo}</th>
						<th scope="col">{$app_rollcall_lectureStatus}</th>
						<th scope="col">{$app_rollcall_participants}</th>
					</tr>
				</thead>
					<tfoot>
						<tr>
							<td colspan="7"><em><span id="warningTextArea">{$app_rollcall_classroomCountResult} : {$activeClassroomCount}</span></em></td>
						</tr>
					</tfoot>
				<tbody>
	                {foreach from=$activeClassroomList key=key item=item}
	                	{if $item.lectureNo > 0}
	    	                {if $item.classroomStatus == 'active'}
	    	                <tr {if $item.lectureStatus == 'off'}id="red"{/if}>
	    	                	<td>{$item.className}</td>
	    	                	<td>{$item.instructor}</td>
	    	                	<td>{$item.program}</td>
	    	                	<td>{$item.lectureNo}</td>
	    	                	<td>{$app_rollcall_{$item.lectureStatus}}</td>
	    	                	<td>{$item.participants}</td>
	    	                </tr>
	    	                {/if}
						{/if}
	                {/foreach}
	            </tbody>
			</table>
	    </div>
	    {/if}
    {else}
    	
    	{if $studentList|@count > 0 && $isClassroomExist}
	    	<div id="clear"></div>
	    	
	    	<!-- Secilen sinifa gore yoklama listesi -->
	 	    <div id="list">
	            <form method="post" name="addRemoveRollcallForm" id="addRemoveRollcallForm">
	    			<table id="listTable" class="tablesorter">
	    				<thead>
	    					<tr>
	    						<th scope="col">{$app_rollcall_nameSurname}</th>
	    						<th scope="col">{$app_rollcall_firstLecture}</th>
	    						<th scope="col" style="width: 10%">{$app_rollcall_attendance}</th>
	    					</tr>
	    				</thead>
	    					<tfoot>
	    						<tr>
	    							<td colspan="3"><em>
	    							<div style="float: left">
	    							    <span id="warningTextArea">{$app_rollcall_studentCountResult} : {count($studentList)}</span></em>
	    							</div>
	                                <div style="float: right" id="submitArea" class="buttons">
	                                    <a id="submit" class="button save" href="javascript:" tabindex="{$tabStart++}">{$app_rollcall_submitRecordLabel}</a>
	                                </div>    
	    							</td>
	    						</tr>
	    					</tfoot>
	    				<tbody>
	    	                {foreach from=$studentList key=key item=item name=students}
	    	                <tr>
	    	                	<td>{$item.name} {$item.surname}</td>
	    	                	<td>{$item.firstLecture}</td>
	    						<td>
	   								<input type="hidden" id="rollCheck_{$item.code}_{$rollcallList[$key]['rollcallCode']}" name="rollCheck_{$item.code}_{$rollcallList[$key]['rollcallCode']}" value="off" />
	    							<input id="rollCheck_{$item.code}_{$rollcallList[$key]['rollcallCode']}" name="rollCheck_{$item.code}_{$rollcallList[$key]['rollcallCode']}" type="checkbox" value="{if $rollcallList[$key]['rollcallCode'] != 0}on{else}off{/if}">
	    						</td>
	    	                </tr>
	    	                {/foreach}
	    	            </tbody>
	    			</table>
	            </form>
		    </div>
		 {else}
	    	<div id="clear"></div>
	    	
				{if $isClassroomExist}
						<div id="noStudent" class="red">{$app_rollcall_noStudent}</div>
				{/if}
		 {/if}
    {/if}
    {if $activeClassroomCount < 1 || !$isClassroomExist}
    	<div id="clear"></div>
    	<div id="noActiveClassroom" class="red">{$app_rollcall_noActiveClassroom}</div>
    {/if}
</div>
<!-- /yoklama -->
