/*!
 * Simple JavaScript Inheritance
 * By John Resig http://ejohn.org/
 * MIT Licensed.
 *
 * Extended by Jonathon Byrd to include function hooks
 * https://gist.github.com/Jonathonbyrd/724083
 *
 * Don't forget your Shims!
 * https://github.com/kriskowal/es5-shim/blob/master
 */
(function(){
  var initializing = false, fnTest = /xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/;

  // creating hooks
  function hookable(fn) {
	  var ifn = fn,
		  hooks = {
			  before : [],
			  after : []
		  };
	  
	  function hookableFunction() {
		  var args = [].slice.call(arguments, 0),
			  i = 0,
			  fn;
		  for (i = 0; !!hooks.before[i]; i += 1) {
			  fn = hooks.before[i];
			  fn.apply(this, args);
		  }
		  var r = ifn.apply(this, arguments);
		  for (i = 0; !!hooks.after[i]; i++) {
			  fn = hooks.after[i];
			  fn.apply(this, args);
		  }
		  return r;
	  }
	  
	  hookableFunction.bind = function (oThis) {
	    if (typeof this !== "function") {
	      // closest thing possible to the ECMAScript 5 internal IsCallable function
	      throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
	    }
	 
	    var aArgs = Array.prototype.slice.call(arguments, 1), 
	        fToBind = this, 
	        fNOP = function () {},
	        fBound = function () {
	          return fToBind.apply(this instanceof fNOP && oThis
	                                 ? this
	                                 : oThis,
	                               aArgs.concat(Array.prototype.slice.call(arguments)));
	        };
	 
	    fNOP.prototype = this.prototype;
	    fBound.prototype = new fNOP();
	 
	    return fBound;
	  };
	  
	  hookableFunction.addHook = function (type, fn) {
		  if (hooks[type] instanceof Array) {
			  hooks[type].push(fn);
		  } else {
			  throw (function () {
				  var e = new Error("Invalid hook type");
				  e.expected = Object.keys(hooks);
				  e.got = type;
				  return e;
			  }());
		  }
	  };
	  
	  return hookableFunction;
  }
  
  // for extending objects
  function extend(o,oo) {
    if (typeof jQuery == 'undefined')
    return o;
    return jQuery.extend(true,oo,o);
  }

  // The base _Class implementation (does nothing)
  this._Class = function(){};

  // Create a new _Class that inherits from this _Class
  _Class.extend = function(prop) {
	var _super = this.prototype;
	
	// Instantiate a base _Class (but only create the instance,
	// don't run the init constructor)
	initializing = true;
	var prototype = new this();
	initializing = false;
	
	// Copy the properties over onto the new prototype
	for (var name in prop) {
	  // Check if we're overwriting an existing function
	  prototype[name] = 
		typeof prop[name] == "function" 
        && typeof _super[name] == "function" 
        && fnTest.test(prop[name])
        // this is how we override a function
		? (function(name, fn){
			  return function() {
				var tmp = this._super;
				
				// Add a new ._super() method that is the same method
				// but on the super-class
				this._super = _super[name];
				
				// The method only need to be bound temporarily, so we
				// remove it when we're done executing
				var ret = fn.apply(this, arguments);
				this._super = tmp;
				
				return ret;
			  };
		  })(name, prop[name])
        // this is how we create a function
		: (typeof prop[name] == 'function' ?hookable(prop[name]) 
        
          // direct overload of a property
          :(typeof prop[name] == 'object' 
              && typeof _super[name] == 'object' 
              // extend an object
              ?extend(prop[name],_super[name]) :prop[name]));
	}
	
	// The dummy class constructor
	function _Class() {
	  // All construction is actually done in the init method
	  if ( !initializing && this.init )
		this.init.apply(this, arguments);
	}
	
	// Populate our constructed prototype object
	_Class.prototype = prototype;
	
	// Enforce the constructor to be what we expect
	_Class.constructor = _Class;
	
	// And make this class extendable
	_Class.extend = hookable(arguments.callee);
	
	return _Class;
  };
})();

Class = _Class.extend({
  defaults:{
	container : null
  },
  data:{},
  init:function(o) {
	this.setOptions(o);
  },
  setOptions:function(o) {
	var obj = new Object();
	for (var attrname in this.defaults) { obj[attrname] = this.defaults[attrname]; }
	for (var attrname in o) { obj[attrname] = o[attrname]; }
	this.data = obj;
	
	return this;
  },
  set:function(p, value) {
	this.data[p] = value;
  },
  get:function(p, d) {
	return typeof this.data[p] != 'undefined' ?this.data[p] :d;
  },
  element:function() {
	return this.get('container');
  },
  rawElement:function() {
	return this.element().context;
  },
  uid:function(){
	return this._uid().replace(/[/,'').replace(/]/,'').replace(/:/,''); 
  },
  _uid:function() {
    if (this.element().attr('id'))
		return this.element().attr('id');

    if (this.element().attr('name'))
		return this.element().attr('name');
	
    var form = '';
    if (this.element().context.form.id)
    	form = this.element().context.form.id;

    if (!form && this.element().context.form.name)
    	form = this.element().context.form.name;
    
    return form + this.element().index();
  }
});
