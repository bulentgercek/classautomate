<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE-8" />
<title>{$index_title}</title>
<meta name="description" content="{$index_description}" />
<meta name="keywords" content="{$index_keywords}" />
<meta name="robots" content="index,follow" />
{$index_loginMeta}
</head>

<style type="text/css">
<!--
@import url("{$themePath}css/{$index_css}");
@import url("{$themePath}css/{$index_gplusButton_css}");
-->
</style>

<!-- <script type="text/javascript" src="{$themePath}js/{$index_jquery_js}"></script> -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>

<script type="text/javascript"> 
            $(document).ready(function(){ 
            }); 
        </script>
<body>
<table align="center">
  <tr>
    <td height="100" align="center" valign="middle"><img src="{$themePath}images/classautomate_logo_250x50.gif" alt="classautomate.com" width="250" height="50"></td>
  </tr>
  <tr>
    <td align="center" valign="middle">{include file = {$login_area}}</td>
  </tr>
</table>
</body>
</html>