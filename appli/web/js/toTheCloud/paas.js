$.ajax({
    async: false,
    type: 'GET',
    url: "/js/toTheCloud/paas.hbs",
    success: function (template) {
        Handlebars.registerPartial('paas', Handlebars.compile(template));
    }
})
function generatePaas(clientId,baseId,instanceId){
    $.ajax({
        async: true,
        type: 'GET',
        url: `${WEB_SERVICE_URL}/ressources/${clientId}/${baseId}/${instanceId}/${byol}/${licence}/cloudPricing`,
        success: function (data) {
            template = Handlebars.compile('{{> paas}}');
            console.log("paas values: "+data);
            $('#tothecloud-paas-container').html(template(data));
        }
    })
}