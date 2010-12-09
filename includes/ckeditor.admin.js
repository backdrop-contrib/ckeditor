(function ($) {
    CKEDITOR.on( 'dialogDefinition', function( ev )
    {
        var dialogName = ev.data.name;
        var dialogDefinition = ev.data.definition;

        if ( dialogName == 'uicolor' )
        {
            // Get a reference to the configBox and hide it (cannot be removed).
            var configBox = dialogDefinition.getContents( 'tab1' ).get( 'configBox' );
            configBox.style = 'display:none';
        }
    });

    $(document).ready(function() {
        if (typeof(CKEDITOR) == "undefined")
            return;

        $('#edit-uicolor-textarea').show();

        Drupal.ckeditorUiColorOnChange = function() {
            var color = CKEDITOR.instances["edit-uicolor-textarea"].getUiColor();
            if ($("#edit-uicolor").val() == "custom" && typeof(color) != "undefined") {
                $('input[name$="uicolor_user"]').val(color);
            }
        };
        
        CKEDITOR.replace("edit-uicolor-textarea",
        {
            extraPlugins : 'uicolor',
            height: 60,
            uiColor: $('input[name$="uicolor_user"]').val() || '#D3D3D3',
            width: 400,
            toolbar : [[ 'Bold', 'Italic', '-', 'NumberedList', 'BulletedList'],[ 'UIColor' ]],
            on:
            {
                focus : Drupal.ckeditorUiColorOnChange,
                blur : Drupal.ckeditorUiColorOnChange
            }
        });

        if ( $("#edit-skin").val() == "kama" ){
            $("#edit-uicolor").removeAttr('disabled');
            $("#edit-uicolor").parent().removeClass('form-disabled');
        }
        else {
            $("#edit-uicolor").attr('disabled', 'disabled');
            $("#edit-uicolor").parent().addClass('form-disabled');
        }

        $("#edit-skin").bind("change", function() {
            if ( $("#edit-skin").val() == "kama" ){
                $("#edit-uicolor").removeAttr('disabled');
                $("#edit-uicolor").parent().removeClass('form-disabled');
            }
            else {
                $("#edit-uicolor").attr('disabled', 'disabled');
                $("#edit-uicolor").parent().addClass('form-disabled');
            }
        });

        $("#edit-uicolor").bind("change", function() {
            if (typeof(Drupal.settings.ckeditor_uicolor) != "undefined") {
                CKEDITOR.instances["edit-uicolor-textarea"].setUiColor(Drupal.settings.ckeditor_uicolor[$(this).val()]);
            }
            if ($(this).val() != "custom") {
                $('input[name$="uicolor_user"]').val("");
            }
            else {
                var color = CKEDITOR.instances["edit-uicolor-textarea"].getUiColor();
                if (typeof(color) != "undefined") {
                    $('input[name$="uicolor_user"]').val(color);
                }
            }
        });
    });
})(jQuery);