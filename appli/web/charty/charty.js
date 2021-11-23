 /**
 * Created by simonvivier on 14/03/17.
 */
/**
 * Created by simonvivier on 14/03/17.
 */
function registerPartials(partialName) {
    return $.ajax({
        async: false,
        type: 'GET',
        url: charty.TEMPLATE_PATH + partialName + ".hbs",
        success: function (template) {
            Handlebars.registerPartial(partialName, Handlebars.compile(template));
        }
    });
}
function generateColor(seed, normalizer) {
    normalizer = normalizer | 16777215;
    return '#' + Math.floor(((Math.abs(seed) % 16777215) + normalizer ) / 2)
            .toString(16);
}

hashCode = function (str) {
    let hash = 0;
    if (str.length == 0) return hash;
    for (i = 0; i < str.length; i++) {
        char = str.charCodeAt(i);
        hash = ((hash << 5) - hash) + char;
        hash = hash & hash; // Convert to 32bit integer
    }
    return hash;
};
window.onbeforeunload = function(){
    charty.cancelRequests();
};
let charty = {
    URL: WEB_SERVICE_URL + '/graph',
    GRAPH_PATH: "/charty/objects/",
    TEMPLATE_PATH: "/charty/template/",
    LAYOUT_PATH: "/charty/layout/",
    objects: [],
    requests: [],
    from : null,
    to :null,
    instanceId : null,
    loadingObjects:0,
    loadedObjects:0,
};
charty.error = (err,graph)=>{
    switch (err.code ){
        case 101:
            charty.cancelRequests();
            alert(err.msg);
            window.location.replace('/');
            break;
        case 102:
            $('#'+graph.id).parent().html('');
            // $('#'+graph.id).html('<div class="warn">'+err.msg+'</div>');
            charty.loaded();
            break;
        default :
            charty.cancelRequests();
            window.location.replace('/');
    }
};
charty.cancelRequests = ()=>{
    for (let requests of charty.requests){
        requests.abort();
    }
};
charty.setDate = function (from, to) {
    charty.from = from;
    charty.to = to;
};
charty.setInstanceId = function (instanceId) {
    charty.instanceId = instanceId;
};
charty.generateTemplate = function generateTemplate(id, layoutName) {
    $( "#progressbar-wrapper" ).show();
    $.ajax({
        url: charty.LAYOUT_PATH + layoutName + ".json",
        async: true,

        success: function (data) {
            let templateId = data.id;
            let partialName = data.template;
            let template;
            let graphLayoutArray;

            if ($('#' + templateId).length) {
                console.log("try to load graph multiple time");
                return;
            }
            if ($.inArray(partialName, Handlebars.partials)) {
                registerPartials(partialName);
            }
            template = Handlebars.compile('{{>' + partialName + ' }}');
            graphLayoutArray = data.graph;
            $('#' + id).append(template(data));
            $.each(graphLayoutArray, function (i, graphLayout) {
                let graphId = templateId + '-' + i;
                charty.loadingObjects ++;
                charty.loadedObjects++;
                charty.objects[graphId] = charty.create(graphId, graphLayout);
            });
        },
        error: function(error){
            console.error(error);
        }
    });

};

/**
 * limit the avg and total data /!\ dont work for overtime
 * @param data
 * @param limit
 */
charty.limitData = function (data, limit) {
    for (let key in data) {
        let tmpDataArray = [];
        let tmpData = {};
        let other = 0;
        for (let subKey in data[key]) {
            tmpDataArray.push({key: subKey, value: data[key][subKey]});
        }
        tmpDataArray.sort(function (a, b) {
            return b.value - a.value;
        });
        for (let i in tmpDataArray) {
            if (i < limit) {
                tmpData[tmpDataArray[i].key] = tmpDataArray[i].value;
            } else {
                other += tmpDataArray[i].value;
            }
        }
        if (other > 0) {
            tmpData['other'] = other;
        }
        data[key] = tmpData;
    }
};
/**
 *
 * @param data
 */
charty.reformatData = function (data) {
    let dataToReturn = [];
    for (let key in data) {
        let value = data[key];
        if (data[key].length == 1 || Object.keys(data[key]).length == 1) {
            let elementKey = Object.keys(data[key])[0];
            let element = value[elementKey];
            if (Array.isArray(element) === false) {
                element = [element];
            }
            if (elementKey == "0") {
                element.unshift(key);
            } else {
                element.unshift(elementKey);
            }
            for (let elementKey in element){
                element[elementKey] = (element[elementKey] !== null)?element[elementKey]:0;
            }
            dataToReturn.push(element);
        }
        else {
            for (let elementName in data[key]) {
                if (Array.isArray(data[key][elementName]) === false) {
                    dataToReturn.push([elementName, value[elementName]]);
                } else {
                    dataToReturn[key + "." + elementName] = value[elementName];
                    dataToReturn[key + "." + elementName].unshift(key + "." + elementName);
                }
            }
        }
    }
    return dataToReturn;
};
charty.gatherDataFromLayout = function (data, layout) {
    let returnedData = {};
    for (let gatherType in layout) {
        for (let className in layout[gatherType]) {
            let indicators = layout[gatherType][className]['indicators'];
            jQuery.extend(returnedData, data[gatherType][className]);
            //returnedData = returnedData.concat(data[gatherType][className]);
        }
    }
    return returnedData;
};
// Progress Bar
charty.loaded = function () {
    charty.loadingObjects --;
    let progression = parseInt((1-(charty.loadingObjects/charty.loadedObjects))*100,10);
    let progressBarWrapperTarget = $( "#progressbar-wrapper" );
    let progressBarTarget = $("#progressbar");
    progressBarWrapperTarget.show();
    progressBarTarget.attr("aria-valuenow",progression);
    progressBarTarget.css("width",+progression+"%");
    progressBarTarget.text(progression+"%");
    if (charty.loadingObjects === 0){
        progressBarWrapperTarget.hide();
    }
};
charty.renderInformationBar = function (id) {
    $.ajax({
        type: 'GET',
        url: WEB_SERVICE_URL + "/instanceMetadata" + "/"+charty.instanceId,
        data:{token:localStorage.getItem('token')},
        success: function (metadata) {

            console.log(JSON.stringify(metadata));
            if (! metadata || metadata == [])return;
            let informationBarWrapper = $("#"+id);
            let partialName = "InformationBanner";
            if ($.inArray(partialName, Handlebars.partials)) {
                registerPartials(partialName);
            }
            template = Handlebars.compile('{{>' + partialName + ' }}');
            metadata.startDate  = charty.from;
            metadata.endDate = charty.to;
            if("baseName" in metadata){
                document.title = "Rapport sur "+ metadata.baseName;
            }
            $("#cloud-cpu-count").html('<span class="cpu-infos">' + metadata.OCPU + ' OCPU' + ' (equal to ' + metadata.cpuCount + ' vcpu and '+ metadata.nbCores +' cores) </span><br />');
            informationBarWrapper.append(template(metadata));
        }
    });
};
