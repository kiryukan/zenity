/**
 * Created by simonvivier on 15/03/17.
 */

charty.Gauge = function Gauge(id, graphLayout) {
    this.id = id;
    this.dataLayout = graphLayout.dataLayout || null;
    this.parameters = graphLayout.parameter || null;
    this.updateOnClick = graphLayout.updateOnClick || null;
    let chartProperty = {
        data: {
            type: 'gauge'
        },
        color: {
            pattern: ['#FF0000', '#ff8000', '#cccc00', '#269900'], // the three color levels for the percentage values.
            threshold: {
                values: [7, 13, 17, 16]
            }
        },
        gauge: {
            label: {
                format: function (value, ratio) {
                    return Math.round(value);
                }
            },
            min: 0,
            max: 20,
            unit: ''
        },
        tooltip: {
            format: {
                value: function (value, ratio, id) {
                    return parseFloat(Math.round(value));
                }
            }
        }
    };
//-----------------------------------------create()-------------------------------------------------------------------//

    charty.requests.push($.ajax({
        url: charty.URL,
        type: 'POST',
        context: this,
        async: true,
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
            this.chart = c3.generate(jQuery.extend(true, {
                bindto: '#' + this.id,
                oninit: function () {
                    charty.loaded();
                },
                data: {
                    columns: data,
//-----------------------------------------onclick()-------------------------------------------------------------------//
                    onclick: function (d, element) {
                        if (d.id != "other") {
                            for (let i in graphLayout.updateOnClick) {
                                boundedElement = graphLayout.updateOnClick[i];
                                id = d.id;
                                color = $(element).css('fill');
                                if (object = charty.objects[boundedElement]) {
                                    let colors = {};
                                    colors[id] = [generateColor(hashCode(id))];
                                    object.update(id, {
                                        colors: colors
                                    });
                                }
                            }
                        }
                    }
                },
            }, chartProperty, graphLayout.graphProperty));
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