<?php
/**
 * E-shop Plugin: Displays forms for ordering things from e-shop
 *
 * @license    MIT (http://www.opensource.org/licenses/mit-license.php)
 * @author     Pavol Rusnak <stick@gk2.sk>
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
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

        $price_btc = $data['btc'];
        $price_usd = $data['usd'];

        if (!$price_btc && !$price_usd) return 'ERROR';
        if (!$price_btc) {
            $price_btc = $price_usd / $btcusd;
        }
        if (!$price_usd) {
            $price_usd = $price_btc * $btcusd;
        }

        $out  = '<table class="eshop_plugin">';
        $out .= sprintf('<tr><th>Price in USD:</th><td class="price">%0.2f USD</td></tr>', $price_usd);
        $out .= sprintf('<tr><th>Price in BTC:</th><td class="price">%0.3f BTC</td></tr>', $price_btc);
        $out .= sprintf('<tr><td colspan="2" class="button"><img src="images/bitcoin.png" alt="buy"/></td></tr>');
        $out .= '</table>';

        return $out;
    }

    function render($mode, &$renderer, $data) {
        $renderer->doc .= $data;
        return true;
    }

}

?>
