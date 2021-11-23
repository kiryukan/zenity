/**
 * Created by simonvivier on 01/06/17.
 */

 // Select report informations: Client, Dates, DB, Server

window.onload = function(){
    $("#select-report-form").submit(function (event) {
        $.ajax({
            dataType: "json",
            type: 'GET',
            async: false,
            url: WEB_SERVICE_URL + '/ressources/setcache',
            data: {
                token: localStorage.getItem('token'),
                cache: {
                    clientId: $('#clientList').val(),
                    baseId: $('#baseList').val(),
                    instanceId: $('#instanceList').val(),
                    date:{
                        'startDate':$('#datepicker').val().split(" - ")[0],
                        'endDate':$('#datepicker').val().split(" - ")[1],
                    },
                    byol: $('#byol').val(),
                    licence: $('#licence').val(),
                },
            },
            success: function (result) {},
        });
    });

};

let token = localStorage.getItem('token');
let clientList = $('#clientList').selectmenu({
    change: function( event, ui ) {
        loadBase();
    }
});
let baseList = $('#baseList').selectmenu({
    change: function( event, ui ) {
        loadInstances();
    }
});
let instanceList = $('#instanceList').selectmenu({
    change: function( event, ui ) {
        
    }
});
let dateRangePickerProperty = {
    timePicker: true,
    timePickerIncrement: 30,
    maxDate: moment(),
    startDate: moment().subtract(7, 'days'),
    endDate: moment(),
    timePicker24Hour: true,
    locale: {
        format: 'DD/MM/YYYY HH:mm'
    },
    ranges: {
        'Today': [moment().startOf('day'), moment().startOf('hour')],
        'Yesterday': [moment().subtract(1, 'days').startOf('day'), moment().endOf('day')],
        'Last 7 day': [moment().subtract(7, 'days').startOf('day'), moment().startOf('hour')],
        'This Month': [moment().startOf('month'), moment().startOf('hour')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        'This year': [moment().startOf('years'),moment().startOf('hour')],
        'Last year':[moment().subtract(1, 'year').startOf('year'),moment().subtract(1, 'year').endOf('year')]
    }
};

// DATES INTERVALS SELECT
let datePicker =  $('input[id="datepicker"]').daterangepicker(dateRangePickerProperty);
loadCache();
loadClients(true);

function loadCache() {
    $.ajax({
        dataType:"json",
        type:'GET',
        url:WEB_SERVICE_URL+'/ressources/cache/',
        data:{token:localStorage.getItem('token')},
        async:false,
        success:function (result) {
            console.log(result);
            if (result){
                localStorage.setItem('loadReportCache',JSON.stringify(result));
            }else{
                localStorage.setItem('loadReportCache','{}');
            }
        },error: function () {
            localStorage.setItem('loadReportCache','{}');
        }
    });
}
// CLIENTS
function loadClients(firstTime=false) {
    $.ajax({
        dataType: "json",
        type:'GET',
        url: WEB_SERVICE_URL+'/ressources',
        data:{token:localStorage.getItem('token'),
        },
        success: function (result) {
            for (let index in result) {
                value = result[index]['name'];
                clientList.append('<option value=' + index + '>' + value + '</option>');
            }
            if(firstTime){
                let cache = JSON.parse(localStorage.getItem("loadReportCache"));
                let selectedClientId =( cache !== null && 'clientId' in cache)?cache['clientId']:null;
                if (selectedClientId !== null) {
                    clientList.val(selectedClientId);
                }
            }
            clientList.selectmenu( "refresh" );

            loadBase(true);
        },
        error: function () {
            }
    });
}
// DB's
function loadBase(firstTime = false){
    let clientId = clientList.val();
    $.ajax({
        dataType: "json",
        type:'GET',
        url: WEB_SERVICE_URL+'/ressources/'+clientId,
        data:{token:localStorage.getItem('token')},
        success: function (result) {

            $('#baseList').empty();
            for (var index in result) {
                value = result[index]['name'];
                baseList.append('<option value=' + result[index]['id'] + '>' + value + '</option>');
            }

            if (firstTime){
                let cache = JSON.parse(localStorage.getItem("loadReportCache"));
                let selectedBaseId =( cache !== null && 'baseId' in cache)?cache['baseId']:null;
                if (selectedBaseId !== null  ) {
                    baseList.val(selectedBaseId);
                }
            }
            baseList.selectmenu( "refresh" );
            loadInstances(firstTime);
        },
        error: function () {
            $('#version').text('');
            baseList.empty();
            baseList.selectmenu( "refresh" );
            loadInstances();
        }
    });
}
// SERVER INSTANCES
function loadInstances(firstTime = false){
    let clientId = $('#clientList').val();
    let baseId = $('#baseList').val();
    $.ajax({
        dataType: "json",
        type:'GET',
        url: WEB_SERVICE_URL+'/ressources/'+clientId+'/'+baseId,
        data:{token:localStorage.getItem('token')},
        success: function (result) {
            instanceList.empty();
            for (let index in result) {
                value = result[index]['serverName'];
                instanceList.append('<option value=' + result[index]['id'] + '>' + value + '</option>');
                version = result.version;
                $('#version').text(version);
            }
            let index = Object.keys(result)[0];
            nbSnapshots = result[index]['nbSnapshots'];
            $('#snapshot-count').text(nbSnapshots);
            if(nbSnapshots >= 0){
                datePicker.data('daterangepicker').setMinDate(result[index]['minDate']);
                datePicker.data('daterangepicker').setMaxDate(result[index]['maxDate']);
                datePicker.data('daterangepicker').setEndDate(result[index]['maxDate']);
                console.log(moment(result[index]['maxDate'],"DD/MM/YYYY-HH:mm"));
                datePicker.data('daterangepicker').setStartDate(moment(result[index]['maxDate'],"DD/MM/YYYY HH:mm").subtract(7, 'days').toDate());
                datePicker.prop('disabled', false);
                $("#form_submit").prop('disabled', false);
            }else{
                datePicker.prop('disabled', true).val('');
                $("#form_submit").prop('disabled', true);
            }
            if (firstTime == true){
                let cache = JSON.parse(localStorage.getItem("loadReportCache"));
                let selectedInstanceId =( cache !== null && 'instanceId' in cache)?cache['instanceId']:null;
                if (selectedInstanceId !== null){
                    instanceList.val(selectedInstanceId).selectmenu( "refresh" ).change();
                    let datePickerCache = (cache !== null && 'date' in cache)?cache['date']:null;
                    if (datePickerCache !== null){
                        let startDate = datePickerCache["startDate"];
                        let endDate = datePickerCache["endDate"];
                        datePicker.data('daterangepicker').setEndDate(endDate);
                        datePicker.data('daterangepicker').setStartDate(startDate);
                    }
                }
            }
            instanceList.selectmenu( "refresh" );
        },
        error: function () {
            instanceList.empty();
            instanceList.selectmenu( "refresh" );
            $('#snapshot-count').text('');
            dateRangePickerProperty.minDate = moment();
            dateRangePickerProperty.maxDate = moment();
            $('input[id="datepicker"]').prop('disabled', true).val('');
            $("#form_submit").prop('disabled', true);
        }
    });
}