(function ($) {

    $('.sbis-color-picker').wpColorPicker();

    //remember last opened tab
    let oldIndex,
        index = 'sbis_tab_key', //data store name
        dataStore = window.sessionStorage;
    try {
        //Fetch previous value
        oldIndex = dataStore.getItem(index);
    } catch (e) {
        //set default to first tab in error state
        oldIndex = 0;
    }
    $('#sbis-tabs').tabs({
        // The zero-based index of the panel that is active (open)
        active: oldIndex,
        // Triggered after a tab has been activated
        activate: function (event, ui) {
            //  Get future value
            var newIndex = ui.newTab.parent().children().index(ui.newTab);
            //  Set future value
            dataStore.setItem(index, newIndex)
        }
    });


    $('.attached-checkout').dependsOn({
        '#attach_checkout': {
            checked: true
        }
    });

    $('.plan-picker').dependsOn({
        '#attach_plan_picker': {
            checked: true
        }
    });

    $('.sbis-related-to-rectangle').dependsOn({
        'input[name="config_shape"]': {
            values: ['rectangle']
        }
    });

    $('.sbis-related-to-mob-rectangle').dependsOn({
        'input[name="mob_config_shape"]': {
            values: ['rectangle']
        }
    });

    $('.sbis-related-to-circle').dependsOn({
        'input[name="config_shape"]': {
            values: ['circle']
        }
    });

    $('.sbis-related-to-mob-circle').dependsOn({
        'input[name="mob_config_shape"]': {
            values: ['circle']
        }
    });

    $('.sbis-js-callback').dependsOn({
        '#attach_callback': {
            checked: true
        }
    });

    $('#plan-picker-mobile').dependsOn({
        'input[name="mobile_config"]': {
            values: ['different']
        }
    });

    $('#related_to_billing_cycle_group').dependsOn({
        '#billing_cycle_group': {
            checked: true
        }
    });

    $('#mob_related_to_billing_cycle_group').dependsOn({
        '#mob_billing_cycle_group': {
            checked: true
        }
    });    
    
    $('#plan_card_defaultSort_row').dependsOn({
        '#plan_card_sort': {
            checked: false
        }
    });

    $('#mob_plan_card_defaultSort_row').dependsOn({
        '#mob_plan_card_sort': {
            checked: false
        }
    });

    $('#sbse-settings-form').submit(function () {
        var btn_show_delay = $('#btn_show_delay').val(),
            btn_flash_delay = $('#btn_flash_delay').val();
        if (btn_show_delay.length && btn_show_delay > 0 && btn_flash_delay.length && btn_flash_delay > 0) {
            if (btn_show_delay >= btn_flash_delay) {
                alert(sbis_obj.btn_flash_delay_validation);
                return false;
            }
        }
    });

    $('.repeater').repeater({
        // (Optional)
        // start with an empty list of repeaters. Set your first (and only)
        // "data-repeater-item" with style="display:none;" and pass the
        // following configuration flag
        initEmpty: false,
        // (Optional)
        // "defaultValues" sets the values of added items.  The keys of
        // defaultValues refer to the value of the input's name attribute.
        // If a default value is not specified for an input, then it will
        // have its value cleared.
        defaultValues: {
            'text-input': 'foo'
        },
        // (Optional)
        // "show" is called just after an item is added.  The item is hidden
        // at this point.  If a show callback is not given the item will
        // have $(this).show() called on it.
        show: function () {
            $(this).slideDown();
        },
        // (Optional)
        // "hide" is called when a user clicks on a data-repeater-delete
        // element.  The item is still visible.  "hide" is passed a function
        // as its first argument which will properly remove the item.
        // "hide" allows for a confirmation step, to send a delete request
        // to the server, etc.  If a hide callback is not given the item
        // will be deleted.
        hide: function (deleteElement) {
            if (confirm('Are you sure you want to delete this element?')) {
                $(this).slideUp(deleteElement);
            }
        },
        // (Optional)
        // You can use this if you need to manually re-index the list
        // for example if you are using a drag and drop library to reorder
        // list items.
        ready: function (setIndexes) {
            //$dragAndDrop.on('drop', setIndexes);
        },
        // (Optional)
        // Removes the delete button from the first list item,
        // defaults to false.
        isFirstItemUndeletable: false
    })
})(jQuery)
