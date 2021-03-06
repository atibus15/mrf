// Author : atibus
// Date   : 08/06/2013
// Desc   : REQUEST PROCESSING FORM and detail viewing FOR ADMIN.

Ext.require([
    'Ext.panel.*',
    'Ext.grid.*',
    'Ext.form.*',
    'Ext.PagingToolbar'
]);


//####################### MODEL HERE ########################//

Ext.define('Request',
{
    extend:'Ext.data.Model',
    fields:
    [
        {name:'EMRFID'},
        {name:'FILEDATE'},
        {name:'BADGENO'},
        {name:'LASTNAME'},
        {name:'FIRSTNAME'},
        {name:'MIDDLENAME'},
        {name:'NAMESUFFIX'},
        {name:'FULLNAME'},
        {name:'HIREDATE'},
        {name:'BUCODE'},
        {name:'BUDESC'},
        {name:'EMPSTATUSCODE'},
        {name:'EMPSTATUSDESC'},
        {name:'DEPARTMENTCODE'},
        {name:'DEPARTMENTDESC'},
        {name:'BRANCHCODE'},
        {name:'BRANCHDESC'},
        {name:'POSITIONCODE'},
        {name:'POSITIONDESC'},
        {name:'EMPRANKCODE'},
        {name:'EMPRANKDESC'},
        {name:'MRFCOMPANY'},
        {name:'MRFDEPT'},
        {name:'NUMOFBODIES'},
        {name:'POSITIONTYPE'},
        {name:'MRFPOSITIONCODE'},
        {name:'MRFRANK'},
        {name:'DURATIONMOS'},
        {name:'EMPLOYSTATUS'},
        {name:'REQREASONCODE'},
        {name:'REPLACECODE'},
        {name:'RELIEVERBADGENO'},
        {name:'RELIEVERNAME'},
        {name:'RELIEVERPOSITION'},
        {name:'RELIEVERLOAFR'},
        {name:'RELIEVERLOATO'},
        {name:'RELIEVERLOAREASON'},
        {name:'ISINPLANTILLA'},
        {name:'DESCRIBEJUSTIFYTEXT'},
        {name:'JOBDESC'},
        {name:'EDUCATTAINED'},
        {name:'EDUCPREFERRED'},
        {name:'WORKEXPERIENCE'},
        {name:'SKILLSREQ'},
        {name:'MACHINESKILLS'},
        {name:'SOFTWARESKILLS'},
        {name:'OTHERQUALS'},
        {name:'AGEFROM'},
        {name:'AGETO'},
        {name:'GENDER'},
        {name:'SALARYRANGE'},
        {name:'REMARKS'},
        {name:'ISAPPROVED'},
        {name:'APPROVEDBY'},
        {name:'APPROVEDDATE'},
        {name:'CANDIDATEHIREDATE'},
        {name:'ENDORSEDDATE'},
        {name:'CANDIDATENAME'},
        {name:'MRFBUDESC'},
        {name:'MRFDEPARTMENTDESC'},
        {name:'MRFPOSITIONDESC'},
        {name:'MRFRANKDESC'},
        {name:'MRFREASONDESC'},
        {name:'REPLACEREASON'},
        {name:'ATTAINMENTDESC'},
        {name:'SALARYDESC'},
        {name:'STATUS'}
    ]
});


//###################### END MODEL DECLARATION ###############//


//######################### STORE HERE ########################//

request_grid_store = Ext.create('Ext.data.Store',
{
    pageSize:15,
    model:'Request',
    proxy :
    {
        type:'ajax',
        url:'index.php?_page=request&_action=getrequestlist',
        reader:
        {
            type:'array',
            root:'data',
            totalProperty:'totalrequest'
        }
    },
    autoLoad:true
});

//#################### END STORE DECLARATION #############################//



//##################### VIEW COMPONENT HERE ############################////

