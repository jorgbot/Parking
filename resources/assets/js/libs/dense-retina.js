/*! Dense v0.0.1 | Copyright (C) 2013 Jukka Svahn | http://dense.rah.pw/ | Released under the MIT License */
!function(a) {
  "function" == typeof define && define.amd ? define(["jquery"], a) : a(jQuery)
}(function(a) {
  var b, c = [], d = {}, e = /^https?:\/\//, f = /\.\w+$/, g = ["image/jpg", "image/jpeg", "image/png", "image/gif", "image/bmp"], h = ["svg"];
  d.init = function(i) {
    return i = a.extend({
      ping: !0,
      dimensions: "preserve"
    }, i), this.filter("img").not(".dense").addClass("dense dense-loading").each(function() {
      var j, k = a(this), l = d.getImageAttribute.call(this), m = k.attr("src"), n=!1;
      if (!l) {
        if (!m || 1 == b || a.inArray(l.split(".").pop().split(/[\?\#]/).shift(), h))
          return;
        l = m.replace(f, function(a) {
          return "_" + b + "x" + a
        }), n = i.ping&&-1 === a.inArray(l, c) && (e.test(l)===!1||-1 !== l.indexOf("://" + document.domain))
      }
      j = function() {
        var a = function() {
          k.removeClass("dense-loading").addClass("dense-ready").trigger("dense-retina-loaded")
        };
        k.attr("src", l).data("dense-original", m), "update" == i.dimensions ? k.dense("updateDimensions").one("dense-dimensions-updated", a) : ("remove" == i.dimensions && k.removeAttr("width height"), a())
      }, n ? a.ajax({
        url: l,
        type: "HEAD"
      }).done(function(b, d, e) {
        var f = e.getResponseHeader("Content-type");
        200 !== e.status || null !== f&&-1 === a.inArray(f.split(";").shift(), g) || (c.push(l), j())
      }) : j()
    }), this
  }, d.updateDimensions = function() {
    return this.each(function() {
      var b, c = a(this), d = c.attr("src");
      d && (b = new Image, b.src = d, a(b).on("load.dense", function() {
        c.attr("width", b.width).attr("height", b.height), c.trigger("dense-dimensions-updated")
      }))
    })
  }, d.devicePixelRatio = function() {
    var b = 1;
    return "undefined" !== a.type(window.devicePixelRatio) ? b = window.devicePixelRatio : "undefined" !== a.type(window.matchMedia) && a.each([1.3, 1.5, 2, 3, 4, 5, 6], function(a, c) {
      var d = ["(-webkit-min-device-pixel-ratio: " + c + ")", "(min--moz-device-pixel-ratio: " + c + ")", "(-o-min-device-pixel-ratio: " + c + ")", "(min-resolution: " + c + "dppx)"].join(",");
      return window.matchMedia(d).matches ? void 0 : (b = c, !1)
    }), Math.ceil(b)
  }, d.getImageAttribute = function() {
    for (var c, d = a(this).eq(0), e=!1, f = 1; b >= f; f++)
      c = d.attr("data-" + f + "x"), c && (e = c);
    return e
  }, b = d.devicePixelRatio(), a.fn.dense = function(b, c) {
    return ("string" !== a.type(b) || "function" !== a.type(d[b])) && (c = b, b = "init"), d[b].call(this, c)
  }, a(document).ready(function() {
    a("body.dense-retina img").dense()
  })
});

