/**
 * Created by simonvivier on 21/04/17.
 */
/**
 * Created by simonvivier on 13/03/17.
 */

charty.LineGraph_note = function (id, graphLayout) {
    chart = new charty.LineGraph(id, graphLayout);
    chart._addData = function (noteName, graphProperty) {
        if (this.chart.data(noteName).length > 0) {
            this.chart.toggle(
                noteName,
                {withLegend: false}
            );
        } else {
            let parameters = (this.parameter) ? this.parameter : {};
            let dataLayout = {
                data_overTime:{
                    Note:{
                        indicators:[
                            noteName
                        ]
                    }
                }
            };
            charty.requests.push($.ajax({
                context: this,
                url: charty.URL,
                async: true,
                data: {
                    layout: JSON.stringify({data: dataLayout, parameters: parameters}),
                    from: charty.from,
                    to: charty.to,
                    instanceId: charty.instanceId,
                    token: localStorage.getItem('token')
                },
                dataType: 'json',
                success: function (d) {
                    let data = charty.gatherDataFromLayout(d, dataLayout);
                    data = charty.reformatData(data);
                    this.chart.load(jQuery.extend({
                        columns: data,
                        type: 'line',
                    }, graphProperty));
                },
                error(xhr){
                    if (xhr.statusText !== 'abort') {
                        let err = JSON.parse(xhr.responseText);
                        charty.error(err,this);
                    }else{

                    }
                }

            }));
        }
    };
    chart.update = function (noteName, graphProperty, i) {
        i = i || 1;
        graphProperty = graphProperty || null;
        if ('chart' in this ) {
            this._addData(noteName, graphProperty);
        } else if (i <= 3) {
            setTimeout(function () {
                this.update(noteName, graphProperty, i + 1)
            }, 500);
        }
        if (i == 3) {
            console.error("addData has failed,chart isnt loaded or no data available");
        }
    };
    return chart;
};