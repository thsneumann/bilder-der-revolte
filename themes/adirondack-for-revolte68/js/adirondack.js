/*
	By Osvaldas Valutis, www.osvaldas.info
	Available for use under the MIT License
*/

;(function( $, window, document, undefined ) {
	$.fn.doubleTapToGo = function( params ) {
		if ( !( 'ontouchstart' in window ) &&
			!navigator.msMaxTouchPoints &&
			!navigator.userAgent.toLowerCase().match( /windows phone os 7/i ) ) return false;

		this.each( function() {
			var curItem = false;

			$( this ).on( 'click', function( e ) {
				var item = $( this );
				if ( item[ 0 ] != curItem[ 0 ] ) {
					e.preventDefault();
					curItem = item;
				}
			});

			$( document ).on( 'click touchstart MSPointerDown', function( e ) {
				var resetItem = true,
					parents	  = $( e.target ).parents();

				for ( var i = 0; i < parents.length; i++ ) {
					if ( parents[ i ] == curItem[ 0 ] ) {
						resetItem = false;
					}
				}

				if ( resetItem ) {
					curItem = false;
				}
			});
		});
		return this;
	};

	if ( $( window ).width() > 800 ) {
		$( '#site-navigation li:has(ul)' ).doubleTapToGo();
	}
	$( '.has-post-thumbnail' ).doubleTapToGo();
})( jQuery, window, document );

/* Modernizr 2.8.3 (Custom Build) | MIT & BSD
 * Build: http://modernizr.com/download/#-csstransforms-csstransforms3d-csstransitions-shiv-cssclasses-teststyles-testprop-testallprops-prefixes-domprefixes-cssclassprefix:modrn!
 */
;

