$.extend(true, Export.labels, {
    Positions: {
        generated: 'Utworzona'
    }
});

$.extend(true, Export.fields, {
    Positions: {
        basic: ['number', 'created'],
        basicReport: ['number', 'created']
    }
});

$.extend(Export.xls.generators, {
    Positions: {
        generate: function(xls) {
            this.xls = xls;
            xls.generators.title('Pozycja');
        }
    }
});

$.extend(Export.pdf.converters, {
    Positions: {}
});

$.extend(Export.pdf.generators, {
    Positions: {}
});
