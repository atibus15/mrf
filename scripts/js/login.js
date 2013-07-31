$(document).ready(function(){
    $('#login-btn').click(function(){
        authenticateUser();
    });
    $('#password').keydown(function (e){
    if(e.keyCode == 13){
        authenticateUser();
    }
})
})


function authenticateUser(){
   var waitBox =  Ext.MessageBox.show({
          msg          : 'Connecting...',
          progressText : 'Verifying user account...',
          width        : 250,
          wait         : true,
          waitConfig   : {interval:200}
   });

    Ext.Ajax.on('requestcomplete',function(conn,o,result){
        waitBox.hide();
    })

    Ext.Ajax.on('requestexception',function(conn,o,result){
        waitBox.hide();
    });
    Ext.Ajax.request({
        url     : '?_page=user&_action=authenticateUser',
        method  : 'POST',
        params  : 
        {
            username: Ext.get('username').getValue(),
            password: Ext.get('password').getValue()
        },
        success : function(result){
                    var response = Ext.JSON.decode(result.responseText);
                    
                    if(!response.success){
                        Ext.MessageBox.show({
                            title   : 'e-Request.',
                            msg     : response.errormsg,
                            buttons : Ext.MessageBox.OK,
                            icon    : Ext.MessageBox.WARNING,
                            animEl  : Ext.getBody()
                        });
                    }
                    else
                    {
                        window.location = response.page.redirect;
                    }
        },
        failure     : function(){}
    });
}
