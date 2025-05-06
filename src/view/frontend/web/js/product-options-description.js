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
                    $.each(self.options.descriptions.options, function(optionId, text) {
                        var shortDescription = $('<div>', {class: 'product-option-short-description'});
                        shortDescription.html(text);

                        var label = $('div[data-option-id="' + optionId + '"] > div.field > label.label');
                        if (label.length === 0) {
                            label = $('div[data-option-id="' + optionId + '"] > div.field > fieldset.fieldset-product-options-inner legend.legend');
                        }

                        if (label.length > 0) {
                            label.after(shortDescription);
                        }
                    });
                }
            });
        }
    });

    return $.mage.productOptionsDescription;
});
