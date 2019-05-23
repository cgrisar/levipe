/* Custom Control Panel JavaScript... */
var H5Button = MediumEditor.extensions.button.extend({
    // Should match what you add to the Statamic object below, and the button name.
    name: 'H5Button',

    tagNames: ['h5'],
    contentDefault: '<b><span>H5</span></b>',
    aria: 'H5',

    handleClick: function () {

        const selection = this.base.options.contentWindow.getSelection().toString().trim();
        const html = `<h5>${selection}</h5>`;
        this.base.pasteHTML(html);

        this.base.checkContentChanged();
    },

});

// Add it to the Statamic object.
Statamic.MediumEditorExtensions.askForLink = H5Button;