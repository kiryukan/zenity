/**
 * Created by simonvivier on 13/03/17.
 */

charty.LineGraph = function LineGraph(id, graphLayout) {

    this.id = id;
    this.dataLayout = graphLayout.dataLayout || null;
    this.parameters = {
        data: {
            x: 'x',
            xFormat: '%Y-%m-%d %H:%M:%S',
            type: 'line',
            groups: [
                [],
                [],
                []
            ]
        },
        point: {
            show: false
        },
        color: {
            pattern: [
                generateColor(hashCode(id))
            ]
        },
        line: {
            connectNull: true,
            label: {
                format: function (value, ratio, id) {
                    return id.split('.')[0];
                }
            } 
        },
        axis: {
            x: {
                type: 'timeseries',
                tick: {
                    format: '%d-%m-%y %Hh%Mm',
                    centered: true,
                    culling: {
                        max: 8
                    },
                    fit: false
                },
                padding: {left: 0, right: 0}
            },
            y: {
                padding: {top: 0, bottom: 1}
            }
        },
        tooltip: {
            format: {
                value: function (value, ratio, id) {
                    return parseFloat(value.toFixed(2));
                }
//            value: d3.format(',') // apply this format to both y and y2
            }
        }

    };
    charty.requests.push($.ajax({
        url: charty.URL,
        context: this,
        async: true,
        type: 'POST',
        data: {
            layout: JSON.stringify({data: graphLayout.data, parameters: graphLayout.parameters}),
            from: charty.from,
            to: charty.to,
            instanceId: charty.instanceId,
            token: localStorage.getItem('token')
        },
        success: function (d) {
            let data = charty.gatherDataFromLayout(d, graphLayout.data);
            data = charty.reformatData(data);
            d.x.unshift('x');
                          //var data = data;
            let isFillByNull = true;
             for (let key in data[0]){
                 let value = data[0][key];
                 if (key != 0 && value != null){
                     isFillByNull = false;
                     break;
                 }
            }
            if (isFillByNull){
                $("#this.id").hide();
                charty.loaded();
                return
            }
            data.push(d.x);
            this.chart = c3.generate(jQuery.extend(true, {
                oninit: function () {
                    charty.loaded();
                },
                bindto: '#' + this.id,
                data: {
                    columns: data,
                }
            },this.parameters,graphLayout.graphProperty));
            if ("preload" in graphLayout){
                this.preload(graphLayout.preload,{});
            }
            this.chart.flush();
        },
        error(xhr){
            if (xhr.statusText !== 'abort') {
                let err = JSON.parse(xhr.responseText);
                charty.error(err,this);
            }else{

            }
        }
    }));

    this._addData = function (nameFilter, graphProperty) {
        if (this.chart.data(nameFilter).length > 0) {
            this.chart.toggle(
                [nameFilter],
                {withLegend: false}
            );
        } else {
            let parameters = (this.parameter) ? this.parameter : {};
            parameters.name_filter = nameFilter;
            charty.requests.push($.ajax({
                context: this,
                url: charty.URL,
                async: true,
                data: {
                    layout: JSON.stringify({data: this.dataLayout, parameters: parameters}),
                    from: charty.from,
                    to: charty.to,
                    instanceId: charty.instanceId,
                    token: localStorage.getItem('token')
                },
                dataType: 'json',
                success: function (d) {
                    let data = charty.gatherDataFromLayout(d, this.dataLayout);
                    data = charty.reformatData(data);
                    this.chart.load(jQuery.extend({
                        columns: data,
                        type: 'area',
                        hide: true,
                    }, graphProperty));
                    let groups = this.chart.groups();
                    if(groups[1].indexOf(nameFilter) != -1){
                        groups[1].push(nameFilter);
                        this.chart.groups(groups);
                    }
                },
                error: (err)=>{
                    console.error("_addData",err);
                }
            }));
        }

    };
    this.preload = function (nameFilterArray,options) {
        for (let nameFilter of nameFilterArray){
            let colors = {} ;
            colors[nameFilter] = [generateColor(hashCode(nameFilter))];
            this._addData(nameFilter,{
                colors: colors
            });
            this.chart.hide(nameFilter,{withLegend: false});
        }
    };
    this.update = function (nameFilter, graphProperty, i) {

        i = i || 1;
        graphProperty = graphProperty || null;
        if ('chart' in this && this.dataLayout) {
            this._addData(nameFilter, graphProperty);
        } else if (i <= 3) {
            setTimeout(function () {
                this.update(nameFilter, graphProperty, i + 1)
            }, 500);
        }
        if (i == 3) {
            console.log("addData has failed,chart isnt loaded or dataLayout is missing");
        }
    };

};

