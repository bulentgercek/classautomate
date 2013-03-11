<table id="appsMainTopTable">
    <!-- uygulama listesi -->
    <tr>
    {foreach from=$appList key=k item=v name=appList}
		{if $smarty.foreach.appList.index % 3 == 0}</tr><tr>{/if}
            <td align="center" valign="middle"><a href="main.php?tab={$k}">
                <img src="{$themePath}images/{$v.image}.png" width="80" height="80" /></a><br>{$v.text}
            </td>
    {/foreach}
    </tr>
    <!-- /uygulama listesi -->        	
</table>
