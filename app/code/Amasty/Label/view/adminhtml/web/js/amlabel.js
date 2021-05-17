define([
    "jquery",
    "jquery/ui",
    'jquery/jquery.cookie'
], function ($) {
    $.widget('mage.amLabelPosition', {
        name: null,
        element: null,
        table: null,
        tds: null,
        positionClasses: [
            'top-left', 'top-center', 'top-right', 'middle-left', 'middle-center',
            'middle-right', 'bottom-left', 'bottom-center', 'bottom-right'
        ],

        _create: function () {
            this.name = this.element.attr('id').replace('labels_', '');
            this.table = $('#amlabel-table-' + this.name);
            if (!this.table.length || !this.element.length) {
                return;
            }

            var self = this;
            this.tds = this.table.find('td');
            this.table.on("click", "td", function () {
                self.tdClick(this);
            });

            var currentValue = this.element.val();
            if (currentValue) {
                var td = this.getElementByIndex(parseInt(currentValue));
                $(td).addClass('selected');
            }

        },

        tdClick: function (item) {
            var value = this.index(item, 1) - 1;
            if (value >= 0) {
                this.element.val(value);
                this.tds.removeClass('selected');

                $(item).addClass('selected');
            }

            var type = this.element.attr('id').replace('_pos', ''),
                label = $('#' + type + '_preview .amasty-label-container');

            if (label.length) {
                var position = this.positionClasses[this.element.val()];
                label.unwrap();
                label.amShowLabel('setPosition', position);
            }
        },

        getElementByIndex: function (currentValue) {
            var col = Math.floor(currentValue / 3),
                cell = currentValue % 3,
                element = this.table.find('tr:nth-child(' + (col + 1) + ') td:nth-child(' + (cell + 1) + ')')[0];

            return element;
        },

        index: function (node, parent) {
            var index = 0;
            var siblings = node.parentNode.childNodes;
            for (var j in siblings) {
                if (siblings.hasOwnProperty(j)) {
                    if (siblings[j].nodeType != Node.ELEMENT_NODE) {
                        continue;
                    }
                    ++index;
                    if (siblings[j] == node) {
                        break;
                    }
                }
            }
            if (parent) {
                index += (this.index(node.parentNode, 0) - 1) * 3;
            }

            return index || -1;
        }
    });

    $.widget('mage.amLabelChoose', {
        _create: function () {
            var self = this;
            this.element.on("click", "input", function () {
                self.itemClick(this);
            });

            $(document).ready(function () {
                self.element.find('input:checked').click();
            });
        },

        itemClick: function (item) {
            var value = item.value,
                labelTypeField = $(item).closest('.field-cat_img, .field-prod_img');
            labelTypeField.next().hide();
            if (item.value.indexOf('text_only') >= 0) {
                labelTypeField.find('.additional').hide();
                labelTypeField.parent().find('.field-prod_image_size, .field-cat_image_size').hide();

                return null;
            }
            if (item.value.indexOf('shape') >= 0) {
                var hide = $('#amlabel-' + value.replace('shape', 'download')),
                    show = $('#amlabel-' + value),
                    type = item.value.replace('shapelabels_', '').replace('_img', '');
                $('.field-' + type + '_label_color').show();
                labelTypeField.next().show();
            } else {
                var hide = $('#amlabel-' + value.replace('download', 'shape')),
                    show = $('#amlabel-' + value),
                    type = value.replace('downloadlabels_', '').replace('_img', '');
                $('.field-' + type + '_label_color').hide();
            }
            show.show();
            hide.hide();
            labelTypeField.parent().find('.field-prod_image_size, .field-cat_image_size').show();
        }
    });


    $.widget('mage.amLabeltabs', {
        _create: function () {
            $('body').on("click", 'a.tab-item-link', function () {
                var id = $(this).attr('id');
                $('label_open_tab_input').val(id);
                $.cookie("amasty_labels_current_tab", id);

                if ($(this)[0].name.indexOf("category") !== -1) {
                    $('#labels_cat_image_size').blur();
                } else if ($(this)[0].name.indexOf("product") !== -1) {
                    $('#labels_prod_image_size').blur();
                }
            });
        }
    });

    $.widget('mage.amLabelPreview', {
        type: '',

        _create: function () {
            var self = this;
            $(document).ready(function () {
                self._initialize();
                self._initializeControls();
            });
        },

        _initialize: function (item) {
            var self = this;
            this.element.hide();
            var menuParent = $('ul.admin__page-nav-items');
            this.element.appendTo(menuParent);
            this.element.wrap('<li>');

            var type = this.element.attr('id').replace('labels_', '').replace('_preview', ''),
                tabId = 'amasty_label_labels_edit_tabs_category_image_section';
            if (type === 'prod') {
                tabId = 'amasty_label_labels_edit_tabs_product_image_section';
            }

            var id = $.cookie("amasty_labels_current_tab");
            if (id == tabId) {
                this.element.show();
            }

            menuParent.on("click", 'a:not(#' + tabId + ')', function () {
                self.element.hide();
            });

            $('#' + tabId).click(function () {
                self.element.show();
            });
        },

        _initializeControls: function () {
            this.type = this.element.attr('id').replace('_preview', '');
            var self = this;
            $('body').on("blur", 'input[id^="' + this.type + '"], textarea[id^="' + this.type + '"]', function () {
                var element = $(this),
                    value = element.val(),
                    name = element.attr('name'),
                    label = $('#' + self.type + '_preview .amasty-label-container'),
                    type = self.type.replace('labels_', '');
                switch (name) {
                    case (type + '_image_size'):
                        label.amShowLabel('setLabelSize', value);
                        label.css("width", "");
                        label.find('.amasty-label-text').css("font-size", "");
                        break;
                    case (type + '_size'):
                        value = value.replace(';', '');
                        label.css({
                            'font-size': value
                        });
                        break;
                    case (type + '_color'):
                        label.css({
                            'color': value
                        });
                        break;
                    case (type + '_style'):
                        var oldStyle = label.attr('style');
                        value = value.split(";");
                        for (item in value) {
                            if (parseInt(item) >= 0) {
                                var st = value[item];
                                var styleName = st.substring(0, st.indexOf(':'));
                                if (styleName) {
                                    if (oldStyle.indexOf(styleName) !== -1) {
                                        var regexp = new RegExp(styleName + "(.*?);");
                                        oldStyle = oldStyle.replace(regexp, st + ';');
                                    } else {
                                        oldStyle += st + ";";
                                    }
                                }
                            }
                        }
                        label.attr('style', oldStyle);
                        break;
                    case (type + '_txt'):
                        label.find('.amasty-label-text').text(value);
                        break;
                }

                label.unwrap();

                label.amShowLabel('setLabelPosition');
                label.amShowLabel('setStyle');
                label.amShowLabel('reloadParentSize');
            });
        }
    });
});
