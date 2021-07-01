[{$smarty.block.parent}]
<script>

    function login(){
        var oForm = document.getElementById('myedit');
        oForm.fnc.value = 'login';
        oForm.submit();
    }

</script>

[{if $oxid != "-1"}]
<br>
<input type="button" value="Login" class="edittext pt-3" onclick="login()" style="margin-top: 1rem">
    [{/if}]