<!-- accountant -->
<style type="text/css">
    <!--
        @import url("{$themePath}css/{$main_jqueryTimePicker_css}");
        -->
</style>
<script type="text/javascript" src="{$themePath}js/{$main_listControl_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryTableSorter_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryTimePicker_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryAutoGrowInput_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_jquerySelectBoxes_js}"></script>
<script type="text/javascript">
     
</script>

<script type="text/javascript">
    /**
     * json metinleri
     *
     * @param strings
     */
    var incomeExpenseCount = {$incomeExpenseCount};
    var studentList = $.parseJSON('{$studentList|@json_encode}');
    var personnelList = $.parseJSON('{$personnelList|@json_encode}');
    var classroomList = $.parseJSON('{$classroomList|@json_encode}');

</script>
<script type="text/javascript">
	/**
	 * ilk calistirilacaklar
	 */
	$(function() {
        /**
         * Genel degiskenler 
         */
        var mainSelectValue, subTypeSelectionValue, startDateValue, endDateValue, keywordValue;
        var defaultMainSelect = "income";
        
        {literal}
            $('select[id=personnelList]').chosen({no_results_text: "Bulunamadı..."});
            $('select[id=studentList]').chosen({no_results_text: "Bulunamadı..."});
            $('select[id=classroomList]').chosen({no_results_text: "Bulunamadı..."});
        {/literal}
        
        
        if (incomeExpenseCount != "") {
		    {literal}
	            $('#listTable').tablesorter( {sortList: [[0,0]]} );
	        {/literal}
       	}
	    /**
	     * form islemleri icin parametre girisleri
	     */
        $('#addIncomeExpenseForm').append('<input type="hidden" name="tc:addIncomeExpense" value="addIncomeExpenseForm|post" />');
        $('#deleteIncomeExpenseForm').append('<input type="hidden" name="tc:deleteIncomeExpense" value="deleteIncomeExpenseForm|direct" />');
       /**
        * uzerinde degisiklik yapilmasi icin acilan kisinin kod numarasi 
        * hidden form nesnesine ataniyor
        */
        $('#deleteIncomeExpenseForm').prepend('<input type="hidden" id="code" name="code" value="">');
        /**
         * mainSelectForm gönderim kontrolleri ve islemleri 
         */
        $("#mainSelect").change(function() {
            $.defineValues();
            $.setValues('mainSelectForm');
            $('#mainSelectForm').submit();
        });
        
        /**
         * filtersForm gonderim kontrolleri ve islemleri
         */
        $('#filtersForm #submit').click(function(e) {
            $.defineValues();
            $.setValues('filtersForm');
            $('#filtersForm').submit();
        });
        
        /**
         * addIncomeExpenseForm gonderim kontrolleri ve islemleri
         */
        $('#addIncomeExpenseForm #submit').click(function(e) {
            $('#addIncomeExpenseForm').append('<input type="hidden" name="mainSelect" value="' + mainSelectValue + '" />');
            $('#addIncomeExpenseForm').append('<input type="hidden" name="type" value="' + $.convertMainSelectToChar(mainSelectValue) + '" />');
            $('#addIncomeExpenseForm').append('<input type="hidden" name="subType" value="' + $('#subTypeSelectionForms').val() + '" />');
            
            if (mainSelectValue == 'income') {
                if ($('#subTypeSelectionForms').val() == '1') {
                    personCode = $('#studentList').val();
                    classroomCode = $('#classroomList').val();
                } else {
                    personCode = 0;
                    classroomCode = 0;
                }
            }
                
            if (mainSelectValue == 'expense') {
                if ($('#subTypeSelectionForms').val() == '6') { 
                    personCode = $('#personnelList').val();
                    classroomCode = $('#classroomList').val();
                } else {
                    personCode = 0;
                    classroomCode = 0;
                }
            }
                
            $('#addIncomeExpenseForm').append('<input type="hidden" name="onBehalfOf" value="' + personCode + '" />');
            $('#addIncomeExpenseForm').append('<input type="hidden" name="classroom" value="' + classroomCode + '" />');
            $('#addIncomeExpenseForm').submit();
        });
        
        $('select[id=subTypeSelectionForms]').change(function() {
            if ($(this).val() == '6') {
                $('#personnelListLine').show('slow');
                $('#classroomListLine').show('slow');
            } else {
                $('#personnelListLine').hide('slow');
                $('#classroomListLine').hide('slow');
            }
        });

        $('select[id=studentList]').change(function () {
            $.fillClassroomListOptions();
        });

        $('select[id=personnelList]').change(function () {
            $.fillClassroomListOptions();
        });

	   /***************************************************************************************************************

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
         * Fatura goruntuleme pop-up
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
        * kayit formu kontrolleri
        * diyalog ekrani ile onay
        */       
        $('[id^="processDelete"]').click(function() {
            $('#deleteIncomeExpenseForm #code').val( $(this).attr('name').split('_')[1] );
            $("#dialogDelete").dialog({
                title:"Onay",
                resizable: false,
                height:140,
                modal: true,
                buttons: {
                    "{$app_accountant_delete}": function() {
                        $('#deleteIncomeExpenseForm').append('<input type="hidden" name="mainSelect" value="' + mainSelectValue + '" />');
                        $('#deleteIncomeExpenseForm').submit();
                    },
                    "{$app_accountant_cancel}": function() {
                        $(this).dialog( "close" );
                        }
                    }
            });       
        });

        $("#startDate").datepicker({
            showButtonPanel: true,
            dateFormat: 'yy/mm/dd'
        }).attr('readonly', true);
        
        $("#endDate").datepicker({
            showButtonPanel: true,
            dateFormat: 'yy/mm/dd'
        }).attr('readonly', true);
                
        /**
         * form gönderim metodlari
         */
        $.defineDateDefaults = function() {
            currentTime = new Date();
            var month = currentTime.getMonth() + 1;
            var startDay = 1;
            var endday = currentTime.getDate();
            var year = currentTime.getFullYear();
            
            $("#startDate").val(year + "/" + month + "/" + startDay);
            $("#endDate").val(year + "/" + month + "/" + endday);
        }
        
        $.defineValues = function() {
            mainSelectValue = $('#mainSelect').val();
            subTypeSelectionValue = $('#subTypeSelection').val();
            startDateValue = $('#startDate').val();
            endDateValue = $('#endDate').val();
            keywordValue = $('#keyword').val();
        }
        
        $.formsDisplaySettings = function () {
            mainSelectValue = $('#mainSelect').val();

            incomesListsTitle = '{$app_accountant_incomesListsTitle}';
            expensesListsTitle = '{$app_accountant_expensesListsTitle}';
            addNewIncomeTitle = '{$app_accountant_addNewIncomeTitle}';
            addNewExpenseTitle = '{$app_accountant_addNewExpenseTitle}';
            profitListsTitle = '{$app_accountant_profitListsTitle}';
            
            if (mainSelectValue == 'income') {
                $('#formsDisplay').show('slow');
                $('#headerFormsDisplay').html(addNewIncomeTitle);
                $('#formsDisplay #submit').html(addNewIncomeTitle);
                $('#headerListsDisplay').html(incomesListsTitle);
                
                subTypeSelectionFormsValue = $('#subTypeSelectionForms').val();
                if (subTypeSelectionFormsValue == '1') {
                    $('#studentListLine').show('slow');
                    $('#classroomListLine').show('slow');
                } else {
                    $('#studentListLine').hide();
                    $('#classroomListLine').hide();
                }
                $('#personnelListLine').hide();
            }
            if (mainSelectValue == 'expense') {
                $('#formsDisplay').show('slow');
                $('#headerFormsDisplay').html(addNewExpenseTitle);
                $('#formsDisplay #submit').html(addNewExpenseTitle);
                $('#headerListsDisplay').html(expensesListsTitle);
                
                subTypeSelectionFormsValue = $('#subTypeSelectionForms').val();
                if (subTypeSelectionFormsValue == '6') {
                    $('#personnelListLine').show('slow');
                    $('#classroomListLine').show('slow');
                } else {
                    $('#personnelListLine').hide();
                    $('#classroomListLine').hide();
                }
                $('#studentListLine').hide();
            }
            if (mainSelectValue == 'profit') {
                $('#headerListsDisplay').html(profitListsTitle);
            }
        }
        
        $.setValues = function(formName) {
            if (formName == 'mainSelectForm') {
                $('#mainSelectForm').append('<input type="hidden" name="tab" value="app_accountant" />');
                $('#mainSelectForm').append('<input type="hidden" name="mainSelect" value="' + mainSelectValue + '" />');
            }
            if (formName == 'filtersForm') {
                $('#filtersForm').append('<input type="hidden" name="tab" value="app_accountant" />');
                $('#filtersForm').append('<input type="hidden" name="mainSelect" value="' + mainSelectValue + '" />');
                if (subTypeSelectionValue != 'all') {
                    $('#filtersForm').append('<input type="hidden" name="subTypeSelection" value="' + subTypeSelectionValue + '" />');
                }
								if (mainSelectValue != 'profit') {
										$('#filtersForm').append('<input type="hidden" name="startDate" value="' + startDateValue + '" />');
										$('#filtersForm').append('<input type="hidden" name="endDate" value="' + endDateValue + '" />');
								}
            }
        }
        
        $.setSelectedOptions = function() {
            $('#mainSelect option[value=' + $.url().param('mainSelect') + ']').attr('selected', 'selected');
            
            if (isset($.url().param('subTypeSelection'))) {
                $('#subTypeSelection option[value=' + $.url().param('subTypeSelection') + ']').attr('selected', 'selected');
						}
						if (isset($.url().param('startDate')) || isset($.url().param('endDate'))) {
                $('#startDate').val($.url().param('startDate'));
                $('#endDate').val($.url().param('endDate'));
            }
        }
        
        $.convertMainSelectToChar = function (selection) {
            if (selection == 'income') return '+';
            if (selection == 'expense') return '-';
            if (selection == 'profit') return '=';
        }
        
        $.fillClassroomListOptions = function () {
            mainSelectValue = $('#mainSelect').val();
            subTypeSelectionFormsValue = $('#subTypeSelectionForms').val();
            $('select[id=classroomList]').removeOption(/./);
            
            if (mainSelectValue == 'income') {
                selectedStudent = $('#studentList option:selected').val();
				
                $.each(studentList, function(indexStudentList, valueStudentList) {
                    if (valueStudentList['code'] == selectedStudent) {
                        classroomsOfSelectedStudent = valueStudentList['classroom'].split(',');
                        
                        $.each(classroomsOfSelectedStudent, function(indexClassroomOfSelectedStudent, valueClassroomOfSelectedStudent) {
                            $.each(classroomList, function(indexClassroom, valueClassroom) {
                                if (valueClassroomOfSelectedStudent == valueClassroom['code'])
                                    $('select[id=classroomList]').addOption(valueClassroom['code'],valueClassroom['name']);
                            });
                        });
                    };
                });
            }
            
            if (mainSelectValue == 'expense') {
                selectedPersonnel = $('#personnelList option:selected').val();

                $.each(classroomList, function(indexClassroomList, valueClassroomList) {
                    if (valueClassroomList['instructor_code'] == selectedPersonnel) {
                        $('select[id=classroomList]').addOption(valueClassroomList['code'],valueClassroomList['name']);
                    }
                });
            }
            
            $('select[id=classroomList]').trigger("liszt:updated");
        }
        
		/**
		 * sayfa acilis islemleri 
		 */
	    $('#formsDisplay').hide();
        $.defineDateDefaults();
        $.setSelectedOptions();
        $.formsDisplaySettings();
        $.fillClassroomListOptions();
	});

