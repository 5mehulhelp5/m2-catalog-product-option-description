/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery',
    'domReady',
    'dropdownDialog'
], function ($, domReady) {
    'use strict';

    var globalOptions = {
        descriptions: {}
    };

    $.widget('mage.productOptionsDescription', {
        options: globalOptions,

        _create: function createProductOptionsDescription() {
        },

        _init: function initProductOptionsDescription() {
            var self = this;

            domReady(function() {
                if (self.options.descriptions.description) {
                    var description = $('<div>', {class: 'product-options-description'});
                    description.html(self.options.descriptions.description);

                    $('#product-options-wrapper').prepend(description);
                }

                if (self.options.descriptions.options) {
                    $.each(self.options.descriptions.options, function(optionId, optionData) {
                        if (optionData.description) {
                            self.updateOptionDescription(optionId, optionData.description);
                        }
                    });
                }

                $('#product_addtocart_form .product-custom-option').on('change', function(event) {
                    var option = $(event.target);
                    var optionWrapper = option.closest('div[data-option-id]');

                    var optionId = optionWrapper.data('option-id');

                    if (optionId && self.options.descriptions.options && self.options.descriptions.options[optionId]) {
                        var optionData = self.options.descriptions.options[optionId];

                        if (optionData && optionData.values) {
                            // @todo: support multiselect and multiple checked checkboxes
                            var nodeName = option.prop('nodeName');
                            var optionValue = null;
                            if (nodeName === 'SELECT') {
                                optionValue = option.val();
                            } else if (nodeName === 'INPUT') {
                                var type = option.attr('type');
                                if (type === 'radio') {
                                    if (option.is(':checked')) {
                                        optionValue = option.val();
                                    }
                                } else if (type === 'checkbox') {
                                    if (option.is(':checked')) {
                                        optionValue = option.val();
                                    }
                                }
                            }

                            if (optionValue && optionData.values[optionValue]) {
                                var optionValueData = optionData.values[optionValue];

                                if (optionValueData.description) {
                                    self.updateOptionDescription(optionId, optionValueData.description);
                                }
                            } else if (optionData.description) {
                                self.updateOptionDescription(optionId, optionData.description);
                            } else {
                                self.removeOptionDescription(optionId);
                            }
                        }
                    }
                });
            });
        },

        updateOptionDescription: function updateOptionDescription(optionId, description) {
            this.removeOptionDescription(optionId);

            var shortDescription = $('<div>', {class: 'product-option-short-description'});
            shortDescription.html(description);

            var label = $('div[data-option-id="' + optionId + '"] > div.field > label.label');
            if (label.length === 0) {
                label = $('div[data-option-id="' + optionId + '"] > div.field > fieldset.fieldset-product-options-inner legend.legend');
            }

            if (label.length > 0) {
                label.after(shortDescription);
            }
        },

        removeOptionDescription: function removeOptionDescription(optionId) {
            var label = $('div[data-option-id="' + optionId + '"] > div.field > label.label');
            if (label.length === 0) {
                label = $('div[data-option-id="' + optionId + '"] > div.field > fieldset.fieldset-product-options-inner legend.legend');
            }

            if (label.length > 0) {
                var previousDescription = label.parent().find('.product-option-short-description');
                if (previousDescription.length > 0) {
                    previousDescription.remove();
                }
            }
        }
    });

    return $.mage.productOptionsDescription;
});
