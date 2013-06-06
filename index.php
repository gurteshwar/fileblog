<?php
$pwd = dirname(__FILE__);
require $pwd.'/lib/underscore.php';

// compile templates
$template_article = __::template(file_get_contents($pwd.'/templates/article.html'));
$template_article_list = __::template(file_get_contents($pwd.'/templates/article-list.html'));
$template_page = __::template(file_get_contents($pwd.'/templates/layout.html'));

function findArticleBySlug($slug, $articles) {
    foreach ($articles as $article) {
        if ($article['slug'] === $slug) {
            return $article;
        }
    }
}

function renderArticle($article) {
    print_r($article);
}

function renderList($articles) {
    print_r($articles);
}

function buildData() {
    // Get articles list
    $baseurl = 'http://localhost/fileblog';
    $filepaths = glob(dirname(__FILE__).'/articles/*.txt');

    // Build blog data
    $articles = array();
    foreach ($filepaths as $filepath) {
        $data = array();
        $line = 0;
        $readingContent = FALSE;

        $handle = fopen($filepath, 'r');
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== FALSE) {
                $line++;

                if (!$readingContent) {
                    switch ($line) {
                        case 1:
                            $data['title'] = substr(trim($buffer), 7);
                            break;

                        case 2:
                            $data['timestamp'] = strtotime(substr(trim($buffer), 6));
                            break;

                        case 3:
                            $data['slug'] = substr(trim($buffer), 6);
                            break;

                        case 4:
                            break;

                        default:
                            $data['content'] = $buffer;
                            $readingContent = TRUE;
                            break;
                    }
                } else {
                    $data['content'] .= $buffer;
                }
            }
            fclose($handle);
        }

        // other info
        $data['url'] = $baseurl.'/'.date('Y\/m\/d\/', $data['timestamp']).$data['slug'];

        $articles[] = $data;
    }

    return $articles;
}

$articles = buildData();

$pquery = $_GET['q'];
if ($pquery) {
    // extract slug
    $parts = explode('/', $pquery);
    $slug = array_pop($parts);

    $article = findArticleBySlug($slug, $articles);

    if (!$article) {
        echo $template_page(array(
            'page_title' => '404',
            'page_content' => 'Not found!',
        ));
    } else {
        echo $template_page(array(
            'page_title' => $article['title'],
            'page_content' => $template_article(array('article' => $article)),
        ));
    }
} else {
    echo $template_page(array(
        'page_title' => 'File Blog',
        'page_content' => $template_article_list(array('articles' => $articles)),
    ));
}
