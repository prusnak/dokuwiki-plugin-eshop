<?php
/**
 * E-shop Plugin: Displays forms for ordering things from e-shop
 *
 * @license    MIT (http://www.opensource.org/licenses/mit-license.php)
 * @author     Pavol Rusnak <stick@gk2.sk>
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) die();
if(!defined('DOKU_PLUGIN_IMAGES')) define('DOKU_PLUGIN_IMAGES',DOKU_PLUGIN.'eshop/images/');
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

        if ($data['usd'] && $btcusd && !$data['btc']) {
            $data['btc'] = $data['usd'] / $btcusd;
        } else
        if ($data['btc'] && $btcusd && !$data['usd']) {
            $data['usd'] = $data['btc'] * $btcusd;
        }
        if ($data['usd'] && $eurusd) {
            $data['eur'] = $data['usd'] / $eurusd;
        }
        if (!$data['btc']) $data['btc'] = 0.0;
        if (!$data['eur']) $data['eur'] = 0.0;
        if (!$data['usd']) $data['usd'] = 0.0;
        return $data;
    }

    function render($mode, &$renderer, $data) {
        if ($data === false || $mode != 'xhtml') return false;

        global $INFO;
        $out  = '<form method="post" action="">';
        $out .= '<table class="eshop_plugin">';
        $out .= sprintf('<tr><th>Price in USD:</th><td class="price" id="eshop_price_usd" data-unitprice="%f">%0.2f</td></tr>', $data['usd'], $data['usd']);
        $out .= sprintf('<tr><th>Price in EUR:</th><td class="price" id="eshop_price_eur" data-unitprice="%f">%0.2f</td></tr>', $data['eur'], $data['eur']);
        $out .= sprintf('<tr><th>Price in BTC:</th><td class="price" id="eshop_price_btc" data-unitprice="%f">%0.3f</td></tr>', $data['btc'], $data['btc']);
        $out .= '<tr><th>Quantity:</th><td class="count"><select name="count" id="eshop_count">';
        for ($i = 1; $i <= 10; $i++) {
            $out .= sprintf('<option value="%d">%d</option>', $i, $i);
        }
        $out .= '</select></td></tr>';
        $out .= '<tr><td colspan="2" class="button"><input class="submit" type="submit" value="Buy"/></td></tr>';
        $out .= '</table>';
        $out .= sprintf('<input type="hidden" name="id" value="%s" />', end(explode(':', $INFO['id'])));
        $out .= sprintf('<input type="hidden" name="name" value="%s" />', $INFO['meta']['title']);
        $out .= sprintf('<input type="hidden" name="btcunit" value="%f" />', $data['btc']);
        $out .= sprintf('<input type="hidden" name="btctotal" id="eshop_total" value="%f" />', $data['btc']);
        $out .= '</form>';

        $renderer->doc .= $out;
        return true;
    }

}

?>
