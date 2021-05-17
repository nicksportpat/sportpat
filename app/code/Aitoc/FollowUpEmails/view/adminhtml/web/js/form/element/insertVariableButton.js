/*
 * Copyright © 2019 Aitoc. All rights reserved.
 */
define([
    'Magento_Ui/js/form/components/button',
    'uiRegistry',
    'underscore',
    "jquery",
    'Magento_Variable/variables'
], function (Input, uiRegistry, _, $, variable) {
    'use strict';

    return Input.extend({
        initialize: function () {
            this._super();
        },

        openPopUp : function () {
            Variables.resetData();
            var textArea = $("textarea[name='template_content']");
            Variables.init(textArea[0].id, null);
            var variables =  $("input[name='variables']");
            variables = variables[0].value.evalJSON();
            var templateVariablesValue = $('template_variables').value;

            if (templateVariablesValue) {
                if (templateVariables = templateVariablesValue.evalJSON()) {
                    this.variables.push(templateVariables);
                }
            }

            var result = null;

            if (variables) {
                result = '<ul class="insert-variable">';
                variables.each(function (variableGroup) {
                    if (variableGroup.label && variableGroup.value) {
                        result += '<li><b>' + variableGroup.label + '</b></li>';
                        variableGroup.value.each(function (variable) {
                            if (variable.value && variable.label) {
                                result += '<li>' +
                                    Variables.prepareVariableRow(variable.value, variable.label) + '</li>';
                            }
                        }.bind(this));
                    }
                }.bind(this));
                result += '</ul>';
            }

            Variables.openVariableChooser(result);
        }
    });
});
