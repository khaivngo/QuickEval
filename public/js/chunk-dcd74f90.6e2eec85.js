(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-dcd74f90"],{"0a49":function(t,e,i){var n=i("9b43"),a=i("626a"),o=i("4bf8"),r=i("9def"),s=i("cd1c");t.exports=function(t,e){var i=1==t,c=2==t,l=3==t,u=4==t,d=6==t,h=5==t||d,v=e||s;return function(e,s,f){for(var p,m,y=o(e),g=a(y),b=n(s,f,3),w=r(g.length),k=0,x=i?v(e,w):c?v(e,0):void 0;w>k;k++)if((h||k in g)&&(p=g[k],m=b(p,k,y),t))if(i)x[k]=m;else if(m)switch(t){case 3:return!0;case 5:return p;case 6:return k;case 2:x.push(p)}else if(u)return!1;return d?-1:l||u?u:x}}},1169:function(t,e,i){var n=i("2d95");t.exports=Array.isArray||function(t){return"Array"==n(t)}},"12b2":function(t,e,i){"use strict";var n=i("2b0e");e["a"]=n["a"].extend({name:"v-card-title",functional:!0,props:{primaryTitle:Boolean},render:function(t,e){var i=e.data,n=e.props,a=e.children;return i.staticClass=("v-card__title "+(i.staticClass||"")).trim(),n.primaryTitle&&(i.staticClass+=" v-card__title--primary"),t("div",i,a)}})},"14ec":function(t,e,i){"use strict";i("f7dc");var n=i("80d2"),a=i("2b0e");e["a"]=a["a"].extend().extend({name:"overlayable",props:{hideOverlay:Boolean},data:function(){return{overlay:null,overlayOffset:0,overlayTimeout:void 0,overlayTransitionDuration:650}},watch:{hideOverlay:function(t){t?this.removeOverlay():this.genOverlay()}},beforeDestroy:function(){this.removeOverlay()},methods:{genOverlay:function(){var t=this;if(!this.isActive||this.hideOverlay||this.isActive&&this.overlayTimeout||this.overlay)return clearTimeout(this.overlayTimeout),this.overlay&&this.overlay.classList.add("v-overlay--active");this.overlay=document.createElement("div"),this.overlay.className="v-overlay",this.absolute&&(this.overlay.className+=" v-overlay--absolute"),this.hideScroll();var e=this.absolute?this.$el.parentNode:document.querySelector("[data-app]");return e&&e.insertBefore(this.overlay,e.firstChild),this.overlay.clientHeight,requestAnimationFrame(function(){t.overlay&&(t.overlay.className+=" v-overlay--active",void 0!==t.activeZIndex&&(t.overlay.style.zIndex=String(t.activeZIndex-1)))}),!0},removeOverlay:function(){var t=this,e=!(arguments.length>0&&void 0!==arguments[0])||arguments[0];if(!this.overlay)return e&&this.showScroll();this.overlay.classList.remove("v-overlay--active"),this.overlayTimeout=window.setTimeout(function(){try{t.overlay&&t.overlay.parentNode&&t.overlay.parentNode.removeChild(t.overlay),t.overlay=null,e&&t.showScroll()}catch(i){console.log(i)}clearTimeout(t.overlayTimeout),t.overlayTimeout=void 0},this.overlayTransitionDuration)},scrollListener:function(t){if("keydown"===t.type){if(["INPUT","TEXTAREA","SELECT"].includes(t.target.tagName)||t.target.isContentEditable)return;var e=[n["p"].up,n["p"].pageup],i=[n["p"].down,n["p"].pagedown];if(e.includes(t.keyCode))t.deltaY=-1;else{if(!i.includes(t.keyCode))return;t.deltaY=1}}(t.target===this.overlay||"keydown"!==t.type&&t.target===document.body||this.checkPath(t))&&t.preventDefault()},hasScrollbar:function(t){if(!t||t.nodeType!==Node.ELEMENT_NODE)return!1;var e=window.getComputedStyle(t);return["auto","scroll"].includes(e.overflowY)&&t.scrollHeight>t.clientHeight},shouldScroll:function(t,e){return 0===t.scrollTop&&e<0||t.scrollTop+t.clientHeight===t.scrollHeight&&e>0},isInside:function(t,e){return t===e||null!==t&&t!==document.body&&this.isInside(t.parentNode,e)},checkPath:function(t){var e=t.path||this.composedPath(t),i=t.deltaY;if("keydown"===t.type&&e[0]===document.body){var n=this.$refs.dialog,a=window.getSelection().anchorNode;return!(n&&this.hasScrollbar(n)&&this.isInside(a,n))||this.shouldScroll(n,i)}for(var o=0;o<e.length;o++){var r=e[o];if(r===document)return!0;if(r===document.documentElement)return!0;if(r===this.$refs.content)return!0;if(this.hasScrollbar(r))return this.shouldScroll(r,i)}return!0},composedPath:function(t){if(t.composedPath)return t.composedPath();var e=[],i=t.target;while(i){if(e.push(i),"HTML"===i.tagName)return e.push(document),e.push(window),e;i=i.parentElement}return e},hideScroll:function(){this.$vuetify.breakpoint.smAndDown?document.documentElement.classList.add("overflow-y-hidden"):(window.addEventListener("wheel",this.scrollListener,{passive:!1}),window.addEventListener("keydown",this.scrollListener))},showScroll:function(){document.documentElement.classList.remove("overflow-y-hidden"),window.removeEventListener("wheel",this.scrollListener),window.removeEventListener("keydown",this.scrollListener)}}})},"169a":function(t,e,i){"use strict";i("6ec0");var n=i("c69d"),a=i("30d4"),o=i("14ec"),r=i("e949"),s=i("261e"),c=i("98a1"),l=i("c584"),u=i("80d2"),d=i("bfc5"),h=i("d9bd"),v=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var i=arguments[e];for(var n in i)Object.prototype.hasOwnProperty.call(i,n)&&(t[n]=i[n])}return t};function f(t,e,i){return e in t?Object.defineProperty(t,e,{value:i,enumerable:!0,configurable:!0,writable:!0}):t[e]=i,t}e["a"]={name:"v-dialog",directives:{ClickOutside:l["a"]},mixins:[n["a"],a["a"],o["a"],r["a"],s["a"],c["a"]],props:{disabled:Boolean,persistent:Boolean,fullscreen:Boolean,fullWidth:Boolean,noClickAnimation:Boolean,light:Boolean,dark:Boolean,maxWidth:{type:[String,Number],default:"none"},origin:{type:String,default:"center center"},width:{type:[String,Number],default:"auto"},scrollable:Boolean,transition:{type:[String,Boolean],default:"dialog-transition"}},data:function(){return{animate:!1,animateTimeout:null,stackClass:"v-dialog__content--active",stackMinZIndex:200}},computed:{classes:function(){var t;return t={},f(t,("v-dialog "+this.contentClass).trim(),!0),f(t,"v-dialog--active",this.isActive),f(t,"v-dialog--persistent",this.persistent),f(t,"v-dialog--fullscreen",this.fullscreen),f(t,"v-dialog--scrollable",this.scrollable),f(t,"v-dialog--animated",this.animate),t},contentClasses:function(){return{"v-dialog__content":!0,"v-dialog__content--active":this.isActive}},hasActivator:function(){return Boolean(!!this.$slots.activator||!!this.$scopedSlots.activator)}},watch:{isActive:function(t){t?(this.show(),this.hideScroll()):(this.removeOverlay(),this.unbind())},fullscreen:function(t){this.isActive&&(t?(this.hideScroll(),this.removeOverlay(!1)):(this.showScroll(),this.genOverlay()))}},beforeMount:function(){var t=this;this.$nextTick(function(){t.isBooted=t.isActive,t.isActive&&t.show()})},mounted:function(){"v-slot"===Object(u["l"])(this,"activator",!0)&&Object(h["a"])("v-dialog's activator slot must be bound, try '<template #activator=\"data\"><v-btn v-on=\"data.on>'",this)},beforeDestroy:function(){"undefined"!==typeof window&&this.unbind()},methods:{animateClick:function(){var t=this;this.animate=!1,this.$nextTick(function(){t.animate=!0,clearTimeout(t.animateTimeout),t.animateTimeout=setTimeout(function(){return t.animate=!1},150)})},closeConditional:function(t){return!(this.$refs.content.contains(t.target)||!this.isActive)&&(this.persistent?(this.noClickAnimation||this.overlay!==t.target||this.animateClick(),!1):Object(u["m"])(this.$refs.content)>=this.getMaxZIndex())},hideScroll:function(){this.fullscreen?document.documentElement.classList.add("overflow-y-hidden"):o["a"].options.methods.hideScroll.call(this)},show:function(){!this.fullscreen&&!this.hideOverlay&&this.genOverlay(),this.$refs.content.focus(),this.$listeners.keydown&&this.bind()},bind:function(){window.addEventListener("keydown",this.onKeydown)},unbind:function(){window.removeEventListener("keydown",this.onKeydown)},onKeydown:function(t){this.$emit("keydown",t)},genActivator:function(){var t=this;if(!this.hasActivator)return null;var e=this.disabled?{}:{click:function(e){e.stopPropagation(),t.disabled||(t.isActive=!t.isActive)}};if("scoped"===Object(u["l"])(this,"activator")){var i=this.$scopedSlots.activator({on:e});return this.activatorNode=i,i}return this.$createElement("div",{staticClass:"v-dialog__activator",class:{"v-dialog__activator--disabled":this.disabled},on:e},this.$slots.activator)}},render:function(t){var e=this,i=[],n={class:this.classes,ref:"dialog",directives:[{name:"click-outside",value:function(){return e.isActive=!1},args:{closeConditional:this.closeConditional,include:this.getOpenDependentElements}},{name:"show",value:this.isActive}],on:{click:function(t){t.stopPropagation()}}};this.fullscreen||(n.style={maxWidth:"none"===this.maxWidth?void 0:Object(u["c"])(this.maxWidth),width:"auto"===this.width?void 0:Object(u["c"])(this.width)}),i.push(this.genActivator());var a=t("div",n,this.showLazyContent(this.$slots.default));return this.transition&&(a=t("transition",{props:{name:this.transition,origin:this.origin}},[a])),i.push(t("div",{class:this.contentClasses,attrs:v({tabIndex:"-1"},this.getScopeIdAttrs()),style:{zIndex:this.activeZIndex},ref:"content"},[this.$createElement(d["a"],{props:{root:!0,light:this.light,dark:this.dark}},[a])])),t("div",{staticClass:"v-dialog__container",style:{display:!this.hasActivator||this.fullWidth?"block":"inline-block"}},i)}}},"261e":function(t,e,i){"use strict";var n=i("2b0e"),a=i("80d2");function o(t){if(Array.isArray(t)){for(var e=0,i=Array(t.length);e<t.length;e++)i[e]=t[e];return i}return Array.from(t)}e["a"]=n["a"].extend().extend({name:"stackable",data:function(){return{stackClass:"unpecified",stackElement:null,stackExclude:null,stackMinZIndex:0,isActive:!1}},computed:{activeZIndex:function(){if("undefined"===typeof window)return 0;var t=this.stackElement||this.$refs.content,e=this.isActive?this.getMaxZIndex(this.stackExclude||[t])+2:Object(a["m"])(t);return null==e?e:parseInt(e)}},methods:{getMaxZIndex:function(){for(var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[],e=this.$el,i=[this.stackMinZIndex,Object(a["m"])(e)],n=[].concat(o(document.getElementsByClassName(this.stackClass))),r=0;r<n.length;r++)t.includes(n[r])||i.push(Object(a["m"])(n[r]));return Math.max.apply(Math,i)}}})},"30d4":function(t,e,i){"use strict";var n=i("3e79"),a=i("d9bd"),o="function"===typeof Symbol&&"symbol"===typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"===typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t};function r(t,e,i){return e in t?Object.defineProperty(t,e,{value:i,enumerable:!0,configurable:!0,writable:!0}):t[e]=i,t}function s(t){var e="undefined"===typeof t?"undefined":o(t);return"boolean"===e||"string"===e||t.nodeType===Node.ELEMENT_NODE}e["a"]={name:"detachable",mixins:[n["a"]],props:{attach:{type:null,default:!1,validator:s},contentClass:{default:""}},data:function(){return{hasDetached:!1}},watch:{attach:function(){this.hasDetached=!1,this.initDetach()},hasContent:"initDetach"},beforeMount:function(){var t=this;this.$nextTick(function(){if(t.activatorNode){var e=Array.isArray(t.activatorNode)?t.activatorNode:[t.activatorNode];e.forEach(function(e){e.elm&&t.$el.parentNode.insertBefore(e.elm,t.$el)})}})},mounted:function(){!this.lazy&&this.initDetach()},deactivated:function(){this.isActive=!1},beforeDestroy:function(){try{if(this.$refs.content&&this.$refs.content.parentNode.removeChild(this.$refs.content),this.activatorNode){var t=Array.isArray(this.activatorNode)?this.activatorNode:[this.activatorNode];t.forEach(function(t){t.elm&&t.elm.parentNode.removeChild(t.elm)})}}catch(e){console.log(e)}},methods:{getScopeIdAttrs:function(){var t=this.$vnode&&this.$vnode.context.$options._scopeId;return t&&r({},t,"")},initDetach:function(){if(!this._isDestroyed&&this.$refs.content&&!this.hasDetached&&""!==this.attach&&!0!==this.attach&&"attach"!==this.attach){var t=void 0;t=!1===this.attach?document.querySelector("[data-app]"):"string"===typeof this.attach?document.querySelector(this.attach):this.attach,t?(t.insertBefore(this.$refs.content,t.firstChild),this.hasDetached=!0):Object(a["c"])("Unable to locate target "+(this.attach||"[data-app]"),this)}}}}},3613:function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-container",{staticClass:"qe-wrapper",style:"background-color:"+t.bgColour,attrs:{fluid:""}},[i("v-toolbar",{attrs:{flat:"",height:"50",color:"#282828"}},[i("v-toolbar-items",[i("v-dialog",{attrs:{"max-width":"500"},scopedSlots:t._u([{key:"activator",fn:function(e){var n=e.on;return[i("v-btn",t._g({attrs:{flat:"",dark:"",color:"#D9D9D9"}},n),[t._v("\n            Instructions\n          ")])]}}]),model:{value:t.instructionsDialog,callback:function(e){t.instructionsDialog=e},expression:"instructionsDialog"}},[i("v-card",[i("v-card-title",{staticClass:"headline"},[t._v("Instructions")]),i("v-card-text",[i("div",{staticClass:"body-2"},[t._v(t._s(t.instructionsText))]),i("h4",{staticClass:"mt-5"},[t._v("Note")]),i("p",[t._v('If the image disappear outside the dragging area, click "reset panning".')])]),i("v-card-actions",[i("v-spacer"),i("v-btn",{attrs:{color:"green darken-1",flat:""},on:{click:function(e){t.instructionsDialog=!1}}},[t._v("Close")])],1)],1)],1)],1),i("v-toolbar-items",[i("v-btn",{staticClass:"panning-reset",attrs:{color:"#D9D9D9",flat:"",dark:"",left:""}},[i("span",[t._v("Reset image panning")])])],1),i("v-spacer"),i("v-toolbar-items",[i("v-dialog",{attrs:{"max-width":"500"},scopedSlots:t._u([{key:"activator",fn:function(e){var n=e.on;return[i("v-btn",t._g({attrs:{flat:"",dark:"",color:"#D9D9D9"}},n),[t._v("\n            Quit Experiment\n          ")])]}}]),model:{value:t.abortDialog,callback:function(e){t.abortDialog=e},expression:"abortDialog"}},[i("v-card",[i("v-card-title",{staticClass:"headline"},[t._v("Do you want to quit the experiment?")]),i("v-card-text"),i("v-card-actions",[i("v-spacer"),i("v-btn",{attrs:{color:"default darken-1",flat:""},on:{click:function(e){t.abortDialog=!1}}},[t._v("Continue")]),i("v-btn",{attrs:{color:"red darken-1",flat:""},on:{click:t.abort}},[t._v("Quit")])],1)],1)],1)],1)],1),i("v-layout",{attrs:{"mt-3":"","justify-center":""}},[i("h4",{staticClass:"subheading font-weight-regular"},[t._v("Original")])]),i("v-layout",{staticStyle:{height:"85vh"},attrs:{"ml-3":"","mr-3":"","pa-0":""}},[i("v-flex",{staticClass:"picture-container",class:t.leftReproductionActive?"selected":"",attrs:{xs4:"","ma-2":""},on:{click:function(e){return t.toggleSelected("left")}}},[i("div",{staticClass:"panzoom"},[i("img",{staticClass:"picture",attrs:{id:"pictureLeft",src:""}})])]),i("v-flex",{staticClass:"picture-container",attrs:{xs4:"","ma-2":""}},[i("div",{staticClass:"panzoom"},[i("img",{staticClass:"picture",attrs:{id:"pictureOriginal",src:""}})])]),i("v-flex",{staticClass:"picture-container",class:t.rightReproductionActive?"selected":"",attrs:{xs4:"","ma-2":""},on:{click:function(e){return t.toggleSelected("right")}}},[i("div",{staticClass:"panzoom"},[i("img",{staticClass:"picture",attrs:{id:"picture-right",src:""}})])])],1),i("v-btn",{attrs:{fixed:"",bottom:"",right:"",color:"#D9D9D9"},on:{click:t.next}},[i("span",{staticClass:"ml-1"},[t._v("next")]),i("v-icon",[t._v("keyboard_arrow_right")])],1)],1)},a=[],o=(i("7514"),{data:function(){return{instructionsDialog:!1,abortDialog:!1,bgColour:"#808080",distance:20,instructionsText:"Rate the images.",rightReproductionActive:!1,leftReproductionActive:!1}},created:function(){$(document).ready(function(){(function(){var t="https://images4.alphacoders.com/213/thumb-1920-213794.jpg";$(".picture").attr("src",t);var e=$(".picture-container");e.find(".panzoom").panzoom({$set:e.find(".panzoom"),minScale:1,maxScale:1,$reset:$(".panning-reset")}).panzoom("zoom")})()})},methods:{abort:function(){this.abortDialog=!0,this.$router.push("/observer")},toggleSelected:function(t){"left"===t?(this.leftReproductionActive=!this.leftReproductionActive,this.rightReproductionActive=!1):(this.rightReproductionActive=!this.rightReproductionActive,this.leftReproductionActive=!1)},next:function(){this.rightReproductionActive=!1,this.leftReproductionActive=!1;var t="https://cakebycourtney.com/wp-content/uploads/2016/04/Tonight-Show-Cake-3-1024x683.jpg";$(".picture").attr("src",t),$(".picture-container").find(".panzoom").panzoom("reset",{animate:!1})}}}),r=o,s=(i("43af"),i("2877")),c=i("6544"),l=i.n(c),u=i("8336"),d=i("b0af"),h=i("99d9"),v=i("12b2"),f=i("a523"),p=i("169a"),m=i("0e8f"),y=i("132d"),g=i("a722"),b=i("9910"),w=i("71d9"),k=i("2a7f"),x=Object(s["a"])(r,n,a,!1,null,"2a235382",null);e["default"]=x.exports;l()(x,{VBtn:u["a"],VCard:d["a"],VCardActions:h["a"],VCardText:h["b"],VCardTitle:v["a"],VContainer:f["a"],VDialog:p["a"],VFlex:m["a"],VIcon:y["a"],VLayout:g["a"],VSpacer:b["a"],VToolbar:w["a"],VToolbarItems:k["a"]})},"377e":function(t,e,i){},"3e79":function(t,e,i){"use strict";var n=i("2b0e");e["a"]=n["a"].extend().extend({name:"bootable",props:{lazy:Boolean},data:function(){return{isBooted:!1}},computed:{hasContent:function(){return this.isBooted||!this.lazy||this.isActive}},watch:{isActive:function(){this.isBooted=!0}},methods:{showLazyContent:function(t){return this.hasContent?t:void 0}}})},"43af":function(t,e,i){"use strict";var n=i("377e"),a=i.n(n);a.a},"4c94":function(t,e,i){},"6ec0":function(t,e,i){},7514:function(t,e,i){"use strict";var n=i("5ca1"),a=i("0a49")(5),o="find",r=!0;o in[]&&Array(1)[o](function(){r=!1}),n(n.P+n.F*r,"Array",{find:function(t){return a(this,t,arguments.length>1?arguments[1]:void 0)}}),i("9c6c")(o)},"99d9":function(t,e,i){"use strict";var n=i("80d2"),a=i("b0af"),o=i("adda"),r=i("d9bd"),s=o["a"].extend({name:"v-card-media",mounted:function(){Object(r["d"])("v-card-media",this.src?"v-img":"v-responsive",this)}}),c=i("12b2");i.d(e,"a",function(){return l}),i.d(e,"b",function(){return u});var l=Object(n["e"])("v-card__actions"),u=Object(n["e"])("v-card__text");a["a"],c["a"]},b0af:function(t,e,i){"use strict";i("4c94"),i("d0e7");var n=i("b64a"),a=i("2b0e");function o(t,e,i){return e in t?Object.defineProperty(t,e,{value:i,enumerable:!0,configurable:!0,writable:!0}):t[e]=i,t}var r=a["a"].extend({name:"elevatable",props:{elevation:[Number,String]},computed:{computedElevation:function(){return this.elevation},elevationClasses:function(){return this.computedElevation?o({},"elevation-"+this.computedElevation,!0):{}}}}),s=i("23bf"),c=i("6a18"),l=i("58df"),u=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var i=arguments[e];for(var n in i)Object.prototype.hasOwnProperty.call(i,n)&&(t[n]=i[n])}return t},d=Object(l["a"])(n["a"],r,s["a"],c["a"]).extend({name:"v-sheet",props:{tag:{type:String,default:"div"},tile:Boolean},computed:{classes:function(){return u({"v-sheet":!0,"v-sheet--tile":this.tile},this.themeClasses,this.elevationClasses)},styles:function(){return this.measurableStyles}},render:function(t){var e={class:this.classes,style:this.styles,on:this.$listeners};return t(this.tag,this.setBackgroundColor(this.color,e),this.$slots.default)}}),h=d,v=i("0d01"),f=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var i=arguments[e];for(var n in i)Object.prototype.hasOwnProperty.call(i,n)&&(t[n]=i[n])}return t};e["a"]=Object(l["a"])(v["a"],h).extend({name:"v-card",props:{flat:Boolean,hover:Boolean,img:String,raised:Boolean},computed:{classes:function(){return f({"v-card":!0,"v-card--flat":this.flat,"v-card--hover":this.hover},h.options.computed.classes.call(this))},styles:function(){var t=f({},h.options.computed.styles.call(this));return this.img&&(t.background='url("'+this.img+'") center center / cover no-repeat'),t}},render:function(t){var e=this.generateRouteLink(this.classes),i=e.tag,n=e.data;return n.style=this.styles,t(i,this.setBackgroundColor(this.color,n),this.$slots.default)}})},bfc5:function(t,e,i){"use strict";var n=i("6a18"),a=i("58df");e["a"]=Object(a["a"])(n["a"]).extend({name:"theme-provider",props:{root:Boolean},computed:{isDark:function(){return this.root?this.rootIsDark:n["a"].options.computed.isDark.call(this)}},render:function(){return this.$slots.default&&this.$slots.default.find(function(t){return!t.isComment&&" "!==t.text})}})},c584:function(t,e,i){"use strict";function n(){return!1}function a(t,e,i){i.args=i.args||{};var a=i.args.closeConditional||n;if(t&&!1!==a(t)&&!("isTrusted"in t&&!t.isTrusted||"pointerType"in t&&!t.pointerType)){var r=(i.args.include||function(){return[]})();r.push(e),!o(t,r)&&setTimeout(function(){a(t)&&i.value&&i.value(t)},0)}}function o(t,e){var i=t.clientX,n=t.clientY,a=!0,o=!1,s=void 0;try{for(var c,l=e[Symbol.iterator]();!(a=(c=l.next()).done);a=!0){var u=c.value;if(r(u,i,n))return!0}}catch(d){o=!0,s=d}finally{try{!a&&l.return&&l.return()}finally{if(o)throw s}}return!1}function r(t,e,i){var n=t.getBoundingClientRect();return e>=n.left&&e<=n.right&&i>=n.top&&i<=n.bottom}e["a"]={inserted:function(t,e){var i=function(i){return a(i,t,e)},n=document.querySelector("[data-app]")||document.body;n.addEventListener("click",i,!0),t._clickOutside=i},unbind:function(t){if(t._clickOutside){var e=document.querySelector("[data-app]")||document.body;e&&e.removeEventListener("click",t._clickOutside,!0),delete t._clickOutside}}}},c69d:function(t,e,i){"use strict";var n=i("58df");function a(t){if(Array.isArray(t)){for(var e=0,i=Array(t.length);e<t.length;e++)i[e]=t[e];return i}return Array.from(t)}function o(t){for(var e=[],i=0;i<t.length;i++){var n=t[i];n.isActive&&n.isDependent?e.push(n):e.push.apply(e,a(o(n.$children)))}return e}e["a"]=Object(n["a"])().extend({name:"dependent",data:function(){return{closeDependents:!0,isActive:!1,isDependent:!0}},watch:{isActive:function(t){if(!t)for(var e=this.getOpenDependents(),i=0;i<e.length;i++)e[i].isActive=!1}},methods:{getOpenDependents:function(){return this.closeDependents?o(this.$children):[]},getOpenDependentElements:function(){for(var t=[],e=this.getOpenDependents(),i=0;i<e.length;i++)t.push.apply(t,a(e[i].getClickableDependentElements()));return t},getClickableDependentElements:function(){var t=[this.$el];return this.$refs.content&&t.push(this.$refs.content),t.push.apply(t,a(this.getOpenDependentElements())),t}}})},cd1c:function(t,e,i){var n=i("e853");t.exports=function(t,e){return new(n(t))(e)}},d0e7:function(t,e,i){},e853:function(t,e,i){var n=i("d3f4"),a=i("1169"),o=i("2b4c")("species");t.exports=function(t){var e;return a(t)&&(e=t.constructor,"function"!=typeof e||e!==Array&&!a(e.prototype)||(e=void 0),n(e)&&(e=e[o],null===e&&(e=void 0))),void 0===e?Array:e}},e949:function(t,e,i){"use strict";var n=i("2b0e");e["a"]=n["a"].extend({name:"returnable",props:{returnValue:null},data:function(){return{isActive:!1,originalValue:null}},watch:{isActive:function(t){t?this.originalValue=this.returnValue:this.$emit("update:returnValue",this.originalValue)}},methods:{save:function(t){this.originalValue=t,this.isActive=!1}}})},f7dc:function(t,e,i){}}]);
//# sourceMappingURL=chunk-dcd74f90.6e2eec85.js.map