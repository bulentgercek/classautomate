<!-- classroom ekleme -->
<style type="text/css">
    <!--
        @import url("{$themePath}css/{$main_jqueryTimePicker_css}");
        -->
</style>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryTableSorter_js}"></script>
<script type="text/javascript">
    /**
     * json metinleri
     *
     * @param strings
     */
    var classroomCount = {$classroomCount};
    var instructorCount = {$instructorCount};
    var programCount = {$programCount};
    var saloonCount = {$saloonCount};
    
</script>
<script type="text/javascript">
	/**
	 * ilk calistirilacaklar
	 */
	$(function() {

		if (classroomCount > 0) {
		    {literal}
	            $('#listTable').tablesorter( {sortList: [[0,0]]} );
	        {/literal}
       	}
	    /**
	     * yeni sinif ekleme form islemleri
	     */
	    $('#addClassroomForm').append('<input type="hidden" name="tc:addClassroom" value="addClassroomForm|post" />');
	    /**
	     * sinif kayit formu kontrolleri
	     */
	    $('#addClassroomForm #submit').click(function(e) {
	        
            if (instructorCount > 0 && programCount > 0 && saloonCount > 0 ) {
                
                $("#dialogAdd").dialog({
                    title:"Onay",
                    resizable: false,
                    height:140,
                    modal: true,
                    buttons: {
                        "{$app_classroom_add}": function() {
                            $('#addClassroomForm').submit();
                        },
                        "{$app_classroom_cancel}": function() {
                            $(this).dialog("close");
                            }
                        }
                });
                
            } else {
            
                $( "#dialogNeedCorrect" ).dialog({
                    modal: true,
                    buttons: {
                        Ok: function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });
                
            }
            
        });
        
        $('#addClassroomForm').live('submit', function() {
        	if ($('#instructorPaymentPeriod :selected').val() == 'percentMonthly') {
	            if ($("#instructorPayment").val() < 0) {
	        		$("#instructorPayment").val(0);
	        	}
	        	if ($("#instructorPayment").val() > 100) {
	        		$("#instructorPayment").val(100);
	        	}
	        }
        	return true;
	    });
	   /***************************************************************************************************************
       /**
        * sinif silme form islemleri
        */
        $('#deleteClassroomForm').append('<input type="hidden" name="tc:deleteClassroom" value="deleteClassroomForm|post" />');
       /**
        * uzerinde degisiklik yapilmasi icin acilan sinifin kod numarasi 
        * hidden form nesnesine ataniyor
        */
        $('#deleteClassroomForm').prepend('<input type="hidden" id="code" name="code" value="">');
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
        * sinif kayit formu kontrolleri
        * diyalog ekrani ile onay
        */       
        $('[id^="processDelete"]').click(function() {
            $('#deleteClassroomForm #code').val( $(this).attr('name').split('_')[1] );
            $("#dialogDelete").dialog({
                title:"Onay",
                resizable: false,
                height:140,
                modal: true,
                buttons: {
                    "{$app_classroom_delete}": function() {
                        $('#deleteClassroomForm').submit();
                    },
                    "{$app_classroom_cancel}": function() {
                        $(this).dialog( "close" );
                        }
                    }
            });       
        });
        
        /**
         * ders zamani secimi
         */
        $('#time').timepicker({
            showLeadingZero: false,
            onHourShow: tpStartOnHourShowCallback,
            onMinuteShow: tpStartOnMinuteShowCallback
        }).attr('readonly', true).css('gTextInput');
        
        $('#endTime').timepicker({
            showLeadingZero: false,
            onHourShow: tpEndOnHourShowCallback,
            onMinuteShow: tpEndOnMinuteShowCallback
        }).attr('readonly', true).css('gTextInput');
        
        $('#instructorPayment').keyup( function() {
        	if ($('#instructorPaymentPeriod :selected').val() == 'percentMonthly') {
	            if ($(this).val() > 100) $(this).val('100');
	            if ($(this).val() < 0) $(this).val('0');
	        }
        });
        
        $('#instructorPayment').keydown( function(event) {
        	if ($('#instructorPaymentPeriod :selected').val() == 'percentMonthly') {
	            if (event.keyCode >= 48 && event.keyCode <= 57) {
	                return true;
	            } else { 
	                if (event.keyCode == 8 || event.keyCode == 9  || event.keyCode >= 96 && event.keyCode <= 105)
	                    return true;
	                else 
	                    return false;
	            }
			}
        });
        
        $('#instructorPaymentPeriod').change(function() {
        	if ($('#instructorPaymentPeriod :selected').val() == 'percentMonthly') {
        		$('#instructorPayment').val(0);
        		$('#instructorPayment').attr('maxlength', 3);
        	} else {
        		$('#instructorPayment').val(0);
        		$('#instructorPayment').attr('maxlength', 5);
        	}
        });
        
		/** sayfa acilis ayalari */
		$('#instructorPayment').val(0);
		$('#instructorPayment').attr('maxlength', 3);
	});

    function tpStartOnHourShowCallback(hour) {
        var tpEndHour = $('#endTime').timepicker('getHour');
        // Check if proposed hour is prior or equal to selected end time hour
        if (hour <= tpEndHour) { return true; }
        // if hour did not match, it can not be selected
        return false;
    }
    function tpStartOnMinuteShowCallback(hour, minute) {
        var tpEndHour = $('#endTime').timepicker('getHour');
        var tpEndMinute = $('#endTime').timepicker('getMinute');
        // Check if proposed hour is prior to selected end time hour
        if (hour < tpEndHour) { return true; }
        // Check if proposed hour is equal to selected end time hour and minutes is prior
        if ( (hour == tpEndHour) && (minute < tpEndMinute) ) { return true; }
        // if minute did not match, it can not be selected
        return false;
    }

    function tpEndOnHourShowCallback(hour) {
        var tpStartHour = $('#time').timepicker('getHour');
        // Check if proposed hour is after or equal to selected start time hour
        if (hour >= tpStartHour) { return true; }
        // if hour did not match, it can not be selected
        return false;
    }
    function tpEndOnMinuteShowCallback(hour, minute) {
        var tpStartHour = $('#time').timepicker('getHour');
        var tpStartMinute = $('#time').timepicker('getMinute');
        // Check if proposed hour is after selected start time hour
        if (hour > tpStartHour) { return true; }
        // Check if proposed hour is equal to selected start time hour and minutes is after
        if ( (hour == tpStartHour) && (minute > tpStartMinute) ) { return true; }
        // if minute did not match, it can not be selected
        return false;
    }
    
</script>
<div id="classroomMain">
	<div id="form">
		<div id="header" class="header brown bold">{$app_classroom_addNewClassroomTitle}</div>
		<form method="post" name="addClassroomForm" id="addClassroomForm">
            <div id="formLine">
                <label for="day">{$app_classroom_classroomsListClassroomDay}</label>
                <select name="day" id="day" tabindex="{$tabStart++}" class="gSelect">

                    {foreach from=$daysOfWeek key=k item=v}
                    <option value="{$v.no}">{$v.day}</option>
                    {/foreach}

                </select>
            </div>

            <div id="formLine">
                <label for="time">{$app_classroom_classroomsListClassroomTime}</label>
                <input id="time" name="time" type="text" size="1" class="gTextInput" style="margin-right: 5px;" value="00:00">
                <input id="endTime" name="endTime" type="text" size="1" class="gTextInput" value="23:00">
            </div>
                        
            <div id="formLine">
                <label for="program">{$app_classroom_classroomsListClassroomProgram}</label>
                <select name="program" id="program" tabindex="{$tabStart++}" class="gSelect">

                    {foreach from=$programsList key=k item=v}
                    <option value="{$k}">{$v}</option>
                    {/foreach}

                </select>
            </div>

            <div id="formLine">
                <label for="instructor">{$app_classroom_classroomsListClassroomInstructor}</label>
                <select name="instructor" id="instructors" tabindex="{$tabStart++}" class="gSelect">

                    {foreach from=$instructorsList key=k item=v}
                    <option value="{$k}">{$v}</option>
                    {/foreach}

                </select>
            </div>
            
            <div id="formLine">
                <label for="instructorPayment">{$app_classroom_classroomsListClassroomInstructorPayment}</label>
                <select name="instructorPaymentPeriod" id="instructorPaymentPeriod" tabindex="{$tabStart++}" class="gSelect">
                	
                    <option value="percentMonthly">{$app_classroom_percentMonthly}</option>
					<option value="fixedMonthly">{$app_classroom_fixedMonthly}</option>
					
                </select>
                <input id="instructorPayment" name="instructorPayment" type="text" size="2" tabindex="{$tabStart++}" class="gTextInput">
            </div>

            <div id="formLine">
                <label for="saloon">{$app_classroom_classroomsListClassroomSaloon}</label>
                <select name="saloon" id="saloon" tabindex="{$tabStart++}" class="gSelect">

                    {foreach from=$saloonsList key=k item=v}
                    <option value="{$k}">{$v}</option>
                    {/foreach}

                </select>
            </div>
                                 
			<div id="submitArea" class="buttons">
				<a id="submit" class="button add" href="javascript:" tabindex="{$tabStart++}">{$app_classroom_submitRecordLabel}</a>
			</div>
			<div style="display:none" id="dialogAdd">{$app_classroom_sureToAdd}</div>
			<div style="display:none" id="dialogNeedCorrect">{$app_classroom_needCorrect}</div>
			
			<div id="clear"></div>
			
		</form>
	</div>
	<div id="list">
		<div id="header" class="header brown bold">{$app_classroom_classroomsListTitle}</div>
		<form method="post" name="deleteClassroomForm" id="deleteClassroomForm">
			<table id="listTable" class="tablesorter">
				<thead>
					<tr>
						<!-- <th scope="col">{$app_classroom_classroomsListClassroomDay}</th> -->
						<th scope="col">{$app_classroom_classroomsListClassroomName}</th>
						<!-- <th scope="col">{$app_classroom_classroomsListClassroomTime}</th> -->
						<th scope="col">{$app_classroom_classroomsListClassroomInstructor}</th>
						<th scope="col">{$app_classroom_classroomsListClassroomSaloon}</th>
						<th scope="col">{$app_classroom_classroomsListUsage}</th>
						<th scope="col">{$app_classroom_classroomsListProcess}</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="7"><em><span id="warningTextArea">{$app_classroom_classroomsListCount} : {$classroomListArray|@count}</span></em></td>
					</tr>
				</tfoot>
				<tbody>
					{foreach from=$classroomListArray key=myid item=item}
					<tr>
						<!-- <td><span class="alphaHalf">[{$item.dayCode}]</span>{$item.day}</td> -->
						<td>{$item.name}</td>
                        <!-- <td>{$item.time}</td> -->
                        <td>{$item.instructor_name} {$item.instructor_surname}</td>
                        <td>{$item.saloon_name}</td>
						<td>{$item.status} {$app_classroom_classroomsListUsageObject}</td>
						<td class="columnProcess" style="width:30px">
							<div class="buttons"><a id="processEdit_{$item.code}" class="button editLeft alphaQuarter" href="main.php?tab=app_classroom_update&amp;code={$item.code}">&nbsp;</a></div>
							{if $item.process == 1}
							<div class="buttons"><a name="processDelete_{$item.code}" id="processDelete_{$item.code}" class="button delete alphaQuarter" href="javascript:">&nbsp;</a></div>
                            <div style="display:none" id="dialogDelete">{$app_classroom_sureToDelete}</div>
							{/if}                                        
						</td>
					</tr>
					{/foreach}
                </tbody>
			</table>
		</form>
	</div>
</div>
<!-- /classroom ekleme -->