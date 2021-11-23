/**
 * Created by simonvivier on 31/07/17.
 */
/**
 * Created by simonvivier on 15/03/17.
 */
function invertColor(hex) {
    if (hex.indexOf('#') === 0) {
        hex = hex.slice(1);
    }
    // convert 3-digit hex to 6-digits.
    if (hex.length === 3) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }
    if (hex.length !== 6) {
        throw new Error('Invalid HEX color.');
    }
    // invert color components
    var r = (255 - parseInt(hex.slice(0, 2), 16)).toString(16),
        g = (255 - parseInt(hex.slice(2, 4), 16)).toString(16),
        b = (255 - parseInt(hex.slice(4, 6), 16)).toString(16);
    // pad each with zeros and   return
    return '#' + padZero(r) + padZero(g) + padZero(b);
}

function padZero(str, len) {
    len = len || 2;
    var zeros = new Array(len).join('0');
    return (zeros + str).slice(-len);
}

charty.Advisory = function Advisory(id, graphLayout) {
    this.id = id;
    this.parameters = graphLayout.parameter || null;
    let chartProperty = {
        data: {
            type: 'area'
        },
        point: {
            //show: false
        },

        line: {
            connectNull: true,
        },
        axis: {
            x: {
                padding: {left: 0, right: 0}
            },
            y: {
                padding: {top: 0, bottom: 1}
            }
        }
    };
//-----------------------------------------create()-------------------------------------------------------------------//

    charty.requests.push($.ajax({
        url:  ( graphLayout.parameters.name_filter != null)?charty.URL+'/advisory/'+graphLayout.parameters.name_filter:charty.URL+'/advisory',
        type: 'POST',
        context: this,
        async: true,
        data: {
            instanceId: charty.instanceId,
            token: localStorage.getItem('token')
        },
        success: function (data) {
            let currentMemoryMap = data['memoryMap'];
            delete data['memoryMap'];
            let xmap = {};
            let jsonGraph = {};
            for(let advisoryName in data){
                let advisoryMap = data [advisoryName];
                let xAxisName = advisoryName+'_x';
                xmap[advisoryName] = xAxisName;
                jsonGraph[advisoryName] = Object.values(advisoryMap);
                jsonGraph[xAxisName] = Object.keys(advisoryMap);
            }

            this.chart = c3.generate(jQuery.extend(true, {
                bindto: '#' + this.id,
                oninit: function () {
                    charty.loaded();
                },
                data: {
                    xs:xmap,
                    json: jsonGraph,
                    color: function (color, d) {
                        if(d !== null
                            && typeof d === 'object'
                            && "x" in d
                            && d.x === currentMemoryMap[d.id]){
                            return '#000000';
                        }else{
                            return  generateColor(hashCode(id));
                        }
                    }
                },
                tooltip:{
                    format: {
                        title: function (d) {
                            return d;
                        },

//            value: d3.format(',') // apply this format to both y and y2
                    }
                }
        }, chartProperty, graphLayout.graphProperty));
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
};