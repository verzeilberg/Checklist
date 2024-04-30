/**
 * Javascript file to
 */
$(document).ready(function () {
    /**
     * Function to check/uncheck all items by checkbox
     */
    $('#checkAll').change(function () {
        if ($(this).is(":checked")) {
            $("input.delete-item").each(function (i) {
                $(this).prop('checked', true);
            });
        } else {
            $("input.delete-item").each(function () {
                $(this).prop('checked', false);
            });
        }
    });

    /**
     * Click function to remove selected items
     */
    $('#remove').click(function (e) {
        e.preventDefault();
        var itemsToDelete = []
        $("input.delete-item").each(function (i) {
            if ($(this).is(":checked")) {
                itemsToDelete.push($(this).val());
            }
        });
        if (itemsToDelete.length > 0) {
            var form_data = new FormData();
            form_data.append('itemsToDelete', itemsToDelete);
            $.ajax({
                url: '/checklistajax/delete-items',
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                async: true,
                type: 'post',
                success: function (response) {
                    if (response.error === false) {
                        $(response.deletedItems).each(function (index, value) {
                            $('tr#checklistItem-' + value).slideUp("slow", function () {
                                // Animation complete.
                            });
                        });
                        $('#checkAll').prop('checked', false);
                    } else {
                        alert(response.error_message);
                    }
                }
            });
        }
    });

    /**
     * Click function to remove checklistitem
     */
    $(document).on('click', '.removeChecklistItemOpen', function () {
        var id = $(this).data('checklistitemid');
        $.ajax({
            url: '/checklistajax/delete-item',
            dataType: 'json',
            data: {
                id: id
            },
            async: true,
            type: 'post',
            success: function (response) {
                if (response.success == true) {
                    $('tr#checklistItem-' + response.checklistItemId).fadeOut('fast',
                        function () {
                        });

                } else {
                    alert(response.errorMessage);
                }
            }
        });
    });

    /**
     * Add a checklist item (open modal)
     */
    $('#addChecklistItemOpen').on('click', function () {
        clearFormFields();
        $(function () {
            $('#addChecklistItem').modal('toggle');
        });
    });


    /**
     * Edit a checklist item get checklist item and open modal
     */
    $(document).on('click', '.editChecklistItemOpen', function () {
        clearFormFields();
        var id = $(this).data('checklistitemid');
        $.ajax({
            url: '/checklistajax/getChecklistItem',
            dataType: 'json',
            data: {
                id: id
            },
            async: true,
            type: 'post',
            success: function (response) {
                $('input#checkListItemId').val(id);
                $.each(response.itemContent[id], function (index, value) {
                    let formField = $('form#create-checklist-item-form input[name=' + index + ']');

                    if (formField.attr('type') === undefined)
                    {
                        formField = $('form#create-checklist-item-form textarea[name=' + index + ']');
                    }
                    console.log(formField);


                    if (
                        formField.attr('type') === 'text' ||
                        formField.attr('type') === 'number'
                        )
                    {
                        formField.val(value);
                    } else if (
                        formField.attr('type') === 'checkbox' ||
                        formField.attr('type') === 'radio'
                    ) {
                        $.each(formField, function () {
                            let checkBoxValue = parseInt($(this).val());
                            let arrayValues = _.toArray(value);
                            if (_.indexOf(arrayValues, checkBoxValue) > -1) {
                                $(this).prop("checked", true);
                            } else {
                                $(this).prop("false", true);
                            }
                        });
                    } else if (formField.is( "textarea" )) {
                        console.log(value);
                        formField.val(value);
                    }
                });
                $(function () {
                    $('#addChecklistItem').modal('toggle');
                });
            }
        });
    });

    /**
     * Click function to add/edit checklistitem
     */
    $('#addChecklistItemButton').on('click', function () {
        //Get form data and serialize it
        let formData = $("form#create-checklist-item-form").serialize();
        let valid = validateForm('create-checklist-item-form');
        let checklistId = $('input[name="checklistId"]').val();
        if (valid == true) {
            //Ajax call to add or edit checklist item. Based on given id.
            $.ajax({
                url: '/checklistajax/addChecklistItem',
                dataType: 'json',
                data: {
                    id: checklistId,
                    formData: formData
                },
                async: true,
                type: 'post',
                success: function (response) {
                    //Check if response is success
                    if (response.success) {
                        //Hide error message and clear it
                        $('#formErrorMessage').hide();
                        $('#formErrorMessage').html('');

                        //Check if row with empty checklist item is present
                        if ($("#myTable tr.noCheclistItems").length > 0) {
                            $("#myTable tr.noCheclistItems").remove();
                        }

                        if (response.action == 'add') { //If action is add than add a new row

                            //Because we are adding a new row in the table we create a new row
                            var row = $('<tr id="checklistItem-' + response.checkListItemId + '">');
                            //Add checkbox for multiple delete
                            row.append($('<td class="text-center">').html('<input class="delete-item" type="checkbox" name="checked-items[]" value="' + response.checkListItemId + '"/>'));


                            $('#myTable').find('thead tr th.checklistFieldHeader').each(function (index) {
                                var checkListFieldId = $(this).data('checklistfield');
                                var checkListItemField = response.item[checkListFieldId];

                                var content = '';
                                if (typeof checkListItemField != 'undefined') {

                                    //Get item form type (text, checkbox, date, select etc.)
                                    var itemFormType = Object.keys(checkListItemField.type)[0];

                                    if (itemFormType == 'text') {
                                        content = checkListItemField.type.text;
                                    } else if (itemFormType === 'textarea') {
                                        content = checkListItemField.type.textarea;
                                    } else if (itemFormType == 'number') {
                                        content = checkListItemField.type.number;
                                    } else if (itemFormType == 'radio') {
                                        content += '<i class="fas fa-check-circle"></i> ' + checkListItemField.type.radio;
                                    } else if (itemFormType == 'select') {
                                        content += checkListItemField.type.select;
                                    } else if (itemFormType == 'checkbox') {
                                        $.each(checkListItemField.type.checkbox, function (index, value) {
                                            content += '<i class="fas fa-check-square"></i> ' + value + '<br/>'
                                        });
                                    }
                                }
                                row.append($('<td data-checklistfield="' + checkListFieldId + '" class="checklistField" id="checkListField-' + checkListFieldId + '">').html(content));
                            });

                            //Create edit button
                            var buttons = '<button data-checklistitemid="' + response.checkListItemId + '" class="btn btn-sm btn-secondary editChecklistItemOpen">';
                            buttons += '<i class="fas fa-edit"></i>';
                            buttons += '</button>&nbsp;';
                            buttons += '<button data-checklistitemid="' + response.checkListItemId + '" class="btn btn-sm btn-danger removeChecklistItemOpen ">';
                            buttons += '<i class="fas fa-trash-alt"></i>';
                            buttons += '</button>';
                            row.append($('<td class="text-center">').html(buttons));
                            $('#myTable > tbody#result').append(row);

                        } else { //If action is edit than edit a row with new values
                            $('#myTable').find('tr#checklistItem-' + response.checkListItemId + ' td.checklistField').each(function (index) {
                                let checkListFieldId = $(this).data('checklistfield');
                                let checkListItemField = response.item[checkListFieldId];
                                let content = '';
                                if (typeof checkListItemField != 'undefined') {

                                    let itemFormType = Object.keys(checkListItemField.type)[0];

                                    if (itemFormType === 'text') {
                                        content = checkListItemField.type.text;
                                    } else if (itemFormType === 'textarea') {
                                        content = checkListItemField.type.textarea;
                                    } else if (itemFormType === 'number') {
                                        content = checkListItemField.type.number;
                                    } else if (itemFormType === 'radio') {
                                        content += '<i class="fas fa-check-circle"></i> ' + checkListItemField.type.radio;
                                    } else if (itemFormType === 'select') {
                                        content += checkListItemField.type.select;
                                    } else if (itemFormType === 'checkbox') {
                                        $.each(checkListItemField.type.checkbox, function (index, value) {
                                            content += '<i class="fas fa-check-square"></i> ' + value + '<br/>'
                                        });
                                    }
                                }

                                $(this).html(content);
                            });
                        }
                        $(function () {
                            $('#addChecklistItem').modal('toggle');
                        });
                    } else {
                        $('#formErrorMessage').html(response.errorMessage);
                        $('#formErrorMessage').show();
                    }
                }
            });
        }
    });

    /**
     * This functions clears al types of input fields
     */
    function clearFormFields() {
        $('form#create-checklist-item-form input[type=text]').val('');
        $('form#create-checklist-item-form input[type=date]').val('');
        $('form#create-checklist-item-form input[type=number]').val('');
        $('form#create-checklist-item-form input[type=hidden]').val('');
        $('form#create-checklist-item-form textarea').val('');
        $('form#create-checklist-item-form input[type=radio]').prop("checked", false);
        $('form#create-checklist-item-form input[type=checkbox]').prop("checked", false);
        $('#formErrorMessage').hide();
        $('#formErrorMessage').html('');
    }


    /*
     * Validate form
     */
    function validateForm(formId) {
        var valid = true;
        $("form#" + formId + " input[required], form#" + formId + " select[required], form#" + formId + " textarea[required]").each(function () {
            if ($(this).is("text") ||
                $(this).is("number") ||
                $(this).is("select") ||
                $(this).is("date")) {
                var value = $(this).val();
                if (value == '') {
                    valid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            } else if ($(this).is("radio")) {
                var name = $(this).attr('name');
                var rdCount = $('input[name="' + name + '"]:checked').length;
                if (rdCount == 0) {
                    valid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            } else if ($(this).is("checkbox")) {
                var name = $(this).attr('name');
                var cbCount = $('input[name="' + name + '"]:checked').length;
                if (cbCount == 0) {
                    valid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            } else if ($(this).is("textarea")) {
                var value = $(this).val();
                if (value == '') {
                    valid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            }
        });
        return valid;
    }

    /**
     * Click function to open export modal
     */
    $("#export").on("click", function () {
        $(function () {
            $('#exportFile').modal('toggle');
        });
    });

    /**
     * Click function to open import modal
     */
    $('#importFile').on('shown.bs.modal', function () {
        $('#myInput').focus()
    })

    //Create well design file upload form file
    $(document).on('change', ':file', function () {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);

        $('input[name=importfileName]').val(label);
    });

    $('#upload').on('click', function () {
        var file_data = $('input[name=importfile]').prop('files')[0];
        var checklistid = $('input[name=checklistid]').val();
        var header = '';
        if ($('input[name=header]').is(":checked")) {
            header = 1;
        }
        var form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('checklistid', checklistid);
        form_data.append('header', header);
        $.ajax({
            url: '/checklistajax/uploadFile',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            async: true,
            type: 'post',
            success: function (response) {
                $(response.items).each(function (key, value) {
                    $('#result').append(value);
                });
                $(function () {
                    $('#importFile').modal('toggle');
                });
            }
        });


    });

});
