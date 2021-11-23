//-------------------------------------RegisterTemplate-----------------------------------/
$.ajax({
    async: false,
    type: 'GET',
    url: '/charty/objects/informationTable/informationRow.hbs',
    success: function (template) {
        Handlebars.registerPartial('informationRow',Handlebars.compile(template));
    }
});
$.ajax({
    async: false,
    type: 'GET',
    url: '/charty/objects/informationTable/informationTable.hbs',
    success: function (template) {
        Handlebars.registerPartial('informationTable',Handlebars.compile(template));
    }
});

//----------------------------------------------------------------------------------------------
charty.InformationTable =  function (id,layout){
    this.indicators = [];

    this.id = id;
    this.tableTemplate = Handlebars.compile('{{> informationTable }}');
    this.rowTemplate = Handlebars.compile('{{> informationRow }}');

    this.dataLayout = layout.dataLayout;
    this.parameters = layout.parameter || null;

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
            let data = charty.gatherDataFromLayout(d, layout.data);
            //console.log("valeur de d: "+d);
            this.indicators = [];
            for(let gatherType in layout.data){
                for(let classKey in layout.data[gatherType]){
                    if(layout.data[gatherType][classKey].indicators == "*"){
                        this.indicators = (Object.keys(d[gatherType][classKey]));
                    }else{
                        for(let indicatorKey in layout.data[gatherType][classKey].indicators){
                            this.indicators.push(layout.data[gatherType][classKey].indicators[indicatorKey]);
                        }
                    }
                }
            }
            let templateData = { // ?? Element row ??
                headRow:[
                    {title:"name"}
                ],
                row:{
                    elements:[]
                }
            };
            // function
            for (let i in this.indicators){                
                let indicator = this.indicators[i];
                //console.log("indicator: "+indicator);
                    templateData.headRow.push({title:indicator});
                    //console.log("title indicator: "+templateData.headRow[title]);
            }
            let tmpData = {}; // recup données dans tab temporaire
            for(let i in this.indicators){
                let indicator = this.indicators[i];
                for(let elementKey in data[indicator]){
                    if(elementKey in tmpData === false){
                        tmpData[elementKey] = {};
                    }
                    tmpData[elementKey][indicator] = data[indicator][elementKey];
                }
            }
            for(let elementKey in tmpData){
                templateData.row[elementKey] = {
                    title:elementKey,
                    content:[]
                };
                for(let i in this.indicators){
                    let indicator = this.indicators[i];
                    if(indicator in tmpData[elementKey]){
                        let value = tmpData[elementKey][indicator];
                        //console.log(value);

                        if($.isNumeric( value )){
                            value =parseFloat(value);
                            value = parseFloat(value.toFixed(2));
                            templateData.row[elementKey].content.push(value);
                            //console.log("templateData: " +templateData.row[elementKey]);
                        }else{
                            templateData.row[elementKey].content.push("");
                        }
                    }else{
                        templateData.row[elementKey].content.push("null");
                    }
                }
            }
            $('#'+this.id).append(this.tableTemplate(templateData));
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

    this.update = function(name_filter,property){
        let re = new RegExp('(\\(|\\)|\\s|\\*|\\:)', 'g');
        let htmlContentId = this.id +'-' + name_filter.replace(re,"_");
        let idSelector = $('#'+htmlContentId);
        if(idSelector.length <= 0){
            let parameters = (this.parameter) ? this.parameter : {};
            parameters.name_filter = name_filter;
            charty.requests.push($.ajax({
                url: charty.URL,
                context: this,
                async: true,
                data: {
                    layout: JSON.stringify({data: this.dataLayout, parameters: parameters}),
                    from: charty.from,
                    to: charty.to,
                    instanceId: charty.instanceId,
                    token:localStorage.getItem('token')
                },
                success: function (d) {
                    let data = charty.gatherDataFromLayout(d, this.dataLayout);
                    let tmpData = {};
                    for(let i in this.indicators){
                        indicator = this.indicators[i];
                        for(elementKey in data[indicator]){
                            if(elementKey in tmpData === false){
                                tmpData[elementKey] = {};
                            }
                            element = data[indicator][elementKey];
                            tmpData[elementKey][indicator] = element;
                        }
                    }
                    let templateData = [];
                    for(let elementKey in tmpData){
                        templateData={
                                title:elementKey,
                                content:[]
                            };
                            ///console.log("elKey: " + elementKey);
                        for(let i in this.indicators){
                            let indicator = this.indicators[i];
                            if(indicator in tmpData[elementKey]){
                                let value = tmpData[elementKey][indicator];
                                if($.isNumeric( value )){
                                    value = parseFloat(value.toFixed(2));
                                }
                                templateData.content.push(value);
                            }else{
                                templateData.content.push("null");
                            }
                        }
                    }
                    templateData.id = htmlContentId;
                    templateData.color = property.color;
                    $('#'+this.id).find('tbody').append(this.rowTemplate(templateData));
                }
            }));

        }else if(idSelector.is(":visible") ) {
            idSelector.hide();
        }else if(idSelector.is(":visible") == false){
        idSelector.show();
    }

    };

};