window.Modernizr = (function( window, document, undefined ) {

    var version = '2.8.3',

    Modernizr = {},

    enableClasses = true,

    docElement = document.documentElement,

    mod = 'modernizr',
    modElem = document.createElement(mod),
    mStyle = modElem.style,

    inputElem  ,


    toString = {}.toString,

    prefixes = ' -webkit- -moz- -o- -ms- '.split(' '),



    omPrefixes = 'Webkit Moz O ms',

    cssomPrefixes = omPrefixes.split(' '),

    domPrefixes = omPrefixes.toLowerCase().split(' '),


    tests = {},
    inputs = {},
    attrs = {},

    classes = [],

    slice = classes.slice,

    featureName,


    injectElementWithStyles = function( rule, callback, nodes, testnames ) {

      var style, ret, node, docOverflow,
          div = document.createElement('div'),
                body = document.body,
                fakeBody = body || document.createElement('body');

      if ( parseInt(nodes, 10) ) {
                      while ( nodes-- ) {
              node = document.createElement('div');
              node.id = testnames ? testnames[nodes] : mod + (nodes + 1);
              div.appendChild(node);
          }
      }

                style = ['&#173;','<style id="s', mod, '">', rule, '</style>'].join('');
      div.id = mod;
          (body ? div : fakeBody).innerHTML += style;
      fakeBody.appendChild(div);
      if ( !body ) {
                fakeBody.style.background = '';
                fakeBody.style.overflow = 'hidden';
          docOverflow = docElement.style.overflow;
          docElement.style.overflow = 'hidden';
          docElement.appendChild(fakeBody);
      }

      ret = callback(div, rule);
        if ( !body ) {
          fakeBody.parentNode.removeChild(fakeBody);
          docElement.style.overflow = docOverflow;
      } else {
          div.parentNode.removeChild(div);
      }

      return !!ret;

    },
    _hasOwnProperty = ({}).hasOwnProperty, hasOwnProp;

    if ( !is(_hasOwnProperty, 'undefined') && !is(_hasOwnProperty.call, 'undefined') ) {
      hasOwnProp = function (object, property) {
        return _hasOwnProperty.call(object, property);
      };
    }
    else {
      hasOwnProp = function (object, property) {
        return ((property in object) && is(object.constructor.prototype[property], 'undefined'));
      };
    }


    if (!Function.prototype.bind) {
      Function.prototype.bind = function bind(that) {

        var target = this;

        if (typeof target != "function") {
            throw new TypeError();
        }

        var args = slice.call(arguments, 1),
            bound = function () {

            if (this instanceof bound) {

              var F = function(){};
              F.prototype = target.prototype;
              var self = new F();

              var result = target.apply(
                  self,
                  args.concat(slice.call(arguments))
              );
              if (Object(result) === result) {
                  return result;
              }
              return self;

            } else {

              return target.apply(
                  that,
                  args.concat(slice.call(arguments))
              );

            }

        };

        return bound;
      };
    }

    function setCss( str ) {
        mStyle.cssText = str;
    }

    function setCssAll( str1, str2 ) {
        return setCss(prefixes.join(str1 + ';') + ( str2 || '' ));
    }

    function is( obj, type ) {
        return typeof obj === type;
    }

    function contains( str, substr ) {
        return !!~('' + str).indexOf(substr);
    }

    function testProps( props, prefixed ) {
        for ( var i in props ) {
            var prop = props[i];
            if ( !contains(prop, "-") && mStyle[prop] !== undefined ) {
                return prefixed == 'pfx' ? prop : true;
            }
        }
        return false;
    }

    function testDOMProps( props, obj, elem ) {
        for ( var i in props ) {
            var item = obj[props[i]];
            if ( item !== undefined) {

                            if (elem === false) return props[i];

                            if (is(item, 'function')){
                                return item.bind(elem || obj);
                }

                            return item;
            }
        }
        return false;
    }

    function testPropsAll( prop, prefixed, elem ) {

        var ucProp  = prop.charAt(0).toUpperCase() + prop.slice(1),
            props   = (prop + ' ' + cssomPrefixes.join(ucProp + ' ') + ucProp).split(' ');

            if(is(prefixed, "string") || is(prefixed, "undefined")) {
          return testProps(props, prefixed);

            } else {
          props = (prop + ' ' + (domPrefixes).join(ucProp + ' ') + ucProp).split(' ');
          return testDOMProps(props, prefixed, elem);
        }
    }


    tests['csstransforms'] = function() {
        return !!testPropsAll('transform');
    };


    tests['csstransforms3d'] = function() {

        var ret = !!testPropsAll('perspective');

                        if ( ret && 'webkitPerspective' in docElement.style ) {

                      injectElementWithStyles('@media (transform-3d),(-webkit-transform-3d){#modernizr{left:9px;position:absolute;height:3px;}}', function( node, rule ) {
            ret = node.offsetLeft === 9 && node.offsetHeight === 3;
          });
        }
        return ret;
    };


    tests['csstransitions'] = function() {
        return testPropsAll('transition');
    };



    for ( var feature in tests ) {
        if ( hasOwnProp(tests, feature) ) {
                                    featureName  = feature.toLowerCase();
            Modernizr[featureName] = tests[feature]();

            classes.push((Modernizr[featureName] ? '' : 'no-') + featureName);
        }
    }



     Modernizr.addTest = function ( feature, test ) {
       if ( typeof feature == 'object' ) {
         for ( var key in feature ) {
           if ( hasOwnProp( feature, key ) ) {
             Modernizr.addTest( key, feature[ key ] );
           }
         }
       } else {

         feature = feature.toLowerCase();

         if ( Modernizr[feature] !== undefined ) {
                                              return Modernizr;
         }

         test = typeof test == 'function' ? test() : test;

         if (typeof enableClasses !== "undefined" && enableClasses) {
           docElement.className+=" modrn-" + (test ? '' : 'no-') + feature;
         }
         Modernizr[feature] = test;

       }

       return Modernizr;
     };


    setCss('');
    modElem = inputElem = null;

    ;(function(window, document) {
                var version = '3.7.0';

            var options = window.html5 || {};

            var reSkip = /^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i;

            var saveClones = /^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i;

            var supportsHtml5Styles;

            var expando = '_html5shiv';

            var expanID = 0;

            var expandoData = {};

            var supportsUnknownElements;

        (function() {
          try {
            var a = document.createElement('a');
            a.innerHTML = '<xyz></xyz>';
                    supportsHtml5Styles = ('hidden' in a);

            supportsUnknownElements = a.childNodes.length == 1 || (function() {
                        (document.createElement)('a');
              var frag = document.createDocumentFragment();
              return (
                typeof frag.cloneNode == 'undefined' ||
                typeof frag.createDocumentFragment == 'undefined' ||
                typeof frag.createElement == 'undefined'
              );
            }());
          } catch(e) {
                    supportsHtml5Styles = true;
            supportsUnknownElements = true;
          }

        }());

            function addStyleSheet(ownerDocument, cssText) {
          var p = ownerDocument.createElement('p'),
          parent = ownerDocument.getElementsByTagName('head')[0] || ownerDocument.documentElement;

          p.innerHTML = 'x<style>' + cssText + '</style>';
          return parent.insertBefore(p.lastChild, parent.firstChild);
        }

            function getElements() {
          var elements = html5.elements;
          return typeof elements == 'string' ? elements.split(' ') : elements;
        }

            function getExpandoData(ownerDocument) {
          var data = expandoData[ownerDocument[expando]];
          if (!data) {
            data = {};
            expanID++;
            ownerDocument[expando] = expanID;
            expandoData[expanID] = data;
          }
          return data;
        }

            function createElement(nodeName, ownerDocument, data){
          if (!ownerDocument) {
            ownerDocument = document;
          }
          if(supportsUnknownElements){
            return ownerDocument.createElement(nodeName);
          }
          if (!data) {
            data = getExpandoData(ownerDocument);
          }
          var node;

          if (data.cache[nodeName]) {
            node = data.cache[nodeName].cloneNode();
          } else if (saveClones.test(nodeName)) {
            node = (data.cache[nodeName] = data.createElem(nodeName)).cloneNode();
          } else {
            node = data.createElem(nodeName);
          }

                                                    return node.canHaveChildren && !reSkip.test(nodeName) && !node.tagUrn ? data.frag.appendChild(node) : node;
        }

            function createDocumentFragment(ownerDocument, data){
          if (!ownerDocument) {
            ownerDocument = document;
          }
          if(supportsUnknownElements){
            return ownerDocument.createDocumentFragment();
          }
          data = data || getExpandoData(ownerDocument);
          var clone = data.frag.cloneNode(),
          i = 0,
          elems = getElements(),
          l = elems.length;
          for(;i<l;i++){
            clone.createElement(elems[i]);
          }
          return clone;
        }

            function shivMethods(ownerDocument, data) {
          if (!data.cache) {
            data.cache = {};
            data.createElem = ownerDocument.createElement;
            data.createFrag = ownerDocument.createDocumentFragment;
            data.frag = data.createFrag();
          }


          ownerDocument.createElement = function(nodeName) {
                    if (!html5.shivMethods) {
              return data.createElem(nodeName);
            }
            return createElement(nodeName, ownerDocument, data);
          };

          ownerDocument.createDocumentFragment = Function('h,f', 'return function(){' +
                                                          'var n=f.cloneNode(),c=n.createElement;' +
                                                          'h.shivMethods&&(' +
                                                                                                                getElements().join().replace(/[\w\-]+/g, function(nodeName) {
            data.createElem(nodeName);
            data.frag.createElement(nodeName);
            return 'c("' + nodeName + '")';
          }) +
            ');return n}'
                                                         )(html5, data.frag);
        }

            function shivDocument(ownerDocument) {
          if (!ownerDocument) {
            ownerDocument = document;
          }
          var data = getExpandoData(ownerDocument);

          if (html5.shivCSS && !supportsHtml5Styles && !data.hasCSS) {
            data.hasCSS = !!addStyleSheet(ownerDocument,
                                                                                'article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}' +
                                                                                    'mark{background:#FF0;color:#000}' +
                                                                                    'template{display:none}'
                                         );
          }
          if (!supportsUnknownElements) {
            shivMethods(ownerDocument, data);
          }
          return ownerDocument;
        }

            var html5 = {

                'elements': options.elements || 'abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output progress section summary template time video',

                'version': version,

                'shivCSS': (options.shivCSS !== false),

                'supportsUnknownElements': supportsUnknownElements,

                'shivMethods': (options.shivMethods !== false),

                'type': 'default',

                'shivDocument': shivDocument,

                createElement: createElement,

                createDocumentFragment: createDocumentFragment
        };

            window.html5 = html5;

            shivDocument(document);

    }(this, document));

    Modernizr._version      = version;

    Modernizr._prefixes     = prefixes;
    Modernizr._domPrefixes  = domPrefixes;
    Modernizr._cssomPrefixes  = cssomPrefixes;



    Modernizr.testProp      = function(prop){
        return testProps([prop]);
    };

    Modernizr.testAllProps  = testPropsAll;


    Modernizr.testStyles    = injectElementWithStyles;    docElement.className = docElement.className.replace(/(^|\s)no-js(\s|$)/, '$1$2') +

                                                    (enableClasses ? " modrn-js modrn-"+classes.join(" modrn-") : '');

    return Modernizr;

})(this, this.document);
;
/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens.
 */
