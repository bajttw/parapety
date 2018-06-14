(function($, B) {
    'use strict';

    $.widget('bajt.modalTrims', $.bajt.modalField, {
        options: {
            ecn: 'Trims',
            separator: ', '
        },
        _customCreateBasicControls: function() {
            this.$checks = this.$fieldsContainer.find('label');
        },
        _customCreateBasicOptions: function() {
            var o = this.options;
            o.dictionary = B.dic.get('Trims', 'Orders');
        },
        _bind: function() {
            this._superApply(arguments);
            this._on(this.$checks, {
                click: this._choice
            });
        },
        _fromDictionary: function(value) {
            return B.dic.from(this.options.dictionary, value, {
                name: 's'
            });
        },
        _choice: function(e) {
            var that = this,
                o = this.options,
                $check = $(e.currentTarget.control),
                value = $check.val(),
                selected = this.$field.readField(),
                _unselectNotAllowed = function(allowed) {
                    if (!$.isArray(allowed)) {
                        allowed = [];
                    }
                    that.$field.filter(':checked').each(function() {
                        var $this = $(this);
                        if (allowed.indexOf(that._fromDictionary($this.val())) < 0) {
                            $this.prop('checked', false);
                        }
                    });
                };

            if (!$check.is(':checked') && selected.length > 0) {
                var choicedSymbol = this._fromDictionary(value),
                    combinations = this.getParameters('combinations')[choicedSymbol] || [],
                    combination = null;
                for (var i = 0; i < selected.length && !combination; i++) {
                    var selectedSymbol = this._fromDictionary(selected[i]);
                    for (var j = 0; j < combinations.length && !combination; j++) {
                        var allowed = $.isArray(combinations[j]) ? combinations[j] : combinations[j].split(' ');
                        if (allowed.indexOf(selectedSymbol) >= 0) {
                            combination = allowed;
                        }
                    }
                }
                _unselectNotAllowed(combination);
            }
            var hh = this.$fieldsContainer.find('input[type=checkbox]:checked');
        },
        read: function() {
            this.mdata.value = this.$field.readField(this.mdata.type);
        },
        // _encode: function () {
        //     return $.isArray(this.mdata.value) ? this.mdata.value.join(this.options.separator || ', ') : '';
        // },
        write: function() {
            this.$field.writeField(this.mdata.value);
            this.ownChange = true;
            this.$field.change();
        }
    });

    $.widget(
        'bajt.positionPositions',
        $.extend(true, {}, B.formPosition, {
            options: {
                dimensions: 'cm',
                ecn: 'Positions',
                summaryField: 'area',
                locale: {
                    validate: {
                        position: 'Pozycja zamówienia'
                    }
                },
                formFields: [
                    {
                        name: 'nr',
                        options: {
                            disp: {
                                type: 'v',
                                tmpl: '<span>__v__</span>'
                            }
                        }
                    },
                    {
                        name: 'lengthcm',
                        options: {
                            precision: 1,
                            round: 0.5,
                            autocorrect: true,
                            type: 'float',
                            check_key: 1,
                            calc: 1,
                            navi: 1,
                            selectOnFocus: true
                        }
                    },
                    {
                        name: 'widthcm',
                        options: {
                            precision: 1,
                            round: 0.5,
                            autocorrect: true,
                            type: 'float',
                            check_key: 1,
                            calc: 1,
                            navi: 2,
                            selectOnFocus: true
                        }
                    },
                    {
                        name: 'quantity',
                        options: {
                            check_key: 1,
                            calc: 1,
                            navi: 3,
                            selectOnFocus: true
                        }
                    },
                    {
                        name: 'area',
                        options: {
                            disp: {
                                type: 1,
                                def: '-'
                            },
                            precision: 3
                        }
                    },
                    {
                        name: 'prodComment',
                        options: {
                            control: {
                                type: 'modal',
                                modal: 'comment',
                                text: 'P',
                                signal: 1,
                                navi: 1
                            }
                        }
                    },
                    {
                        name: 'positionComment',
                        options: {
                            control: {
                                type: 'modal',
                                modal: 'comment',
                                text: 'K',
                                signal: 1,
                                navi: 1
                            }
                        }
                    },
                    {
                        name: 'clientComment',
                        options: {
                            control: {
                                type: 'modal',
                                modal: 'comment',
                                text: 'W',
                                signal: 1,
                                navi: 1
                            }
                        }
                    },
                    {
                        name: 'model',
                        options: {
                            control: {
                                type: 'modal',
                                modal: 1,
                                text: 'M',
                                signal: 2,
                                navi: '1'
                            },
                            disp: {
                                type: 'dic.s',
                                def: 'M',
                                prefix: 'btn'
                            }
                        }
                    },
                    {
                        name: 'color',
                        options: {
                            control: {
                                type: 'modal',
                                modal: 1,
                                text: 'K',
                                signal: 2,
                                navi: '1'
                            },
                            disp: {
                                type: 'dic.s',
                                def: 'K',
                                prefix: 'btn'
                            }
                        }
                    },
                    {
                        name: 'trims',
                        options: {
                            control: {
                                type: 'modal',
                                modal: 1,
                                modalWidget: 'modalTrims',
                                text: 'O',
                                signal: 2,
                                navi: '1'
                            },
                            disp: {
                                type: 'dic.s',
                                def: 'O',
                                prefix: 'btn'
                                // prototype: false
                            },
                            dictionary: 'Trims'
                        }
                    },
                    {
                        name: 'size',
                        options: {
                            control: {
                                type: 'modal',
                                modal: 1,
                                text: 'G',
                                signal: 2,
                                navi: 1
                            },
                            disp: {
                                type: 'dic.s',
                                def: 'G',
                                prefix: 'btn'
                            }
                        }
                    },
                    {
                        name: 'uploads',
                        options: {
                            control: {
                                signal: 2,
                                navi: '1'
                            }
                        }
                    }
                ],
                customActions: ['details']
            },
            _customCreateBasicOptions: function() {
                var o = this.options;
                o.fieldOptions = {
                    model: this._paramOptions,
                    size: this._paramOptions,
                    trims: this._paramOptions,
                    color: this._paramOptions
                };
            },
            // _customCreateData: function() {
            //     this.sum = {
            //         _area: 0,
            //         area: 0,
            //         quantity: 0
            //     };
            // },
            _paramOptions: function(fieldName, options) {
                return {
                    fieldDefault: this.options.parent.field(fieldName),
                    dictionary: this.getDictionary(fieldName),
                    disp: {
                        tmpl: this.options.templates[fieldName]
                    }
                };
            },
            calc: function() {
                var o = this.options,
                    fl = this._fieldsByName['length' + o.dimensionsUnit],
                    fw = this._fieldsByName['width' + o.dimensionsUnit],
                    dividend = o.dimensionsUnit === 'cm' ? 10000 : 1000000,
                    fq = this._fieldsByName.quantity;
                this.sum = {
                    quantity: fq.value(),
                    _area: 0,
                    area: 0
                };
                if (fl.status() >= 0 && fw.status() >= 0 && fq.status() >= 0) {
                    this.sum._area = fl.value() * fw.value() * this.sum.quantity;
                    this.sum.area = this.sum._area / dividend;
                }
                this._sumVal(this.sum.area);
                return this.getSummary();
            },
            getDictionary: function(name) {
                return this.options.parent.getDictionary(name);
            }
        })
    );

    $.widget(
        'bajt.order',
        $.extend(true, {}, B.basicForm, B.extFormPositions, {
            options: {
                dimensionsUnit: 'cm',
                expBtns: true,
                formFields: [
                    'client',
                    {
                        name: 'number',
                        options: {
                            disp: {
                                type: 1,
                                def: '-auto-'
                            }
                        }
                    },
                    'clientNumber',
                    {
                        name: 'created',
                        options: {
                            type: 'date',
                            format: 'YYYY-MM-DD HH:mm',
                            disp: {
                                type: 1,
                                def: '-auto-'
                            }
                        }
                    },
                    {
                        name: 'approved',
                        options: {
                            type: 'date',
                            format: 'YYYY-MM-DD HH:mm',
                            disp: {
                                type: 1,
                                def: '-auto-'
                            }
                        }
                    },
                    {
                        name: 'term',
                        options: {
                            type: 'date',
                            format: 'YYYY-MM-DD',
                            widget: {
                                type: 'datepicker',
                                options: {
                                    locale: {
                                        format: 'YYYY-MM-DD'
                                    }
                                }
                            }
                        }
                    },
                    {
                        name: 'express',
                        options: {
                            widget: {
                                type: 'combobox'
                            },
                            dictionary: true
                        }
                    },
                    {
                        name: 'quantity',
                        options: {
                            disp: {
                                type: 1,
                                def: '-'
                            }
                        }
                    },
                    {
                        name: 'area',
                        options: {
                            disp: {
                                type: 1,
                                def: '-'
                            },
                            precision: 3
                        }
                    },
                    {
                        name: 'prodComment',
                        options: {
                            control: {
                                type: 'modal',
                                modal: 'comment',
                                text: 'P',
                                signal: 1
                            }
                        }
                    },
                    {
                        name: 'orderComment',
                        options: {
                            control: {
                                type: 'modal',
                                modal: 'comment',
                                text: 'K',
                                signal: 1
                            }
                        }
                    },
                    {
                        name: 'clientComment',
                        options: {
                            control: {
                                type: 'modal',
                                modal: 'comment',
                                text: 'W',
                                signal: 1
                            }
                        }
                    },
                    {
                        name: 'model',
                        options: {
                            widget: {
                                type: 'combobox'
                            },
                            setField: 1,
                            control: {
                                type: 'modal',
                                modal: 1,
                                icon: 'library_books'
                            },
                            dictionary: true
                        }
                    },
                    {
                        name: 'color',
                        options: {
                            widget: {
                                type: 'combobox'
                            },
                            setField: 1,
                            control: {
                                type: 'modal',
                                modal: 1,
                                icon: 'library_books'
                            },
                            dictionary: true
                        }
                    },
                    {
                        name: 'size',
                        options: {
                            widget: {
                                type: 'combobox'
                            },
                            setField: 1,
                            control: {
                                type: 'modal',
                                modal: 1,
                                icon: 'library_books'
                            },
                            dictionary: true
                        }
                    },
                    {
                        name: 'trims',
                        options: {
                            setField: 1,
                            control: {
                                type: 'modal',
                                modal: 1,
                                modalWidget: 'modalTrims',
                                icon: 'library_books'
                            },
                            disp: {
                                type: 'dic.s',
                                def: '-'
                                // prototype: false
                            },
                            dictionary: 'Trims'
                        }
                    },
                    {
                        name: 'status',
                        options: {
                            disp: {
                                def: '-',
                                type: 'dic'
                            },
                            dictionary: true
                        }
                    }
                ],
                positionsOptions: {
                    autoNew: true,
                    calc: true,
                    ecn: 'Positions',
                    focusField: 'lengthcm',
                    actions: ['add', 'import']
                },
                copyTextarea: '#orders_exp_copy',
                summaryField: 'area',
                statusButtons: {
                    prev: {
                        2: {
                            label: 'Edycja',
                            title: 'Edycja',
                            addClass: '',
                            icon: 'reply'
                        },
                        3: {
                            label: 'Do klienta',
                            title: 'Do klienta',
                            addClass: '',
                            icon: 'reply'
                        }
                    },
                    next: {
                        1: {
                            label: 'Zatwierdź',
                            title: 'Zatwierdź',
                            addClass: '',
                            icon: 'send'
                        },
                        2: {
                            label: 'Obsługa',
                            title: 'Obsługa',
                            addClass: '',
                            icon: 'edit'
                        }
                    }
                }
            },
            _blockPartial: function() {
                if (this._status === 2 || this._status > 3) {
                    var noBlock = {
                            2: ['status', 'approved'],
                            3: ['status']
                        },
                        nb = noBlock.hasOwnProperty(this._status) ? noBlock[this._status] : [];
                    this._blockFields(true, B.obj.arrayDiff(Object.keys(this._fieldsByName), nb));
                    this._blockFields(false,  nb);
                    this._blockPositions(true);
                    $(B.html.validateSelector(this.options.formName + '_client')).prop('disabled', true);
                }
            },
            _checkStatus: function() {
                // if(that._fieldsByName['status'].value() == 2 && posit.servicesCount == 0)
                // that._fieldsByName['status'].value(1);
            },
            _customAllowedOperation: function(operation, data) {
                var allow = true;
                switch (operation) {
                    case 'prevStatus':
                        allow = this._status === 2 || this._status === 3;
                        break;
                    case 'nextStatus':
                        allow =
                            !this.field('created').isEmpty() &&
                            this._status < 3 &&
                            (this._status === 2 || this.field('approved').isEmpty());
                        break;
                }
                return allow;
            },
            _customBlock: function(block) {
                $(B.html.validateSelector(this.options.formName + '_client')).prop('disabled', block);
            },
            _customChange: function(data) {
                if (B.obj.is(data.field)) {
                    var f = data.field,
                        name = f.option('name');
                    switch (name) {
                        case 'express':
                            var defTerm = { 1: 21, 2: 14, 3: 7 },
                                expr = f.value(),
                                days =
                                    B.dic.from(this.getDictionary('terms'), expr, { name: 'n' }) || defTerm[expr] || 14,
                                term = this._fieldsByName.created.isEmpty()
                                    ? moment()
                                    : moment(this._fieldsByName.created.value());
                            if (!isSet(days)) {
                                days = expr > 1 ? expr * 7 : 7;
                            }
                            term.add(days, 'days');
                            if (this._fieldsByName.term.formWidget) {
                                this._fieldsByName.term.formWidget.setStartDate(term);
                            } else {
                                this._fieldsByName.term.value(term);
                            }
                            break;
                    }
                }
            },
            _customCreateBasicControls: function() {
                this.$importModal= $('#import_modal');
            },
            _customState: function(state, data) {
                switch (state) {
                    case 'start':
                    case 'normal':
                    case 'submitSuccess':
                        this._blockPartial();
                        break;
                }
                return this._state;
            },
            _posActionImport:function(e){
                stopTrigger(e);
                this.$importModal.modalImport('show', {
                    form: this,
                    fnImport: this.importPositions
                });
            },
            _posImportData: function(inData) {
                var outData = {},
                    fields = [null, 'lengthcm', 'widthcm', 'quantity'];
                for (var i in inData) {
                    if (fields[i]) {
                        outData[fields[i]] = inData[i];
                    }
                }
                return outData;
            },
            _statusActionNext: function(e) {
                this._statusSet(this._status+1);
            },
            _statusActionPrev: function(e) {
                this._statusSet(this._status - 1);
            },
            summary: function(options) {
                this._posSummary(null);
                this._sumVal();
                return this.getSummary();
            }
        })
    );

    $.fn.initFormOrder = function() {
        var $form = $(this).find('form[data-form=orders]');
        if (B.obj.is$($form)) {
            $form.order();
            $('#import_modal').modalImport();
            $('.modal-field').modalField();
            $('#trims_modal').modalTrims();
        }
    };
})(jQuery, Bajt);

            // _bind: function() {
            //     this._bindForm();
            //     this._bindButtons(this.$statusBtns, '_statusAction');
            //     // var that = this,
            //     //     o = this.options;
            //     // this._bindForm();
            //     // this.$addPositionBtns.each(function() {
            //     //     var $this = $(this);
            //     //     that._on($this, {
            //     //         click: function(e) {
            //     //             that._posNew($this.data('add-position'));
            //     //         }
            //     //     });
            //     // });
            //     // this._on(this.import.$showBtn, {
            //     //     click: function() {
            //     //         that.import.$modal['modalImport']('show', { form: that, fnImport: that.importPositions } );
            //     //     }
            //     // });
            //     // this._on(this.$statusBtns.prev, {
            //     //     click: this._prevStatus
            //     // });
            //     // this._on(this.$statusBtns.next, {
            //     //     click: this._nextStatus
            //     // });
            //     return this;
            // },


            // summary: function(fn) {
            //     var o = this.options,
            //         dividend = o.dimensionsUnit === 'cm' ? 10000 : 1000000,
            //         sum = {
            //             quantity: 0,
            //             _area: 0,
            //             area: 0
            //         };
            //     fn = fn ? fn : 'getSummary';
            //     for (var p in this._positions) {
            //         var positions = this._positions[p].rows;
            //         for (var i in positions) {
            //             B.obj.summary(sum, positions[i][fn]());
            //         }
            //     }
            //     this._fieldsByName.area.value(sum._area / dividend);
            //     this._fieldsByName.quantity.value(sum.quantity);
            //     return sum;
            // }

