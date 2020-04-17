jQuery(document).ready(function(){

    var wcssc_share = {

        button_id: '#wcssc-share-cart',
        sharebox_div: '#wcssc-share-box',

        show_sharebox: function () {

            jQuery(this.sharebox_div).dialog({
                modal: true,
                draggable: false,
                resizable: false,

                open: function (event, ui) {
                    wcssc_share.open()
                },

                close: function () {
                    wcssc_share.close()
                }
            })
        },

        add_loader: function () {
            jQuery(wcssc_share.sharebox_div).html(wcssc_vars.loader).dialog({ modal: true }).dialog('open')
        },

        open: function () {
            jQuery.post(wcssc_vars.ajaxurl, { action: 'wcssc_share_dialog_html' }, function (response) {
                jQuery(wcssc_share.sharebox_div).html(response).dialog({ modal: true }).dialog('open')
            })
        },

        close: function () {
            jQuery(wcssc_share.sharebox_div).html(wcssc_vars.loader)
        }
    }
    
    var wcssc_email = {

        button_id: '#wcssc-mail',

        show_emailbox: function(){
            
            jQuery.post( wcssc_vars.ajaxurl, { action : 'wcssc_generate_email_html' }, function( response ){
                jQuery(wcssc_share.sharebox_div).html(response).dialog({modal:true}).dialog('open');
            });
        }
    };


    var wcssc_save = {

        button_id: '#wcssc-save',

        show_savebox: function(){
            
            jQuery.post( wcssc_vars.ajaxurl, { action : 'wcssc_generate_save_html' }, function( response ){
                jQuery(wcssc_share.sharebox_div).html(response).dialog({modal:true}).dialog('open');
            });
        }
    };

    jQuery('body').on('click', wcssc_share.button_id, function (e) {
        e.preventDefault()
        wcssc_share.show_sharebox()
    })

    jQuery('body').on('click', '.wcssc-dialog-back', function (e) {
        e.preventDefault()
        wcssc_share.add_loader()
        wcssc_share.open()
    })

    jQuery('body').on('click', wcssc_email.button_id, function (e) {
        e.preventDefault()
        wcssc_share.add_loader()
        wcssc_email.show_emailbox()
    })

    jQuery('body').on('click', wcssc_save.button_id, function (e) {
        e.preventDefault()
        wcssc_share.add_loader()
        wcssc_save.show_savebox()
    })

    /**
     * Generate email box.
     */
    jQuery('body').on('click', '#wcssc-send-mail', function(e){
        
        e.preventDefault();
        var mailData = jQuery(this).parents('form').serialize();

        if( jQuery("#wcssc-mailto").val().trim() === '' ){
            return false;
        }

        jQuery.post( wcssc_vars.ajaxurl, { action : 'wcssc_send_mail', data: mailData }, function( response ){
            if( response.success === true ){
                jQuery(wcssc_share.sharebox_div).html(response.data.msg).dialog({modal:true}).dialog('open');
				setTimeout(function(){ jQuery(wcssc_share.sharebox_div).dialog({modal:true}).dialog('open'); }, 2000);
            }
        });
    });

    /**
     * Save cart script.
     */
    jQuery( 'body' ).on('click', '#wcssc-save-cart', function (e) {

        e.preventDefault()
        var CartData = jQuery(this).parents('form').serialize()

        if (jQuery('#wcssc-save-ip').val().trim() === '') {
            return false
        }

        jQuery.post(wcssc_vars.ajaxurl, { action: 'wcssc_save_cart', data: CartData }, function (response) {

            if (response.success === true) {
                jQuery(wcssc_share.sharebox_div).html(response.data.msg).dialog({ modal: true }).dialog('open')
            }

            setTimeout(function () {jQuery(wcssc_share.sharebox_div).dialog({ modal: true }).dialog('close')}, 2000)
        })
    });

    /**
     * Copy to clipboard script.
     */
    jQuery('body').on( 'click', '#wcssc-clipboard', function( e ){
        e.preventDefault();

        var copyText = document.getElementById("wcssc-copy-link"),
        msg = jQuery(this).data('msg');
        /* Select the text field */
        copyText.select();
        copyText.setSelectionRange(0, 99999); /*For mobile devices*/
        /* Copy the text inside the text field */
        document.execCommand("copy");

        jQuery(wcssc_share.sharebox_div).html(msg).dialog({modal:true}).dialog('open');
        setTimeout(function(){jQuery(wcssc_share.sharebox_div).dialog({modal:true}).dialog('close')}, 2000);
    });
});