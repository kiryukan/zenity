/**
 * Created by simonvivier on 15/03/17.
 */
charty.create = function (id,layout){
    let graphType = layout.type;
    switch (graphType){
        case 'LineGraph':
            return new charty.LineGraph(id,layout);
            break;
        case 'PieChart':
            return new charty.PieChart(id,layout);
            break;
        case 'InformationTable':
            return new charty.InformationTable(id,layout);
            break;
        case 'Gauge':
            return new charty.Gauge(id,layout);
            break;
        case 'LineGraph_note':
            return new charty.LineGraph_note(id,layout);
            break;
        case 'InformationTableOverTime':
            return new charty.InformationTableOverTime(id,layout);
            break;
        case 'Advisory':
            return new charty.Advisory(id,layout);
            break;
    }
};