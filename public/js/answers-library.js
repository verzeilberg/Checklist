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
                    //Check if answer exists
                    if (response.answerExcists == true) {
                        $('#answerLibrary').modal('toggle');
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
                                $('#answerLibrary').modal('toggle');
                            }
                        });
                    } else {
                        //Answer does not exist so add it
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
                    var row = $('<tr>');
                    row.append($('<td>').html(response.data.label));
                    row.append($('<td>').html(response.data.value));
                    $('#answerLibraryTable > tbody#resultAnswerLibraryTable').append(row);
                    $('input#labelAnswer').val('');
                    $('input#valueAnswer').val('');


                } else {
                    alert(response.errorMessage);
                }
            }
        });
    }

    //Function to search for answers in library
    $('#searchAnswerLibrary').on('click', function (e) {
        e.preventDefault();
        var searchPhrase = $('input[name=search]').val();

        if (searchPhrase) {
            searchAnswer(searchPhrase);
        } else {
            console.log('geen zoekwoord ingevuld');
        }
    });

    //Function to search and return result
    function searchAnswer(searchPhrase) {
        $('#answerLibraryTable > tbody#resultAnswerLibraryTable').empty();
        $.ajax({
            url: '/checklistajax/searchAnswer',
            dataType: 'json',
            data: {
                searchPhrase: searchPhrase,
            },
            async: true,
            type: 'post',
            success: function (response) {
                if (response.success == true) {
                    $.each(response.result, function (index, object) {
                        var row = $('<tr>');
                        row.append($('<td class="text-center">').html('<button data-answerid="' + object.id + '" class="btn btn-sm btn-success addAnswerToQuestion"><i class="fas fa-link"></i></button>'))
                        row.append($('<td>').html(object.label));
                        row.append($('<td>').html(object.value));
                        $('#answerLibraryTable > tbody#resultAnswerLibraryTable').append(row);
                    });

                } else {
                    alert(response.errorMessage);
                }
            }
        });
    }

    //Add answer to question
    $(document).on('click', '.addAnswerToQuestion', function () {
        var answerId = $(this).data('answerid');
        $.ajax({
            url: '/checklistajax/addAnswerToQuestion',
            dataType: 'json',
            data: {
                answerId: answerId,
            },
            async: true,
            type: 'post',
            success: function (response) {
                if (response.success == true) {
                    $('div#selectedAswers input:checkbox[value="' + response.data.id + '"]').prop('checked', true);
                    var row = $('<tr>');
                    row.append($('<td>').html(response.data.label));
                    row.append($('<td>').html(response.data.value));
                    row.append($('<td class="text-center">').html('<button type="button" class="btn btn-sm btn-danger removeAnswer"><i class="fas fa-trash-alt"></i></button>'));
                    $('#answersTable > tbody#result').append(row);

                } else {
                    alert(response.errorMessage);
                }
            }
        });
    });
});


//Function to delete answer from question
$(document).on('click', '.removeAnswer', function () {
    let answerId = $(this).data('answerid');
    $('div#selectedAswers input:checkbox[value="' + answerId + '"]').prop('checked', false);
    $(this).parent('td').parent('tr').remove();
});

//Function to search for answers on index in library
$('.searchOnIndex').on('click', function () {
    $('#answerLibraryTable > tbody#resultAnswerLibraryTable').empty();
    var index = $(this).data('index');
    $.ajax({
        url: '/checklistajax/searchAnswerOnIndex',
        dataType: 'json',
        data: {
            index: index
        },
        async: true,
        type: 'post',
        success: function (response) {
            if (response.success == true) {
                $.each(response.result, function (index, object) {
                    var row = $('<tr>');
                    row.append($('<td class="text-center">').html('<button data-answerid="' + object.id + '" class="btn btn-sm btn-success addAnswerToQuestion"><i class="fas fa-link"></i></button>'))
                    row.append($('<td>').html(object.label));
                    row.append($('<td>').html(object.value));
                    $('#answerLibraryTable > tbody#resultAnswerLibraryTable').append(row);
                });

            } else {
                alert(response.errorMessage);
            }
        }
    });
});
