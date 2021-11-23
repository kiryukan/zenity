/**
 * Created by simonvivier on 20/01/17.
 */
/*
* TODO DES REFACTO SUR LES CHARGEMENT DE LISTE
* */
window.buffer = {};
window.buffer.indicator = {};
window.buffer.note = {};

let scheduleDate_input = $('#scheduleDate-input').daterangepicker({
    timePicker: true,
    locale: {
        format: 'DD/MM/YYYY HH:mm'
    },
    minDate:moment().startOf('hours'),
    singleDatePicker: true,
    timePicker24Hour: true,
    timePickerIncrement: 30,


});
let clientList = $('#clientList').selectmenu({});
let scheduleDate_dialog = $('#scheduleDate-dialog');
let schedule_button = $('#schedule-button');

schedule_button.click(function(){
    alert("le recalcul des notes est programmer pour le "+scheduleDate_input.val());
    sendBuffer(scheduleDate_input.val(),$('#recalculationPeriod-select').val());
    scheduleDate_dialog.dialog('close');

});

let submit_update_button = $('#submit-update-button');
submit_update_button.click(function () {
    submit();
});
function getMetadata(){
   if(null === sessionStorage.getItem('metadata') ){
        $.ajax({
            url: `${config.serverPath}/gestion/metadata`,
            dataType: 'json',
            async: false,
            success: function (jsonData) {
                sessionStorage.setItem('metadata',JSON.stringify({classes : jsonData}));
            }
        });
    }
    return  JSON.parse(sessionStorage.getItem('metadata'));
}
function getIndicatorId(selector){
    return $(selector).parents(".indicator").attr('id').replace("indicator-","");
}

function updateNoteName(noteNameFieldId,noteId){
    let noteName = $(noteNameFieldId).val();
    if (!buffer[noteId]){
        window.buffer.note[noteId] = {};
    }
    window.buffer.note[noteId].name = noteName;
}

function updateIsAvg(fieldId,noteId){
    if (!buffer[noteId]){
        window.buffer.note[noteId] = {};
    }
    window.buffer.note[noteId].isAvg = $(fieldId).prop('checked');
}

function addIndicator(parentDivId) {
    let parentType = parentDivId.split('-')[0];
    let parentId = parentDivId.split('-')[1];
    $.ajax({
        dataType:'json',
        type: 'GET',
        url: `${config.serverPath}/gestion/AuditEngine/newIndicator`,
        data:{
            parentType: parentType,
            parentId: parentId
        },
        success: function (indicatorId) {
            let metaData = getMetadata();
            let indicatorData = {
                id:indicatorId,
                class:$.map((metaData['classes']) , function (value, key) {
                    return {name:key};
                }),
                noClass:true,
                noFilter:true,
                noProperty:true,
            };
            if(parentType == 'note'){
                indicatorData['coeff'] = true;
            }
            let propertyListTemplate = Handlebars.compile('{{> indicator }}');
            $('#' + parentDivId).append(propertyListTemplate(indicatorData));
        }
    });
}

function addNote(parentClassName) {
    $.ajax({
        dataType:'json',
        type: 'GET',
        url: `${config.serverPath}/gestion/AuditEngine/newNote`,
        success: function (noteId) {
            noteTemplate = Handlebars.compile('{{> note }}');
            $('#'+parentClassName).append(noteTemplate({id:noteId}));
        }
    });
}

function deleteNote(noteId){
    $.ajax({
        type: 'POST',
        url: `${config.serverPath}/gestion/AuditEngine/deleteNote`,
        data:{
            noteId:noteId.split('-')[1]
        },
        success: function () {
            $('#'+noteId).remove();
            $('#button-'+noteId).remove();
        }
    });
}

function deleteIndicator(indicatorId){
    delete window.buffer.indicator[indicatorId.split('-')[1]];

    $.ajax({
        type: 'POST',
        url: `${config.serverPath}/gestion/AuditEngine/deleteIndicator`,
        data:{
            indicatorId:indicatorId.split('-')[1]
        },
        success: function () {
            $('#'+indicatorId).remove();
        }
    });
}

