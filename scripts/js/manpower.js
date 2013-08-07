//author : atibus
//date   : 08/02/2013

Ext.require([
    'Ext.form.*',
    'Ext.panel.*',
    'Ext.tip.*'
]);

gender_store = [['M','Male'],['F','Female']];



replacement_combo = {
    xtype:'combobox',
    fieldLabel:'Due to the',
    name:'replacement_to',
    displayField:'desc',
    valueField:'code',
    store:dropDownStore('getgenericlookup','EMRF','REPLACE'),
    allowBlank:false,
    labelWidth  :75,
    width :230
};


special_project_fieldset = {
    title:'Special Project',
    items:
    [
        {
            xtype:'textarea',
            fieldLabel:'PROJECT DESCRIPTION',
            labelAlign:'top',
            width:880,
            name:'justification'
        }
    ]
};

reliever_fieldset = {
    title:'Reliever',
    defaultType:'panel',
    defaults:{
        layout:'column',
        border:false,
        frame:false,
        bodyStyle:'background-color:transparent;',
        defaultType:'textfield',
        defaults:{
            enableKeyEvents:true,
            style:'margin:0 25px 5px 0'
        }
    },
    items:
    [
        {
            items:
            [
                {
                    fieldLabel :'Badge No.',
                    name       :'employee_badge_no',
                    labelWidth :110,
                    width      :200,
                    listeners  :{
                        blur:function(){

                            var employee    = getEmployeeDetails(this.getValue());
                            Ext.getCmp('employee_name').setValue(employee.data.NAME);
                            Ext.getCmp('employee_position').setValue(employee.data.POSITIONDESC);
                        }
                    }
                },
                {
                    fieldLabel  :'Name',
                    name        :'employee_name',
                    id          :'employee_name',
                    labelWidth  :50,
                    width       :300
                },
                {
                    fieldLabel  :'Position',
                    name        :'employee_position',
                    id          :'employee_position',
                    labelWidth  :75,
                    width       :325
                },
                {
                    xtype      :'datefield',
                    fieldLabel :'<b>Duration of Leave:</b> From',
                    name       :'loa_from',
                    labelWidth :150,
                    width      :250
                },
                {
                    xtype      :'datefield',
                    fieldLabel :'To',
                    name       :'loa_to',
                    labelWidth :25,
                    width      :135
                }
            ]
        },
        {
            items:
            [
                {
                    xtype:'textarea',
                    fieldLabel:'REASON OF EXTENDED LEAVE',
                    name:'extended_leave_reason',
                    labelAlign:'top',
                    width:880
                }
            ]
        }      
    ]
};


additional_staff_fieldset = {
    title:'Additional Staff',
    id :'additional_staff_fieldset',
    layout:'column',
    items:
    [
        {
            xtype:'radiogroup',
            fieldLabel:'Within Plantilla',
            labelWidth:90,
            width:200,
            items:
            [
                {
                    boxLabel:'Yes',
                    inputValue:1,
                    name:'within_platilla'

                },
                {
                    boxLabel:'No',
                    inputValue:0,
                    name:'within_platilla'
                }
            ],
            listeners:{
                change:function()
                {
                    var plantilla_val   = this.getValue().within_platilla;
                    var my_fieldset     = this.findParentByType('fieldset');

                    if(plantilla_val==0)
                    {
                        getFileRequirements();
                    }
                    else if(my_fieldset.getComponent(3))
                    {
                        my_fieldset.getComponent(3).destroy(true); // remove justification text area
                    }
                    else
                    {
                        return;
                    }
                }
            }
        },
        {
            xtype:'textarea',
            name:'justification',
            emptyText:'Please input Remarks/Justification here.',
            labelAlign:'top',
            width:880
        }
    ]
}


permanent_position_fieldset = {
    title   :'Permanent Position',
    items   :
    [
        {
            fieldLabel  :'Position Title',
            name        :'position_title',
            id          :'position_title',
            forceSelection:false,
            store       :positionStore(),
            queryMode   :'local',
            displayField:'desc',
            valueField  :'code',
            width       :340,
            labelWidth  :90
        },
        {
            fieldLabel  :'Rank/Level',
            name        :'rank',
            store       :dropDownStore('getgenericlookup','EMRF','RANKP'),
            displayField:'desc',
            valueField  :'code',
            labelWidth  :75,
            width       :230
        }
    ]
};

temporary_position_fieldset = {
    title   :'Temporary Position',
    items   :
    [
        {
            fieldLabel  :'Position Title',
            name        :'position_title',
            id          :'position_title',
            store       :positionStore(),
            queryMode   :'local',
            displayField:'desc',
            valueField  :'code',
            labelWidth  : 90,
            width       :340
        },
        {
            fieldLabel  :'Rank/Level',
            name        :'rank',
            store       :dropDownStore('getgenericlookup','EMRF','RANKT'),
            displayField:'desc',
            valueField  :'code',
            labelWidth  :75,
            width       :230
        },
        {
            fieldLabel  :'Employment Status',
            name        :'employment_status',
            id          :'employment_status',
            width       : 260,
            labelWidth  : 125,
            store       :dropDownStore('getgenericlookup','EMRF','EMPSTAT'),
            displayField:'desc',
            valueField  :'code'
        },
        {
            xtype       :'numberfield',
            fieldLabel  :'Duration of Contract',
            name        :'duration_of_contract',
            labelWidth  :120,
            width       :200,
            minValue    :1
        }
    ]
};


