{* render a simple jquery ui menu 
=================================
*}
{literal}
    <script>
        $(function () {
            $("#menu > ul").menu();
        });
    </script>
    <style>
        .ui-menu {
            width: 150px;
        }
    </style>
{/literal}
<div id="menu">
    {include uri='design:parts/render/recursive-list.tpl' args=$args}
</div>

{* TODO expand current menu item *}
{literal}
    <script>
        $("#menu > ul").menu("focus", null, menu.find(".current"));
    </script>
{/literal}