// _create: function() {
//     var that = this,
//         o = this.options;
//     this.state('init');
//     this._createBasicControls();
//     this._createBasicOptions();
//     this._createData();
//     this._createFields();
//     this._initPositions();
//     this.element.initExpBtns({
//         entitySettings: o.entitySettings
//     });
//     this.calc();
//     this._bind();
//     this.state('start');
// },

// this._on(this.element, {
//     navigate: this._navigate,
//     positionRemove: this._posRemove,
//     changed: this._change,
//     submit: this._submit
// });

// this._on(this.$impPositionsBtn, {
//     click: function () {
//         $('#import_modal').modalImport("show", that);
//     }
// });

// _posAdd: function(postionsECN, $position) {
//     var o = this.options,
//         positions = this._positions[postionsECN],
//         position = $position['position' + postionsECN](
//             $.extend(
//                 {
//                     parent: this,
//                     formName: o.formName,
//                     dimensionsUnit: o.dimensionsUnit,
//                     index: positions.index,
//                     nr: positions.count + 1
//                 },
//                 o.positionsOptions[postionsECN]
//             )
//         ).data('bajtPosition' + postionsECN);
//     positions.rows.push(position);
//     positions.index++;
//     positions.count++;
//     // this._checkStatus();
//     // if(this._fieldsByName['status'].value() < 2 && this.servicesCount)
//     //     this._fieldsByName['status'].value(2);
//     return position;
// },

