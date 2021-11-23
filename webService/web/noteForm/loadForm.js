/**
 * Created by simonvivier on 20/01/17.
 */
function getMetadata(){
    if(null === localStorage.getItem('metadata') ){
        $.ajax({
            url: `${config.serverPath}/gestion/metadata`,
            dataType: 'json',
            async: false,
            success: function (jsonData) {
                localStorage.setItem('metadata',JSON.stringify({classes : jsonData}));
            }
        });
    }
    console.log(JSON.parse(localStorage.getItem('metadata')));
    return JSON.parse(localStorage.getItem('metadata'));
    //return  JSON.parse(sessionStorage.getItem('metadata'));
}
function loadForm(formName) {
    $.ajax({
        url: `${config.serverPath}/gestion/AuditEngine/noteData`,
        dataType: 'json',
        async: false,
        success: function ($data) {
            let templateData = [];
            let noteArray = $data.notes;
            if (noteArray.length >= 1 ){
                for (let noteKey in noteArray) {
                    let note = noteArray[noteKey];
                    let noteTemplateData = {
                        id: note.id,
                        name:note.name,
                        isAvg : (note.isAvg)?true:null,
                        indicator:[],
                    };
                    if (note.indicator.length >= 1 ) {
                        for (indicatorKey in note['indicator']) {
                            indicator = note['indicator'][indicatorKey];
                            let indicatorTemplateData = {
                                id:indicator.id,
                                class:loadClasses(indicator.class),
                                property : loadProperty(indicator.class, indicator.field),
                                isAvg : (note.isAvg)?true:null,
                                propertyType : loadPropertyType(indicator.class,indicator.field),
                                minValue : indicator.minValue,
                                maxValue : indicator.maxValue,
                                coeff : indicator.coeff,
                                filter : loadProperty(indicator.class, indicator.filter.field),
                                filterInput : {
                                    value:indicator.filter.value,
                                    filterType : loadPropertyType(indicator.class,indicator.filter.field)
                                },
                            };
                            noteTemplateData['indicator'].push(indicatorTemplateData);
                        }
                    }
                    templateData.push(noteTemplateData)
                }
            }
            let noteTemplate = Handlebars.compile('{{> noteForm }}');
            $(formName).append(noteTemplate({note:templateData}));
        }
    });
}

function loadClasses(selectedClass){

    let metaData = getMetadata();
    return $.map((metaData['classes']) , function (value, key) {
        if(null != selectedClass && key === selectedClass){
            return {selected:true,name:key};
        }else{
            return {name:key};
        }
    });
}
function loadProperty(className,selectedProperty){
    let metaData = getMetadata();
    if(className != null && className in metaData['classes']){
        data = $.map((metaData['classes'][className]['properties']) , function (value, key) {
            if(key === selectedProperty){
                return {selected:true,name:key};
            }else{
                return {name:key};
            }
        });
        return data;
    }
    else return {};
}
function loadPropertyType(className,propertyName){
    let metaData = getMetadata();

    if(null != propertyName
        && null != className
        && className in metaData['classes']
        && propertyName in metaData['classes'][className]['properties'])
    {
        let typeSet = {};
        let type = metaData['classes'][className]['properties'][propertyName]['type'];
        typeSet[type] = true;
        return typeSet;
    }
    return 'none';
}
loadForm('#noteForm');
