/**
 * Created by simonvivier on 15/03/17.
 */

charty.PieChart = function PieChart(id, graphLayout) {
    this.id = id;
    this.dataLayout = graphLayout.dataLayout || null;
    this.parameters = graphLayout.parameter || null;
    this.updateOnClick = graphLayout.updateOnClick || null;
    chartProperty = {};
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
            charty.limitData(data, graphLayout.limit);
            data = charty.reformatData(data);
            let normalizer = null;
            if ('generatedColor' in graphLayout) {
                if (graphLayout.generatedColor !== true) {
                    normalizer = graphLayout.generatedColor;
                }
                let colors = {};
                for (i in data) {
                    id = data[i][0];
                    colors[id] = generateColor(hashCode(id), normalizer);
                }
                colors['other'] = '#D3D3D3';
                chartProperty = {
                    data: {
                        colors: {
                            colors
                        }
                    }
                };
            }
            this.chart = c3.generate(jQuery.extend(true, {
                oninit: function () {
                    charty.loaded();
                },
                bindto: '#' + this.id,
                data: {
                    columns: data,
                    type: 'pie',
                    order: null,
                    groups: [
                        [],
                        [],
                        []
                    ],
                    colors: {
                        other: "#D3D3D3"
                    },
                    tooltip: {
                        format: {
                            value: function (value, ratio, id) {
                                return parseFloat(value.toFixed(2));
                            }
//            value: d3.format(',') // apply this format to both y and y2
                        }
                    },
//----------------------------------------------------------------onclick()------------------------------------------------------
                    onclick: function (d, element) {
                        if (d.id != "other") {
                            for (let i in graphLayout.updateOnClick) {
                                let boundedElement = graphLayout.updateOnClick[i];
                                let id = d.id;
                                let color = $(element).css('fill');
                                if (object = charty.objects[boundedElement]) {
                                    let colors = {};
                                    colors[id] = [color];
                                    object.update(id, {
                                        colors: colors
                                    });
                                }
                            }
                        }
                    }
                }
            }, chartProperty, graphLayout.graphProperty));
            this.chart.flush();
            /**/
        },
        error(xhr){
            if (xhr.statusText !== 'abort') {
                let err = JSON.parse(xhr.responseText);
                charty.error(err,this);
            }else{

            }
        }
    }));
};