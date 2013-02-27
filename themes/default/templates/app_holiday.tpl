<!-- holiday ekleme -->
<style type="text/css">
    <!--
        @import url("{$themePath}css/{$main_jqueryTimePicker_css}");
        -->
</style>
<script type="text/javascript" src="{$themePath}js/{$main_listControl_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryTimePicker_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryAlert_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryTableSorter_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryAutoGrowInput_js}"></script>
<script type="text/javascript">
    /**
     * json metinleri
     *
     * @param strings
     */
    var holidayCount = {$holidayCount};
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
        /**
         * holiday kayit formu kontrolleri
         */
        $('#addHolidayForm #submit').click(function(e) {
            /**
             * yeni tatil ekleme form islemleri
             */
            startDateTime = $("#startDate").val().split('/').join('-') + ' ' + $("#startTime").val();
            endDateTime = $("#endDate").val().split('/').join('-') + ' ' + $("#endTime").val();
            $('#addHolidayForm').append('<input type="hidden" name="tc:addHoliday" value="addHolidayForm|post" />');
            $('#addHolidayForm').append('<input type="hidden" name="startDateTime" value="' + startDateTime + '" />');
            $('#addHolidayForm').append('<input type="hidden" name="endDateTime" value="' + endDateTime + '" />');
            $('#addHolidayForm').submit();
        });
        
        /**
         * egitmen listesi 'option' listesini hafizala
         */
        $.listControl.init('holidayPersonnel');
        $.listControl.init('backupPersonnel');
              
        /**
         * tatil liste tablosunu tablesorter ile bagla
         */
        if (holidayCount > 0) {
            {literal}
                $('#listTable').tablesorter( {sortList: [[0,0]]} );
            {/literal}
        }

       /**
        * uzerinde degisiklik yapilmasi icin acilan kisinin kod numarasi 
        * hidden form nesnesine ataniyor
        */
        $('#deleteHolidayForm').prepend('<input type="hidden" id="code" name="code" value="">');
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
        * holiday kayit formu kontrolleri
        * diyalog ekrani ile onay
        */       
        $('[id^="processDelete"]').click(function() {
            $('#deleteHolidayForm #code').val( $(this).attr('name').split('_')[1] );
            $( "#dialog" ).dialog({
                title:"Onay",
                resizable: false,
                height:140,
                modal: true,
                buttons: {
                    "{$app_holiday_delete}": function() {
                       /**
                        * salon silme form islemleri
                        */
                        $('#deleteHolidayForm').append('<input type="hidden" name="tc:deleteHoliday" value="deleteHolidayForm|post" />');
                        $('#deleteHolidayForm').submit();
                    },
                    "{$app_holiday_cancel}": function() {
                        $( this ).dialog( "close" );
                        }
                    }
            });       
        });
        
        $('#subject').change(function(){
           var value = $(this).val();
           if (value == 'personnel') {
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
            dateFormat: 'yy/mm/dd'
        }).attr('readonly', true);
        
        $("#endDate").datepicker({
            showButtonPanel: true,
            dateFormat: 'yy/mm/dd'
        }).attr('readonly', true);
        
        currentTime = new Date();
        var month = currentTime.getMonth() + 1;
        var day = currentTime.getDate();
        var year = currentTime.getFullYear();
        
        $("#startDate").val(year + "/" + month + "/" + day);
        $("#endDate").val(year + "/" + month + "/" + day);
        
        $('#personnel').hide();
        $('#classroom').hide();
        $('#custom').hide();
        
        $.listControl.removeItem('backupPersonnel', 1);
        
        /**
         * customSubject alaninin boyutunu, maksimum harf sayisini belirle
         * ve alanin otomatik genislemesini sagla
         */
        $('#customSubject').width('150px').attr("maxlength","40").autoGrowInput({
            comfortZone: 10, minWidth: 150
        });
        
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
<div id="holidayMain">
    <div id="form">
        <form method="post" name="addHolidayForm" id="addHolidayForm">
            <div id="header" class="header brown bold">{$app_holiday_addNewHolidayTitle}</div>

            <div id="formLine">
                <label for="startDate">{$app_holiday_startDate}</label>
                <input id="startDate" type="text" size="1" class="gTextInput">
                <label for="endDate">{$app_holiday_endDate}</label>
                <input id="endDate" type="text" size="1" class="gTextInput">
            </div>
            
            <div id="formLine">
                <label for="startTime">{$app_holiday_startTime}</label>
                <input id="startTime" type="text" size="1" class="gTextInput" value="00:00">
                <label for="endTime">{$app_holiday_endTime}</label>
                <input id="endTime" type="text" size="1" class="gTextInput" value="23:00">
            </div>
            
            <div id="formLine">
                <label for="subject">{$app_holiday_subject}</label>
                <select name="subject" id="subject" tabindex="{$tabStart++}" class="gSelect">

                    {foreach from=$subjectList key=myid item=item}
                    <option value="official|{$item.code}">{$item.subject}</option>
                    {/foreach}
                    <option value="personnel">{$app_holiday_personnelList}</option>
                    <option value="classroom">{$app_holiday_classroomList}</option>
                    <option value="custom">{$app_holiday_customList}</option>

                </select>
            </div>
            
            <div id="personnel">
                <div id="formLine">
                    <label for="holidayPersonnel">{$app_holiday_holidayPersonnel}</label>
                    <select name="holidayPersonnel" id="holidayPersonnel" tabindex="{$tabStart++}" class="gSelect">
                        {foreach from=$instructorList key=myid item=item}
                        <option value="{$item.code}">{$item.name} {$item.surname}</option>
                        {/foreach}
                    </select>
                </div>
                <div id="formLine">
                    <label for="backupPersonnel">{$app_holiday_backupPersonnel}</label>
                    <select name="backupPersonnel" id="backupPersonnel" tabindex="{$tabStart++}" class="gSelect">
    
                        <option value="0">{$app_holiday_none}</option>
                        {foreach from=$instructorList key=myid item=item}
                        <option value="{$item.code}">{$item.name} {$item.surname}</option>
                        {/foreach}
    
                    </select>
                </div>                
            </div>

            <div id="classroom">
                <div id="formLine">
                    <label for="holidayClassroom">{$app_holiday_holidayClassroom}</label>
                    <select name="holidayClassroom" id="holidayClassroom" tabindex="{$tabStart++}" class="gSelect">
                        {foreach from=$classroomList key=myid item=item}
	                        {if $item.status == 'active'}
	                        	<option value="{$item.code}">{$item.name}</option>
	                        {/if}
                        {/foreach}
                    </select>
                </div>
            </div>
            
            <div id= "custom">
                <div id="formLine">
                    <label for="customSubject">{$app_holiday_customHoliday}</label>
                    <input id="customSubject" name="customSubject" type="text" size="1" class="gTextInput">                    
                </div>
            </div>
            
            <div id="submitArea" class="buttons">
                <a id="submit" class="button add" href="javascript:" tabindex="{$tabStart++}">{$app_holiday_submitRecordLabel}</a>
            </div>
            
            <div id="clear"></div>
            
        </form>
    </div>

    <div id="list">
        <div id="header" class="header brown bold">{$app_holiday_holidaysListTitle}</div>
        <form method="post" name="deleteHolidayForm" id="deleteHolidayForm">
            <table id="listTable" class="tablesorter">
                <thead>
                    <tr>
                        <th scope="col">{$app_holiday_holidaysListHolidayStartDateTime}</th>
                        <th scope="col">{$app_holiday_holidaysListHolidayEndDateTime}</th>
                        <th scope="col">{$app_holiday_holidaysListHolidayReason}</th>
                        <th scope="col">{$app_holiday_holidaysListProcess}</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td colspan="4"><em><span id="warningTextArea">{$app_holiday_holidaysListCount} : {$holidayCount}</span></em></td>
                    </tr>
                </tfoot>
                <tbody>
                    {foreach from=$holidayList key=myid item=item}
                    <tr>
                        <td>{$item.startDateTime}</td>
                        <td>{$item.endDateTime}</td>
                        <td>{$item.reason}</td>
                        <td class="columnProcess" style="width:30px">
                            <div class="buttons"><a id="processEdit_{$item.code}" class="button editLeft alphaQuarter" href="main.php?tab=app_holiday_update&amp;code={$item.code}">&nbsp;</a></div>
                            <div class="buttons"><a name="processDelete_{$item.code}" id="processDelete_{$item.code}" class="button delete alphaQuarter" href="javascript:">&nbsp;</a></div>
                            <div style="display:none" id="dialog">{$app_holiday_sureToDelete}</div>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </form>
    </div>

</div>
<!-- /holiday ekleme -->