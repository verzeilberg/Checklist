$(document).ready(function () {
    //Click function to add answer
    $('#addAnswerButton').on('click', function () {
        var label = $('input#labelAnswer').val();
        var value = $('input#valueAnswer').val();

        //Ajax call to add or edit checklist item. Based on given id.
        $.ajax({
            url: '/checklistajax/checkIfAnswerExcist',
            dataType: 'json',
            data: {
                label: label
            },
            async: true,
            type: 'post',
            success: function (response) {
                if (response.success == true) {
                    //Check if answer excists
                    if (response.answerExcists == true) {
                        $('#addAnswer').modal('toggle');
                        bootbox.alert({
                            message: 'Antwoord bestaat al!',
                            size: 'large',
                            className: 'text-dark text-center',
                            buttons: {
                                ok: {
                                    label: 'Oke',
                                    className: 'btn-dark'
                                }
                            },
                            callback: function () {
                                $('#addAnswer').modal('toggle');
                            }
                        });
                    } else {
                        //Answer does not excist so add it
                        addAnswer(label, value);
                    }
                } else {
                    alert(response.errorMessage);
                }
            }
        });
    });


    //Function to add answer to answer library
    function addAnswer(label, value) {
        $.ajax({
            url: '/checklistajax/addAnswer',
            dataType: 'json',
            data: {
                label: label,
                value: value
            },
            async: true,
            type: 'post',
            success: function (response) {
                if (response.success == true) {

                    var row = $('<tr id="item-' + response.checkListItemId + '">');
                    row.append($('<td class="text-center">').html('<input type="checkbox" name="answers[]"\n' +
                        '                                                           value="' + response.data.id + '"/>'));
                    row.append($('<td>').html(response.data.label));
                    row.append($('<td>').html(response.data.value));
                    $('#answersTable > tbody#result').append(row);

                    $(function () {
                        $('#addAnswer').modal('toggle');
                    });
                } else {
                    alert(response.errorMessage);
                }
            }
        });
    }


});
