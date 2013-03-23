/**
 * cache script and css to localStorage (same domain only)
 *
 * $CLS.style("a.css");
 *
 * $CLS.script("jquery-1.5.2.min.js").wait()
 *     .script("a.js")
 *     .script("b.js")
 *     .script("c.js")
 *     .wait(function() {
 *         alert("something");
 *     });
 *
 * $CLS.remove(['a.css', 'jquery-1.5.2.min.js', 'a.js', 'b.js', 'c.js']);
 */
window.$CLS = (function(window) {
	var
	KEY_PREFIX = "cls-",
	localStorage = window.localStorage || null,
	get = function (url, callback) {
		var xhr = new XMLHttpRequest();
		xhr.open("GET", url, true);
		xhr.onreadystatechange = function (e) {
			if (xhr.readyState === 4) {
				callback(xhr.responseText);
			}
		};
		xhr.send();
	},
	scriptEval = function(text) {
		var script = document.createElement("script"),
			head = document.getElementsByTagName("head")[0];
		script.type = "text/javascript";
		script.appendChild(document.createTextNode(text));
		head.insertBefore(script, head.firstChild);
		head.removeChild(script);
	},
	insertStyle = function(text) {
		var style = document.createElement("style"),
			head = document.getElementsByTagName("head")[0];
		style.type = "text/css";
		style.appendChild(document.createTextNode(text));
		head.appendChild(style);
	},
	queueExec = function(waitCount) {
		var script, i, j, callback;
		if (executedCount >= waitCount) {
			for (i = 0; i < scripts.length; i++) {
				script = scripts[i];
				if (!script) {
					// loading or already executed
					continue;
				}
				scripts[i] = null;
				scriptEval(script);
				executedCount++;
 
				for (j = i; j < executedCount; j++) {
					if (callback = waitCallbacks[j]) {
						waitCallbacks[j] = null;
						callback();
					}
				}
			}
		}
	},
	scripts = [],
	executedCount = 0,
	waitCount = 0,
	waitCallbacks = [];
 
	return {
		script: function(path) {
			var key = KEY_PREFIX + path,
				scriptIndex = scripts.length,
				_waitCount = waitCount;
 
			scripts[scriptIndex] = null;
 
			if (localStorage && localStorage[key]) {
				scripts[scriptIndex] = localStorage[key];
				queueExec(_waitCount);
			} else {
				get(path, function(text) {
					if (localStorage) {
						localStorage[key] = text;
					}
					scripts[scriptIndex] = text;
					queueExec(_waitCount);
				});
			}
			return this;
		},
		style: function(path) {
			var key = KEY_PREFIX + path,
				css;
 
			if (localStorage && localStorage[key]) {
				// insertStyle(localStorage[key]);
				css = '<style type="text/css">' + localStorage[key] + '</style>';
				document.write(css);
			} else {
				get(path, function(text) {
					if (localStorage) {
						localStorage[key] = text;
					}
					insertStyle(text);
				});
			}
			return this;
		},
		wait: function(callback) {
			waitCount = scripts.length;
			if (callback) {
				if (executedCount >= waitCount - 1) {
					callback();
				} else {
					waitCallbacks[waitCount - 1] = callback;
				}
			}
			return this;
		},
		remove: function(paths) {
			var i, key;
			for (i = 0; i < paths.length; i++) {
				key = KEY_PREFIX + paths[i];
				localStorage.removeItem(key);
			}
		}
	};
})(window);