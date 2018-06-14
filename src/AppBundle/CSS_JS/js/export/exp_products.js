$.extend(true, Export.labels, {
    Products: {
        generated: 'Utworzona'
    }
});

$.extend(true, Export.fields, {
    Products: {
        basic: ['number', 'created'],
        basicReport: ['number', 'created']
    }
});

$.extend(Export.xls.generators, {
    Products: {
        order: function() {},
        generate: function(xls) {
            this.xls = xls;
            xls.generators.title('Produkt');
        }
    }
});

$.extend(Export.pdf.converters, {
    Products: {}
});

$.extend(Export.pdf.generators, {
    Products: {}
});