request_grid = 
Ext.create('Ext.grid.Panel',
{
    title:'MRF List',
    store:request_grid_store,
    autoWidth:true,
    forceFit:true,
    height:500,
    tbar:
    [
        {
            xtype:'button',
            text:'<b>View Details</b>',
            autoWidth:true,
            iconCls:'view-icon',
            handler:function()
            {
                showRequestDetails();
            }
        },
        {
            id:'status',
            xtype:'combobox',
            store:[['P','Pending'],['A','Approved'],['D','Disapproved']],
            triggerAction:'all',
            editable:false,
            fieldLabel:'Status',
            value:'P',
            labelWidth:50,
            width:175
        },
        {
            id:'date_start',
            name:'date_start',
            xtype:'datefield',
            fieldLabel:'Date From',
            maxValue:_today,
            editable:false,
            value:_first_date_month,
            labelWidth:65,
            width:200
        },
        {
            id:'date_end',
            name:'date_end',
            xtype:'datefield',
            fieldLabel:'Date To',
            editable:false,
            maxValue:_today,
            value:_last_date_month,
            labelWidth:60,
            width:200
        },
        {
            xtype:'button',
            text:'<b>Show</b>',
            id:'show_btn',
            style:'margin-top:8px;',
            iconCls:'search-icon',
            autoWidth:true
        }

    ],
    columns:
    [
        {text:'File Date',      dataIndex:'FILEDATE', width:100},
        {text:'Position Type',   dataIndex:'POSITIONTYPE', width:100,renderer:function(type_no){return (type_no == 1) ? 'Permanent' : 'Temporary';}},
        {text:'Position Title',  dataIndex:'MRFPOSITIONDESC',width:250},
        {text:'Rank/Level', dataIndex:'MRFRANKDESC',width:100},
        {text:'Reason of Request',    dataIndex:'MRFREASONDESC', width:250}
    ],
    listeners:{
        itemdblclick:function(){
            showRequestDetails();
        }
    },
    bbar:Ext.create('Ext.PagingToolbar', {
        store:request_grid_store,
        displayInfo: true,
        displayMsg: 'Displaying systems {0} - {1} of {2}',
        emptyMsg: "No system to display"
    })
});


//################### END OF VIEW COMP DECLARATION##############//


//#################### VIEW LAYOUT ############################//
Ext.onReady(function()
{
    request_grid.render('request-form-container');

    reloadGridStore();

    Ext.getCmp('show_btn').on('click',function()
    {
        reloadGridStore();
    });

});
//#################### END VIEW LAYOUTING ####################//

function reloadGridStore()
{
    request_grid_store.proxy.setExtraParam('status'      ,Ext.getCmp('status').getValue());
    request_grid_store.proxy.setExtraParam('date_start'  ,Ext.getCmp('date_start').getValue());
    request_grid_store.proxy.setExtraParam('date_end'    ,Ext.getCmp('date_end').getValue());

    request_grid.store.load();
}


