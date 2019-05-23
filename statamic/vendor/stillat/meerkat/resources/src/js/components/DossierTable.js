window.DossierTable = DossierTable = Vue.component('DossierTable', {

    template: Meerkat.getDossierTemplate(),

    props: ['options', 'keyword'],

    data: function () {
        return {
            items: [],
            columns: [],
            sortCol: this.options.sort || null,
            sortOrder: this.options.sortOrder || 'asc',
            sortOrders: {},
            reordering: false,
            sizes: {
                xs: false,
                sm: false,
                md: false,
                lg: false
            },
            shouldOverrideToMobile: false        }
    },

    partials: {
        // The default cell markup will be a link to the edit_url with a status symbol
        // if it's the first cell. Remaining cells just get the label.
        cell: Meerkat.getDossierCellTemplate()
    },

    computed: {

        hasCheckboxes: function () {
            if (this.options.checkboxes === false) {
                return false;
            }

            return true;
        },

        itemsAreChecked: function () {
            return this.checkedItems.length > 0;
        },

        hasSearch: function () {
            if (this.options.search === false) {
                return false;
            }

            return true;
        },

        hasHeaders: function () {
            if (this.options.headers === false) {
                return false;
            }

            return true;
        },

        hasActions: function () {
            return this.options.partials.actions !== undefined
                && this.options.partials.actions !== '';
        },

        showBulkActions: function () {
            return (this.hasItems && this.hasCheckboxes && this.itemsAreChecked && !this.reordering);
        },

        hasItems: function () {
            return this.$parent.hasItems;
        },

        reorderable: function () {
            return this.options.reorderable;
        },

        checkedItems: function () {
            return this.items.filter(function (item) {
                return item.checked;
            }).map(function (item) {
                return item.id;
            });
        },

        allItemsChecked: function () {
            return this.items.length === this.checkedItems.length;
        },

        computedSearch: function () {
            if (this.reordering) {
                return null;
            }

            return this.keyword;
        },

        computedSortCol: function () {
            if (this.reordering) {
                return false;
            }

            return this.sortCol;
        },

        computedSortOrder: function () {
            if (this.reordering) {
                return false;
            }

            return this.sortOrders[this.sortCol];
        }
    },

    beforeCompile: function () {
        var self = this;

        _.each(self.options.partials, function (str, name) {
            self.$options.partials[name] = str;
        });
    },

    ready: function () {
        this.items = this.$parent.items;
        this.columns = this.$parent.columns;

        this.setColumns();
        this.setSortOrders();

        this.sortCol = this.options.sort || this.columns[0].field;

        if (typeof this.$parent !== 'undefined' && this.$parent !== null) {
            if (typeof this.$parent.passMobileOverride !== 'undefined') {
                if (this.$parent.passMobileOverride) {
                    this.shouldOverrideToMobile = true;
                }
            }
        }

        window.addEventListener('resize', this.handleResize);
        this.handleResize();
    },

    beforeDestroy: function () {
        window.removeEventListener('resize', this.handleResize);
    },

    methods: {

        setIntended: function (intended) {
            this.intendedComment = intended;
        },

        handleResize: function () {

            if (this.shouldOverrideToMobile) {
                this.sizes.xs = true;
                this.sizes.sm = false;
                this.sizes.md = false;
                this.sizes.lg = false;
                return;
            }

            var width = window.innerWidth;

            if (width <= 767) {
                this.sizes.xs = true;
                this.sizes.sm = false;
                this.sizes.md = false;
                this.sizes.lg = false;
                return;
            }
            if (width >= 768 && width <= 991) {
                this.sizes.xs = false;
                this.sizes.sm = true;
                this.sizes.md = false;
                this.sizes.lg = false;
                return;
            }
            if (width >= 992 && width <= 1199) {
                this.sizes.xs = false;
                this.sizes.sm = false;
                this.sizes.md = true;
                this.sizes.lg = false;
                return;
            }
            if (width >= 1200) {
                this.sizes.xs = false;
                this.sizes.sm = false;
                this.sizes.md = false;
                this.sizes.lg = true;
                return;
            }
        },
        
        registerPartials: function () {
            var self = this;

            _.each(self.options.partials, function (str, name) {
                Vue.partial(name, str);
            });
        },

        setColumns: function () {
            var columns = [];
            _.each(this.columns, function (column) {
                if (typeof column === 'object') {
                    columns.push({label: column.label, field: column.field, translation: column.translation});
                } else {
                    columns.push({label: column, field: column});
                }
            });
            this.columns = columns;
        },

        setSortOrders: function () {
            var sortOrders = {};
            _.each(this.columns, function (col) {
                sortOrders[col.field] = 1;
            });

            // Apply the initial sort order
            sortOrders[this.sortCol] = (this.sortOrder === 'asc') ? 1 : -1;

            this.sortOrders = sortOrders;
        },

        sortBy: function (col) {
            if (this.sortCol === col.field) {
                this.sortOrders[col.field] = this.sortOrders[col.field] * -1;
            }

            this.sortCol = col.field;
        },

        checkAllItems: function () {
            var status = !this.allItemsChecked;

            _.each(this.items, function (item) {
                item.checked = status;
            });
        },

        toggle: function (item) {
            item.checked = !item.checked;
        },

        enableReorder: function () {
            var self = this;

            self.reordering = true;

            $(this.$els.tbody).sortable({
                axis: 'y',
                revert: 175,
                placeholder: 'placeholder',
                handle: '.drag-handle',
                forcePlaceholderSize: true,

                start: function (e, ui) {
                    ui.item.data('start', ui.item.index())
                },

                update: function (e, ui) {
                    var start = ui.item.data('start'),
                        end = ui.item.index();

                    self.items.splice(end, 0, self.items.splice(start, 1)[0]);
                }

            });
        },

        disableReorder: function () {
            this.reordering = false;
            $(this.$els.tbody).sortable('destroy');
        },

        saveOrder: function () {
            this.$parent.saveOrder();
        },

        /**
         * Dynamically call a method on the parent component
         *
         * Eg. `call('foo', 'bar', 'baz')` would be the equivalent
         * of doing `this.$parent.foo('bar', 'baz')`
         */
        call: function (method) {
            var args = Array.prototype.slice.call(arguments, 1);
            this.$parent[method].apply(this, args);
        }
    },

    events: {
        'reordering.start': function () {
            this.enableReorder();
        },
        'reordering.saved': function () {
            this.reordering = false;
        },
        'reordering.stop': function () {
            this.disableReorder();
        }
    }
});