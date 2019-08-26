$(document).ready(function () {

    /*
     * Set options variable from hidden input field.
     */
    var options = $('input[name=options]').val();

    /*
     * Initialize iconpicker
     */
     var iconPicker = $('#target').iconpicker();

    /*
     * Check if options is filled otherwise create empty object
     */
    if (options !== '') {
        if(typeof(options) === 'string') {
            options = JSON.parse(options);
        }
        checkOptions();
    } else {
        options = {};
    }

    /* Text input */

    /*
     * On change iconpicker
     * Get value and set in options array, empty and disable tekst input
     */
    $('#target').on('change', function(e) {
        if (e.icon != '' && e.icon != 'empty') {
            $('input[name=apppreinput]').val();
            $('input[name=apppreinput]').prop("disabled", true);
            setPrependAppendValue('icon', e.icon);
        } else {
            $('input[name=apppreinput]').val();
            $('input[name=apppreinput]').prop("disabled", false);
            setPrependAppendValue('', '');
        }
    });

    /*
     * On keyup tekst input for pre- append
     * Get value and set in options array, set icon input to null and disable select
     */
    $('input[name=apppreinput]').on('keyup', function(e) {
        if ($(this).val() !== '') {
            $('#target').prop("disabled", true);
            setPrependAppendValue('text', $(this).val());
        } else {
            $('#target').prop("disabled", false);
            setPrependAppendValue('', '');
        }
    });

    /*
     * Check if options in options object are filled and set the appropiate fields
     */
    function checkOptions() {
        //Readonly option
        if (typeof(options.readonly) !== 'undefined' && options.readonly == true) {
            $('input[name=readonly]').prop('checked', true);
        }

        //Disabled option
        if (typeof(options.disabled) !== 'undefined' && options.disabled == true) {
            $('input[name=disabled]').prop('checked', true);
        }

        //Maxlength option
        if (typeof(options.maxlength) !== 'undefined' && options.maxlength.on == true) {
            $('input[name=maxlength]').prop('checked', true);
            $('input[name=maxLengthValue]').val(options.maxlength.ml);
        }

        //Autofocus option
        if (typeof(options.autofocus) !== 'undefined' && options.autofocus == true) {
            $('input[name=autofocus]').prop('checked', true);
        }

        //Prependappend option
        if (typeof(options.prependappend) !== 'undefined' && options.prependappend.on === true) {
            $('input[name=prependappend]').prop('checked', true);
            $('input[value="'+ options.prependappend.preapp +'"]').prop('checked',true);

            //Check wich type is set
            if (options.prependappend.type === 'icon') {
                $('input[name=apppreinput]').val('');
                $('input[name=apppreinput]').prop('disabled', true);
                $('#target > i').removeClass();
                $('#target > i').addClass(options.prependappend.value);
                $('#target > input').val(options.prependappend.value);
            }

            if (options.prependappend.type === 'text') {
                $('button#target').prop('disabled', true);
                $('input[name=apppreinput]').val(options.prependappend.value);
                $('#target > i').removeClass();
                $('#target > i').addClass('empty');
                $('#target > input').val('');
            }
        }

        //Measurements option
        if (typeof(options.measurements) !== 'undefined' && options.measurements.on === true) {
            $('input[name=measurements]').prop('checked', true);
            $('input[name=textareawidth]').val(options.measurements.width);
            $('input[name=textareaheigt]').val(options.measurements.height);
            $('input[name=measurementsType]').val(options.measurements.type);
        }

        //Resize option
        if (typeof(options.resize) !== 'undefined' && options.resize == true) {
            $('input[name=resize]').prop('checked', true);
        }
    }

    /*
     * Teaxtarea options
     *
     * Measurements
     *
     * Set the measurements options (width and height)
     */
    function functionSetMeasurementsOption() {
        var width = $('input[name=textareawidth]').val();
        var height = $('input[name=textareaheigt]').val();
        options['measurements']['width'] = width;
        options['measurements']['height'] = height;

        var myJSON = JSON.stringify(options, null, 2);
        $('input[name=options]').val(myJSON);
    }

    /*
     * Set the measurement type options (px or %)
     */
    function functionSetMeasurementTypeOption(){
        var type = $( "select[name=measurementsType] option:selected" ).val();
        options['measurements']['type'] = type;

        var myJSON = JSON.stringify(options, null, 2);
        $('input[name=options]').val(myJSON);
    }

    /*
     * Set measurements options on or off and add it to json object
     */
    function measurementsOption() {
        if ($('input[name=measurements]').prop('checked')) {
            options['measurements'] = {};
            options['measurements']['on'] = true;
            $('#textareaMeasurements').show();
            functionSetMeasurementsOption();
            functionSetMeasurementTypeOption();
        } else {
            $('#textareaMeasurements').hide();
            delete options['measurements'];
        }

        var myJSON = JSON.stringify(options, null, 2);

    }

    /*
     * Check if readonly option is checked and add to option object
     */
    function readOnlyOption() {
        if ($('input[name=readonly]').prop('checked')) {
            options.readonly = true;
        } else {
            delete options.readonly;
        }

        var myJSON = JSON.stringify(options, null, 2);
        $('input[name=options]').val(myJSON);
    }

    /*
     * Check if disabled option is checked and add to option object
     */
    function disabledOption() {
        if ($('input[name=disabled]').prop('checked')) {
            options['disabled'] = true;
        } else {
            delete options['disabled'];
        }

        var myJSON = JSON.stringify(options, null, 2);
        $('input[name=options]').val(myJSON);
    }

    /*
     * Check if resize option is checked and add to option object
     */
    function resizeOption() {
        if ($('input[name=resize]').prop('checked')) {
            options['resize'] = true;
        } else {
            delete options['resize'];
        }

        var myJSON = JSON.stringify(options, null, 2);
        $('input[name=options]').val(myJSON);
    }

    /*
     * Check if max length option is checked and add to option object
     */
    function maxLengthOption() {
        if ($('input[name=maxlength]').prop('checked')) {
            options['maxlength'] = {};
            options['maxlength']['on'] = true;
            functionSetMaxLengthOption();
            $('input[name=maxLengthValue]').show();
        } else {
            $('input[name=maxLengthValue]').hide();
            $('input[name=maxLengthValue]').val(255);
            delete options['maxlength'];
        }

        var myJSON = JSON.stringify(options, null, 2);
        $('input[name=options]').val(myJSON);
    }

    /*
     * Set the max length in option object
     */
    function functionSetMaxLengthOption(){
        var maxLength = $('input[name=maxLengthValue]').val();
        if(maxLength == '')
        {
            $('input[name=maxLengthValue]').val(255);
        }
        options['maxlength']['ml'] = maxLength;

        var myJSON = JSON.stringify(options, null, 2);
        $('input[name=options]').val(myJSON);
    }

    /*
     * Check if autofocus option is checked and add to option object
     */
    function autoFocusOption() {
        if ($('input[name=autofocus]').prop('checked')) {
            options['autofocus'] = true;
        } else {
            delete options['autofocus'];
        }

        var myJSON = JSON.stringify(options, null, 2);
        $('input[name=options]').val(myJSON);
    }

    /*
     * Check if prepend/append option is checked and add to option object
     */
    function prependAppendOption() {
        if ($('input[name=prependappend]').prop('checked')) {
            options['prependappend'] = {};
            options['prependappend']['on'] = true;
            functionSetPrependAppend();
            $('#showPrependAppend').show();
        } else {
            delete options['prependappend'];
            $('#showPrependAppend').hide();
        }

        var myJSON = JSON.stringify(options, null, 2);
        $('input[name=options]').val(myJSON);
    }


    /*
     * Set prepend/append option to option object
     */
    function functionSetPrependAppend(){
        var preappend = $('input[name=preapp]:checked').val();
        options['prependappend']['preapp'] = preappend;
        var myJSON = JSON.stringify(options, null, 2);
        $('input[name=options]').val(myJSON);
    }

    /*
     * Set prepend/append value to option object
     */
    function setPrependAppendValue(type, value) {
        options['prependappend']['type'] = type;
        options['prependappend']['value'] = value;
        var myJSON = JSON.stringify(options, null, 2);
        $('input[name=options]').val(myJSON);
    }




    /*
     * Checks if inputs are changed
     */

    /*
     * When measurements input is changed
     */
    $('input[name=measurements]').change(function () {
        measurementsOption();
    });

    /*
     * When measurements input fileds width or height is changed
     */
    $('input[name=textareawidth], input[name=textareaheigt]').keyup(function () {
        functionSetMeasurementsOption();
    });

    /*
     * When measurements type input is changed
     */
    $('select[name=measurementsType]').change(function () {
        functionSetMeasurementTypeOption();
    });


    /*
     * When readonly input is changed
     */
    $('input[name=readonly]').change(function () {
        readOnlyOption();
    });

    /*
     * When disabled input is changed
     */
    $('input[name=disabled]').change(function () {
        disabledOption();
    });

    /*
     * When disabled input is changed
     */
    $('input[name=resize]').change(function () {
        resizeOption();
    });

    /*
     * When max length input is changed
     */
    $('input[name=maxlength]').change(function () {
        maxLengthOption();
    });

    /*
     * When max length input (for x characters) is changed
     */
    $('input[name=maxLengthValue]').change(function () {
        functionSetMaxLengthOption();
    });

    /*
     * When autofocus input is changed
     */
    $('input[name=autofocus]').change(function () {
        autoFocusOption();
    });


    /*
     * When prepen/dappend input is changed
     */
    $('input[name=prependappend]').change(function () {
        prependAppendOption();
    });


    /* Set prepend append */
    $('input[name=preapp]').change(function () {
        functionSetPrependAppend();
    });

    /*
     * End
     */


    /*
     * Make checklist fields sortable
     */
    $('.sortable').nestedSortable({
        disableNesting: 'no-nest',
        forcePlaceholderSize: true,
        handle: 'div',
        helper: 'clone',
        items: 'li',
        maxLevels: 0,
        opacity: .6,
        placeholder: 'placeholder',
        revert: 250,
        tabSize: 25,
        tolerance: 'pointer',
        toleranceElement: '> div',
        placeholder: "ui-state-highlight",
        relocate: function (event, ui) {
            list = $(this).nestedSortable('toHierarchy', {startDepthCount: 0, excludeRoot: true});
            $.ajax({
                type: 'POST',
                data: {
                    list: list,
                },
                url: "<?php echo $this->url('checklistajax', ['action' => 'orderCheckListFields']); ?>",
                async: true,
                success: function (data) {
                    if (data.success === true) {
                    } else {
                        alert('fout');
                    }

                }
            });
        }
    });


    /*
     * Check if field is mandatory and show required message
     */
    function checkIfMandatory() {
        if ($('input[name=required]').is(':checked')) {
            $('#requiredMessage').show();
        } else {
            $('#requiredMessage').hide();
            $('input[name=requiredMessage]').val('');
        }
    }

    /*
     * Check if fieldtype is 13 and show introtext
     */
    function checkIfIntroText() {
        var fieldType = $('select[name=checklistFieldType]').val();
        if (fieldType == 13) {
            $('#introText').show();
            $('#required').hide();
            $('input[name=required]').prop("checked", false);
            checkIfMandatory();
        } else {
            $('#introText').hide();
            $('#introText div div textarea').val('');
            $('#required').show();
            checkIfMandatory();
        }
    }


    /*
     * Reset all option fields
     */
    function resetOptionFields()
    {
        $('#optionField input[type=checkbox]').prop('checked', false);
        $('#optionField input[type=text], #optionField input[type=number]').val('');
        $('#target > i').removeClass();
        $('#target > i').addClass('empty');
        $('#target > input').val('');
        maxLengthOption();
    }

    /*
     * Hide all options field and show the one according to filed type id
     */
    function getOptionsByFieldType() {
        var selectedValue = $('select[name=checklistFieldType]').val();
        resetOptionFields();
        $('#optionField').hide();
        $('.option').hide();
        $('.options' + selectedValue).show();
        $('#optionField').show();

    }


    /*
     * Show answers based on the selected field type
     */
    function showAnswers(selectedValue) {
        if (selectedValue == 3 || selectedValue == 11 || selectedValue == 12) {
            $('#answers').show();
        } else {
            $('#answers').hide();
        }
    }

    /*
     * Show answers based on the selected field type
     */
    function showAnswersOnPageLoad()
    {
        $('div#answers').hide();
        var selectedValue = $("select[name=checklistFieldType]").val();
        showAnswers(selectedValue);
    }

    /*
     * When input with attribute required is changed
     */
    $('input[name=required]').change(function () {
        checkIfMandatory();
    });

    /*
     * When input with attribute required is changed
     */
    $('select[name=checklistFieldType]').change(function () {
        checkIfIntroText();
    });

    /*
     * When input with attribute required is changed
     */
    $("select[name=checklistFieldType]").change(function () {
        var selectedValue = $(this).val();
        showAnswers(selectedValue);
        getOptionsByFieldType();

    });

    /*
     * Initialise functions
     */
    readOnlyOption();
    disabledOption();
    autoFocusOption();
    maxLengthOption();
    prependAppendOption();
    measurementsOption();
    checkIfMandatory();
    checkIfIntroText();
    getOptionsByFieldType();
    showAnswersOnPageLoad();

});