function classListUpdate(classList){
    let className = $(classList).val();

    let metaData = getMetadata();
    let data = $.map((metaData['classes'][className]['properties']) , function (value, key) {
        return {name:key};
    });
    let propertyListTemplate = Handlebars.compile('{{> propertyList }}');
    let propertyListHtml = propertyListTemplate({property:data,noProperty:true});
    $(classList).parents(".indicator").find(".propertyList").html(propertyListHtml);

    let filterListTemplate = Handlebars.compile('{{> filterList }}');
    let filterListHtml = filterListTemplate({filter:data,noFilter:true});
    $(classList).parents(".indicator").find(".filterList").html(filterListHtml);

    let indicatorId = getIndicatorId(classList);
    if (!buffer.indicator[indicatorId]){
        window.buffer.indicator[indicatorId] = {};
    }

    window.buffer.indicator[indicatorId].class = className;
}
function updateCoeff(coeff){
    let coeffValue = $(coeff).val();
    let indicatorId = getIndicatorId(coeff);
    if (!window.buffer.indicator[indicatorId]){
        window.buffer.indicator[indicatorId] = {};
    }

    window.buffer.indicator[indicatorId].coeff = coeffValue;
}
function propertyListUpdate(propertyList){
    let propertyName = $(propertyList).val();
    let className = $(propertyList).parents(".indicator").find(".classList").find("select").val();
    let metaData = getMetadata();
    let data = {};
    let type = metaData['classes'][className]['properties'][propertyName]['type'];

    data[type] = true;
    console.log(data);
    propertyListTemplate = Handlebars.compile('{{> propertyInput }}');
    propertyListHtml = propertyListTemplate({propertyType:[data]});
    $(propertyList).parents(".indicator").find(".propertyInput").html(propertyListHtml);

    indicatorId = getIndicatorId(propertyList);
    if (!window.buffer.indicator[indicatorId]){
        window.buffer.indicator[indicatorId] = {};
    }

    window.buffer.indicator[indicatorId].field = propertyName;
}

function propertyInputUpdate(field,propertyInput){
    let propertyValue = $(propertyInput).val();

    let indicatorId = getIndicatorId(propertyInput);
    if (!window.buffer.indicator[indicatorId]){
        window.buffer.indicator[indicatorId] = {};
    }
    window.buffer.indicator[indicatorId][field] = propertyValue;
}

function filterInputUpdate(field,filterInput){
    let filterValue = $(filterInput).val();

    let indicatorId = getIndicatorId(filterInput);
    if (!window.buffer.indicator[indicatorId]){
        window.buffer.indicator[indicatorId] = {};
    }

    if (!window.buffer.indicator[indicatorId]['filter']){
        window.buffer.indicator[indicatorId]['filter'] = {};
    }
    window.buffer.indicator[indicatorId].filter[field] = filterValue;
}


function filterListUpdate(filterList){
    let propertyName = $(filterList).val();
    let className = $(filterList).parents(".indicator").find(".classList").find("select").val();
    let metaData = getMetadata();
    let type = metaData['classes'][className]['properties'][propertyName]['type'];
    let data = {};
    let indicatorId = getIndicatorId(filterList);

    data[type] = true;
    propertyListTemplate = Handlebars.compile('{{> filterInput }}');
    propertyListHtml = propertyListTemplate({filterInput:{filterType:[data]}});
    console.log({filter:{filterType:[data]}});
    $(filterList).parents(".indicator").find(".filterInput").html(propertyListHtml);

    if (!window.buffer.indicator[indicatorId]){
        window.buffer.indicator[indicatorId] = {};
    }
    if (!window.buffer.indicator[indicatorId]['filter']){
        window.buffer.indicator[indicatorId]['filter'] = {};
    }

    window.buffer.indicator[indicatorId]['filter'].field = propertyName;
}

function sendBuffer(sheduledDate,recalculationPeriod){
    if(JSON.stringify(window.buffer) != JSON.stringify({ indicator:{},note:{} })){
        $.ajax({
            url: `${config.serverPath}/config/AuditEngine/updateNote`,
            type: 'POST',
            data: {
                jsondata:JSON.stringify({indicatorArray:window.buffer.indicator,noteArray:window.buffer.note}),
                scheduledDate:sheduledDate,
                recalculationPeriod:recalculationPeriod
            },
            success:function () {
                window.buffer = {};
                window.buffer.indicator = {};
                window.buffer.note = {};
            },
            error:function () {
                console.log('server cannot be reached the data wasnt send');
            }
        });
    }
}

function submit(){
    scheduleDate_dialog.dialog( {
        minWidth: 500,
        minHeight: 300
    });
}
