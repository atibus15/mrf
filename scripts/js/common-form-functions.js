//author: atibus
//date :07/19/2013
//IDE :sublime text 2
//control + alt + shift + m to minify script if doesnt work download jsminifier.
Ext.require(['Ext.data.*']);


var userid      = Ext.get('userid').getValue(),
badgeno         = Ext.get('badgeno').getValue(),
lastname        = Ext.get('lastname').getValue(),
firstname       = Ext.get('firstname').getValue(),
middlename      = Ext.get('middlename').getValue(),
fullname        = Ext.get('name').getValue(),
locationcode    = Ext.get('locationcode').getValue(),
emailaddress    = Ext.get('emailaddress').getValue(),
bucode          = Ext.get('bucode').getValue(),
sapbucode       = Ext.get('sapbucode').getValue(),
budesc          = Ext.get('budesc').getValue(),
deptcode        = Ext.get('deptcode').getValue(),
groupcode       = Ext.get('groupcode').getValue(),
positioncode    = Ext.get('positioncode').getValue(),
hiredate        = Ext.get('hiredate').getValue(),
branchcode      = Ext.get('branchcode').getValue(),
birthdate       = Ext.get('birthdate').getValue(),
department_code = Ext.get('departmentcode').getValue(),
emp_status_code = Ext.get('empstatuscode').getValue(),
position_desc   = Ext.get('positiondesc').getValue(),
branch          = Ext.get('branchdesc').getValue(),
department_desc = Ext.get('departmentdesc').getValue();

_today          = Ext.Date.format(new Date(),'m/d/Y');

_fdom           = Ext.Date.getFirstDateOfMonth(new Date());
_ldom           = Ext.Date.getLastDateOfMonth(new Date());

_first_date_month= Ext.Date.format(_fdom, 'm/d/Y');
_last_date_month = Ext.Date.format(_ldom,'m/d/Y');

Ext.define('Dropdown',
{
    extend:'Ext.data.Model',
    fields:[{name:'code'},{name:'desc'}]
});



function dropDownStore(action,app_code, sub_code)
{
    Ext.Ajax.on('requestcomplete',function(success, result){
        var response = Ext.JSON.decode(result.responseText);
        if(!response.success)
        {
            messageBox(response.errormsg);
        }
    });

    return Ext.create('Ext.data.ArrayStore',
    {
        model:'Dropdown',
        proxy :
        {
            type:'ajax',
            url:'?_page=lookUp&_action='+action+'&app_code='+app_code+'&sub_code='+sub_code,
            reader:{root:'data'}
        },
        autoLoad:false
    });
}



function getJobDescriptions(position_code)
{
    var wait_box = Ext.Msg.wait('Loading Job Description...','e-MRF');

    var request = $.ajax({
        url:'?_page=lookUp&_action=getjobdescriptions',
        method:'post',
        async:false,
        data:{
            position_code:position_code
        },
        success:function()
        {
            wait_box.hide();
        },
        failure:function()
        {
            wait_box.hide();
        }
    });

    var response = $.parseJSON(request.responseText);

    if(!response.success)
    {
        messageBox(response.errormsg);
        return false;
    }
    
    return response.data;
}


// not asyncronous enable auto suggest on dropdown if typeAhead
function positionStore()
{
    var request = $.ajax({
        url:'?_page=lookUp&_action=getpositions',
        method:'post',
        async:false
    });

    var response = $.parseJSON(request.responseText);

    if(!response.success)
    {
        messageBox(response.errormsg);
        return false;
    }
    
    return Ext.create('Ext.data.Store',
    {
        model:'Dropdown',
        data:response.data,
        autoLoad:true
    });
}

function departmentStore()
{
    var request = $.ajax({
        url:'?_page=lookUp&_action=getdepartments',
        method:'post',
        async:false
    });

    var response = $.parseJSON(request.responseText);

    if(!response.success)
    {
        messageBox(response.errormsg);
        return false;
    }
    
    return Ext.create('Ext.data.Store',
    {
        model:'Dropdown',
        data:response.data,
        autoLoad:true
    });
}


function getEmployeeDetails(badge_no)
{
    var wait_box = Ext.Msg.wait('Loading Employee Info...','e-MRF');

    var request = $.ajax({
        url:'?_page=employee&_action=getEmployeeDetails',
        method:'post',
        async:false,
        data:{
            badgeno:badge_no
        },
        success:function()
        {
            wait_box.hide();
        },
        failure:function()
        {
            wait_box.hide();
        }
    });

    var response = $.parseJSON(request.responseText);

    if(!response.success)
    {
        messageBox(response.errormsg);
        return false;
    }
    
    return response;
}

Ext.define('MY.custom.TextField',
{
    extend:'Ext.form.TextField',
    alias:'widget.mytextfield',
    initComponent:function()
    {
        Ext.apply(this,{
            enableKeyEvents:true,
            listeners:{
                keyup:function()
                {
                    this.setValue(this.getValue().toUpperCase());
                }
            }
        });
        this.callParent(arguments);
    }
});



function submitRequestForm(module_method)
{
    var form = request_form.getForm();

    var form_fields = form._fields;

    var first_invalid_field = false;

    form_fields.each(function(field){
        if(!field.isValid())
        {
            first_invalid_field = field;
            return false;
        }
    })
    if(first_invalid_field)
    {
        first_invalid_field.fireEvent('mouseover');
        first_invalid_field.focus();
        return false;
    }
    

    if(form.isValid())
    {

        form.submit({
            url:'?_page=request&_action='+module_method,
            method:'POST',
            waitMsg:'Sending request...',
            success:function(form, action)
            {
                Ext.Msg.alert('MRF', action.result.message,function(){window.location = '?_page=user&_action=homepage';});
            },
            failure:function(form, action)
            {
                Ext.Msg.alert('MRF', action.result.errormsg);
            }
        });
    }
}

submit_btn = 
{
    text:'Submit',
    id : 'submit_btn',
    iconCls:'submit-icon'
};

clear_btn =
{
    text:'Clear',
    id:'clear_btn',
    iconCls:'erase2-icon',
    handler:function()
    {
        this.findParentByType('form').getForm().reset();
    }
};

cancel_btn =
{
    text:'Cancel',
    id:'cancel_btn',
    iconCls:'close-icon',
    handler:function()
    {
        window.location = '?page=user&action=homepage';
    }
};

approve_btn =
{
    text: 'Approved',
    id :'approve_btn',
    name:'approve',
    iconCls:'approve-icon'
};

disapprove_btn =
{
    text: 'Disapproved',
    name:'disapprove',
    id :'disapprove_btn',
    iconCls:'disapprove-icon'
};
back_btn =
{
    text:'Back to List',
    id:'back_btn',
    name:'back',
    iconCls:'back-icon'  
};