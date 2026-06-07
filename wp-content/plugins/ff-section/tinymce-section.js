(function() {
    tinymce.create("tinymce.plugins.ff_section_plugin", {

        //url argument holds the absolute url of our plugin directory
        init : function(ed, url) {

            //add new button
            ed.addButton("section", {
                title : "Section",
                cmd : "section_command",
                image : url + "/ff_row.png"
            });

            //button functionality.
            ed.addCommand("section_command", function() {
                var selected_text = ed.selection.getContent();
                var return_text = '[section]' + selected_text + '[/section]';
                ed.execCommand("mceInsertContent", 0, return_text);
            });

            // styling the thing
            ed.on('BeforeSetContent', function(e){
            	if(e.content && /[private[^\]]*]/gi.test(e.content)){
            		e.content = e.content.replace(/(\[\/?section[^\]]*\])/gi, '<span style="font-family:monospace;color:#666; background:#FFF8DC;">$1</span>');
            	}
            });
			ed.on('PostProcess', function(e){
            	if(e.content && /[section[^\]]*]/gi.test(e.content)){
            		e.content = e.content.replace(/<span[^>]*>(\[\/?section[^\]]*\])<\/span>/gi, '$1');
            	}
            });

        },

        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                longname : "Section",
                author : "Firefly Interactive",
                version : "1.0"
            };
        }
    });

    tinymce.PluginManager.add("ff_section_plugin", tinymce.plugins.ff_section_plugin);
})();