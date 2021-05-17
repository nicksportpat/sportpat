! function(e, r) {
    if ("undefined" == typeof JSON) {
        var t = r.createElement("script");
        t.setAttribute("type", "text/javascript");
        t.setAttribute("src", "//cdnjs.cloudflare.com/ajax/libs/json2/20150503/json2.min.js");
        r.getElementsByTagName("head")[0].appendChild(t);
    }
    var a = "traffic_src";
    e.getTrafficSrcCookie = function() {
		for (var e, t = r.cookie.split(";"), o = 0; o < t.length; o++)
            if (t[o].indexOf(a) >= 0) {
                e = t[o];
                break;
            }
        
            if (e){
                try {
                    return (e = e.substring(e.indexOf("=") + 1, e.length), JSON.parse(e));
                }catch(e){
                    return null;
                }
                
            }else{
                return null;
            }
        //return e ? (e = e.substring(e.indexOf("=") + 1, e.length), JSON.parse(e)) : null
    };
    var o = {
            getParameterByName: function(e, r) {
                r = r.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
                var t = new RegExp("[\\?&]" + r + "=([^&#]*)"),
                    a = t.exec(e);
                return null === a ? "" : decodeURIComponent(a[1].replace(/\+/g, " "));
            },
            getKeywords: function(e) {
                if ("" === e || "(direct)" === e) return "";
                for (var r = "daum:q eniro:search_word naver:query pchome:q images.google:q google:q yahoo:p yahoo:q msn:q bing:q aol:query aol:q lycos:q lycos:query ask:q cnn:query virgilio:qs baidu:wd baidu:word alice:qs yandex:text najdi:q seznam:q rakuten:qt biglobe:q goo.ne:MT search.smt.docomo:MT onet:qt onet:q kvasir:q terra:query rambler:query conduit:q babylon:q search-results:q avg:q comcast:q incredimail:q startsiden:q go.mail.ru:q centrum.cz:q 360.cn:q sogou:query tut.by:query globo:q ukr:q so.com:q haosou.com:q auone:q".split(" "), t = 0; t < r.length; t++) {
                    var a = r[t].split(":"),
                        o = a[0],
                        n = a[1];
                    if (e.indexOf(o) >= 0 && (i.ga_source = o, "" !== this.getParameterByName(e, n))) return this.getParameterByName(e, n);
                }
                var c = new RegExp("^https?://(www.)?google(.com?)?(.[a-z]{2}t?)?/?$", "i"),
                    u = new RegExp("^https?://(r.)?search.yahoo.com/?[^?]*$", "i"),
                    g = new RegExp("^https?://(www.)?bing.com/?$", "i");
                return c.test(e) || u.test(e) || g.test(e) ? "(not provided)" : "";
            },
            getMedium: function(e) {
                return "" !== i.ga_medium ? i.ga_medium : "" !== i.ga_gclid ? "cpc" : "" === i.ga_source ? "" : "(direct)" === i.ga_source ? "(none)" : "" !== i.ga_keyword ? "organic" : "referral";
            },
            getDateAfterYears: function(e) {
                return new Date((new Date).getTime() + 365 * e * 24 * 60 * 60 * 1e3);
            },
            getHostname: function(e) {
                var r = new RegExp("^(https://|http://)?([^/?:#]+)"),
                    t = r.exec(e)[2];
                return null !== t ? t : "";
            },
            waitLoad: function(e, r) {
				var t = 100,
                    a = function() {
                        setTimeout(function() {
                            t--, e() ? r() : t > 0 ? a() : console.error("timed-out!!");
                        }, 100);
                    };
                a();
            }
        },
        n = [{
            key: "utm_source",
            label: "ga_source",
            required: !0
        }, {
            key: "utm_medium",
            label: "ga_medium",
            required: !0
        }, {
            key: "utm_campaign",
            label: "ga_campaign",
            required: !0
        }, {
            key: "utm_content",
            label: "ga_content",
            required: !1
        }, {
            key: "utm_term",
            label: "ga_keyword",
            required: !1
        }],
        i = {},
        c = function() {
            i.ga_gclid = o.getParameterByName(r.location.href, "gclid");
            for (var t = !1, c = 0; c < n.length; c++) {
                var u = o.getParameterByName(r.location.href, n[c].key);
                if (n[c].required && "" === u) {
                    t = !0;
                    for (var g = 0; g < n.length; g++) i[n[g].label] = "";
                    break;
                }
                i[n[c].label] = u;
            }
            if ("" !== i.ga_gclid && "" === i.ga_source) i.ga_source = "google";
            else if (t) {
                if (r.referrer.indexOf(r.location.host) >= 0) return;
                if (null !== e.getTrafficSrcCookie() && "" === r.referrer) return;
                i.ga_source = "" !== r.referrer ? r.referrer : "(direct)";
            }
            if (i.ga_keyword = "" === i.ga_keyword ? o.getKeywords(i.ga_source) : i.ga_keyword, i.ga_medium = o.getMedium(i), i.ga_landing_page = r.location.href, i.ga_source = o.getHostname(i.ga_source), i.ga_client_id = ga.getAll()[0].get("clientId"), "" !== i.ga_source) {
                var l = JSON.stringify(i);
                r.cookie = a + "=; expires=" + new Date(-1), r.cookie = a + "=" + l + "; expires=" + o.getDateAfterYears(1) + "; path=/";
            }
        };
    o.waitLoad(function() {
        return "undefined" != typeof JSON;
    }, function() {
        o.waitLoad(function() {
			if ("undefined" === typeof ga) return;
            return "undefined" != typeof ga.getAll;
        }, c);
    });
}(window, document);