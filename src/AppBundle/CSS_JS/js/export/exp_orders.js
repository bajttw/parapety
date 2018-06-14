$.extend(true, Export.labels, {
    Orders: {
        created: 'Utworzone'
    }
});

$.extend(true, Export.fields, {
    Orders: {
        basic: ['number', 'created'],
        basicReport: ['number', 'created']
    }
});
$.extend(Export.xls.generators, {
    Orders: {
        basic: function() {
            var xls = this.xls;
            var fields = Export.fields.Orders.basicCSV,
                labels = Export._labelFields(fields, 'Orders');
            var cellIndex = 0,
                rowIndex = xls.current.row.idx;
            for (var i = 0, ien = fields.length; i < ien; i++) {
                xls._cell(labels[i], {
                    rowIndex: rowIndex,
                    cellIndex: i
                });
                xls._cell(Bajt.getFieldValue(xls.data, fields[i], 'Orders'), {
                    rowIndex: rowIndex + 1,
                    cellIndex: i
                });
            }
            xls.current.row.idx = rowIndex + 2;
        },
        parameters: function() {
            var xls = this.xls;
            var fields = Export.fields.Orders.param,
                labels = Export._labelFields(fields, 'Orders');
            var rowIndex = xls.current.row.idx;
            for (var i = 0, ien = fields.length; i < ien; i++) {
                xls._cell(labels[i], {
                    rowIndex: rowIndex,
                    cellIndex: i
                });
                xls._cell(Export.convertField.call(xls, Bajt.getFieldValue(xls.data, fields[i], 'Orders'), fields[i]), {
                    rowIndex: rowIndex + 1,
                    cellIndex: i
                });
            }
            xls.current.row.idx = rowIndex + 2;
        },
        positions: function() {
            var i,
                ien,
                xls = this.xls,
                fields = Export.fields.Positions.all,
                labels = Export._labelFields(fields, 'Positions'),
                rowIndex = xls.current.row.idx;
            for (i = 0, ien = fields.length; i < ien; i++) {
                xls._cell(labels[i], {
                    rowIndex: rowIndex,
                    cellIndex: i
                });
            }
            rowIndex++;
            for (i = 0, ien = xls.data.positions.length; i < ien; i++) {
                var pos = xls.data.positions[i],
                    cellIndex = 0;
                for (var j = 0, jen = fields.length; j < jen; j++) {
                    xls._cell(Export.convertField.call(xls, pos[fields[j]], fields[j]), {
                        rowIndex: rowIndex,
                        cellIndex: cellIndex
                    });
                    cellIndex++;
                }
                rowIndex++;
            }
            xls.current.row.idx = rowIndex;
        },
        generate: function(xls) {
            this.xls = xls;
            xls.generators.title(xls);
            this.basic();
            this.parameters();
            this.positions();
        }
    }
});

$.extend(Export.pdf.converters, {
    Orders: {}
});

