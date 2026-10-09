<?php

/**
 * The homepage illustration: a cookie resting on a browser window, crumbs around.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<svg class="cc-scene" viewBox="0 0 340 300" role="img"
     aria-label="<?php esc_attr_e('Un cookie posé sur une fenêtre de navigateur, entouré de miettes', 'code-cookie'); ?>">
    <circle cx="92" cy="54" r="7" class="cc-scene__crumb"></circle>
    <circle cx="282" cy="42" r="5" class="cc-scene__crumb"></circle>
    <circle cx="312" cy="96" r="8" class="cc-scene__crumb"></circle>
    <circle cx="36" cy="122" r="6" class="cc-scene__crumb"></circle>

    <rect x="46" y="88" width="248" height="176" rx="18" class="cc-scene__window"></rect>
    <rect x="46" y="88" width="248" height="34" rx="18" class="cc-scene__bar"></rect>
    <rect x="46" y="110" width="248" height="12" class="cc-scene__bar"></rect>
    <circle cx="68" cy="105" r="5" class="cc-scene__dot cc-scene__dot--earth"></circle>
    <circle cx="85" cy="105" r="5" class="cc-scene__dot cc-scene__dot--caramel"></circle>
    <circle cx="102" cy="105" r="5" class="cc-scene__dot cc-scene__dot--mint"></circle>

    <rect x="68" y="140" width="96" height="9" rx="4.5" class="cc-scene__line"></rect>
    <rect x="68" y="158" width="150" height="9" rx="4.5" class="cc-scene__line"></rect>
    <rect x="68" y="176" width="64" height="9" rx="4.5" class="cc-scene__line"></rect>
    <rect x="68" y="218" width="118" height="9" rx="4.5" class="cc-scene__line"></rect>
    <rect x="68" y="236" width="86" height="9" rx="4.5" class="cc-scene__line"></rect>

    <circle cx="232" cy="196" r="66" class="cc-scene__cookie"></circle>
    <circle cx="285" cy="152" r="26" class="cc-scene__bite"></circle>
    <circle cx="212" cy="172" r="10" class="cc-scene__chip"></circle>
    <circle cx="252" cy="214" r="12" class="cc-scene__chip"></circle>
    <circle cx="206" cy="218" r="7.5" class="cc-scene__chip"></circle>
    <circle cx="245" cy="168" r="6" class="cc-scene__chip"></circle>
    <circle cx="276" cy="206" r="7" class="cc-scene__chip"></circle>

    <circle cx="156" cy="276" r="6" class="cc-scene__crumb"></circle>
    <circle cx="186" cy="286" r="4" class="cc-scene__crumb"></circle>
    <circle cx="214" cy="274" r="5" class="cc-scene__crumb"></circle>
</svg>
