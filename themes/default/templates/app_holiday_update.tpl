<!-- tatil duzenle -->
<style type="text/css">
    <!--
        @import url("{$themePath}css/{$main_jqueryTimePicker_css}");
        -->
</style>
<script type="text/javascript" src="{$themePath}js/{$main_listControl_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryTimePicker_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryAlert_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryTableSorter_js}"></script>
<script type="text/javascript">
	/**
	 * json metinleri
	 *
	 * @param strings
	 */
    holidayType = '{$holidayList["type"]}';
    var instructorList = new Array();
    {foreach from=$instructorList key=myid item=item}
    instructorList[{$item.code}] = "{$item.name} {$item.surname}";
    {/foreach}
</script>
<script type="text/javascript">
	/**
	 * ilk calistirilacaklar
	 */
	$(function() {
	    /** konu tablolarini gizle */
        $('#personnel').hide();
        $('#custom').hide();
        
		/**
		 * form ismini aktarabilmek icin gizli degisken yarat
		 */
        $('#updateHolidayForm').append('<input type="hidden" name="tc:updateHoliday" value="updateHolidayForm|post" />');
        /**
         * uzerinde degisiklik yapilmasi icin acilan kod numarasi 
         * hidden form nesnesine ataniyor
         */
        $('#updateHolidayForm').prepend('<input type="hidden" name="code" value="{$holidayList["code"]}" />');

        /*
         * egitmen listesi 'option' listesini hafizala
         */
        $.listControl.init('holidayPersonnel');
        $.listControl.init('backupPersonnel');
        
        /**
         * holiday kayit formu kontrolleri
         */
        $('#updateHolidayForm #submit').click(function(e) {
            startDateTime = $("#startDate").val().split('/').join('-') + ' ' + $("#startTime").val();
            endDateTime = $("#endDate").val().split('/').join('-') + ' ' + $("#endTime").val();
            $('#updateHolidayForm').append('<input type="hidden" name="startDateTime" value="' + startDateTime + '" />');
            $('#updateHolidayForm').append('<input type="hidden" name="endDateTime" value="' + endDateTime + '" />');
            $('#updateHolidayForm').submit();
        });
 
         $('#subject').change(function(){
           var value = $(this).val().split('|')[0];
           if (value == 'official') {
								$('#custom').hide('slow');
								$('#classroom').hide('slow');
								$('#personnel').hide('slow');
           } else if (value == 'personnel') {
								$('#custom').hide('slow');
								$('#classroom').hide('slow');
								$('#personnel').show('slow');
           } else if (value == 'custom') {
								$('#personnel').hide('slow');
								$('#classroom').hide('slow');
								$('#custom').show('slow');
           } else if (value == 'classroom') {
								$('#personnel').hide('slow');
								$('#custom').hide('slow');
								$('#classroom').show('slow');
           }
        });
        
        $('#holidayPersonnel').change(function() {
            $.listControl.restoreAll('backupPersonnel');
            personnelListIndex = $("#holidayPersonnel :selected").index();
            $.listControl.removeItem('backupPersonnel', personnelListIndex + 1);
        });
               
        $('#startTime').timepicker({
            showLeadingZero: false,
            onHourShow: tpStartOnHourShowCallback,
            onMinuteShow: tpStartOnMinuteShowCallback
        }).attr('readonly', true).css('gTextInput');
        
        $('#endTime').timepicker({
            showLeadingZero: false,
            onHourShow: tpEndOnHourShowCallback,
            onMinuteShow: tpEndOnMinuteShowCallback
        }).attr('readonly', true).css('gTextInput');
        
        $("#startDate").datepicker({
            showButtonPanel: true,
            dateFormat: 'yy-mm-dd'
        }).attr('readonly', true);
        
        $("#endDate").datepicker({
            showButtonPanel: true,
            dateFormat: 'yy-mm-dd'
        }).attr('readonly', true);
        
        /**
         * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
         * update bilgilerine gore formun ici dolduruluyor
         * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
         */
        $('#updateHolidayForm #startDate').val('{$holidayList["startDate"]}');
        $('#updateHolidayForm #endDate').val('{$holidayList["endDate"]}');

        $('#updateHolidayForm #startTime').val('{$holidayList["startTime"]}');
        $('#updateHolidayForm #endTime').val('{$holidayList["endTime"]}');
        
        if (holidayType == 'official') {
            $('#updateHolidayForm #subject').val('official|{$holidayList["subject"]}');
						$('#classroom').hide();
						$('#personnel').hide();
						$('#custom').hide();
        }
        
        if (holidayType == 'personnel') {
            $('#updateHolidayForm #subject').val('personnel');
            $('#holidayPersonnel option[value={$holidayList["holidayPersonnel"]}]').attr('selected', 'selected');
            $('#backupPersonnel option[value={$holidayList["backupPersonnel"]}]').attr('selected', 'selected');
            $('#personnel').show('slow');
        }
        
        if (holidayType == 'classroom') {
            $('#updateHolidayForm #subject').val('classroom');
            $('#holidayClassroom option[value={$holidayList["holidayClassroom"]}]').attr('selected', 'selected');
            $('#classrooom').show('slow');
        }
        
        if (holidayType == 'custom') {
            $('#updateHolidayForm #subject').val('custom');
            $('#customSubject').val('{$holidayList["customSubject"]}')
            $('#custom').show('slow');
        }
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
        var tpStartHour = $('#startTime').timepicker('getHour');
        // Check if proposed hour is after or equal to selected start time hour
        if (hour >= tpStartHour) { return true; }
        // if hour did not match, it can not be selected
        return false;
    }
    function tpEndOnMinuteShowCallback(hour, minute) {
        var tpStartHour = $('#startTime').timepicker('getHour');
        var tpStartMinute = $('#startTime').timepicker('getMinute');
        // Check if proposed hour is after selected start time hour
        if (hour > tpStartHour) { return true; }
        // Check if proposed hour is equal to selected start time hour and minutes is after
        if ( (hour == tpStartHour) && (minute > tpStartMinute) ) { return true; }
        // if minute did not match, it can not be selected
        return false;
    }
    
</script>
<div id="holidayUpdateMain">
	<div id="back">
		<p style="margin-right: 10px;">{$app_holiday_update_submitToHolidayText}</p>
		<a id="submitBackButton" class="button editLeft" href="main.php?tab=app_holiday" tabindex="{$tabStart++}">{$app_holiday_update_submitToHoliday}</a>
	</div>
    <div id="form">
        <form method="post" name="updateHolidayForm" id="updateHolidayForm">
            <div id="header" class="header brown bold">{$app_holiday_update_updateHolidayTitle}</div>

            <div id="formLine">
                <label for="startDate">{$app_holiday_update_startDate}</label>
                <input id="startDate" type="text" size="1" class="gTextInput" style="margin-right: 5px;">
                <label for="endDate">{$app_holiday_update_endDate}</label>
                <input id="endDate" type="text" size="1" class="gTextInput">
            </div>
            
            <div id="formLine">
                <label for="startTime">{$app_holiday_update_startTime}</label>
                <input id="startTime" type="text" size="1" class="gTextInput" style="margin-right: 5px;" value="00:00">
                <label for="endTime">{$app_holiday_update_endTime}</label>
                <input id="endTime" type="text" size="1" class="gTextInput" value="23:00">
            </div>
            
            <div id="formLine">
                <label for="subject">{$app_holiday_update_subject}</label>
                <select name="subject" id="subject" tabindex="{$tabStart++}" class="gSelect">

                    {foreach from=$subjectList key=myid item=item}
                    <option value="official|{$item.code}">{$item.subject}</option>
                    {/foreach}
                    <option value="personnel">{$app_holiday_update_personnelList}</option>
                    <option value="classroom">{$app_holiday_update_classroomList}</option>
                    <option value="custom">{$app_holiday_update_customList}</option>

                </select>
            </div>
            
            <div id="personnel">
                <div id="formLine">
                    <label for="holidayPersonnel">{$app_holiday_update_holidayPersonnel}</label>
                    <select name="holidayPersonnel" id="holidayPersonnel" tabindex="{$tabStart++}" class="gSelect">
    
                        {foreach from=$instructorList key=myid item=item}
                        <option value="{$item.code}">{$item.name} {$item.surname}</option>
                        {/foreach}
    
                    </select>
                </div>
                <div id="formLine">
                    <label for="backupPersonnel">{$app_holiday_update_backupPersonnel}</label>
                    <select name="backupPersonnel" id="backupPersonnel" tabindex="{$tabStart++}" class="gSelect">
    
                        <option value="0">{$app_holiday_update_none}</option>
                        {foreach from=$instructorList key=myid item=item}
                        <option value="{$item.code}">{$item.name} {$item.surname}</option>
                        {/foreach}
    
                    </select>
                </div>                
            </div>
            
						<div id="classroom">
                <div id="formLine">
                    <label for="holidayClassroom">{$app_holiday_update_holidayClassroom}</label>
                    <select name="holidayClassroom" id="holidayClassroom" tabindex="{$tabStart++}" class="gSelect">
                        {foreach from=$classroomList key=myid item=item}
	                        {if $item.status == 'active'}
	                        	<option value="{$item.code}">{$item.name}</option>
	                        {/if}
                        {/foreach}
                    </select>
                </div>
            </div>
                        
            <div id="custom">
                <div id="formLine">
                    <label for="customSubject">{$app_holiday_update_customHoliday}</label>
                    <input id="customSubject" name="customSubject" type="text" size="1" class="gTextInput">                    
                </div>
            </div>
            
            <div id="submitArea" class="buttons">
                <a id="submit" class="button add" href="javascript:" tabindex="{$tabStart++}">{$app_holiday_update_submitRecordLabel}</a>
            </div>
            
            <div id="clear"></div>
            
    </form>
</div>
<!-- /salon duzenle -->