$.ajax({
    async: false,
    type: 'GET',
    url: `${config.serverPath}/noteForm/template/indicator/classList.hbs`,
    success: function (template) {
        Handlebars.registerPartial('classList',Handlebars.compile(template));
    }
});

$.ajax({
    async: false,
    type: 'GET',
    url: `${config.serverPath}/noteForm/template/indicator/filterInput.hbs`,
    success: function (template) {
        Handlebars.registerPartial('filterInput',Handlebars.compile(template));
    }
});

$.ajax({
    async: false,
    type: 'GET',
    url: `${config.serverPath}/noteForm/template/indicator/filterList.hbs`,
    success: function (template) {
        Handlebars.registerPartial('filterList',Handlebars.compile(template));
    }
});

$.ajax({
    async: false,
    type: 'GET',
    url: `${config.serverPath}/noteForm/template/indicator/propertyInput.hbs`,
    success: function (template) {
        Handlebars.registerPartial('propertyInput',Handlebars.compile(template));
    }
});
$.ajax({
    async: false,
    type: 'GET',
    url: `${config.serverPath}/noteForm/template/indicator/propertyList.hbs`,
    success: function (template) {
        Handlebars.registerPartial('propertyList',Handlebars.compile(template));
    }
});
$.ajax({
    async: false,
    type: 'GET',
    url: `${config.serverPath}/noteForm/template/indicator/indicator.hbs`,
    success: function (template) {
        Handlebars.registerPartial('indicator',Handlebars.compile(template));
    }
});
$.ajax({
    async: false,
    type: 'GET',
    url: `${config.serverPath}/noteForm/template/note.hbs`,
    success: function (template) {
        Handlebars.registerPartial('note',Handlebars.compile(template));
    }
});

$.ajax({
    async: false,
    type: 'GET',
    url: `${config.serverPath}/noteForm/template/noteForm.hbs`,
    success: function (template) {
        Handlebars.registerPartial('noteForm',Handlebars.compile(template));
    }
});
