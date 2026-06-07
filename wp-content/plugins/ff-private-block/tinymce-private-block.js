(function() {
    tinymce.create("tinymce.plugins.ff_private_block_plugin", {

        //url argument holds the absolute url of our plugin directory
        init : function(ed, url) {

            //add new button
            ed.addButton("private_block", {
                title : "Private Text",
                cmd : "private_block_command",
                image : url + "/eye-private.png"
            });

            //add new button
            ed.addButton("public_block", {
                title : "Public Text",
                cmd : "public_block_command",
                image : url + "/eye-public.png"
            });

            //button functionality.
            ed.addCommand("private_block_command", function() {
                var selected_text = ed.selection.getContent();
                var return_text = '[private]' + selected_text + '[/private]';
                ed.execCommand("mceInsertContent", 0, return_text);
            });

             //button functionality.
            ed.addCommand("public_block_command", function() {
                var selected_text = ed.selection.getContent();
                var return_text = '[private vis="1"]' + selected_text + '[/private]';
                ed.execCommand("mceInsertContent", 0, return_text);
            });

            // styling the thing
            ed.on('BeforeSetContent', function(e){
            	if(e.content && /[private[^\]]*]/gi.test(e.content)){
            		e.content = e.content.replace(/(\[\/?private[^\]]*\])/gi, '<span style="font-family:monospace;color:#666; background:#FFF8DC;">$1</span>');
            	}
            });
			ed.on('PostProcess', function(e){
            	if(e.content && /[private[^\]]*]/gi.test(e.content)){
            		e.content = e.content.replace(/<span[^>]*>(\[\/?private[^\]]*\])<\/span>/gi, '$1');
            	}
            });
        },

        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                longname : "Private Block",
                author : "Firefly Interactive",
                version : "1.0"
            };
        }
    });

    tinymce.PluginManager.add("ff_private_block_plugin", tinymce.plugins.ff_private_block_plugin);
})();