Ext.onReady(function(){

    request_form = Ext.create('Ext.form.FormPanel',{
        autoWidth   :true,
        autoHeight  :true,
        border      :false,
        fileUpload  :true,
        frame       :false,
        renderTo    :'request-form-container',
        defaultType :'panel',
        defaults    :{
            autoHeight  :true,
            autoWidth   :true,
            padding     :'10px 10px 10px 10px;',
            frame       :true,
            border      :true,
            defaultType :'fieldset',
            defaults    :{
                layout:'column',
                defaultType:'combobox',
                defaults:{
                    width       :350,
                    labelWidth  :150,
                    enableKeyEvents:true,
                    style:'margin:0 25px 5px 0',
                    allowBlank:false
                }
            }
        },
        items:
        [
            {
                title   :'REQUEST DETAILS',
                padding :'10px 20px 10px 20px',
                xtype   :'panel',
                layout  :'column',
                defaults:{style:'margin:0 25px 5px 0',allowBlank  :false},
                items   :
                [
                    {
                        xtype       :'combobox',
                        fieldLabel  :'Company',
                        name        :'company',
                        store       :dropDownStore('getcompanies'),
                        invalidCls  :'invalid-field',
                        displayField:'desc',
                        valueField  :'code',
                        labelWidth  :100,
                        width       :350
                    },
                    {
                        xtype       :'combobox',
                        fieldLabel  :'Department',
                        name        :'department',
                        store       :departmentStore('getdepartments'),
                        queryMode   :'local',
                        displayField:'desc',
                        valueField  :'code',
                        labelWidth  :75
                    },
                    {
                        xtype       :'numberfield',
                        fieldLabel  :'No. of Bodies Needed',
                        name        :'needed_number',
                        labelWidth  :125,
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
                    {
                        xtype       :'combobox',
                        fieldLabel  :'Position Type',
                        name        :'position_type',
                        id          :'position_type',
                        store       :[[1,'Permanent'],[2,'Temporary']],
                        allowBlank  :false,
                        width       :350,
                        labelWidth  :100
                    },
                    {
                        xtype:'box',
                        id :'temp'
                    }
                ]
            },
            {
                title   :'Reason of Request',
                id      :'request_panel',
                layout  :'column',
                padding:'10px 20px 10px 20px',
                items   :
                [
                    {
                        xtype       :'combobox',
                        fieldLabel  :'Reason of Request',
                        name        :'reason_of_request',
                        id          :'reason_of_request',
                        emptyText   :'Please Select',
                        style       :'margin:0 25px 25px 0',
                        allowBlank  :false,
                        labelWidth  :125,
                        width       :350,
                        store       :dropDownStore('getgenericlookup','EMRF','REASON'),
                        displayField:'desc',
                        valueField  :'code'
                    },
                    {
                        hidden:true // joke lang to mejo. pero pag tinangal magkakagulo
                    },
                    {
                        xtype:'textarea',
                        fieldLabel:'JOB DESCRIPTION',
                        labelAlign:'top',
                        name:'job_description',
                        id  :'job_description',
                        width:890,
                        allowBlank:false
                    },
                    {
                        title:'Job Specification',
                        defaultType:'textarea',
                        defaults:{
                            style:'margin:0 20px 10px 0',
                            labelAlign:'top',
                            width:880,
                            allowBlank  :false
                        },
                        items:
                        [
                            {
                                xtype:'combobox',
                                fieldLabel:'Educational Attainment',
                                name:'education_attainment',
                                width: 430,
                                store:dropDownStore('getgenericlookup','EMRF','EDUCA'),
                                displayField:'desc',
                                valueField  :'code'
                            },
                            {
                                xtype:'mytextfield',
                                fieldLabel:'Education Preferred',
                                name:'preferred_education',
                                width: 430
                            },
                            {
                                fieldLabel:'WORK EXPERIENCE',
                                name:'work_experience'
                            },
                            {
                                fieldLabel:'SKILLS REQUIRED',
                                name:'required_skills'
                            },
                            {
                                fieldLabel:'MACHINE OPERATION SKILL REQUIRED',
                                name:'machine_skill'
                            },
                            {
                                fieldLabel:'SOFTWARE/PROGRAMMING SKILLS',
                                name:'software_skill'
                            },
                            {
                                fieldLabel:'OTHER QUALIFICATION',
                                name:'other_qualification'
                            },
                            {
                                xtype:'panel',
                                frame:false,
                                border:false,
                                layout:'column',
                                bodyStyle:'background-color:transparent;',
                                defaults:{allowBlank  :false},
                                items:
                                [
                                    {
                                        xtype:'numberfield',
                                        fieldLabel:'<b>AGE RANGE:</b> &emsp;From',
                                        name:'age_from',
                                        labelWidth:125,
                                        width:200
                                    },
                                    {
                                        width:125,
                                        labelWidth:50,
                                        labelAlign:'right',
                                        xtype:'numberfield',
                                        fieldLabel:'To',
                                        name:'age_to'
                                    },
                                    {
                                        labelWidth:85,
                                        labelAlign:'right',
                                        xtype:'combobox',
                                        fieldLabel:'Gender',
                                        name:'gender',
                                        store:gender_store
                                    },
                                    {
                                        labelAlign:'right',
                                        xtype:'combobox',
                                        fieldLabel:'Salary range',
                                        name:'salary_range',
                                        width : 315,
                                        store       :dropDownStore('getgenericlookup','EMRF','SALRANGE'),
                                        displayField:'desc',
                                        valueField  :'code'
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
                                value:fullname,
                                labelWidth:100,
                                width:300
                            },
                            {
                                fieldLabel:'<b>Position</b>',
                                value:position_desc,
                                labelWidth:75,
                                width:365
                            },
                            {
                                xtype:'datefield',
                                readOnly:true,
                                fieldLabel:'<b>Request date</b>',
                                name:'filedate',
                                value:_today,
                                labelWidth:100,
                                width:200
                            }
                        ]
                    }
                ],
                buttonAlign:'center',
                buttons:
                [
                    submit_btn, clear_btn, cancel_btn
                ]
            }
        ]
    });

    // LISTENERS 
    var position_type           = Ext.getCmp('position_type'),
    reason_of_request           = Ext.getCmp('reason_of_request'),
    request_panel               = Ext.getCmp('request_panel'),
    position_detail_panel       = Ext.getCmp('position_detail_panel');


    position_type.on('collapse',function()
    {
        var my_val = this.getValue();

        position_detail_panel.remove(1);

        if(my_val == 1)
        {
            position_detail_panel.add(permanent_position_fieldset);
        }
        else if(my_val == 2)
        {
            position_detail_panel.add(temporary_position_fieldset);
        }
        else
        {
            return;
        }


        //add listener to position title after render... 
        //get job description by position title code.. 
        // and set job description textarea value;
        var position_title_cmp = Ext.getCmp('position_title');

        position_title_cmp.on('collapse',function()
        {
            var title_val   = this.getValue();
            var job_desc    = (title_val) ? getJobDescriptions(title_val) : '';

            Ext.getCmp('job_description').setValue(job_desc);
        });

        position_title_cmp.on('focus',function(){this.expand();});

    });


    reason_of_request.on('collapse', function()
    {
        var my_val = this.getValue();
        request_panel.remove(1,true);
        if(my_val == 'REASON1')
        {
            request_panel.insert(1,replacement_combo);
        }
        else if(my_val == 'REASON2')
        {
            request_panel.insert(1,reliever_fieldset);
        }
        else if(my_val == 'REASON3')
        {
            request_panel.insert(1,special_project_fieldset);
        }
        else if(my_val == 'REASON4')
        {
            request_panel.insert(1,additional_staff_fieldset);
        }
        else
        {
            return;
        }
    });

    Ext.getCmp('submit_btn').on('click',function(){
        submitRequestForm('sendMRF');
    });


});


function getFileRequirements()
{
    var wait_box = Ext.Msg.wait('Loading requirements...','e-Request');

    Ext.Ajax.on('requestcomplete',function(){wait_box.hide();});
    Ext.Ajax.on('requestexception',function(){wait_box.hide();})

    Ext.Ajax.request({
        url:'?_page=lookUp&_action=getRequirements',
        method:'POST',
        callback:function(success, opt, result)
        {
            wait_box.hide();
            var response = Ext.JSON.decode(result.responseText);

            if(response.success)
            {
                var additional_staff_fieldset = Ext.getCmp('additional_staff_fieldset');
                var requirements = response.data;
                var req_len      = requirements.length;
                for(var i=0; i<req_len; i++)
                {
                    var desc        = requirements[i].DESCRIPTION;
                    var req_name    = requirements[i].STREQUIREID;
                    var allow_blank = (requirements[i].ISREQUIRED == 1) ? false : true;

                    additional_staff_fieldset.add({
                        xtype       :'filefield',
                        fieldLabel  :desc, 
                        name        :req_name,
                        id          :req_name, 
                        allowBlank  :allow_blank,
                        width       :880,
                        labelWidth  :250
                    });
                }
            }
            else
            {
                messageBox(response.errormsg,Ext.getBody(),Ext.MessageBox.WARNING);
            }
        }
    })
}