function showRequestDetails()
{
    var selection = request_grid.getSelectionModel().getSelection();

    if(!selection.length)
    {
        messageBox('Please select record.');
        return false;
    }
    request_grid.hide();

    var selected = selection[0];
    var position_type   = selected.get('POSITIONTYPE');
    var reason_code     = selected.get('REQREASONCODE');
    var request_id      = selected.get('EMRFID');
    var status          = selected.get('STATUS');

    var wait_box = Ext.Msg.wait('Loading Request Details...','e-MRF');

    var replacement_info = {
        xtype:'displayfield',
        fieldLabel:'Replacement due to the',
        labelStyle:'font-weight:bold;',
        value:selected.get('REPLACEREASON'),
        name:'replacement_to',
        labelWidth  :300,
        width :820
    };

    var special_project_fieldset = 
    {
        title:'Special Project',
        items:
        [
            {
                xtype:'displayfield',
                fieldLabel:'PROJECT DESCRIPTION',
                value:selected.get('DESCRIBEJUSTIFYTEXT'),
                labelAlign:'top',
                width:880
            }
        ]
    };

    var reliever_fieldset = 
    {
        title:'Reliever',
        defaultType:'panel',
        defaults:{
            layout:'column',
            border:false,
            frame:false,
            bodyStyle:'background-color:transparent;',
            defaultType:'displayfield',
            defaults:{labelStyle:'font-weight:bold;'}
        },
        items:
        [
            {
                items:
                [
                    {
                        fieldLabel :'Badge No.',
                        name       :'employee_badge_no',
                        value:selected.get('RELIEVERBADGENO'),
                        labelWidth :110,
                        width      :200
                    },
                    {
                        fieldLabel  :'Name',
                         value:selected.get('RELIEVERNAME'),
                        labelWidth  :50,
                        width       :300
                    },
                    {
                        fieldLabel  :'Position',
                        value:selected.get('RELIEVERPOSITION'),
                        id          :'employee_position',
                        labelWidth  :75,
                        width       :325
                    },
                    {
                        fieldLabel :'Duration of Leave: From',
                        value:selected.get('RELIEVERLOATO'),
                        labelWidth :175,
                        width      :250
                    },
                    {
                        fieldLabel :'To',
                        value:selected.get('RELIEVERLOAFR'),
                        labelWidth :25,
                        width      :135
                    }
                ]
            },
            {
                items:
                [
                    {
                        xtype:'displayfield',
                        fieldLabel:'REASON OF EXTENDED LEAVE',
                        name:'extended_leave_reason',
                        value:selected.get('RELIEVERLOAREASON'),
                        labelAlign:'top',
                        width:880
                    }
                ]
            }      
        ]
    };


    var additional_staff_fieldset = {
        title:'Additional Staff',
        id :'additional_staff_fieldset',
        layout:'column',
        items:
        [
            {
                xtype:'displayfield',
                fieldLabel:'Within Plantilla',
                value:selected.get('ISINPLANTILLA') ? 'Yes' : 'No',
                labelWidth:100,
                width:200
            },
            {
                xtype:'displayfield',
                fieldLabel:'Justification',
                value:selected.get('DESCRIBEJUSTIFYTEXT'),
                labelAlign:'top',
                width:880
            }
        ]
    }


    var permanent_position_fieldset = {
        title   :'Permanent Position',
        items   :
        [
            {
                fieldLabel  :'Position Title',
                name        :'position_title',
                value:selected.get('MRFPOSITIONDESC'),
                width       :340,
                labelWidth  :90
            },
            {
                fieldLabel  :'Rank/Level',
                name        :'rank',
                value:selected.get('MRFRANKDESC'),
                labelWidth  :75,
                width       :230
            }
        ]
    };

    var temporary_position_fieldset = {
        title   :'Temporary Position',
        items   :
        [
            {
                fieldLabel  :'Position Title',
                name        :'position_title',
                value:selected.get('MRFPOSITIONDESC'),
                labelWidth  : 90,
                width       :340
            },
            {
                fieldLabel  :'Rank/Level',
                name        :'rank',
                value:selected.get('MRFRANKDESC'),
                labelWidth  :75,
                width       :230
            },
            {
                fieldLabel  :'Employment Status',
                name        :'employment_status',
                value:selected.get('EMPLOYSTATUS'),
                width       : 200,
                labelWidth  : 125
            },
            {
                fieldLabel  :'Duration of Contract',
                name        :'duration_of_contract',
                value:selected.get('DURATIONMOS')
            }
        ]
    };

    var position_details_fieldset = (position_type == 1) ? permanent_position_fieldset : temporary_position_fieldset;

    var accessible_buttons = (status=='P') ? [approve_btn, disapprove_btn, back_btn] : [back_btn];

    var reason_of_request_fieldset;
    if(reason_code == 'REASON1')
    {
        reason_of_request_fieldset = replacement_info;
    }
    else if(reason_code == 'REASON2')
    {
        reason_of_request_fieldset = reliever_fieldset;
    }
    else if(reason_code == 'REASON3')
    {
        reason_of_request_fieldset = special_project_fieldset;
    }
    else
    {
        reason_of_request_fieldset = additional_staff_fieldset;
    }

    request_form = Ext.create('Ext.form.FormPanel',{
        autoWidth   :true,
        autoHeight  :true,
        border      :true,
        fileUpload  :true,
        frame       :true,
        title       :'REQUEST DETAILS',
        renderTo    :'request-form-container',
        defaultType :'fieldset',
        defaults    :{
            autoHeight  :true,
            autoWidth   :true,
            padding     :'10px 10px 10px 10px;',
            frame       :true,
            border      :true,
            defaultType :'fieldset',
            defaults    :{
                layout:'column',
                defaultType:'displayfield',
                defaults:{
                    width       :350,
                    labelWidth  :150,
                    style:'margin:0 25px 5px 0',
                    labelStyle:'font-weight:bold;'
                }
            }
        },
        items:
        [
            {
                padding :'10px 20px 10px 20px',
                layout  :'column',
                defaultType:'displayfield',
                defaults:{style:'margin:0 25px 5px 0',labelStyle:'font-weight:bold;'},
                items   :
                [
                    {
                        fieldLabel  :'Company',
                        name        :'company',
                        value:selected.get('MRFBUDESC'),
                        labelWidth  :100
                    },
                    {
                        fieldLabel  :'Department',
                        value:selected.get('MRFBUDESC'),
                        labelWidth  :75
                    },
                    {
                        fieldLabel  :'No. of Bodies Needed',
                        value:selected.get('NUMOFBODIES'),
                        name        :'needed_number',
                        labelWidth  :150,
                        width       :265
                    }
                ]
            },
            {
                title   :'Position Details',
                id      :'position_detail_panel',
                padding :'10px 20px 10px 20px',
                items   :
                [
                    position_details_fieldset
                ]
            },
            {
                title   :'Reason of Request',
                id      :'request_panel',
                layout  :'column',
                padding:'10px 20px 10px 20px',
                items   :
                [
                    reason_of_request_fieldset,
                    {
                        title:'JOB DESCRIPTION',
                        labelAlign:'top',
                        name:'job_description',
                        id  :'job_description',
                        html:selected.get('JOBDESC'),
                        width:890,
                        allowBlank:false
                    },
                    {
                        title:'Job Specification',
                        defaultType:'displayfield',
                        defaults:{
                            labelAlign:'top',
                            width:880,
                            labelStyle:'font-weight:bold;',
                            labelWidth:150
                        },
                        items:
                        [
                            {
                                labelAlign:'left',
                                fieldLabel:'Educational Attainment',
                                name:'education_attainment',
                                value:selected.get('ATTAINMENTDESC'),
                                width: 430
                            },
                            {
                                labelAlign:'left',
                                fieldLabel:'Education Preferred',
                                name:'preferred_education',
                                value:selected.get('EDUCPREFERRED'),
                                width: 430
                            },
                            {
                                fieldLabel:'WORK EXPERIENCE',
                                name:'work_experience',
                                value:selected.get('WORKEXPERIENCE')
                            },
                            {
                                fieldLabel:'SKILLS REQUIRED',
                                name:'required_skills',
                                value:selected.get('SKILLSREQ')
                            },
                            {
                                fieldLabel:'MACHINE OPERATION SKILL REQUIRED',
                                name:'machine_skill',
                                value:selected.get('MACHINESKILLS')
                            },
                            {
                                fieldLabel:'SOFTWARE/PROGRAMMING SKILLS',
                                name:'software_skill',
                                value:selected.get('SOFTWARESKILLS')
                            },
                            {
                                fieldLabel:'OTHER QUALIFICATION',
                                name:'other_qualification',
                                value:selected.get('OTHERQUALS')
                            },
                            {
                                xtype:'panel',
                                frame:false,
                                border:false,
                                layout:'column',
                                bodyStyle:'background-color:transparent;',
                                defaultType:'displayfield',
                                defaults:{labelStyle:'font-weight:bold;'},
                                items:
                                [
                                    {
                                        fieldLabel:'<b>AGE RANGE:</b> &emsp;From',
                                        name:'age_from',
                                        value:selected.get('AGEFROM'),
                                        labelWidth:125,
                                        width:200
                                    },
                                    {
                                        width:125,
                                        labelWidth:50,
                                        labelAlign:'right',
                                        fieldLabel:'To',
                                        value:selected.get('AGETO'),
                                        name:'age_to'
                                    },
                                    {
                                        labelWidth:85,
                                        labelAlign:'right',
                                        fieldLabel:'Gender',
                                        value:(selected.get('GENDER') == 'M') ? 'Male' : 'Female'
                                    },
                                    {
                                        labelAlign:'right',
                                        fieldLabel:'Salary range',
                                        value:selected.get('SALARYDESC'),
                                        width : 315
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        xtype:'panel',
                        frame:false,
                        border:false,
                        padding:'25px 0 25px 0',
                        bodyStyle:'background-color:transparent;',
                        layout:'column',
                        defaultType:'displayfield',
                        items:
                        [
                            {
                                fieldLabel:'<b>Requested by</b>',
                                value:selected.get('FULLNAME'),
                                labelWidth:100,
                                width:300
                            },
                            {
                                fieldLabel:'<b>Position</b>',
                                value:selected.get('POSITIONDESC'),
                                labelWidth:75,
                                width:365
                            },
                            {
                                fieldLabel:'<b>Request date</b>',
                                value:selected.get('FILEDATE'),
                                labelWidth:100,
                                width:200
                            }
                        ]
                    }
                ]
            }
        ],
        buttonAlign :'center',
        buttons     :accessible_buttons
    });
    

    Ext.getCmp('back_btn').on('click',function(){
        request_grid.show();
        this.findParentByType('form').destroy();
    });

    if(typeof(Ext.getCmp('approve_btn'))!=='undefined')
    {
        Ext.getCmp('approve_btn').on('click',function(){
            updateRequest(request_id,'A');
        });
    }

    if(typeof(Ext.getCmp('disapprove_btn'))!=='undefined')
    {
        Ext.getCmp('disapprove_btn').on('click',function(){
            updateRequest(request_id,'D');
        }); 
    }

    wait_box.hide();
}

// params : action_code = array('A','D');
// request_id = EMRFID value;
function updateRequest(request_id, action_code)
{
    var wait_box = Ext.Msg.wait('Processing request...','e-MRF');

    Ext.Ajax.request({
        url:'?_page=route&_action=updaterequest',
        method:'POST',
        params:{
            request_id:request_id,
            action_code:action_code
        },
        callback:function(option, success, result)
        {
            wait_box.hide();
            var response = Ext.JSON.decode(result.responseText);

            if(response.success)
            {
                Ext.MessageBox.show({
                    title   : 'e-MRF',
                    msg     : response.message,
                    buttons : Ext.MessageBox.OK,
                    icon    : Ext.MessageBox.INFO,
                    animEl  : document.body,
                    fn:function()
                    {
                        request_grid.show();
                        request_form.destroy();
                        reloadGridStore();
                    }
                });
            }
            else
            {
                messageBox(response.errormsg);
                return false;
            }
        }
    });
}