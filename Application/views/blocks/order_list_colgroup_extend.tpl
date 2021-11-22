<script>

    var styles = `
    .ORDERFOLDER_NEW
    {
        color: blue;
    }

    .ORDERFOLDER_FINISHED
    {
        color: green;
    }

    .ORDERFOLDER_PROBLEMS
    {
        color: red;
    }`

    var styleSheet = document.createElement("style")
    styleSheet.type = "text/css"
    styleSheet.innerText = styles
    document.head.appendChild(styleSheet)
</script>


<col width="10%">
<col width="12%">
[{$smarty.block.parent}]