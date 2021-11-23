$.ajax({
    async: false,
    type: 'GET',
    url: "/js/toTheCloud/iaas.hbs",
    success: function (template) {
        Handlebars.registerPartial('iaas', Handlebars.compile(template));
    }
})
function generateIaas(clientId,baseId,instanceId){
    $.ajax({
        async: true,
        type: 'GET',
        url: `${WEB_SERVICE_URL}/ressources/${clientId}/${baseId}/${instanceId}/cloudPricing`,
        success: function (data) {
            template = Handlebars.compile('{{> iaas}}');
            //console.log("iaas values: "+data)
            $('#tothecloud-iaas-container').html(template(data))
        }
    })
}