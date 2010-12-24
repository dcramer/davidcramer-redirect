<?php
function slug($s) {
    $s = strtolower($s);
    $s = str_replace(' ', '-', $s);
    $s = preg_replace('/[^a-z0-9\-_]/', '', $s);
    $s = preg_replace('/[\-]+/', '-', $s);
    return $s;
}

$db = mysql_connect('127.0.0.1', 'root') or die(mysql_error());
mysql_select_db('dcramer_blog', $db) or die(mysql_error());
$tax = array();
$terms = array();
$q = mysql_query("select a.term_taxonomy_id, b.term_id, a.parent, b.slug from wp_term_taxonomy a inner join wp_terms b on a.term_id = b.term_id and a.taxonomy ='category'", $db) or die(mysql_error());
while ($result = mysql_fetch_array($q))
{
    $tax[$result['term_taxonomy_id']] = array($result['parent'], $result['slug']);
    $terms[$result['term_id']] = array($result['parent'], $result['slug']);
}
echo "origin,dest\n";
$q = mysql_query("select ID, post_date, post_name, post_title from wp_posts where post_type = 'post'", $db) or die(mysql_error());
while ($result = mysql_fetch_array($q))
{ 
    $q2 = mysql_query('select term_taxonomy_id from wp_term_relationships where object_id = '.$result['ID'].' order by term_order') or die(mysql_error());
    $post_terms = array();
    while ($r2 = mysql_fetch_array($q2)) {
        if (empty($tax[$r2['term_taxonomy_id']])) {
            continue;
        }
        array_push($post_terms, $tax[$r2['term_taxonomy_id']]);
    }
    $t = $post_terms[0];
    $tree = array($t[1]);
    while (!empty($t[0])) {
        if ($t = $terms[$t[0]]) {
            array_push($tree, $t[1]);
        }
    }
    $tree = array_reverse($tree);

    $old_link = '/'.implode('/', $tree).'/'.$result['ID']."/".$result['post_name'].".html";

    $new_link = '/'.str_replace('-', '/', substr($result['post_date'], 0, 10)).'/'.slug($result['post_title']).'/';
    if (in_array('--full', $argv)) {
        echo "http://davidcramer.net{$old_link},http://justcramer.com{$new_link}\n";
    } else {
        echo "{$old_link},{$new_link}\n";
    }
}
?>