</script>
<div id="accountantMain">
    <div id="filters">
        <div id="filtersFormLine">
            <div id="header" class="header brown bold">{$app_accountant_title}</div>
            <form method="get" name="mainSelectForm" id="mainSelectForm">
                <label for="mainSelect">{$app_accountant_mainSelectTitle}</label>
                <select id="mainSelect" tabindex="{$tabStart++}" class="gSelect">
                    <option value="income">{$app_accountant_income}</option>
                    <option value="expense">{$app_accountant_expense}</option>
                    <option value="profit">{$app_accountant_profit}</option>
                </select>
            </form>
        </div>
        <p id="filtersHeader" class="gray bold">{$app_accountant_filtersTitle}</p>
        <form method="get" name="filtersForm" id="filtersForm">

            <div id="filtersFormLine">
                <label for="subTypeSelection">{$app_accountant_subTypeSelectionTitle}</label>
                <select id="subTypeSelection" tabindex="{$tabStart++}" class="gSelect">
                    {if $mainSelectTypeCode != '='}
                    <option value="all">{$app_accountant_all}</option>
                    {/if}
                    {foreach from=$incomeExpenseTypeList key=myid item=item}
                    <option value="{$item.code}">{$app_accountant_{$item.typeShort}}</option>
                    {/foreach}
                </select>
            </div>
            {if $mainSelectTypeCode == '+' || $mainSelectTypeCode == '-'}
            <div id="filtersFormLine">
                <label for="startDate">{$app_accountant_dateFilter}</label>
                <div id="clear"></div>
                <input id="startDate" type="text" size="1" class="gTextInput">-
                <input id="endDate" type="text" size="1" class="gTextInput">
            </div>
            {/if}
            <div id="filtersSubmitArea">
                <a id="submit" class="button editRight" href="javascript:" tabindex="{$tabStart++}">{$app_accountant_filterIt}</a>
            </div>
        </form>
    </div>
    <div id="accountantCenter">
        <div id="formsDisplay">
            <div id="headerFormsDisplay" class="brown bold header"></div>
            <form method="post" name="addIncomeExpenseForm" id="addIncomeExpenseForm">

                <div id="formLine">
                    <label for="subTypeSelectionForms">{$app_accountant_subTypeSelectionTitle}</label>
                    <select id="subTypeSelectionForms" tabindex="{$tabStart++}" class="gSelect">
                        {foreach from=$incomeExpenseTypeList key=myid item=item}
                        <option value="{$item.code}">{$app_accountant_{$item.typeShort}}</option>
                        {/foreach}
                    </select>
                </div>
                
                <div id="studentListLine">
                    <select id="studentList" data-placeholder="Bir Öğrenci Seçiniz..." style="width: 200px;" class="chzn-select" tabindex="{$tabStart++}">
                        {foreach from=$studentList key=myid item=item}
                        <option value="{$item.code}">{$item.name} {$item.surname}</option>
                        {/foreach}
                    </select>
                </div>
                
                <div id="personnelListLine">
                    <select id="personnelList" data-placeholder="Bir Personel Seçiniz..." style="width: 200px;" class="chzn-select" tabindex="{$tabStart++}">
                        {foreach from=$personnelList key=myid item=item}
                        <option value="{$item.code}">{$item.name} {$item.surname}</option>
                        {/foreach}
                    </select>
                </div>
                
                <div id="classroomListLine">
                    <select id="classroomList" data-placeholder="Bir Sınıf Seçiniz..." style="width: 200px;" class="chzn-select" tabindex="{$tabStart++}">
                        {foreach from=$classroomList key=myid item=item}
                        <option value="{$item.code}">{$item.name} {$item.surname}</option>
                        {/foreach}
                    </select>
                </div>
                              
                <div id="formLine">
                    <label for="amount">{$app_accountant_amount}</label>
                    <input name="amount" type="text" id="amount" tabindex="{$tabStart++}" class="gTextInput" />
                </div>
                
                <div id="formLine">
                    <label for="invoiceInfo">{$app_accountant_invoiceInfo}</label>
                    <input name="invoiceInfo" type="text" id="invoiceInfo" tabindex="{$tabStart++}" class="gTextInput" />
                </div>
    
                <div id="submitArea" class="buttons">
                    <a id="submit" class="button add" href="javascript:" tabindex="{$tabStart++}"></a>
                </div>

            </form>
        </div>
        {if $mainSelectTypeCode == '+' || $mainSelectTypeCode == '-'}
        <div id="listsDisplay">
            <div id="headerListsDisplay" class="brown bold header"></div>
            <div id="list">
                <form method="post" name="deleteIncomeExpenseForm" id="deleteIncomeExpenseForm">
                    <table id="listTable" class="tablesorter">
                        <thead>
                            <tr>
                                <th scope="col">{$app_accountant_dateTimeListTitle}</th>
                                <th scope="col">{$app_accountant_subTypeListTitle}</th>
                                <th scope="col">{$app_accountant_onBehalfOfListTitle}</th>
                                <th scope="col">{$app_accountant_classroomListTitle}</th>
                                <th scope="col">{$app_accountant_amountListTitle}</th>
                                <th scope="col">{$app_accountant_processTitle}</th>
                            </tr>
                        </thead>
                            <tfoot>
                                <tr>
                                    <td colspan="6"><em><span id="warningTextArea">{$app_accountant_countResult} : {count($incomeExpenseList)}</span></em></td>
                                </tr>
                            </tfoot>
                        <tbody>
                            {foreach from=$incomeExpenseList key=key item=item}
                                <tr>
                                    <td>{$item.dateTime}</td>
                                    <td>{$item.subType}</td>
                                    <td>{$item.onBehalfOf}</td>
                                    <td>{$item.classroom}</td>
                                    <td>
                                    <a href="#" id="trigger_{$item.code}" class="trigger noDecor">{$item.amount} {$currency}</a>
                                        <div id="pop-up_{$item.code}" class="pop-up">
                                            <span class="red header">Fatura Bilgisi</span><br>
                                            <span class="black">{if $item.invoiceInfo eq ''}{$main_none}{else}{$item.invoiceInfo}{/if}</span>
                                        </div>
                                    </td>
                                    <td class="columnProcess" style="width:30px">
                                        <div class="buttons"><a name="processDelete_{$item.code}" id="processDelete_{$item.code}" class="button delete alphaQuarter" href="javascript:">&nbsp;</a></div>
                                        <div style="display:none" id="dialogDelete">{$app_accountant_sureToDelete}</div>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        {/if}
        {if $mainSelectTypeCode == '='}
        <div id="listsDisplay">
            <div id="headerListsDisplay" class="brown bold header"></div>
            <div id="list">
                <table id="listTable" class="tablesorter">
                    <thead>
                        <tr>
                            <th scope="col">{$app_accountant_profitSeasonListTitle}</th>
                            <th scope="col">{$app_accountant_incomesTotalListTitle}</th>
                            <th scope="col">{$app_accountant_expensesTotalListTitle}</th>
                            <th scope="col">{$app_accountant_profitTotalListTitle}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$profitList key=key item=item}
                            <tr>
                                <td>{$item.profitSeason}</td>
                                <td>{$item.incomesTotal} {$currency}</td>
                                <td>{$item.expensesTotal} {$currency}</td>
                                <td>{$item.profitTotal} {$currency}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
        {/if}
    </div>
</div>
<!-- /accountant -->