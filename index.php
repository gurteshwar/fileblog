<?php
$baseurl = 'http://localhost/fileblog';

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
    $pwd = dirname(__FILE__);
    $filepaths = glob($pwd.'/articles/*.txt');

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
        echo '404';
    } else {
        renderArticle($article);
    }
} else {
    renderList($articles);
}