// _blockPositions: function(block) {
//     for (var p in this._positions) {
//         var positions = this._positions[p].rows;
//         for (var i in positions) {
//             positions[i].block(block);
//         }
//     }
// },

// _posAdd: function ($position) {
//     var
//         o = this.options,
//         position = $position.orderPosition($.extend(true, {
//             order: this,
//             dimensionsUnit: o.dimensionsUnit,
//             index: this.posIndex,
//             formName: o.formName,
//             nr: this.posCount + 1
//         }, o.positionsOptions)).data('bajtOrderPosition');
//     this._positions.push(position);
//     this.posIndex++;
//     this.posCount++;
//     return position;
// },

// this._positions = {};
// for (var k in this.options.positionsOptions) {
//     this._positions[k] = {
//         tab: [],
//         count: 0,
//         index: 0
//     };
// }

// this.$closeBtn = this.element.find('.form-footer [data-close]')
// this.$positions = this.element.find('[data-form=positions]');

// _initPositions: function() {
//     var p,
//         that = this,
//         o = this.options,
//         _addPos=function(idx, element){
//             that._posAdd(p, $(element));
//         };
//     this.$positions = {};
//     for (p in o.positionsOptions) {
//         var $positions = this.element.find(o.fieldSelectorPrefix + B.str.firstLower(p));
//         $.extend(true, o.positionsOptions[p], $positions.data());
//         $positions.find('.row-pos').each(_addPos);
//         this.$positions[p] = $positions;
//     }
//     this.calc();
// },
// _navigate: function(e, data) {
//     var i,
//         step = data.step === 3 ? 1 : data.step,
//         fieldName = data.field.option('name');
//     stopTrigger(e);
//     if (data.position) {
//         var pos_ecn = data.position.option('ecn'),
//             positions = this._positions[pos_ecn];
//         i = positions.rows.indexOf(data.position) + step;
//         if (data.step === 3) {
//             fieldName = this.options.positionsOptions.focusField || 0;
//         }
//         while (i >= 0 && i <= positions.index && positions.rows[i] === undefined) {
//             i += step;
//         }
//         if (positions.rows[i]) {
//             positions.rows[i].focus(fieldName, data);
//         } else if (i > 0) {
//             this._posNew(pos_ecn, fieldName, {}, data);
//         }
//     } else {
//         var fields = this._fields,
//             fc = fields.length;
//         i = fields.indexOf(data.field) + step;
//         while (i >= 0 && i < fc && !fields[i].option('navi')) {
//             i += step;
//         }
//         if (fields[i]) {
//             fields[i].focus(data);
//         }
//     }
// },
// _navigateCtrl: function(e, data) {
//     if (data.position) {
//         stopTrigger(e);
//         var pTarget = data.position.option('nr') - 1 + data.step,
//             positions = this._positions[data.position.option('ecn')];
//         if (0 <= pTarget && pTarget < positions.count) {
//             data.target = data.index;
//             positions.element.find('.row-data').naviElement(null, data);
//         }
//     }
// },
// _posNew: function(positionsECN, focusField, values, eventData) {
//     if (!this.allowedOperation('new' + positionsECN)) {
//         return;
//     }
//     var that = this,
//         o = this.options,
//         $positions = this.$positions[positionsECN],
//         $newPosition = $(
//             o.positionsOptions[positionsECN].prototype.replace(
//                 /__pn__/g,
//                 this._positions[positionsECN].index
//             )
//         ),
//         position = this._posAdd(positionsECN, $newPosition);
//     $newPosition.appendTo($positions).slideDown('fast', function() {
//         $newPosition.initFormWidgets();
//         that.state('changing');
//         position.setValues(values);
//         that.state('normal');
//         position.focus(focusField, eventData);
//         if (typeof that.importQueue === 'number') {
//             if (that.importQueue > 1) {
//                 that.importQueue--;
//             } else {
//                 delete that.importQueue;
//                 that.calc();
//             }
//         }
//     });
//     return $newPosition;
// },

