(function($, B) {
    'use strict';

    var orderwork={
            options: {
                formFields: [
                    'client',
                    {
                        name: 'number',
                        options: {
                            disp: {
                                type: 1,
                                default: '-auto-'
                            }
                        }
                    },
                    {
                        name: 'generated',
                        options: {
                            type: 'date',
                            format: 'YYYY-MM-DD HH:mm',
                            disp: {
                                type: 1,
                                default: '-auto-'
                            }
                        }
                    },
                    {
                        name: 'progress',
                        options: {}
                    }
                ],
                ordersImportFields:['id', 'number', 'created', 'approved', 'area', 'client.name', 'client.code'],
                productsTableFilter: '',
                labels: {
                    form: 'Produkcja'
                }
            },
            _create: function() {
                var that = this,
                    o = this.options;
                this.state('init');
                this._createBasicControls();
                this._createBasicOptions();
                this._createData();
                this._createFields();
                this._initOrders();
                this.element.initExpBtns({
                    entitySettings: o.entitySettings
                });
                
                // this.calc();
                this._bind();
                this._blockPartial();
                this.state('start');
            },
            _bind: function() {
                var that = this,
                    o = this.options;
                this._on(this.element, {
                    changed: this._change,
                    submit: this._submit
                });
                this._on(this.$orders, {
                    change: this._change
                    
                });
                if (B.obj.is$(this.$close)) {
                    this._on(this.$close, {
                        click: this.close
                    });
                }
            },
            _blockPartial: function() {
                // this.orders.block(true);
            },
            _customCreateBasicControls: function() {
                this.productsTable = $('#products_table').DataTable();
            },
            _customCreateBasicOptions: function() {
                var o = this.options;
                o.fieldOptions = {};
            },
            _customCreateData: function() {
                this._status = 0;
                this.orders = {
                    tab: [],
                    count: 0,
                    index: 0
                };
            },
            _customState: function(state, data) {
                var b = 1;
                switch (state) {
                    case 'normal':
                        this._blockPartial();
                        break;
                    case 'submitSuccess':
                        if(this.options.productsTableFilter){
                        DT.setFilter(this.productsTable, this.options.productsTableFilter, this.options.entityId );}
                        else{                        this.productsTable.ajax.reload();}
                        this._blockPartial();
                        break;
                }
                return this._state;
            },
            _initOrders: function() {
                this.$orders = this._findFieldElement('orders');
                this.orders = this.$orders.initFormWidget({
                    widget: 'dtImport',
                    ecn: 'Orders',
                    addBtn: '#add_orders_btn',
                    modal: '#orders_import_modal',
                    modalBtn: '#orders_import_btn',
                    table: '#orders_table',
                    unique: {
                        on: true,
                        showFilter: true,
                        filter: true
                    },
                    importFields: this.options.ordersImportFields 
                });
                return this.orders;
            },
            calc: function() {
                return this.summary('calc');
            },
            summary: function(fn) {
                var o = this.options,
                    dividend = o.dimensionsUnit === 'cm' ? 10000 : 1000000,
                    sum = {
                        quantity: 0,
                        _area: 0,
                        area: 0
                    };
                fn = fn ? fn : 'getSummary';
                for (var p in this._positions) {
                    var positions = this._positions[p].tab;
                    for (var i in positions) {
                        B.obj.summary(sum, positions[i][fn]());
                    }
                }
                this._fieldsByName.area.value(sum._area / dividend);
                this._fieldsByName.quantity.value(sum.quantity);
                return sum;
            }
        };

   
    $.widget(
        'bajt.formProduction',
        $.extend(true, {}, B.basicForm, orderwork, {
            options:{
                productsTableFilter: 'hiddenProduction'
            }
        })
    );
    
    $.widget(
        'bajt.formDelivery',
        $.extend(true, {}, B.basicForm, orderwork, {
            options:{
                productsTableFilter: 'hiddenDelivery'

            }
        })
    );

    $.fn.initFormProduction = function() {
        var $form = $(this).find('form[data-form=productions]');
        if (B.obj.is$($form)) {
            $form.formProduction();
        }
    };

    $.fn.initFormDelivery = function() {
        var $form = $(this).find('form[data-form=deliveries]');
        if (B.obj.is$($form)) {
            $form.formDelivery();
        }
    };
})(jQuery, Bajt);
