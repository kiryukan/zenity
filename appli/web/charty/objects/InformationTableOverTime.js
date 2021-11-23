//-------------------------------------RegisterTemplate-----------------------------------/
$.ajax({
    async: false,
    type: 'GET',
    url: '/charty/objects/informationTable/informationRow.hbs',
    success: (template)=>{
        Handlebars.registerPartial('informationRow',Handlebars.compile(template));
    }
});
$.ajax({
    async: false,
    type: 'GET',
    url: '/charty/objects/informationTable/informationTable.hbs',
    success: (template)=>{
        Handlebars.registerPartial('informationTable',Handlebars.compile(template));
    }
});
$.ajax({
    async: false,
    type: 'GET',
    url: '/charty/objects/informationTable/rangeSelectorInput.hbs',
    success: (template)=>{
        Handlebars.registerPartial('rangeSelector',Handlebars.compile(template));
    }
});
$.ajax({
    async: false,
    type: 'GET',
    url: '/charty/objects/informationTable/rangeSelectorTooltip.hbs',
    success: (template)=>{
        Handlebars.registerPartial('rangeSelectorTooltip',Handlebars.compile(template));
    }
});
//----------------------------------------------------------------------------------------------
charty.InformationTableOverTime =  function (id,layout){
    this.indicators = [];
    this.id = id;
    this.tableTemplate = Handlebars.compile('{{> informationTable }}');
    this.rowTemplate = Handlebars.compile('{{> informationRow }}');
    this.rangeSelectorTemplate = Handlebars.compile('{{> rangeSelector }}');
    this.rangeSelectorTooltipTemplate = Handlebars.compile('{{> rangeSelectorTooltip }}');
    this.dataLayout = layout.dataLayout;
    this.parameters = layout.parameter || null;
    this.modificationPoints = [];
    this.xAxis = [];
    this.altered = false;
//-----------------------------------------create()-------------------------------------------------------------------//
    charty.requests.push($.ajax({
        url: charty.URL,
        context: this,
        async: true,
        type:'POST',
        data: {
            layout: JSON.stringify({data: layout.data, parameters: layout.parameters}),
            from: charty.from,
            to: charty.to,
            instanceId: charty.instanceId,
            token:localStorage.getItem('token')
        },
        success: function (d) {
            this.xAxis = d['x'];
            let nbSnapshot = this.xAxis.length-1;
            delete d['x'];
            this.data = d;
            this._load(nbSnapshot);
            this.initRangeSelector();
            charty.loaded();
        },
        error(xhr){
            if (xhr.statusText !== 'abort') {
                let err = JSON.parse(xhr.responseText);
                charty.error(err,this);
            }else{

            }
        }
    }));

//-----------------------------------------Update-------------------------------------------------------------------//

    this.update = function(indicator,value){
        if (!value){
            value = "DEFAULT";
        }
        $("#"+this.id+"-"+indicator+"-value").html(value);
    };
    this._load = function(loadingPosition){
        for(let gatherType in this.data){
            let classesContent = this.data[gatherType];

            for(let classKey in classesContent){
                let elements = classesContent[classKey];

                    let templateData = {
                        headRow:[
                            {title:"paramètre"},
                            {title:"valeur"},
                            {title:"historique"}
                        ],
                        row:{}
                    };
                for(let elementKey in elements) {
                    for (let indicator in elements[elementKey]) {
                        let indicatorOverTime = elements[elementKey][indicator];
                        let lastIndicator = indicatorOverTime[loadingPosition] ? indicatorOverTime[loadingPosition] : "DEFAULT";
                        let content = [];
                        content.push("<div id=" + this.id + "-" + indicator + "-value>" + lastIndicator + "</div>");
                        let sliderDiv = document.createElement("div");
                        sliderDiv.setAttribute("id", this.id + "-" + indicator + "-slider-wrapper");
                        content.push(sliderDiv.outerHTML);
                        templateData.row[elementKey] = {
                            id: this.id + "-" + indicator,
                            title: elementKey,
                            content: content
                        };
                        //console.log("titre: "+ elementKey);
                    }
                }
                $('#'+this.id).prepend(this.tableTemplate(templateData));
            }
        }
    };
    this.initRangeSelector = function(){
        for(let gatherType in this.data) {
            let classesContent = this.data[gatherType];
            for (let classKey in classesContent) {
                let elements = classesContent[classKey];
                for (let elementKey in elements) {
                    for (let indicator in elements[elementKey]) {
                        let indicatorOverTime = elements[elementKey][indicator];
                        let oldIndicatorValue = indicatorOverTime[this.xAxis.length - 1];
                        for (let snapshotId in indicatorOverTime) {
                            let indicatorValue = indicatorOverTime[snapshotId];
                            if (oldIndicatorValue !== indicatorValue) {
                                if (!(indicator in this.modificationPoints)) {
                                    this.modificationPoints[indicator] = {};
                                }
                                this.modificationPoints[indicator][snapshotId] = (indicatorValue)?indicatorValue:"DEFAULT";
                            }
                            oldIndicatorValue = indicatorValue;
                        }
                    }
                }
            }
        }
        for (let indicator of Object.keys(this.modificationPoints)){
            let tooltip = $('<div class="slider-tooltip" />').hide();
            let that = this;
            let max = Object.keys(that.modificationPoints[indicator]).length -1;
            tooltip.text(that.xAxis[max]);
            $("#"+this.id+"-"+indicator+"-slider-wrapper").slider({
                min: 0,
                max: max,
                value: max,
                slide: function(event, ui) {
                    let key = Object.keys(that.modificationPoints[indicator])[ui.value];
                    tooltip.text(that.xAxis[key]);
                    that.update(indicator,that.modificationPoints[indicator][key]);
                },
                change: function(event, ui) {}
            })
            .find(".ui-slider-handle").append(tooltip).hover(function() {
                tooltip.show();
            }, function() {
                tooltip.hide();
            });
        }
    };
};
