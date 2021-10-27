
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue');

require('./bootstrap');
require('./libs/bootstrap-select');
require('./libs/dense-retina');
require('./libs/html5');
require('./libs/parallax');
require('./libs/slidebars');
require('./libs/slicknav');
require('./libs/waves');

// ------------------------------------------------
//  Dense Retina Display
// ------------------------------------------------

if ($.isFunction($.fn.dense)) {
    $('.img-retina').dense();
}

// ------------------------------------------------
//  Bootstrap Select
// ------------------------------------------------

if ($.isFunction($.fn.selectpicker)) {
    $('.selectpicker').selectpicker({
        style: 'btn-default'
    });
}

// ------------------------------------------------
//  Parallax
// ------------------------------------------------

if ($.isFunction($.fn.parallax)) {
    $(document).ready(function () {
        $('.parallax').parallax();
    });
}

// ------------------------------------------------
//  Side menus
// ------------------------------------------------
/*
if ($.isFunction($.fn.slideAndSwipe)) {
    $('#leftNav').slideAndSwipe();
}
*/
if ($.isFunction($.fn.clone)) {
    $(function () {
        $(".adv-panel").html($('.flat-mega-menu ul.mcollapse').clone());
        $(".adv-panel ul").removeClass('drop-down one-column hover-fade');
        $(".adv-panel ul:first-child").addClass('mobile-menu').removeClass('mcollapse changer');
    });
}