( function( $ ) {
	var container, button, menu;

	container = document.getElementById( 'site-navigation' );
	if ( ! container )
		return;

	button = container.getElementsByTagName( 'button' )[0];
	if ( 'undefined' === typeof button )
		return;

	menu = container.getElementsByTagName( 'ul' )[0];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	if ( -1 === menu.className.indexOf( 'nav-menu' ) )
		menu.className += ' nav-menu';

	button.onclick = function() {
		if ( -1 !== container.className.indexOf( 'toggled' ) )
			container.className = container.className.replace( ' toggled', '' );
		else
			container.className += ' toggled';
	};

	// make dropdowns functional on focus
	$( '.main-navigation' ).find( 'a' ).on( 'focus blur', function() {
		$( this ).parents('li').toggleClass( 'focus' );
	} );

	// Toggle the "long-title" class
	var navButtonsWidth = $(".menu-toggle").outerWidth() + $(".small-widgets-toggle").outerWidth() + 30;
	if ( ( $(".site-title").outerWidth() + navButtonsWidth ) > $(window).width() ) {
		$( ".site-header" ).addClass('long-title');
	}

} )( jQuery );

( function( $ ) {
	if ( location.hash.length && ( ( location.hash.indexOf('comment') != -1 ) || ( location.hash.indexOf('respond') != -1 ) ) ) {
		$( document.body ).addClass('comments-visible');

		if ( $( '#content' ).height() < $( '#comments-container' ).height() ) {
			$( '#comments-bg' ).css({ backgroundColor: 'transparent' });
			$( '#comments-container' ).css({ backgroundColor: 'rgba(44, 54, 66, 0.9)' });
		}
	}

	/***
	 * Run this code when the #toggle-menu link has been tapped
	 * or clicked
	 */
	$( '.toggle-comments' ).on( 'touchstart click', function(e) {
		e.preventDefault();

		var $body     = $( 'body' ),
			$comments = $( '#comments-container' ),

			/* Cross browser support for CSS "transition end" event */
			transitionEnd = 'transitionend webkitTransitionEnd otransitionend MSTransitionEnd';

		/* When the toggle menu link is clicked, animation starts */
		$body.addClass( 'animating' );

		// console.log( $( '#content' ).height(), $comments.height() );
		if ( $( '#content' ).height() < $comments.height() ) {
			$( '#comments-bg' ).css({ backgroundColor: 'transparent' });
			$comments.css({ backgroundColor: 'rgba(44, 54, 66, 0.9)' });
		}

		/***
		 * Determine the direction of the animation and
		 * add the correct direction class depending
		 * on whether the menu was already visible.
		 */
		if ( $body.hasClass( 'comments-visible' ) ) {
			$body.addClass( 'right' );
		} else {
			$body.addClass( 'left' );
			// Comments are visible, move focus to the first focusable element.
			var element = $( document.getElementById( "comments" ) ).find( 'a,select,input,button,textarea' );
			if ( element.length ) {
				element.first().focus();
			}
		}

		/***
		 * When the animation (technically a CSS transition)
		 * has finished, remove all animating classes and
		 * either add or remove the "menu-visible" class
		 * depending whether it was visible or not previously.
		 */
		if ( Modernizr.csstransitions ) {
			$comments.on( transitionEnd, function() {
				$body
					.removeClass( 'animating left right' )
					.toggleClass( 'comments-visible' );

				if ( ! $body.hasClass('comments-visible' ) ) {
					$comments.css({ backgroundColor: 'transparent' });
					$( '#comments-bg' ).css({ backgroundColor: 'rgba(44, 54, 66, 0.9)' });
				}

				$comments.off( transitionEnd );
			} );
		} else {
			// We don't have transitions, so there is no animation.
			$body
				.removeClass( 'animating left right' )
				.toggleClass( 'comments-visible' );

			if ( ! $body.hasClass('comments-visible' ) ) {
				$comments.css({ backgroundColor: 'transparent' });
				$( '#comments-bg' ).css({ backgroundColor: 'rgba(44, 54, 66, 0.9)' });
			}
		}

		// If we've clicked the text link, we should change the URL and jump to the top of the comments
		if ( e.target.className.length && e.target.className.indexOf('text') !== -1 ) {
			if ( ! $body.hasClass( 'comments-visible' ) ){
				location.hash = '#comments';
				$( window ).scrollTop( $comments.offset().top );
			}
		}

	} );
} )( jQuery );
/**
 * Widget Toggle handler
 */
