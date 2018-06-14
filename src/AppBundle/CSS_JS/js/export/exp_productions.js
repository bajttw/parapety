$.extend(true, Export.labels, {
    Productions: {
        generated: 'Utworzona',
        pos_length: 'długość',
        pos_width:'szerokość',
        pos_size: 'grubość',
        pos_model: 'frez',
        pos_trims: 'opcje',
        pos_color: 'kolor',
        order_number: 'nr zamówienia',
        order_clientNumber: 'nr zamówienia klienta',
        order_client: 'klient',
        order_approved: 'data przyjęcia',
        order_quantity: 'ilość sztuk',
        pr_barcode: 'kod kreskowy',
        pr_clientBarcode: 'kod kreskowy klienta',
        nr_elementu: 'nr elementu',
        nazwa_produktu: 'nazwa produktu',
        nazwa_z_biblioteki: 'nazwa z bilblioteki',
        plyta	: 'płyta',
        kat_slojow: 'kąt słojów',
        kat_kroku: 'kąt kroku',
        FMCINCLUDE: 'FMCINCLUDE',
        FMCKONTUR: 'FMCKONTUR',
        one: 'one',
        order_id: 'id zamówienia'
            
    }
});

var _extraProduct={
    'nazwa_produktu': 'MDF',
    'nazwa_z_biblioteki': 'baza',
    'plyta'	: '',
    'kat_slojow': '0',
    'kat_kroku': '1',
    'FMCINCLUDE': 'baza.fmc',
    'FMCKONTUR': 'baza_k.fmc',
    'one': '1'
};

$.extend(true, Export.fields, {
    Productions: {
        basic: ['number', 'created'],
        basicReport: ['number', 'created'],
        order: {order_id: 'id', order_client : 'client.code', order_approved:'approved', order_number:'number', order_clientNumber:'clientNumber', order_quantity:'quantity'},
        position:{ pos_length:'length', pos_width:'width', pos_size: 'size.symbol', pos_model:'model.symbol', pos_trims:'trims', pos_color : 'color.symbol', plyta: 'size.symbol'},
        product: {pr_id : 'id', pr_label:'label', pr_clientLabel:'clientLabel', pr_barcode:'barcode', pr_clientBarcode:'clientBarcode', pr_number:'number', nr_element: 'number'},
        products: [ 'pos_length', 'pos_width', 'pos_size', 'pos_model', 'pos_trims', 'order_client', 'order_approved', 'order_number', 'order_clientNumber', 'pos_color', 'order_quantity', 'nr_element', 'nazwa_produktu', 'pr_barcode', 'pr_clientBarcode', 
            'pos_size',
            'kat_slojow',
            'kat_kroku',
            'FMCINCLUDE',
            'FMCKONTUR',
            'one',
            'order_id'
        ]       
 
    }
});

$.extend(Export.xls.converters, {
    Productions: {
        pos_length: Export.xls.converters.toInt, 
        pos_width: Export.xls.converters.toInt, 
        order_quantity: Export.xls.converters.toInt, 
        pr_barcode: function(value, diff) {
            return '*'+ Export.xls.converters.validateBarcode(value) +'*';
        },
        nr_element:function(value, diff) {
            return value.slice(value.indexOf('-')+1).split('/')[0];
        },
        order_number:function(value, diff) {
            return value.slice(0, value.indexOf('/'));
        },
        pr_clientBarcode:function(value, diff) {
            return '*'+ Export.xls.converters.validateBarcode(value) +'*';
        },
        products:function(xls){
            var products=[], 
                _convertFields=function(data, ecn){
                    for(var l in data){
                        data[l]=Export.convertField.call(xls, data[l], l );
                    }
                    return data;
                };
            for(var i in xls.data.orders){
                var nrElement=1,
                    order=xls.data.orders[i],
                    orderData=_convertFields(Bajt.entity.export(order, 'Orders', Export.fields.Productions.order));
                for(var j in order.positions){
                    var position=order.positions[j],
                        positionData=_convertFields(Bajt.entity.export(position, 'Positions', Export.fields.Productions.position));
                    for(var k in position.products){
                        var product=position.products[k],
                            productData=_convertFields(Bajt.entity.export(product, 'Products', Export.fields.Productions.product));
                        products.push($.extend(productData, positionData, orderData, _extraProduct, {kat_kroku: position.pos_length >  2790 || position.pos_width >  2790 ? 1 : 90 }));
                    }
                }
            }
            console.log(products);
            return products;
        }
    }
});

$.extend(Export.xls.generators, {
    Productions: {
        generate: function(xls) {
            var rowIndex=1,
                products=xls.converters.Productions.products(xls);
            this.xls = xls;
            xls.generators.title('Produkcja nr ' + Bajt.getFieldValue(xls.data, 'number', 'Productions') + ' z dnia '+ Bajt.getFieldValue(xls.data, 'generated', 'Productions'), {rowIndex: rowIndex++, cellIndex: 0});
            xls.blockData(Export._labelFields( Export.fields.Productions.products , 'Productions'), {rowIndex: rowIndex++, cellIndex: 0 });
            for(var i in products){
                xls.blockData(products[i], {rowIndex: rowIndex++, cellIndex: 0, fields: Export.fields.Productions.products });
            }
        }
    }
});


$.extend(Export.pdf.converters, {
    Productions: {
        
    }
});

$.extend(Export.pdf.generators, {
    Productions: {}
});

