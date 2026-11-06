<?php

/**
 * The drawn cover, used when a post has no usable featured image.
 *
 * Deliberately not a bitmap: it inherits the flavour colours, follows the
 * light/dark scheme, and weighs nothing.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<span class="cc-cover" aria-hidden="true">
    <svg viewBox="0 0 320 160" focusable="false" preserveAspectRatio="xMidYMid slice">
        <circle class="cc-cover__crumb" cx="40" cy="34" r="6"></circle>
        <circle class="cc-cover__crumb" cx="286" cy="30" r="4.5"></circle>
        <circle class="cc-cover__crumb" cx="264" cy="128" r="7"></circle>
        <circle class="cc-cover__crumb" cx="58" cy="124" r="5"></circle>

        <circle class="cc-cover__cookie" cx="160" cy="80" r="48"></circle>
        <circle class="cc-cover__bite" cx="196" cy="48" r="17"></circle>

        <circle class="cc-cover__chip" cx="142" cy="64" r="8"></circle>
        <circle class="cc-cover__chip" cx="172" cy="94" r="9"></circle>
        <circle class="cc-cover__chip" cx="146" cy="99" r="5.5"></circle>
        <circle class="cc-cover__chip" cx="177" cy="66" r="4.5"></circle>
        <circle class="cc-cover__chip" cx="124" cy="85" r="4"></circle>

        <circle class="cc-cover__crumb" cx="214" cy="110" r="5"></circle>
        <circle class="cc-cover__crumb" cx="232" cy="96" r="3"></circle>
    </svg>
</span>
