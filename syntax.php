<?php
/**
 * E-shop Plugin: Displays forms for ordering things from e-shop
 *
 * @license    MIT (http://www.opensource.org/licenses/mit-license.php)
 * @author     Pavol Rusnak <stick@gk2.sk>
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_PLUGIN_IMAGES')) define('DOKU_PLUGIN_IMAGES',DOKU_BASE.'lib/plugins/eshop/images/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_eshop extends DokuWiki_Syntax_Plugin {

    function getType() { return 'substition'; }

    function getPType() { return 'block'; }

    function getSort() { return 201; }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{\{eshop>[^}]*\}\}', $mode, 'plugin_eshop');
    }

    function handle($match, $state, $pos, &$handler) {
        $match = substr($match, 8, -2);
        $pairs = explode('&', $match);
        foreach ($pairs as $pair) {
            list($key, $value) = explode('=', $pair, 2);
            $data[trim($key)] = trim($value);
        }
        $data = array_change_key_case($data, CASE_LOWER);

        $btcusd = $this->getConf('btcusd');
        $eurusd = $this->getConf('eurusd');

        $id = $data['id'];
        $name = $data['name'];
        $price_btc = $data['btc'];
        $price_usd = $data['usd'];

        if (!$id) return 'ESHOP ERROR: id missing';

        if (!$name) return 'ESHOP ERROR: name missing';

        if (!$price_btc && !$price_usd) return 'ESHOP ERROR: Both btc and usd prices missing';

        if (!$price_btc) {
            $price_btc = $price_usd / $btcusd;
        }
        if (!$price_usd) {
            $price_usd = $price_btc * $btcusd;
        }
        $price_eur = $price_usd / $eurusd;

        $out  = '<form method="post" action="">';
        $out .= '<table class="eshop_plugin">';
        $out .= sprintf('<tr><th>Price in USD:</th><td class="price">%0.2f</td></tr>', $price_usd);
        $out .= sprintf('<tr><th>Price in EUR:</th><td class="price">%0.2f</td></tr>', $price_eur);
        $out .= sprintf('<tr><th>Price in BTC:</th><td class="price">%0.3f</td></tr>', $price_btc);
        $out .= '<tr><th>Quantity:</th><td class="count"><select name="count">';
        for ($i = 1; $i <= 10; $i++) {
            $out .= sprintf('<option value="%d">%d</option>', $i, $i);
        }
        $out .= '</select></td></tr>';
        $out .= '<tr><td colspan="2" class="button"><input class="submit" type="submit" value="Buy"/></td></tr>';
        $out .= '</table>';
        $out .= sprintf('<input type="hidden" name="id" value="%s" />', $id);
        $out .= sprintf('<input type="hidden" name="name" value="%s" />', $name);
        $out .= sprintf('<input type="hidden" name="btc" value="%f" />', $price_btc);
        $out .= '</form>';

        return $out;
    }

    function render($mode, &$renderer, $data) {
        if ($data === false || $mode != 'xhtml') return false;
        $renderer->doc .= $data;
        return true;
    }

}

?>