( function( $ ) {
	var $widgets = $( "#secondary" )
		widgets = {};

	widgets.toggle = function(e) {
			e.preventDefault();

			var $body    = $( 'body' ),
				$page    = $( '#page' ),

				/* Cross browser support for CSS "transition end" event */
				transitionEnd = 'transitionend webkitTransitionEnd otransitionend MSTransitionEnd';

			/* When the toggle link is clicked, animation starts */
			$body.addClass( 'widgets-animating' );

			/***
			 * Determine the direction of the animation and add the correct
			 * translation. We can't do the class switch since the height of
			 * the widget area can be variable.
			 */
			if ( Modernizr.csstransforms3d ) {
				if ( $body.hasClass( 'widgets-visible' ) ) {
					$page.css({
						transform: 'translate3d( 0, 0px, 0)'
					});
					$widgets.css({
						transform: 'translate3d( 0, -' + $widgets.height() + 'px, 0)'
					});
					$( window ).scrollTop( 0 );
					$( document ).off( 'keyup', widgets.keyToggle );
				} else {
					$page.css({
						transform: 'translate3d( 0, ' + $widgets.height() + 'px, 0)'
					});
					$widgets.css({
						transform: 'translate3d( 0, 0px, 0)'
					});

					// Widgets are visible, move focus to the first focusable element.
					var element = $( document.getElementById( "secondary" ) ).find( 'a,select,input,button,textarea' );
					if ( element.length ) {
						element.first().focus();
					}
					$( document ).on( 'keyup', widgets.keyToggle );
				}
			} else if ( Modernizr.csstransforms ) {
				// IE9 supports 2d transforms, but not 3d.
				if ( $body.hasClass( 'widgets-visible' ) ) {
					$page.attr( 'style', 'transform: translate( 0, 0 );' );
					$widgets.attr( 'style', 'transform: translate( 0, -' + $widgets.height() + 'px)' );
					$( window ).scrollTop( 0 );
					$( document ).off( 'keyup', widgets.keyToggle );
				} else {
					$page.attr( 'style', 'transform: translate( 0, ' + $widgets.height() + 'px)' );
					$widgets.attr( 'style', 'transform: translate( 0, 0 );' );

					// Widgets are visible, move focus to the first focusable element.
					var element = $( document.getElementById( "secondary" ) ).find( 'a,select,input,button,textarea' );
					if ( element.length ) {
						element.first().focus();
					}
					$( document ).on( 'keyup', widgets.keyToggle );
				}
			}


			/***
			 * When the animation (technically a CSS transition)
			 * has finished, remove all animating classes and
			 * either add or remove the "widgets-visible" class
			 * depending whether it was visible or not previously.
			 */
			if ( Modernizr.csstransitions ) {
				$widgets.on( transitionEnd, function() {
					$body
						.removeClass( 'widgets-animating up down' )
						.toggleClass( 'widgets-visible' );

					$widgets.off( transitionEnd );
				} );
			} else {
				$body.removeClass( 'widgets-animating up down' ).toggleClass( 'widgets-visible' );
			}
		};

	widgets.keyToggle = function( event ){
			if ( 27 === event.which ) {
				widgets.toggle( event );
			}
		};

	$widgets.css({
		transform: 'translate3d( 0, -' + $widgets.height() + 'px, 0)'
	});

	$( '.widgets-toggle' ).on( 'touchstart click', widgets.toggle );

} )( jQuery );
