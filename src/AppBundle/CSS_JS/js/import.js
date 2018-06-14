(function($) {
    'use strict';

    $.widget(
        'bajt.importForm',
        $.extend(true, {}, Bajt.basicForm, {
            options: {},
            _create: function() {
                var that = this,
                    o = this.options;
                this.state('init');
                this._createBasicOptions();
                this._createData();
                console.log('this.options');
                console.log(this.options);
                this._createBasicControls();
                this._createFields();
                this._bind();
                this.state('normal');
            },
            _bind: function() {
                console.log('importForm - _bind');
                var that = this;
                this._on(this.element, {
                    submit: this._submit,
                    changed: this._change
                });
            }
        })
    );

    $.fn.initImportForm = function() {
        var $form = $(this).find('form[data-form=import]');
        if (Bajt.obj.is$($form)) {
            $form.importForm();
        }
    };
})(jQuery);