// var that = this,
//     // $md=$('#date_modal'),
//     // fn={
//     //     2: 'approve',
//     //     3: 'service'
//     // },
//     status = fieldStatus.value();
// // field=this._fieldsByName[fn[status]];
// // field.value(moment());
// // $md['modalField']('show', field, this.$statusBtns.next).on('hide.bs.modal', function(){
// //     if(!field.isEmpty())
// //         fieldStatus.value(status+1);
// //     // fieldStatus.value(status);
// // });
// fieldStatus.value(status + 1);

//     fn = {
//         3: 'service'
//     };
// // this.field(fn[status]).value(null);
// this.field('status').value(status - 1);

// _posRemove: function(e, data) {
//     var that = this,
//         $position = data.position.element,
//         positions = this._positions[data.position.option('ecn')];
//     $position.slideToggle('fast', function() {
//         positions.rows.splice(positions.rows.indexOf(data.position), 1);
//         positions.count--;
//         that.calc();
//         that._checkStatus();
//         $position.remove();
//     });
// },

// importPositions: function(positionsECN, positions) {
//     if ($.isArray(positions) && this.allowedOperation('import' + positionsECN)) {
//         var po=this.options.positionsOptions[positionsECN];
//         this.importQueue = positions.length;
//         for(var i=0, ien=positions.length; i<ien; i++ ){

//             this._posNew(
//                 positionsECN,
//                 po.focusField,
//                 this._importConvert(positionsECN, positions[i])
//             );
//         }

//         // var that = this;
//         // if (positions.length > 0) {
//         //     this.importQueue = positions.length;
//         //     $.each(positions, function(idx, position) {
//         //         that._posNew(
//         //             positionsECN,
//         //             that.options.positionsOptions[positionsECN].focusField,
//         //             position
//         //         );
//         //     });
//         // }
//     }
// },
