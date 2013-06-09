Precompiling all underscore templates on page load
==================================================

It's a good idea to precompile all [underscore.js](http://underscorejs.org/) templates on page load and have them available for use globally:

**HTML**

    <script type="text/template" data-name="template-1">
        Hi <%= name %>
    </script>


**JS** (assuming you have jQuery):

    var templates = {};

    $(document).ready(function () {
        $("script[type='text/template']").each(function () {
            templates[$(this).data('name')] = _.template($(this).html());
        });
    });


Now you can call these templates by name anywhere on your page:

    templates['template-1']({ name: 'John Doe' });
    // Hi John Doe
