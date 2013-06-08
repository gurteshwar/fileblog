#Fileblog
A static file-based blog system powered by PHP that can be managed by git. Posts are written in Markdown and comments are powered by Disqus. Fileblog uses underscore templates which can be easily customized from scratch.

###Demo
[My blog](http://ramniquesingh.name)

###Setup
1. Clone this repository
    git clone git@github.com:ramniquesingh/fileblog.git my_blog
2. Make sure `articles` directory is readable and writable by the server / PHP

        cd my_blog
        chmod u+rw articles

3. Set document root  for your blog to `my_blog/public`
3. Configure your blog url and other settings in `config.ini`

        site_url = 'http://myblog.com'
        timezone = 'GMT'
        disqus_shortname = 'my_blog'

4. You should now be able to access your blog at: `http://myblog.com`

###Creating an entry
Use the generate tool to create a blog entry

        cd my_blog
        php generate 'This is a new post'
        $ Created article: my_blog//articles/1370732893_this-is-a-new-post.md

This will create an editable markdown file inside the articles directory with the following contents:

        This is a new post
        ==================

        Content goes here ...


###Templates
You can modify the templates inside `templates` directory. Fileblog comes with zero styling and intends for the user to style accordingly.

###Support
Report issues in the issue tracker. Or contact me directly on twitter: [@ramniquesingh](http://twitter.com/ramniquesingh)