$.extend(Export.pdf.generators, {
    ServiceOrders: {
        generators: {
            basicData: function(type) {
                var pdf = this.pdf,
                    ecn = this.options.ecn,
                    _convert = function(name) {
                        return Export.convertField.call(pdf, pdf.data[name], name);
                    },
                    fields = Export.fields.ServiceOrders[type || 'basic'],
                    // console.log(pdf._styleText('pdf.data.accessory', 'justify'));
                    rows = [
                        pdf._rowStyle(Export._labelFields(fields, ecn), 'tableHeader'),
                        Export.convertFields(pdf.data, fields, Export.convertField, {
                            caller: pdf.converters.ServiceOrders
                        }),
                        pdf._fillEmptyCells(
                            [
                                {
                                    style: 'tableHeader',
                                    colSpan: fields.length,
                                    text: Export._label('description', ecn)
                                }
                            ],
                            fields.length - 1
                        ),
                        pdf._fillEmptyCells(
                            [
                                {
                                    colSpan: fields.length,
                                    text: pdf.data.description,
                                    style: 'justify'
                                }
                            ],
                            fields.length - 1
                        )
                    ];
                if (pdf.data.accessory) {
                    rows.push(
                        pdf._fillEmptyCells(
                            [
                                {
                                    style: 'tableHeader',
                                    colSpan: fields.length,
                                    text: Export._label('accessory', ecn)
                                }
                            ],
                            fields.length - 1
                        )
                    );
                    rows.push(
                        pdf._fillEmptyCells(
                            [
                                {
                                    colSpan: fields.length,
                                    text: pdf.data.accessory,
                                    style: 'justify'
                                }
                            ],
                            fields.length - 1
                        )
                    );
                }
                return pdf._table(rows, {
                    options: {
                        margin: [0, 5, 0, 0],
                        fontSize: 8,
                        layout: 'noBorders',
                        table: {
                            widths: (function() {
                                var w = [];
                                for (var i = 0; i < fields.length; i++) {
                                    w.push('*');
                                }
                                return w;
                            })()
                        }
                    }
                });
            }
        },
        agreement: {
            pdf: null,
            header: function() {
                var pdf = this.pdf;
                return pdf.generators.header.call(this, 'Umowa serwisowa NR ' + pdf.data.number);
            },
            doc: {
                pageOrientation: 'landscape',
                styles: {
                    defaultStyle: {
                        fontSize: 8
                    },
                    title: {
                        fontSize: 10
                    }
                }
            },
            options: {},
            _options: function(options) {
                this.options = $.extend(
                    true,
                    {
                        ecn: 'ServiceOrders',
                        title: 'POTWIERDZENIE PRZYJĘCIA SERWISOWEGO'
                    },
                    Bajt.obj.is(options) ? options : {}
                );
            },
            content: function() {
                return {
                    margin: [20, 5],
                    stack: [
                        this.header(),
                        this.pdf.generators.membersTable.call(this, 'ServiceOrders'),
                        this.pdf.generators.ServiceOrders.generators.basicData.call(this),
                        {
                            style: 'justify',
                            fontSize: 8,
                            stack: this.options.content
                        },
                        {
                            fontSize: 8,
                            margin: [20, 10],
                            stack: this.options.personal
                        },
                        this.pdf.generators.signs.call(this, true, true)
                    ]
                };
            },
            generate: function(pdf, options) {
                this.pdf = pdf;
                this._options(options);
                var content = this.content();
                return pdf._table([[content, $.extend(true, {}, content)]], {
                    options: {
                        table: {
                            widths: ['*', '*']
                        },
                        layout: 'noBorders'
                    }
                });
            }
        },
        report: {
            pdf: null,
            header: function() {
                var pdf = this.pdf;
                return pdf.generators.header.call(this, 'Zlecenie serwisowe NR ' + pdf.data.number);
            },
            doc: {
                // pageOrientation: "landscape",
                styles: {
                    defaultStyle: {
                        fontSize: 8
                    },
                    title: {
                        fontSize: 10
                    }
                }
            },
            options: {},
            _options: function(options) {
                this.options = $.extend(
                    true,
                    {
                        ecn: 'ServiceOrders',
                        title: 'RAPORT SERWISOWY'
                    },
                    Bajt.obj.is(options) ? options : {}
                );
            },
            services: function() {
                var pdf = this.pdf,
                    content = [{ text: 'Usługi:', style: 'h' }];
                content.push(pdf.generators.Services.generators.table.call(this));
                return { style: 'justify', fontSize: 8, stack: content };
            },
            materials: function() {
                var pdf = this.pdf,
                    materials = pdf.data.materials;
                if ($.isArray(materials) && materials.length > 0) {
                    var content = [{ text: 'Materiały:', style: 'h' }];
                    content.push(pdf.generators.Materials.generators.table.call(this));
                    return {
                        style: 'justify',
                        fontSize: 8,
                        stack: content
                    };
                }
                return '';
            },
            externalServices: function() {
                var pdf = this.pdf,
                    externalServices = pdf.data.externalServices;
                // if(isArray(externalServices) && externalServices.length >0){
                // 	var content=[{"text":"Usługi zewnętrznych serwisów:","style":"h"}];
                // 	content.push(pdf.generators.Materials.generators.table.call(this));
                // 	return { style: 'justify', fontSize: 8, stack: content };
                // }
                return '';
            },
            summary: function() {
                var pdf = this.pdf;
                return '';
            },
            content: function() {
                return {
                    margin: [20, 5],
                    stack: [
                        this.header(),
                        this.pdf.generators.membersTable.call(this, 'ServiceOrders'),
                        this.pdf.generators.ServiceOrders.generators.basicData.call(this, 'basicReport'),
                        this.services(),
                        this.materials(),
                        this.externalServices(),
                        this.summary(),
                        {
                            style: 'comment',
                            fontSize: 8,
                            stack: this.options.comments
                        },
                        this.pdf.generators.signs.call(this, true)
                    ]
                };
            },
            generate: function(pdf, options) {
                this.pdf = pdf;
                console.log(pdf.data);
                this._options(options);
                var content = this.content();
                return content;
            }
        }
    }
});
