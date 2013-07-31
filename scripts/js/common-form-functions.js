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

_today = Ext.Date.format(new Date(),'m/d/Y');


Ext.define('Dropdown',
{
    extend:'Ext.data.Model',
    fields:[{name:'code'},{name:'desc'}]
});



function dropDownStore(appcode, subappcode)
{
    return Ext.create('Ext.data.ArrayStore',
    {
        model:'Dropdown',
        proxy :
        {
            type:'ajax',
            url:'?_page=lookUp&_action=getGenDtl&appcode='+appcode+'&subappcode='+subappcode,
            reader:{root:'data'}
        },
        autoLoad:false
    });
}


function requestTypeStore()
{
    return Ext.create('Ext.data.ArrayStore',
    {
        model:'Dropdown',
        proxy:
        {
            type:'ajax',
            url:'?_page=lookUp&_action=getRequestTypes',
            reader:{root:'data'}
        },
        autoLoad:false
    });
}

function getEmployeeDetails(badge_no)
{
    var wait_box = Ext.Msg.wait('Loading Employee Info...','e-Request');


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

function getLastRequestDate(request_code,item_code)
{
    var wait_box = Ext.Msg.wait('Getting last request date...','e-Request');

    var request = $.ajax({
        url:'?_page=lookUp&_action=getLastRequestDate',
        method:'post',
        async:false,
        data:{
            requestcode : request_code,
            itemcode   : item_code
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

    return response.last_request_date;
}


function fillFormValue()
{

    setCmpValue('requestor_branch_code', branchcode);
    setCmpValue('requestor_branch', branch);
    setCmpValue('requestor_badge_no', badgeno);
    setCmpValue('requestor_name', fullname);
    setCmpValue('requestor_position', position_desc);
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
        first_invalid_field.focus();
        return false;
    }
    

    if(form.isValid())
    {

        form.submit({
            url:'?_page=RequestFile&_action='+module_method,
            method:'POST',
            waitMsg:'Sending request...',
            success:function(form, action)
            {
                Ext.Msg.alert('e-Request', action.result.message,function(){window.location = '?_page=user&_action=homepage';});
            },
            failure:function(form, action)
            {
                Ext.Msg.alert('e-Request', action.result.errormsg);
            }
        });
    }
}

var submit_btn = 
{
    text:'Submit',
    id : 'submit-btn',
    iconCls:'submit-icon'
};

var clear_btn =
{
    text:'Clear',
    id:'clear_btn',
    iconCls:'erase2-icon',
    handler:function()
    {
        this.findParentByType('form').getForm().reset();
    }
};

var cancel_btn =
{
    text:'Cancel',
    id:'cancel_btn',
    iconCls:'close-icon',
    handler:function()
    {
        window.location = '?page=user&action=homepage';
    }
};

var approve_btn =
{
    text: 'Approved',
    id :'approve-btn',
    name:'approve',
    iconCls:'approve-icon'
};

var disapprove_btn =
{
    text: 'Disapproved',
    name:'disapprove',
    id :'disapprove_btn',
    iconCls:'disapprove-icon'
};
var back_btn =
{
    text:'Back to List',
    id:'back-btn',
    name:'back',
    iconCls:'back-icon'  
};