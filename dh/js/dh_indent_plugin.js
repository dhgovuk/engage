(function(){
    tinymce.PluginManager.add('dh_indent_button', function(editor, url) {
        // Add a button that opens a window
        editor.addButton('dh_indent_button', {
            text: 'Indent',
            icon: false,
            onclick: function() {
                editor.insertContent('[dh-indent]' + editor.selection.getContent() + '[/dh-indent]');
            }
        });
    });
})();