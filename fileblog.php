<?php

require dirname(__FILE__).'/lib/underscore.php';
require dirname(__FILE__).'/lib/Markdown.php';

class Fileblog {
    public $config;
    public $templates;

    public function __construct() {
        // load config
        $this->config = parse_ini_file('config.ini');

        // timezone
        date_default_timezone_set($this->config['timezone']);

        // compile templates
        $dir = dirname(__FILE__);
        $this->templates = array(
            'article'      => __::template(file_get_contents($dir.'/templates/article.html')),
            'article_list' => __::template(file_get_contents($dir.'/templates/article-list.html')),
            '404'          => __::template(file_get_contents($dir.'/templates/404.html')),
            'page'         => __::template(file_get_contents($dir.'/templates/layout.html')),
        );
    }

    private function read_article($filepath) {
        $data = array();

        // Article meta
        $path_parts = pathinfo($filepath);
        $filename_parts = explode('_', $path_parts['filename']);
        $data['timestamp'] = intval($filename_parts[0]);
        $data['slug'] = $filename_parts[1];
        $data['url'] = $this->config['site_url'].'/'.date('Y\/m\/d\/', $data['timestamp']).$data['slug'];

        // Read article content
        $data['content'] = '';
        $line = 0;
        $readingContent = FALSE;
        $handle = fopen($filepath, 'r');
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== FALSE) {
                $line++;

                if (! $readingContent) {
                    switch ($line) {
                        // First line is title
                        case 1:
                            $data['title'] = trim($buffer);
                            break;

                        // Skip one line
                        case 2:
                            $readingContent = TRUE;
                            break;
                    }
                } else {
                    $data['content'] .= $buffer;
                }
            }
            fclose($handle);
        }
        $data['content'] = Markdown::defaultTransform($data['content']);
        $data['excerpt'] = substr(strip_tags($data['content']), 0, $this->config['excerpt_char_limit']);

        return $data;
    }

    private function show_list($page=1) {
        $filelist = glob(dirname(__FILE__).'/'.$this->config['articles_dir'].'/*.md');
        $filelist = array_reverse($filelist);

        // pageination
        $pagination = array();
        $pagination['page'] = $page;
        $pagination['offset'] = ($pagination['page'] - 1) * $this->config['articles_per_page'];
        $pagination['pagecount'] = ceil(sizeof($filelist) / $this->config['articles_per_page']);

        $filelist = array_slice($filelist, $pagination['offset'], $this->config['articles_per_page']);

        $articles = array();
        foreach ($filelist as $file) {
            $articles[] = $this->read_article($file);
        }

        $this->render_page(array(
            'page_title' => $page == 1 ? $this->config['homepage_title'] : 'Page '.$page,
            'page_content' => $this->render_template('article_list', array(
                'articles' => $articles,
                'pagination' => $pagination,
            )),
        ));
    }

    private function show_article($url_segments) {
        $urldata = array_combine(array('y', 'm', 'd', 'slug'), $url_segments);
        $udate = strtotime($urldata['y'].'-'.$urldata['m'].'-'.$urldata['d']);

        // find all files with this slug
        $filelist = glob(dirname(__FILE__).'/'.$this->config['articles_dir'].'/*_'.$urldata['slug'].'.md');
        $targetfile = NULL;
        foreach ($filelist as $f) {
            $path_parts = pathinfo($f);
            $filename_parts = explode('_', $path_parts['filename']);
            $timestamp = strtotime('midnight', intval($filename_parts[0]));

            if ($timestamp == $udate) {
                $targetfile = $f;
                break;
            }
        }

        if ($targetfile) {
            $article = $this->read_article($targetfile);

            $this->render_page(array(
                'page_title' => $article['title'],
                'page_content' => $this->render_template('article', array(
                    'article' => $article
                )),
            ));
        } else {
            $this->render_page(array(
                'page_title' => 'Not found',
                'page_content' => $this->render_template('404'),
                'response_code' => 404
            ));
        }
    }

    private function render_template($template, $data=array()) {
        $viewdata = array_merge($data, array(
            'app' => $this,
        ));

        return $this->templates[$template]($viewdata);
    }

    private function render_page($data) {
        $default_data = array(
            'page_title' => '',
            'page_content' => '',
            'response_code' => 200
        );
        $viewdata = array_merge($default_data, $data);

        http_response_code($viewdata['response_code']);

        echo $this->templates['page'](array(
            'page_title' => $viewdata['page_title'],
            'page_content' => $viewdata['page_content'],
            'app' => $this,
        ));
    }

    private function route($query) {
        if ($query) {
            $parts = explode('/', $query);

            if ($parts[0] == 'page') {
                $this->show_list(intval($parts[1]));
            }
            elseif (sizeof($parts) == 4) {
                $this->show_article($parts);
            }
            else {
                $this->show_404();
            }
        } else {
            $this->show_list();
        }
    }

    public function start() {
        $query = isset($_GET['q']) ? $_GET['q'] : NULL;
        $this->route($query);
    }
}
