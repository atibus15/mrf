
// Ext.WindowMgr.zseed = 50000;



function messageBox(text,focus_el,icon){

    if(!icon){
        icon = Ext.MessageBox.INFO;
    }
    if(!focus_el)
    {
        focus_el = document.body;
    }

    Ext.MessageBox.show({
        title   : 'e-Request',
        msg     : text,
        width   : 300,
        buttons : Ext.MessageBox.OK,
        icon    : icon,
        animEl  : document.body,
        fn:function()
        {
            focus_el.focus();
        }
    })
}


function messageRedirect(text,location,icon){

    if(!location){
        location = 'index.php';
    }
    if(!icon){
        icon = Ext.MessageBox.INFO;
    }
    Ext.MessageBox.show({
        title   : 'e-Request',
        msg     : text,
        width   : 300,
        buttons : Ext.MessageBox.OK,
        icon    : icon,
        animEl  : document.body,
        fn:function()
        {
            window.location = location;
        }
    })
}

function successmessage(text){
    $.n.success(text);
}
function warningmessage(text){
    $.n.warning(text);
}
function message(text){
    $.n(text);
}
function errormessage(text){
    $.n.error(text);
}

function setCmpValue(cmp_id, value)
{
    Ext.getCmp(cmp_id).setValue(value);
}